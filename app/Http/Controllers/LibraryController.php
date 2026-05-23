<?php

namespace App\Http\Controllers;

use App\Models\LibraryBook;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class LibraryController extends Controller
{
    /* ══════════════════════════════════════════════════════
     |  HELPERS PRIVÉS
    ══════════════════════════════════════════════════════ */

    /** Retourne l'institution_id de l'utilisateur courant (null = superadmin). */
    private function currentInstitutionId(): ?int
    {
        return Auth::user()->institution_id ?? null;
    }

    /** Extension → type normalisé */
    private function resolveFileType(string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf'  => 'pdf',
            'doc', 'docx' => 'docx',
            'ppt', 'pptx' => 'pptx',
            'xls', 'xlsx' => 'xlsx',
            'epub' => 'epub',
            default => 'other',
        };
    }

    /** Règles de validation communes pour le formulaire livre */
    private function bookRules(bool $isUpdate = false): array
    {
        $fileRule = $isUpdate
            ? ['nullable', File::types(['pdf','doc','docx','ppt','pptx','xls','xlsx','epub'])->max(50 * 1024)]
            : ['required', File::types(['pdf','doc','docx','ppt','pptx','xls','xlsx','epub'])->max(50 * 1024)];

        return [
            'title'          => 'required|string|max:255',
            'author'         => 'nullable|string|max:255',
            'isbn'           => 'nullable|string|max:50',
            'description'    => 'nullable|string|max:2000',
            'category'       => 'nullable|string|max:100',
            'level'          => 'nullable|string|max:100',
            'language'       => 'nullable|string|max:10',
            'allow_download' => 'boolean',
            'is_published'   => 'boolean',
            'cover'          => ['nullable', File::types(['jpg','jpeg','png','webp'])->max(2 * 1024)],
            'file'           => $fileRule,
        ];
    }

    /** Upload du fichier et de la couverture. Retourne les paths. */
    private function handleUploads(Request $request, ?LibraryBook $existing = null): array
    {
        $data = [];

        if ($request->hasFile('file')) {
            // Supprimer l'ancien fichier
            if ($existing && $existing->file_path) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $file = $request->file('file');
            $ext  = $file->getClientOriginalExtension();
            $path = $file->storeAs(
                'library/books',
                Str::slug($request->title) . '_' . time() . '.' . $ext,
                'public'
            );
            $data['file_path']  = $path;
            $data['file_type']  = $this->resolveFileType($ext);
            $data['file_size']  = $file->getSize();
        }

        if ($request->hasFile('cover')) {
            if ($existing && $existing->cover_path) {
                Storage::disk('public')->delete($existing->cover_path);
            }
            $cover = $request->file('cover');
            $data['cover_path'] = $cover->storeAs(
                'library/covers',
                Str::slug($request->title) . '_cover_' . time() . '.' . $cover->getClientOriginalExtension(),
                'public'
            );
        }

        return $data;
    }

    /* ══════════════════════════════════════════════════════
     |  ① SUPERADMIN — toutes les ressources
    ══════════════════════════════════════════════════════ */

    /** GET /superadmin/library */
    public function superIndex(Request $request)
    {
        $query = LibraryBook::with('institution', 'uploader')
            ->withTrashed();

        $this->applyFilters($query, $request);

        $books        = $query->latest()->paginate(15)->withQueryString();
        $institutions = Institution::orderBy('name')->get();
        $categories   = LibraryBook::categories();
        $stats        = $this->globalStats();

        return view('superadmin.index', compact(
            'books', 'institutions', 'categories', 'stats'
        ));
    }

    /** POST /superadmin/library */
    public function superStore(Request $request)
    {
        $validated = $request->validate($this->bookRules());
        $uploads   = $this->handleUploads($request);

        LibraryBook::create(array_merge($validated, $uploads, [
            'institution_id' => null,   // global = visible par tous
            'uploaded_by'    => Auth::id(),
            'uploader_role'  => 'superadmin',
            'allow_download' => $request->boolean('allow_download', true),
            'is_published'   => $request->boolean('is_published', true),
        ]));

        return back()->with('success', 'Livre ajouté avec succès à la bibliothèque globale.');
    }

    /** PUT /superadmin/library/{book} */
    public function superUpdate(Request $request, LibraryBook $book)
    {
        $validated = $request->validate($this->bookRules(true));
        $uploads   = $this->handleUploads($request, $book);

        $book->update(array_merge($validated, $uploads, [
            'allow_download' => $request->boolean('allow_download', true),
            'is_published'   => $request->boolean('is_published', true),
        ]));

        return back()->with('success', 'Livre mis à jour.');
    }

    /** DELETE /superadmin/library/{book} */
    public function superDestroy(LibraryBook $book)
    {
        Storage::disk('public')->delete([$book->file_path, $book->cover_path]);
        $book->forceDelete();

        return back()->with('success', 'Livre supprimé définitivement.');
    }

    /** PATCH /superadmin/library/{book}/toggle */
    public function superTogglePublish(LibraryBook $book)
    {
        $book->update(['is_published' => ! $book->is_published]);

        return back()->with('success', $book->is_published ? 'Livre publié.' : 'Livre masqué.');
    }

    /* ══════════════════════════════════════════════════════
     |  ② ADMIN (DIRECTEUR) — ressources de son institution
    ══════════════════════════════════════════════════════ */

    /** GET /admin/library */
    public function adminIndex(Request $request)
    {
        $institutionId = $this->currentInstitutionId();
        $institution   = Auth::user()->institution;

        $myBooks = LibraryBook::with('uploader')
            ->forInstitution($institutionId);

        $this->applyFilters($myBooks, $request);

        $myBooks    = $myBooks->latest()->paginate(12)->withQueryString();
        $categories = LibraryBook::categories();
        $stats      = $this->institutionStats($institutionId);

        return view('admin.index', compact(
            'myBooks', 'categories', 'stats', 'institution'
        ));
    }

    /** POST /admin/library */
    public function adminStore(Request $request)
    {
        $validated = $request->validate($this->bookRules());
        $uploads   = $this->handleUploads($request);

        LibraryBook::create(array_merge($validated, $uploads, [
            'institution_id' => $this->currentInstitutionId(),
            'uploaded_by'    => Auth::id(),
            'uploader_role'  => 'directeur',
            'allow_download' => $request->boolean('allow_download', true),
            'is_published'   => $request->boolean('is_published', true),
        ]));

        return back()->with('success', 'Livre ajouté à la bibliothèque de votre établissement.');
    }

    /** PUT /admin/library/{book} */
    public function adminUpdate(Request $request, LibraryBook $book)
    {
        abort_if($book->institution_id !== $this->currentInstitutionId(), 403);

        $validated = $request->validate($this->bookRules(true));
        $uploads   = $this->handleUploads($request, $book);

        $book->update(array_merge($validated, $uploads, [
            'allow_download' => $request->boolean('allow_download', true),
            'is_published'   => $request->boolean('is_published', true),
        ]));

        return back()->with('success', 'Livre mis à jour.');
    }

    /** DELETE /admin/library/{book} */
    public function adminDestroy(LibraryBook $book)
    {
        abort_if($book->institution_id !== $this->currentInstitutionId(), 403);

        Storage::disk('public')->delete([$book->file_path, $book->cover_path]);
        $book->delete();

        return back()->with('success', 'Livre supprimé.');
    }

    /* ══════════════════════════════════════════════════════
     |  ③ ENSEIGNANT — consulte + ajoute des cours
    ══════════════════════════════════════════════════════ */

    /** GET /teacher/library */
    public function teacherIndex(Request $request)
    {
        $user          = Auth::user();
        $teacher       = $user->teacher ?? $user->teacherProfile ?? null;
        $institution   = $user->institution;
        $institutionId = $this->currentInstitutionId();

        $query = LibraryBook::visibleFor($institutionId);
        $this->applyFilters($query, $request);

        $books = $query->latest()->paginate(12)->withQueryString();

        // Livres uploadés par cet enseignant
        $myUploads = LibraryBook::where('uploaded_by', Auth::id())
            ->where('uploader_role', 'teacher')
            ->latest()->get();

        $categories = LibraryBook::categories();

        return view('teacher.index', compact(
            'books', 'myUploads', 'categories', 'institution', 'teacher'
        ));
    }

    /** POST /teacher/library */
    public function teacherStore(Request $request)
    {
        $validated = $request->validate($this->bookRules());
        $uploads   = $this->handleUploads($request);

        LibraryBook::create(array_merge($validated, $uploads, [
            'institution_id' => $this->currentInstitutionId(),
            'uploaded_by'    => Auth::id(),
            'uploader_role'  => 'teacher',
            'allow_download' => $request->boolean('allow_download', true),
            'is_published'   => $request->boolean('is_published', true),
        ]));

        return back()->with('success', 'Cours/Ressource ajouté(e) à la bibliothèque.');
    }

    /** DELETE /teacher/library/{book} — uniquement ses propres uploads */
    public function teacherDestroy(LibraryBook $book)
    {
        abort_if($book->uploaded_by !== Auth::id(), 403, 'Action non autorisée.');

        Storage::disk('public')->delete([$book->file_path, $book->cover_path]);
        $book->delete();

        return back()->with('success', 'Ressource supprimée.');
    }

    /* ══════════════════════════════════════════════════════
     |  ④ ÉTUDIANT — lecture uniquement (téléchargement selon flag)
    ══════════════════════════════════════════════════════ */

    /** GET /student/library */
    public function studentIndex(Request $request)
    {
        $user          = Auth::user();
        $apprenant     = $user->apprenant;
        $institution   = $user->institution;
        $institutionId = $this->currentInstitutionId();

        $query = LibraryBook::visibleFor($institutionId);
        $this->applyFilters($query, $request);

        $books      = $query->latest()->paginate(12)->withQueryString();
        $categories = LibraryBook::categories();

        return view('student.index', compact(
            'books', 'categories', 'institution', 'apprenant'
        ));
    }

    /* ══════════════════════════════════════════════════════
     |  ⑤ ACTIONS COMMUNES — lire & télécharger
    ══════════════════════════════════════════════════════ */

    /**
     * Lecture en ligne (iframe / viewer).
     * GET /library/{book}/read
     */
    public function read(LibraryBook $book)
    {
        $institutionId = $this->currentInstitutionId();
        $this->authorizeView($book, $institutionId);

        $book->incrementViews();

        return view('read', compact('book'));
    }

    /**
     * Téléchargement du fichier.
     * GET /library/{book}/download
     */
    public function download(LibraryBook $book)
    {
        $institutionId = $this->currentInstitutionId();
        $this->authorizeView($book, $institutionId);

        if (! $book->allow_download) {
            abort(403, 'Le téléchargement de ce document n\'est pas autorisé.');
        }

        abort_unless(Storage::disk('public')->exists($book->file_path), 404);

        $book->incrementDownloads();

        return Storage::disk('public')->download(
            $book->file_path,
            Str::slug($book->title) . '.' . pathinfo($book->file_path, PATHINFO_EXTENSION)
        );
    }

    /* ══════════════════════════════════════════════════════
     |  HELPERS INTERNES
    ══════════════════════════════════════════════════════ */

    /** Vérifie qu'un utilisateur peut voir ce livre. */
    private function authorizeView(LibraryBook $book, ?int $institutionId): void
    {
        $canSee = $book->is_published && (
            is_null($book->institution_id) ||
            $book->institution_id === $institutionId
        );
        abort_if(! $canSee, 403);
    }

    /** Applique les filtres communs (search, category, type, level). */
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sub) =>
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
            );
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }
        if ($request->filled('level')) {
            $query->where('level', 'like', "%{$request->level}%");
        }
        if ($request->filled('uploader_role')) {
            $query->where('uploader_role', $request->uploader_role);
        }
    }

    /** Stats globales pour le superadmin. */
    private function globalStats(): array
    {
        return [
            'total'       => LibraryBook::count(),
            'global'      => LibraryBook::whereNull('institution_id')->count(),
            'by_inst'     => LibraryBook::whereNotNull('institution_id')->count(),
            'views'       => LibraryBook::sum('views'),
            'downloads'   => LibraryBook::sum('downloads'),
            'institutions'=> Institution::count(),
        ];
    }

    /** Stats pour une institution. */
    private function institutionStats(int $institutionId): array
    {
        $base = LibraryBook::forInstitution($institutionId);
        return [
            'total'     => (clone $base)->count(),
            'published' => (clone $base)->where('is_published', true)->count(),
            'hidden'    => (clone $base)->where('is_published', false)->count(),
            'views'     => (clone $base)->sum('views'),
            'downloads' => (clone $base)->sum('downloads'),
        ];
    }
}