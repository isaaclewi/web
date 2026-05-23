@extends('teacher.master')

@section('title', 'Bibliothèque')
@section('page-title', 'Bibliothèque')

@push('styles')
<style>
    .lib-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr)); gap:.875rem; }
    .book-card {
        background:white; border:1px solid #e5e7eb; border-radius:.75rem;
        overflow:hidden; transition:all .2s; display:flex; flex-direction:column;
    }
    .book-card:hover { border-color:#c7d2fe; box-shadow:0 6px 20px rgba(99,102,241,.08); transform:translateY(-2px); }
    .book-cover {
        height:145px; background:linear-gradient(135deg,#f0f9ff,#e0e7ff);
        display:flex; align-items:center; justify-content:center; font-size:3rem;
        position:relative; overflow:hidden;
    }
    .book-cover img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
    .book-badge {
        position:absolute; top:.5rem; right:.5rem; font-size:.6rem; font-weight:700;
        padding:.2rem .5rem; border-radius:99px;
    }
    .badge-global { background:#d1fae5; color:#065f46; }
    .badge-mine   { background:#ede9fe; color:#5b21b6; }
    .badge-inst   { background:#dbeafe; color:#1e40af; }
    .book-body { padding:.875rem; flex:1; display:flex; flex-direction:column; gap:.3rem; }
    .book-title { font-size:.8rem; font-weight:600; color:#1f2937; line-height:1.4;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .book-meta { font-size:.68rem; color:#6b7280; }
    .book-actions { display:flex; gap:.375rem; padding:.75rem; border-top:1px solid #f3f4f6; }
    .btn-lib {
        flex:1; display:flex; align-items:center; justify-content:center; gap:.3rem;
        padding:.4rem; border-radius:.375rem; font-size:.72rem; font-weight:600;
        border:none; cursor:pointer; text-decoration:none; transition:all .15s;
    }
    .btn-read { background:#eef2ff; color:#4338ca; }
    .btn-read:hover { background:#e0e7ff; }
    .btn-dl   { background:#f0fdf4; color:#14532d; }
    .btn-dl:hover { background:#dcfce7; }
    .btn-del  { background:#fef2f2; color:#991b1b; }
    .btn-del:hover { background:#fee2e2; }

    .section-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1rem; }
    .section-title  { font-size:.9rem; font-weight:700; color:#1f2937; }
    .filter-bar { background:white; border:1px solid #e5e7eb; border-radius:.625rem;
        padding:.875rem 1rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; margin-bottom:1.25rem; }
    .f-input { background:#f9fafb; border:1px solid #e5e7eb; border-radius:.375rem;
        padding:.4rem .75rem; font-size:.8rem; color:#374151; outline:none; transition:border-color .2s; }
    .f-input:focus { border-color:#6366f1; }

    /* Mes uploads sidebar */
    .my-uploads-list { background:white; border:1px solid #e5e7eb; border-radius:.75rem; overflow:hidden; }
    .my-upload-item { display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; border-bottom:1px solid #f3f4f6; }
    .my-upload-item:last-child { border-bottom:none; }
    .my-upload-icon { width:36px; height:36px; border-radius:.5rem; background:#eef2ff; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
    .my-upload-info { flex:1; min-width:0; }
    .my-upload-title { font-size:.78rem; font-weight:600; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .my-upload-meta  { font-size:.68rem; color:#9ca3af; }

    /* Modal */
    .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:900; align-items:center; justify-content:center; padding:1rem; }
    .modal-bg.open { display:flex; }
    .modal { background:white; border-radius:1rem; padding:1.75rem; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 20px 60px rgba(0,0,0,.15); }
    .modal-title { font-size:1rem; font-weight:700; color:#111827; margin-bottom:1.25rem; }
    .modal-close { position:absolute; top:1rem; right:1rem; background:#f1f5f9; border:none; cursor:pointer; color:#6b7280; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
    .modal-close:hover { background:#fee2e2; color:#dc2626; }
    .f-label { font-size:.7rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem; }
    .f-field { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.5rem; padding:.575rem .875rem; font-size:.875rem; color:#374151; width:100%; outline:none; transition:all .2s; font-family:inherit; }
    .f-field:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.08); }
    textarea.f-field { resize:vertical; min-height:80px; }
    .btn-submit { background:#1f2937; color:white; font-weight:700; border:none; padding:.6rem 1.25rem; border-radius:.5rem; cursor:pointer; font-size:.8rem; }
    .upload-zone { border:2px dashed #e5e7eb; border-radius:.625rem; padding:1.5rem; text-align:center; cursor:pointer; transition:all .2s; }
    .upload-zone:hover { border-color:#6366f1; background:#fafafe; }
    .upload-zone input[type=file] { display:none; }
    .empty-lib { text-align:center; padding:3rem 1rem; color:#6b7280; }
    .empty-lib-icon { font-size:2.5rem; opacity:.4; margin-bottom:.5rem; }

    @media(max-width:960px) {
        .lib-layout { flex-direction:column; }
        .sidebar-col { width:100%; }
    }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.5rem;">
    <div>
        <h2 style="font-size:1rem;font-weight:700;color:#111827;">📚 Bibliothèque</h2>
        <p style="font-size:.72rem;color:#6b7280;margin-top:.15rem;">Consultez et partagez des ressources pédagogiques</p>
    </div>
    <button onclick="openModal('addModal')" class="btn-submit" style="display:flex;align-items:center;gap:.4rem;">
        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Partager un cours
    </button>
</div>

@if(session('success'))
    <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;border-radius:.5rem;padding:.75rem 1rem;margin-bottom:1.25rem;font-size:.8rem;">
        ✓ {{ session('success') }}
    </div>
@endif

<div class="lib-layout" style="display:flex;gap:1.25rem;">

    {{-- Colonne principale --}}
    <div style="flex:1;min-width:0;">

        {{-- Filtres --}}
        <form method="GET" class="filter-bar">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher…" class="f-input" style="flex:1;min-width:160px;">
            <select name="category" class="f-input">
                <option value="">Catégorie</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
            <select name="file_type" class="f-input">
                <option value="">Format</option>
                @foreach(\App\Models\LibraryBook::fileTypes() as $t)
                    <option value="{{ $t }}" @selected(request('file_type') === $t)>{{ strtoupper($t) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-submit" style="padding:.4rem .875rem;">Filtrer</button>
            @if(request()->hasAny(['q','category','file_type']))
                <a href="{{ route('teacher.library') }}" style="color:#6b7280;font-size:.78rem;">✕</a>
            @endif
        </form>

        {{-- Grille --}}
        @if($books->isEmpty())
            <div class="empty-lib">
                <div class="empty-lib-icon">📚</div>
                <p>Aucune ressource disponible pour le moment.</p>
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
                        <span class="book-badge {{ $book->is_global ? 'badge-global' : ($book->uploaded_by === auth()->id() ? 'badge-mine' : 'badge-inst') }}">
                            {{ $book->is_global ? 'Global' : ($book->uploaded_by === auth()->id() ? 'Mon cours' : 'École') }}
                        </span>
                    </div>
                    <div class="book-body">
                        <div class="book-title">{{ $book->title }}</div>
                        @if($book->author)<div class="book-meta">✍️ {{ $book->author }}</div>@endif
                        <div class="book-meta">{{ strtoupper($book->file_type) }} · {{ $book->file_size_human }}</div>
                        @if($book->category)<div class="book-meta">🏷 {{ $book->category }}</div>@endif
                        <div class="book-meta">👁 {{ $book->views }} · ⬇ {{ $book->downloads }}</div>
                    </div>
                    <div class="book-actions">
                        <a href="{{ route('library.read', $book) }}" class="btn-lib btn-read">👁 Lire</a>
                        @if($book->allow_download)
                            <a href="{{ route('library.download', $book) }}" class="btn-lib btn-dl">⬇</a>
                        @endif
                        @if($book->uploaded_by === auth()->id())
                            <form method="POST" action="{{ route('teacher.library.destroy', $book) }}" onsubmit="return confirm('Supprimer ce cours ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-lib btn-del" style="width:30px;padding:0;">🗑</button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div style="margin-top:1.25rem;">{{ $books->links() }}</div>
        @endif
    </div>

    {{-- Sidebar : mes uploads --}}
    <div class="sidebar-col" style="width:280px;flex-shrink:0;">
        <div style="font-size:.78rem;font-weight:700;color:#374151;margin-bottom:.625rem;">
            📂 Mes ressources partagées ({{ $myUploads->count() }})
        </div>
        @if($myUploads->isEmpty())
            <div style="background:white;border:1px solid #e5e7eb;border-radius:.625rem;padding:1.25rem;text-align:center;color:#6b7280;font-size:.78rem;">
                Vous n'avez pas encore partagé de ressource.
            </div>
        @else
            <div class="my-uploads-list">
                @foreach($myUploads as $up)
                <div class="my-upload-item">
                    <div class="my-upload-icon">{{ $up->file_icon }}</div>
                    <div class="my-upload-info">
                        <div class="my-upload-title">{{ $up->title }}</div>
                        <div class="my-upload-meta">{{ strtoupper($up->file_type) }} · 👁 {{ $up->views }} · ⬇ {{ $up->downloads }}</div>
                    </div>
                    <form method="POST" action="{{ route('teacher.library.destroy', $up) }}" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;color:#d1d5db;cursor:pointer;font-size:.9rem;">🗑</button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Modal --}}
<div class="modal-bg" id="addModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('addModal')">✕</button>
        <div class="modal-title">📤 Partager un cours ou une ressource</div>
        <form method="POST" action="{{ route('teacher.library.store') }}" enctype="multipart/form-data">
            @csrf
            @include('partials.book_form')
            <div style="margin-top:1.25rem;text-align:right;">
                <button type="submit" class="btn-submit">Partager</button>
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
function showFileName(input, labelId) {
    const l = document.getElementById(labelId);
    if (input.files && input.files[0]) l.textContent = '✅ ' + input.files[0].name;
}
</script>
@endpush
