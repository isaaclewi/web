<?php

namespace App\Services;

use App\Models\Apprenant;
use App\Models\Bulletin;
use App\Models\Classe;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\GradeConfig;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ══════════════════════════════════════════════════════════
 *  BulletinCalculatorService
 *
 *  Calcule les bulletins en intégrant DEUX sources de notes :
 *
 *  1. Les notes saisies par les enseignants via
 *     Grade + Evaluation (TeacherDashboardController::gradesStore)
 *     → table `grades` (score, evaluation_id, apprenant_id)
 *     → table `evaluations` (subject_id, type, periode, max_score)
 *
 *  2. (Futur) Des notes saisies manuellement côté admin/staff
 *     → même structure, même table, juste un type différent.
 *
 *  Formule par matière :
 *    moyenne_matiere = (moy_devoirs × pct_devoirs + moy_examen × pct_examen) / 100
 *
 *  Formule générale (pondérée par coefficient) :
 *    moyenne_generale = Σ(moyenne_matiere × coefficient) / Σ(coefficient)
 * ══════════════════════════════════════════════════════════
 */
class BulletinCalculatorService
{
    public function __construct(private GradeConfig $config) {}

    /* ──────────────────────────────────────────────────────
     |  POINT D'ENTRÉE — UN apprenant
     ────────────────────────────────────────────────────── */
    public function calculerPourApprenant(Apprenant $apprenant, string $periode): Bulletin
    {
        $lignes          = $this->calculerLignes($apprenant, $periode);
        $moyenneGenerale = $this->calculerMoyenneGenerale($lignes);
        $mention         = $this->getMention($moyenneGenerale);

        return DB::transaction(function () use ($apprenant, $periode, $lignes, $moyenneGenerale, $mention) {
            $bulletin = Bulletin::updateOrCreate(
                [
                    'apprenant_id'     => $apprenant->id,
                    'institution_id'   => $apprenant->institution_id,
                    'classe_id'        => $apprenant->class_id,
                    'annee_academique' => $this->config->annee_academique,
                    'periode'          => $periode,
                ],
                [
                    'moyenne_generale' => $moyenneGenerale,
                    'mention'          => $mention,
                    'lignes'           => $lignes,            // JSON des détails par matière
                    'calcule_at'       => now(),
                    'calcule_par'      => Auth::id(),
                    'publie'           => false,
                ]
            );

            // ── Recalcul du rang dans la classe après mise à jour ──
            $this->recalculerRangs($apprenant->class_id, $apprenant->institution_id, $periode);

            return $bulletin->fresh();
        });
    }

    /* ──────────────────────────────────────────────────────
     |  POINT D'ENTRÉE — Toute une classe
     ────────────────────────────────────────────────────── */
    public function calculerPourClasse(Classe $classe, string $periode): array
    {
        $apprenants = Apprenant::where('class_id', $classe->id)
            ->where('institution_id', $classe->institution_id)
            ->get();

        $bulletins = [];

        DB::transaction(function () use ($apprenants, $periode, &$bulletins) {
            foreach ($apprenants as $apprenant) {
                $lignes          = $this->calculerLignes($apprenant, $periode);
                $moyenneGenerale = $this->calculerMoyenneGenerale($lignes);
                $mention         = $this->getMention($moyenneGenerale);

                $bulletins[] = Bulletin::updateOrCreate(
                    [
                        'apprenant_id'     => $apprenant->id,
                        'institution_id'   => $apprenant->institution_id,
                        'classe_id'        => $apprenant->class_id,
                        'annee_academique' => $this->config->annee_academique,
                        'periode'          => $periode,
                    ],
                    [
                        'moyenne_generale' => $moyenneGenerale,
                        'mention'          => $mention,
                        'lignes'           => $lignes,
                        'calcule_at'       => now(),
                        'calcule_par'      => Auth::id(),
                        'publie'           => false,
                    ]
                );
            }

            // ── Rangs après insertion de tous les bulletins de la classe ──
            if ($apprenants->isNotEmpty()) {
                $this->recalculerRangs(
                    $apprenants->first()->class_id,
                    $apprenants->first()->institution_id,
                    $periode
                );
            }
        });

        return $bulletins;
    }

    /* ══════════════════════════════════════════════════════
     |  CALCUL DES LIGNES (une ligne = une matière)
     ══════════════════════════════════════════════════════ */

    /**
     * Retourne un tableau de lignes, une par matière :
     * [
     *   'matiere'          => 'Mathématiques',
     *   'coefficient'      => 3,
     *   'subject_id'       => 12,
     *   'moy_devoirs'      => 14.5,
     *   'moy_examen'       => 12.0,
     *   'moyenne'          => 13.0,    ← pondérée pct_devoirs / pct_examen
     *   'nb_devoirs'       => 3,
     *   'nb_examens'       => 1,
     *   'note_max'         => 20,
     *   'appreciation'     => 'Bien',
     * ]
     */
    private function calculerLignes(Apprenant $apprenant, string $periode): array
    {
        // ── 1. Matières de la classe de cet apprenant ──
        $subjects = Subject::where('institution_id', $apprenant->institution_id)
            ->where(function ($q) use ($apprenant) {
                $q->where('class_id', $apprenant->class_id)
                  ->orWhereNull('class_id');          // matières globales sans classe spécifique
            })
            ->orderBy('name')
            ->get();

        // Si aucune matière n'est liée à la classe, on tente via niveau
        if ($subjects->isEmpty() && $apprenant->niveau_id) {
            $subjects = Subject::where('institution_id', $apprenant->institution_id)
                ->whereHas('classe', fn ($q) => $q->where('niveau_id', $apprenant->niveau_id))
                ->orderBy('name')
                ->get();
        }

        $lignes = [];

        foreach ($subjects as $subject) {
            // ── 2. Évaluations de cette matière pour cette période ──
            //    On filtre par période : la colonne `periode` de la table evaluations
            //    peut contenir trimestre1, semestre1, annuel, etc.
            //    On inclut aussi les évaluations sans période spécifiée (NULL / '').
            $evaluationQuery = Evaluation::where('subject_id', $subject->id)
                ->where(function ($q) use ($periode) {
                    $q->where('periode', $periode)
                      ->orWhereNull('periode')
                      ->orWhere('periode', '');
                });

            $evaluations = $evaluationQuery->get();

            if ($evaluations->isEmpty()) {
                // Pas d'évaluation pour cette matière → on insère quand même la ligne
                // avec des valeurs nulles pour que la matière apparaisse dans le bulletin.
                $lignes[] = $this->ligneVide($subject);
                continue;
            }

            $evalIds = $evaluations->pluck('id');

            // ── 3. Notes de cet apprenant sur ces évaluations ──
            $grades = Grade::where('apprenant_id', $apprenant->id)
                ->whereIn('evaluation_id', $evalIds)
                ->with('evaluation')
                ->get();

            // ── 4. Séparer devoirs / examens ──
            //    Types "devoir"  : controle, tp, projet, interro, devoir, quizz …
            //    Types "examen"  : examen, composition, bac_blanc …
            $gradesDevoirs = $grades->filter(fn ($g) => $this->isDevoir($g->evaluation->type ?? ''));
            $gradesExamens = $grades->filter(fn ($g) => $this->isExamen($g->evaluation->type ?? ''));

            $moyDevoirs = $this->moyenneNormalisee($gradesDevoirs);
            $moyExamens = $this->moyenneNormalisee($gradesExamens);

            // ── 5. Moyenne de la matière ──
            $moyenne = $this->calculerMoyenneMatiere($moyDevoirs, $moyExamens);

            $lignes[] = [
                'matiere'      => $subject->name,
                'subject_id'   => $subject->id,
                'coefficient'  => (float) ($subject->coefficient ?? 1),
                'note_max'     => $this->config->note_max,
                'moy_devoirs'  => $moyDevoirs,
                'moy_examen'   => $moyExamens,
                'moyenne'      => $moyenne,
                'nb_devoirs'   => $gradesDevoirs->count(),
                'nb_examens'   => $gradesExamens->count(),
                'nb_total'     => $grades->count(),
                'appreciation' => $this->getAppreciation($moyenne),
            ];
        }

        return $lignes;
    }

    /* ══════════════════════════════════════════════════════
     |  HELPERS DE CALCUL
     ══════════════════════════════════════════════════════ */

    /**
     * Moyenne pondérée générale.
     * Σ(moyenne_matière × coefficient) / Σ(coefficient)
     * On ignore les matières sans note (moyenne = null).
     */
    private function calculerMoyenneGenerale(array $lignes): ?float
    {
        $sumPondere = 0;
        $sumCoeff   = 0;

        foreach ($lignes as $ligne) {
            if ($ligne['moyenne'] === null) {
                continue;
            }
            $coeff       = (float) ($ligne['coefficient'] ?? 1);
            $sumPondere += $ligne['moyenne'] * $coeff;
            $sumCoeff   += $coeff;
        }

        if ($sumCoeff === 0.0) {
            return null;
        }

        return round($sumPondere / $sumCoeff, $this->config->decimales ?? 2);
    }

    /**
     * Calcule la moyenne d'une matière en appliquant la pondération
     * pct_devoirs / pct_examen de la GradeConfig.
     *
     *   Si l'une des deux catégories est absente (null), on utilise uniquement l'autre.
     *   Si les deux sont présentes :
     *     moyenne = (moy_devoirs * pct_devoirs + moy_examen * pct_examen) / 100
     */
    private function calculerMoyenneMatiere(?float $moyDevoirs, ?float $moyExamens): ?float
    {
        $pctD = (float) ($this->config->pct_devoirs ?? 40);
        $pctE = (float) ($this->config->pct_examen  ?? 60);

        if ($moyDevoirs !== null && $moyExamens !== null) {
            $val = ($moyDevoirs * $pctD + $moyExamens * $pctE) / 100;
        } elseif ($moyDevoirs !== null) {
            $val = $moyDevoirs;
        } elseif ($moyExamens !== null) {
            $val = $moyExamens;
        } else {
            return null;
        }

        // Ramener sur la note_max de la config
        $noteMax = (float) ($this->config->note_max ?? 20);
        if ($noteMax !== 20.0) {
            // Les grades sont toujours saisis sur max_score de l'évaluation,
            // déjà normalisés sur 20 par moyenneNormalisee().
            // Si note_max config ≠ 20, on remet à l'échelle.
            $val = $val * $noteMax / 20;
        }

        return round($val, $this->config->decimales ?? 2);
    }

    /**
     * Moyenne des notes d'un groupe, ramenée sur 20 (base commune).
     * score est sur evaluation.max_score → on normalise sur 20.
     */
    private function moyenneNormalisee($grades): ?float
    {
        if ($grades->isEmpty()) {
            return null;
        }

        $total  = 0;
        $count  = 0;

        foreach ($grades as $grade) {
            $maxScore = (float) ($grade->evaluation->max_score ?? 20);
            if ($maxScore <= 0) {
                continue;
            }
            $total += ($grade->score / $maxScore) * 20;   // normalisation sur 20
            $count++;
        }

        return $count > 0 ? round($total / $count, 2) : null;
    }

    /* ══════════════════════════════════════════════════════
     |  CLASSIFICATION DES TYPES D'ÉVALUATION
     ══════════════════════════════════════════════════════ */

    /**
     * Retourne true si ce type d'évaluation est de la catégorie "devoir".
     * Adapte cette liste aux valeurs utilisées dans ta table evaluations.
     */
    private function isDevoir(string $type): bool
    {
        return in_array(strtolower($type), [
            'controle', 'interro', 'tp', 'projet',
            'devoir', 'dm', 'quizz', 'quiz', 'td',
            // TeacherDashboardController::evaluationStore utilise ces valeurs :
            // 'controle','examen','tp','projet'
            // → 'controle', 'tp', 'projet' sont des devoirs
        ]);
    }

    /**
     * Retourne true si ce type d'évaluation est de la catégorie "examen".
     */
    private function isExamen(string $type): bool
    {
        return in_array(strtolower($type), [
            'examen', 'composition', 'bac_blanc', 'baccalaureat',
            'exam', 'final', 'certification',
        ]);
    }

    /* ══════════════════════════════════════════════════════
     |  MENTIONS ET APPRÉCIATIONS
     ══════════════════════════════════════════════════════ */

    private function getMention(?float $moyenne): ?string
    {
        if ($moyenne === null) {
            return null;
        }

        $mentions = $this->config->mentions ?? GradeConfig::defaultMentions();

        // Trier par seuil décroissant
        usort($mentions, fn ($a, $b) => $b['min'] <=> $a['min']);

        foreach ($mentions as $mention) {
            if ($moyenne >= (float) $mention['min']) {
                return $mention['libelle'];
            }
        }

        return 'Insuffisant';
    }

    private function getAppreciation(?float $moyenne): string
    {
        if ($moyenne === null) {
            return '—';
        }

        $noteMax = (float) ($this->config->note_max ?? 20);
        $pct     = $noteMax > 0 ? ($moyenne / $noteMax) * 100 : 0;

        return match (true) {
            $pct >= 90 => 'Excellent',
            $pct >= 75 => 'Très bien',
            $pct >= 65 => 'Bien',
            $pct >= 55 => 'Assez bien',
            $pct >= 50 => 'Passable',
            $pct >= 30 => 'Insuffisant',
            default    => 'Très insuffisant',
        };
    }

    /* ══════════════════════════════════════════════════════
     |  RANG DANS LA CLASSE
     ══════════════════════════════════════════════════════ */

    private function recalculerRangs(int $classeId, int $institutionId, string $periode): void
    {
        $bulletins = Bulletin::where('classe_id', $classeId)
            ->where('institution_id', $institutionId)
            ->where('annee_academique', $this->config->annee_academique)
            ->where('periode', $periode)
            ->whereNotNull('moyenne_generale')
            ->orderByDesc('moyenne_generale')
            ->get();

        foreach ($bulletins as $rang => $bulletin) {
            $bulletin->update(['rang' => $rang + 1]);
        }
    }

    /* ══════════════════════════════════════════════════════
     |  HELPER — ligne vide (matière sans note)
     ══════════════════════════════════════════════════════ */

    private function ligneVide(Subject $subject): array
    {
        return [
            'matiere'      => $subject->name,
            'subject_id'   => $subject->id,
            'coefficient'  => (float) ($subject->coefficient ?? 1),
            'note_max'     => $this->config->note_max,
            'moy_devoirs'  => null,
            'moy_examen'   => null,
            'moyenne'      => null,
            'nb_devoirs'   => 0,
            'nb_examens'   => 0,
            'nb_total'     => 0,
            'appreciation' => '—',
        ];
    }
}