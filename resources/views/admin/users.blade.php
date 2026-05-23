@extends('admin.master')

@push('styles')
<style>
    /* ══════════════════════════════════════════
       STAFF PAGE — LIGHT THEME
    ══════════════════════════════════════════ */

    /* ── Stat cards ── */
    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .875rem;
        padding: 1.1rem 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        transition: box-shadow .15s, border-color .15s;
    }
    .stat-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,.06); border-color: #d1d5db; }
    .stat-icon {
        width: 2.6rem; height: 2.6rem;
        border-radius: .625rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── Role pill tabs ── */
    .pill-tabs {
        display: flex; gap: .25rem;
        background: #f3f4f6;
        border-radius: .625rem; padding: .2rem;
        flex-wrap: wrap;
    }
    .pill-tab {
        padding: .35rem .85rem;
        border-radius: .4rem;
        font-size: .78rem; font-weight: 500;
        color: #6b7280; border: none;
        background: transparent; cursor: pointer;
        transition: all .15s; white-space: nowrap;
        display: flex; align-items: center; gap: .35rem;
    }
    .pill-tab.active { background: #1f2937; color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
    .pill-tab .chip {
        font-size: .68rem; font-weight: 600;
        background: rgba(255,255,255,.2);
        padding: .05rem .4rem; border-radius: 9999px;
    }
    .pill-tab:not(.active) .chip { background: #e5e7eb; color: #6b7280; }

    /* ── Table ── */
    .staff-table-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .875rem;
        overflow: hidden;
    }
    .staff-table { width: 100%; border-collapse: collapse; }
    .staff-table thead tr {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .staff-table th {
        padding: .75rem 1rem;
        font-size: .75rem; font-weight: 600;
        color: #6b7280; text-transform: uppercase;
        letter-spacing: .04em; text-align: left; white-space: nowrap;
    }
    .staff-table td {
        padding: .875rem 1rem;
        font-size: .8125rem; color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    .staff-table tbody tr:last-child td { border-bottom: none; }
    .staff-table tbody tr { transition: background .1s; }
    .staff-table tbody tr:hover { background: #fafafa; }

    /* ── Avatar ── */
    .avatar {
        width: 2.25rem; height: 2.25rem;
        border-radius: .5rem;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .75rem; letter-spacing: .04em;
        flex-shrink: 0;
    }

    /* ── Badges ── */
    .badge {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .2rem .65rem;
        border-radius: 9999px;
        font-size: .72rem; font-weight: 600; white-space: nowrap;
    }
    .badge-active    { background: #dcfce7; color: #16a34a; }
    .badge-inactive  { background: #fef9c3; color: #a16207; }
    .badge-blocked   { background: #fee2e2; color: #dc2626; }
    .badge-admin     { background: #ede9fe; color: #7c3aed; }
    .badge-comptable { background: #fef3c7; color: #b45309; }
    .badge-surveillant { background: #e0f2fe; color: #0369a1; }
    .badge-directeur { background: #f0fdf4; color: #166534; }
    .badge-dot { width: .45rem; height: .45rem; border-radius: 50%; background: currentColor; }

    /* ── Buttons ── */
    .btn-primary {
        display: inline-flex; align-items: center; gap: .45rem;
        padding: .55rem 1rem;
        background: #1f2937; color: #fff;
        border: none; border-radius: .5rem;
        font-size: .8125rem; font-weight: 500;
        cursor: pointer; transition: background .15s, transform .1s;
    }
    .btn-primary:hover { background: #374151; }
    .btn-primary:active { transform: scale(.97); }
    .btn-secondary {
        display: inline-flex; align-items: center; gap: .45rem;
        padding: .55rem 1rem;
        background: #fff; color: #374151;
        border: 1px solid #e5e7eb; border-radius: .5rem;
        font-size: .8125rem; font-weight: 500;
        cursor: pointer; transition: background .15s;
    }
    .btn-secondary:hover { background: #f9fafb; }
    .btn-danger {
        display: inline-flex; align-items: center; gap: .45rem;
        padding: .55rem 1rem;
        background: #fee2e2; color: #dc2626;
        border: none; border-radius: .5rem;
        font-size: .8125rem; font-weight: 500;
        cursor: pointer; transition: background .15s;
    }
    .btn-danger:hover { background: #fecaca; }

    /* ── Action menu ── */
    .action-menu { position: relative; }
    .action-dropdown {
        position: absolute; right: 0; top: 100%; margin-top: .25rem;
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: .5rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.10);
        min-width: 190px; z-index: 50;
        display: none; overflow: hidden;
    }
    .action-dropdown.open { display: block; }
    .action-dropdown-item {
        display: flex; align-items: center; gap: .625rem;
        padding: .625rem 1rem;
        font-size: .8125rem; color: #374151;
        cursor: pointer; transition: background .15s;
        border: none; background: none;
        width: 100%; text-align: left;
    }
    .action-dropdown-item:hover { background: #f3f4f6; }
    .action-dropdown-item.danger { color: #dc2626; }
    .action-dropdown-item.danger:hover { background: #fef2f2; }
    .action-dropdown-divider { height: 1px; background: #f3f4f6; }

    /* ── Filter bar ── */
    .filter-bar {
        display: flex; flex-wrap: wrap;
        align-items: center; gap: .75rem;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .875rem;
        padding: .875rem 1.25rem;
    }
    .filter-input {
        border: 1px solid #e5e7eb; border-radius: .5rem;
        padding: .5rem .875rem;
        font-size: .8125rem; color: #374151;
        background: #f9fafb;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }
    .filter-input:focus {
        outline: none; border-color: #9ca3af;
        background: #fff; box-shadow: 0 0 0 3px rgba(156,163,175,.15);
    }
    .search-wrap { position: relative; flex: 1; min-width: 200px; }
    .search-wrap svg {
        position: absolute; left: .65rem; top: 50%;
        transform: translateY(-50%); color: #9ca3af;
        width: .9rem; height: .9rem; pointer-events: none;
    }
    .search-wrap input { padding-left: 2rem; width: 100%; }

    /* ── Modal ── */
    .modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px);
        z-index: 100;
        display: none; align-items: center;
        justify-content: center; padding: 1rem;
    }
    .modal-backdrop.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 1rem;
        width: 100%; max-width: 600px;
        max-height: 90vh; overflow-y: auto;
        box-shadow: 0 24px 64px rgba(0,0,0,.18);
        animation: mIn .2s ease;
    }
    .modal-box.sm { max-width: 420px; }
    @keyframes mIn {
        from { opacity:0; transform: translateY(14px) scale(.97); }
        to   { opacity:1; transform: translateY(0) scale(1); }
    }
    .modal-header {
        display: flex; align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        position: sticky; top: 0; background: #fff; z-index: 1;
    }
    .modal-body { padding: 1.5rem; }
    .modal-footer {
        display: flex; justify-content: flex-end;
        gap: .75rem; padding: 1rem 1.5rem;
        background: #f9fafb;
        border-top: 1px solid #f3f4f6;
        border-radius: 0 0 1rem 1rem;
        position: sticky; bottom: 0;
    }

    /* ── Form ── */
    .form-label { display: block; font-size: .8rem; font-weight: 500; color: #374151; margin-bottom: .35rem; }
    .form-hint  { font-size: .75rem; color: #9ca3af; margin-top: .25rem; }
    .form-input {
        width: 100%; border: 1px solid #e5e7eb; border-radius: .5rem;
        padding: .6rem .875rem; font-size: .875rem; color: #1f2937;
        background: #fff; transition: border-color .2s, box-shadow .2s;
    }
    .form-input:focus { outline: none; border-color: #9ca3af; box-shadow: 0 0 0 3px rgba(156,163,175,.15); }
    .g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .g3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .75rem; }
    @media(max-width:600px) { .g2, .g3 { grid-template-columns: 1fr; } }

    .sec-sep {
        display: flex; align-items: center; gap: .75rem;
        margin: 1.25rem 0 1rem;
    }
    .sec-sep::before, .sec-sep::after { content:''; flex:1; height:1px; background:#e5e7eb; }
    .sec-sep span { font-size:.7rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }

    /* ── Role picker ── */
    .role-selector-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .5rem; }
    .role-card {
        border: 2px solid #e5e7eb; border-radius: .625rem;
        padding: .75rem .5rem; text-align: center; cursor: pointer;
        transition: all .18s ease; background: #fafafa;
        display: flex; flex-direction: column; align-items: center; gap: .35rem;
    }
    .role-card:hover  { border-color: #9ca3af; background: #f3f4f6; }
    .role-card.selected { border-color: #1f2937; background: #f8fafc; box-shadow: 0 0 0 3px rgba(31,41,55,.08); }
    .role-card .rc-icon { font-size: 1.375rem; line-height: 1; }
    .role-card .rc-name { font-size: .7rem; font-weight: 600; color: #374151; }

    /* ── Pagination ── */
    .pagination-wrap {
        display: flex; align-items: center;
        justify-content: space-between;
        padding: .875rem 1.25rem;
        border-top: 1px solid #f3f4f6;
        font-size: .8rem; color: #6b7280;
        flex-wrap: wrap; gap: .5rem;
    }
    .page-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: .375rem; font-size: .8125rem;
        color: #6b7280; border: 1px solid transparent; transition: all .15s; cursor: pointer;
    }
    .page-btn:hover { background: #f3f4f6; color: #1f2937; }
    .page-btn.active { background: #1f2937; color: #fff; border-color: #1f2937; }

    /* ── Empty state ── */
    .empty-state {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 4rem 1rem; color: #9ca3af; text-align: center;
    }
    .empty-state svg { width: 3rem; height: 3rem; opacity: .25; margin-bottom: 1rem; }

    /* ── Info view rows ── */
    .info-row {
        display: flex; justify-content: space-between;
        padding: .6rem 0; border-bottom: 1px solid #f9fafb;
        font-size: .8125rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .lbl { color: #9ca3af; font-size: .78rem; }
    .info-row .val { font-weight: 500; color: #1f2937; text-align: right; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- ═══════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Personnel administratif</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $institution->name }} — Gestion du staff
            </p>
        </div>
        <button onclick="openModal('createModal')" class="btn-primary shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau membre
        </button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
        <button class="ml-auto" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
        <button class="ml-auto" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif
    @if($errors->any())
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
    </div>
    @endif

    {{-- ═══════════════════════════════════
         STATS
    ═══════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @php
        $statCards = [
            ['label'=>'Total staff',    'val'=>$stats['total'],       'bg'=>'bg-gray-100',   'fg'=>'text-gray-600',   'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label'=>'Administrateurs','val'=>$stats['admin'],       'bg'=>'bg-violet-50',  'fg'=>'text-violet-600', 'icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label'=>'Comptables',     'val'=>$stats['comptable'],   'bg'=>'bg-amber-50',   'fg'=>'text-amber-600',  'icon'=>'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ['label'=>'Surveillants',   'val'=>$stats['surveillant'], 'bg'=>'bg-sky-50',     'fg'=>'text-sky-600',    'icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
            ['label'=>'Directeurs',     'val'=>$stats['directeur'],   'bg'=>'bg-green-50',   'fg'=>'text-green-600',  'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ];
        @endphp
        @foreach($statCards as $sc)
        <div class="stat-card">
            <div class="stat-icon {{ $sc['bg'] }}">
                <svg class="w-5 h-5 {{ $sc['fg'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $sc['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-900 leading-none">{{ number_format($sc['val']) }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $sc['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════
         FILTER BAR
    ═══════════════════════════════════ --}}
    <div class="filter-bar">
        {{-- Tabs rôles --}}
        <div class="pill-tabs">
            @php
            $roleTabs = [
                'all'         => ['label'=>'Tous',          'count'=>$stats['total']],
                'admin'       => ['label'=>'Administrateurs','count'=>$stats['admin']],
                'comptable'   => ['label'=>'Comptables',    'count'=>$stats['comptable']],
                'surveillant' => ['label'=>'Surveillants',  'count'=>$stats['surveillant']],
                'directeur'   => ['label'=>'Directeurs',    'count'=>$stats['directeur']],
            ];
            @endphp
            @foreach($roleTabs as $rk => $rv)
            <button class="pill-tab {{ $rk === 'all' ? 'active' : '' }}"
                    data-role="{{ $rk }}" onclick="filterRole('{{ $rk }}')">
                {{ $rv['label'] }}
                <span class="chip">{{ $rv['count'] }}</span>
            </button>
            @endforeach
        </div>

        {{-- Recherche --}}
        <div class="search-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="searchInput" class="filter-input"
                   placeholder="Nom, prénom, matricule, poste…"
                   oninput="applyFilters()">
        </div>

        {{-- Unité administrative --}}
        <select id="filterUnit" class="filter-input" onchange="applyFilters()">
            <option value="">Toutes les unités</option>
            @foreach($administrativeUnits as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>

        {{-- Statut --}}
        <select id="filterStatus" class="filter-input" onchange="applyFilters()">
            <option value="">Tous statuts</option>
            <option value="active">Actif</option>
            <option value="inactive">Inactif</option>
            <option value="blocked">Bloqué</option>
        </select>
    </div>

    {{-- ═══════════════════════════════════
         TABLE
    ═══════════════════════════════════ --}}
    <div class="staff-table-wrap">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>Membre</th>
                    <th>Rôle</th>
                    <th>Poste / Unité</th>
                    <th>Contact</th>
                    <th>Compte</th>
                    <th>Statut</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($staffMembers as $s)
            @php
                $colors = ['bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-sky-100 text-sky-700','bg-green-100 text-green-700','bg-rose-100 text-rose-700'];
                $col    = $colors[$s->id % 5];
                $initials = strtoupper(substr($s->prenom,0,1).substr($s->nom,0,1));

                $roleName  = $s->user?->roles->pluck('name')->first() ?? 'admin';
                $roleBadge = match($roleName) {
                    'admin'       => ['badge-admin',      'Administrateur'],
                    'comptable'   => ['badge-comptable',  'Comptable'],
                    'surveillant' => ['badge-surveillant','Surveillant'],
                    'directeur'   => ['badge-directeur',  'Directeur'],
                    default       => ['badge-admin',      ucfirst($roleName)],
                };

                $statusBadge = match($s->user?->status ?? 'active') {
                    'active'   => ['badge-active',  'Actif'],
                    'inactive' => ['badge-inactive','Inactif'],
                    'blocked'  => ['badge-blocked', 'Bloqué'],
                    default    => ['badge-inactive','—'],
                };
            @endphp
            <tr class="staff-row"
                data-role="{{ $roleName }}"
                data-status="{{ $s->user?->status ?? 'active' }}"
                data-unit="{{ $s->administrative_unit_id ?? '' }}"
                data-search="{{ strtolower($s->nom.' '.$s->prenom.' '.($s->matricule??'').' '.($s->poste??'')) }}">

                <td>
                    <div class="flex items-center gap-2.5">
                        <div class="avatar {{ $col }}">{{ $initials }}</div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $s->prenom }} {{ $s->nom }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $s->matricule ?? '—' }}</p>
                        </div>
                    </div>
                </td>

                <td>
                    <span class="badge {{ $roleBadge[0] }}">{{ $roleBadge[1] }}</span>
                </td>

                <td>
                    <p class="text-sm text-gray-700">{{ $s->poste ?? '—' }}</p>
                    @if($s->administrativeUnit)
                    <p class="text-xs text-gray-400">{{ $s->administrativeUnit->name }}</p>
                    @endif
                </td>

                <td>
                    <p class="text-sm text-gray-500">{{ $s->telephone ?? '—' }}</p>
                    @if($s->user)
                    <p class="text-xs text-gray-400">{{ $s->user->email }}</p>
                    @endif
                </td>

                <td>
                    @if($s->user)
                    <span class="text-xs text-gray-500">Compte lié</span>
                    @else
                    <span class="text-xs text-gray-300 italic">Sans compte</span>
                    @endif
                </td>

                <td>
                    <span class="badge {{ $statusBadge[0] }}">
                        <span class="badge-dot"></span>
                        {{ $statusBadge[1] }}
                    </span>
                </td>

                <td>
                    <div class="action-menu" style="float:right">
                        <button onclick="toggleMenu({{ $s->id }})"
                                class="p-1.5 hover:bg-gray-100 rounded-lg transition text-gray-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="5" cy="12" r="1.5"/>
                                <circle cx="12" cy="12" r="1.5"/>
                                <circle cx="19" cy="12" r="1.5"/>
                            </svg>
                        </button>
                        <div class="action-dropdown" id="menu-{{ $s->id }}">

                            {{-- Voir --}}
                            <button class="action-dropdown-item"
                                    onclick='openViewModal(@json($s)); closeAllMenus()'>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Voir le profil
                            </button>

                            {{-- Modifier --}}
                            <button class="action-dropdown-item"
                                    onclick='openEditModal(@json($s)); closeAllMenus()'>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Modifier
                            </button>

                            @if($s->user)
                            {{-- Reset mdp --}}
                            <button class="action-dropdown-item"
                                    onclick="openResetPwd({{ $s->id }}, '{{ addslashes($s->prenom.' '.$s->nom) }}'); closeAllMenus()">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Réinitialiser MDP
                            </button>

                            <div class="action-dropdown-divider"></div>

                            {{-- Status --}}
                            @if(($s->user->status ?? '') === 'active')
                            <button class="action-dropdown-item"
                                    onclick="confirmStatus('{{ route('admin.staff.status', $s->id) }}','inactive','Désactiver {{ addslashes($s->prenom.' '.$s->nom) }} ?','Cet utilisateur ne pourra plus se connecter.','Désactiver'); closeAllMenus()">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6"/>
                                </svg>
                                Désactiver
                            </button>
                            @else
                            <button class="action-dropdown-item"
                                    onclick="confirmStatus('{{ route('admin.staff.status', $s->id) }}','active','Activer {{ addslashes($s->prenom.' '.$s->nom) }} ?','Cet utilisateur pourra se reconnecter.','Activer'); closeAllMenus()">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Activer
                            </button>
                            @endif

                            @if(($s->user->status ?? '') !== 'blocked')
                            <button class="action-dropdown-item danger"
                                    onclick="confirmStatus('{{ route('admin.staff.status', $s->id) }}','blocked','Bloquer {{ addslashes($s->prenom.' '.$s->nom) }} ?','L\'accès sera immédiatement révoqué.','Bloquer', true); closeAllMenus()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                                </svg>
                                Bloquer
                            </button>
                            @endif
                            @endif

                            <div class="action-dropdown-divider"></div>

                            {{-- Supprimer --}}
                            <button class="action-dropdown-item danger"
                                    onclick="openDeleteModal({{ $s->id }}, '{{ addslashes($s->prenom.' '.$s->nom) }}'); closeAllMenus()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm font-medium">Aucun membre du personnel enregistré</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($staffMembers->hasPages())
        <div class="pagination-wrap">
            <span>Affichage de {{ $staffMembers->firstItem() }} à {{ $staffMembers->lastItem() }} sur {{ $staffMembers->total() }}</span>
            <div class="flex items-center gap-1">
                <a href="{{ $staffMembers->previousPageUrl() }}"
                   class="page-btn {{ !$staffMembers->onFirstPage() ? '' : 'opacity-30 pointer-events-none' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                @foreach($staffMembers->getUrlRange(1, $staffMembers->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-btn {{ $page === $staffMembers->currentPage() ? 'active' : '' }}">
                    {{ $page }}
                </a>
                @endforeach
                <a href="{{ $staffMembers->nextPageUrl() }}"
                   class="page-btn {{ $staffMembers->hasMorePages() ? '' : 'opacity-30 pointer-events-none' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
        @else
        <div class="pagination-wrap">
            <span>{{ $staffMembers->total() }} membre(s) au total</span>
        </div>
        @endif
    </div>

</div>{{-- /space-y-5 --}}


{{-- ══════════════════════════════════════════════════════════
     MODAL — CRÉER UN MEMBRE DU STAFF
══════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="createModal">
<div class="modal-box">
    <div class="modal-header">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Nouveau membre du personnel</h2>
            <p class="text-xs text-gray-400 mt-0.5">Rattaché à <strong>{{ $institution->name }}</strong></p>
        </div>
        <button onclick="closeModal('createModal')" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form action="{{ route('admin.staff.store') }}" method="POST">
        @csrf
        <div class="modal-body space-y-5">

            {{-- 1. Rôle --}}
            <div>
                <p class="form-label mb-2">Rôle / Fonction <span class="text-red-500">*</span></p>
                <div class="role-selector-grid">
                    @foreach(['admin'=>['⚙️','Administrateur'],'comptable'=>['💼','Comptable'],'surveillant'=>['👁️','Surveillant'],'directeur'=>['🏫','Directeur']] as $rk=>$rv)
                    <div class="role-card" data-role="{{ $rk }}" onclick="selectRole(this,'{{ $rk }}')">
                        <span class="rc-icon">{{ $rv[0] }}</span>
                        <span class="rc-name">{{ $rv[1] }}</span>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="role" id="cRoleInput" required>
            </div>

            {{-- 2. Identité --}}
            <div>
                <div class="sec-sep"><span>Identité</span></div>
                <div class="g3 mb-3">
                    <div>
                        <label class="form-label">Matricule</label>
                        <input type="text" name="matricule" class="form-input" placeholder="STF-001">
                    </div>
                    <div>
                        <label class="form-label">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" class="form-input" placeholder="MBEMBA" required>
                    </div>
                    <div>
                        <label class="form-label">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" class="form-input" placeholder="Paul" required>
                    </div>
                </div>
                <div class="g2">
                    <div>
                        <label class="form-label">Téléphone</label>
                        <input type="tel" name="telephone" class="form-input" placeholder="+242 06…">
                    </div>
                    <div>
                        <label class="form-label">Poste / Fonction</label>
                        <input type="text" name="poste" class="form-input" placeholder="Secrétaire général">
                    </div>
                </div>
            </div>

            {{-- 3. Unité administrative --}}
            <div>
                <div class="sec-sep"><span>Rattachement</span></div>
                <div>
                    <label class="form-label">Unité administrative</label>
                    <select name="administrative_unit_id" class="form-input">
                        <option value="">— Laisser vide pour créer automatiquement —</option>
                        @foreach($administrativeUnits as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    <p class="form-hint">Une unité « Direction » sera créée si aucune n'est sélectionnée.</p>
                </div>
            </div>

            {{-- 4. Compte d'accès --}}
            <div>
                <div class="sec-sep"><span>Compte d'accès <span class="text-gray-400 font-normal normal-case">(optionnel)</span></span></div>
                <div class="g2 mb-3">
                    <div>
                        <label class="form-label">Nom d'affichage</label>
                        <input type="text" name="name" class="form-input" placeholder="Paul MBEMBA">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="paul@ecole.cg">
                    </div>
                </div>
                <div class="g2">
                    <div>
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-input" placeholder="Min. 8 caractères" minlength="8">
                    </div>
                    <div>
                        <label class="form-label">Confirmation</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Répéter">
                    </div>
                </div>
                <p class="form-hint mt-2">Si l'email est renseigné, un compte utilisateur sera créé avec le rôle sélectionné.</p>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('createModal')" class="btn-secondary">Annuler</button>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </form>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL — MODIFIER
══════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="editModal">
<div class="modal-box">
    <div class="modal-header">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Modifier le membre</h2>
            <p class="text-xs text-gray-400 mt-0.5" id="editSubtitle">—</p>
        </div>
        <button onclick="closeModal('editModal')" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form id="editForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-body space-y-5">

            {{-- Rôle en lecture seule --}}
            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl p-3">
                <span class="text-2xl" id="eRoleIcon">⚙️</span>
                <div>
                    <p class="text-xs text-gray-400">Rôle</p>
                    <p class="text-sm font-semibold text-gray-900" id="eRoleLabel">—</p>
                </div>
            </div>

            {{-- Identité --}}
            <div>
                <div class="sec-sep"><span>Identité</span></div>
                <div class="g3 mb-3">
                    <div>
                        <label class="form-label">Matricule</label>
                        <input type="text" name="matricule" id="e_matricule" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="e_nom" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" id="e_prenom" class="form-input" required>
                    </div>
                </div>
                <div class="g2">
                    <div>
                        <label class="form-label">Téléphone</label>
                        <input type="tel" name="telephone" id="e_telephone" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Poste / Fonction</label>
                        <input type="text" name="poste" id="e_poste" class="form-input">
                    </div>
                </div>
            </div>

            {{-- Rattachement --}}
            <div>
                <div class="sec-sep"><span>Rattachement</span></div>
                <div>
                    <label class="form-label">Unité administrative</label>
                    <select name="administrative_unit_id" id="e_unit" class="form-input">
                        <option value="">— Choisir —</option>
                        @foreach($administrativeUnits as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Compte --}}
            <div>
                <div class="sec-sep"><span>Compte d'accès</span></div>
                <div class="g2">
                    <div>
                        <label class="form-label">Nom d'affichage</label>
                        <input type="text" name="name" id="e_name" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="e_email" class="form-input">
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('editModal')" class="btn-secondary">Annuler</button>
            <button type="submit" class="btn-primary">Mettre à jour</button>
        </div>
    </form>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL — VOIR PROFIL
══════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="viewModal">
<div class="modal-box sm">
    <div class="modal-header">
        <div class="flex items-center gap-3">
            <div class="avatar w-10 h-10 text-sm bg-violet-100 text-violet-700" id="viewAvatar">--</div>
            <div>
                <h2 class="text-base font-semibold text-gray-900" id="viewName">—</h2>
                <p class="text-xs text-gray-400" id="viewRole">—</p>
            </div>
        </div>
        <button onclick="closeModal('viewModal')" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <div id="viewRows" class="divide-y divide-gray-50"></div>
    </div>
    <div class="modal-footer">
        <button type="button" onclick="closeModal('viewModal')" class="btn-secondary">Fermer</button>
    </div>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL — RESET MDP
══════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="resetPwdModal">
<div class="modal-box sm">
    <div class="modal-header">
        <h2 class="text-base font-semibold text-gray-900">Réinitialiser le mot de passe</h2>
        <button onclick="closeModal('resetPwdModal')" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <form id="resetPwdForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-body space-y-4">
            <p class="text-sm text-gray-500">
                Nouveau mot de passe pour <strong id="resetPwdName" class="text-gray-800">—</strong>
            </p>
            <div>
                <label class="form-label">Nouveau mot de passe <span class="text-red-500">*</span></label>
                <input type="password" name="password" class="form-input" required minlength="8" placeholder="Min. 8 caractères">
            </div>
            <div>
                <label class="form-label">Confirmation <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" class="form-input" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('resetPwdModal')" class="btn-secondary">Annuler</button>
            <button type="submit" class="btn-primary">Réinitialiser</button>
        </div>
    </form>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL — CONFIRM STATUS / DELETE
══════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="confirmModal">
<div class="modal-box sm">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0" id="confirmIcon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900" id="confirmTitle">Confirmer</h2>
                <p class="text-sm text-gray-500 mt-1" id="confirmDesc">—</p>
            </div>
        </div>
        <form id="confirmForm" method="POST" class="mt-5">
            @csrf
            <input type="hidden" name="_method" id="confirmMethod" value="PATCH">
            <input type="hidden" name="status" id="confirmStatus" value="">
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModal('confirmModal')" class="btn-secondary">Annuler</button>
                <button type="submit" id="confirmBtn" class="btn-danger">Confirmer</button>
            </div>
        </form>
    </div>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL — SUPPRIMER
══════════════════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="deleteModal">
<div class="modal-box sm">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Supprimer <span id="deleteStaffName" class="text-red-600">—</span> ?
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Le profil Staff et le compte utilisateur associé seront définitivement supprimés.
                </p>
            </div>
        </div>
        <form id="deleteForm" method="POST" class="mt-5">
            @csrf @method('DELETE')
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModal('deleteModal')" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-danger">Supprimer définitivement</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection


@push('scripts')
<script>
// ══════════════════════════════════════════
//  MODAL HELPERS
// ══════════════════════════════════════════
function openModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
document.querySelectorAll('.modal-backdrop').forEach(el =>
    el.addEventListener('click', e => { if(e.target===el) closeModal(el.id); })
);
document.addEventListener('keydown', e => {
    if(e.key==='Escape') document.querySelectorAll('.modal-backdrop.open').forEach(m => closeModal(m.id));
});

// ══════════════════════════════════════════
//  ROLE SELECTOR (create modal)
// ══════════════════════════════════════════
const ROLE_LABELS = { admin:'Administrateur', comptable:'Comptable', surveillant:'Surveillant', directeur:'Directeur' };
const ROLE_ICONS  = { admin:'⚙️', comptable:'💼', surveillant:'👁️', directeur:'🏫' };

function selectRole(card, role) {
    card.closest('.role-selector-grid').querySelectorAll('.role-card')
        .forEach(c => c.classList.toggle('selected', c === card));
    document.getElementById('cRoleInput').value = role;
}

// ══════════════════════════════════════════
//  EDIT MODAL
// ══════════════════════════════════════════
function openEditModal(s) {
    const roleName = s.user?.roles?.[0]?.name ?? 'admin';
    document.getElementById('editForm').action = `/admin/staff/${s.id}`;
    document.getElementById('editSubtitle').textContent = (s.prenom||'') + ' ' + (s.nom||'');
    document.getElementById('eRoleIcon').textContent  = ROLE_ICONS[roleName]  ?? '⚙️';
    document.getElementById('eRoleLabel').textContent = ROLE_LABELS[roleName] ?? roleName;

    document.getElementById('e_matricule').value = s.matricule  || '';
    document.getElementById('e_nom').value        = s.nom        || '';
    document.getElementById('e_prenom').value     = s.prenom     || '';
    document.getElementById('e_telephone').value  = s.telephone  || '';
    document.getElementById('e_poste').value      = s.poste      || '';
    document.getElementById('e_unit').value       = s.administrative_unit_id || '';
    document.getElementById('e_name').value       = s.user?.name  || '';
    document.getElementById('e_email').value      = s.user?.email || '';

    openModal('editModal');
}

// ══════════════════════════════════════════
//  VIEW MODAL
// ══════════════════════════════════════════
function openViewModal(s) {
    const initials = ((s.prenom||'').charAt(0)+(s.nom||'').charAt(0)).toUpperCase();
    const roleName = s.user?.roles?.[0]?.name ?? 'admin';
    document.getElementById('viewAvatar').textContent = initials;
    document.getElementById('viewName').textContent   = (s.prenom||'') + ' ' + (s.nom||'');
    document.getElementById('viewRole').textContent   = ROLE_LABELS[roleName] ?? roleName;

    const rows = [
        ['Matricule',   s.matricule || '—'],
        ['Poste',       s.poste || '—'],
        ['Téléphone',   s.telephone || '—'],
        ['Email',       s.user?.email || '—'],
        ['Statut',      s.user?.status === 'active' ? '✅ Actif' : '⏸ Inactif'],
    ];
    document.getElementById('viewRows').innerHTML = rows.map(([l, v]) =>
        `<div class="info-row"><span class="lbl">${l}</span><span class="val">${v}</span></div>`
    ).join('');
    openModal('viewModal');
}

// ══════════════════════════════════════════
//  RESET PASSWORD
// ══════════════════════════════════════════
function openResetPwd(id, name) {
    document.getElementById('resetPwdForm').action = `/admin/staff/${id}/reset-password`;
    document.getElementById('resetPwdName').textContent = name;
    openModal('resetPwdModal');
}

// ══════════════════════════════════════════
//  CONFIRM STATUS
// ══════════════════════════════════════════
function confirmStatus(url, statusVal, title, desc, btnLabel, isDanger=false) {
    document.getElementById('confirmForm').action   = url;
    document.getElementById('confirmMethod').value  = 'PATCH';
    document.getElementById('confirmStatus').value  = statusVal;
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmDesc').textContent  = desc;
    document.getElementById('confirmBtn').textContent   = btnLabel;
    const icon = document.getElementById('confirmIcon');
    icon.className = (isDanger ? 'w-11 h-11 bg-red-100' : 'w-11 h-11 bg-amber-100')
                   + ' rounded-full flex items-center justify-center shrink-0';
    document.getElementById('confirmBtn').className = isDanger
        ? 'btn-danger'
        : 'btn-primary';
    openModal('confirmModal');
}

// ══════════════════════════════════════════
//  DELETE
// ══════════════════════════════════════════
function openDeleteModal(id, name) {
    document.getElementById('deleteForm').action = `/admin/staff/${id}`;
    document.getElementById('deleteStaffName').textContent = name;
    openModal('deleteModal');
}

// ══════════════════════════════════════════
//  ACTION MENUS
// ══════════════════════════════════════════
function toggleMenu(id) { closeAllMenus(id); document.getElementById(`menu-${id}`).classList.toggle('open'); }
function closeAllMenus(skip=null) {
    document.querySelectorAll('.action-dropdown.open').forEach(m => {
        if(!skip || m.id !== `menu-${skip}`) m.classList.remove('open');
    });
}
document.addEventListener('click', e => { if(!e.target.closest('.action-menu')) closeAllMenus(); });

// ══════════════════════════════════════════
//  FILTERS
// ══════════════════════════════════════════
function filterRole(role) {
    document.querySelectorAll('.pill-tab').forEach(t => t.classList.toggle('active', t.dataset.role === role));
    applyFilters();
}
function applyFilters() {
    const role   = document.querySelector('.pill-tab.active')?.dataset.role || 'all';
    const q      = document.getElementById('searchInput').value.toLowerCase();
    const unit   = document.getElementById('filterUnit').value;
    const status = document.getElementById('filterStatus').value;

    document.querySelectorAll('.staff-row').forEach(row => {
        const ok = (role   === 'all' || row.dataset.role   === role)
                && (!q      || row.dataset.search.includes(q))
                && (!unit   || row.dataset.unit   === unit)
                && (!status || row.dataset.status === status);
        row.style.display = ok ? '' : 'none';
    });
}
</script>
@endpush