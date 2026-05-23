@extends('student.master')

@section('title', 'Bibliothèque')
@section('page-title', 'Bibliothèque')
@section('page-sub', 'Consultez les ressources disponibles')

@push('styles')
<style>
    .lib-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; }
    .book-card {
        background:white; border:1px solid var(--border); border-radius:var(--radius);
        overflow:hidden; transition:all .2s; display:flex; flex-direction:column; cursor:pointer;
    }
    .book-card:hover { border-color:#c7d2fe; box-shadow:0 8px 24px rgba(99,102,241,.1); transform:translateY(-3px); }
    .book-cover {
        height:150px; background:linear-gradient(135deg,var(--primary-light),#e0e7ff);
        display:flex; align-items:center; justify-content:center; font-size:3.5rem;
        position:relative; overflow:hidden;
    }
    .book-cover img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
    .book-badge {
        position:absolute; top:.5rem; right:.5rem; font-size:.6rem; font-weight:700;
        padding:.2rem .5rem; border-radius:99px;
    }
    .badge-global { background:var(--success-light); color:#065f46; }
    .badge-school { background:var(--primary-light); color:var(--primary); }
    .book-body { padding:.875rem; flex:1; display:flex; flex-direction:column; gap:.35rem; }
    .book-title { font-size:.8rem; font-weight:600; color:var(--ink); line-height:1.4;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .book-meta { font-size:.68rem; color:var(--muted); }
    .book-actions { display:flex; gap:.375rem; padding:.75rem; border-top:1px solid #f1f5f9; }
    .btn-lib {
        flex:1; display:flex; align-items:center; justify-content:center; gap:.3rem;
        padding:.45rem; border-radius:.375rem; font-size:.73rem; font-weight:600;
        border:none; cursor:pointer; text-decoration:none; transition:all .15s;
    }
    .btn-read { background:var(--primary-light); color:var(--primary); }
    .btn-read:hover { background:#e0e7ff; }
    .btn-dl   { background:var(--success-light); color:#065f46; }
    .btn-dl:hover { background:#d1fae5; }

    /* Search hero */
    .search-hero {
        background:linear-gradient(135deg,var(--primary-light) 0%,#e0e7ff 100%);
        border:1px solid #c7d2fe; border-radius:var(--radius);
        padding:1.75rem 1.5rem; margin-bottom:1.5rem; text-align:center;
    }
    .search-hero h2 { font-size:1.1rem; font-weight:700; color:var(--ink); margin-bottom:.25rem; }
    .search-hero p  { font-size:.78rem; color:var(--muted); margin-bottom:1rem; }
    .search-input-wrap { display:flex; gap:.5rem; max-width:500px; margin:0 auto; }
    .search-inp {
        flex:1; background:white; border:1px solid var(--border); border-radius:.5rem;
        padding:.575rem 1rem; font-size:.875rem; color:var(--ink); outline:none; font-family:var(--font);
        transition:border-color .2s;
    }
    .search-inp:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(99,102,241,.1); }
    .search-btn { background:var(--primary); color:white; border:none; border-radius:.5rem; padding:.575rem 1.25rem; font-weight:700; cursor:pointer; font-size:.8rem; font-family:var(--font); }

    /* Category pills */
    .cat-pills { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .cat-pill  {
        padding:.35rem .875rem; border-radius:99px; font-size:.75rem; font-weight:600;
        border:1px solid var(--border); background:white; color:var(--muted); cursor:pointer;
        text-decoration:none; transition:all .15s;
    }
    .cat-pill:hover, .cat-pill.active { background:var(--primary); color:white; border-color:var(--primary); }

    .filter-bar { background:white; border:1px solid var(--border); border-radius:.625rem;
        padding:.75rem 1rem; display:flex; gap:.625rem; flex-wrap:wrap; align-items:center; margin-bottom:1.25rem; }
    .f-inp { background:#f8fafc; border:1px solid var(--border); border-radius:.375rem;
        padding:.4rem .75rem; font-size:.8rem; color:var(--ink-mid); outline:none; font-family:var(--font); transition:border-color .2s; }
    .f-inp:focus { border-color:var(--primary); }

    .no-dl-badge { background:var(--amber-light); color:#92400e; font-size:.65rem; padding:.15rem .4rem; border-radius:4px; }
    .empty-lib { text-align:center; padding:4rem 1rem; }
    .empty-icon { font-size:3rem; opacity:.3; margin-bottom:.75rem; }
    .empty-text { font-size:.875rem; color:var(--muted); }

    @media(max-width:600px) {
        .lib-grid { grid-template-columns:repeat(auto-fill,minmax(145px,1fr)); }
        .search-input-wrap { flex-direction:column; }
    }
</style>
@endpush

@section('content')

{{-- Hero recherche --}}
<div class="search-hero">
    <h2>📚 Bibliothèque numérique</h2>
    <p>Accédez aux livres, cours et ressources mis à votre disposition</p>
    <form method="GET" action="{{ route('student.library') }}" class="search-input-wrap">
        <input type="text" name="q" value="{{ request('q') }}" class="search-inp" placeholder="Rechercher un titre, un auteur…">
        <button type="submit" class="search-btn">Rechercher</button>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-s" style="margin-bottom:1.25rem;">✓ {{ session('success') }}</div>
@endif

{{-- Catégories --}}
<div class="cat-pills">
    <a href="{{ route('student.library') }}" class="cat-pill {{ !request('category') ? 'active' : '' }}">
        Tous
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('student.library', ['category' => $cat] + request()->except('category', 'page')) }}"
           class="cat-pill {{ request('category') === $cat ? 'active' : '' }}">
            {{ $cat }}
        </a>
    @endforeach
</div>

{{-- Filtres --}}
<form method="GET" class="filter-bar">
    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
    <select name="file_type" class="f-inp">
        <option value="">Tous formats</option>
        @foreach(\App\Models\LibraryBook::fileTypes() as $t)
            <option value="{{ $t }}" @selected(request('file_type') === $t)>{{ strtoupper($t) }}</option>
        @endforeach
    </select>
    <select name="level" class="f-inp">
        <option value="">Tous niveaux</option>
        <option @selected(request('level') === 'Terminale')>Terminale</option>
        <option @selected(request('level') === 'Première')>Première</option>
        <option @selected(request('level') === 'Seconde')>Seconde</option>
        <option @selected(request('level') === 'Licence 1')>Licence 1</option>
        <option @selected(request('level') === 'Licence 2')>Licence 2</option>
        <option @selected(request('level') === 'Licence 3')>Licence 3</option>
        <option @selected(request('level') === 'Master 1')>Master 1</option>
        <option @selected(request('level') === 'Master 2')>Master 2</option>
    </select>
    <button type="submit" class="search-btn" style="padding:.4rem .875rem;">Filtrer</button>
    @if(request()->hasAny(['file_type','level']))
        <a href="{{ route('student.library', request()->only(['q','category'])) }}" style="color:var(--muted);font-size:.78rem;">✕ Réinitialiser</a>
    @endif
    <span style="margin-left:auto;font-size:.75rem;color:var(--muted);">{{ $books->total() }} ressource(s)</span>
</form>

{{-- Grille --}}
@if($books->isEmpty())
    <div class="empty-lib">
        <div class="empty-icon">📚</div>
        <p class="empty-text">Aucune ressource ne correspond à votre recherche.</p>
        <a href="{{ route('student.library') }}" style="font-size:.78rem;color:var(--primary);margin-top:.5rem;display:inline-block;">
            Voir toutes les ressources
        </a>
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
                <span class="book-badge {{ $book->is_global ? 'badge-global' : 'badge-school' }}">
                    {{ $book->is_global ? '🌐 Global' : '🏫 École' }}
                </span>
            </div>
            <div class="book-body">
                <div class="book-title">{{ $book->title }}</div>
                @if($book->author)<div class="book-meta">✍️ {{ $book->author }}</div>@endif
                @if($book->category)<div class="book-meta">🏷 {{ $book->category }}</div>@endif
                @if($book->level)<div class="book-meta">📐 {{ $book->level }}</div>@endif
                <div class="book-meta" style="display:flex;gap:.5rem;margin-top:.2rem;">
                    <span>{{ strtoupper($book->file_type) }}</span>
                    <span>·</span>
                    <span>{{ $book->file_size_human }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.25rem;">
                    <span class="book-meta">👁 {{ $book->views }}</span>
                    @if(!$book->allow_download)
                        <span class="no-dl-badge">Lecture seule</span>
                    @endif
                </div>
            </div>
            <div class="book-actions">
                <a href="{{ route('library.read', $book) }}" class="btn-lib btn-read">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Lire
                </a>
                @if($book->allow_download)
                    <a href="{{ route('library.download', $book) }}" class="btn-lib btn-dl" style="flex:0;padding:.45rem .75rem;" title="Télécharger">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:1.5rem;">{{ $books->links() }}</div>
@endif

@endsection
