@extends('admin.master')

@push('styles')
    <style>
        .acad-tab-nav {
            display: flex;
            gap: 0;
            background: #f3f4f6;
            border-radius: 0.75rem;
            padding: 0.25rem;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .acad-tab-nav::-webkit-scrollbar {
            display: none;
        }

        .acad-tab-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #6b7280;
            white-space: nowrap;
            cursor: pointer;
            border: none;
            background: transparent;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .acad-tab-btn:hover {
            color: #374151;
            background: #e5e7eb;
        }

        .acad-tab-btn.active {
            background: #1f2937;
            color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
        }

        .acad-tab-btn svg {
            width: 1rem;
            height: 1rem;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .acad-tab-btn .count-chip {
            background: rgba(255, 255, 255, .2);
            padding: .1rem .45rem;
            border-radius: 9999px;
            font-size: .7rem;
            font-weight: 600;
        }

        .acad-tab-btn:not(.active) .count-chip {
            background: #e5e7eb;
            color: #6b7280;
        }

        .acad-panel {
            display: none;
        }

        .acad-panel.active {
            display: block;
            animation: fadeUp .2s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.6rem 0.875rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156, 163, 175, .15);
        }

        .form-label {
            display: block;
            font-size: .8rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: .35rem;
        }

        .data-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: box-shadow .15s, border-color .15s;
        }

        .data-card:hover {
            border-color: #d1d5db;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        }

        .inline-form-card {
            background: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            backdrop-filter: blur(3px);
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
            max-width: 560px;
            max-height: 88vh;
            overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .15);
            animation: modalIn .2s ease;
        }

        @keyframes modalIn {
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
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .section-header h3 {
            font-size: .9375rem;
            font-weight: 600;
            color: #111827;
        }

        .section-header p {
            font-size: .8rem;
            color: #6b7280;
            margin-top: .15rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
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
            gap: .5rem;
            padding: .55rem 1rem;
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            font-size: .8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .btn-danger {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
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

        .btn-icon {
            padding: .4rem;
            border: none;
            background: none;
            border-radius: .375rem;
            color: #9ca3af;
            cursor: pointer;
            transition: background .15s, color .15s;
            display: flex;
            align-items: center;
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
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 280px;
        }

        .search-box svg {
            position: absolute;
            left: .65rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: .9rem;
            height: .9rem;
        }

        .search-box input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: .5rem .875rem .5rem 2rem;
            font-size: .8125rem;
            background: #f9fafb;
            color: #374151;
            transition: border-color .2s, background .2s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #9ca3af;
            background: #fff;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            text-align: center;
            color: #9ca3af;
        }

        .empty-state svg {
            width: 2.5rem;
            height: 2.5rem;
            opacity: .3;
            margin-bottom: .75rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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

            /* Badge année active */
            .flex.items-center.gap-2.px-3 {
                justify-content: center;
            }

            /* Stats : 2 colonnes sur mobile */
            .grid.grid-cols-2.sm\:grid-cols-5 {
                grid-template-columns: 1fr 1fr;
            }

            /* Tabs : scroll horizontal, texte raccourci */
            .acad-tab-nav {
                gap: 0;
                padding: .2rem;
            }

            .acad-tab-btn {
                padding: .5rem .65rem;
                font-size: .72rem;
            }

            .count-chip {
                display: none;
            }

            /* Panels : layout 2 colonnes → 1 colonne */
            #panel-classes>div,
            #panel-niveaux>div,
            #panel-filieres>div,
            #panel-matieres>div {
                grid-template-columns: 1fr !important;
            }

            /* Formulaire inline : passe en dessous de la liste */
            #panel-classes>div>div:first-child,
            #panel-niveaux>div>div:first-child,
            #panel-filieres>div>div:first-child,
            #panel-matieres>div>div:first-child {
                order: 2;
            }

            #panel-classes>div>div:last-child,
            #panel-niveaux>div>div:last-child,
            #panel-filieres>div>div:last-child,
            #panel-matieres>div>div:last-child {
                order: 1;
            }

            /* Section header : stack sur mobile */
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: .6rem;
            }

            .search-box {
                max-width: 100%;
                width: 100%;
            }

            /* Affectations : 3 colonnes → 1 colonne */
            #panel-affectations>div[style*="grid-template-columns:1fr 1fr 1fr"] {
                display: flex !important;
                flex-direction: column;
            }

            /* SW widgets */
            .sw-filters {
                flex-direction: column;
            }

            .sw-filters select,
            .sw-filters input {
                min-width: unset;
                width: 100%;
            }

            /* Modals */
            .modal-backdrop {
                padding: .5rem;
                align-items: flex-end;
            }

            .modal-box {
                max-width: 100% !important;
                border-radius: 1rem 1rem 0 0;
                max-height: 92vh;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-header {
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

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {

            /* Stats : 1 seule colonne */
            .grid.grid-cols-2.sm\:grid-cols-5 {
                grid-template-columns: 1fr;
            }

            /* Tabs : icônes seulement */
            .acad-tab-btn span:not(.count-chip) {
                display: none;
            }

            .acad-tab-btn {
                padding: .5rem .55rem;
            }

            /* data-card : empiler les actions */
            .data-card {
                flex-wrap: wrap;
                gap: .5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="space-y-5">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion académique</h1>
                <p class="text-sm text-gray-500 mt-0.5">Structures, affectations — {{ $institution->name }}</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-lg">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium text-blue-700">{{ $academicYear->label }}</span>
                <span class="text-xs text-blue-400">(Année active)</span>
            </div>
        </div>

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
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @php
                $quickStats = [
                    [
                        'label' => 'Classes',
                        'val' => $stats['classes'],
                        'icon' =>
                            'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'color' => 'text-indigo-600 bg-indigo-50',
                    ],
                    [
                        'label' => 'Niveaux',
                        'val' => $stats['sections'],
                        'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
                        'color' => 'text-purple-600 bg-purple-50',
                    ],
                    [
                        'label' => 'Filières',
                        'val' => $stats['filieres'],
                        'icon' =>
                            'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
                        'color' => 'text-teal-600 bg-teal-50',
                    ],
                    [
                        'label' => 'Matières',
                        'val' => $stats['matieres'],
                        'icon' =>
                            'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                        'color' => 'text-amber-600 bg-amber-50',
                    ],
                    [
                        'label' => 'Enseignants',
                        'val' => $stats['teachers'],
                        'icon' =>
                            'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                        'color' => 'text-rose-600 bg-rose-50',
                    ],
                ];
            @endphp
            @foreach ($quickStats as $s)
                <div class="bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $s['color'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="{{ $s['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900 leading-none">{{ number_format($s['val']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $s['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- MAIN TABS --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <div class="px-4 pt-4 pb-0 border-b border-gray-100">
                <div class="acad-tab-nav" role="tablist">
                    @php
                        $tabs = [
                            [
                                'id' => 'classes',
                                'label' => 'Classes',
                                'count' => $stats['classes'],
                                'icon' =>
                                    'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                            ],
                            [
                                'id' => 'niveaux',
                                'label' => 'Niveaux',
                                'count' => $stats['sections'],
                                'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
                            ],
                            [
                                'id' => 'filieres',
                                'label' => 'Filières',
                                'count' => $stats['filieres'],
                                'icon' =>
                                    'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
                            ],
                            [
                                'id' => 'matieres',
                                'label' => 'Matières',
                                'count' => $stats['matieres'],
                                'icon' =>
                                    'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                            ],
                            [
                                'id' => 'affectations',
                                'label' => 'Affectations',
                                'count' => null,
                                'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                            ],
                        ];
                    @endphp
                    @foreach ($tabs as $i => $tab)
                        <button class="acad-tab-btn {{ $i === 0 ? 'active' : '' }}" data-tab="{{ $tab['id'] }}"
                            onclick="switchTab('{{ $tab['id'] }}')" role="tab">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}" />
                            </svg>
                            {{ $tab['label'] }}
                            @if ($tab['count'] !== null)
                                <span class="count-chip">{{ $tab['count'] }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="p-5">

                {{-- ════════════════════════════════
                 PANEL 1 : CLASSES
                 Classe : institution_id, niveau_id, filiere_id, name, code
            ════════════════════════════════ --}}
                <div class="acad-panel active" id="panel-classes">
                    <div style="display:grid; grid-template-columns:1fr 1.6fr; gap:1.5rem;">

                        {{-- Formulaire --}}
                        <div>
                            <div class="section-header">
                                <div>
                                    <h3>Nouvelle classe</h3>
                                    <p>Ajouter une classe</p>
                                </div>
                            </div>
                            <div class="inline-form-card">
                                <form action="{{ route('admin.academic.classes.store') }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" class="form-input"
                                            placeholder="Ex : Terminale A" required value="{{ old('name') }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Code</label>
                                        <input type="text" name="code" class="form-input" placeholder="Ex : TLA"
                                            value="{{ old('code') }}" maxlength="20">
                                    </div>
                                    <div>
                                        <label class="form-label">Niveau</label>
                                        <select name="niveau_id" class="form-input">
                                            <option value="">— Aucun niveau —</option>
                                            @foreach ($sections as $niveau)
                                                <option value="{{ $niveau->id }}"
                                                    {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                                    {{ $niveau->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Filière</label>
                                        <select name="filiere_id" class="form-input">
                                            <option value="">— Aucune filière —</option>
                                            @foreach ($filieres as $filiere)
                                                <option value="{{ $filiere->id }}"
                                                    {{ old('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                                    {{ $filiere->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn-primary w-full justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Créer la classe
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Liste --}}
                        <div>
                            <div class="section-header">
                                <div>
                                    <h3>Classes existantes</h3>
                                    <p>{{ $classes->count() }} classe(s)</p>
                                </div>
                                <div class="search-box">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input type="text" placeholder="Rechercher…"
                                        oninput="filterList('classList', this.value)">
                                </div>
                            </div>
                            <div class="space-y-2" id="classList">
                                @forelse($classes as $classe)
                                    <div class="data-card"
                                        data-search="{{ strtolower($classe->name . ' ' . ($classe->code ?? '')) }}">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="w-9 h-9 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($classe->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-sm text-gray-900 truncate">
                                                    {{ $classe->name }}</p>
                                                <p class="text-xs text-gray-400 truncate">
                                                    @if ($classe->niveau)
                                                        {{ $classe->niveau->name }}
                                                    @endif
                                                    @if ($classe->filiere)
                                                        · {{ $classe->filiere->name }}
                                                    @endif
                                                    @if ($classe->code)
                                                        · <span class="font-mono">{{ $classe->code }}</span>
                                                    @endif
                                                    · {{ $classe->apprenants_count ?? 0 }} élève(s)
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button class="btn-icon"
                                                onclick="openEditClassModal({{ $classe->id }}, '{{ addslashes($classe->name) }}', '{{ $classe->code ?? '' }}', {{ $classe->niveau_id ?? 'null' }}, {{ $classe->filiere_id ?? 'null' }})">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="btn-icon danger"
                                                onclick="confirmDelete('{{ route('admin.academic.classes.destroy', $classe->id) }}', '{{ addslashes($classe->name) }}')">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-400">Aucune classe créée</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════
                 PANEL 2 : NIVEAUX (= Niveau model)
                 Niveau : name, cycle
                 Pas d'institution_id — niveaux globaux
            ════════════════════════════════ --}}
                <div class="acad-panel" id="panel-niveaux">
                    <div style="display:grid; grid-template-columns:1fr 1.6fr; gap:1.5rem;">
                        <div>
                            <div class="section-header">
                                <div>
                                    <h3>Nouveau niveau</h3>
                                    <p>Ex : 6ème, Terminale, L1…</p>
                                </div>
                            </div>
                            <div class="inline-form-card">
                                <form action="{{ route('admin.academic.niveaux.store') }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" class="form-input"
                                            placeholder="Ex : Terminale" required value="{{ old('name') }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Cycle</label>
                                        <select name="cycle" class="form-input">
                                            <option value="">— Choisir —</option>
                                            <option value="primaire" {{ old('cycle') === 'primaire' ? 'selected' : '' }}>
                                                Primaire</option>
                                            <option value="secondaire"
                                                {{ old('cycle') === 'secondaire' ? 'selected' : '' }}>Secondaire</option>
                                            <option value="universite"
                                                {{ old('cycle') === 'universite' ? 'selected' : '' }}>Université</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn-primary w-full justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Créer le niveau
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <div class="section-header">
                                <div>
                                    <h3>Niveaux existants</h3>
                                    <p>{{ $sections->count() }} niveau(x)</p>
                                </div>
                                <div class="search-box">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input type="text" placeholder="Rechercher…"
                                        oninput="filterList('niveauList', this.value)">
                                </div>
                            </div>
                            <div class="space-y-2" id="niveauList">
                                @forelse($sections as $niveau)
                                    <div class="data-card"
                                        data-search="{{ strtolower($niveau->name . ' ' . ($niveau->cycle ?? '')) }}">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="w-9 h-9 bg-purple-100 text-purple-700 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($niveau->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-sm text-gray-900">{{ $niveau->name }}</p>
                                                <p class="text-xs text-gray-400">
                                                    {{ $niveau->cycle ? ucfirst($niveau->cycle) : '—' }}
                                                    · {{ $niveau->classes_count ?? 0 }} classe(s)
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button class="btn-icon"
                                                onclick="openEditNiveauModal({{ $niveau->id }}, '{{ addslashes($niveau->name) }}', '{{ $niveau->cycle ?? '' }}')">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="btn-icon danger"
                                                onclick="confirmDelete('{{ route('admin.academic.niveaux.destroy', $niveau->id) }}', '{{ addslashes($niveau->name) }}')">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-400">Aucun niveau créé</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════
                 PANEL 3 : FILIÈRES
                 Filière : institution_id, name
            ════════════════════════════════ --}}
                <div class="acad-panel" id="panel-filieres">
                    <div style="display:grid; grid-template-columns:1fr 1.6fr; gap:1.5rem;">
                        <div>
                            <div class="section-header">
                                <div>
                                    <h3>Nouvelle filière</h3>
                                    <p>Parcours spécialisé</p>
                                </div>
                            </div>
                            <div class="inline-form-card">
                                <form action="{{ route('admin.academic.filieres.store') }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" class="form-input"
                                            placeholder="Ex : Sciences Exactes" required value="{{ old('name') }}">
                                    </div>
                                    <button type="submit" class="btn-primary w-full justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Créer la filière
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <div class="section-header">
                                <div>
                                    <h3>Filières existantes</h3>
                                    <p>{{ $filieres->count() }} filière(s)</p>
                                </div>
                                <div class="search-box">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input type="text" placeholder="Rechercher…"
                                        oninput="filterList('filiereList', this.value)">
                                </div>
                            </div>
                            <div class="space-y-2" id="filiereList">
                                @forelse($filieres as $filiere)
                                    <div class="data-card" data-search="{{ strtolower($filiere->name) }}">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="w-9 h-9 bg-teal-100 text-teal-700 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($filiere->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-sm text-gray-900">{{ $filiere->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $filiere->classes_count ?? 0 }}
                                                    classe(s)</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button class="btn-icon"
                                                onclick="openEditFiliereModal({{ $filiere->id }}, '{{ addslashes($filiere->name) }}')">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="btn-icon danger"
                                                onclick="confirmDelete('{{ route('admin.academic.filieres.destroy', $filiere->id) }}', '{{ addslashes($filiere->name) }}')">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-400">Aucune filière créée</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════
                 PANEL 4 : MATIÈRES (Subject)
                 Subject : institution_id, class_id, teacher_id, name, coefficient
            ════════════════════════════════ --}}
                <div class="acad-panel" id="panel-matieres">
                    <div style="display:grid; grid-template-columns:1fr 1.6fr; gap:1.5rem;">
                        <div>
                            <div class="section-header">
                                <div>
                                    <h3>Nouvelle matière</h3>
                                    <p>Discipline enseignée</p>
                                </div>
                            </div>
                            <div class="inline-form-card">
                                <form action="{{ route('admin.academic.matieres.store') }}" method="POST"
                                    class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" class="form-input"
                                            placeholder="Ex : Mathématiques" required value="{{ old('name') }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Coefficient <span class="text-red-500">*</span></label>
                                        <input type="number" name="coefficient" class="form-input" placeholder="2"
                                            min="0.5" step="0.5" required value="{{ old('coefficient') }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Classe</label>
                                        <select name="class_id" class="form-input">
                                            <option value="">— Aucune classe —</option>
                                            @foreach ($classes as $c)
                                                <option value="{{ $c->id }}"
                                                    {{ old('class_id') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Enseignant</label>
                                        <select name="teacher_id" class="form-input">
                                            <option value="">— Aucun —</option>
                                            @foreach ($teachers as $t)
                                                <option value="{{ $t->id }}"
                                                    {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                                                    {{ $t->prenom }} {{ $t->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn-primary w-full justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Créer la matière
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div>
                            <div class="section-header">
                                <div>
                                    <h3>Matières existantes</h3>
                                    <p>{{ $matieres->count() }} matière(s)</p>
                                </div>
                                <div class="search-box">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input type="text" placeholder="Rechercher…"
                                        oninput="filterList('matiereList', this.value)">
                                </div>
                            </div>
                            <div class="space-y-2" id="matiereList">
                                @forelse($matieres as $matiere)
                                    <div class="data-card" data-search="{{ strtolower($matiere->name) }}">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="w-9 h-9 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($matiere->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-sm text-gray-900">{{ $matiere->name }}</p>
                                                <p class="text-xs text-gray-400">
                                                    Coef. {{ $matiere->coefficient ?? '—' }}
                                                    @if ($matiere->classe)
                                                        · {{ $matiere->classe->name }}
                                                    @endif
                                                    @if ($matiere->teacher)
                                                        · {{ $matiere->teacher->prenom }} {{ $matiere->teacher->nom }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button class="btn-icon"
                                                onclick="openEditMatiereModal({{ $matiere->id }}, '{{ addslashes($matiere->name) }}', {{ $matiere->coefficient ?? 1 }}, {{ $matiere->class_id ?? 'null' }}, {{ $matiere->teacher_id ?? 'null' }})">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button class="btn-icon danger"
                                                onclick="confirmDelete('{{ route('admin.academic.matieres.destroy', $matiere->id) }}', '{{ addslashes($matiere->name) }}')">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-400">Aucune matière créée</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════════
     PANEL 5 : AFFECTATIONS — avec recherche AJAX
     Remplacez le contenu de <div class="acad-panel" id="panel-affectations">
     par celui-ci.
════════════════════════════════════════════════════════════ --}}

                <style>
                    /* ── SEARCH WIDGET ── */
                    .sw-wrap {
                        border: 1px solid #e5e7eb;
                        border-radius: .625rem;
                        overflow: hidden;
                        background: #fff;
                    }

                    .sw-filters {
                        display: flex;
                        gap: .5rem;
                        flex-wrap: wrap;
                        padding: .75rem;
                        background: #f9fafb;
                        border-bottom: 1px solid #e5e7eb;
                    }

                    .sw-filters select,
                    .sw-filters input {
                        flex: 1;
                        min-width: 120px;
                        border: 1px solid #e5e7eb;
                        border-radius: .375rem;
                        padding: .4rem .6rem;
                        font-size: .78rem;
                        background: #fff;
                        outline: none;
                        transition: border-color .2s;
                    }

                    .sw-filters select:focus,
                    .sw-filters input:focus {
                        border-color: #9ca3af;
                    }

                    .sw-results {
                        max-height: 200px;
                        overflow-y: auto;
                        display: none;
                    }

                    .sw-results.show {
                        display: block;
                    }

                    .sw-item {
                        padding: .55rem .875rem;
                        font-size: .8rem;
                        color: #374151;
                        cursor: pointer;
                        border-bottom: 1px solid #f3f4f6;
                        display: flex;
                        align-items: center;
                        gap: .5rem;
                        transition: background .15s;
                    }

                    .sw-item:hover {
                        background: #f0fdf4;
                    }

                    .sw-item:last-child {
                        border-bottom: none;
                    }

                    .sw-item .initials {
                        width: 28px;
                        height: 28px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: .65rem;
                        font-weight: 700;
                        flex-shrink: 0;
                    }

                    .sw-item .lbl {
                        font-weight: 600;
                    }

                    .sw-item .sub {
                        font-size: .7rem;
                        color: #9ca3af;
                    }

                    .sw-empty {
                        padding: 1rem;
                        text-align: center;
                        color: #9ca3af;
                        font-size: .8rem;
                    }

                    .sw-selected {
                        display: none;
                        align-items: center;
                        justify-content: space-between;
                        padding: .55rem .875rem;
                        background: #f0fdf4;
                        border-top: 1px solid #bbf7d0;
                        font-size: .8rem;
                    }

                    .sw-selected.show {
                        display: flex;
                    }

                    .sw-selected .name {
                        font-weight: 600;
                        color: #166534;
                    }

                    .sw-selected .clear {
                        background: none;
                        border: none;
                        cursor: pointer;
                        color: #9ca3af;
                        font-size: 1rem;
                        line-height: 1;
                        transition: color .15s;
                    }

                    .sw-selected .clear:hover {
                        color: #ef4444;
                    }

                    .sw-count {
                        font-size: .7rem;
                        color: #9ca3af;
                        padding: .25rem .875rem;
                        border-top: 1px solid #f3f4f6;
                        background: #fafafa;
                    }
                </style>

                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.25rem;">

                    {{-- ────────────────────────────────────────
         WIDGET 1 : Enseignant → Classe
    ──────────────────────────────────────── --}}
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-800">Enseignant → Classe</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Qui intervient dans quelle classe</p>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('admin.academic.affectations.teacher-classe') }}" method="POST"
                                id="form-tc">
                                @csrf

                                {{-- Recherche enseignant --}}
                                <div class="mb-3">
                                    <label class="form-label">Enseignant *</label>
                                    <div class="sw-wrap" id="sw-teacher">
                                        <div class="sw-filters">
                                            <input type="text" placeholder="🔍 Nom, prénom, spécialité…"
                                                oninput="swSearch('teacher', this.value)" autocomplete="off">
                                        </div>
                                        <div class="sw-results" id="sw-teacher-results"></div>
                                        <div class="sw-count" id="sw-teacher-count" style="display:none;"></div>
                                        <div class="sw-selected" id="sw-teacher-selected">
                                            <span class="name" id="sw-teacher-name"></span>
                                            <button type="button" class="clear" onclick="swClear('teacher')">✕</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="teacher_id" id="sw-teacher-id" required>
                                </div>

                                {{-- Recherche classe --}}
                                <div class="mb-3">
                                    <label class="form-label">Classe *</label>
                                    <div class="sw-wrap" id="sw-classe-tc">
                                        <div class="sw-filters">
                                            <input type="text" placeholder="🔍 Nom de la classe…"
                                                oninput="swSearch('classe-tc', this.value)" autocomplete="off">
                                            <select onchange="swSearch('classe-tc', '')">
                                                <option value="">Tous niveaux</option>
                                                @foreach ($sections as $n)
                                                    <option value="{{ $n->id }}">{{ $n->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sw-results" id="sw-classe-tc-results"></div>
                                        <div class="sw-selected" id="sw-classe-tc-selected">
                                            <span class="name" id="sw-classe-tc-name"></span>
                                            <button type="button" class="clear"
                                                onclick="swClear('classe-tc')">✕</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="classe_id" id="sw-classe-tc-id" required>
                                </div>

                                <button type="submit" class="btn-primary w-full justify-center text-xs py-2"
                                    id="btn-tc" disabled style="opacity:.5;cursor:not-allowed;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Affecter
                                </button>
                            </form>

                            {{-- Liste existante --}}
                            <div class="mt-4 space-y-1.5 max-h-48 overflow-y-auto">
                                @forelse($teacherClasseAffectations as $aff)
                                    <div
                                        class="flex items-center justify-between text-xs bg-gray-50 rounded-lg px-2.5 py-1.5 border border-gray-100">
                                        <span class="text-gray-700 truncate">{{ $aff->teacher->prenom }}
                                            {{ $aff->teacher->nom }}</span>
                                        <span class="mx-1 text-gray-300">→</span>
                                        <span class="text-gray-500 truncate">{{ $aff->classe->name }}</span>
                                        <form
                                            action="{{ route('admin.academic.affectations.teacher-classe-destroy', ['teacher' => explode('_', $aff->id)[0], 'classe' => explode('_', $aff->id)[1]]) }}"
                                            method="POST" class="ml-1 shrink-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-300 hover:text-red-500 transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-xs text-center text-gray-300 py-3">Aucune affectation</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- ────────────────────────────────────────
         WIDGET 2 : Enseignant → Niveau
    ──────────────────────────────────────── --}}
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-800">Enseignant → Niveau</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Quel niveau l'enseignant couvre</p>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('admin.academic.affectations.teacher-niveau') }}" method="POST"
                                id="form-tn">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Enseignant *</label>
                                    <div class="sw-wrap" id="sw-teacher-tn">
                                        <div class="sw-filters">
                                            <input type="text" placeholder="🔍 Nom, prénom…"
                                                oninput="swSearch('teacher-tn', this.value)" autocomplete="off">
                                        </div>
                                        <div class="sw-results" id="sw-teacher-tn-results"></div>
                                        <div class="sw-selected" id="sw-teacher-tn-selected">
                                            <span class="name" id="sw-teacher-tn-name"></span>
                                            <button type="button" class="clear"
                                                onclick="swClear('teacher-tn')">✕</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="teacher_id" id="sw-teacher-tn-id" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Niveau *</label>
                                    <select name="niveau_id" class="form-input" required>
                                        <option value="">Choisir…</option>
                                        @foreach ($sections as $n)
                                            <option value="{{ $n->id }}">{{ $n->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn-primary w-full justify-center text-xs py-2"
                                    id="btn-tn" disabled style="opacity:.5;cursor:not-allowed;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Affecter
                                </button>
                            </form>

                            <div class="mt-4 space-y-1.5 max-h-48 overflow-y-auto">
                                @php
                                    $teacherNiveauAff = $teachers->flatMap(
                                        fn($t) => $t->niveaux->map(fn($n) => ['teacher' => $t, 'niveau' => $n]),
                                    );
                                @endphp
                                @forelse($teacherNiveauAff as $aff)
                                    <div
                                        class="flex items-center justify-between text-xs bg-gray-50 rounded-lg px-2.5 py-1.5 border border-gray-100">
                                        <span class="text-gray-700 truncate">{{ $aff['teacher']->prenom }}
                                            {{ $aff['teacher']->nom }}</span>
                                        <span class="mx-1 text-gray-300">→</span>
                                        <span class="text-gray-500 truncate">{{ $aff['niveau']->name }}</span>
                                        <form
                                            action="{{ route('admin.academic.affectations.teacher-niveau-destroy', ['teacher' => $aff['teacher']->id, 'niveau' => $aff['niveau']->id]) }}"
                                            method="POST" class="ml-1 shrink-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-300 hover:text-red-500 transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-xs text-center text-gray-300 py-3">Aucune affectation</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- ────────────────────────────────────────
         WIDGET 3 : Élève → Classe
    ──────────────────────────────────────── --}}
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-800">Élève → Classe</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Inscrire un élève dans une classe</p>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('admin.academic.affectations.eleve-classe') }}" method="POST"
                                id="form-ec">
                                @csrf

                                {{-- Recherche apprenant --}}
                                <div class="mb-3">
                                    <label class="form-label">Élève *</label>
                                    <div class="sw-wrap" id="sw-apprenant">
                                        <div class="sw-filters">
                                            <input type="text" placeholder="🔍 Nom, prénom, matricule…"
                                                oninput="swSearch('apprenant', this.value)" autocomplete="off">
                                            <select onchange="swSearch('apprenant', '')">
                                                <option value="">Tous niveaux</option>
                                                @foreach ($sections as $n)
                                                    <option value="{{ $n->id }}" data-filter="niveau_id">
                                                        {{ $n->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sw-results" id="sw-apprenant-results"></div>
                                        <div class="sw-count" id="sw-apprenant-count" style="display:none;"></div>
                                        <div class="sw-selected" id="sw-apprenant-selected">
                                            <span class="name" id="sw-apprenant-name"></span>
                                            <button type="button" class="clear"
                                                onclick="swClear('apprenant')">✕</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="apprenant_id" id="sw-apprenant-id" required>
                                </div>

                                {{-- Recherche classe destination --}}
                                <div class="mb-3">
                                    <label class="form-label">Classe de destination *</label>
                                    <div class="sw-wrap" id="sw-classe-ec">
                                        <div class="sw-filters">
                                            <input type="text" placeholder="🔍 Nom de la classe…"
                                                oninput="swSearch('classe-ec', this.value)" autocomplete="off">
                                            <select onchange="swSearch('classe-ec', '')">
                                                <option value="">Tous niveaux</option>
                                                @foreach ($sections as $n)
                                                    <option value="{{ $n->id }}">{{ $n->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sw-results" id="sw-classe-ec-results"></div>
                                        <div class="sw-selected" id="sw-classe-ec-selected">
                                            <span class="name" id="sw-classe-ec-name"></span>
                                            <button type="button" class="clear"
                                                onclick="swClear('classe-ec')">✕</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="classe_id" id="sw-classe-ec-id" required>
                                </div>

                                <button type="submit" class="btn-primary w-full justify-center text-xs py-2"
                                    id="btn-ec" disabled style="opacity:.5;cursor:not-allowed;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Inscrire dans la classe
                                </button>
                            </form>

                            {{-- Liste affectations élèves --}}
                            <div class="mt-4 space-y-1.5 max-h-48 overflow-y-auto">
                                @forelse($eleveClasseAffectations->take(15) as $aff)
                                    @if ($aff->apprenant && $aff->classe)
                                        <div
                                            class="flex items-center justify-between text-xs bg-gray-50 rounded-lg px-2.5 py-1.5 border border-gray-100">
                                            <span class="text-gray-700 truncate max-w-[80px]">
                                                {{ $aff->apprenant->prenom }} {{ $aff->apprenant->nom }}
                                            </span>
                                            <span class="mx-1 text-gray-300">→</span>
                                            <span
                                                class="text-gray-500 truncate max-w-[60px]">{{ $aff->classe->name }}</span>
                                            <form
                                                action="{{ route('admin.academic.affectations.eleve-classe-destroy', $aff->apprenant->id) }}"
                                                method="POST" class="ml-1 shrink-0"
                                                onsubmit="return confirm('Retirer {{ $aff->apprenant->prenom }} de sa classe ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-gray-300 hover:text-red-500 transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @empty
                                    <p class="text-xs text-center text-gray-300 py-3">Aucun élève inscrit</p>
                                @endforelse
                                @if ($eleveClasseAffectations->count() > 15)
                                    <p class="text-xs text-center text-gray-400 py-1">
                                        + {{ $eleveClasseAffectations->count() - 15 }} autre(s)
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>{{-- /grid --}}

                {{-- ════════════ SCRIPT AJAX SEARCH WIDGETS ════════════ --}}
                <script>
                    const SW_ROUTES = {
                        teacher: "{{ route('admin.academic.search.teachers') }}",
                        'teacher-tn': "{{ route('admin.academic.search.teachers') }}",
                        apprenant: "{{ route('admin.academic.search.apprenants') }}",
                        'classe-tc': "{{ route('admin.academic.search.classes') }}",
                        'classe-ec': "{{ route('admin.academic.search.classes') }}",
                    };

                    // Couleurs par type
                    const SW_COLORS = {
                        teacher: {
                            bg: '#dbeafe',
                            color: '#1d4ed8'
                        },
                        'teacher-tn': {
                            bg: '#dbeafe',
                            color: '#1d4ed8'
                        },
                        apprenant: {
                            bg: '#dcfce7',
                            color: '#166534'
                        },
                        'classe-tc': {
                            bg: '#fef3c7',
                            color: '#b45309'
                        },
                        'classe-ec': {
                            bg: '#fef3c7',
                            color: '#b45309'
                        },
                    };

                    // Quel bouton activer selon quelles sélections
                    const SW_BUTTONS = {
                        'btn-tc': ['sw-teacher-id', 'sw-classe-tc-id'],
                        'btn-tn': ['sw-teacher-tn-id'],
                        'btn-ec': ['sw-apprenant-id', 'sw-classe-ec-id'],
                    };

                    let swDebounce = {};

                    function swSearch(type, q) {
                        clearTimeout(swDebounce[type]);
                        swDebounce[type] = setTimeout(() => _swFetch(type, q), 280);
                    }

                    function _swFetch(type, q) {
                        const url = SW_ROUTES[type];
                        const wrap = document.getElementById('sw-' + type);
                        if (!url || !wrap) return;

                        // Récupérer les filtres depuis les selects du widget
                        const selects = wrap.querySelectorAll('.sw-filters select');
                        const params = new URLSearchParams();
                        if (q) params.set('q', q);
                        selects.forEach(sel => {
                            if (sel.value) {
                                // Déduire le nom du param depuis data-filter ou position
                                const filterName = sel.dataset.filter || (type.startsWith('classe') ? 'niveau_id' :
                                    'niveau_id');
                                params.set(filterName, sel.value);
                            }
                        });

                        fetch(url + '?' + params.toString(), {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(r => r.json())
                            .then(({
                                data,
                                total
                            }) => _swRender(type, data, total));
                    }

                    function _swRender(type, data, total) {
                        const container = document.getElementById('sw-' + type + '-results');
                        const countEl = document.getElementById('sw-' + type + '-count');
                        if (!container) return;

                        container.classList.add('show');
                        const colors = SW_COLORS[type] || {
                            bg: '#f3f4f6',
                            color: '#374151'
                        };

                        if (countEl) {
                            countEl.style.display = 'block';
                            countEl.textContent = total > 50 ?
                                total + ' résultats — affinez pour voir plus' :
                                total + ' résultat(s)';
                        }

                        if (!data || !data.length) {
                            container.innerHTML = '<div class="sw-empty">Aucun résultat</div>';
                            return;
                        }

                        container.innerHTML = data.map(item => {
                            const label = item.label || (item.prenom ? item.prenom + ' ' + item.nom : item.name);
                            const sub = [item.specialite, item.niveau, item.filiere, item.matricule]
                                .filter(Boolean).join(' · ');
                            const init = (item.prenom || item.name || '?').charAt(0).toUpperCase() +
                                (item.nom || '').charAt(0).toUpperCase();

                            return `<div class="sw-item" onclick="swSelect('${type}', ${item.id}, '${escJ(label)}')">
            <div class="initials" style="background:${colors.bg};color:${colors.color};">${init}</div>
            <div>
                <div class="lbl">${label}</div>
                ${sub ? '<div class="sub">' + sub + '</div>' : ''}
            </div>
        </div>`;
                        }).join('');
                    }

                    function swSelect(type, id, label) {
                        // Cacher résultats
                        const res = document.getElementById('sw-' + type + '-results');
                        if (res) res.classList.remove('show');

                        const countEl = document.getElementById('sw-' + type + '-count');
                        if (countEl) countEl.style.display = 'none';

                        // Afficher la sélection
                        const selBox = document.getElementById('sw-' + type + '-selected');
                        const selName = document.getElementById('sw-' + type + '-name');
                        if (selBox) selBox.classList.add('show');
                        if (selName) selName.textContent = label;

                        // Stocker la valeur
                        const hiddenInput = document.getElementById('sw-' + type + '-id');
                        if (hiddenInput) hiddenInput.value = id;

                        // Activer les boutons concernés
                        _swCheckButtons();
                    }

                    function swClear(type) {
                        const hiddenInput = document.getElementById('sw-' + type + '-id');
                        if (hiddenInput) hiddenInput.value = '';

                        const selBox = document.getElementById('sw-' + type + '-selected');
                        if (selBox) selBox.classList.remove('show');

                        const res = document.getElementById('sw-' + type + '-results');
                        if (res) {
                            res.innerHTML = '';
                            res.classList.remove('show');
                        }

                        // Vider le champ texte
                        const wrap = document.getElementById('sw-' + type);
                        if (wrap) {
                            const input = wrap.querySelector('.sw-filters input');
                            if (input) input.value = '';
                        }

                        _swCheckButtons();
                    }

                    function _swCheckButtons() {
                        Object.entries(SW_BUTTONS).forEach(([btnId, fieldIds]) => {
                            const btn = document.getElementById(btnId);
                            if (!btn) return;
                            const allFilled = fieldIds.every(fId => {
                                const el = document.getElementById(fId);
                                return el && el.value;
                            });
                            btn.disabled = !allFilled;
                            btn.style.opacity = allFilled ? '1' : '.5';
                            btn.style.cursor = allFilled ? 'pointer' : 'not-allowed';
                        });
                    }

                    function escJ(str) {
                        return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
                    }

                    // Fermer les résultats si clic ailleurs
                    document.addEventListener('click', function(e) {
                        document.querySelectorAll('.sw-results.show').forEach(el => {
                            if (!el.closest('.sw-wrap').contains(e.target)) {
                                el.classList.remove('show');
                            }
                        });
                    });
                </script>


                {{-- ═══════════════ MODALS ═══════════════ --}}

                {{-- Edit Classe --}}
                <div class="modal-backdrop" id="editClassModal">
                    <div class="modal-box">
                        <div class="modal-header">
                            <h2 class="text-base font-semibold text-gray-900">Modifier la classe</h2>
                            <button onclick="closeModal('editClassModal')" class="btn-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form id="editClassForm" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body space-y-4">
                                <div>
                                    <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="ecName" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">Code</label>
                                    <input type="text" name="code" id="ecCode" class="form-input"
                                        maxlength="20">
                                </div>
                                <div>
                                    <label class="form-label">Niveau</label>
                                    <select name="niveau_id" id="ecNiveau" class="form-input">
                                        <option value="">— Aucun —</option>
                                        @foreach ($sections as $n)
                                            <option value="{{ $n->id }}">{{ $n->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Filière</label>
                                    <select name="filiere_id" id="ecFiliere" class="form-input">
                                        <option value="">— Aucune —</option>
                                        @foreach ($filieres as $f)
                                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeModal('editClassModal')"
                                    class="btn-secondary">Annuler</button>
                                <button type="submit" class="btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Edit Niveau --}}
                <div class="modal-backdrop" id="editNiveauModal">
                    <div class="modal-box">
                        <div class="modal-header">
                            <h2 class="text-base font-semibold text-gray-900">Modifier le niveau</h2>
                            <button onclick="closeModal('editNiveauModal')" class="btn-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form id="editNiveauForm" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body space-y-4">
                                <div>
                                    <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="enName" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">Cycle</label>
                                    <select name="cycle" id="enCycle" class="form-input">
                                        <option value="">— Choisir —</option>
                                        <option value="primaire">Primaire</option>
                                        <option value="secondaire">Secondaire</option>
                                        <option value="universite">Université</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeModal('editNiveauModal')"
                                    class="btn-secondary">Annuler</button>
                                <button type="submit" class="btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Edit Filière --}}
                <div class="modal-backdrop" id="editFiliereModal">
                    <div class="modal-box">
                        <div class="modal-header">
                            <h2 class="text-base font-semibold text-gray-900">Modifier la filière</h2>
                            <button onclick="closeModal('editFiliereModal')" class="btn-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form id="editFiliereForm" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body space-y-4">
                                <div>
                                    <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="efName" class="form-input" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeModal('editFiliereModal')"
                                    class="btn-secondary">Annuler</button>
                                <button type="submit" class="btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Edit Matière --}}
                <div class="modal-backdrop" id="editMatiereModal">
                    <div class="modal-box">
                        <div class="modal-header">
                            <h2 class="text-base font-semibold text-gray-900">Modifier la matière</h2>
                            <button onclick="closeModal('editMatiereModal')" class="btn-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form id="editMatiereForm" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body space-y-4">
                                <div>
                                    <label class="form-label">Nom <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="emName" class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label">Coefficient <span class="text-red-500">*</span></label>
                                    <input type="number" name="coefficient" id="emCoef" class="form-input"
                                        min="0.5" step="0.5" required>
                                </div>
                                <div>
                                    <label class="form-label">Classe</label>
                                    <select name="class_id" id="emClasse" class="form-input">
                                        <option value="">— Aucune —</option>
                                        @foreach ($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Enseignant</label>
                                    <select name="teacher_id" id="emTeacher" class="form-input">
                                        <option value="">— Aucun —</option>
                                        @foreach ($teachers as $t)
                                            <option value="{{ $t->id }}">{{ $t->prenom }}
                                                {{ $t->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeModal('editMatiereModal')"
                                    class="btn-secondary">Annuler</button>
                                <button type="submit" class="btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Confirm Delete --}}
                <div class="modal-backdrop" id="confirmDeleteModal">
                    <div class="modal-box" style="max-width:380px">
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-base font-semibold text-gray-900">
                                        Supprimer <span id="deleteItemName" class="text-red-600"></span> ?
                                    </h2>
                                    <p class="text-sm text-gray-500 mt-1">Cette action est irréversible.</p>
                                </div>
                            </div>
                            <form id="deleteForm" method="POST" class="mt-5">
                                @csrf @method('DELETE')
                                <div class="flex gap-3 justify-end">
                                    <button type="button" onclick="closeModal('confirmDeleteModal')"
                                        class="btn-secondary">Annuler</button>
                                    <button type="submit" class="btn-danger">Supprimer définitivement</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @endsection

            @push('scripts')
                <script>
                    // ==================== TABS ====================
                    function switchTab(id) {
                        document.querySelectorAll('.acad-tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === id));
                        document.querySelectorAll('.acad-panel').forEach(p => p.classList.toggle('active', p.id === `panel-${id}`));
                        history.replaceState(null, '', '#' + id);
                    }
                    document.addEventListener('DOMContentLoaded', () => {
                        const hash = window.location.hash.replace('#', '');
                        const valid = ['classes', 'niveaux', 'filieres', 'matieres', 'affectations'];
                        if (valid.includes(hash)) switchTab(hash);
                    });

                    // ==================== MODALS ====================
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

                    // ==================== EDIT MODALS ====================
                    function openEditClassModal(id, name, code, niveauId, filiereId) {
                        document.getElementById('editClassForm').action = `/admin/academic/classes/${id}`;
                        document.getElementById('ecName').value = name;
                        document.getElementById('ecCode').value = code || '';
                        document.getElementById('ecNiveau').value = niveauId || '';
                        document.getElementById('ecFiliere').value = filiereId || '';
                        openModal('editClassModal');
                    }

                    function openEditNiveauModal(id, name, cycle) {
                        document.getElementById('editNiveauForm').action = `/admin/academic/niveaux/${id}`;
                        document.getElementById('enName').value = name;
                        document.getElementById('enCycle').value = cycle || '';
                        openModal('editNiveauModal');
                    }

                    function openEditFiliereModal(id, name) {
                        document.getElementById('editFiliereForm').action = `/admin/academic/filieres/${id}`;
                        document.getElementById('efName').value = name;
                        openModal('editFiliereModal');
                    }

                    function openEditMatiereModal(id, name, coef, classeId, teacherId) {
                        document.getElementById('editMatiereForm').action = `/admin/academic/matieres/${id}`;
                        document.getElementById('emName').value = name;
                        document.getElementById('emCoef').value = coef;
                        document.getElementById('emClasse').value = classeId || '';
                        document.getElementById('emTeacher').value = teacherId || '';
                        openModal('editMatiereModal');
                    }

                    // ==================== DELETE ====================
                    function confirmDelete(url, name) {
                        document.getElementById('deleteForm').action = url;
                        document.getElementById('deleteItemName').textContent = name;
                        openModal('confirmDeleteModal');
                    }

                    // ==================== SEARCH ====================
                    function filterList(containerId, query) {
                        const q = query.toLowerCase();
                        document.querySelectorAll(`#${containerId} .data-card`).forEach(card => {
                            card.style.display = card.dataset.search.includes(q) ? '' : 'none';
                        });
                    }
                </script>
            @endpush
