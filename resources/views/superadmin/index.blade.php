@extends('superadmin.master')

@section('title', 'Bibliothèque Globale — SyntriForge Edu')

@push('styles')
<style>
    .lib-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1rem; }
    .book-card {
        background:var(--c-surface); border:1px solid var(--c-border);
        border-radius:.75rem; overflow:hidden; transition:all .2s;
        display:flex; flex-direction:column;
    }
    .book-card:hover { border-color:var(--c-border-b); box-shadow:0 8px 28px rgba(0,0,0,.35); transform:translateY(-2px); }
    .book-cover {
        height:160px; background:var(--c-surface-2);
        display:flex; align-items:center; justify-content:center;
        font-size:3rem; position:relative; overflow:hidden;
    }
    .book-cover img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
    .book-badge {
        position:absolute; top:.5rem; right:.5rem;
        font-size:.6rem; font-weight:700; padding:.2rem .5rem;
        border-radius:99px; letter-spacing:.05em;
    }
    .badge-global { background:rgba(0,212,255,.15); color:var(--c-accent); border:1px solid rgba(0,212,255,.2); }
    .badge-inst   { background:rgba(16,185,129,.12); color:#34d399; border:1px solid rgba(16,185,129,.2); }
    .badge-hidden { background:rgba(239,68,68,.12); color:#f87171; border:1px solid rgba(239,68,68,.2); }
    .book-body { padding:.875rem; flex:1; display:flex; flex-direction:column; gap:.4rem; }
    .book-title { font-size:.8rem; font-weight:600; color:var(--c-text); line-height:1.4;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .book-meta  { font-size:.68rem; color:var(--c-muted); }
    .book-actions { display:flex; gap:.375rem; padding:.75rem; border-top:1px solid var(--c-border); }
    .btn-lib {
        flex:1; display:flex; align-items:center; justify-content:center; gap:.3rem;
        padding:.4rem; border-radius:.375rem; font-size:.72rem; font-weight:600;
        border:none; cursor:pointer; text-decoration:none; transition:all .15s;
    }
    .btn-read   { background:var(--c-accent-dim); color:var(--c-accent); }
    .btn-read:hover { background:rgba(0,212,255,.15); }
    .btn-edit   { background:rgba(245,158,11,.1); color:#fbbf24; }
    .btn-edit:hover { background:rgba(245,158,11,.18); }
    .btn-del    { background:rgba(239,68,68,.1); color:#f87171; }
    .btn-del:hover { background:rgba(239,68,68,.18); }
    .stat-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:.75rem; margin-bottom:1.5rem; }
    .stat-mini {
        background:var(--c-surface); border:1px solid var(--c-border);
        border-radius:.625rem; padding:1rem; text-align:center;
    }
    .stat-mini-val { font-size:1.5rem; font-weight:700; color:var(--c-accent); font-variant-numeric:tabular-nums; }
    .stat-mini-lbl { font-size:.68rem; color:var(--c-muted); margin-top:.2rem; }
    .filter-bar {
        background:var(--c-surface); border:1px solid var(--c-border);
        border-radius:.625rem; padding:.875rem 1rem;
        display:flex; gap:.75rem; flex-wrap:wrap; align-items:center;
        margin-bottom:1.25rem;
    }
    .f-input {
        background:var(--c-surface-2); border:1px solid var(--c-border);
        border-radius:.375rem; padding:.4rem .75rem; font-size:.8rem;
        color:var(--c-text); transition:all .2s; outline:none;
    }
    .f-input:focus { border-color:var(--c-accent); }
    .f-input option { background:var(--c-surface-2); }
    .section-title { font-size:.9rem; font-weight:600; color:var(--c-text); margin-bottom:1rem; display:flex; align-items:center; gap:.5rem; }
    .empty-lib { text-align:center; padding:4rem 1rem; color:var(--c-muted); }
    .empty-lib-icon { font-size:3rem; margin-bottom:.75rem; opacity:.4; }

    /* Modal */
    .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:900; align-items:center; justify-content:center; padding:1rem; }
    .modal-bg.open { display:flex; }
    .modal { background:var(--c-surface); border:1px solid var(--c-border); border-radius:1rem; padding:1.75rem; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 24px 60px rgba(0,0,0,.6); }
    .modal-title { font-size:1rem; font-weight:700; color:var(--c-text); margin-bottom:1.25rem; }
    .modal-close { position:absolute; top:1rem; right:1rem; background:rgba(255,255,255,.06); border:1px solid var(--c-border); color:var(--c-muted); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1rem; transition:all .15s; }
    .modal-close:hover { background:rgba(239,68,68,.15); color:#f87171; }
    .f-label { font-size:.7rem; font-weight:600; color:var(--c-muted); text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem; }
    .f-field { background:var(--c-surface-2); border:1px solid var(--c-border); border-radius:.5rem; padding:.575rem .875rem; font-size:.875rem; color:var(--c-text); width:100%; outline:none; transition:all .2s; font-family:inherit; }
    .f-field:focus { border-color:var(--c-accent); box-shadow:0 0 0 3px rgba(0,212,255,.08); }
    .f-field::placeholder { color:var(--c-muted); }
    textarea.f-field { resize:vertical; min-height:80px; }
    .btn-submit { background:var(--c-accent); color:var(--c-bg); font-weight:700; border:none; padding:.6rem 1.25rem; border-radius:.5rem; cursor:pointer; font-size:.8rem; transition:all .2s; }
    .btn-submit:hover { opacity:.88; }
    .upload-zone { border:2px dashed var(--c-border); border-radius:.625rem; padding:1.5rem; text-align:center; cursor:pointer; transition:all .2s; }
    .upload-zone:hover { border-color:var(--c-accent); background:var(--c-accent-dim); }
    .upload-zone input[type=file] { display:none; }
    .tag-file { display:inline-block; background:rgba(0,212,255,.1); color:var(--c-accent); font-size:.72rem; padding:.2rem .6rem; border-radius:4px; margin-top:.4rem; }
</style>
@endpush

@section('content')

{{-- En-tête --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.125rem;font-weight:700;color:var(--c-text);">📚 Bibliothèque Globale</h1>
        <p style="font-size:.75rem;color:var(--c-muted);margin-top:.25rem;">
            Ressources visibles par toutes les institutions
        </p>
    </div>
    <button onclick="openModal('addModal')" class="btn-submit" style="display:flex;align-items:center;gap:.4rem;">
        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Ajouter un livre
    </button>
</div>

{{-- Flash --}}
@if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);color:#34d399;border-radius:.5rem;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.8rem;">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- Stats --}}
<div class="stat-row">
    <div class="stat-mini">
        <div class="stat-mini-val">{{ $stats['total'] }}</div>
        <div class="stat-mini-lbl">Total livres</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-val">{{ $stats['global'] }}</div>
        <div class="stat-mini-lbl">Ressources globales</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-val">{{ $stats['by_inst'] }}</div>
        <div class="stat-mini-lbl">Par institution</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-val">{{ number_format($stats['views']) }}</div>
        <div class="stat-mini-lbl">Lectures totales</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-val">{{ number_format($stats['downloads']) }}</div>
        <div class="stat-mini-lbl">Téléchargements</div>
    </div>
</div>

{{-- Filtres --}}
<form method="GET" class="filter-bar">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un titre, auteur…"
        class="f-input" style="min-width:220px;flex:1;">
    <select name="category" class="f-input">
        <option value="">Toutes catégories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
        @endforeach
    </select>
    <select name="file_type" class="f-input">
        <option value="">Tous formats</option>
        @foreach(\App\Models\LibraryBook::fileTypes() as $t)
            <option value="{{ $t }}" @selected(request('file_type') === $t)>{{ strtoupper($t) }}</option>
        @endforeach
    </select>
    <select name="uploader_role" class="f-input">
        <option value="">Tous les rôles</option>
        <option value="superadmin" @selected(request('uploader_role') === 'superadmin')>Super Admin</option>
        <option value="directeur"  @selected(request('uploader_role') === 'directeur')>Directeur</option>
        <option value="teacher"    @selected(request('uploader_role') === 'teacher')>Enseignant</option>
    </select>
    <button type="submit" class="btn-submit">Filtrer</button>
    @if(request()->hasAny(['q','category','file_type','uploader_role']))
        <a href="{{ route('superadmin.library') }}" style="color:var(--c-muted);font-size:.78rem;">✕ Réinitialiser</a>
    @endif
</form>

{{-- Grille livres --}}
@if($books->isEmpty())
    <div class="empty-lib">
        <div class="empty-lib-icon">📚</div>
        <p>Aucun livre dans la bibliothèque globale.</p>
        <p style="font-size:.75rem;margin-top:.25rem;">Commencez par en ajouter un.</p>
    </div>
@else
    <div class="lib-grid">
        @foreach($books as $book)
        <div class="book-card">
            <div class="book-cover">
                @if($book->cover_path)
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}">
                @else
                    {{ $book->file_icon }}
                @endif
                <span class="book-badge {{ $book->is_global ? 'badge-global' : 'badge-inst' }}">
                    {{ $book->is_global ? 'GLOBAL' : ($book->institution->name ?? 'Institution') }}
                </span>
                @if(!$book->is_published)
                    <span class="book-badge badge-hidden" style="top:auto;bottom:.5rem;">MASQUÉ</span>
                @endif
            </div>
            <div class="book-body">
                <div class="book-title">{{ $book->title }}</div>
                @if($book->author)
                    <div class="book-meta">✍️ {{ $book->author }}</div>
                @endif
                <div class="book-meta">
                    {{ strtoupper($book->file_type) }} · {{ $book->file_size_human }}
                    @if($book->category) · {{ $book->category }} @endif
                </div>
                <div class="book-meta" style="display:flex;gap:.75rem;margin-top:.25rem;">
                    <span>👁 {{ $book->views }}</span>
                    <span>⬇ {{ $book->downloads }}</span>
                </div>
            </div>
            <div class="book-actions">
                <a href="{{ route('library.read', $book) }}" class="btn-lib btn-read" title="Lire">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Lire
                </a>
                <button class="btn-lib btn-edit" onclick="openEditModal({{ $book->id }}, {{ $book->toJson() }})" title="Modifier">
                    ✏️
                </button>
                <form method="POST" action="{{ route('superadmin.library.toggle', $book) }}" style="flex:0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-lib" style="background:rgba(99,102,241,.1);color:#818cf8;width:32px;padding:0;" title="{{ $book->is_published ? 'Masquer' : 'Publier' }}">
                        {{ $book->is_published ? '🙈' : '👁' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('superadmin.library.destroy', $book) }}" style="flex:0;" onsubmit="return confirm('Supprimer définitivement ce livre ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-lib btn-del" style="width:32px;padding:0;" title="Supprimer">🗑</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div style="margin-top:1.25rem;">{{ $books->links() }}</div>
@endif

{{-- ══ MODAL AJOUT ══ --}}
<div class="modal-bg" id="addModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('addModal')">✕</button>
        <div class="modal-title">➕ Ajouter un livre / ressource globale</div>
        <form method="POST" action="{{ route('superadmin.library.store') }}" enctype="multipart/form-data">
            @csrf
            @include('partials.book_form')
            <div style="margin-top:1.25rem;text-align:right;">
                <button type="submit" class="btn-submit">Publier le livre</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL ÉDITION ══ --}}
<div class="modal-bg" id="editModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('editModal')">✕</button>
        <div class="modal-title">✏️ Modifier le livre</div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('partials.book_form', ['isEdit' => true])
            <div style="margin-top:1.25rem;text-align:right;">
                <button type="submit" class="btn-submit">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }

document.querySelectorAll('.modal-bg').forEach(bg => {
    bg.addEventListener('click', e => { if(e.target === bg) closeModal(bg.id); });
});

function openEditModal(id, book) {
    const form = document.getElementById('editForm');
    form.action = `/superadmin/library/${id}`;
    form.title.value   = book.title   || '';
    form.author.value  = book.author  || '';
    form.isbn.value    = book.isbn    || '';
    form.description.value = book.description || '';
    form.category.value    = book.category    || '';
    form.level.value       = book.level       || '';
    form.allow_download.checked = book.allow_download;
    form.is_published.checked   = book.is_published;
    openModal('editModal');
}
</script>
@endpush
