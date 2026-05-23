@extends('admin.master')

@section('title', 'Bibliothèque — ' . ($institution->name ?? 'Mon Établissement'))

@push('styles')
<style>
    .lib-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem; }
    .book-card {
        background:white; border:1px solid #e5e7eb; border-radius:.75rem;
        overflow:hidden; transition:all .2s; display:flex; flex-direction:column;
    }
    .book-card:hover { border-color:#d1d5db; box-shadow:0 6px 20px rgba(0,0,0,.07); transform:translateY(-2px); }
    .book-cover {
        height:155px; background:#f3f4f6; display:flex; align-items:center;
        justify-content:center; font-size:3rem; position:relative; overflow:hidden;
    }
    .book-cover img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
    .book-badge {
        position:absolute; top:.5rem; right:.5rem; font-size:.6rem; font-weight:700;
        padding:.2rem .5rem; border-radius:99px; letter-spacing:.05em;
    }
    .badge-my  { background:#dbeafe; color:#1e40af; }
    .badge-global { background:#d1fae5; color:#065f46; }
    .badge-hidden { background:#fee2e2; color:#991b1b; }
    .book-body { padding:.875rem; flex:1; display:flex; flex-direction:column; gap:.35rem; }
    .book-title { font-size:.8rem; font-weight:600; color:#1f2937; line-height:1.4;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .book-meta { font-size:.68rem; color:#6b7280; }
    .book-actions { display:flex; gap:.375rem; padding:.75rem; border-top:1px solid #f3f4f6; }
    .btn-lib {
        flex:1; display:flex; align-items:center; justify-content:center; gap:.3rem;
        padding:.4rem; border-radius:.375rem; font-size:.72rem; font-weight:600;
        border:none; cursor:pointer; text-decoration:none; transition:all .15s;
    }
    .btn-read { background:#eff6ff; color:#1d4ed8; }
    .btn-read:hover { background:#dbeafe; }
    .btn-edit { background:#fffbeb; color:#92400e; }
    .btn-edit:hover { background:#fef3c7; }
    .btn-del  { background:#fef2f2; color:#991b1b; }
    .btn-dl   { background:#f0fdf4; color:#14532d; }

    .stat-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:.75rem; margin-bottom:1.5rem; }
    .stat-mini { background:white; border:1px solid #e5e7eb; border-radius:.625rem; padding:1rem; text-align:center; }
    .stat-mini-val { font-size:1.4rem; font-weight:700; color:#1f2937; }
    .stat-mini-lbl { font-size:.68rem; color:#6b7280; margin-top:.2rem; }

    .filter-bar { background:white; border:1px solid #e5e7eb; border-radius:.625rem;
        padding:.875rem 1rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; margin-bottom:1.25rem; }
    .f-input { background:#f9fafb; border:1px solid #e5e7eb; border-radius:.375rem;
        padding:.4rem .75rem; font-size:.8rem; color:#374151; outline:none; transition:border-color .2s; }
    .f-input:focus { border-color:#1f2937; }

    /* Tabs */
    .lib-tabs { display:flex; gap:.375rem; margin-bottom:1.25rem; }
    .lib-tab  { padding:.5rem 1rem; border-radius:.5rem; font-size:.8rem; font-weight:600;
        border:1px solid #e5e7eb; background:white; color:#6b7280; cursor:pointer; transition:all .15s; }
    .lib-tab.active { background:#1f2937; color:white; border-color:#1f2937; }

    /* Modal */
    .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:900; align-items:center; justify-content:center; padding:1rem; }
    .modal-bg.open { display:flex; }
    .modal { background:white; border-radius:1rem; padding:1.75rem; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 20px 60px rgba(0,0,0,.15); }
    .modal-title { font-size:1rem; font-weight:700; color:#111827; margin-bottom:1.25rem; }
    .modal-close { position:absolute; top:1rem; right:1rem; background:#f1f5f9; border:none; cursor:pointer; color:#6b7280; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.9rem; transition:all .15s; }
    .modal-close:hover { background:#fee2e2; color:#dc2626; }
    .f-label { font-size:.7rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem; }
    .f-field { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.5rem; padding:.575rem .875rem; font-size:.875rem; color:#374151; width:100%; outline:none; transition:all .2s; font-family:inherit; }
    .f-field:focus { border-color:#1f2937; box-shadow:0 0 0 3px rgba(31,41,55,.06); }
    textarea.f-field { resize:vertical; min-height:80px; }
    .btn-submit { background:#1f2937; color:white; font-weight:700; border:none; padding:.6rem 1.25rem; border-radius:.5rem; cursor:pointer; font-size:.8rem; transition:all .2s; }
    .btn-submit:hover { background:#111827; }
    .upload-zone { border:2px dashed #e5e7eb; border-radius:.625rem; padding:1.5rem; text-align:center; cursor:pointer; transition:all .2s; }
    .upload-zone:hover { border-color:#1f2937; background:#f9fafb; }
    .upload-zone input[type=file] { display:none; }
    .empty-lib { text-align:center; padding:4rem 1rem; color:#6b7280; }
    .empty-lib-icon { font-size:3rem; margin-bottom:.75rem; opacity:.4; }
    /* Styles pour l'onglet "global" */
    #globalBooks { display:none; }
    #globalBooks.active { display:block; }
    #myBooks.active { display:block; }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.125rem;font-weight:700;color:#111827;">📚 Bibliothèque</h1>
        <p style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">{{ $institution->name ?? 'Mon Établissement' }}</p>
    </div>
    <button onclick="openModal('addModal')" class="btn-submit" style="display:flex;align-items:center;gap:.4rem;">
        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Ajouter un livre
    </button>
</div>

@if(session('success'))
    <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:.5rem;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.8rem;">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- Stats --}}
<div class="stat-row">
    <div class="stat-mini">
        <div class="stat-mini-val">{{ $stats['total'] }}</div>
        <div class="stat-mini-lbl">Mes livres</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-val">{{ $stats['published'] }}</div>
        <div class="stat-mini-lbl">Publiés</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-val">{{ $stats['hidden'] }}</div>
        <div class="stat-mini-lbl">Masqués</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-val">{{ number_format($stats['views']) }}</div>
        <div class="stat-mini-lbl">Lectures</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-val">{{ number_format($stats['downloads']) }}</div>
        <div class="stat-mini-lbl">Téléchargements</div>
    </div>
</div>

{{-- Filtres --}}
<form method="GET" class="filter-bar">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Titre, auteur…" class="f-input" style="flex:1;min-width:180px;">
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
    <button type="submit" class="btn-submit">Filtrer</button>
    @if(request()->hasAny(['q','category','file_type']))
        <a href="{{ route('admin.library') }}" style="color:#6b7280;font-size:.78rem;">✕</a>
    @endif
</form>

{{-- Grille --}}
@if($myBooks->isEmpty())
    <div class="empty-lib">
        <div class="empty-lib-icon">📚</div>
        <p>Aucun livre dans votre bibliothèque.</p>
        <p style="font-size:.75rem;margin-top:.25rem;">Commencez par ajouter des ressources pour vos élèves.</p>
    </div>
@else
    <div class="lib-grid">
        @foreach($myBooks as $book)
        <div class="book-card">
            <div class="book-cover">
                @if($book->cover_path)
                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}">
                @else
                    {{ $book->file_icon }}
                @endif
                @if(!$book->is_published)
                    <span class="book-badge badge-hidden">MASQUÉ</span>
                @else
                    <span class="book-badge badge-my">{{ strtoupper($book->file_type) }}</span>
                @endif
            </div>
            <div class="book-body">
                <div class="book-title">{{ $book->title }}</div>
                @if($book->author)
                    <div class="book-meta">✍️ {{ $book->author }}</div>
                @endif
                <div class="book-meta">{{ $book->file_size_human }} @if($book->category) · {{ $book->category }} @endif</div>
                <div class="book-meta">👁 {{ $book->views }} · ⬇ {{ $book->downloads }}</div>
            </div>
            <div class="book-actions">
                <a href="{{ route('library.read', $book) }}" class="btn-lib btn-read">👁 Lire</a>
                <button class="btn-lib btn-edit" onclick="openEditModal({{ $book->id }}, {{ $book->toJson() }})">✏️</button>
                <form method="POST" action="{{ route('admin.library.destroy', $book) }}" onsubmit="return confirm('Supprimer ce livre ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-lib btn-del" style="width:32px;padding:0;">🗑</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:1.25rem;">{{ $myBooks->links() }}</div>
@endif

{{-- Modal ajout --}}
<div class="modal-bg" id="addModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('addModal')">✕</button>
        <div class="modal-title">➕ Ajouter un livre</div>
        <form method="POST" action="{{ route('admin.library.store') }}" enctype="multipart/form-data">
            @csrf
            @include('partials.book_form')
            <div style="margin-top:1.25rem;text-align:right;">
                <button type="submit" class="btn-submit">Publier</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal édition --}}
<div class="modal-bg" id="editModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('editModal')">✕</button>
        <div class="modal-title">✏️ Modifier le livre</div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('partials.book_form', ['isEdit' => true])
            <div style="margin-top:1.25rem;text-align:right;">
                <button type="submit" class="btn-submit">Enregistrer</button>
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
    form.action = `/admin/library/${id}`;
    form.title.value       = book.title       || '';
    form.author.value      = book.author      || '';
    form.isbn.value        = book.isbn        || '';
    form.description.value = book.description || '';
    form.category.value    = book.category    || '';
    form.level.value       = book.level       || '';
    form.allow_download.checked = book.allow_download;
    form.is_published.checked   = book.is_published;
    openModal('editModal');
}
function showFileName(input, labelId) {
    const l = document.getElementById(labelId);
    if (input.files && input.files[0]) l.textContent = '✅ ' + input.files[0].name;
}
</script>
@endpush
