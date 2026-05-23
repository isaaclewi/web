@extends('admin.master')

@section('content')

{{-- ═══════════════════════════════ FLASH MESSAGES ═══════════════════════════════ --}}
@if(session('success'))
    <div id="flash-success" class="flash-toast flash-success">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div id="flash-error" class="flash-toast flash-error">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

<style>
    :root {
        --sage:   #4a7c59;
        --sage-l: #e8f0eb;
        --amber:  #c97d2e;
        --amber-l:#fdf3e7;
        --slate:  #1e293b;
        --muted:  #64748b;
        --border: #e2e8f0;
        --surface:#f8fafc;
        --white:  #ffffff;
        --radius: 12px;
        --shadow: 0 2px 12px rgba(0,0,0,.07);
    }

    /* ── PAGE HEADER ── */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 2rem;
        gap: 1rem;
    }
    .page-title { font-size: 1.5rem; font-weight: 700; color: var(--slate); margin: 0 0 .25rem; }
    .page-sub   { font-size: .875rem; color: var(--muted); margin: 0; }

    /* ── STAT STRIP ── */
    .stat-strip { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 2rem; }
    .stat-box {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem 1.5rem;
        display: flex; align-items: center; gap: 1rem;
    }
    .stat-box .icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .stat-box .icon svg { width: 22px; height: 22px; }
    .stat-box .val { font-size: 1.5rem; font-weight: 700; color: var(--slate); line-height: 1; }
    .stat-box .lbl { font-size: .75rem; color: var(--muted); margin-top: .2rem; }
    .icon-sage   { background: var(--sage-l); color: var(--sage); }
    .icon-amber  { background: var(--amber-l); color: var(--amber); }
    .icon-blue   { background: #dbeafe; color: #1d4ed8; }
    .icon-purple { background: #ede9fe; color: #7c3aed; }

    /* ── TWO-COLUMN LAYOUT ── */
    .two-col { display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; align-items: start; }

    /* ── CARD ── */
    .card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .card-title { font-size: 1rem; font-weight: 600; color: var(--slate); margin: 0; }
    .card-body  { padding: 1.5rem; }

    /* ── SEARCH BAR ── */
    .search-bar {
        display: flex; gap: .75rem; align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .search-bar input {
        flex: 1; padding: .5rem .875rem;
        border: 1px solid var(--border); border-radius: 8px;
        font-size: .875rem; background: var(--white); outline: none;
        transition: border-color .2s;
    }
    .search-bar input:focus { border-color: var(--sage); }

    /* ── TABLE ── */
    .parents-table { width: 100%; border-collapse: collapse; }
    .parents-table th {
        background: var(--surface); padding: .75rem 1rem;
        text-align: left; font-size: .7rem; font-weight: 600;
        text-transform: uppercase; color: var(--muted);
        border-bottom: 1px solid var(--border);
    }
    .parents-table td {
        padding: .9rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: .875rem; color: #374151;
        vertical-align: middle;
    }
    .parents-table tr:last-child td { border-bottom: none; }
    .parents-table tr:hover td { background: var(--surface); }

    .avatar {
        width: 36px; height: 36px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 700;
        background: var(--sage-l); color: var(--sage); flex-shrink: 0;
    }
    .avatar.f { background: #fce7f3; color: #be185d; }

    .parent-name { font-weight: 600; color: var(--slate); }
    .parent-meta { font-size: .75rem; color: var(--muted); }

    .badge-link {
        display: inline-flex; align-items: center; gap: .3rem;
        background: var(--sage-l); color: var(--sage);
        padding: .2rem .6rem; border-radius: 20px;
        font-size: .72rem; font-weight: 600; cursor: pointer;
        border: none; transition: background .2s;
    }
    .badge-link:hover { background: #d1e7d8; }

    .status-dot {
        width: 8px; height: 8px; border-radius: 50%; display: inline-block;
    }
    .dot-active   { background: #22c55e; }
    .dot-inactive { background: #94a3b8; }

    /* ── ACTIONS ── */
    .action-btn {
        width: 30px; height: 30px; border-radius: 7px;
        border: 1px solid var(--border); background: var(--white);
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .2s; color: var(--muted);
    }
    .action-btn:hover { border-color: var(--sage); color: var(--sage); background: var(--sage-l); }
    .action-btn.danger:hover { border-color: #ef4444; color: #ef4444; background: #fee2e2; }
    .action-btn svg { width: 14px; height: 14px; }

    /* ── FORM ── */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-grid.full { grid-template-columns: 1fr; }
    .field { display: flex; flex-direction: column; gap: .35rem; }
    .field label { font-size: .78rem; font-weight: 600; color: var(--slate); }
    .field input, .field select, .field textarea {
        padding: .55rem .875rem;
        border: 1px solid var(--border); border-radius: 8px;
        font-size: .875rem; outline: none; transition: border-color .2s;
        background: var(--white);
    }
    .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--sage); }
    .field .hint { font-size: .72rem; color: var(--muted); }
    @error('*') .field input, .field select { border-color: #ef4444; } @enderror

    /* ── SECTION TITLE ── */
    .section-title {
        font-size: .7rem; font-weight: 700;
        text-transform: uppercase; color: var(--muted);
        letter-spacing: .08em; margin: 1.25rem 0 .75rem;
        padding-bottom: .5rem;
        border-bottom: 1px solid var(--border);
    }

    /* ── BUTTONS ── */
    .btn-primary {
        background: var(--sage); color: #fff;
        border: none; border-radius: 8px;
        padding: .6rem 1.25rem; font-size: .875rem; font-weight: 600;
        cursor: pointer; transition: background .2s;
        display: inline-flex; align-items: center; gap: .5rem;
    }
    .btn-primary:hover { background: #3d6b4b; }
    .btn-secondary {
        background: var(--white); color: var(--slate);
        border: 1px solid var(--border); border-radius: 8px;
        padding: .6rem 1.25rem; font-size: .875rem; font-weight: 600;
        cursor: pointer; transition: all .2s;
        display: inline-flex; align-items: center; gap: .5rem;
    }
    .btn-secondary:hover { border-color: var(--sage); color: var(--sage); }
    .btn-danger {
        background: #ef4444; color: #fff;
        border: none; border-radius: 8px;
        padding: .6rem 1.25rem; font-size: .875rem; font-weight: 600;
        cursor: pointer; transition: background .2s;
    }
    .btn-danger:hover { background: #dc2626; }

    /* ── AFFECTATION PANEL ── */
    .affect-card { position: sticky; top: 90px; }
    .affectation-list { display: flex; flex-direction: column; gap: .75rem; max-height: 360px; overflow-y: auto; }
    .affect-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: .75rem; border: 1px solid var(--border); border-radius: 10px;
        background: var(--surface);
    }
    .affect-row .left { display: flex; align-items: center; gap: .75rem; }
    .affect-row .name { font-weight: 600; font-size: .875rem; color: var(--slate); }
    .affect-row .meta { font-size: .72rem; color: var(--muted); }
    .lien-badge {
        font-size: .68rem; font-weight: 700;
        padding: .15rem .5rem; border-radius: 20px;
        background: var(--amber-l); color: var(--amber);
        text-transform: uppercase;
    }

    /* ── MODAL ── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        display: none; align-items: center; justify-content: center; z-index: 200;
        backdrop-filter: blur(2px);
    }
    .modal-overlay.open { display: flex; }
    .modal {
        background: var(--white); border-radius: 16px;
        width: 560px; max-width: 95vw; max-height: 90vh;
        overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: slideUp .25s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .modal-header {
        padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        position: sticky; top: 0; background: var(--white); z-index: 10;
    }
    .modal-title { font-size: 1.05rem; font-weight: 700; color: var(--slate); margin: 0; }
    .modal-close {
        width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border);
        background: var(--surface); cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--muted); transition: all .2s;
    }
    .modal-close:hover { background: #fee2e2; border-color: #ef4444; color: #ef4444; }
    .modal-body  { padding: 1.5rem; }
    .modal-footer {
        padding: 1rem 1.5rem; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: flex-end; gap: .75rem;
        position: sticky; bottom: 0; background: var(--white);
    }

    /* ── CHILDREN CHIPS ── */
    .children-chips { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .5rem; }
    .child-chip {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--sage-l); color: var(--sage);
        padding: .25rem .6rem; border-radius: 20px; font-size: .78rem; font-weight: 600;
    }
    .child-chip .remove {
        cursor: pointer; opacity: .6; transition: opacity .2s;
        display: flex; align-items: center;
    }
    .child-chip .remove:hover { opacity: 1; }

    /* ── FLASH TOASTS ── */
    .flash-toast {
        position: fixed; top: 1.25rem; right: 1.25rem; z-index: 9999;
        padding: .875rem 1.25rem; border-radius: 10px;
        display: flex; align-items: center; gap: .625rem;
        font-size: .875rem; font-weight: 500;
        box-shadow: 0 4px 20px rgba(0,0,0,.15);
        animation: slideRight .3s ease, fadeOut .4s 4s ease forwards;
    }
    .flash-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .flash-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    @keyframes slideRight { from { transform: translateX(20px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes fadeOut    { from { opacity: 1; } to   { opacity: 0; pointer-events: none; } }

    /* ── EMPTY STATE ── */
    .empty-state {
        text-align: center; padding: 3rem 1.5rem; color: var(--muted);
    }
    .empty-state svg { width: 48px; height: 48px; opacity: .3; margin: 0 auto 1rem; display: block; }
    .empty-state p { font-size: .875rem; }

    /* ── PAGINATION ── */
    .pagination-wrap { padding: 1rem 1.5rem; border-top: 1px solid var(--border); }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .two-col { grid-template-columns: 1fr; }
        .affect-card { position: static; }
    }
    @media (max-width: 768px) {
        .stat-strip { grid-template-columns: 1fr 1fr; }
        .form-grid  { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
        .stat-strip { grid-template-columns: 1fr; }
    }

    /* ── TABS ── */
    .tab-bar {
        display: flex; gap: .5rem; padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border); background: var(--surface);
    }
    .tab-btn {
        padding: .45rem .875rem; border-radius: 7px;
        font-size: .8rem; font-weight: 600; cursor: pointer;
        border: 1px solid transparent; transition: all .2s;
        color: var(--muted); background: transparent;
    }
    .tab-btn.active { background: var(--sage); color: #fff; border-color: var(--sage); }
    .tab-btn:not(.active):hover { background: var(--sage-l); color: var(--sage); }
</style>

{{-- ═══════════════════════════════ PAGE HEADER ═══════════════════════════════ --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Parents & Tuteurs</h1>
        <p class="page-sub">Gérez les parents, tuteurs et le suivi de leurs enfants</p>
    </div>
    <button class="btn-primary" onclick="openModal('modal-add-parent')">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau parent
    </button>
</div>

{{-- ═══════════════════════════════ STATS ═══════════════════════════════ --}}
<div class="stat-strip">
    <div class="stat-box">
        <div class="icon icon-sage">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <div class="val">{{ $stats['total'] }}</div>
            <div class="lbl">Total parents</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="icon icon-blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="val">{{ $stats['actifs'] }}</div>
            <div class="lbl">Actifs</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="icon icon-amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div>
            <div class="val">{{ $stats['total_enfants'] }}</div>
            <div class="lbl">Enfants suivis</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="icon icon-purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div>
            <div class="val">{{ $stats['avec_compte'] }}</div>
            <div class="lbl">Avec compte</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════ MAIN GRID ═══════════════════════════════ --}}
<div class="two-col">

    {{-- ── LEFT : TABLE DES PARENTS ── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Liste des parents / tuteurs</h2>
            <span style="font-size:.78rem;color:var(--muted);">{{ $parents->total() }} entrée(s)</span>
        </div>

        {{-- Search --}}
        <div class="search-bar">
            <svg width="16" height="16" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="search-parents" placeholder="Rechercher par nom, prénom, téléphone…"
                value="{{ request('search') }}"
                onkeyup="filterParents(this.value)">
            @if(request('search'))
                <a href="{{ route('admin.parents') }}" class="btn-secondary" style="padding:.4rem .75rem;font-size:.78rem;">
                    Effacer
                </a>
            @endif
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table class="parents-table" id="parents-table">
                <thead>
                    <tr>
                        <th>Parent / Tuteur</th>
                        <th>Contact</th>
                        <th>Profession</th>
                        <th>Enfants</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parents as $parent)
                    <tr data-search="{{ strtolower($parent->nom.' '.$parent->prenom.' '.$parent->telephone) }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div class="avatar {{ $parent->sexe === 'F' ? 'f' : '' }}">
                                    {{ strtoupper(substr($parent->prenom,0,1).substr($parent->nom,0,1)) }}
                                </div>
                                <div>
                                    <div class="parent-name">{{ $parent->prenom }} {{ $parent->nom }}</div>
                                    <div class="parent-meta">{{ $parent->matricule ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:.875rem;">{{ $parent->telephone ?? '—' }}</div>
                            <div style="font-size:.75rem;color:var(--muted);">{{ $parent->email ?? '' }}</div>
                        </td>
                        <td>{{ $parent->profession ?? '—' }}</td>
                        <td>
                            @if($parent->apprenants->count())
                                <button class="badge-link"
                                    onclick="showChildren({{ $parent->id }}, '{{ addslashes($parent->prenom.' '.$parent->nom) }}')">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/>
                                    </svg>
                                    {{ $parent->apprenants->count() }} enfant(s)
                                </button>
                            @else
                                <span style="color:var(--muted);font-size:.8rem;">Aucun</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-dot {{ $parent->status ? 'dot-active' : 'dot-inactive' }}"></span>
                            <span style="font-size:.78rem;margin-left:.3rem;">
                                {{ $parent->status ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:.4rem;">
                                {{-- Affecter enfant --}}
                                <button class="action-btn" title="Affecter un enfant"
                                    onclick="openAffectModal({{ $parent->id }}, '{{ addslashes($parent->prenom.' '.$parent->nom) }}')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </button>
                                {{-- Éditer --}}
                                <button class="action-btn" title="Modifier"
                                    onclick="openEditModal({{ $parent->id }})">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                {{-- Supprimer --}}
                                <form method="POST" action="{{ route('admin.parents.destroy', $parent->id) }}"
                                    onsubmit="return confirm('Supprimer ce parent ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn danger" title="Supprimer">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p>Aucun parent enregistré pour le moment.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($parents->hasPages())
        <div class="pagination-wrap">
            {{ $parents->links() }}
        </div>
        @endif
    </div>

    {{-- ── RIGHT : AFFECTATIONS RÉCENTES ── --}}
    <div class="card affect-card">
        <div class="card-header">
            <h2 class="card-title">Affectations récentes</h2>
        </div>
        <div class="card-body">
            @if($recentAffectations->count())
            <div class="affectation-list">
                @foreach($recentAffectations as $aff)
                <div class="affect-row">
                    <div class="left">
                        <div class="avatar" style="width:32px;height:32px;font-size:.68rem;">
                            {{ strtoupper(substr($aff->pivot_parent_prenom ?? $aff->parent->prenom ?? '?',0,1).substr($aff->pivot_parent_nom ?? $aff->parent->nom ?? '?',0,1)) }}
                        </div>
                        <div>
                            <div class="name">{{ $aff->prenom }} {{ $aff->nom }}</div>
                            <div class="meta">
                                {{ $aff->classe->name ?? 'Sans classe' }}
                                · Parent : {{ $aff->parents->first()?->prenom }} {{ $aff->parents->first()?->nom }}
                            </div>
                        </div>
                    </div>
                    <span class="lien-badge">{{ $aff->parents->first()?->pivot?->lien ?? 'parent' }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state" style="padding:2rem 1rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/>
                </svg>
                <p>Aucune affectation récente</p>
            </div>
            @endif
        </div>

        {{-- Quick stats --}}
        <div style="border-top:1px solid var(--border);padding:1rem 1.5rem;">
            <div class="section-title" style="margin-top:0;">Aperçu rapide</div>
            @foreach($apprenantsSansParent->take(5) as $ap)
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;">
                <div style="font-size:.8rem;color:var(--slate);">{{ $ap->prenom }} {{ $ap->nom }}</div>
                <button class="badge-link" style="background:#fef3c7;color:var(--amber);"
                    onclick="openAffectModalForApprenant({{ $ap->id }}, '{{ addslashes($ap->prenom.' '.$ap->nom) }}')">
                    + Lier parent
                </button>
            </div>
            @endforeach
            @if($apprenantsSansParent->count() > 5)
            <div style="font-size:.75rem;color:var(--muted);margin-top:.5rem;">
                + {{ $apprenantsSansParent->count() - 5 }} autre(s) élève(s) sans parent
            </div>
            @endif
            @if($apprenantsSansParent->isEmpty())
            <div style="font-size:.8rem;color:#22c55e;">✓ Tous les élèves ont un parent assigné</div>
            @endif
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL — AJOUTER UN PARENT
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-add-parent">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Nouveau parent / tuteur</h3>
            <button class="modal-close" onclick="closeModal('modal-add-parent')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.parents.store') }}">
            @csrf
            <div class="modal-body">

                <div class="section-title" style="margin-top:0;">Informations personnelles</div>
                <div class="form-grid">
                    <div class="field">
                        <label>Prénom *</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="ex: Marie">
                    </div>
                    <div class="field">
                        <label>Nom *</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="ex: DUPONT">
                    </div>
                    <div class="field">
                        <label>Sexe</label>
                        <select name="sexe">
                            <option value="">— Choisir —</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="+242 06 000 0000">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="parent@email.com">
                    </div>
                    <div class="field">
                        <label>Profession</label>
                        <input type="text" name="profession" value="{{ old('profession') }}" placeholder="ex: Médecin">
                    </div>
                    <div class="field form-grid full" style="grid-column:span 2;">
                        <label>Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="ex: Brazzaville, Poto-Poto">
                    </div>
                </div>

                <div class="section-title">Affecter un enfant (optionnel)</div>

                {{-- Filtres rapides --}}
                <div class="form-grid" style="margin-bottom:.5rem;">
                    <div class="field">
                        <label>Niveau</label>
                        <select id="add-f-niveau" onchange="searchApprenants('add')">
                            <option value="">Tous</option>
                            @foreach($niveaux as $n)
                            <option value="{{ $n->id }}">{{ $n->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Classe</label>
                        <select id="add-f-classe" onchange="searchApprenants('add')">
                            <option value="">Toutes</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="field" style="margin-bottom:.75rem;">
                    <input type="text" id="add-f-search"
                        placeholder="🔍 Rechercher un élève par nom, prénom, matricule…"
                        oninput="searchApprenants('add')" autocomplete="off">
                </div>

                <div id="add-apprenant-results" style="
                    max-height:180px;overflow-y:auto;
                    border:1px solid var(--border);border-radius:10px;
                    background:var(--surface);display:none;margin-bottom:.75rem;">
                </div>

                <div id="add-selected-box" style="display:none;
                    padding:.6rem 1rem;background:var(--sage-l);
                    border:1px solid #b5d6bf;border-radius:8px;margin-bottom:.75rem;
                    font-size:.85rem;color:var(--sage);font-weight:600;">
                </div>

                <input type="hidden" name="apprenant_id" id="add-apprenant-id">

                <div class="form-grid">
                    <div class="field" style="grid-column:span 2;">
                        <label>Lien de parenté</label>
                        <select name="lien">
                            <option value="père">Père</option>
                            <option value="mère">Mère</option>
                            <option value="tuteur">Tuteur / Tutrice</option>
                            <option value="grand-père">Grand-père</option>
                            <option value="grand-mère">Grand-mère</option>
                            <option value="oncle">Oncle</option>
                            <option value="tante">Tante</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>

                <div class="section-title">Compte utilisateur (optionnel)</div>
                <div class="form-grid">
                    <div class="field">
                        <label>Mot de passe</label>
                        <input type="password" name="password" placeholder="Min. 8 caractères">
                        <span class="hint">Laissez vide pour ne pas créer de compte</span>
                    </div>
                    <div class="field">
                        <label>Confirmer</label>
                        <input type="password" name="password_confirmation" placeholder="Confirmer">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-add-parent')">Annuler</button>
                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL — ÉDITER UN PARENT
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-edit-parent">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Modifier le parent</h3>
            <button class="modal-close" onclick="closeModal('modal-edit-parent')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="edit-parent-form">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="section-title" style="margin-top:0;">Informations personnelles</div>
                <div class="form-grid">
                    <div class="field">
                        <label>Prénom *</label>
                        <input type="text" name="prenom" id="edit-prenom" required>
                    </div>
                    <div class="field">
                        <label>Nom *</label>
                        <input type="text" name="nom" id="edit-nom" required>
                    </div>
                    <div class="field">
                        <label>Sexe</label>
                        <select name="sexe" id="edit-sexe">
                            <option value="">— Choisir —</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" id="edit-telephone">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" id="edit-email">
                    </div>
                    <div class="field">
                        <label>Profession</label>
                        <input type="text" name="profession" id="edit-profession">
                    </div>
                    <div class="field" style="grid-column:span 2;">
                        <label>Adresse</label>
                        <input type="text" name="adresse" id="edit-adresse">
                    </div>
                    <div class="field">
                        <label>Statut</label>
                        <select name="status" id="edit-status">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-parent')">Annuler</button>
                <button type="submit" class="btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL — AFFECTER UN ENFANT  (recherche AJAX filtrée)
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-affect">
    <div class="modal" style="width:520px;">
        <div class="modal-header">
            <h3 class="modal-title" id="affect-modal-title">Affecter un enfant</h3>
            <button class="modal-close" onclick="closeModal('modal-affect')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="affect-form" action="{{ route('admin.parents.affect') }}">
            @csrf
            <input type="hidden" name="parent_id"    id="affect-parent-id">
            <input type="hidden" name="apprenant_id" id="affect-apprenant-id">

            <div class="modal-body">

                {{-- ── FILTRES ── --}}
                <div class="section-title" style="margin-top:0;">Filtrer les élèves</div>
                <div class="form-grid" style="margin-bottom:.75rem;">
                    <div class="field">
                        <label>Niveau</label>
                        <select id="f-niveau" onchange="resetFilters('niveau')">
                            <option value="">Tous les niveaux</option>
                            @foreach($niveaux as $n)
                            <option value="{{ $n->id }}">{{ $n->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Filière</label>
                        <select id="f-filiere" onchange="resetFilters('filiere')">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $f)
                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Classe</label>
                        <select id="f-classe" onchange="resetFilters('classe')">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Recherche</label>
                        <div style="position:relative;">
                            <input type="text" id="f-search"
                                placeholder="Nom, prénom, matricule…"
                                oninput="searchApprenants()"
                                autocomplete="off">
                            <span id="f-spinner" style="
                                display:none;position:absolute;right:.6rem;top:50%;
                                transform:translateY(-50%);font-size:.75rem;color:var(--muted);">
                                ⏳
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ── RÉSULTATS ── --}}
                <div class="section-title">Sélectionner un élève
                    <span id="f-count" style="font-weight:400;text-transform:none;font-size:.72rem;color:var(--muted);"></span>
                </div>

                <div id="apprenant-results" style="
                    max-height:260px; overflow-y:auto;
                    border:1px solid var(--border); border-radius:10px;
                    background:var(--surface);">
                    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.85rem;">
                        Utilisez les filtres ci-dessus pour rechercher un élève.
                    </div>
                </div>

                {{-- Élève sélectionné --}}
                <div id="selected-apprenant-box" style="display:none;
                    margin-top:.75rem;padding:.75rem 1rem;
                    background:var(--sage-l);border:1px solid #b5d6bf;border-radius:10px;
                    display:none;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-weight:600;font-size:.875rem;color:var(--sage);"
                            id="selected-apprenant-name"></div>
                        <div style="font-size:.75rem;color:var(--muted);"
                            id="selected-apprenant-meta"></div>
                    </div>
                    <button type="button" onclick="clearSelectedApprenant()"
                        style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:1.1rem;">✕</button>
                </div>

                {{-- ── LIEN ── --}}
                <div class="section-title">Lien de parenté</div>
                <div class="field">
                    <select name="lien" required>
                        <option value="père">Père</option>
                        <option value="mère">Mère</option>
                        <option value="tuteur" selected>Tuteur / Tutrice</option>
                        <option value="grand-père">Grand-père</option>
                        <option value="grand-mère">Grand-mère</option>
                        <option value="oncle">Oncle</option>
                        <option value="tante">Tante</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modal-affect')">Annuler</button>
                <button type="submit" class="btn-primary" id="affect-submit-btn" disabled
                    style="opacity:.5;cursor:not-allowed;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/>
                    </svg>
                    Affecter
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════
     MODAL — VOIR LES ENFANTS D'UN PARENT
══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-children">
    <div class="modal" style="width:500px;">
        <div class="modal-header">
            <h3 class="modal-title" id="children-modal-title">Enfants</h3>
            <button class="modal-close" onclick="closeModal('modal-children')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="modal-body" id="children-list">
            {{-- Rempli par JS --}}
        </div>
    </div>
</div>


@php
    $parentsJs = $parents->map(function ($p) {
        return [
            'id'         => $p->id,
            'prenom'     => $p->prenom,
            'nom'        => $p->nom,
            'sexe'       => $p->sexe,
            'telephone'  => $p->telephone,
            'email'      => $p->email,
            'profession' => $p->profession,
            'adresse'    => $p->adresse,
            'status'     => $p->status,
            'apprenants' => $p->apprenants->map(function ($a) {
                return [
                    'id'        => $a->id,
                    'prenom'    => $a->prenom,
                    'nom'       => $a->nom,
                    'classe'    => optional($a->classe)->name,
                    'matricule' => $a->matricule,
                    'lien'      => optional($a->pivot)->lien ?? 'parent',
                ];
            })->values()->all(),
        ];
    })->values()->all();
@endphp

<script>
/* ════════════════════════════════════════════════════
   CONFIG
════════════════════════════════════════════════════ */
const parentsData = @json($parentsJs);
const ROUTES = {
    update:         "{{ route('admin.parents.update', ':id') }}",
    detach:         "{{ route('admin.parents.detach') }}",
    searchStudents: "{{ route('admin.parents.search-apprenants') }}",
};

/* ════════════════════════════════════════════════════
   MODAL HELPERS
════════════════════════════════════════════════════ */
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

document.addEventListener('DOMContentLoaded', function () {

    /* ── Fermer sur clic backdrop ── */
    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
    });

    /* ── Fermer sur Échap ── */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(el => el.classList.remove('open'));
        }
    });

    /* ── AUTO-DISMISS FLASH ── */
    setTimeout(() => {
        document.querySelectorAll('.flash-toast').forEach(el => el.remove());
    }, 4500);
});

/* ════════════════════════════════════════════════════
   TABLE SEARCH (filtre côté client)
════════════════════════════════════════════════════ */
function filterParents(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#parents-table tbody tr[data-search]').forEach(row => {
        row.style.display = row.dataset.search.includes(q) ? '' : 'none';
    });
}

/* ════════════════════════════════════════════════════
   EDIT MODAL
════════════════════════════════════════════════════ */
function openEditModal(id) {
    const p = parentsData.find(x => x.id === id);
    if (!p) return;
    document.getElementById('edit-prenom').value     = p.prenom     || '';
    document.getElementById('edit-nom').value        = p.nom        || '';
    document.getElementById('edit-sexe').value       = p.sexe       || '';
    document.getElementById('edit-telephone').value  = p.telephone  || '';
    document.getElementById('edit-email').value      = p.email      || '';
    document.getElementById('edit-profession').value = p.profession || '';
    document.getElementById('edit-adresse').value    = p.adresse    || '';
    document.getElementById('edit-status').value     = p.status ? '1' : '0';
    document.getElementById('edit-parent-form').action = ROUTES.update.replace(':id', id);
    openModal('modal-edit-parent');
}

/* ════════════════════════════════════════════════════
   AFFECT MODAL
════════════════════════════════════════════════════ */
function openAffectModal(parentId, parentName) {
    document.getElementById('affect-parent-id').value    = parentId;
    document.getElementById('affect-apprenant-id').value = '';
    document.getElementById('affect-modal-title').textContent = 'Affecter un enfant à ' + parentName;
    resetAffectModal();
    openModal('modal-affect');
}

function openAffectModalForApprenant(apprenantId, apprenantName) {
    document.getElementById('affect-parent-id').value    = '';
    document.getElementById('affect-apprenant-id').value = apprenantId;
    document.getElementById('affect-modal-title').textContent = 'Lier un parent à ' + apprenantName;
    resetAffectModal();
    document.getElementById('selected-apprenant-name').textContent = apprenantName;
    document.getElementById('selected-apprenant-meta').textContent = '';
    document.getElementById('selected-apprenant-box').style.display = 'flex';
    enableAffectSubmit();
    openModal('modal-affect');
}

function resetAffectModal() {
    const fs = document.getElementById('f-search');
    const fn = document.getElementById('f-niveau');
    const ff = document.getElementById('f-filiere');
    const fc = document.getElementById('f-classe');
    if (fs) fs.value = '';
    if (fn) fn.value = '';
    if (ff) ff.value = '';
    if (fc) fc.value = '';
    document.getElementById('apprenant-results').innerHTML =
        '<div style="padding:2rem;text-align:center;color:var(--muted);font-size:.85rem;">Utilisez les filtres ci-dessus pour rechercher un élève.</div>';
    document.getElementById('selected-apprenant-box').style.display = 'none';
    document.getElementById('affect-apprenant-id').value = '';
    disableAffectSubmit();
}

function enableAffectSubmit() {
    const btn = document.getElementById('affect-submit-btn');
    if (!btn) return;
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor  = 'pointer';
}
function disableAffectSubmit() {
    const btn = document.getElementById('affect-submit-btn');
    if (!btn) return;
    btn.disabled = true;
    btn.style.opacity = '.5';
    btn.style.cursor  = 'not-allowed';
}
function clearSelectedApprenant() {
    document.getElementById('affect-apprenant-id').value = '';
    document.getElementById('selected-apprenant-box').style.display = 'none';
    disableAffectSubmit();
}

/* ════════════════════════════════════════════════════
   AJAX — RECHERCHE APPRENANTS
════════════════════════════════════════════════════ */
let searchDebounce = null;

function resetFilters() {
    searchApprenants('affect');
}

function searchApprenants(context) {
    context = context || 'affect';
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => _doSearch(context), 300);
}

function _doSearch(context) {
    const isAffect = (context === 'affect');

    const q         = (document.getElementById(isAffect ? 'f-search'  : 'add-f-search')  || {value:''}).value;
    const niveauId  = (document.getElementById(isAffect ? 'f-niveau'  : 'add-f-niveau')  || {value:''}).value;
    const filiereId = (document.getElementById(isAffect ? 'f-filiere' : 'x')             || {value:''}).value;
    const classeId  = (document.getElementById(isAffect ? 'f-classe'  : 'add-f-classe')  || {value:''}).value;

    if (!q && !niveauId && !filiereId && !classeId) {
        const el = document.getElementById(isAffect ? 'apprenant-results' : 'add-apprenant-results');
        if (el) {
            el.innerHTML = '<div style="padding:1.5rem;text-align:center;color:var(--muted);font-size:.85rem;">Saisissez au moins un critère.</div>';
            el.style.display = 'block';
        }
        return;
    }

    const spinner = document.getElementById('f-spinner');
    if (spinner) spinner.style.display = 'inline';

    const params = new URLSearchParams();
    if (q)         params.set('q',          q);
    if (niveauId)  params.set('niveau_id',  niveauId);
    if (filiereId) params.set('filiere_id', filiereId);
    if (classeId)  params.set('classe_id',  classeId);

    fetch(ROUTES.searchStudents + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(json) {
        if (spinner) spinner.style.display = 'none';
        renderResults(json.data, json.total, context);
    })
    .catch(function() {
        if (spinner) spinner.style.display = 'none';
    });
}

function renderResults(data, total, context) {
    const isAffect  = (context === 'affect');
    const container = document.getElementById(isAffect ? 'apprenant-results' : 'add-apprenant-results');
    const countEl   = document.getElementById('f-count');

    if (!container) return;
    container.style.display = 'block';

    if (countEl) {
        countEl.textContent = total > 50
            ? ' — ' + total + ' résultats, affinez votre recherche'
            : ' — ' + total + ' résultat(s)';
    }

    if (!data || !data.length) {
        container.innerHTML = '<div style="padding:1.5rem;text-align:center;color:var(--muted);font-size:.85rem;">Aucun élève trouvé.</div>';
        return;
    }

    container.innerHTML = data.map(function(a) {
        var initials = ((a.prenom||'').charAt(0) + (a.nom||'').charAt(0)).toUpperCase();
        var meta     = [a.classe, a.niveau, a.matricule].filter(Boolean).join(' · ');
        var pSafe    = (a.prenom||'').replace(/'/g,"\\'");
        var nSafe    = (a.nom||'').replace(/'/g,"\\'");
        var cSafe    = (a.classe||'').replace(/'/g,"\\'");
        var mSafe    = (a.matricule||'').replace(/'/g,"\\'");
        return '<div style="padding:.65rem 1rem;cursor:pointer;border-bottom:1px solid var(--border);'
             + 'display:flex;align-items:center;gap:.75rem;transition:background .15s;"'
             + ' onmouseenter="this.style.background=\'var(--sage-l)\'"'
             + ' onmouseleave="this.style.background=\'\'"'
             + ' onclick="selectApprenant(' + a.id + ',\'' + pSafe + ' ' + nSafe + '\',\'' + cSafe + '\',\'' + mSafe + '\',\'' + context + '\')">'
             + '<div class="avatar" style="width:32px;height:32px;font-size:.68rem;flex-shrink:0;">' + initials + '</div>'
             + '<div style="flex:1;min-width:0;">'
             + '<div style="font-weight:600;font-size:.85rem;color:var(--slate);">' + (a.prenom||'') + ' ' + (a.nom||'') + '</div>'
             + '<div style="font-size:.72rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + meta + '</div>'
             + '</div>'
             + '<svg width="14" height="14" fill="none" stroke="var(--sage)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>'
             + '</div>';
    }).join('');
}

function selectApprenant(id, name, classe, matricule, context) {
    if (context === 'affect') {
        document.getElementById('affect-apprenant-id').value = id;
        document.getElementById('selected-apprenant-name').textContent = name;
        document.getElementById('selected-apprenant-meta').textContent = [classe, matricule].filter(Boolean).join(' · ');
        document.getElementById('selected-apprenant-box').style.display = 'flex';
        document.getElementById('apprenant-results').style.display = 'none';
        enableAffectSubmit();
    } else {
        document.getElementById('add-apprenant-id').value = id;
        var box = document.getElementById('add-selected-box');
        box.textContent = '✓ ' + name + (classe ? ' — ' + classe : '');
        box.style.display = 'block';
        document.getElementById('add-apprenant-results').style.display = 'none';
    }
}

/* ════════════════════════════════════════════════════
   CHILDREN MODAL
════════════════════════════════════════════════════ */
function showChildren(parentId, parentName) {
    var p = null;
    for (var i = 0; i < parentsData.length; i++) {
        if (parentsData[i].id === parentId) { p = parentsData[i]; break; }
    }
    document.getElementById('children-modal-title').textContent = 'Enfants de ' + parentName;

    var html = '';
    if (p && p.apprenants && p.apprenants.length) {
        p.apprenants.forEach(function(a) {
            var init = ((a.prenom||'?').charAt(0) + (a.nom||'?').charAt(0)).toUpperCase();
            html += '<div style="display:flex;align-items:center;justify-content:space-between;'
                  + 'padding:.875rem;border:1px solid var(--border);border-radius:10px;margin-bottom:.75rem;">'
                  + '<div style="display:flex;align-items:center;gap:.75rem;">'
                  + '<div class="avatar" style="width:36px;height:36px;font-size:.72rem;">' + init + '</div>'
                  + '<div>'
                  + '<div style="font-weight:600;color:var(--slate);font-size:.875rem;">' + (a.prenom||'') + ' ' + (a.nom||'') + '</div>'
                  + '<div style="font-size:.75rem;color:var(--muted);">' + (a.classe||'Sans classe') + ' · ' + (a.matricule||'') + '</div>'
                  + '</div></div>'
                  + '<div style="display:flex;align-items:center;gap:.5rem;">'
                  + '<span class="lien-badge">' + (a.lien||'parent') + '</span>'
                  + '<form method="POST" action="' + ROUTES.detach + '" onsubmit="return confirm(\'Retirer cet enfant ?\')">'
                  + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                  + '<input type="hidden" name="parent_id" value="' + parentId + '">'
                  + '<input type="hidden" name="apprenant_id" value="' + a.id + '">'
                  + '<button type="submit" class="action-btn danger" title="Retirer">'
                  + '<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                  + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                  + '</svg></button></form>'
                  + '</div></div>';
        });
    } else {
        html = '<div class="empty-state"><p>Aucun enfant affecté à ce parent.</p></div>';
    }

    document.getElementById('children-list').innerHTML = html;
    openModal('modal-children');
}
</script>

@endsection