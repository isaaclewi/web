<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\LibraryBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuperAdminLibraryController extends Controller
{
    /**
     * Le superadmin N'A PAS d'institution_id.
     * Les livres "globaux" ont institution_id = NULL et is_global = true.
     */

    public function index(Request $request)
    {
        $query = LibraryBook::query()->with('institution', 'uploader');

        // Filtres
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($s) =>
                $s->where('title', 'like', "%$q%")
                  ->orWhere('author', 'like', "%$q%")
            );
        }
        if ($request->filled('category'))    $query->where('category',    $request->category);
        if ($request->filled('file_type'))   $query->where('file_type',   $request->file_type);
        if ($request->filled('uploader_role')) {
            $query->whereHas('uploader', fn($u) =>
                $u->whereHas('roles', fn($r) => $r->where('name', $request->uploader_role))
            );
        }

        $books      = $query->latest()->paginate(24)->withQueryString();
        $categories = LibraryBook::distinct()->pluck('category')->filter()->sort()->values();

        $stats = [
            'total'     => LibraryBook::count(),
            'global'    => LibraryBook::whereNull('institution_id')->where('is_global', true)->count(),
            'by_inst'   => LibraryBook::whereNotNull('institution_id')->count(),
            'views'     => LibraryBook::sum('views'),
            'downloads' => LibraryBook::sum('downloads'),
        ];

        return view('superadmin.library', compact('books', 'categories', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'nullable|string|max:255',
            'isbn'           => 'nullable|string|max:30',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string|max:100',
            'level'          => 'nullable|string|max:100',
            'file'           => 'required|file|max:51200', // 50 MB
            'cover'          => 'nullable|image|max:4096',
            'allow_download' => 'nullable|boolean',
            'is_published'   => 'nullable|boolean',
        ]);

        // Upload fichier principal
        $filePath = $request->file('file')->store('library/books', 'public');
        $fileType = $request->file('file')->getClientOriginalExtension();
        $fileSize = $request->file('file')->getSize();

        // Cover optionnelle
        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('library/covers', 'public');
        }

        LibraryBook::create([
            // SuperAdmin → institution_id = NULL, is_global = true
            'institution_id'  => null,
            'uploaded_by'     => Auth::id(),
            'title'           => $data['title'],
            'author'          => $data['author'] ?? null,
            'isbn'            => $data['isbn'] ?? null,
            'description'     => $data['description'] ?? null,
            'category'        => $data['category'] ?? null,
            'level'           => $data['level'] ?? null,
            'file_path'       => $filePath,
            'file_type'       => strtolower($fileType),
            'file_size'       => $fileSize,
            'cover_path'      => $coverPath,
            'allow_download'  => (bool) ($data['allow_download'] ?? true),
            'is_published'    => (bool) ($data['is_published'] ?? true),
            'is_global'       => true, // Toujours global quand créé par le superadmin
        ]);

        return redirect()->route('superadmin.library')->with('success', 'Livre ajouté à la bibliothèque globale.');
    }

    public function update(Request $request, LibraryBook $book)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'nullable|string|max:255',
            'isbn'           => 'nullable|string|max:30',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string|max:100',
            'level'          => 'nullable|string|max:100',
            'cover'          => 'nullable|image|max:4096',
            'allow_download' => 'nullable|boolean',
            'is_published'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('cover')) {
            if ($book->cover_path) Storage::disk('public')->delete($book->cover_path);
            $data['cover_path'] = $request->file('cover')->store('library/covers', 'public');
        }

        $book->update([
            'title'          => $data['title'],
            'author'         => $data['author'] ?? null,
            'isbn'           => $data['isbn'] ?? null,
            'description'    => $data['description'] ?? null,
            'category'       => $data['category'] ?? null,
            'level'          => $data['level'] ?? null,
            'cover_path'     => $data['cover_path'] ?? $book->cover_path,
            'allow_download' => (bool) ($data['allow_download'] ?? $book->allow_download),
            'is_published'   => (bool) ($data['is_published'] ?? $book->is_published),
        ]);

        return redirect()->route('superadmin.library')->with('success', 'Livre mis à jour.');
    }

    public function toggle(LibraryBook $book)
    {
        $book->update(['is_published' => !$book->is_published]);
        $status = $book->is_published ? 'publié' : 'masqué';
        return redirect()->route('superadmin.library')->with('success', "Livre $status.");
    }

    public function destroy(LibraryBook $book)
    {
        if ($book->file_path)  Storage::disk('public')->delete($book->file_path);
        if ($book->cover_path) Storage::disk('public')->delete($book->cover_path);
        $book->delete();
        return redirect()->route('superadmin.library')->with('success', 'Livre supprimé.');
    }

    /**
     * Lecture d'un livre — incrémente le compteur de vues.
     * Les livres globaux (institution_id = null) sont accessibles à tous.
     */
    public function read(LibraryBook $book)
    {
        // Vérifier accès : global OU même institution que l'utilisateur
        $user = Auth::user();
        $canAccess = $book->is_global
            || $user->hasRole('superadmin')
            || (int) $book->institution_id === (int) $user->institution_id;

        if (!$canAccess) {
            abort(403, 'Accès refusé à cette ressource.');
        }

        $book->increment('views');
        return view('library.read', compact('book'));
    }
}
