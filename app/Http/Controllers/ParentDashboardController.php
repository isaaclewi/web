<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\SchoolParent;
use App\Models\SuiviDisciplinaire;
use App\Models\FinancialRecord;
use App\Models\Institution;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    /* ── Helper : récupère le profil SchoolParent de l'utilisateur connecté ── */
    private function getParent(): SchoolParent
    {
        $user   = Auth::user();
        $parent = SchoolParent::where('user_id', $user->id)
            ->with([
                'apprenants' => fn ($q) => $q->with([
                    'classe:id,name',
                    'niveau:id,name',
                    'filiere:id,name',
                    'institution:id,name,academic_year,devise',
                ]),
            ])
            ->first();

        abort_if(! $parent, 403, 'Profil parent introuvable.');
        return $parent;
    }

    /* ─────────────────────────────────────────────────────────────
     | 1. DASHBOARD PRINCIPAL
     | GET /parent/dashboard
     ───────────────────────────────────────────────────────────── */
    public function index()
    {
        $user        = Auth::user() ?? redirect()->route('login')->send();
        $institution = $user->institution;
        $schoolParent = $this->getParent();
        $enfants      = $schoolParent->apprenants;

        // Pour chaque enfant : moyenne générale + nb incidents ouverts + solde financier
        $enfantsData = $enfants->map(function ($a) {
            $institution = $a->institution;
            $annee       = $institution?->academic_year ?? date('Y').'-'.(date('Y') + 1);
            $anneeCivile = date('Y');

            // Matières de la classe
            $subjects = $a->class_id
                ? Subject::where('class_id', $a->class_id)->get()
                : collect();

            // Moyenne générale
            $moyenne = $this->calculerMoyenne($a, $subjects);

            // Finances
            $finances = FinancialRecord::where('apprenant_id', $a->id)
                ->where('annee_academique', $annee)->get();
            $totalDu    = $finances->sum('montant_du');
            $totalPaye  = $finances->sum('montant_paye');
            $totalReste = $finances->sum('montant_reste');

            // Incidents disciplinaires ouverts
            $incidentsOuverts = SuiviDisciplinaire::where('apprenant_id', $a->id)
                ->where('annee_civile', $anneeCivile)
                ->where('statut', 'ouvert')->count();

            $totalIncidents = SuiviDisciplinaire::where('apprenant_id', $a->id)
                ->where('annee_civile', $anneeCivile)->count();

            // Dernières notes (5)
            $dernieresNotes = Grade::where('apprenant_id', $a->id)
                ->with('evaluation.subject')
                ->orderByDesc('created_at')
                ->take(5)->get();

            return [
                'apprenant'       => $a,
                'moyenne'         => $moyenne,
                'totalDu'         => $totalDu,
                'totalPaye'       => $totalPaye,
                'totalReste'      => $totalReste,
                'incidentsOuverts'=> $incidentsOuverts,
                'totalIncidents'  => $totalIncidents,
                'dernieresNotes'  => $dernieresNotes,
                'annee'           => $annee,
            ];
        });

        // Alertes globales (impayés + incidents graves)
        $alertes = [];
        foreach ($enfantsData as $d) {
            if ($d['totalReste'] > 0) {
                $alertes[] = [
                    'type'  => 'finance',
                    'color' => 'amber',
                    'icon'  => '💰',
                    'text'  => number_format($d['totalReste'], 0, ',', ' ')
                               .' FCFA impayé(s) — '.$d['apprenant']->prenom,
                ];
            }
            if ($d['incidentsOuverts'] > 0) {
                $alertes[] = [
                    'type'  => 'discipline',
                    'color' => 'red',
                    'icon'  => '⚠️',
                    'text'  => $d['incidentsOuverts'].' incident(s) ouvert(s) — '
                               .$d['apprenant']->prenom,
                ];
            }
        }

        return view('parent.dashboard', compact(
            'user', 'institution', 'schoolParent', 'enfants', 'enfantsData', 'alertes'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     | 2. NOTES D'UN ENFANT
     | GET /parent/enfant/{apprenant}/notes
     ───────────────────────────────────────────────────────────── */
    public function notes(Apprenant $apprenant, Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $this->assertParentOf($apprenant);

        $institution = $apprenant->institution;
        $apprenant->load(['classe', 'niveau', 'filiere']);

        $subjects = $apprenant->class_id
            ? Subject::where('class_id', $apprenant->class_id)->with('teacher')->get()
            : collect();

        // Filtres
        $subjectFilter = $request->get('subject_id');
        $query = Grade::where('apprenant_id', $apprenant->id)
            ->with(['evaluation.subject.teacher']);
        if ($subjectFilter) {
            $query->whereHas('evaluation', fn($q) => $q->where('subject_id', $subjectFilter));
        }
        $grades = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Moyennes par matière
        $moyennesParMatiere = $subjects->map(function ($sub) use ($apprenant) {
            $g = Grade::where('apprenant_id', $apprenant->id)
                ->whereHas('evaluation', fn($q) => $q->where('subject_id', $sub->id))
                ->get();
            return [
                'subject' => $sub,
                'avg'     => $g->isNotEmpty() ? round($g->avg('score'), 2) : null,
                'count'   => $g->count(),
                'min'     => $g->isNotEmpty() ? $g->min('score') : null,
                'max'     => $g->isNotEmpty() ? $g->max('score') : null,
            ];
        });

        $moyenneGenerale = $this->calculerMoyenne($apprenant, $subjects);

        return view('parent.notes', compact(
            'user', 'institution', 'apprenant',
            'grades', 'subjects', 'moyennesParMatiere',
            'moyenneGenerale', 'subjectFilter'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     | 3. FINANCES D'UN ENFANT
     | GET /parent/enfant/{apprenant}/finances
     ───────────────────────────────────────────────────────────── */
    public function finances(Apprenant $apprenant, Request $request)
    {
        $user = Auth::user() ?? redirect()->route('login')->send();
        $this->assertParentOf($apprenant);

        $institution = $apprenant->institution;
        $annee       = $request->get('annee', $institution?->academic_year ?? date('Y').'-'.(date('Y') + 1));
        $apprenant->load(['classe', 'niveau']);

        $finances = FinancialRecord::where('apprenant_id', $apprenant->id)
            ->where('annee_academique', $annee)
            ->orderBy('mois')
            ->get();

        $allFinances  = FinancialRecord::where('apprenant_id', $apprenant->id)
            ->orderBy('annee_academique')->orderBy('mois')->get();

        $anneesDispos = FinancialRecord::where('apprenant_id', $apprenant->id)
            ->distinct()->pluck('annee_academique')->sort()->values();
        if (! $anneesDispos->contains($annee)) $anneesDispos->prepend($annee);

        $moisLabels = FinancialRecord::moisLabels();

        $totaux = [
            'du'    => $finances->sum('montant_du'),
            'paye'  => $finances->sum('montant_paye'),
            'reste' => $finances->sum('montant_reste'),
        ];

        return view('parent.finances', compact(
            'user', 'institution', 'apprenant',
            'finances', 'allFinances', 'annee', 'anneesDispos',
            'moisLabels', 'totaux'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     | 4. DISCIPLINAIRE D'UN ENFANT (via DisciplinaireController)
     |    Déjà géré par DisciplinaireController::parentView()
     ───────────────────────────────────────────────────────────── */

    /* ─────────────────────────────────────────────────────────────
     | HELPER : vérifie que l'utilisateur est bien parent de cet apprenant
     ───────────────────────────────────────────────────────────── */
    private function assertParentOf(Apprenant $apprenant): void
    {
        $user   = Auth::user();
        $parent = SchoolParent::where('user_id', $user->id)->first();

        if (! $parent) abort(403, 'Profil parent introuvable.');

        $linked = $parent->apprenants()->where('apprenants.id', $apprenant->id)->exists();
        if (! $linked) abort(403, 'Cet élève n\'est pas lié à votre compte.');
    }

    /* ─────────────────────────────────────────────────────────────
     | HELPER : calcule la moyenne générale pondérée
     ───────────────────────────────────────────────────────────── */
    private function calculerMoyenne(Apprenant $apprenant, $subjects): ?float
    {
        if ($subjects->isEmpty()) return null;
        $num = $den = 0;
        foreach ($subjects as $sub) {
            $avg = Grade::where('apprenant_id', $apprenant->id)
                ->whereHas('evaluation', fn($q) => $q->where('subject_id', $sub->id))
                ->avg('score');
            if ($avg !== null) {
                $coeff = $sub->coefficient ?? 1;
                $num  += $avg * $coeff;
                $den  += $coeff;
            }
        }
        return $den > 0 ? round($num / $den, 2) : null;
    }
}