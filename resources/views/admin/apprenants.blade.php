@extends('admin.master')

@push('styles')
    <style>
        /* ══════════════════════════════════════════
                   APPRENANTS PAGE — LIGHT THEME
        ══════════════════════════════════════════ */

        /* ── Stat cards ── */
        .stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
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
            border-radius: 0.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ── Filter bar ── */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
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
        .appr-table-wrap {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .875rem;
            overflow: hidden;
        }

        .appr-table {
            width: 100%;
            border-collapse: collapse;
        }

        .appr-table thead tr {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .appr-table th {
            padding: .75rem 1rem;
            font-size: .75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .04em;
            text-align: left;
            white-space: nowrap;
        }

        .appr-table td {
            padding: .875rem 1rem;
            font-size: .8125rem;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .appr-table tbody tr:last-child td {
            border-bottom: none;
        }

        .appr-table tbody tr {
            transition: background .1s;
        }

        .appr-table tbody tr:hover {
            background: #fafafa;
        }

        /* ── Avatar ── */
        .avatar {
            width: 2.1rem;
            height: 2.1rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .75rem;
            flex-shrink: 0;
        }

        /* ── Status badge ── */
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

        .badge-dot {
            width: .45rem;
            height: .45rem;
            border-radius: 50%;
            background: currentColor;
            display: inline-block;
            flex-shrink: 0;
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
            text-decoration: none;
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
            text-decoration: none;
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

        /* ── Action icon buttons (corrigés) ── */
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            padding: 0;
            border: none;
            background: transparent;
            border-radius: .375rem;
            color: #9ca3af;
            cursor: pointer;
            transition: background .15s, color .15s;
            flex-shrink: 0;
            line-height: 1;
            vertical-align: middle;
        }

        .btn-icon:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-icon.danger:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-icon svg {
            width: 1rem;
            height: 1rem;
            display: block;
            flex-shrink: 0;
        }

        /* Conteneur des actions dans le tableau */
        .actions-cell {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .125rem;
        }

        /* ── Bulk bar ── */
        .bulk-bar {
            display: none;
            align-items: center;
            gap: .75rem;
            background: #1f2937;
            color: #fff;
            border-radius: .875rem;
            padding: .75rem 1.25rem;
            font-size: .8125rem;
            font-weight: 500;
        }

        .bulk-bar.show {
            display: flex;
        }

        .bulk-bar .b-neutral {
            padding: .35rem .875rem;
            background: rgba(255,255,255,.15);
            color: #fff;
            border: none;
            border-radius: .375rem;
            font-size: .8rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
        }

        .bulk-bar .b-neutral:hover {
            background: rgba(255,255,255,.25);
        }

        .bulk-bar .b-danger {
            padding: .35rem .875rem;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: .375rem;
            font-size: .8rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
        }

        .bulk-bar .b-danger:hover {
            background: #fecaca;
        }

        /* ── Pagination ── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .875rem 1.25rem;
            border-top: 1px solid #f3f4f6;
            font-size: .8rem;
            color: #6b7280;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state svg {
            width: 2.5rem;
            height: 2.5rem;
            color: #d1d5db;
            margin: 0 auto;
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
            max-width: 580px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .18);
            animation: mIn .2s ease;
        }

        .modal-box.wide {
            max-width: 720px;
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

        /* ── Section divider dans modal ── */
        .section-divider {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: .875rem;
        }

        .section-divider svg {
            width: .9rem;
            height: .9rem;
            flex-shrink: 0;
        }

        /* ── Form elements ── */
        .form-label {
            display: block;
            font-size: .8rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: .35rem;
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
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156, 163, 175, .15);
        }

        select.form-input {
            cursor: pointer;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }

        /* ── View modal info rows ── */
        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .625rem 0;
        }

        .info-row .lbl {
            font-size: .8rem;
            color: #9ca3af;
            font-weight: 500;
        }

        .info-row .val {
            font-size: .875rem;
            color: #1f2937;
            font-weight: 500;
            text-align: right;
        }

        /* ══════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════ */
        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
                padding: .75rem 1rem;
                gap: .5rem;
            }

            .search-wrap {
                min-width: unset;
                width: 100%;
            }

            .filter-input {
                width: 100%;
            }

            .appr-table-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .appr-table {
                min-width: 640px;
            }

            .appr-table th:nth-child(5),
            .appr-table td:nth-child(5),
            .appr-table th:nth-child(6),
            .appr-table td:nth-child(6) {
                display: none;
            }

            .pagination-wrap {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: .75rem;
            }

            .modal-backdrop {
                padding: .5rem;
                align-items: flex-end;
            }

            .modal-box,
            .modal-box.wide,
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

            .grid-2,
            .grid-3 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .appr-table th:nth-child(4),
            .appr-table td:nth-child(4) {
                display: none;
            }

            .appr-table {
                min-width: 420px;
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
                <h1 class="text-2xl font-bold text-gray-900">Apprenants</h1>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $institution->name }} · Année {{ $institution->academic_year ?? date('Y') . '-' . (date('Y') + 1) }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Import CSV --}}
                <button onclick="openModal('importModal')" class="btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Importer
                </button>
                {{-- Nouvel apprenant --}}
                <button onclick="openModal('createModal')" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvel apprenant
                </button>
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
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

        {{-- ═══════════════════════════════════
             STATS ROW
        ═══════════════════════════════════ --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
                $statCards = [
                    [
                        'label' => 'Total inscrits',
                        'val'   => $stats['total'],
                        'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                        'bg'    => 'bg-indigo-50',
                        'fg'    => 'text-indigo-600',
                    ],
                    [
                        'label' => 'Actifs',
                        'val'   => $stats['active'],
                        'icon'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'bg'    => 'bg-green-50',
                        'fg'    => 'text-green-600',
                    ],
                    [
                        'label' => 'Garçons',
                        'val'   => $stats['garcons'],
                        'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        'bg'    => 'bg-blue-50',
                        'fg'    => 'text-blue-600',
                    ],
                    [
                        'label' => 'Filles',
                        'val'   => $stats['filles'],
                        'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        'bg'    => 'bg-pink-50',
                        'fg'    => 'text-pink-600',
                    ],
                ];
            @endphp
            @foreach ($statCards as $sc)
                <div class="stat-card">
                    <div class="stat-icon {{ $sc['bg'] }}">
                        <svg class="w-5 h-5 {{ $sc['fg'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $sc['icon'] }}" />
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
            {{-- Recherche --}}
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="searchInput" class="filter-input"
                    placeholder="Rechercher nom, prénom, matricule…" oninput="applyFilters()">
            </div>

            {{-- Niveau --}}
            <select id="filterNiveau" class="filter-input" onchange="applyFilters()">
                <option value="">Tous les niveaux</option>
                @foreach ($niveaux as $n)
                    <option value="{{ $n->id }}">{{ $n->name }}</option>
                @endforeach
            </select>

            {{-- Filière --}}
            <select id="filterFiliere" class="filter-input" onchange="applyFilters()">
                <option value="">Toutes les filières</option>
                @foreach ($filieres as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>

            {{-- Classe --}}
            <select id="filterClasse" class="filter-input" onchange="applyFilters()">
                <option value="">Toutes les classes</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>

            {{-- Statut --}}
            <select id="filterStatus" class="filter-input" onchange="applyFilters()">
                <option value="">Tous statuts</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
            </select>

            {{-- Reset --}}
            <button onclick="resetFilters()" class="btn-icon" title="Réinitialiser les filtres">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        {{-- ═══════════════════════════════════
             BULK ACTION BAR
        ═══════════════════════════════════ --}}
        <div class="bulk-bar" id="bulkBar">
            <span id="bulkCount">0</span> sélectionné(s)
            <button class="b-neutral" onclick="exportSelected()">Exporter</button>
            <button class="b-danger" onclick="openBulkDelete()">Supprimer</button>
            <button class="b-neutral" onclick="clearSelection()">✕ Annuler</button>
        </div>

        {{-- ═══════════════════════════════════
             TABLE
        ═══════════════════════════════════ --}}
        <div class="appr-table-wrap">
            <table class="appr-table">
                <thead>
                    <tr>
                        <th style="width:2.5rem">
                            <input type="checkbox" id="checkAll" onchange="toggleAll(this)"
                                class="rounded border-gray-300 text-gray-800 focus:ring-0">
                        </th>
                        <th>Apprenant</th>
                        <th>Matricule</th>
                        <th>Classe</th>
                        <th>Niveau / Filière</th>
                        <th>Âge</th>
                        <th>Statut</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody id="apprenantTableBody">
                    @forelse($apprenants as $a)
                        @php
                            $colors = [
                                'bg-indigo-100 text-indigo-700',
                                'bg-teal-100 text-teal-700',
                                'bg-amber-100 text-amber-700',
                                'bg-rose-100 text-rose-700',
                                'bg-purple-100 text-purple-700',
                            ];
                            $col = $colors[$a->id % 5];
                            $age = $a->date_naissance
                                ? \Carbon\Carbon::parse($a->date_naissance)->age
                                : null;
                            $statusBadge = match ((string) $a->status) {
                                '1', 'active'   => ['badge-active', 'Actif'],
                                '0', 'inactive' => ['badge-inactive', 'Inactif'],
                                default          => ['badge-blocked', 'Bloqué'],
                            };
                        @endphp
                        <tr class="appr-row"
                            data-search="{{ strtolower($a->nom . ' ' . $a->prenom . ' ' . ($a->matricule ?? '')) }}"
                            data-niveau="{{ $a->niveau_id ?? '' }}"
                            data-filiere="{{ $a->filiere_id ?? '' }}"
                            data-classe="{{ $a->class_id ?? '' }}"
                            data-status="{{ $a->status }}">

                            <td>
                                <input type="checkbox"
                                    class="row-check rounded border-gray-300 text-gray-800 focus:ring-0"
                                    value="{{ $a->id }}" onchange="updateBulk()">
                            </td>

                            {{-- Apprenant --}}
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="avatar {{ $col }}">
                                        {{ strtoupper(substr($a->prenom, 0, 1) . substr($a->nom, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $a->prenom }} {{ $a->nom }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ $a->sexe === 'M' ? '♂ Garçon' : ($a->sexe === 'F' ? '♀ Fille' : '—') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Matricule --}}
                            <td>
                                <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                    {{ $a->matricule ?? '—' }}
                                </span>
                            </td>

                            {{-- Classe --}}
                            <td>
                                @if ($a->classe)
                                    <span class="font-medium text-gray-700">{{ $a->classe->name }}</span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Non affecté</span>
                                @endif
                            </td>

                            {{-- Niveau / Filière --}}
                            <td>
                                <div>
                                    <p class="text-xs text-gray-700">{{ $a->niveau->name ?? '—' }}</p>
                                    @if ($a->filiere)
                                        <p class="text-xs text-gray-400">{{ $a->filiere->name }}</p>
                                    @endif
                                </div>
                            </td>

                            {{-- Âge --}}
                            <td class="text-gray-500">{{ $age ? $age . ' ans' : '—' }}</td>

                            {{-- Statut --}}
                            <td>
                                <span class="badge {{ $statusBadge[0] }}">
                                    <span class="badge-dot"></span>
                                    {{ $statusBadge[1] }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="actions-cell">
                                    {{-- Voir profil --}}
                                    <button type="button" class="btn-icon" title="Voir le profil"
                                        onclick='openViewModal(@json($a))'>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    {{-- Modifier --}}
                                    <button type="button" class="btn-icon" title="Modifier"
                                        onclick='openEditModal(@json($a))'>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    {{-- Réinitialiser mot de passe --}}
                                    <button type="button" class="btn-icon" title="Réinitialiser le mot de passe"
                                        onclick="openResetPwd({{ $a->id }}, '{{ addslashes($a->prenom . ' ' . $a->nom) }}')">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </button>
                                    {{-- Supprimer --}}
                                    <button type="button" class="btn-icon danger" title="Supprimer"
                                        onclick="openDeleteModal({{ $a->id }}, '{{ addslashes($a->prenom . ' ' . $a->nom) }}')">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="8">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-sm font-medium text-gray-400 mt-1">Aucun apprenant enregistré</p>
                                    <p class="text-xs text-gray-300 mt-0.5">Commencez par en créer un</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($apprenants->hasPages())
                <div class="pagination-wrap">
                    <span>
                        Affichage de {{ $apprenants->firstItem() }} à {{ $apprenants->lastItem() }}
                        sur {{ $apprenants->total() }} apprenants
                    </span>
                    <div class="flex items-center gap-1">
                        {{ $apprenants->links() }}
                    </div>
                </div>
            @else
                <div class="pagination-wrap">
                    <span>{{ $apprenants->total() }} apprenant(s) au total</span>
                </div>
            @endif
        </div>

    </div>{{-- /space-y-5 --}}


    {{-- ══════════════════════════════════════════════════════════
         MODAL — CRÉER APPRENANT
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="createModal">
        <div class="modal-box wide">
            <div class="modal-header">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Nouvel apprenant</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Remplissez les informations ci-dessous</p>
                </div>
                <button type="button" onclick="closeModal('createModal')" class="btn-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.apprenants.store') }}" method="POST">
                @csrf
                <div class="modal-body space-y-5">

                    {{-- Identité --}}
                    <div>
                        <div class="section-divider">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Identité
                        </div>
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                <input type="text" name="nom" class="form-input" required placeholder="Ex : Mbemba">
                            </div>
                            <div>
                                <label class="form-label">Prénom <span class="text-red-500">*</span></label>
                                <input type="text" name="prenom" class="form-input" required placeholder="Ex : Junior">
                            </div>
                            <div>
                                <label class="form-label">Date de naissance</label>
                                <input type="date" name="date_naissance" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Sexe</label>
                                <select name="sexe" class="form-input">
                                    <option value="">— Choisir —</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Matricule</label>
                            <input type="text" name="matricule" class="form-input"
                                placeholder="Laissez vide → généré automatiquement (ex: ETB-2025-0042)">
                            <p style="font-size:.75rem;color:#9ca3af;margin-top:.25rem;">
                                Format auto : ETB-{ANNEE}-XXXX si le champ est vide.
                            </p>
                        </div>
                    </div>

                    {{-- Scolarité --}}
                    <div>
                        <div class="section-divider">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                            Scolarité
                        </div>
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Niveau</label>
                                <select name="niveau_id" id="c_niveau" class="form-input"
                                    onchange="filterClassesByNiveau('c')">
                                    <option value="">— Choisir —</option>
                                    @foreach ($niveaux as $n)
                                        <option value="{{ $n->id }}">{{ $n->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Filière</label>
                                <select name="filiere_id" class="form-input">
                                    <option value="">— Choisir —</option>
                                    @foreach ($filieres as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Classe</label>
                                <select name="class_id" id="c_classe" class="form-input">
                                    <option value="">— Choisir —</option>
                                    @foreach ($classes as $c)
                                        <option value="{{ $c->id }}" data-niveau="{{ $c->niveau_id }}">
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Année académique</label>
                                <input type="text" name="annee_academique" class="form-input"
                                    value="{{ $institution->academic_year ?? '' }}" placeholder="Ex : 2024-2025">
                            </div>
                        </div>
                    </div>

                    {{-- Compte utilisateur --}}
                    <div>
                        <div class="section-divider">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Compte d'accès <span class="text-gray-400 font-normal normal-case">(optionnel)</span>
                        </div>
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input" placeholder="apprenant@ecole.cg">
                            </div>
                            <div>
                                <label class="form-label">Mot de passe initial</label>
                                <input type="password" name="password" class="form-input" placeholder="Min. 8 caractères">
                            </div>
                        </div>
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
         MODAL — MODIFIER APPRENANT
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="editModal">
        <div class="modal-box wide">
            <div class="modal-header">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Modifier l'apprenant</h2>
                    <p class="text-xs text-gray-400 mt-0.5" id="editModalSubtitle">—</p>
                </div>
                <button type="button" onclick="closeModal('editModal')" class="btn-icon">
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
                        <div class="section-divider">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Identité
                        </div>
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                <input type="text" name="nom" id="e_nom" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">Prénom <span class="text-red-500">*</span></label>
                                <input type="text" name="prenom" id="e_prenom" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">Date de naissance</label>
                                <input type="date" name="date_naissance" id="e_dob" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Sexe</label>
                                <select name="sexe" id="e_sexe" class="form-input">
                                    <option value="">— Choisir —</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 grid-2">
                            <div>
                                <label class="form-label">Matricule</label>
                                <input type="text" name="matricule" id="e_matricule" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Statut</label>
                                <select name="status" id="e_status" class="form-input">
                                    <option value="1">Actif</option>
                                    <option value="0">Inactif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Scolarité --}}
                    <div>
                        <div class="section-divider">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                            Scolarité
                        </div>
                        <div class="grid-2">
                            <div>
                                <label class="form-label">Niveau</label>
                                <select name="niveau_id" id="e_niveau" class="form-input"
                                    onchange="filterClassesByNiveau('e')">
                                    <option value="">— Choisir —</option>
                                    @foreach ($niveaux as $n)
                                        <option value="{{ $n->id }}">{{ $n->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Filière</label>
                                <select name="filiere_id" id="e_filiere" class="form-input">
                                    <option value="">— Choisir —</option>
                                    @foreach ($filieres as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Classe</label>
                                <select name="class_id" id="e_classe" class="form-input">
                                    <option value="">— Choisir —</option>
                                    @foreach ($classes as $c)
                                        <option value="{{ $c->id }}" data-niveau="{{ $c->niveau_id }}">
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Année académique</label>
                                <input type="text" name="annee_academique" id="e_annee" class="form-input">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('editModal')" class="btn-secondary">Annuler</button>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════
         MODAL — VOIR PROFIL
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="viewModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="avatar" id="viewAvatar"
                        style="width:2.5rem;height:2.5rem;font-size:.875rem;background:#e0e7ff;color:#4338ca;">--</div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900" id="viewName">—</h2>
                        <p class="text-xs text-gray-400" id="viewMatricule">—</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('viewModal')" class="btn-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewInfoRows" class="divide-y divide-gray-50"></div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('viewModal')" class="btn-secondary">Fermer</button>
            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════
         MODAL — RÉINITIALISER MOT DE PASSE
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="resetPwdModal">
        <div class="modal-box sm">
            <div class="modal-header">
                <h2 class="text-base font-semibold text-gray-900">Réinitialiser le mot de passe</h2>
                <button type="button" onclick="closeModal('resetPwdModal')" class="btn-icon">
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
                        <input type="password" name="password_confirmation" class="form-input" required
                            placeholder="Répéter le mot de passe">
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
                            Supprimer <span id="deleteApprName" class="text-red-600">—</span> ?
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Cette action est irréversible. Le compte et toutes les données associées seront supprimés.
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


    {{-- ══════════════════════════════════════════════════════════
         MODAL — IMPORT CSV
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="importModal">
        <div class="modal-box sm">
            <div class="modal-header">
                <h2 class="text-base font-semibold text-gray-900">Importer des apprenants</h2>
                <button type="button" onclick="closeModal('importModal')" class="btn-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.apprenants.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body space-y-4">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700">
                        <p class="font-semibold mb-1">Format attendu (CSV) :</p>
                        <p class="font-mono">nom, prenom, date_naissance, sexe, matricule, niveau_id, filiere_id, class_id</p>
                    </div>
                    <div>
                        <label class="form-label">Fichier CSV <span class="text-red-500">*</span></label>
                        <input type="file" name="csv_file" accept=".csv,.xlsx" required
                            class="form-input py-1.5 text-xs file:mr-3 file:py-1 file:px-3 file:rounded file:border-0
                                   file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700">
                    </div>
                    <div>
                        <label class="form-label">Classe cible (optionnel)</label>
                        <select name="default_class_id" class="form-input">
                            <option value="">— Utiliser la colonne du fichier —</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('importModal')" class="btn-secondary">Annuler</button>
                    <button type="submit" class="btn-primary">Importer</button>
                </div>
            </form>
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
        function openEditModal(a) {
            document.getElementById('editForm').action = `/admin/apprenants/${a.id}`;
            document.getElementById('editModalSubtitle').textContent = (a.prenom || '') + ' ' + (a.nom || '');
            document.getElementById('e_nom').value = a.nom || '';
            document.getElementById('e_prenom').value = a.prenom || '';
            document.getElementById('e_dob').value = a.date_naissance || '';
            document.getElementById('e_sexe').value = a.sexe || '';
            document.getElementById('e_matricule').value = a.matricule || '';
            document.getElementById('e_status').value = a.status != null ? String(a.status) : '1';
            document.getElementById('e_niveau').value = a.niveau_id || '';
            document.getElementById('e_filiere').value = a.filiere_id || '';
            document.getElementById('e_classe').value = a.class_id || '';
            document.getElementById('e_annee').value = a.annee_academique || '';
            openModal('editModal');
        }

        // ══════════════════════════════════════════
        //  VIEW MODAL
        // ══════════════════════════════════════════
        function openViewModal(a) {
            const initials = ((a.prenom || '').charAt(0) + (a.nom || '').charAt(0)).toUpperCase();
            document.getElementById('viewAvatar').textContent = initials;
            document.getElementById('viewName').textContent = (a.prenom || '') + ' ' + (a.nom || '');
            document.getElementById('viewMatricule').textContent = a.matricule
                ? 'Matricule : ' + a.matricule
                : 'Aucun matricule';

            const rows = [
                ['Sexe', a.sexe === 'M' ? '♂ Garçon' : a.sexe === 'F' ? '♀ Fille' : '—'],
                ['Date de naissance', a.date_naissance || '—'],
                ['Année académique', a.annee_academique || '—'],
                ['Statut', a.status == 1 ? '✅ Actif' : '⏸ Inactif'],
            ];

            document.getElementById('viewInfoRows').innerHTML = rows.map(([lbl, val]) =>
                `<div class="info-row"><span class="lbl">${lbl}</span><span class="val">${val}</span></div>`
            ).join('');

            openModal('viewModal');
        }

        // ══════════════════════════════════════════
        //  RESET PASSWORD MODAL
        // ══════════════════════════════════════════
        function openResetPwd(id, name) {
            document.getElementById('resetPwdForm').action = `/admin/apprenants/${id}/reset-password`;
            document.getElementById('resetPwdName').textContent = name;
            openModal('resetPwdModal');
        }

        // ══════════════════════════════════════════
        //  DELETE MODAL
        // ══════════════════════════════════════════
        function openDeleteModal(id, name) {
            document.getElementById('deleteForm').action = `/admin/apprenants/${id}`;
            document.getElementById('deleteApprName').textContent = name;
            openModal('deleteModal');
        }

        // ══════════════════════════════════════════
        //  CLASSE FILTER BY NIVEAU (create + edit)
        // ══════════════════════════════════════════
        function filterClassesByNiveau(prefix) {
            const niveauId = document.getElementById(prefix + '_niveau').value;
            const classeSelect = document.getElementById(prefix + '_classe');
            Array.from(classeSelect.options).forEach(opt => {
                if (!opt.value) return;
                opt.hidden = niveauId ? opt.dataset.niveau !== niveauId : false;
            });
            classeSelect.value = '';
        }

        // ══════════════════════════════════════════
        //  CLIENT-SIDE FILTERS
        // ══════════════════════════════════════════
        function applyFilters() {
            const q       = document.getElementById('searchInput').value.toLowerCase();
            const niveau  = document.getElementById('filterNiveau').value;
            const filiere = document.getElementById('filterFiliere').value;
            const classe  = document.getElementById('filterClasse').value;
            const status  = document.getElementById('filterStatus').value;

            let visible = 0;
            document.querySelectorAll('.appr-row').forEach(row => {
                const matchSearch  = !q       || row.dataset.search.includes(q);
                const matchNiveau  = !niveau  || row.dataset.niveau  === niveau;
                const matchFiliere = !filiere || row.dataset.filiere === filiere;
                const matchClasse  = !classe  || row.dataset.classe  === classe;
                const matchStatus  = !status  || row.dataset.status  === status;
                const show = matchSearch && matchNiveau && matchFiliere && matchClasse && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.style.display = visible === 0 ? '' : 'none';
        }

        function resetFilters() {
            ['searchInput', 'filterNiveau', 'filterFiliere', 'filterClasse', 'filterStatus'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            applyFilters();
        }

        // ══════════════════════════════════════════
        //  BULK SELECTION
        // ══════════════════════════════════════════
        function toggleAll(master) {
            document.querySelectorAll('.row-check').forEach(cb => {
                if (!cb.closest('tr').style.display || cb.closest('tr').style.display !== 'none')
                    cb.checked = master.checked;
            });
            updateBulk();
        }

        function updateBulk() {
            const checked = document.querySelectorAll('.row-check:checked').length;
            const bar     = document.getElementById('bulkBar');
            document.getElementById('bulkCount').textContent = checked;
            bar.classList.toggle('show', checked > 0);
            document.getElementById('checkAll').indeterminate =
                checked > 0 && checked < document.querySelectorAll('.row-check').length;
            document.getElementById('checkAll').checked =
                checked === document.querySelectorAll('.row-check').length;
        }

        function clearSelection() {
            document.querySelectorAll('.row-check, #checkAll').forEach(cb => cb.checked = false);
            document.getElementById('checkAll').indeterminate = false;
            updateBulk();
        }

        function openBulkDelete() {
            const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
            if (!ids.length) return;
            if (confirm(`Supprimer ${ids.length} apprenant(s) sélectionné(s) ? Cette action est irréversible.`)) {
                const f = document.createElement('form');
                f.method  = 'POST';
                f.action  = '{{ route('admin.apprenants.bulkDestroy') }}';
                f.innerHTML = `@csrf @method('DELETE')<input name="ids" value="${ids.join(',')}">`;
                document.body.appendChild(f);
                f.submit();
            }
        }

        function exportSelected() {
            const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
            const url = ids.length
                ? `{{ route('admin.apprenants.export') }}?ids=${ids.join(',')}`
                : `{{ route('admin.apprenants.export') }}`;
            window.location.href = url;
        }
    </script>
@endpush