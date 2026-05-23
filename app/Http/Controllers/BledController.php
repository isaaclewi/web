<?php

namespace App\Http\Controllers;

use App\Models\Apprenant;
use App\Models\Archive;
use App\Models\Bulletin;
use App\Models\Classe;
use App\Models\EmploiDuTemps;
use App\Models\FinancialRecord;
use App\Models\Institution;
use App\Models\Staff;
use App\Models\SuiviDisciplinaire;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * BLED — Bureau de Liaison et d'Enregistrement des Données
 *
 * Responsable de l'archivage et de la consultation des données
 * passées des établissements académiques.
 */
class BledController extends Controller
{
    /* ─────────────────────────────────────────────────────────────
     | HELPER : institution courante
     ───────────────────────────────────────────────────────────── */
    private function getInstitution(): Institution
    {
        $inst = Auth::user()?->institution;
        if (! $inst) {
            abort(403, 'Aucun établissement lié à votre compte.');
        }

        return $inst;
    }

    private function assertArchiveOwner(Archive $archive, int $instId): void
    {
        if ((int) $archive->institution_id !== $instId) {
            abort(403, 'Archive introuvable dans votre établissement.');
        }
    }

    public function generateCsvPublic($institutionId, $categorie, $annee, $filtre = '')
    {
        $data = collect();

        switch ($categorie) {
            case 'apprenants':
                $data = \App\Models\User::where('institution_id', $institutionId)->get();
                break;

            case 'enseignants':
                $data = \App\Models\User::where('institution_id', $institutionId)
                    ->where('role', 'enseignant')
                    ->get();
                break;

            default:
                $data = collect();
        }

        $csv = '';

        if ($data->count()) {
            $headers = array_keys($data->first()->toArray());
            $csv .= implode(',', $headers)."\n";

            foreach ($data as $row) {
                $csv .= implode(',', $row->toArray())."\n";
            }
        }

        return $csv;
    }

    /* ─────────────────────────────────────────────────────────────
     | INDEX — page principale BLED
     | GET /admin/bled
     ───────────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $institution = $this->getInstitution();
        $instId = $institution->id;
        $annee = $request->get('annee', $institution->academic_year ?? date('Y').'-'.(date('Y') + 1));
        $categorie = $request->get('cat', 'apprenants');
        $search = $request->get('q', '');
        $typeExport = $request->get('type_export', '');

        // ── Années disponibles (union de toutes les tables) ──────────
        $anneesApprenants = Apprenant::where('institution_id', $instId)->distinct()->pluck('annee_academique');
        $anneesFinances = Financialrecord::where('institution_id', $instId)->distinct()->pluck('annee_academique');
        $anneesBulletins = Bulletin::where('institution_id', $instId)->distinct()->pluck('annee_academique');
        $anneesDispos = $anneesApprenants
            ->merge($anneesFinances)
            ->merge($anneesBulletins)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if (! $anneesDispos->contains($annee)) {
            $anneesDispos->push($annee);
            $anneesDispos = $anneesDispos->sort()->values();
        }

        // ── Stats globales ───────────────────────────────────────────
        $statsGlobaux = [
            'total_archives' => Archive::where('institution_id', $instId)->count(),
            'nb_annees' => $anneesDispos->count(),
            'total_apprenants' => Apprenant::where('institution_id', $instId)->count(),
            'derniere_archive' => optional(Archive::where('institution_id', $instId)->latest()->first())->nom,
            'derniere_annee' => optional(Archive::where('institution_id', $instId)->latest()->first())->annee_academique,
        ];

        // ── Données par catégorie (paginées) ─────────────────────────
        $archivesApprenants = $this->queryApprenants($instId, $annee, $search)->paginate(25)->withQueryString();
        $archivesEnseignants = $this->queryEnseignants($instId, $search)->paginate(25)->withQueryString();
        $archivesBulletins = $this->queryBulletins($instId, $annee)->paginate(25)->withQueryString();
        $archivesFinances = $this->queryFinances($instId, $annee, $search)->paginate(25)->withQueryString();
        $archivesDisciplinaire = $this->queryDisciplinaire($instId, $annee)->paginate(25)->withQueryString();
        $archivesClasses = Classe::where('institution_id', $instId)->withCount('apprenants')->with(['niveau', 'filiere'])->orderBy('name')->get();
        $archivesPlanning = EmploiDuTemps::where('institution_id', $instId)->where('annee_academique', $annee)->with(['classe', 'subject', 'teacher'])->orderBy('jour')->orderBy('heure_debut')->paginate(30)->withQueryString();
        $archivesStaff = Staff::where('institution_id', $instId)->with(['administrativeUnit'])->orderBy('nom')->paginate(25)->withQueryString();

        // ── Archives créées manuellement ─────────────────────────────
        $archivesQuery = Archive::where('institution_id', $instId)->latest();
        if ($typeExport) {
            $archivesQuery->where('type_export', $typeExport);
        }
        $archives = $archivesQuery->paginate(10)->withQueryString();

        // ── Classes disponibles pour le modal ────────────────────────
        $classesDispos = Classe::where('institution_id', $instId)->orderBy('name')->get();

        return view('admin.bled_archive', compact(
            'institution', 'annee', 'categorie', 'search', 'anneesDispos',
            'statsGlobaux',
            'archivesApprenants', 'archivesEnseignants', 'archivesBulletins',
            'archivesFinances', 'archivesDisciplinaire', 'archivesClasses',
            'archivesPlanning', 'archivesStaff',
            'archives', 'classesDispos',
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     | STORE — créer et persister une archive
     | POST /admin/bled
     ───────────────────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $institution = $this->getInstitution();
        $instId = $institution->id;

        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'annee_academique' => 'required|string|max:20',
            'categorie' => 'required|in:complet,apprenants,enseignants,bulletins,finances,disciplinaire,classes,planning,staff',
            'type_export' => 'required|in:annuel,trimestriel,manuel',
            'description' => 'nullable|string|max:1000',
            'classe_id' => 'nullable|exists:classes,id',
            'periode' => 'nullable|string|max:30',
        ]);

        $annee = $data['annee_academique'];
        $categorie = $data['categorie'];
        $search = '';

        // ── Génération CSV en mémoire ─────────────────────────────────
        $csvContent = $this->generateCsv($instId, $categorie, $annee, $search, $data['classe_id'] ?? null, $data['periode'] ?? null);

        // ── Stockage sur disque ───────────────────────────────────────
        $fileName = 'archives/'.$instId.'/'.$annee.'/'.$categorie.'_'.now()->format('Ymd_His').'.csv';
        Storage::disk('local')->put($fileName, $csvContent);
        $taille = Storage::disk('local')->size($fileName);

        // ── Enregistrement en base ────────────────────────────────────
        Archive::create([
            'institution_id' => $instId,
            'nom' => $data['nom'],
            'annee_academique' => $annee,
            'categorie' => $categorie,
            'type_export' => $data['type_export'],
            'description' => $data['description'] ?? null,
            'fichier_path' => $fileName,
            'taille_octets' => $taille,
            'cree_par' => Auth::id(),
        ]);

        return redirect()->route('admin.bled.index', ['annee' => $annee, 'cat' => $categorie])
            ->with('success', "Archive « {$data['nom']} » créée et enregistrée.");
    }

    /* ─────────────────────────────────────────────────────────────
     | DOWNLOAD — télécharger une archive
     | GET /admin/bled/{archive}/download
     ───────────────────────────────────────────────────────────── */
    public function download(Archive $archive)
    {
        $this->assertArchiveOwner($archive, $this->getInstitution()->id);

        if (! Storage::disk('local')->exists($archive->fichier_path)) {
            return redirect()->back()->with('error', 'Fichier introuvable. Veuillez recréer l\'archive.');
        }

        $ext = pathinfo($archive->fichier_path, PATHINFO_EXTENSION);
        $fileName = \Str::slug($archive->nom).'_'.$archive->annee_academique.'.'.$ext;
        $mimeType = $ext === 'csv' ? 'text/csv' : 'application/octet-stream';

        return Storage::disk('local')->download($archive->fichier_path, $fileName, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /* ─────────────────────────────────────────────────────────────
     | PREVIEW — aperçu rapide (50 premières lignes)
     | GET /admin/bled/{archive}/preview
     ───────────────────────────────────────────────────────────── */
    public function preview(Archive $archive)
    {
        $this->assertArchiveOwner($archive, $this->getInstitution()->id);

        if (! Storage::disk('local')->exists($archive->fichier_path)) {
            return redirect()->back()->with('error', 'Fichier introuvable.');
        }

        $content = Storage::disk('local')->get($archive->fichier_path);
        $lines = explode("\n", $content);
        $preview = array_slice($lines, 0, 51);

        $anneesDispos = Archive::where('institution_id', $this->getInstitution()->id)
            ->distinct()
            ->pluck('annee_academique');

        // ✅ AJOUT IMPORTANT : variables vides pour éviter les erreurs
        $archivesApprenants = collect();
        $archivesEnseignants = collect();
        $archivesBulletins = collect();
        $archivesFinances = collect();
        $archivesDisciplinaire = collect();
        $archivesClasses = collect();
        $archivesPlanning = collect();
        $archivesStaff = collect();

        return view('admin.bled_archive', compact(
            'archive',
            'preview',
            'anneesDispos',
            'archivesApprenants',
            'archivesEnseignants',
            'archivesBulletins',
            'archivesFinances',
            'archivesDisciplinaire',
            'archivesClasses',
            'archivesPlanning',
            'archivesStaff'
        ));
    }

    /* ─────────────────────────────────────────────────────────────
     | EXPORT À LA VOLÉE (sans stocker)
     | GET /admin/bled/export?cat=apprenants&annee=…
     ───────────────────────────────────────────────────────────── */
    public function export(Request $request)
    {
        $institution = $this->getInstitution();
        $instId = $institution->id;
        $categorie = $request->get('cat', 'apprenants');
        $annee = $request->get('annee', $institution->academic_year);
        $search = $request->get('q', '');
        $format = $request->get('format', 'csv');

        $csvContent = $this->generateCsv($instId, $categorie, $annee, $search);

        $fileName = $categorie.'_'.$annee.'_'.now()->format('Ymd').'.csv';

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    /* ─────────────────────────────────────────────────────────────
     | EXPORT GLOBAL (toutes catégories, un CSV par catégorie dans un ZIP)
     | GET /admin/bled/export/global?annee=…
     ───────────────────────────────────────────────────────────── */
    public function exportGlobal(Request $request)
    {
        $institution = $this->getInstitution();
        $instId = $institution->id;
        $annee = $request->get('annee', $institution->academic_year);

        $categories = ['apprenants', 'enseignants', 'bulletins', 'finances', 'disciplinaire', 'classes', 'planning', 'staff'];
        $tmpDir = storage_path('app/tmp_bled_'.$instId.'_'.time());
        @mkdir($tmpDir, 0755, true);

        foreach ($categories as $cat) {
            $csv = $this->generateCsv($instId, $cat, $annee, '');
            file_put_contents($tmpDir.'/'.$cat.'_'.$annee.'.csv', $csv);
        }

        $zipPath = $tmpDir.'.zip';
        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            foreach (glob($tmpDir.'/*.csv') as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Nettoyage des fichiers temporaires après envoi
        register_shutdown_function(function () use ($tmpDir, $zipPath) {
            array_map('unlink', glob($tmpDir.'/*.csv'));
            @rmdir($tmpDir);
            @unlink($zipPath);
        });

        $zipName = 'bled_'.\Str::slug($institution->name).'_'.$annee.'_'.now()->format('Ymd').'.zip';

        return response()->download($zipPath, $zipName, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /* ─────────────────────────────────────────────────────────────
     | DESTROY — supprimer une archive
     | DELETE /admin/bled/{archive}
     ───────────────────────────────────────────────────────────── */
    public function destroy(Archive $archive)
    {
        $this->assertArchiveOwner($archive, $this->getInstitution()->id);
        $nom = $archive->nom;
        Storage::disk('local')->delete($archive->fichier_path);
        $archive->delete();

        return redirect()->back()->with('success', "Archive « {$nom} » supprimée.");
    }

    /* ══════════════════════════════════════════════════════════════
     | GÉNÉRATEURS DE CSV
     ══════════════════════════════════════════════════════════════ */

    /**
     * Dispatch vers le bon générateur CSV selon la catégorie.
     */
    private function generateCsv(int $instId, string $categorie, string $annee, string $search = '', ?int $classeId = null, ?string $periode = null): string
    {
        return match ($categorie) {
            'apprenants' => $this->csvApprenants($instId, $annee, $search, $classeId),
            'enseignants' => $this->csvEnseignants($instId, $search),
            'bulletins' => $this->csvBulletins($instId, $annee, $classeId, $periode),
            'finances' => $this->csvFinances($instId, $annee, $search),
            'disciplinaire' => $this->csvDisciplinaire($instId, $annee),
            'classes' => $this->csvClasses($instId),
            'planning' => $this->csvPlanning($instId, $annee),
            'staff' => $this->csvStaff($instId),
            'complet' => $this->csvComplet($instId, $annee, $search),
            default => "Catégorie inconnue\n",
        };
    }

    /* ── CSV Apprenants ── */
    private function csvApprenants(int $instId, string $annee, string $search, ?int $classeId): string
    {
        $rows = $this->queryApprenants($instId, $annee, $search, $classeId)->get();
        $h = fopen('php://temp', 'r+');
        fprintf($h, "\xEF\xBB\xBF"); // BOM UTF-8
        fputcsv($h, ['Matricule', 'Nom', 'Prénom', 'Sexe', 'Date naissance', 'Niveau', 'Filière', 'Classe', 'Année', 'Statut', 'Date inscription']);
        foreach ($rows as $a) {
            fputcsv($h, [
                $a->matricule, $a->nom, $a->prenom,
                $a->sexe === 'M' ? 'Masculin' : ($a->sexe === 'F' ? 'Féminin' : ''),
                $a->date_naissance ?? '',
                $a->niveau->name ?? '',
                $a->filiere->name ?? '',
                $a->classe->name ?? '',
                $a->annee_academique,
                $a->status ? 'Actif' : 'Inactif',
                $a->created_at?->format('d/m/Y'),
            ]);
        }
        rewind($h);
        $content = stream_get_contents($h);
        fclose($h);

        return $content;
    }

    /* ── CSV Enseignants ── */
    private function csvEnseignants(int $instId, string $search): string
    {
        $rows = $this->queryEnseignants($instId, $search)->get();
        $h = fopen('php://temp', 'r+');
        fprintf($h, "\xEF\xBB\xBF");
        fputcsv($h, ['Matricule', 'Nom', 'Prénom', 'Sexe', 'Spécialité', 'Contrat', 'Date recrutement', 'Téléphone', 'Email', 'Statut']);
        foreach ($rows as $t) {
            fputcsv($h, [
                $t->matricule, $t->nom, $t->prenom,
                $t->sexe === 'M' ? 'Masculin' : ($t->sexe === 'F' ? 'Féminin' : ''),
                $t->specialite ?? '', $t->type_contrat ?? '',
                $t->date_recrutement ? \Carbon\Carbon::parse($t->date_recrutement)->format('d/m/Y') : '',
                $t->telephone ?? '', $t->email ?? '',
                $t->status ? 'Actif' : 'Inactif',
            ]);
        }
        rewind($h);
        $content = stream_get_contents($h);
        fclose($h);

        return $content;
    }

    /* ── CSV Bulletins ── */
    private function csvBulletins(int $instId, string $annee, ?int $classeId, ?string $periode): string
    {
        $q = $this->queryBulletins($instId, $annee);
        if ($classeId) {
            $q->where('classe_id', $classeId);
        }
        if ($periode) {
            $q->where('periode', $periode);
        }
        $rows = $q->get();
        $h = fopen('php://temp', 'r+');
        fprintf($h, "\xEF\xBB\xBF");
        fputcsv($h, ['Apprenant', 'Classe', 'Période', 'Moyenne générale', 'Rang', 'Effectif', 'Mention', 'Admis', 'Publié']);
        foreach ($rows as $b) {
            $perLabel = match ($b->periode) {
                'trimestre1' => '1er Trim.', 'trimestre2' => '2ème Trim.',
                'trimestre3' => '3ème Trim.', 'semestre1' => '1er Sem.',
                'semestre2' => '2ème Sem.',  'annuel' => 'Annuel',
                default => $b->periode,
            };
            fputcsv($h, [
                ($b->apprenant->prenom ?? '').' '.($b->apprenant->nom ?? ''),
                $b->classe->name ?? '',
                $perLabel,
                number_format($b->moyenne_generale, 2),
                $b->rang ?? '',
                $b->effectif_classe ?? '',
                $b->mention ?? '',
                $b->admis ? 'Oui' : 'Non',
                $b->publie ? 'Oui' : 'Non',
            ]);
        }
        rewind($h);
        $content = stream_get_contents($h);
        fclose($h);

        return $content;
    }

    /* ── CSV Finances ── */
    private function csvFinances(int $instId, string $annee, string $search): string
    {
        $rows = $this->queryFinances($instId, $annee, $search)->get();
        $h = fopen('php://temp', 'r+');
        fprintf($h, "\xEF\xBB\xBF");
        fputcsv($h, ['Apprenant', 'Classe', 'Mois', 'Année', 'Dû (FCFA)', 'Payé (FCFA)', 'Reste (FCFA)', 'Statut', 'Mode', 'Date paiement', 'Référence']);
        foreach ($rows as $f) {
            fputcsv($h, [
                ($f->apprenant->prenom ?? '').' '.($f->apprenant->nom ?? ''),
                $f->apprenant->classe->name ?? '',
                $f->mois_label, $f->annee_academique,
                $f->montant_du, $f->montant_paye, $f->montant_reste,
                $f->statut,
                $f->mode_paiement ?? '',
                $f->date_paiement ? \Carbon\Carbon::parse($f->date_paiement)->format('d/m/Y') : '',
                $f->reference ?? '',
            ]);
        }
        rewind($h);
        $content = stream_get_contents($h);
        fclose($h);

        return $content;
    }

    /* ── CSV Disciplinaire ── */
    private function csvDisciplinaire(int $instId, string $annee): string
    {
        $rows = $this->queryDisciplinaire($instId, $annee)->get();
        $h = fopen('php://temp', 'r+');
        fprintf($h, "\xEF\xBB\xBF");
        fputcsv($h, ['Apprenant', 'Date incident', 'Type', 'Gravité', 'Description', 'Sanction', 'Parents notifiés', 'Statut']);
        foreach ($rows as $d) {
            fputcsv($h, [
                ($d->apprenant->prenom ?? '').' '.($d->apprenant->nom ?? ''),
                $d->date_incident ? $d->date_incident->format('d/m/Y') : '',
                $d->type_label, $d->gravite_label,
                $d->description ?? '',
                $d->sanction_label,
                $d->parents_notifies ? 'Oui' : 'Non',
                $d->statut ?? '',
            ]);
        }
        rewind($h);
        $content = stream_get_contents($h);
        fclose($h);

        return $content;
    }

    /* ── CSV Classes ── */
    private function csvClasses(int $instId): string
    {
        $rows = Classe::where('institution_id', $instId)->withCount('apprenants')->with(['niveau', 'filiere'])->orderBy('name')->get();
        $h = fopen('php://temp', 'r+');
        fprintf($h, "\xEF\xBB\xBF");
        fputcsv($h, ['Nom', 'Code', 'Niveau', 'Filière', 'Effectif']);
        foreach ($rows as $c) {
            fputcsv($h, [$c->name, $c->code ?? '', $c->niveau->name ?? '', $c->filiere->name ?? '', $c->apprenants_count]);
        }
        rewind($h);
        $content = stream_get_contents($h);
        fclose($h);

        return $content;
    }

    /* ── CSV Planning ── */
    private function csvPlanning(int $instId, string $annee): string
    {
        $rows = EmploiDuTemps::where('institution_id', $instId)->where('annee_academique', $annee)->with(['classe', 'subject', 'teacher'])->orderBy('jour')->orderBy('heure_debut')->get();
        $h = fopen('php://temp', 'r+');
        fprintf($h, "\xEF\xBB\xBF");
        fputcsv($h, ['Classe', 'Matière', 'Enseignant', 'Jour', 'Heure début', 'Heure fin', 'Type', 'Salle', 'Période']);
        foreach ($rows as $e) {
            fputcsv($h, [
                $e->classe->name ?? '',
                $e->subject->name ?? '',
                $e->teacher ? $e->teacher->prenom.' '.$e->teacher->nom : '',
                ucfirst($e->jour),
                substr($e->heure_debut, 0, 5),
                substr($e->heure_fin, 0, 5),
                $e->type_label, $e->salle ?? '', $e->periode ?? '',
            ]);
        }
        rewind($h);
        $content = stream_get_contents($h);
        fclose($h);

        return $content;
    }

    /* ── CSV Staff ── */
    private function csvStaff(int $instId): string
    {
        $rows = Staff::where('institution_id', $instId)->with(['administrativeUnit'])->orderBy('nom')->get();
        $h = fopen('php://temp', 'r+');
        fprintf($h, "\xEF\xBB\xBF");
        fputcsv($h, ['Matricule', 'Nom', 'Prénom', 'Poste', 'Unité admin.', 'Téléphone', 'Email', 'Statut']);
        foreach ($rows as $s) {
            fputcsv($h, [
                $s->matricule, $s->nom, $s->prenom,
                $s->poste ?? '',
                $s->administrativeUnit->name ?? '',
                $s->telephone ?? '', $s->email ?? '',
                $s->status ? 'Actif' : 'Inactif',
            ]);
        }
        rewind($h);
        $content = stream_get_contents($h);
        fclose($h);

        return $content;
    }

    /* ── CSV Complet (toutes catégories concaténées) ── */
    private function csvComplet(int $instId, string $annee, string $search): string
    {
        $categories = ['apprenants', 'enseignants', 'bulletins', 'finances', 'disciplinaire', 'classes', 'planning', 'staff'];
        $out = '';
        foreach ($categories as $cat) {
            $out .= '=== '.strtoupper($cat)." ===\r\n";
            $out .= $this->generateCsv($instId, $cat, $annee, $search);
            $out .= "\r\n\r\n";
        }

        return $out;
    }

    /* ══════════════════════════════════════════════════════════════
     | REQUÊTES RÉUTILISABLES (base query)
     ══════════════════════════════════════════════════════════════ */

    private function queryApprenants(int $instId, string $annee, string $search = '', ?int $classeId = null)
    {
        $q = Apprenant::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['niveau', 'filiere', 'classe']);

        if ($search) {
            $q->where(fn ($s) => $s->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('matricule', 'like', "%{$search}%")
            );
        }
        if ($classeId) {
            $q->where('class_id', $classeId);
        }

        return $q->orderBy('nom');
    }

    private function queryEnseignants(int $instId, string $search = '')
    {
        $q = Teacher::where('institution_id', $instId);

        if ($search) {
            $q->where(fn ($s) => $s->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('specialite', 'like', "%{$search}%")
                ->orWhere('matricule', 'like', "%{$search}%")
            );
        }

        return $q->orderBy('nom');
    }

    private function queryBulletins(int $instId, string $annee)
    {
        return Bulletin::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['apprenant', 'classe'])
            ->orderBy('rang');
    }

    private function queryFinances(int $instId, string $annee, string $search = '')
    {
        $q = Financialrecord::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['apprenant.classe']);

        if ($search) {
            $q->whereHas('apprenant', fn ($s) => $s->where('nom', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhere('matricule', 'like', "%{$search}%")
            );
        }

        return $q->orderBy('mois');
    }

    private function queryDisciplinaire(int $instId, string $annee)
    {
        return SuiviDisciplinaire::where('institution_id', $instId)
            ->where('annee_academique', $annee)
            ->with(['apprenant'])
            ->orderBy('date_incident', 'desc');
    }
}
