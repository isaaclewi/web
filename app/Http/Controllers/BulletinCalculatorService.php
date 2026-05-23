<?php

namespace App\Services;

use App\Models\Apprenant;
use App\Models\Bulletin;
use App\Models\Classe;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\GradeConfig;
use App\Models\Institution;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class BulletinCalculatorService
{
    private GradeConfig $config;

    public function __construct(GradeConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Calcule et sauvegarde le bulletin d'UN apprenant pour une période.
     * Ne publie PAS — la publication reste côté admin.
     */
    public function calculerPourApprenant(
        Apprenant $apprenant,
        string $periode,
        bool $recalculerRang = true
    ): Bulletin {
        $instId = $this->config->institution_id;
        $annee = $this->config->annee_academique;

        // Sujets de la classe de l'apprenant
        $classeId = $apprenant->class_id;
        $subjects = Subject::where('institution_id', $instId)
            ->when($classeId, fn ($q) => $q->where('class_id', $classeId))
            ->get();

        $detailMatieres = [];
        $sommePonderee = 0;
        $sommeCoeffs = 0;
        $sommeDevoirsPonderee = 0;
        $sommeExamensPonderee = 0;
        $sommeCoeffsDDevoirs = 0;
        $sommeCoeffsExamens = 0;

        foreach ($subjects as $subject) {
            $moyDevoirs = $this->calculerMoyenneType($apprenant->id, $subject->id, 'devoir', $periode, $annee, $instId);
            $moyExamens = $this->calculerMoyenneType($apprenant->id, $subject->id, 'examen', $periode, $annee, $instId);

            // Appliquer les pondérations institution
            if ($moyDevoirs !== null && $moyExamens !== null) {
                $moyMatiere = round(
                    ($moyDevoirs * $this->config->pct_devoirs / 100)
                    + ($moyExamens * $this->config->pct_examen / 100),
                    $this->config->decimales
                );
            } elseif ($moyDevoirs !== null) {
                $moyMatiere = round($moyDevoirs, $this->config->decimales);
            } elseif ($moyExamens !== null) {
                $moyMatiere = round($moyExamens, $this->config->decimales);
            } else {
                continue; // pas de notes pour cette matière
            }

            $coeff = (float) $subject->coefficient;
            $sommePonderee += $moyMatiere * $coeff;
            $sommeCoeffs += $coeff;

            if ($moyDevoirs !== null) {
                $sommeDevoirsPonderee += $moyDevoirs * $coeff;
                $sommeCoeffsDDevoirs += $coeff;
            }
            if ($moyExamens !== null) {
                $sommeExamensPonderee += $moyExamens * $coeff;
                $sommeCoeffsExamens += $coeff;
            }

            $mention = $this->config->getMention($moyMatiere);

            $detailMatieres[] = [
                'subject_id' => $subject->id,
                'nom' => $subject->name,
                'coefficient' => $coeff,
                'moy_devoirs' => $moyDevoirs,
                'moy_examens' => $moyExamens,
                'moyenne' => $moyMatiere,
                'mention' => $mention,
            ];
        }

        $moyGenerale = $sommeCoeffs > 0
            ? round($sommePonderee / $sommeCoeffs, $this->config->decimales)
            : null;

        $moyDevGlobal = $sommeCoeffsDDevoirs > 0
            ? round($sommeDevoirsPonderee / $sommeCoeffsDDevoirs, $this->config->decimales)
            : null;

        $moyExGlobal = $sommeCoeffsExamens > 0
            ? round($sommeExamensPonderee / $sommeCoeffsExamens, $this->config->decimales)
            : null;

        $mention = $moyGenerale !== null ? $this->config->getMention($moyGenerale) : null;
        $admis = $moyGenerale !== null && $moyGenerale >= $this->config->note_passage;

        // Compensation éventuelle
        if (! $admis && $this->config->compensation_active && $moyGenerale !== null) {
            $admis = $moyGenerale >= (float) $this->config->seuil_compensation;
        }

        $bulletin = Bulletin::updateOrCreate(
            [
                'apprenant_id' => $apprenant->id,
                'annee_academique' => $annee,
                'periode' => $periode,
            ],
            [
                'institution_id' => $instId,
                'classe_id' => $classeId,
                'moyenne_generale' => $moyGenerale,
                'moyenne_devoirs' => $moyDevGlobal,
                'moyenne_examens' => $moyExGlobal,
                'mention' => $mention,
                'admis' => $admis,
                'detail_matieres' => $detailMatieres,
                'calcule_at' => now(),
                'calcule_par' => Auth::id(),
                // On ne touche PAS à publie/publie_at
            ]
        );

        if ($recalculerRang && $classeId) {
            $this->recalculerRangs($classeId, $annee, $periode);
        }

        return $bulletin->fresh();
    }

    /**
     * Calcule tous les bulletins d'une classe pour une période.
     */
    public function calculerPourClasse(Classe $classe, string $periode): array
    {
        $annee = $this->config->annee_academique;
        $apprenants = Apprenant::where('class_id', $classe->id)
            ->where('institution_id', $this->config->institution_id)
            ->get();

        $bulletins = [];
        foreach ($apprenants as $apprenant) {
            $bulletins[] = $this->calculerPourApprenant($apprenant, $periode, false);
        }

        // Recalcul groupé des rangs
        $this->recalculerRangs($classe->id, $annee, $periode);

        return $bulletins;
    }

    /**
     * Recalcule les rangs dans une classe pour une période.
     */
    public function recalculerRangs(int $classeId, string $annee, string $periode): void
    {
        $bulletins = Bulletin::where('classe_id', $classeId)
            ->where('annee_academique', $annee)
            ->where('periode', $periode)
            ->whereNotNull('moyenne_generale')
            ->orderByDesc('moyenne_generale')
            ->get();

        $effectif = $bulletins->count();
        $rang = 1;
        foreach ($bulletins as $b) {
            $b->update(['rang' => $rang, 'effectif_classe' => $effectif]);
            $rang++;
        }
    }

    // ──────────────────────────────────────────────────────
    // PRIVÉ
    // ──────────────────────────────────────────────────────

    private function calculerMoyenneType(
        int $apprenantId,
        int $subjectId,
        string $typeEvaluation,
        string $periode,
        string $annee,
        int $instId
    ): ?float {
        $evaluationIds = Evaluation::where('subject_id', $subjectId)
            ->where('institution_id', $instId)
            ->where('type_evaluation', $typeEvaluation)
            ->where('periode', $periode)
            ->where('compte_dans_moyenne', true)
            ->pluck('id');

        if ($evaluationIds->isEmpty()) {
            return null;
        }

        $grades = Grade::whereIn('evaluation_id', $evaluationIds)
            ->where('apprenant_id', $apprenantId)
            ->join('evaluations', 'grades.evaluation_id', '=', 'evaluations.id')
            ->select('grades.score', 'evaluations.max_score')
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        // Ramener chaque note sur la même base (note_max institution)
        $noteMax = (float) $this->config->note_max;
        $somme = 0;
        $count = 0;
        foreach ($grades as $g) {
            $maxScore = $g->max_score > 0 ? $g->max_score : $noteMax;
            // Normalisation : (score / max_eval) * note_max_config
            $somme += ((float) $g->score / $maxScore) * $noteMax;
            $count++;
        }

        return $count > 0 ? round($somme / $count, $this->config->decimales) : null;
    }
}
