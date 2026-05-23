@extends('staff.master')

@section('title', 'Parents')
@section('page-title', 'Gestion des parents')
@section('page-sub', 'Suivi et affectation des tuteurs')

@push('styles')
<style>
.fg2 { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
.fg3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.875rem; }
.fg-group { display:flex; flex-direction:column; gap:.35rem; }

/* ── Parent avatar ── */
.par-av { width:38px; height:38px; border-radius:10px; background:linear-gradient(135deg,#f59e0b22,#3b82f622); border:1.5px solid var(--brd); display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-size:.75rem; font-weight:700; color:var(--night); flex-shrink:0; }

/* ── Enfants pills ── */
.enfant-pill { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .55rem; border-radius:20px; font-size:.68rem; font-weight:600; background:var(--info-l); color:#1e3a8a; margin:.1rem; }

/* ── Modal ── */
.s-modal { display:none; position:fixed; inset:0; z-index:500; background:rgba(8,12,20,.6); backdrop-filter:blur(4px); align-items:flex-start; justify-content:center; padding-top:3%; }
.s-modal.open { display:flex; }
.s-modal-box { background:var(--white); border-radius:16px; width:580px; max-width:95%; max-height:92vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:modalIn .25s cubic-bezier(.4,0,.2,1) both; }
@keyframes modalIn { from{transform:translateY(-16px);opacity:0} to{transform:none;opacity:1} }
.s-modal-hd { padding:1.25rem 1.5rem; border-bottom:1px solid var(--brd); display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; background:var(--white); z-index:1; }
.s-modal-hd h3 { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; }
.s-modal-body { padding:1.5rem; }
.s-modal-ft { padding:1rem 1.5rem; border-top:1px solid var(--brd); display:flex; gap:.75rem; justify-content:flex-end; position:sticky; bottom:0; background:var(--white); }

/* ── Act dropdown ── */
.act-wrap { position:relative; display:inline-block; }
.act-menu { display:none; position:absolute; right:0; top:calc(100% + 4px); z-index:100; background:var(--white); border:1px solid var(--brd); border-radius:10px; box-shadow:0 8px 28px rgba(0,0,0,.1); min-width:180px; overflow:hidden; }
.act-wrap:hover .act-menu, .act-wrap:focus-within .act-menu { display:block; }
.act-menu a,.act-menu button { display:flex; align-items:center; gap:.5rem; width:100%; padding:.6rem 1rem; font-size:.8rem; font-weight:500; color:#374151; border:none; background:none; cursor:pointer; font-family:inherit; text-decoration:none; transition:background .12s; }
.act-menu a:hover,.act-menu button:hover { background:var(--bg); }
.act-sep { height:1px; background:var(--brd); margin:.25rem 0; }
.act-del { color:var(--err) !important; }
.act-del:hover { background:var(--err-l) !important; }

/* ── Affectation cards ── */
.aff-card { background:var(--bg); border:1px solid var(--brd); border-radius:10px; padding:.875rem; margin-bottom:.5rem; display:flex; align-items:center; gap:.75rem; }
.aff-card:last-child { margin-bottom:0; }

/* ── Sans parent section ── */
.sanspar-list { max-height:200px; overflow-y:auto; border:1px solid var(--brd); border-radius:9px; }
.sanspar-item { padding:.5rem .875rem; border-bottom:1px solid var(--brd); font-size:.78rem; display:flex; align-items:center; justify-content:space-between; }
.sanspar-item:last-child { border-bottom:none; }
/* ═══════════════════════════════════════════════════════
   PARENTS — Responsive CSS
   Mobile-first : 320px → 480px → 768px → 1024px+
═══════════════════════════════════════════════════════ */

*, *::before, *::after { box-sizing: border-box; }

/* ── STATS GRID ── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: .75rem;
    margin-bottom: 1.5rem;
}
@media (min-width: 640px) { .stat-grid { grid-template-columns: repeat(4, 1fr); } }

/* ── TOOLBAR ── */
.par-toolbar {
    display: flex;
    flex-direction: column;
    gap: .625rem;
    margin-bottom: 1.25rem;
}
@media (min-width: 640px) {
    .par-toolbar {
        flex-direction: row;
        align-items: center;
        flex-wrap: wrap;
    }
}
.par-toolbar form {
    display: flex;
    gap: .5rem;
    flex: 1;
    min-width: 0;
    width: 100%;
}
@media (min-width: 640px) { .par-toolbar form { width: auto; min-width: 220px; } }
.par-toolbar form .inp { flex: 1; min-width: 0; width: 100%; }
.par-toolbar .btn { width: 100%; justify-content: center; }
@media (min-width: 640px) { .par-toolbar .btn { width: auto; } }

/* ── LAYOUT PRINCIPAL : table + sidebar ── */
.par-layout {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    align-items: stretch;
}
@media (min-width: 900px) {
    .par-layout {
        display: grid;
        grid-template-columns: 1fr 280px;
        align-items: start;
    }
}

/* ── SIDEBAR ── */
.par-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    /* Sur mobile, la sidebar s'affiche en 2 colonnes si assez large */
}
@media (min-width: 480px) and (max-width: 899px) {
    .par-sidebar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
}

/* ── TABLE SCROLL ── */
.par-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.s-tbl {
    width: 100%;
    border-collapse: collapse;
    min-width: 640px; /* scroll horizontal en dessous */
}

/* ── FORMULAIRES GRILLES ── */
.fg2 { display: grid; grid-template-columns: 1fr; gap: .75rem; }
.fg3 { display: grid; grid-template-columns: 1fr; gap: .75rem; }

@media (min-width: 420px) {
    .fg2 { grid-template-columns: 1fr 1fr; }
}
@media (min-width: 480px) {
    .fg3 { grid-template-columns: 1fr 1fr 1fr; }
}

/* ── MODAL ── */
.s-modal {
    display: none;
    position: fixed; inset: 0; z-index: 500;
    background: rgba(8,12,20,.6);
    backdrop-filter: blur(4px);
    align-items: flex-start;
    justify-content: center;
    padding: 1rem;
    overflow-y: auto;
}
.s-modal.open { display: flex; }

.s-modal-box {
    background: var(--white);
    border-radius: 16px;
    width: 100%;
    max-width: 580px;
    max-height: none; /* laisser le scroll de la page gérer */
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: modalIn .25s cubic-bezier(.4,0,.2,1) both;
    margin: auto 0; /* centrage vertical dans le scroll */
}
@keyframes modalIn {
    from { transform: translateY(-16px); opacity: 0; }
    to   { transform: none; opacity: 1; }
}

.s-modal-hd {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--brd);
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; background: var(--white); z-index: 1;
}
.s-modal-hd h3 { font-size: .95rem; font-weight: 700; }

.s-modal-body { padding: 1rem 1.25rem; }
@media (min-width: 480px) { .s-modal-body { padding: 1.25rem 1.5rem; } }

.s-modal-ft {
    padding: .875rem 1.25rem;
    border-top: 1px solid var(--brd);
    display: flex; flex-wrap: wrap; gap: .5rem; justify-content: flex-end;
    position: sticky; bottom: 0; background: var(--white);
}
.s-modal-ft .btn { flex: 1; min-width: 100px; justify-content: center; }
@media (min-width: 400px) { .s-modal-ft .btn { flex: none; width: auto; } }

/* ── SANS PARENT LIST ── */
.sanspar-list {
    max-height: 220px;
    overflow-y: auto;
    border: 1px solid var(--brd);
    border-radius: 9px;
}
.sanspar-item {
    padding: .5rem .875rem;
    border-bottom: 1px solid var(--brd);
    font-size: .78rem;
    display: flex; align-items: center; justify-content: space-between;
    gap: .5rem;
}
.sanspar-item:last-child { border-bottom: none; }

/* ── ENFANT PILLS ── */
.enfant-pill {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .2rem .55rem; border-radius: 20px;
    font-size: .68rem; font-weight: 600;
    background: var(--info-l); color: #1e3a8a; margin: .1rem;
    white-space: nowrap;
}

/* ── PAR AVATAR ── */
.par-av {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg,#f59e0b22,#3b82f622);
    border: 1.5px solid var(--brd);
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 700; color: var(--night);
    flex-shrink: 0;
}

/* ── ACTION DROPDOWN ── */
.act-wrap { position: relative; display: inline-block; }
.act-menu {
    display: none;
    position: absolute; right: 0; top: calc(100% + 4px);
    z-index: 100;
    background: var(--white); border: 1px solid var(--brd);
    border-radius: 10px; box-shadow: 0 8px 28px rgba(0,0,0,.1);
    min-width: 180px; overflow: hidden;
}
.act-wrap:hover .act-menu,
.act-wrap:focus-within .act-menu { display: block; }
/* Sur mobile, le menu s'ouvre à gauche si trop proche du bord droit */
@media (max-width: 480px) {
    .act-menu { right: auto; left: 0; }
}
.act-menu a,
.act-menu button {
    display: flex; align-items: center; gap: .5rem;
    width: 100%; padding: .6rem 1rem;
    font-size: .8rem; font-weight: 500; color: #374151;
    border: none; background: none; cursor: pointer;
    font-family: inherit; text-decoration: none;
    transition: background .12s;
}
.act-menu a:hover, .act-menu button:hover { background: var(--bg); }
.act-sep { height: 1px; background: var(--brd); margin: .25rem 0; }
.act-del { color: var(--err) !important; }
.act-del:hover { background: var(--err-l) !important; }

/* ── CARD ── */
.s-card-hd {
    padding: .875rem 1.125rem;
    border-bottom: 1px solid var(--brd);
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .5rem;
}
.s-card-hd h3 { font-size: .86rem; font-weight: 700; margin: 0; }

/* ── PAGINATION ── */
.par-pagination {
    padding: .875rem 1.125rem;
    border-top: 1px solid var(--brd);
    overflow-x: auto;
}

/* ── TRÈS PETITS ÉCRANS ── */
@media (max-width: 380px) {
    .stat-grid { grid-template-columns: 1fr 1fr; gap: .5rem; }
    .s-modal-box { border-radius: 12px; }
    .s-modal-hd h3 { font-size: .85rem; }
}
@media(max-width:768px) { .fg2,.fg3{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')

{{-- ══ STATS ══ --}}
<div class="stat-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-val">{{ $stats['total'] }}</div>
        <div class="stat-lbl">Total parents</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--ok)">{{ $stats['actifs'] }}</div>
        <div class="stat-lbl">Actifs</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['total_enfants'] }}</div>
        <div class="stat-lbl">Enfants liés</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--info)">{{ $stats['avec_compte'] }}</div>
        <div class="stat-lbl">Avec compte</div>
    </div>
</div>

{{-- ══ TOOLBAR ══ --}}
<div class="par-toolbar">
    <form method="GET" style="flex:1;min-width:220px;display:flex;gap:.5rem">
        <input class="inp" name="search" value="{{ request('search') }}" placeholder="Rechercher un parent…">
        <button class="btn btn-dk" type="submit">Chercher</button>
    </form>
    <button class="btn btn-ot" onclick="openAffect()">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        Affecter un enfant
    </button>
    <button class="btn btn-gold" onclick="openCreate()">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouveau parent
    </button>
</div>

{{-- ══ LAYOUT ══ --}}
<div class="par-layout">

{{-- TABLE --}}
<div class="s-card">
    <div class="s-card-hd">
        <h3>Liste des parents</h3>
        <span style="font-size:.72rem;color:var(--mist)">{{ $parents->total() }} parent(s)</span>
    </div>
    <div class="par-table-wrap">
    <table class="s-tbl">
        <thead>
            <tr>
                <th>Parent</th>
                <th>Contact</th>
                <th>Enfants liés</th>
                <th>Compte</th>
                <th>Statut</th>
                <th style="width:70px"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($parents as $p)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:.75rem">
                        <div class="par-av">{{ mb_substr($p->prenom,0,1) }}{{ mb_substr($p->nom,0,1) }}</div>
                        <div>
                            <div style="font-weight:600;font-size:.83rem">{{ $p->prenom }} {{ $p->nom }}</div>
                            <div style="font-size:.68rem;color:var(--mist);font-family:monospace">{{ $p->matricule }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:.78rem;color:var(--mist)">
                    {{ $p->telephone ?? '—' }}<br>
                    @if($p->email)<span style="font-size:.68rem">{{ $p->email }}</span>@endif
                </td>
                <td>
                    @forelse($p->apprenants->take(3) as $a)
                        <span class="enfant-pill">{{ $a->prenom }} {{ $a->nom }}</span>
                    @empty <span style="color:var(--mist);font-size:.78rem">Aucun</span>
                    @endforelse
                    @if($p->apprenants->count()>3)
                        <span class="bdg bdg-n">+{{ $p->apprenants->count()-3 }}</span>
                    @endif
                </td>
                <td>
                    @if($p->user_id)
                        <span class="bdg bdg-g">✓ Compte</span>
                    @else
                        <span class="bdg bdg-n">—</span>
                    @endif
                </td>
                <td>
                    <span class="bdg {{ $p->status?'bdg-g':'bdg-r' }}">{{ $p->status?'Actif':'Inactif' }}</span>
                </td>
                <td>
                    <div class="act-wrap" tabindex="0">
                        <button class="btn btn-ot btn-sm">•••</button>
                        <div class="act-menu">
                            <a href="{{ route('staff.parents.show',$p) }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Voir la fiche
                            </a>
                            <button onclick="openEdit({{ json_encode($p) }})">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Modifier
                            </button>
                            <button onclick="openAffectFor({{ $p->id }}, '{{ $p->prenom }} {{ $p->nom }}')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/></svg>
                                Affecter un enfant
                            </button>
                            @if($p->user_id)
                            <button onclick="openPwd({{ $p->id }}, '{{ $p->prenom }} {{ $p->nom }}')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                Réinitialiser MDP
                            </button>
                            @endif
                            <div class="act-sep"></div>
                            <form method="POST" action="{{ route('staff.parents.destroy',$p) }}">
                                @csrf @method('DELETE')
                                <button class="act-del" onclick="return confirm('Supprimer ce parent ?')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6">
                <div class="s-empty">
                    <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <h4>Aucun parent</h4>
                    <p>Commencez par en enregistrer un.</p>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="par-pagination">{{ $parents->links() }}</div>
</div>

{{-- SIDEBAR --}}
<div class="par-sidebar"> 

    {{-- Apprenants sans parent --}}
    <div class="s-card">
        <div class="s-card-hd">
            <h3>Sans tuteur</h3>
            <span class="bdg bdg-a">{{ $apprenantsSansParent->count() }}</span>
        </div>
        <div class="s-card-body" style="padding:.5rem 0">
            @forelse($apprenantsSansParent->take(8) as $a)
            <div class="sanspar-item">
                <div>
                    <div style="font-size:.78rem;font-weight:600">{{ $a->prenom }} {{ $a->nom }}</div>
                    <div style="font-size:.67rem;color:var(--mist)">{{ $a->classe?->name ?? '—' }}</div>
                </div>
                <button class="btn btn-ot btn-sm" onclick="openAffectApprenant({{ $a->id }}, '{{ $a->prenom }} {{ $a->nom }}')">
                    Lier
                </button>
            </div>
            @empty
            <div style="padding:1rem;text-align:center;font-size:.78rem;color:var(--mist)">
                ✓ Tous les apprenants ont un tuteur
            </div>
            @endforelse
        </div>
    </div>

    {{-- Affectations récentes --}}
    @if($recentAffectations->count())
    <div class="s-card">
        <div class="s-card-hd"><h3>Affectations récentes</h3></div>
        <div class="s-card-body" style="padding:.5rem 0">
            @foreach($recentAffectations->take(5) as $a)
            <div style="padding:.6rem 1.125rem;border-bottom:1px solid var(--brd)">
                <div style="font-size:.78rem;font-weight:600">{{ $a->prenom }} {{ $a->nom }}</div>
                @foreach($a->parents as $par)
                <div style="font-size:.68rem;color:var(--mist)">↳ {{ $par->prenom }} {{ $par->nom }}</div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>


{{-- ══ MODAL CRÉER PARENT ══ --}}
<div class="s-modal" id="modal-create">
    <div class="s-modal-box">
        <div class="s-modal-hd">
            <h3>Nouveau parent / tuteur</h3>
            <button class="btn btn-ot btn-sm" onclick="closeCreate()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.parents.store') }}">
            @csrf
            <div class="s-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Prénom *</label><input class="inp" name="prenom" required></div>
                    <div class="fg-group"><label class="lbl">Nom *</label><input class="inp" name="nom" required></div>
                </div>
                <div class="fg3" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Sexe</label>
                        <select class="inp" name="sexe">
                            <option value="">—</option><option value="M">M</option><option value="F">F</option>
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Téléphone</label><input class="inp" name="telephone"></div>
                    <div class="fg-group"><label class="lbl">Profession</label><input class="inp" name="profession"></div>
                </div>
                <div class="fg-group" style="margin-bottom:.875rem">
                    <label class="lbl">Adresse</label>
                    <input class="inp" name="adresse">
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Affecter à l'apprenant</label>
                        <select class="inp" name="apprenant_id">
                            <option value="">— Optionnel —</option>
                            @foreach($apprenants as $a)
                                <option value="{{ $a->id }}">{{ $a->prenom }} {{ $a->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Lien de parenté</label>
                        <select class="inp" name="lien">
                            <option value="pere">Père</option>
                            <option value="mere">Mère</option>
                            <option value="tuteur" selected>Tuteur / Tutrice</option>
                        </select>
                    </div>
                </div>
                <div style="border-top:1px solid var(--brd);padding-top:.875rem">
                    <div class="lbl" style="color:var(--mist);margin-bottom:.75rem">Compte utilisateur (optionnel)</div>
                    <div class="fg-group" style="margin-bottom:.75rem">
                        <label class="lbl">Email</label>
                        <input class="inp" type="email" name="email">
                    </div>
                    <div class="fg2">
                        <div class="fg-group"><label class="lbl">Mot de passe</label><input class="inp" type="password" name="password"></div>
                        <div class="fg-group"><label class="lbl">Confirmer</label><input class="inp" type="password" name="password_confirmation"></div>
                    </div>
                </div>
            </div>
            <div class="s-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeCreate()">Annuler</button>
                <button type="submit" class="btn btn-gold">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL EDIT ══ --}}
<div class="s-modal" id="modal-edit">
    <div class="s-modal-box">
        <div class="s-modal-hd">
            <h3>Modifier le parent</h3>
            <button class="btn btn-ot btn-sm" onclick="closeEdit()">✕</button>
        </div>
        <form method="POST" id="edit-form">
            @csrf @method('PUT')
            <div class="s-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Prénom *</label><input class="inp" name="prenom" id="e_prenom" required></div>
                    <div class="fg-group"><label class="lbl">Nom *</label><input class="inp" name="nom" id="e_nom" required></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Téléphone</label><input class="inp" name="telephone" id="e_tel"></div>
                    <div class="fg-group"><label class="lbl">Email</label><input class="inp" type="email" name="email" id="e_email"></div>
                </div>
                <div class="fg2">
                    <div class="fg-group"><label class="lbl">Profession</label><input class="inp" name="profession" id="e_prof"></div>
                    <div class="fg-group"><label class="lbl">Statut</label>
                        <select class="inp" name="status" id="e_status">
                            <option value="1">Actif</option><option value="0">Inactif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="s-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeEdit()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL AFFECTER ENFANT ══ --}}
<div class="s-modal" id="modal-affect">
    <div class="s-modal-box" style="max-width:440px">
        <div class="s-modal-hd">
            <h3 id="affect-title">Affecter un enfant</h3>
            <button class="btn btn-ot btn-sm" onclick="closeAffect()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.parents.affect') }}">
            @csrf
            <div class="s-modal-body">
                <input type="hidden" name="parent_id" id="affect_parent_id">
                <div class="fg-group" style="margin-bottom:.875rem">
                    <label class="lbl">Apprenant *</label>
                    <select class="inp" name="apprenant_id" id="affect_apprenant_id" required>
                        <option value="">Sélectionner…</option>
                        @foreach($apprenants as $a)
                            <option value="{{ $a->id }}">{{ $a->prenom }} {{ $a->nom }} ({{ $a->classe?->name ?? '—' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg-group">
                    <label class="lbl">Lien de parenté</label>
                    <select class="inp" name="lien">
                        <option value="pere">Père</option>
                        <option value="mere">Mère</option>
                        <option value="tuteur" selected>Tuteur / Tutrice</option>
                    </select>
                </div>
            </div>
            <div class="s-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeAffect()">Annuler</button>
                <button type="submit" class="btn btn-gold">Affecter</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL RESET PASSWORD ══ --}}
<div class="s-modal" id="modal-pwd" style="align-items:center;padding-top:0">
    <div class="s-modal-box" style="max-width:400px">
        <div class="s-modal-hd">
            <h3 id="pwd-title">Réinitialiser le mot de passe</h3>
            <button class="btn btn-ot btn-sm" onclick="closePwd()">✕</button>
        </div>
        <form method="POST" id="pwd-form">
            @csrf
            <div class="s-modal-body">
                <div class="fg-group" style="margin-bottom:.75rem"><label class="lbl">Nouveau mot de passe</label><input class="inp" type="password" name="password" required minlength="8"></div>
                <div class="fg-group"><label class="lbl">Confirmer</label><input class="inp" type="password" name="password_confirmation" required></div>
            </div>
            <div class="s-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closePwd()">Annuler</button>
                <button type="submit" class="btn btn-gold">Réinitialiser</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openCreate(){ document.getElementById('modal-create').classList.add('open'); document.body.style.overflow='hidden'; }
function closeCreate(){ document.getElementById('modal-create').classList.remove('open'); document.body.style.overflow=''; }

function openEdit(p) {
    document.getElementById('e_prenom').value = p.prenom??'';
    document.getElementById('e_nom').value    = p.nom??'';
    document.getElementById('e_tel').value    = p.telephone??'';
    document.getElementById('e_email').value  = p.email??'';
    document.getElementById('e_prof').value   = p.profession??'';
    document.getElementById('e_status').value = p.status??'1';
    document.getElementById('edit-form').action = `/staff/parents/${p.id}`;
    document.getElementById('modal-edit').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeEdit(){ document.getElementById('modal-edit').classList.remove('open'); document.body.style.overflow=''; }

function openAffect() {
    document.getElementById('affect_parent_id').value = '';
    document.getElementById('affect-title').textContent = 'Affecter un enfant';
    document.getElementById('modal-affect').classList.add('open');
    document.body.style.overflow='hidden';
}
function openAffectFor(parentId, parentName) {
    document.getElementById('affect_parent_id').value = parentId;
    document.getElementById('affect-title').textContent = `Affecter à : ${parentName}`;
    document.getElementById('modal-affect').classList.add('open');
    document.body.style.overflow='hidden';
}
function openAffectApprenant(appId, appName) {
    document.getElementById('affect_apprenant_id').value = appId;
    document.getElementById('affect-title').textContent = `Lier : ${appName}`;
    document.getElementById('modal-affect').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeAffect(){ document.getElementById('modal-affect').classList.remove('open'); document.body.style.overflow=''; }

function openPwd(id, name) {
    document.getElementById('pwd-title').textContent = `Réinitialiser : ${name}`;
    document.getElementById('pwd-form').action = `/staff/parents/${id}/reset-password`;
    document.getElementById('modal-pwd').classList.add('open');
    document.body.style.overflow='hidden';
}
function closePwd(){ document.getElementById('modal-pwd').classList.remove('open'); document.body.style.overflow=''; }

['modal-create','modal-edit','modal-affect','modal-pwd'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e){ if(e.target===this){this.classList.remove('open');document.body.style.overflow='';} });
});
</script>
@endpush