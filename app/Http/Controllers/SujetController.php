<?php

namespace App\Http\Controllers;
 
use App\Models\Classe;
use App\Models\SujetExamen;
use App\Models\SujetFichier;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
 
class SujetController extends Controller
{
    /* ────────────────────────────────────────────────────────
     | HELPERS
     ──────────────────────────────────────────────────────── */
 
    private function getTeacher(): Teacher
    {
        $t = Teacher::where('user_id', Auth::id())->first();
        abort_if(! $t, 403, 'Profil enseignant introuvable.');
        return $t;
    }
 
    private function getInstitution(): Institution
    {
        $inst = Auth::user()?->institution;
        abort_if(! $inst, 403, 'Aucun établissement lié à votre compte.');
        return $inst;
    }
 
    private function assertAdminOwns(SujetExamen $sujet): void
    {
        $instId = $this->getInstitution()->id;
        abort_if((int) $sujet->institution_id !== $instId, 403);
    }
 
    /* ════════════════════════════════════════════════════════
     | CÔTÉ ENSEIGNANT
     ════════════════════════════════════════════════════════ */
 
    /**
     * GET /teacher/sujets
     */
    public function teacherIndex(Request $request)
    {
        $teacher = $this->getTeacher();
 
        $query = SujetExamen::where('teacher_id', $teacher->id)
            ->with(['subject', 'classe', 'fichiers'])
            ->latest();
 
        if ($request->filled('type_filter')) {
            $query->where('type', $request->type_filter);
        }
 
        $sujets  = $query->paginate(12)->withQueryString();
 
        $subjects = Subject::where('teacher_id', $teacher->id)->with('classe')->get();
        $classes  = $teacher->classes;
 
        $stats = [
            'total'      => SujetExamen::where('teacher_id', $teacher->id)->count(),
            'en_attente' => SujetExamen::where('teacher_id', $teacher->id)->where('statut', 'en_attente')->count(),
            'valide'     => SujetExamen::where('teacher_id', $teacher->id)->where('statut', 'valide')->count(),
            'ce_mois'    => SujetExamen::where('teacher_id', $teacher->id)
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
        ];
 
        return view('teacher.sujets', compact('teacher', 'sujets', 'subjects', 'classes', 'stats'));
    }
 
    /**
     * POST /teacher/sujets
     */
    public function teacherStore(Request $request)
    {
        $teacher = $this->getTeacher();
 
        $data = $request->validate([
            'titre'          => 'required|string|max:255',
            'type'           => 'required|in:controle,examen,composition,tp,interro,devoir,rattrapage',
            'subject_id'     => 'required|exists:subjects,id',
            'classe_id'      => 'nullable|exists:classes,id',
            'date_evaluation'=> 'nullable|date',
            'duree_minutes'  => 'nullable|integer|min:15|max:480',
            'instructions'   => 'nullable|string|max:2000',
            'fichiers'       => 'required|array|min:1|max:5',
            'fichiers.*'     => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);
 
        // Vérifier que la matière appartient bien à cet enseignant
        Subject::where('id', $data['subject_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();
 
        $sujet = SujetExamen::create([
            'teacher_id'      => $teacher->id,
            'institution_id'  => $teacher->institution_id,
            'subject_id'      => $data['subject_id'],
            'classe_id'       => $data['classe_id'] ?? null,
            'titre'           => $data['titre'],
            'type'            => $data['type'],
            'date_evaluation' => $data['date_evaluation'] ?? null,
            'duree_minutes'   => $data['duree_minutes'] ?? null,
            'instructions'    => $data['instructions'] ?? null,
            'statut'          => 'en_attente',
        ]);
 
        // Stockage des fichiers
        foreach ($request->file('fichiers') as $file) {
            $path = $file->store(
                'sujets/' . $teacher->institution_id . '/' . $sujet->id,
                'local'
            );
            SujetFichier::create([
                'sujet_examen_id' => $sujet->id,
                'nom_original'    => $file->getClientOriginalName(),
                'chemin'          => $path,
                'mime_type'       => $file->getMimeType(),
                'taille_octets'   => $file->getSize(),
                'extension'       => $file->getClientOriginalExtension(),
            ]);
        }
 
        // Notification admin (optionnel — décommenter si Notification créée)
        // Notification::send(
        //     User::where('institution_id', $teacher->institution_id)->role('admin')->get(),
        //     new NouveauSujetNotification($sujet)
        // );
 
        return redirect()->route('teacher.sujets.index')
            ->with('success', "Sujet « {$sujet->titre} » envoyé à l'administration.");
    }
 
    /**
     * DELETE /teacher/sujets/{sujet}
     * Seul l'enseignant propriétaire peut retirer un sujet encore en attente.
     */
    public function teacherDestroy(SujetExamen $sujet)
    {
        $teacher = $this->getTeacher();
        abort_if($sujet->teacher_id !== $teacher->id, 403);
        abort_if($sujet->statut !== 'en_attente', 422, 'Ce sujet ne peut plus être retiré.');
 
        foreach ($sujet->fichiers as $f) {
            Storage::disk('local')->delete($f->chemin);
        }
        $sujet->fichiers()->delete();
        $sujet->delete();
 
        return redirect()->route('teacher.sujets.index')
            ->with('success', 'Sujet retiré.');
    }
 
    /**
     * GET /teacher/sujets/fichier/{fichier}/download
     * Téléchargement sécurisé — réservé à l'enseignant propriétaire.
     */
    public function teacherDownload(SujetFichier $fichier)
    {
        $teacher = $this->getTeacher();
        abort_if($fichier->sujetExamen->teacher_id !== $teacher->id, 403);
 
        if (! Storage::disk('local')->exists($fichier->chemin)) {
            abort(404, 'Fichier introuvable.');
        }
 
        return Storage::disk('local')->download($fichier->chemin, $fichier->nom_original, [
            'Content-Type' => $fichier->mime_type ?? 'application/octet-stream',
        ]);
    }
 
    /* ════════════════════════════════════════════════════════
     | CÔTÉ ADMINISTRATION
     ════════════════════════════════════════════════════════ */
 
    /**
     * GET /admin/sujets
     */
    public function adminIndex(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;
 
        $query = SujetExamen::where('institution_id', $instId)
            ->with(['teacher', 'subject', 'classe', 'fichiers'])
            ->latest();
 
        // Filtre statut
        if ($request->filled('statut') && $request->statut !== 'all') {
            $query->where('statut', $request->statut);
        }
 
        // Filtre type
        if ($request->filled('type_filter')) {
            $query->where('type', $request->type_filter);
        }
 
        // Filtre enseignant
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
 
        // Recherche texte
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($s) =>
                $s->where('titre', 'like', "%{$q}%")
                  ->orWhere('instructions', 'like', "%{$q}%")
                  ->orWhereHas('teacher', fn ($t) =>
                      $t->where('nom', 'like', "%{$q}%")
                        ->orWhere('prenom', 'like', "%{$q}%")
                  )
                  ->orWhereHas('subject', fn ($su) =>
                      $su->where('name', 'like', "%{$q}%")
                  )
            );
        }
 
        $sujets   = $query->paginate(12)->withQueryString();
        $teachers = Teacher::where('institution_id', $instId)->orderBy('nom')->get();
 
        $stats = [
            'total'      => SujetExamen::where('institution_id', $instId)->count(),
            'en_attente' => SujetExamen::where('institution_id', $instId)->where('statut', 'en_attente')->count(),
            'recu'       => SujetExamen::where('institution_id', $instId)->where('statut', 'recu')->count(),
            'valide'     => SujetExamen::where('institution_id', $instId)->where('statut', 'valide')->count(),
            'rejete'     => SujetExamen::where('institution_id', $instId)->where('statut', 'rejete')->count(),
            'ce_mois'    => SujetExamen::where('institution_id', $instId)
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
            'nb_teachers'=> SujetExamen::where('institution_id', $instId)->distinct('teacher_id')->count('teacher_id'),
        ];
 
        return view('admin.sujets', compact('institution', 'sujets', 'teachers', 'stats'));
    }
 
    /**
     * PATCH /admin/sujets/{sujet}/statut
     * Change le statut et enregistre un feedback éventuel.
     */
    public function adminStatut(Request $request, SujetExamen $sujet)
    {
        $this->assertAdminOwns($sujet);
 
        $data = $request->validate([
            'statut'        => 'required|in:recu,valide,rejete,archive,en_attente',
            'feedback_admin'=> 'nullable|string|max:2000',
        ]);
 
        $sujet->update([
            'statut'         => $data['statut'],
            'feedback_admin' => $data['feedback_admin'] ?? $sujet->feedback_admin,
            'traite_par'     => Auth::id(),
            'traite_at'      => now(),
        ]);
 
        $labels = [
            'recu'      => 'marqué reçu',
            'valide'    => 'validé',
            'rejete'    => 'rejeté',
            'archive'   => 'archivé',
            'en_attente'=> 'remis en attente',
        ];
 
        return redirect()->back()
            ->with('success', "Sujet « {$sujet->titre} » {$labels[$data['statut']]}.");
    }
 
    /**
     * GET /admin/sujets/fichier/{fichier}/download
     * Téléchargement admin — vérifie l'appartenance à l'institution.
     */
    public function adminDownload(SujetFichier $fichier)
    {
        $this->assertAdminOwns($fichier->sujetExamen);
 
        if (! Storage::disk('local')->exists($fichier->chemin)) {
            abort(404, 'Fichier introuvable.');
        }
 
        return Storage::disk('local')->download($fichier->chemin, $fichier->nom_original, [
            'Content-Type' => $fichier->mime_type ?? 'application/octet-stream',
        ]);
    }
}