@extends('admin.master')

@push('styles')
    <style>
        /* ══════════════════════════════════════════
               STAFF PAGE — LIGHT THEME
            ══════════════════════════════════════════ */

        .stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .875rem;
            padding: 1.1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: box-shadow .15s, border-color .15s;
        }

        .stat-card:hover {
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
            border-color: #d1d5db;
        }

        .stat-icon {
            width: 2.6rem;
            height: 2.6rem;
            border-radius: .625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .staff-table-wrap {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .875rem;
            overflow: hidden;
        }

        .staff-table {
            width: 100%;
            border-collapse: collapse;
        }

        .staff-table thead tr {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .staff-table th {
            padding: .75rem 1rem;
            font-size: .75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .04em;
            text-align: left;
            white-space: nowrap;
        }

        .staff-table td {
            padding: .875rem 1rem;
            font-size: .8125rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .staff-table tbody tr:last-child td {
            border-bottom: none;
        }

        .staff-table tbody tr {
            transition: background .1s;
        }

        .staff-table tbody tr:hover {
            background: #fafafa;
        }

        .avatar {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: .5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .75rem;
            letter-spacing: .04em;
            flex-shrink: 0;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .65rem;
            border-radius: 9999px;
            font-size: .72rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-active {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-inactive {
            background: #fef9c3;
            color: #a16207;
        }

        .badge-dot {
            width: .45rem;
            height: .45rem;
            border-radius: 50%;
            background: currentColor;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem 1rem;
            background: #1f2937;
            color: #fff;
            border: none;
            border-radius: .5rem;
            font-size: .8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, transform .1s;
        }

        .btn-primary:hover {
            background: #374151;
        }

        .btn-primary:active {
            transform: scale(.97);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem 1rem;
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            font-size: .8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-secondary:hover {
            background: #f9fafb;
        }

        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem 1rem;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: .5rem;
            font-size: .8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .action-menu {
            position: relative;
        }

        .action-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: .25rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
            min-width: 190px;
            z-index: 50;
            display: none;
            overflow: hidden;
        }

        .action-dropdown.open {
            display: block;
        }

        .action-dropdown-item {
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: .625rem 1rem;
            font-size: .8125rem;
            color: #374151;
            cursor: pointer;
            transition: background .15s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .action-dropdown-item:hover {
            background: #f3f4f6;
        }

        .action-dropdown-item.danger {
            color: #dc2626;
        }

        .action-dropdown-item.danger:hover {
            background: #fef2f2;
        }

        .action-dropdown-divider {
            height: 1px;
            background: #f3f4f6;
        }

        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .875rem;
            padding: .875rem 1.25rem;
        }

        .filter-input {
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: .5rem .875rem;
            font-size: .8125rem;
            color: #374151;
            background: #f9fafb;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #9ca3af;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(156, 163, 175, .15);
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .search-wrap svg {
            position: absolute;
            left: .65rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: .9rem;
            height: .9rem;
            pointer-events: none;
        }

        .search-wrap input {
            padding-left: 2rem;
            width: 100%;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(4px);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 1rem;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .18);
            animation: mIn .2s ease;
        }

        .modal-box.sm {
            max-width: 420px;
        }

        @keyframes mIn {
            from {
                opacity: 0;
                transform: translateY(14px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: .75rem;
            padding: 1rem 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #f3f4f6;
            border-radius: 0 0 1rem 1rem;
            position: sticky;
            bottom: 0;
        }

        .form-label {
            display: block;
            font-size: .8rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: .35rem;
        }

        .form-hint {
            font-size: .75rem;
            color: #9ca3af;
            margin-top: .25rem;
        }

        .form-input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: .6rem .875rem;
            font-size: .875rem;
            color: #1f2937;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156, 163, 175, .15);
        }

        .g2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .g3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: .75rem;
        }

        /* ══════════════════════════════════════════
       RESPONSIVE
    ══════════════════════════════════════════ */
        @media (max-width: 768px) {

            /* Header */
            .flex.flex-col.sm\:flex-row {
                flex-direction: column;
                align-items: stretch;
            }

            .flex.flex-col.sm\:flex-row .btn-primary {
                width: 100%;
                justify-content: center;
            }

            /* Filter bar : colonne */
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
                gap: .6rem;
                padding: .75rem 1rem;
            }

            .search-wrap {
                min-width: unset;
                width: 100%;
            }

            .filter-input {
                width: 100%;
            }

            /* Table → scroll horizontal */
            .staff-table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .staff-table {
                min-width: 580px;
            }

            /* Cacher colonnes secondaires sur très petit écran */
            .staff-table th:nth-child(3),
            .staff-table td:nth-child(3),
            .staff-table th:nth-child(4),
            .staff-table td:nth-child(4) {
                display: none;
            }

            /* Stats */
            .grid.grid-cols-1.sm\:grid-cols-3 {
                grid-template-columns: 1fr;
            }

            /* Pagination */
            .pagination-wrap {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: .75rem;
            }

            /* Modals */
            .modal-backdrop {
                padding: .5rem;
                align-items: flex-end;
            }

            .modal-box {
                max-width: 100%;
                border-radius: 1rem 1rem 0 0;
                max-height: 92vh;
            }

            .modal-box.sm {
                max-width: 100%;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-header {
                padding: 1rem;
            }

            .modal-footer {
                padding: .875rem 1rem;
                flex-direction: column;
                gap: .5rem;
            }

            .modal-footer button {
                width: 100%;
                justify-content: center;
            }

            /* Grilles dans les modals */
            .g2,
            .g3 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .staff-table {
                min-width: 420px;
            }

            .staff-table th:nth-child(5),
            .staff-table td:nth-child(5) {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="space-y-5">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Personnel administratif</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $institution->name }} — Gestion du staff
                </p>
            </div>
            <button onclick="openModal('createModal')" class="btn-primary shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau membre
            </button>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
                <button class="ml-auto" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
                <button class="ml-auto" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
        @if ($errors->any())
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    @foreach ($errors->all() as $e)
                        <p>{{ $e }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- STATS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @php
                $statCards = [
                    [
                        'label' => 'Total staff',
                        'val' => $stats['total'],
                        'bg' => 'bg-gray-100',
                        'fg' => 'text-gray-600',
                        'icon' =>
                            'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    ],
                    [
                        'label' => 'Actifs',
                        'val' => $stats['actifs'],
                        'bg' => 'bg-green-50',
                        'fg' => 'text-green-600',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'label' => 'Inactifs',
                        'val' => $stats['inactifs'],
                        'bg' => 'bg-amber-50',
                        'fg' => 'text-amber-600',
                        'icon' => 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                ];
            @endphp
            @foreach ($statCards as $sc)
                <div class="stat-card">
                    <div class="stat-icon {{ $sc['bg'] }}">
                        <svg class="w-5 h-5 {{ $sc['fg'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="{{ $sc['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900 leading-none">{{ number_format($sc['val']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $sc['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- FILTER BAR --}}
        <div class="filter-bar">
            {{-- Recherche --}}
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="searchInput" class="filter-input" placeholder="Nom, prénom, matricule, poste…"
                    oninput="applyFilters()">
            </div>

            {{-- Unité administrative --}}
            <select id="filterUnit" class="filter-input" onchange="applyFilters()">
                <option value="">Toutes les unités</option>
                @foreach ($administrativeUnits as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>

            {{-- Statut --}}
            <select id="filterStatus" class="filter-input" onchange="applyFilters()">
                <option value="">Tous statuts</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
            </select>
        </div>

        {{-- TABLE --}}
        <div class="staff-table-wrap">
            <table class="staff-table">
                <thead>
                    <tr>
                        <th>Membre</th>
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
                            $colors = [
                                'bg-violet-100 text-violet-700',
                                'bg-amber-100 text-amber-700',
                                'bg-sky-100 text-sky-700',
                                'bg-green-100 text-green-700',
                                'bg-rose-100 text-rose-700',
                            ];
                            $col = $colors[$s->id % 5];
                            $initials = strtoupper(substr($s->prenom ?? '', 0, 1) . substr($s->nom ?? '', 0, 1));

                            $statusBadge = $s->status ? ['badge-active', 'Actif'] : ['badge-inactive', 'Inactif'];
                        @endphp
                        <tr class="staff-row" data-status="{{ $s->status ? '1' : '0' }}"
                            data-unit="{{ $s->administrative_unit_id ?? '' }}"
                            data-search="{{ strtolower(($s->nom ?? '') . ' ' . ($s->prenom ?? '') . ' ' . ($s->matricule ?? '') . ' ' . ($s->poste ?? '')) }}">

                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="avatar {{ $col }}">{{ $initials }}</div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $s->prenom }}
                                            {{ $s->nom }}</p>
                                        <p class="text-xs text-gray-400 font-mono">{{ $s->matricule ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <p class="text-sm text-gray-700">{{ $s->poste ?? '—' }}</p>
                                @if ($s->administrativeUnit)
                                    <p class="text-xs text-gray-400">{{ $s->administrativeUnit->name }}</p>
                                @endif
                            </td>

                            <td>
                                <p class="text-sm text-gray-500">{{ $s->telephone ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $s->email ?? ($s->user?->email ?? '—') }}</p>
                            </td>

                            <td>
                                @if ($s->user_id)
                                    <span class="text-xs font-medium text-indigo-600">Compte lié</span>
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
                                            <circle cx="5" cy="12" r="1.5" />
                                            <circle cx="12" cy="12" r="1.5" />
                                            <circle cx="19" cy="12" r="1.5" />
                                        </svg>
                                    </button>
                                    <div class="action-dropdown" id="menu-{{ $s->id }}">

                                        {{-- Voir --}}
                                        <button class="action-dropdown-item"
                                            onclick='openViewModal(@json($s)); closeAllMenus()'>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Voir le profil
                                        </button>

                                        {{-- Modifier --}}
                                        <button class="action-dropdown-item"
                                            onclick='openEditModal(@json($s)); closeAllMenus()'>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Modifier
                                        </button>

                                        {{-- Reset MDP (seulement si compte lié) --}}
                                        @if ($s->user_id)
                                            <button class="action-dropdown-item"
                                                onclick="openResetPwd({{ $s->id }}, '{{ addslashes($s->prenom . ' ' . $s->nom) }}'); closeAllMenus()">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                </svg>
                                                Réinitialiser MDP
                                            </button>
                                        @endif

                                        <div class="action-dropdown-divider"></div>

                                        {{-- Toggle statut --}}
                                        @if ($s->status)
                                            <button class="action-dropdown-item"
                                                onclick="confirmToggleStatus(
                                        '{{ route('admin.staff.status', $s->id) }}',
                                        0,
                                        'Désactiver {{ addslashes($s->prenom . ' ' . $s->nom) }} ?',
                                        'Ce membre ne sera plus marqué comme actif.',
                                        'Désactiver'
                                    ); closeAllMenus()">
                                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M10 9v6m4-6v6" />
                                                </svg>
                                                Désactiver
                                            </button>
                                        @else
                                            <button class="action-dropdown-item"
                                                onclick="confirmToggleStatus(
                                        '{{ route('admin.staff.status', $s->id) }}',
                                        1,
                                        'Activer {{ addslashes($s->prenom . ' ' . $s->nom) }} ?',
                                        'Ce membre sera de nouveau marqué comme actif.',
                                        'Activer'
                                    ); closeAllMenus()">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Activer
                                            </button>
                                        @endif

                                        <div class="action-dropdown-divider"></div>

                                        {{-- Supprimer --}}
                                        <button class="action-dropdown-item danger"
                                            onclick="openDeleteModal({{ $s->id }}, '{{ addslashes($s->prenom . ' ' . $s->nom) }}'); closeAllMenus()">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-sm font-medium">Aucun membre du personnel enregistré</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($staffMembers->hasPages())
                <div class="pagination-wrap">
                    <span>Affichage de {{ $staffMembers->firstItem() }} à {{ $staffMembers->lastItem() }} sur
                        {{ $staffMembers->total() }}</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ $staffMembers->previousPageUrl() }}"
                            class="page-btn {{ !$staffMembers->onFirstPage() ? '' : 'opacity-30 pointer-events-none' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @foreach ($staffMembers->getUrlRange(1, $staffMembers->lastPage()) as $page => $url)
                            <a href="{{ $url }}"
                                class="page-btn {{ $page === $staffMembers->currentPage() ? 'active' : '' }}">
                                {{ $page }}
                            </a>
                        @endforeach
                        <a href="{{ $staffMembers->nextPageUrl() }}"
                            class="page-btn {{ $staffMembers->hasMorePages() ? '' : 'opacity-30 pointer-events-none' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
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
                <button onclick="closeModal('createModal')"
                    class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.staff.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-5">

                    {{-- Identité --}}
                    <div>
                        <div class="sec-sep"><span>Identité</span></div>
                        <div class="g3 mb-3">
                            <div>
                                <label class="form-label">Matricule <span class="text-gray-400 font-normal">(auto si
                                        vide)</span></label>
                                <input type="text" name="matricule" class="form-input"
                                    placeholder="Laissez vide → généré automatiquement (ex: STF-0001)">
                                <p style="font-size:.75rem;color:#9ca3af;margin-top:.25rem;">
                                    Format auto : STF-XXXX si le champ est vide.
                                </p>
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
                                <input type="text" name="poste" class="form-input"
                                    placeholder="Secrétaire général">
                            </div>
                        </div>
                    </div>

                    {{-- Unité administrative --}}
                    <div>
                        <div class="sec-sep"><span>Rattachement</span></div>
                        <div>
                            <label class="form-label">Unité administrative</label>
                            <select name="administrative_unit_id" class="form-input">
                                <option value="">— Laisser vide pour créer automatiquement —</option>
                                @foreach ($administrativeUnits as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            <p class="form-hint">Une unité « Direction » sera créée si aucune n'est sélectionnée.</p>
                        </div>
                    </div>

                    {{-- Compte d'accès --}}
                    <div>
                        <div class="sec-sep"><span>Compte d'accès <span
                                    class="text-gray-400 font-normal normal-case">(optionnel)</span></span></div>
                        <div class="g2 mb-3">
                            <div>
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input" placeholder="paul@ecole.cg">
                            </div>
                            <div></div>{{-- spacer --}}
                        </div>
                        <div class="g2">
                            <div>
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="password" class="form-input"
                                    placeholder="Min. 8 caractères" minlength="8">
                            </div>
                            <div>
                                <label class="form-label">Confirmation</label>
                                <input type="password" name="password_confirmation" class="form-input"
                                    placeholder="Répéter">
                            </div>
                        </div>
                        <p class="form-hint mt-2">Si l'email est renseigné, un compte utilisateur sera créé
                            automatiquement.</p>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('createModal')" class="btn-secondary">Annuler</button>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
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
                <button onclick="closeModal('editModal')"
                    class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body space-y-5">

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
                                @foreach ($administrativeUnits as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Compte --}}
                    <div>
                        <div class="sec-sep"><span>Compte d'accès</span></div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="e_email" class="form-input">
                            <p class="form-hint">Modifier l'email mettra à jour le compte utilisateur lié si existant.</p>
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
                    <div class="avatar text-sm bg-violet-100 text-violet-700" id="viewAvatar">--</div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900" id="viewName">—</h2>
                        <p class="text-xs text-gray-400" id="viewPoste">—</p>
                    </div>
                </div>
                <button onclick="closeModal('viewModal')"
                    class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                <button onclick="closeModal('resetPwdModal')"
                    class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                        <input type="password" name="password" class="form-input" required minlength="8"
                            placeholder="Min. 8 caractères">
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
     MODAL — CONFIRM TOGGLE STATUS
══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="confirmModal">
        <div class="modal-box sm">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 bg-amber-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900" id="confirmTitle">Confirmer</h2>
                        <p class="text-sm text-gray-500 mt-1" id="confirmDesc">—</p>
                    </div>
                </div>
                <form id="confirmForm" method="POST" class="mt-5">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" id="confirmStatus" value="">
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeModal('confirmModal')"
                            class="btn-secondary">Annuler</button>
                        <button type="submit" id="confirmBtn" class="btn-primary">Confirmer</button>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">
                            Supprimer <span id="deleteStaffName" class="text-red-600">—</span> ?
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Le profil staff et le compte utilisateur associé seront définitivement supprimés. Cette action
                            est irréversible.
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
        function openModal(id) {
            document.getElementById(id).classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.modal-backdrop').forEach(el =>
            el.addEventListener('click', e => {
                if (e.target === el) closeModal(el.id);
            })
        );
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape')
                document.querySelectorAll('.modal-backdrop.open').forEach(m => closeModal(m.id));
        });

        // ══════════════════════════════════════════
        //  EDIT MODAL
        // ══════════════════════════════════════════
        function openEditModal(s) {
            document.getElementById('editForm').action = `/admin/staff/${s.id}`;
            document.getElementById('editSubtitle').textContent = (s.prenom || '') + ' ' + (s.nom || '');

            document.getElementById('e_matricule').value = s.matricule || '';
            document.getElementById('e_nom').value = s.nom || '';
            document.getElementById('e_prenom').value = s.prenom || '';
            document.getElementById('e_telephone').value = s.telephone || '';
            document.getElementById('e_poste').value = s.poste || '';
            document.getElementById('e_unit').value = s.administrative_unit_id || '';
            document.getElementById('e_email').value = s.email || (s.user ? s.user.email : '') || '';

            openModal('editModal');
        }

        // ══════════════════════════════════════════
        //  VIEW MODAL
        // ══════════════════════════════════════════
        function openViewModal(s) {
            const initials = ((s.prenom || '').charAt(0) + (s.nom || '').charAt(0)).toUpperCase();
            document.getElementById('viewAvatar').textContent = initials;
            document.getElementById('viewName').textContent = (s.prenom || '') + ' ' + (s.nom || '');
            document.getElementById('viewPoste').textContent = s.poste || '—';

            const rows = [
                ['Matricule', s.matricule || '—'],
                ['Téléphone', s.telephone || '—'],
                ['Email', s.email || (s.user ? s.user.email : '—') || '—'],
                ['Compte', s.user_id ? '✅ Compte lié' : '—'],
                ['Statut', s.status ? '✅ Actif' : '⏸ Inactif'],
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
        //  CONFIRM TOGGLE STATUS
        // ══════════════════════════════════════════
        function confirmToggleStatus(url, statusVal, title, desc, btnLabel) {
            document.getElementById('confirmForm').action = url;
            document.getElementById('confirmStatus').value = statusVal;
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmDesc').textContent = desc;
            document.getElementById('confirmBtn').textContent = btnLabel;
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
        function toggleMenu(id) {
            closeAllMenus(id);
            document.getElementById(`menu-${id}`).classList.toggle('open');
        }

        function closeAllMenus(skip = null) {
            document.querySelectorAll('.action-dropdown.open').forEach(m => {
                if (!skip || m.id !== `menu-${skip}`) m.classList.remove('open');
            });
        }
        document.addEventListener('click', e => {
            if (!e.target.closest('.action-menu')) closeAllMenus();
        });

        // ══════════════════════════════════════════
        //  FILTERS
        // ══════════════════════════════════════════
        function applyFilters() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const unit = document.getElementById('filterUnit').value;
            const status = document.getElementById('filterStatus').value;

            document.querySelectorAll('.staff-row').forEach(row => {
                const ok = (!q || row.dataset.search.includes(q)) &&
                    (!unit || row.dataset.unit === unit) &&
                    (!status || row.dataset.status === status);
                row.style.display = ok ? '' : 'none';
            });
        }
    </script>
@endpush
