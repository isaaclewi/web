@extends('staff.master')

@section('title', 'Bibliothèque')
@section('page-title', 'Bibliothèque numérique')
@section('page-sub', 'Gestion des ressources pédagogiques')

@push('styles')
<style>
.fg2 { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
.fg3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.875rem; }
.fg-group { display:flex; flex-direction:column; gap:.35rem; }

/* ── Book cards grid ── */
.book-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:1rem; }
.book-card {
    background:var(--white); border:1px solid var(--brd); border-radius:14px;
    overflow:hidden; transition:box-shadow .2s, border-color .2s;
    display:flex; flex-direction:column;
}
.book-card:hover { box-shadow:0 6px 24px rgba(0,0,0,.1); border-color:var(--brd-d); }
.book-cover {
    height:140px; background:linear-gradient(135deg,var(--night-4),var(--night-2));
    display:flex; align-items:center; justify-content:center;
    position:relative; overflow:hidden;
}
.book-cover img { width:100%; height:100%; object-fit:cover; }
.book-cover-placeholder {
    font-family:'Syne',sans-serif; font-size:2.5rem; font-weight:800;
    color:rgba(255,255,255,.15); user-select:none;
}
.book-type-badge {
    position:absolute; top:.5rem; right:.5rem;
    background:rgba(0,0,0,.5); color:var(--white);
    padding:.2rem .5rem; border-radius:5px; font-size:.62rem; font-weight:700;
    letter-spacing:.08em;
}
.book-status-dot {
    position:absolute; top:.5rem; left:.5rem;
    width:8px; height:8px; border-radius:50%;
    border:2px solid rgba(255,255,255,.6);
}
.book-status-dot.pub   { background:var(--ok); }
.book-status-dot.unpub { background:var(--err); }

.book-body { padding:.875rem; flex:1; display:flex; flex-direction:column; }
.book-title { font-family:'Syne',sans-serif; font-size:.85rem; font-weight:700; color:var(--night); margin-bottom:.2rem; line-height:1.3; }
.book-author{ font-size:.73rem; color:var(--mist); margin-bottom:.625rem; }
.book-meta { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:.875rem; }
.book-category { font-size:.65rem; font-weight:600; background:var(--bg); border:1px solid var(--brd); border-radius:5px; padding:.15rem .45rem; color:#374151; }
.book-stats { display:flex; gap:.75rem; margin-top:auto; font-size:.7rem; color:var(--mist); }
.book-stat { display:flex; align-items:center; gap:.3rem; }
.book-stat svg { width:12px; height:12px; }
.book-actions { padding:.625rem .875rem; border-top:1px solid var(--brd); display:flex; gap:.375rem; }

/* ── Modal ── */
.lib-modal { display:none; position:fixed; inset:0; z-index:500; background:rgba(8,12,20,.6); backdrop-filter:blur(4px); align-items:flex-start; justify-content:center; padding-top:3%; }
.lib-modal.open { display:flex; }
.lib-modal-box { background:var(--white); border-radius:16px; width:620px; max-width:95%; max-height:92vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:modalIn .25s cubic-bezier(.4,0,.2,1) both; }
@keyframes modalIn { from{transform:translateY(-16px);opacity:0} to{transform:none;opacity:1} }
.lib-modal-hd { padding:1.25rem 1.5rem; border-bottom:1px solid var(--brd); display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; background:var(--white); z-index:1; }
.lib-modal-hd h3 { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; }
.lib-modal-body { padding:1.5rem; }
.lib-modal-ft { padding:1rem 1.5rem; border-top:1px solid var(--brd); display:flex; gap:.75rem; justify-content:flex-end; position:sticky; bottom:0; background:var(--white); }

/* ── View toggle ── */
.view-toggle { display:flex; gap:.25rem; }
.view-btn { width:32px; height:32px; border-radius:7px; border:1px solid var(--brd); background:var(--white); color:var(--mist); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .12s; }
.view-btn.on,.view-btn:hover { background:var(--night); color:var(--white); border-color:var(--night); }
.view-btn svg { width:14px; height:14px; }

/* ── Table view ── */
#view-table { display:none; }
body.table-view .book-grid { display:none; }
body.table-view #view-table { display:block; }

@media(max-width:768px) { .fg2,.fg3{grid-template-columns:1fr;} .book-grid{grid-template-columns:1fr 1fr;} }
@media(max-width:480px) { .book-grid{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')

{{-- ══ STATS ══ --}}
<div class="stat-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-val">{{ $stats['total'] }}</div>
        <div class="stat-lbl">Total livres</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--ok)">{{ $stats['published'] }}</div>
        <div class="stat-lbl">Publiés</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--mist)">{{ $stats['hidden'] }}</div>
        <div class="stat-lbl">Masqués</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['views'] }}</div>
        <div class="stat-lbl">Vues totales</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--info)">{{ $stats['downloads'] }}</div>
        <div class="stat-lbl">Téléchargements</div>
    </div>
</div>

{{-- ══ FILTRES + TOOLBAR ══ --}}
<div style="background:var(--white);border:1px solid var(--brd);border-radius:14px;padding:.875rem 1.375rem;margin-bottom:1.25rem">
    <form method="GET" style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:flex-end">
        <div style="flex:2;min-width:180px">
            <input class="inp" name="q" value="{{ request('q') }}" placeholder="Rechercher un livre…">
        </div>
        <select class="inp" name="category" style="flex:1;min-width:140px">
            <option value="">Catégorie</option>
            @foreach($categories ?? [] as $cat)
                <option value="{{ $cat }}" @selected(request('category')==$cat)>{{ $cat }}</option>
            @endforeach
        </select>
        <select class="inp" name="file_type" style="flex:1;min-width:100px">
            <option value="">Type</option>
            <option value="pdf" @selected(request('file_type')=='pdf')>PDF</option>
            <option value="docx" @selected(request('file_type')=='docx')>Word</option>
            <option value="pptx" @selected(request('file_type')=='pptx')>PowerPoint</option>
            <option value="xlsx" @selected(request('file_type')=='xlsx')>Excel</option>
            <option value="epub" @selected(request('file_type')=='epub')>Epub</option>
        </select>
        <input class="inp" name="level" value="{{ request('level') }}" placeholder="Niveau…" style="flex:1;min-width:100px">
        <button class="btn btn-dk" type="submit">Filtrer</button>
    </form>
</div>

{{-- ══ HEADER TABLE ══ --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
    <div>
        <span style="font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700">{{ $myBooks->total() }} livre(s)</span>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center">
        <div class="view-toggle">
            <button class="view-btn on" id="btn-grid" onclick="setView('grid')" title="Grille">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </button>
            <button class="view-btn" id="btn-table" onclick="setView('table')" title="Tableau">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </button>
        </div>
        <button class="btn btn-gold" onclick="openCreate()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter un livre
        </button>
    </div>
</div>

{{-- ══ GRILLE LIVRES ══ --}}
@if($myBooks->count())
<div class="book-grid">
    @foreach($myBooks as $book)
    <div class="book-card">
        <div class="book-cover">
            @if($book->cover_path)
                <img src="{{ asset('storage/'.$book->cover_path) }}" alt="{{ $book->title }}">
            @else
                <div class="book-cover-placeholder">{{ mb_substr($book->title,0,1) }}</div>
            @endif
            <span class="book-type-badge">{{ strtoupper($book->file_type) }}</span>
            <div class="book-status-dot {{ $book->is_published?'pub':'unpub' }}"></div>
        </div>
        <div class="book-body">
            <div class="book-title">{{ $book->title }}</div>
            <div class="book-author">{{ $book->author ?? 'Auteur inconnu' }}</div>
            <div class="book-meta">
                @if($book->category)<span class="book-category">{{ $book->category }}</span>@endif
                @if($book->level)<span class="book-category">{{ $book->level }}</span>@endif
            </div>
            <div class="book-stats">
                <span class="book-stat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ $book->views }}
                </span>
                <span class="book-stat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    {{ $book->downloads }}
                </span>
                @if($book->is_published)
                    <span class="book-stat" style="color:var(--ok)">✓ Publié</span>
                @else
                    <span class="book-stat" style="color:var(--err)">✗ Masqué</span>
                @endif
            </div>
        </div>
        <div class="book-actions">
            <a href="{{ route('library.read',$book) }}" class="btn btn-ot btn-sm" style="flex:1;justify-content:center">Lire</a>
            @if($book->allow_download)
            <a href="{{ route('library.download',$book) }}" class="btn btn-ok btn-sm" title="Télécharger">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
            @endif
            <button class="btn btn-ot btn-sm" onclick="openEdit({{ json_encode($book) }})" title="Modifier">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <form method="POST" action="{{ route('admin.library.destroy',$book) }}">
                @csrf @method('DELETE')
                <button class="btn btn-err btn-sm" onclick="return confirm('Supprimer ce livre ?')" title="Supprimer">✕</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{-- Table view (alt) --}}
<div id="view-table" style="display:none">
    <div class="s-card">
        <table class="s-tbl">
            <thead>
                <tr><th>Livre</th><th>Auteur</th><th>Type</th><th>Catégorie</th><th>Vues</th><th>DL</th><th>Statut</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($myBooks as $book)
                <tr>
                    <td><strong style="font-size:.83rem">{{ $book->title }}</strong></td>
                    <td style="font-size:.78rem">{{ $book->author ?? '—' }}</td>
                    <td><span class="bdg bdg-b">{{ strtoupper($book->file_type) }}</span></td>
                    <td style="font-size:.78rem">{{ $book->category ?? '—' }}</td>
                    <td>{{ $book->views }}</td>
                    <td>{{ $book->downloads }}</td>
                    <td><span class="bdg {{ $book->is_published?'bdg-g':'bdg-r' }}">{{ $book->is_published?'Publié':'Masqué' }}</span></td>
                    <td style="display:flex;gap:.35rem">
                        <a href="{{ route('library.read',$book) }}" class="btn btn-ot btn-sm">Lire</a>
                        <form method="POST" action="{{ route('admin.library.destroy',$book) }}">@csrf @method('DELETE')<button class="btn btn-err btn-sm" onclick="return confirm('Supprimer ?')">✕</button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:1rem">{{ $myBooks->links() }}</div>

@else
<div class="s-empty">
    <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
    <h4>Aucun livre</h4>
    <p>Ajoutez vos premières ressources pédagogiques.</p>
    <button class="btn btn-gold" onclick="openCreate()" style="margin-top:1rem">+ Ajouter un livre</button>
</div>
@endif


{{-- ══ MODAL AJOUTER ══ --}}
<div class="lib-modal" id="modal-create">
    <div class="lib-modal-box">
        <div class="lib-modal-hd">
            <h3>Ajouter un livre</h3>
            <button class="btn btn-ot btn-sm" onclick="closeCreate()">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.library.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="lib-modal-body">
                <div class="fg-group" style="margin-bottom:.875rem"><label class="lbl">Titre *</label><input class="inp" name="title" required></div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Auteur</label><input class="inp" name="author"></div>
                    <div class="fg-group"><label class="lbl">ISBN</label><input class="inp" name="isbn" placeholder="978…"></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Catégorie</label><input class="inp" name="category" placeholder="Mathématiques, Littérature…"></div>
                    <div class="fg-group"><label class="lbl">Niveau</label><input class="inp" name="level" placeholder="6e, Terminale, Bac…"></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Langue</label>
                        <select class="inp" name="language">
                            <option value="fr">Français</option>
                            <option value="en">Anglais</option>
                            <option value="ar">Arabe</option>
                        </select>
                    </div>
                </div>
                <div class="fg-group" style="margin-bottom:.875rem"><label class="lbl">Description</label><textarea class="inp" name="description" rows="3" placeholder="Résumé du contenu…"></textarea></div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Fichier *</label><input class="inp" type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.epub" required></div>
                    <div class="fg-group"><label class="lbl">Couverture</label><input class="inp" type="file" name="cover" accept="image/*"></div>
                </div>
                <div style="display:flex;gap:1.5rem">
                    <label style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;cursor:pointer">
                        <input type="checkbox" name="is_published" value="1" checked> Publier immédiatement
                    </label>
                    <label style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;cursor:pointer">
                        <input type="checkbox" name="allow_download" value="1" checked> Autoriser le téléchargement
                    </label>
                </div>
            </div>
            <div class="lib-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeCreate()">Annuler</button>
                <button type="submit" class="btn btn-gold">Ajouter le livre</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL MODIFIER ══ --}}
<div class="lib-modal" id="modal-edit">
    <div class="lib-modal-box">
        <div class="lib-modal-hd">
            <h3>Modifier le livre</h3>
            <button class="btn btn-ot btn-sm" onclick="closeEdit()">✕</button>
        </div>
        <form method="POST" id="edit-form" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="lib-modal-body">
                <div class="fg-group" style="margin-bottom:.875rem"><label class="lbl">Titre *</label><input class="inp" name="title" id="e_title" required></div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Auteur</label><input class="inp" name="author" id="e_author"></div>
                    <div class="fg-group"><label class="lbl">Catégorie</label><input class="inp" name="category" id="e_category"></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Niveau</label><input class="inp" name="level" id="e_level"></div>
                </div>
                <div class="fg-group" style="margin-bottom:.875rem"><label class="lbl">Description</label><textarea class="inp" name="description" id="e_desc" rows="3"></textarea></div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Remplacer le fichier</label><input class="inp" type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.epub"></div>
                    <div class="fg-group"><label class="lbl">Remplacer la couverture</label><input class="inp" type="file" name="cover" accept="image/*"></div>
                </div>
                <div style="display:flex;gap:1.5rem">
                    <label style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;cursor:pointer">
                        <input type="checkbox" name="is_published" id="e_pub" value="1"> Publié
                    </label>
                    <label style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;cursor:pointer">
                        <input type="checkbox" name="allow_download" id="e_dl" value="1"> Téléchargement autorisé
                    </label>
                </div>
            </div>
            <div class="lib-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeEdit()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// View toggle
let currentView = 'grid';
function setView(v) {
    currentView = v;
    if(v === 'table') {
        document.body.classList.add('table-view');
        document.getElementById('btn-table').classList.add('on');
        document.getElementById('btn-grid').classList.remove('on');
        document.getElementById('view-table').style.display='block';
        document.querySelector('.book-grid') && (document.querySelector('.book-grid').style.display='none');
    } else {
        document.body.classList.remove('table-view');
        document.getElementById('btn-grid').classList.add('on');
        document.getElementById('btn-table').classList.remove('on');
        document.getElementById('view-table').style.display='none';
        document.querySelector('.book-grid') && (document.querySelector('.book-grid').style.display='');
    }
}

function openCreate(){ document.getElementById('modal-create').classList.add('open'); document.body.style.overflow='hidden'; }
function closeCreate(){ document.getElementById('modal-create').classList.remove('open'); document.body.style.overflow=''; }

function openEdit(book) {
    document.getElementById('e_title').value    = book.title??'';
    document.getElementById('e_author').value   = book.author??'';
    document.getElementById('e_category').value = book.category??'';
    document.getElementById('e_level').value    = book.level??'';
    document.getElementById('e_desc').value     = book.description??'';
    document.getElementById('e_pub').checked    = !!book.is_published;
    document.getElementById('e_dl').checked     = !!book.allow_download;
    document.getElementById('edit-form').action = `/admin/library/${book.id}`;
    document.getElementById('modal-edit').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeEdit(){ document.getElementById('modal-edit').classList.remove('open'); document.body.style.overflow=''; }

['modal-create','modal-edit'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e){ if(e.target===this){this.classList.remove('open');document.body.style.overflow='';} });
});
</script>
@endpush