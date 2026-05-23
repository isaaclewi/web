@extends('admin.master')

@push('styles')
    <style>
        /* ══════════════════════════════════════════
               TEACHERS PAGE — LIGHT THEME
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

        /* ── Pill tabs ── */
        .pill-tabs {
            display: flex;
            gap: .25rem;
            background: #f3f4f6;
            border-radius: .625rem;
            padding: .2rem;
            flex-wrap: wrap;
        }

        .pill-tab {
            padding: .35rem .85rem;
            border-radius: .4rem;
            font-size: .78rem;
            font-weight: 500;
            color: #6b7280;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .pill-tab.active {
            background: #1f2937;
            color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
        }

        .pill-tab .chip {
            font-size: .68rem;
            font-weight: 600;
            background: rgba(255, 255, 255, .2);
            padding: .05rem .4rem;
            border-radius: 9999px;
        }

        .pill-tab:not(.active) .chip {
            background: #e5e7eb;
            color: #6b7280;
        }

        /* ── Filter bar ── */
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

        /* ── Table ── */
        .teacher-table-wrap {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .875rem;
            overflow: hidden;
        }

        .teacher-table {
            width: 100%;
            border-collapse: collapse;
        }

        .teacher-table thead tr {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .teacher-table th {
            padding: .75rem 1rem;
            font-size: .75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .04em;
            text-align: left;
            white-space: nowrap;
        }

        .teacher-table td {
            padding: .875rem 1rem;
            font-size: .8125rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .teacher-table tbody tr:last-child td {
            border-bottom: none;
        }

        .teacher-table tbody tr {
            transition: background .1s;
        }

        .teacher-table tbody tr:hover {
            background: #fafafa;
        }

        /* ── Avatar ── */
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

        /* ── Badges ── */
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

        .badge-blocked {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-cdi {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-cdd {
            background: #ede9fe;
            color: #7c3aed;
        }

        .badge-vacataire {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-benevole {
            background: #f0fdf4;
            color: #166534;
        }

        .badge-dot {
            width: .45rem;
            height: .45rem;
            border-radius: 50%;
            background: currentColor;
        }

        /* ── Contrat tag ── */
        .contrat-tag {
            display: inline-flex;
            align-items: center;
            padding: .15rem .55rem;
            border-radius: .375rem;
            font-size: .7rem;
            font-weight: 600;
            white-space: nowrap;
        }

        /* ── Buttons ── */
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

        /* ── Action menu ── */
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

        /* ── Modal ── */
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
            max-width: 640px;
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

        /* ── Form ── */
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

            /* Page header */
            .flex.flex-col.sm\:flex-row {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-primary.shrink-0 {
                width: 100%;
                justify-content: center;
            }

            /* Stats : 2 colonnes (déjà grid-cols-2 via Tailwind) */
            .grid.grid-cols-2.sm\:grid-cols-3 {
                grid-template-columns: 1fr 1fr;
            }

            /* Filter bar */
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
                padding: .75rem 1rem;
                gap: .6rem;
            }

            /* Pill tabs : scroll horizontal */
            .pill-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                scrollbar-width: none;
                padding-bottom: .1rem;
            }

            .pill-tabs::-webkit-scrollbar {
                display: none;
            }

            /* Search + select pleine largeur */
            .search-wrap {
                min-width: unset;
                width: 100%;
            }

            .filter-input {
                width: 100%;
            }

            /* Table → scroll horizontal */
            .teacher-table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .teacher-table {
                min-width: 620px;
            }

            /* Cacher colonnes secondaires */
            .teacher-table th:nth-child(4),
            .teacher-table td:nth-child(4),
            .teacher-table th:nth-child(5),
            .teacher-table td:nth-child(5) {
                display: none;
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

            .modal-box,
            .modal-box.sm {
                max-width: 100% !important;
                border-radius: 1rem 1rem 0 0;
                max-height: 92vh;
            }

            .modal-header {
                padding: 1rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                flex-direction: column;
                gap: .5rem;
                padding: .875rem 1rem;
            }

            .modal-footer button {
                width: 100%;
                justify-content: center;
            }

            /* Confirm/delete modals (pas de modal-footer) */
            #confirmModal .flex.gap-3.justify-end,
            #deleteModal .flex.gap-3.justify-end {
                flex-direction: column;
                gap: .5rem;
            }

            #confirmModal .flex.gap-3.justify-end button,
            #deleteModal .flex.gap-3.justify-end button {
                width: 100%;
                justify-content: center;
            }

            /* Grilles dans modals */
            .g2,
            .g3 {
                grid-template-columns: 1fr;
            }

            /* Check grid : 2 colonnes fixes */
            .check-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {

            /* Stats : 1 colonne */
            .grid.grid-cols-2.sm\:grid-cols-3 {
                grid-template-columns: 1fr;
            }

            /* Cacher encore la colonne Contrat */
            .teacher-table th:nth-child(3),
            .teacher-table td:nth-child(3) {
                display: none;
            }

            .teacher-table {
                min-width: 380px;
            }

            /* Check grid : 1 colonne */
            .check-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="space-y-5">

        {{-- ═══════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════ --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Enseignants</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $institution->name }}</p>
            </div>
            <button onclick="openModal('createModal')" class="btn-primary shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvel enseignant
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

        {{-- ═══════════════════════════════════
         STATS
    ═══════════════════════════════════ --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
                $statCards = [
                    [
                        'label' => 'Total',
                        'val' => $stats['total'],
                        'bg' => 'bg-gray-100',
                        'fg' => 'text-gray-600',
                        'icon' =>
                            'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                    ],
                    [
                        'label' => 'Actifs',
                        'val' => $stats['active'],
                        'bg' => 'bg-green-50',
                        'fg' => 'text-green-600',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'label' => 'Hommes',
                        'val' => $stats['hommes'],
                        'bg' => 'bg-blue-50',
                        'fg' => 'text-blue-600',
                        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    ],
                    [
                        'label' => 'Femmes',
                        'val' => $stats['femmes'],
                        'bg' => 'bg-pink-50',
                        'fg' => 'text-pink-600',
                        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    ],
                    [
                        'label' => 'CDI',
                        'val' => $stats['cdi'],
                        'bg' => 'bg-indigo-50',
                        'fg' => 'text-indigo-600',
                        'icon' =>
                            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    ],
                    [
                        'label' => 'Vacataires',
                        'val' => $stats['vacataire'],
                        'bg' => 'bg-amber-50',
                        'fg' => 'text-amber-600',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
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

        {{-- ═══════════════════════════════════
         FILTER BAR
    ═══════════════════════════════════ --}}
        <div class="filter-bar">
            {{-- Contrat tabs --}}
            <div class="pill-tabs">
                @php
                    $contratTabs = [
                        'all' => 'Tous',
                        'CDI' => 'CDI',
                        'CDD' => 'CDD',
                        'vacataire' => 'Vacataire',
                        'benevole' => 'Bénévole',
                    ];
                @endphp
                @foreach ($contratTabs as $ck => $cl)
                    <button class="pill-tab {{ $ck === 'all' ? 'active' : '' }}" data-contrat="{{ $ck }}"
                        onclick="filterContrat('{{ $ck }}')">
                        {{ $cl }}
                    </button>
                @endforeach
            </div>

            {{-- Recherche --}}
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="searchInput" class="filter-input"
                    placeholder="Nom, prénom, matricule, spécialité…" oninput="applyFilters()">
            </div>

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
        <div class="teacher-table-wrap">
            <table class="teacher-table">
                <thead>
                    <tr>
                        <th>Enseignant</th>
                        <th>Spécialité</th>
                        <th>Contrat</th>
                        <th>Classes assignées</th>
                        <th>Contact</th>
                        <th>Statut</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $t)
                        @php
                            $colors = [
                                'bg-blue-100 text-blue-700',
                                'bg-teal-100 text-teal-700',
                                'bg-violet-100 text-violet-700',
                                'bg-rose-100 text-rose-700',
                                'bg-amber-100 text-amber-700',
                            ];
                            $col = $colors[$t->id % 5];
                            $initials = strtoupper(substr($t->prenom, 0, 1) . substr($t->nom, 0, 1));
                            $contratBadge = match ($t->type_contrat) {
                                'CDI' => 'badge-cdi',
                                'CDD' => 'badge-cdd',
                                'vacataire' => 'badge-vacataire',
                                'benevole' => 'badge-benevole',
                                default => '',
                            };
                            $statusBadge = match ($t->user?->status ?? 'active') {
                                'active' => ['badge-active', 'Actif'],
                                'inactive' => ['badge-inactive', 'Inactif'],
                                'blocked' => ['badge-blocked', 'Bloqué'],
                                default => ['', '—'],
                            };
                        @endphp
                        <tr class="teacher-row" data-contrat="{{ $t->type_contrat ?? '' }}"
                            data-status="{{ $t->user?->status ?? 'active' }}"
                            data-search="{{ strtolower($t->nom . ' ' . $t->prenom . ' ' . ($t->matricule ?? '') . ' ' . ($t->specialite ?? '')) }}">

                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="avatar {{ $col }}">{{ $initials }}</div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $t->prenom }}
                                            {{ $t->nom }}</p>
                                        <p class="text-xs text-gray-400 font-mono">{{ $t->matricule ?? '—' }}</p>
                                        @if ($t->user)
                                            <p class="text-xs text-gray-400">{{ $t->user->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="text-sm text-gray-600">{{ $t->specialite ?? '—' }}</td>

                            <td>
                                @if ($t->type_contrat)
                                    <span class="badge {{ $contratBadge }}">{{ ucfirst($t->type_contrat) }}</span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>

                            <td>
                                <div class="pill-list">
                                    @forelse($t->classes->take(3) as $c)
                                        <span class="pill">{{ $c->name }}</span>
                                    @empty
                                        <span class="text-xs text-gray-300 italic">Non assigné</span>
                                    @endforelse
                                    @if ($t->classes->count() > 3)
                                        <span
                                            class="pill bg-gray-200 text-gray-500">+{{ $t->classes->count() - 3 }}</span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <p class="text-sm text-gray-500">{{ $t->telephone ?? '—' }}</p>
                            </td>

                            <td>
                                <span class="badge {{ $statusBadge[0] }}">
                                    <span class="badge-dot"></span>
                                    {{ $statusBadge[1] }}
                                </span>
                            </td>

                            <td>
                                <div class="action-menu" style="float:right">
                                    <button onclick="toggleMenu({{ $t->id }})"
                                        class="p-1.5 hover:bg-gray-100 rounded-lg transition text-gray-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="5" cy="12" r="1.5" />
                                            <circle cx="12" cy="12" r="1.5" />
                                            <circle cx="19" cy="12" r="1.5" />
                                        </svg>
                                    </button>
                                    <div class="action-dropdown" id="menu-{{ $t->id }}">
                                        <button class="action-dropdown-item"
                                            onclick='openViewModal(@json($t->load(['classes', 'niveaux', 'filieres'])->toArray()); closeAllMenus()'>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Voir le profil
                                        </button>
                                        <button class="action-dropdown-item"
                                            onclick='openEditModal(@json($t)); closeAllMenus()'>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Modifier
                                        </button>
                                        @if ($t->user)
                                            <button class="action-dropdown-item"
                                                onclick="openResetPwd({{ $t->id }}, '{{ addslashes($t->prenom . ' ' . $t->nom) }}'); closeAllMenus()">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                </svg>
                                                Réinitialiser MDP
                                            </button>
                                            <div class="action-dropdown-divider"></div>
                                            @if (($t->user->status ?? '') === 'active')
                                                <button class="action-dropdown-item"
                                                    onclick="confirmStatus('{{ route('admin.teachers.status', $t->id) }}','inactive','Désactiver {{ addslashes($t->prenom . ' ' . $t->nom) }} ?','Cet enseignant ne pourra plus se connecter.','Désactiver'); closeAllMenus()">
                                                    <svg class="w-4 h-4 text-amber-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M10 9v6m4-6v6" />
                                                    </svg>
                                                    Désactiver
                                                </button>
                                            @else
                                                <button class="action-dropdown-item"
                                                    onclick="confirmStatus('{{ route('admin.teachers.status', $t->id) }}','active','Activer {{ addslashes($t->prenom . ' ' . $t->nom) }} ?','Cet enseignant pourra se reconnecter.','Activer'); closeAllMenus()">
                                                    <svg class="w-4 h-4 text-green-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Activer
                                                </button>
                                            @endif
                                            @if (($t->user->status ?? '') !== 'blocked')
                                                <button class="action-dropdown-item danger"
                                                    onclick="confirmStatus('{{ route('admin.teachers.status', $t->id) }}','blocked','Bloquer {{ addslashes($t->prenom . ' ' . $t->nom) }} ?','L\'accès sera immédiatement révoqué.','Bloquer',true); closeAllMenus()">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                                                    </svg>
                                                    Bloquer
                                                </button>
                                            @endif
                                        @endif
                                        <div class="action-dropdown-divider"></div>
                                        <button class="action-dropdown-item danger"
                                            onclick="openDeleteModal({{ $t->id }}, '{{ addslashes($t->prenom . ' ' . $t->nom) }}'); closeAllMenus()">
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
                            <td colspan="7">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    </svg>
                                    <p class="text-sm font-medium">Aucun enseignant enregistré</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($teachers->hasPages())
                <div class="pagination-wrap">
                    <span>Affichage de {{ $teachers->firstItem() }} à {{ $teachers->lastItem() }} sur
                        {{ $teachers->total() }}</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ $teachers->previousPageUrl() }}"
                            class="page-btn {{ !$teachers->onFirstPage() ? '' : 'opacity-30 pointer-events-none' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @foreach ($teachers->getUrlRange(1, $teachers->lastPage()) as $page => $url)
                            <a href="{{ $url }}"
                                class="page-btn {{ $page === $teachers->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        <a href="{{ $teachers->nextPageUrl() }}"
                            class="page-btn {{ $teachers->hasMorePages() ? '' : 'opacity-30 pointer-events-none' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @else
                <div class="pagination-wrap">
                    <span>{{ $teachers->total() }} enseignant(s) au total</span>
                </div>
            @endif
        </div>

    </div>{{-- /space-y-5 --}}


    {{-- ══════════════════════════════════════════════════════════
     MODAL — CRÉER
══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="createModal">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Nouvel enseignant</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $institution->name }}</p>
                </div>
                <button onclick="closeModal('createModal')"
                    class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.teachers.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-5">

                    {{-- Identité --}}
                    <div>
                        <div class="sec-sep"><span>Identité</span></div>
                        <div class="g3 mb-3">
                            <div>
                                <label class="form-label">Matricule</label>
                                <input type="text" name="matricule" class="form-input"
                                    placeholder="Laissez vide → généré automatiquement (ex: ENS-0001)">
                                <p style="font-size:.75rem;color:#9ca3af;margin-top:.25rem;">
                                    Format auto : ENS-XXXX si le champ est vide.
                                </p>
                            </div>
                            <div>
                                <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                <input type="text" name="nom" class="form-input" placeholder="DUPONT" required>
                            </div>
                            <div>
                                <label class="form-label">Prénom <span class="text-red-500">*</span></label>
                                <input type="text" name="prenom" class="form-input" placeholder="Jean" required>
                            </div>
                        </div>
                        <div class="g2 mb-3">
                            <div>
                                <label class="form-label">Sexe</label>
                                <select name="sexe" class="form-input">
                                    <option value="">—</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="telephone" class="form-input" placeholder="+242 06…">
                            </div>
                        </div>
                        <div class="g2">
                            <div>
                                <label class="form-label">Spécialité</label>
                                <input type="text" name="specialite" class="form-input" placeholder="Mathématiques">
                            </div>
                            <div>
                                <label class="form-label">Type de contrat</label>
                                <select name="type_contrat" class="form-input">
                                    <option value="">—</option>
                                    <option value="CDI">CDI</option>
                                    <option value="CDD">CDD</option>
                                    <option value="vacataire">Vacataire</option>
                                    <option value="benevole">Bénévole</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Date de recrutement</label>
                            <input type="date" name="date_recrutement" class="form-input">
                        </div>
                    </div>

                    {{-- Affectations --}}
                    <div>
                        <div class="sec-sep"><span>Affectations</span></div>

                        @if ($niveaux->count())
                            <div class="mb-3">
                                <label class="form-label mb-1">Niveaux enseignés</label>
                                <div class="check-grid">
                                    @foreach ($niveaux as $n)
                                        <label class="check-item" onclick="this.classList.toggle('checked')">
                                            <input type="checkbox" name="niveaux[]" value="{{ $n->id }}">
                                            {{ $n->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($filieres->count())
                            <div class="mb-3">
                                <label class="form-label mb-1">Filières</label>
                                <div class="check-grid">
                                    @foreach ($filieres as $f)
                                        <label class="check-item" onclick="this.classList.toggle('checked')">
                                            <input type="checkbox" name="filieres[]" value="{{ $f->id }}">
                                            {{ $f->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($classes->count())
                            <div>
                                <label class="form-label mb-1">Classes assignées</label>
                                <div class="check-grid">
                                    @foreach ($classes as $c)
                                        <label class="check-item" onclick="this.classList.toggle('checked')">
                                            <input type="checkbox" name="classes[]" value="{{ $c->id }}">
                                            {{ $c->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Compte optionnel --}}
                    <div>
                        <div class="sec-sep"><span>Compte d'accès <span
                                    class="text-gray-400 font-normal normal-case">(optionnel)</span></span></div>
                        <div class="g2 mb-3">
                            <div>
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input" placeholder="jean@ecole.cg">
                            </div>
                            <div>
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="password" class="form-input"
                                    placeholder="Min. 8 caractères" minlength="8">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Confirmation</label>
                            <input type="password" name="password_confirmation" class="form-input"
                                placeholder="Répéter">
                        </div>
                        <p class="form-hint mt-1">Si l'email est renseigné, un compte avec le rôle « Enseignant » sera
                            créé.</p>
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
                    <h2 class="text-base font-semibold text-gray-900">Modifier l'enseignant</h2>
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
                            <div><label class="form-label">Matricule</label><input type="text" name="matricule"
                                    id="e_matricule" class="form-input"></div>
                            <div><label class="form-label">Nom <span class="text-red-500">*</span></label><input
                                    type="text" name="nom" id="e_nom" class="form-input" required></div>
                            <div><label class="form-label">Prénom <span class="text-red-500">*</span></label><input
                                    type="text" name="prenom" id="e_prenom" class="form-input" required></div>
                        </div>
                        <div class="g2 mb-3">
                            <div>
                                <label class="form-label">Sexe</label>
                                <select name="sexe" id="e_sexe" class="form-input">
                                    <option value="">—</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                            <div><label class="form-label">Téléphone</label><input type="tel" name="telephone"
                                    id="e_telephone" class="form-input"></div>
                        </div>
                        <div class="g2 mb-3">
                            <div><label class="form-label">Spécialité</label><input type="text" name="specialite"
                                    id="e_specialite" class="form-input"></div>
                            <div>
                                <label class="form-label">Type de contrat</label>
                                <select name="type_contrat" id="e_type_contrat" class="form-input">
                                    <option value="">—</option>
                                    <option value="CDI">CDI</option>
                                    <option value="CDD">CDD</option>
                                    <option value="vacataire">Vacataire</option>
                                    <option value="benevole">Bénévole</option>
                                </select>
                            </div>
                        </div>
                        <div class="g2">
                            <div><label class="form-label">Date de recrutement</label><input type="date"
                                    name="date_recrutement" id="e_date_recrutement" class="form-input"></div>
                            <div>
                                <label class="form-label">Statut</label>
                                <select name="status" id="e_status" class="form-input">
                                    <option value="1">Actif</option>
                                    <option value="0">Inactif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Affectations --}}
                    <div>
                        <div class="sec-sep"><span>Affectations</span></div>

                        @if ($niveaux->count())
                            <div class="mb-3">
                                <label class="form-label mb-1">Niveaux enseignés</label>
                                <div class="check-grid" id="e_niveaux_grid">
                                    @foreach ($niveaux as $n)
                                        <label class="check-item" onclick="this.classList.toggle('checked')">
                                            <input type="checkbox" name="niveaux[]" value="{{ $n->id }}"
                                                class="e-niveau-cb">
                                            {{ $n->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($filieres->count())
                            <div class="mb-3">
                                <label class="form-label mb-1">Filières</label>
                                <div class="check-grid" id="e_filieres_grid">
                                    @foreach ($filieres as $f)
                                        <label class="check-item" onclick="this.classList.toggle('checked')">
                                            <input type="checkbox" name="filieres[]" value="{{ $f->id }}"
                                                class="e-filiere-cb">
                                            {{ $f->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($classes->count())
                            <div>
                                <label class="form-label mb-1">Classes assignées</label>
                                <div class="check-grid" id="e_classes_grid">
                                    @foreach ($classes as $c)
                                        <label class="check-item" onclick="this.classList.toggle('checked')">
                                            <input type="checkbox" name="classes[]" value="{{ $c->id }}"
                                                class="e-classe-cb">
                                            {{ $c->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Compte --}}
                    <div>
                        <div class="sec-sep"><span>Compte d'accès</span></div>
                        <div class="g2">
                            <div><label class="form-label">Nom d'affichage</label><input type="text" name="name"
                                    id="e_name" class="form-input"></div>
                            <div><label class="form-label">Email</label><input type="email" name="email"
                                    id="e_email" class="form-input"></div>
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
                    <div class="avatar w-10 h-10 text-sm bg-blue-100 text-blue-700" id="viewAvatar">--</div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900" id="viewName">—</h2>
                        <p class="text-xs text-gray-400" id="viewSpecialite">—</p>
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
                    <p class="text-sm text-gray-500">Nouveau mot de passe pour <strong id="resetPwdName"
                            class="text-gray-800">—</strong></p>
                    <div><label class="form-label">Nouveau mot de passe <span class="text-red-500">*</span></label><input
                            type="password" name="password" class="form-input" required minlength="8"
                            placeholder="Min. 8 caractères"></div>
                    <div><label class="form-label">Confirmation <span class="text-red-500">*</span></label><input
                            type="password" name="password_confirmation" class="form-input" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('resetPwdModal')" class="btn-secondary">Annuler</button>
                    <button type="submit" class="btn-primary">Réinitialiser</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════
     MODAL — CONFIRM STATUS
══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="confirmModal">
        <div class="modal-box sm">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0" id="confirmIcon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    @csrf
                    <input type="hidden" name="_method" id="confirmMethod" value="PATCH">
                    <input type="hidden" name="status" id="confirmStatus" value="">
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeModal('confirmModal')"
                            class="btn-secondary">Annuler</button>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Supprimer <span id="deleteTeacherName"
                                class="text-red-600">—</span> ?</h2>
                        <p class="text-sm text-gray-500 mt-1">Le profil, les affectations et le compte utilisateur associé
                            seront définitivement supprimés.</p>
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
            if (e.key === 'Escape') document.querySelectorAll('.modal-backdrop.open').forEach(m => closeModal(m
                .id));
        });

        // ══════════════════════════════════════════
        //  EDIT MODAL — pré-remplir
        // ══════════════════════════════════════════
        function openEditModal(t) {
            document.getElementById('editForm').action = `/admin/teachers/${t.id}`;
            document.getElementById('editSubtitle').textContent = (t.prenom || '') + ' ' + (t.nom || '');

            document.getElementById('e_matricule').value = t.matricule || '';
            document.getElementById('e_nom').value = t.nom || '';
            document.getElementById('e_prenom').value = t.prenom || '';
            document.getElementById('e_sexe').value = t.sexe || '';
            document.getElementById('e_telephone').value = t.telephone || '';
            document.getElementById('e_specialite').value = t.specialite || '';
            document.getElementById('e_type_contrat').value = t.type_contrat || '';
            document.getElementById('e_date_recrutement').value = t.date_recrutement || '';
            document.getElementById('e_status').value = t.status != null ? String(t.status) : '1';
            document.getElementById('e_name').value = t.user?.name || '';
            document.getElementById('e_email').value = t.user?.email || t.email || '';

            // Cocher les pivots
            const teacherNiveaux = (t.niveaux || []).map(n => String(n.id));
            const teacherFilieres = (t.filieres || []).map(f => String(f.id));
            const teacherClasses = (t.classes || []).map(c => String(c.id));

            document.querySelectorAll('.e-niveau-cb').forEach(cb => {
                const checked = teacherNiveaux.includes(cb.value);
                cb.checked = checked;
                cb.closest('.check-item').classList.toggle('checked', checked);
            });
            document.querySelectorAll('.e-filiere-cb').forEach(cb => {
                const checked = teacherFilieres.includes(cb.value);
                cb.checked = checked;
                cb.closest('.check-item').classList.toggle('checked', checked);
            });
            document.querySelectorAll('.e-classe-cb').forEach(cb => {
                const checked = teacherClasses.includes(cb.value);
                cb.checked = checked;
                cb.closest('.check-item').classList.toggle('checked', checked);
            });

            openModal('editModal');
        }

        // ══════════════════════════════════════════
        //  VIEW MODAL
        // ══════════════════════════════════════════
        function openViewModal(t) {
            const initials = ((t.prenom || '').charAt(0) + (t.nom || '').charAt(0)).toUpperCase();
            document.getElementById('viewAvatar').textContent = initials;
            document.getElementById('viewName').textContent = (t.prenom || '') + ' ' + (t.nom || '');
            document.getElementById('viewSpecialite').textContent = t.specialite || 'Aucune spécialité';

            const classeNames = (t.classes || []).map(c => c.name).join(', ') || '—';
            const niveauNames = (t.niveaux || []).map(n => n.name).join(', ') || '—';
            const filiereNames = (t.filieres || []).map(f => f.name).join(', ') || '—';

            const rows = [
                ['Matricule', t.matricule || '—'],
                ['Sexe', t.sexe === 'M' ? '♂ Masculin' : t.sexe === 'F' ? '♀ Féminin' : '—'],
                ['Téléphone', t.telephone || '—'],
                ['Email', t.user?.email || t.email || '—'],
                ['Contrat', t.type_contrat ? t.type_contrat.toUpperCase() : '—'],
                ['Recrutement', t.date_recrutement || '—'],
                ['Niveaux', niveauNames],
                ['Filières', filiereNames],
                ['Classes', classeNames],
                ['Statut', t.status == 1 ? '✅ Actif' : '⏸ Inactif'],
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
            document.getElementById('resetPwdForm').action = `/admin/teachers/${id}/reset-password`;
            document.getElementById('resetPwdName').textContent = name;
            openModal('resetPwdModal');
        }

        // ══════════════════════════════════════════
        //  CONFIRM STATUS
        // ══════════════════════════════════════════
        function confirmStatus(url, statusVal, title, desc, btnLabel, isDanger = false) {
            document.getElementById('confirmForm').action = url;
            document.getElementById('confirmMethod').value = 'PATCH';
            document.getElementById('confirmStatus').value = statusVal;
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmDesc').textContent = desc;
            document.getElementById('confirmBtn').textContent = btnLabel;
            document.getElementById('confirmIcon').className =
                (isDanger ? 'w-11 h-11 bg-red-100' : 'w-11 h-11 bg-amber-100') +
                ' rounded-full flex items-center justify-center shrink-0';
            document.getElementById('confirmBtn').className =
                isDanger ? 'btn-danger' : 'btn-primary';
            openModal('confirmModal');
        }

        // ══════════════════════════════════════════
        //  DELETE
        // ══════════════════════════════════════════
        function openDeleteModal(id, name) {
            document.getElementById('deleteForm').action = `/admin/teachers/${id}`;
            document.getElementById('deleteTeacherName').textContent = name;
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
        function filterContrat(contrat) {
            document.querySelectorAll('.pill-tab').forEach(t => t.classList.toggle('active', t.dataset.contrat ===
                contrat));
            applyFilters();
        }

        function applyFilters() {
            const contrat = document.querySelector('.pill-tab.active')?.dataset.contrat || 'all';
            const q = document.getElementById('searchInput').value.toLowerCase();
            const status = document.getElementById('filterStatus').value;

            document.querySelectorAll('.teacher-row').forEach(row => {
                const ok = (contrat === 'all' || row.dataset.contrat === contrat) &&
                    (!q || row.dataset.search.includes(q)) &&
                    (!status || row.dataset.status === status);
                row.style.display = ok ? '' : 'none';
            });
        }
    </script>
@endpush
