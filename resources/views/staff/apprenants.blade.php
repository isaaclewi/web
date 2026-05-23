@extends('staff.master')

@section('title', 'Apprenants')
@section('page-title', 'Gestion des apprenants')
@section('page-sub', 'Apprenants')

@push('styles')
<style>
    @keyframes fadeUp { from { opacity:0; transform:translateY(14px) } to { opacity:1; transform:none } }
    .fu { animation: fadeUp .45s cubic-bezier(.22,1,.36,1) both }
    .fu1 { animation-delay:.04s }.fu2 { animation-delay:.09s }.fu3 { animation-delay:.14s }.fu4 { animation-delay:.19s }

    /* ── KPI strip ── */
    .kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.75rem }
    .kpi {
        background:#fff; border:1px solid var(--brd); border-radius:14px;
        padding:1.25rem 1.375rem; position:relative; overflow:hidden;
        transition:box-shadow .2s, border-color .2s;
    }
    .kpi:hover { box-shadow:0 4px 18px rgba(0,0,0,.07); border-color:var(--brd-d); }
    .kpi-accent { position:absolute; top:0; left:0; width:3px; height:100%; border-radius:14px 0 0 14px; }
    .kpi-val { font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:var(--night); line-height:1; letter-spacing:-.05em; margin-top:.25rem; }
    .kpi-lbl { font-size:.73rem; color:var(--mist); margin-top:.35rem; font-weight:400; }
    .kpi-icon { position:absolute; top:1.125rem; right:1.125rem; width:36px; height:36px; border-radius:10px; background:var(--bg); display:flex; align-items:center; justify-content:center; }
    .kpi-icon svg { width:16px; height:16px; }

    /* ── Toolbar ── */
    .toolbar { display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem; flex-wrap:wrap; }
    .search-wrap { position:relative; flex:1; min-width:200px; max-width:360px; }
    .search-wrap svg { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--mist); pointer-events:none; }
    .search-inp { width:100%; padding:.55rem .875rem .55rem 2.375rem; border:1.5px solid var(--brd); border-radius:9px; font-size:.84rem; font-family:inherit; color:var(--night); background:#fff; outline:none; transition:border-color .18s,box-shadow .18s; }
    .search-inp:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(245,158,11,.12); }
    .search-inp::placeholder { color:var(--mist); }

    .filter-sel { padding:.55rem .875rem; border:1.5px solid var(--brd); border-radius:9px; font-size:.83rem; font-family:inherit; color:var(--night); background:#fff; outline:none; cursor:pointer; transition:border-color .18s; min-width:120px; }
    .filter-sel:focus { border-color:var(--gold); }

    /* ── Table enrichie ── */
    .tbl-wrap { background:#fff; border:1px solid var(--brd); border-radius:14px; overflow:hidden; }
    .tbl-header { padding:.875rem 1.375rem; border-bottom:1px solid var(--brd); display:flex; align-items:center; justify-content:space-between; background:#fafbfd; gap:.75rem; flex-wrap:wrap; }
    .tbl-header h3 { font-family:'Syne',sans-serif; font-size:.9rem; font-weight:700; letter-spacing:-.01em; }
    .tbl-count { font-size:.73rem; color:var(--mist); background:var(--bg); border:1px solid var(--brd); border-radius:20px; padding:.15rem .65rem; font-weight:500; }

    table { width:100%; border-collapse:collapse; }
    thead th { padding:.65rem 1.25rem; text-align:left; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--mist); border-bottom:1px solid var(--brd); background:#fafbfd; white-space:nowrap; }
    thead th:first-child { padding-left:1.375rem; }
    thead th:last-child { text-align:right; padding-right:1.375rem; }
    tbody td { padding:.875rem 1.25rem; border-bottom:1px solid #f3f5f9; font-size:.85rem; color:#374151; vertical-align:middle; }
    tbody td:first-child { padding-left:1.375rem; }
    tbody td:last-child { padding-right:1.375rem; text-align:right; }
    tbody tr { transition:background .12s; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#fafbfd; }

    /* Avatar apprenant */
    .av-cell { display:flex; align-items:center; gap:.75rem; }
    .av-circle { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-weight:700; font-size:.75rem; flex-shrink:0; }
    .av-m { background:#dbeafe; color:#1e40af; }
    .av-f { background:#fce7f3; color:#9d174d; }
    .av-n { background:var(--bg); color:var(--mist); }
    .av-name { font-weight:600; color:var(--night); font-size:.85rem; line-height:1.25; }
    .av-mat  { font-size:.72rem; color:var(--mist); margin-top:.05rem; font-family:'DM Sans',sans-serif; }

    /* Action buttons row */
    .act-row { display:flex; align-items:center; gap:.375rem; justify-content:flex-end; }
    .act-btn { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:7px; border:none; cursor:pointer; transition:all .15s; background:var(--bg); color:var(--mist); font-family:inherit; }
    .act-btn:hover { background:var(--brd); color:var(--night); }
    .act-btn.danger:hover { background:var(--err-l); color:var(--err); }
    .act-btn svg { width:14px; height:14px; pointer-events:none; }

    /* ── Modal ── */
    .modal-bg { display:none; position:fixed; inset:0; background:rgba(8,12,20,.55); backdrop-filter:blur(4px); z-index:200; align-items:center; justify-content:center; padding:1rem; }
    .modal-bg.open { display:flex; }
    .modal { background:#fff; border-radius:16px; width:100%; max-width:540px; box-shadow:0 24px 60px rgba(0,0,0,.18); animation:modalIn .3s cubic-bezier(.22,1,.36,1); }
    @keyframes modalIn { from { transform:translateY(20px) scale(.97); opacity:0 } to { opacity:1; transform:none } }
    .modal-hd { padding:1.25rem 1.5rem; border-bottom:1px solid var(--brd); display:flex; align-items:center; justify-content:space-between; }
    .modal-title { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; color:var(--night); }
    .modal-close { width:30px; height:30px; border-radius:7px; border:none; background:var(--bg); color:var(--mist); cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
    .modal-close:hover { background:var(--err-l); color:var(--err); }
    .modal-close svg { width:13px; height:13px; }
    .modal-body { padding:1.5rem; }
    .modal-footer { padding:1rem 1.5rem; border-top:1px solid var(--brd); display:flex; justify-content:flex-end; gap:.625rem; background:#fafbfd; border-radius:0 0 16px 16px; }

    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
    .form-group { display:flex; flex-direction:column; gap:.3rem; margin-bottom:.875rem; }
    .form-group:last-child { margin-bottom:0; }

    /* ── Import zone ── */
    .import-zone { border:2px dashed var(--brd); border-radius:10px; padding:1.5rem; text-align:center; transition:border-color .18s,background .18s; cursor:pointer; }
    .import-zone:hover { border-color:var(--gold); background:#fffbf0; }
    .import-zone input[type=file] { display:none; }

    /* ── Pagination ── */
    .pag-wrap { padding:.875rem 1.375rem; border-top:1px solid var(--brd); display:flex; align-items:center; justify-content:between; gap:.5rem; background:#fafbfd; }

    /* ── Responsive ── */
    @media(max-width:768px) {
        .kpi-row { grid-template-columns:1fr 1fr; }
        .form-row { grid-template-columns:1fr; }
        .hcol-mat,.hcol-niveau,.hcol-filiere { display:none; }
        .dcol-mat,.dcol-niveau,.dcol-filiere { display:none; }
    }
    @media(max-width:480px) {
        .kpi-row { grid-template-columns:1fr; }
        .hcol-classe { display:none; }
        .dcol-classe { display:none; }
    }
</style>
@endpush

@section('content')
@php
    $ini = fn($a) => strtoupper(mb_substr($a->prenom,0,1).mb_substr($a->nom,0,1));
@endphp

{{-- ══ KPI ══ --}}
<div class="kpi-row fu fu1">
    <div class="kpi">
        <div class="kpi-accent" style="background:var(--info)"></div>
        <div class="kpi-icon"><svg fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <p style="font-size:.68rem;text-transform:uppercase;letter-spacing:.1em;color:var(--mist);font-weight:600">Total</p>
        <p class="kpi-val">{{ number_format($stats['total']) }}</p>
        <p class="kpi-lbl">Apprenants inscrits</p>
    </div>
    <div class="kpi">
        <div class="kpi-accent" style="background:var(--ok)"></div>
        <div class="kpi-icon"><svg fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <p style="font-size:.68rem;text-transform:uppercase;letter-spacing:.1em;color:var(--mist);font-weight:600">Actifs</p>
        <p class="kpi-val" style="color:var(--ok)">{{ number_format($stats['active']) }}</p>
        <p class="kpi-lbl">Comptes actifs</p>
    </div>
    <div class="kpi">
        <div class="kpi-accent" style="background:#3b82f6"></div>
        <div class="kpi-icon"><svg fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
        <p style="font-size:.68rem;text-transform:uppercase;letter-spacing:.1em;color:var(--mist);font-weight:600">Garçons</p>
        <p class="kpi-val" style="color:#3b82f6">{{ number_format($stats['garcons']) }}</p>
        <p class="kpi-lbl">Élèves masculins</p>
    </div>
    <div class="kpi">
        <div class="kpi-accent" style="background:#ec4899"></div>
        <div class="kpi-icon"><svg fill="none" stroke="#ec4899" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
        <p style="font-size:.68rem;text-transform:uppercase;letter-spacing:.1em;color:var(--mist);font-weight:600">Filles</p>
        <p class="kpi-val" style="color:#ec4899">{{ number_format($stats['filles']) }}</p>
        <p class="kpi-lbl">Élèves féminines</p>
    </div>
</div>

{{-- ══ TOOLBAR ══ --}}
<div class="toolbar fu fu2">
    {{-- Recherche --}}
    <form method="GET" action="{{ route('staff.apprenants') }}" style="display:contents">
        <div class="search-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input class="search-inp" type="search" name="search" placeholder="Nom, prénom, matricule…"
                value="{{ request('search') }}" autocomplete="off">
        </div>
        <select class="filter-sel" name="classe_id" onchange="this.form.submit()">
            <option value="">Toutes les classes</option>
            @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('classe_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <select class="filter-sel" name="niveau_id" onchange="this.form.submit()">
            <option value="">Tous les niveaux</option>
            @foreach($niveaux as $n)
                <option value="{{ $n->id }}" {{ request('niveau_id') == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>
            @endforeach
        </select>
        @if(request()->hasAny(['search','classe_id','niveau_id']))
            <a href="{{ route('staff.apprenants') }}" class="btn btn-ot btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Réinitialiser
            </a>
        @endif
    </form>

    <div style="margin-left:auto;display:flex;gap:.625rem;flex-wrap:wrap">
        {{-- Export CSV — AdminDashboardController::apprenantExport() --}}
        <a href="{{ route('admin.apprenants.export') }}" class="btn btn-ot btn-sm" title="Exporter CSV">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exporter
        </a>
        {{-- Import CSV --}}
        <button onclick="document.getElementById('modal-import').classList.add('open')" class="btn btn-ot btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Importer
        </button>
        {{-- Ajouter — AdminDashboardController::apprenantStore() --}}
        <button onclick="openCreate()" class="btn btn-gold btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter
        </button>
    </div>
</div>

{{-- ══ TABLE ══ --}}
<div class="tbl-wrap fu fu3">
    <div class="tbl-header">
        <div style="display:flex;align-items:center;gap:.75rem">
            <h3>Liste des apprenants</h3>
            <span class="tbl-count">{{ $apprenants->total() }} au total</span>
        </div>
        <span style="font-size:.73rem;color:var(--mist)">
            Page {{ $apprenants->currentPage() }} / {{ $apprenants->lastPage() }}
        </span>
    </div>

    @if($apprenants->isEmpty())
    <div class="s-empty">
        <div class="s-empty-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <h4>Aucun apprenant trouvé</h4>
        <p>Modifiez vos filtres ou ajoutez un premier apprenant.</p>
    </div>
    @else
    <div style="overflow-x:auto">
    <table>
        <thead>
            <tr>
                <th>Apprenant</th>
                <th class="hcol-classe">Classe</th>
                <th class="hcol-niveau">Niveau</th>
                <th class="hcol-filiere">Filière</th>
                <th>Sexe</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apprenants as $a)
            <tr>
                {{-- Identité --}}
                <td>
                    <div class="av-cell">
                        <div class="av-circle {{ $a->sexe === 'M' ? 'av-m' : ($a->sexe === 'F' ? 'av-f' : 'av-n') }}">
                            {{ $ini($a) }}
                        </div>
                        <div>
                            <div class="av-name">{{ $a->prenom }} {{ $a->nom }}</div>
                            <div class="av-mat">{{ $a->matricule ?? '—' }}</div>
                        </div>
                    </div>
                </td>
                {{-- Classe --}}
                <td class="dcol-classe">
                    @if($a->classe)
                        <span class="bdg bdg-b">{{ $a->classe->name }}</span>
                    @else
                        <span style="color:var(--mist);font-size:.78rem">Non affecté</span>
                    @endif
                </td>
                {{-- Niveau --}}
                <td class="dcol-niveau" style="font-size:.82rem;color:var(--ink-70)">{{ $a->niveau->name ?? '—' }}</td>
                {{-- Filière --}}
                <td class="dcol-filiere" style="font-size:.82rem;color:var(--ink-70)">{{ $a->filiere->name ?? '—' }}</td>
                {{-- Sexe --}}
                <td>
                    @if($a->sexe === 'M')
                        <span class="bdg bdg-b">Garçon</span>
                    @elseif($a->sexe === 'F')
                        <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:20px;font-size:.69rem;font-weight:600;background:#fce7f3;color:#9d174d">
                            <span style="width:5px;height:5px;border-radius:50%;background:currentColor;opacity:.7"></span>Fille
                        </span>
                    @else
                        <span class="bdg bdg-n">—</span>
                    @endif
                </td>
                {{-- Statut --}}
                <td>
                    @if($a->status)
                        <span class="bdg bdg-g">Actif</span>
                    @else
                        <span class="bdg bdg-r">Inactif</span>
                    @endif
                </td>
                {{-- Actions --}}
                <td>
                    <div class="act-row">
                        {{-- Modifier — apprenantUpdate() --}}
                        <button class="act-btn" title="Modifier"
                            onclick='openEdit(@json($a))'>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        {{-- Reset password — apprenantResetPassword() --}}
                        <button class="act-btn" title="Réinitialiser le mot de passe"
                            onclick="openReset({{ $a->id }}, '{{ $a->prenom }} {{ $a->nom }}')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </button>
                        {{-- Supprimer — apprenantDestroy() --}}
                        <form method="POST" action="{{ route('admin.apprenants.destroy', $a->id) }}"
                            onsubmit="return confirm('Supprimer « {{ $a->prenom }} {{ $a->nom }} » ? Cette action est irréversible.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="act-btn danger" title="Supprimer">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    {{-- Pagination --}}
    @if($apprenants->hasPages())
    <div class="pag-wrap">
        <div style="flex:1;font-size:.73rem;color:var(--mist)">
            Affichage de {{ $apprenants->firstItem() }}–{{ $apprenants->lastItem() }} sur {{ $apprenants->total() }}
        </div>
        <div style="display:flex;gap:.375rem">
            @if($apprenants->onFirstPage())
                <span class="s-pag"><span style="opacity:.3">&laquo;</span></span>
            @else
                <a href="{{ $apprenants->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1px solid var(--brd);font-size:.82rem;color:var(--mist);text-decoration:none;transition:all .15s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">‹</a>
            @endif

            @foreach($apprenants->getUrlRange(max(1,$apprenants->currentPage()-2), min($apprenants->lastPage(),$apprenants->currentPage()+2)) as $page => $url)
                @if($page == $apprenants->currentPage())
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:var(--night);color:#fff;font-size:.82rem;font-weight:700">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1px solid var(--brd);font-size:.82rem;color:var(--mist);text-decoration:none;transition:all .15s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">{{ $page }}</a>
                @endif
            @endforeach

            @if($apprenants->hasMorePages())
                <a href="{{ $apprenants->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:1px solid var(--brd);font-size:.82rem;color:var(--mist);text-decoration:none;transition:all .15s" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">›</a>
            @else
                <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;opacity:.3">›</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

{{-- ══════════════════════════════════════════
 | MODAL : CRÉER / MODIFIER — apprenantStore() + apprenantUpdate()
 ══════════════════════════════════════════ --}}
<div class="modal-bg" id="modal-form">
    <div class="modal">
        <div class="modal-hd">
            <span class="modal-title" id="modal-form-title">Ajouter un apprenant</span>
            <button class="modal-close" onclick="closeForm()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-apprenant" method="POST" action="{{ route('admin.apprenants.store') }}">
            @csrf
            <div id="form-method-field"></div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="lbl">Prénom <span style="color:var(--err)">*</span></label>
                        <input class="inp" type="text" name="prenom" id="f-prenom" required placeholder="ex: Jean">
                    </div>
                    <div class="form-group">
                        <label class="lbl">Nom <span style="color:var(--err)">*</span></label>
                        <input class="inp" type="text" name="nom" id="f-nom" required placeholder="ex: NDOKI">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="lbl">Matricule</label>
                        <input class="inp" type="text" name="matricule" id="f-matricule" placeholder="Auto-généré si vide">
                    </div>
                    <div class="form-group">
                        <label class="lbl">Date de naissance</label>
                        <input class="inp" type="date" name="date_naissance" id="f-date_naissance">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="lbl">Sexe</label>
                        <select class="inp" name="sexe" id="f-sexe">
                            <option value="">— Choisir —</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="lbl">Statut</label>
                        <select class="inp" name="status" id="f-status">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="lbl">Classe</label>
                        <select class="inp" name="class_id" id="f-class_id">
                            <option value="">— Aucune —</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="lbl">Niveau</label>
                        <select class="inp" name="niveau_id" id="f-niveau_id">
                            <option value="">— Aucun —</option>
                            @foreach($niveaux as $n)
                                <option value="{{ $n->id }}">{{ $n->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="lbl">Filière</label>
                    <select class="inp" name="filiere_id" id="f-filiere_id">
                        <option value="">— Aucune —</option>
                        @foreach($filieres as $f)
                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row" id="f-login-fields" style="border-top:1px solid var(--brd);padding-top:.875rem;margin-top:.25rem">
                    <div class="form-group">
                        <label class="lbl">Email (compte)</label>
                        <input class="inp" type="email" name="email" id="f-email" placeholder="Optionnel">
                    </div>
                    <div class="form-group">
                        <label class="lbl">Mot de passe initial</label>
                        <input class="inp" type="password" name="password" id="f-password" placeholder="8 caractères min">
                    </div>
                </div>
                <input type="hidden" name="annee_academique" value="{{ $institution->academic_year }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ot" onclick="closeForm()">Annuler</button>
                <button type="submit" class="btn btn-gold">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span id="btn-form-label">Ajouter l'apprenant</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
 | MODAL : RESET MDP — apprenantResetPassword()
 ══════════════════════════════════════════ --}}
<div class="modal-bg" id="modal-reset">
    <div class="modal" style="max-width:400px">
        <div class="modal-hd">
            <span class="modal-title">Réinitialiser le mot de passe</span>
            <button class="modal-close" onclick="document.getElementById('modal-reset').classList.remove('open')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-reset" method="POST" action="">
            @csrf @method('PUT')
            <div class="modal-body">
                <p style="font-size:.84rem;color:var(--mist);margin-bottom:1rem" id="reset-name-label"></p>
                <div class="form-group">
                    <label class="lbl">Nouveau mot de passe <span style="color:var(--err)">*</span></label>
                    <input class="inp" type="password" name="password" required minlength="8" placeholder="8 caractères minimum">
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label class="lbl">Confirmer</label>
                    <input class="inp" type="password" name="password_confirmation" required placeholder="Répétez le mot de passe">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ot" onclick="document.getElementById('modal-reset').classList.remove('open')">Annuler</button>
                <button type="submit" class="btn btn-dk">Réinitialiser</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
 | MODAL : IMPORT CSV — apprenantImport()
 ══════════════════════════════════════════ --}}
<div class="modal-bg" id="modal-import">
    <div class="modal" style="max-width:460px">
        <div class="modal-hd">
            <span class="modal-title">Importer des apprenants</span>
            <button class="modal-close" onclick="document.getElementById('modal-import').classList.remove('open')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.apprenants.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="import-zone" onclick="document.getElementById('csv-input').click()">
                    <input type="file" name="csv_file" id="csv-input" accept=".csv,.txt,.xlsx" required
                        onchange="document.getElementById('csv-fname').textContent = this.files[0]?.name ?? 'Aucun fichier'">
                    <svg fill="none" stroke="var(--mist)" viewBox="0 0 24 24" style="width:32px;height:32px;margin:0 auto .75rem"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <p style="font-size:.84rem;font-weight:600;color:var(--night)">Cliquer pour sélectionner</p>
                    <p style="font-size:.73rem;color:var(--mist);margin-top:.25rem">CSV, TXT ou XLSX — 2 Mo max</p>
                    <p id="csv-fname" style="font-size:.78rem;color:var(--gold);margin-top:.5rem;font-weight:600">Aucun fichier sélectionné</p>
                </div>
                <div class="form-group" style="margin-top:1rem">
                    <label class="lbl">Classe par défaut (optionnel)</label>
                    <select class="inp" name="default_class_id">
                        <option value="">— Aucune —</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="background:#fffbf0;border:1px solid #fde68a;border-radius:8px;padding:.75rem;font-size:.76rem;color:#92400e;line-height:1.6;margin-top:.5rem">
                    <strong>Format attendu :</strong> nom, prenom, sexe, date_naissance, matricule, niveau_id, filiere_id, class_id, annee_academique
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ot" onclick="document.getElementById('modal-import').classList.remove('open')">Annuler</button>
                <button type="submit" class="btn btn-gold">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ROUTES = {
    store:  "{{ route('admin.apprenants.store') }}",
    reset:  "/admin/apprenants/{id}/reset-password",
    update: "/admin/apprenants/{id}",
};

/* ── Fermer modales au clic sur le fond ── */
document.querySelectorAll('.modal-bg').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-bg.open').forEach(m => m.classList.remove('open'));
});

/* ── CREATE ── */
function openCreate() {
    const form = document.getElementById('form-apprenant');
    form.reset();
    document.getElementById('form-method-field').innerHTML = '';
    form.action = ROUTES.store;
    document.getElementById('modal-form-title').textContent = 'Ajouter un apprenant';
    document.getElementById('btn-form-label').textContent = "Ajouter l'apprenant";
    document.getElementById('f-login-fields').style.display = '';
    document.getElementById('modal-form').classList.add('open');
    setTimeout(() => document.getElementById('f-prenom').focus(), 100);
}

/* ── EDIT — apprenantUpdate() ── */
function openEdit(a) {
    const form = document.getElementById('form-apprenant');
    form.reset();
    document.getElementById('form-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    form.action = ROUTES.update.replace('{id}', a.id);
    document.getElementById('modal-form-title').textContent = 'Modifier ' + a.prenom + ' ' + a.nom;
    document.getElementById('btn-form-label').textContent = 'Enregistrer';

    /* Remplir les champs */
    document.getElementById('f-prenom').value        = a.prenom ?? '';
    document.getElementById('f-nom').value           = a.nom ?? '';
    document.getElementById('f-matricule').value     = a.matricule ?? '';
    document.getElementById('f-date_naissance').value= a.date_naissance ?? '';
    document.getElementById('f-sexe').value          = a.sexe ?? '';
    document.getElementById('f-status').value        = a.status != null ? String(a.status) : '1';
    document.getElementById('f-class_id').value      = a.class_id ?? '';
    document.getElementById('f-niveau_id').value     = a.niveau_id ?? '';
    document.getElementById('f-filiere_id').value    = a.filiere_id ?? '';

    /* Masquer les champs email/mdp en mode édition */
    document.getElementById('f-login-fields').style.display = 'none';
    document.getElementById('modal-form').classList.add('open');
}

function closeForm() {
    document.getElementById('modal-form').classList.remove('open');
}

/* ── RESET PASSWORD — apprenantResetPassword() ── */
function openReset(id, nom) {
    document.getElementById('form-reset').action = ROUTES.reset.replace('{id}', id);
    document.getElementById('reset-name-label').textContent = 'Réinitialiser le mot de passe de « ' + nom + ' »';
    document.getElementById('form-reset').reset();
    document.getElementById('modal-reset').classList.add('open');
}
</script>
@endpush