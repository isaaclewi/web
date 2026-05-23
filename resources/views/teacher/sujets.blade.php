{{-- ================================================================
    Vue Enseignant — Envoi de sujets d'examens
    resources/views/teacher/sujets.blade.php
    Route : GET /teacher/sujets  →  teacher.sujets.index
    ================================================================ --}}
@extends('teacher.master')

@section('title', 'Mes sujets')
@section('page-title', 'Sujets d\'examens')

@push('styles')
    <style>
        .sj-wrap {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        /* ── Hero ── */
        .sj-hero {
            background: #0f172a;
            border-radius: .875rem;
            padding: 1.75rem 2rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .sj-hero::after {
            content: '';
            position: absolute;
            right: -60px;
            top: -60px;
            width: 220px;
            height: 220px;
            border: 40px solid rgba(255, 255, 255, .04);
            border-radius: 50%;
            pointer-events: none;
        }

        .sj-hero h1 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.02em;
        }

        .sj-hero p {
            font-size: .82rem;
            color: #94a3b8;
            margin-top: .3rem;
        }

        .sj-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .1);
            color: #cbd5e1;
            font-size: .7rem;
            font-weight: 600;
            padding: .25rem .7rem;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: .875rem;
        }

        /* ── Stats ── */
        .sj-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: .875rem;
            margin-bottom: 1.5rem;
        }

        .sj-kpi {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            padding: 1.1rem 1.25rem;
        }

        .sj-kpi-label {
            font-size: .68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            margin-bottom: .3rem;
        }

        .sj-kpi-val {
            font-size: 1.7rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1;
        }

        .sj-kpi-sub {
            font-size: .72rem;
            color: #94a3b8;
            margin-top: .25rem;
        }

        /* ── Card ── */
        .sj-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .875rem;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .sj-card-hd {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .sj-card-title {
            font-size: .875rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .sj-table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            border-radius: .75rem;
        }

        /* Empêche la table de se casser */
        .sj-table {
            min-width: 950px;
            /* ajuste selon ton contenu */
        }

        .sj-table-wrap::after {
            content: "→ Glisser pour voir plus";
            position: absolute;
            right: 10px;
            bottom: 8px;
            font-size: 0.7rem;
            color: #94a3b8;
        }

        .sj-table-wrap {
            position: relative;
        }

        .sj-icon {
            width: 28px;
            height: 28px;
            border-radius: .375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ── Form ── */
        .sj-form {
            padding: 1.5rem;
        }

        .sj-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .sj-row {
            margin-bottom: 1rem;
        }

        .sj-row label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: .35rem;
        }

        .sj-row input,
        .sj-row select,
        .sj-row textarea {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
            padding: .55rem .875rem;
            font-size: .84rem;
            color: #0f172a;
            outline: none;
            transition: border-color .15s;
            background: #fff;
        }

        .sj-row input:focus,
        .sj-row select:focus,
        .sj-row textarea:focus {
            border-color: #94a3b8;
        }

        .sj-row textarea {
            resize: vertical;
            min-height: 76px;
        }

        /* Drop zone */
        .sj-drop {
            border: 2px dashed #cbd5e1;
            border-radius: .625rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            background: #f8fafc;
        }

        .sj-drop:hover,
        .sj-drop.dragover {
            border-color: #475569;
            background: #f1f5f9;
        }

        .sj-drop-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto .75rem;
            background: #f1f5f9;
            border-radius: .5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sj-drop-icon svg {
            width: 22px;
            height: 22px;
            color: #64748b;
        }

        .sj-drop p {
            font-size: .82rem;
            color: #64748b;
        }

        .sj-drop strong {
            color: #0f172a;
        }

        .sj-drop small {
            font-size: .72rem;
            color: #94a3b8;
            display: block;
            margin-top: .25rem;
        }

        .sj-file-list {
            margin-top: .875rem;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .sj-file-item {
            display: flex;
            align-items: center;
            gap: .625rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
            padding: .5rem .75rem;
            font-size: .8rem;
        }

        .sj-file-item svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            color: #475569;
        }

        .sj-file-name {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #334155;
            font-weight: 500;
        }

        .sj-file-size {
            color: #94a3b8;
            font-size: .72rem;
            flex-shrink: 0;
        }

        .sj-file-rm {
            width: 20px;
            height: 20px;
            border: none;
            background: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .sj-file-rm:hover {
            color: #dc2626;
        }

        .sj-file-rm svg {
            width: 14px;
            height: 14px;
        }

        /* Boutons */
        .sj-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .55rem 1.1rem;
            border-radius: .5rem;
            font-size: .82rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .15s;
            text-decoration: none;
        }

        .sj-btn-primary {
            background: #0f172a;
            color: #fff;
        }

        .sj-btn-primary:hover {
            background: #1e293b;
        }

        .sj-btn-ghost {
            background: #f1f5f9;
            color: #0f172a;
        }

        .sj-btn-ghost:hover {
            background: #e2e8f0;
        }

        .sj-btn-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .sj-btn-danger:hover {
            background: #fecaca;
        }

        /* Badge */
        .sj-badge {
            display: inline-block;
            padding: .18rem .55rem;
            border-radius: 99px;
            font-size: .68rem;
            font-weight: 600;
        }

        .sj-badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .sj-badge-green {
            background: #d1fae5;
            color: #065f46;
        }

        .sj-badge-gold {
            background: #fef3c7;
            color: #92400e;
        }

        .sj-badge-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .sj-badge-slate {
            background: #f1f5f9;
            color: #475569;
        }

        .sj-badge-violet {
            background: #ede9fe;
            color: #5b21b6;
        }

        /* Table */
        .sj-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .8rem;
        }

        .sj-table th {
            padding: .6rem 1.1rem;
            background: #f8fafc;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .sj-table td {
            padding: .8rem 1.1rem;
            border-bottom: 1px solid #f8fafc;
            vertical-align: middle;
            color: #334155;
        }

        .sj-table tr:last-child td {
            border-bottom: none;
        }

        .sj-table tr:hover td {
            background: #fafafa;
        }

        .sj-empty {
            padding: 3rem 2rem;
            text-align: center;
            color: #94a3b8;
            font-size: .84rem;
        }

        /* Flash */
        .sj-flash {
            padding: .75rem 1.1rem;
            border-radius: .5rem;
            font-size: .84rem;
            font-weight: 500;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .sj-flash-ok {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .sj-flash-err {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* Progress upload */
        .sj-progress {
            height: 4px;
            background: #e2e8f0;
            border-radius: 99px;
            overflow: hidden;
            margin-top: .375rem;
            display: none;
        }

        .sj-progress-fill {
            height: 100%;
            background: #0f172a;
            border-radius: 99px;
            transition: width .3s;
        }

        @media(max-width:640px) {
            .sj-grid {
                grid-template-columns: 1fr;
            }

            .sj-hero {
                padding: 1.25rem;
            }

            .sj-kpi-val {
                font-size: 1.3rem;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $typeLabels = [
            'controle' => 'Contrôle',
            'examen' => 'Examen',
            'tp' => 'Travaux pratiques',
            'projet' => 'Projet',
        ];
        $statutColors = [
            'en_attente' => 'sj-badge-gold',
            'recu' => 'sj-badge-blue',
            'valide' => 'sj-badge-green',
            'rejete' => 'sj-badge-red',
            'archive' => 'sj-badge-slate',
        ];
        $statutLabels = [
            'en_attente' => 'En attente',
            'recu' => 'Reçu',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            'archive' => 'Archivé',
        ];
    @endphp

    <div class="sj-wrap">

        {{-- Flash --}}
        @if (session('success'))
            <div class="sj-flash sj-flash-ok">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="sj-flash sj-flash-err">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ── Hero ── --}}
        <div class="sj-hero">
            <div class="sj-chip">
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Sujets d'examens
            </div>
            <h1>Envoi de sujets à l'administration</h1>
            <p>Déposez vos sujets directement — l'administration est notifiée instantanément.</p>
        </div>

        {{-- ── KPIs ── --}}
        <div class="sj-kpis">
            <div class="sj-kpi">
                <div class="sj-kpi-label">Total envoyés</div>
                <div class="sj-kpi-val">{{ $stats['total'] ?? 0 }}</div>
                <div class="sj-kpi-sub">tous types</div>
            </div>
            <div class="sj-kpi">
                <div class="sj-kpi-label">En attente</div>
                <div class="sj-kpi-val" style="color:#d97706;">{{ $stats['en_attente'] ?? 0 }}</div>
                <div class="sj-kpi-sub">non encore validés</div>
            </div>
            <div class="sj-kpi">
                <div class="sj-kpi-label">Validés</div>
                <div class="sj-kpi-val" style="color:#059669;">{{ $stats['valide'] ?? 0 }}</div>
                <div class="sj-kpi-sub">par l'administration</div>
            </div>
            <div class="sj-kpi">
                <div class="sj-kpi-label">Ce mois</div>
                <div class="sj-kpi-val">{{ $stats['ce_mois'] ?? 0 }}</div>
                <div class="sj-kpi-sub">{{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
         FORMULAIRE D'ENVOI
    ══════════════════════════════════════════════ --}}
        <div class="sj-card">
            <div class="sj-card-hd">
                <div class="sj-card-title">
                    <div class="sj-icon" style="background:#dbeafe;">
                        <svg width="15" height="15" fill="none" stroke="#1d4ed8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </div>
                    Envoyer un nouveau sujet
                </div>
            </div>

            <div class="sj-form">
                <form method="POST" action="{{ route('teacher.sujets.store') }}" enctype="multipart/form-data"
                    id="sujetForm">
                    @csrf

                    <div class="sj-grid">
                        <div class="sj-row">
                            <label>Titre du sujet *</label>
                            <input type="text" name="titre" placeholder="Ex : Examen final — Mathématiques" required
                                value="{{ old('titre') }}">
                            @error('titre')
                                <span style="font-size:.72rem;color:#dc2626;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="sj-row">
                            <label>Type d'évaluation *</label>
                            <select name="type" required>
                                <option value="">— Choisir —</option>
                                @foreach ($typeLabels as $val => $label)
                                    <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <span style="font-size:.72rem;color:#dc2626;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="sj-grid">
                        <div class="sj-row">
                            <label>Matière *</label>
                            <select name="subject_id" required>
                                <option value="">— Choisir une matière —</option>
                                @foreach ($subjects as $sub)
                                    <option value="{{ $sub->id }}"
                                        {{ old('subject_id') == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}{{ $sub->classe ? ' — ' . $sub->classe->name : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <span style="font-size:.72rem;color:#dc2626;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="sj-row">
                            <label>Classe concernée</label>
                            <select name="classe_id">
                                <option value="">— Toutes mes classes —</option>
                                @foreach ($classes as $cl)
                                    <option value="{{ $cl->id }}"
                                        {{ old('classe_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sj-grid">
                        <div class="sj-row">
                            <label>Date prévue de l'évaluation</label>
                            <input type="date" name="date_evaluation" value="{{ old('date_evaluation') }}">
                        </div>
                        <div class="sj-row">
                            <label>Durée (minutes)</label>
                            <input type="number" name="duree_minutes" min="15" max="480" placeholder="Ex : 120"
                                value="{{ old('duree_minutes') }}">
                        </div>
                    </div>

                    <div class="sj-row">
                        <label>Instructions / remarques pour l'administration</label>
                        <textarea name="instructions" placeholder="Nombre de copies, conditions particulières, matériel autorisé…">{{ old('instructions') }}</textarea>
                    </div>

                    {{-- Drop zone fichier --}}
                    <div class="sj-row">
                        <label>Fichier(s) du sujet * <span style="font-weight:400;color:#94a3b8;">(PDF, Word, images — max
                                10 Mo/fichier)</span></label>
                        <div class="sj-drop" id="dropZone" onclick="document.getElementById('fileInput').click()">
                            <div class="sj-drop-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p><strong>Cliquez pour choisir</strong> ou glissez-déposez vos fichiers</p>
                            <small>PDF, DOCX, XLSX, JPG, PNG · Max 10 Mo par fichier · Jusqu'à 5 fichiers</small>
                            <input type="file" id="fileInput" name="fichiers[]" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="display:none;"
                                onchange="handleFiles(this.files)">
                        </div>
                        <div class="sj-progress" id="uploadProgress">
                            <div class="sj-progress-fill" id="progressFill" style="width:0%"></div>
                        </div>
                        <div class="sj-file-list" id="fileList"></div>
                        @error('fichiers')
                            <span
                                style="font-size:.72rem;color:#dc2626;display:block;margin-top:.3rem;">{{ $message }}</span>
                        @enderror
                        @error('fichiers.*')
                            <span
                                style="font-size:.72rem;color:#dc2626;display:block;margin-top:.3rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div
                        style="display:flex; gap:.75rem; justify-content:flex-end; padding-top:.5rem; border-top:1px solid #f1f5f9; margin-top:.5rem;">
                        <button type="reset" class="sj-btn sj-btn-ghost" onclick="clearFiles()">Annuler</button>
                        <button type="submit" class="sj-btn sj-btn-primary" id="submitBtn">
                            <svg width="14" height="14" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Envoyer à l'administration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
         HISTORIQUE DES ENVOIS
    ══════════════════════════════════════════════ --}}
        <div class="sj-card">
            <div class="sj-card-hd">
                <div class="sj-card-title">
                    <div class="sj-icon" style="background:#f1f5f9;">
                        <svg width="15" height="15" fill="none" stroke="#475569" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    Mes envois récents
                </div>
                {{-- Filtre type --}}
                <form method="GET" style="display:flex; gap:.5rem; align-items:center;">
                    <select name="type_filter" onchange="this.form.submit()"
                        style="border:1px solid #e2e8f0; border-radius:.4rem; padding:.35rem .6rem; font-size:.78rem; color:#334155; outline:none; background:#fff;">
                        <option value="">Tous les types</option>
                        @foreach ($typeLabels as $val => $label)
                            <option value="{{ $val }}" {{ request('type_filter') === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="sj-table-wrap">
                <table class="sj-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Matière</th>
                            <th>Classe</th>
                            <th>Date éval.</th>
                            <th>Envoyé le</th>
                            <th>Statut</th>
                            <th>Feedback</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sujets as $sujet)
                            <tr>
                                <td style="font-weight:600; max-width:180px;">
                                    <span
                                        style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;">{{ $sujet->titre }}</span>
                                </td>
                                <td>
                                    <span
                                        class="sj-badge sj-badge-slate">{{ $typeLabels[$sujet->type] ?? $sujet->type }}</span>
                                </td>
                                <td>{{ $sujet->subject->name ?? '—' }}</td>
                                <td>{{ $sujet->classe->name ?? 'Toutes' }}</td>
                                <td>{{ $sujet->date_evaluation ? \Carbon\Carbon::parse($sujet->date_evaluation)->format('d/m/Y') : '—' }}
                                </td>
                                <td>{{ $sujet->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="sj-badge {{ $statutColors[$sujet->statut] ?? 'sj-badge-slate' }}">
                                        {{ $statutLabels[$sujet->statut] ?? $sujet->statut }}
                                    </span>
                                </td>
                                <td>
                                    @if ($sujet->feedback_admin)
                                        <span title="{{ $sujet->feedback_admin }}"
                                            style="cursor:help; color:#475569; font-style:italic; font-size:.78rem;">
                                            {{ \Str::limit($sujet->feedback_admin, 40) }}
                                        </span>
                                    @else
                                        <span style="color:#cbd5e1; font-size:.75rem;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex; gap:.35rem;">
                                        @foreach ($sujet->fichiers as $f)
                                            <a href="{{ route('teacher.sujets.download', $f->id) }}"
                                                class="sj-btn sj-btn-ghost" style="padding:.3rem .5rem; font-size:.72rem;"
                                                title="Télécharger {{ $f->nom_original }}">
                                                <svg width="13" height="13" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        @endforeach
                                        @if ($sujet->statut === 'en_attente')
                                            <form method="POST"
                                                action="{{ route('teacher.sujets.destroy', $sujet->id) }}"
                                                onsubmit="return confirm('Retirer cet envoi ?');" style="margin:0;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="sj-btn sj-btn-danger"
                                                    style="padding:.3rem .5rem; font-size:.72rem;">
                                                    <svg width="13" height="13" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="sj-empty">Aucun sujet envoyé pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sujets->hasPages())
                <div style="padding:.875rem 1.1rem; border-top:1px solid #f1f5f9;">
                    {{ $sujets->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        let selectedFiles = [];

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');

        // Drag & drop
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        function handleFiles(files) {
            const allowed = ['application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/jpeg', 'image/png'
            ];
            const maxSize = 10 * 1024 * 1024;

            Array.from(files).forEach(file => {
                if (selectedFiles.length >= 5) return;
                if (!allowed.includes(file.type) && !file.name.match(/\.(pdf|docx?|xlsx?|jpe?g|png)$/i)) return;
                if (file.size > maxSize) {
                    alert(`${file.name} dépasse 10 Mo.`);
                    return;
                }
                if (selectedFiles.find(f => f.name === file.name && f.size === file.size)) return;
                selectedFiles.push(file);
            });
            renderFileList();
            syncFileInput();
        }

        function renderFileList() {
            fileList.innerHTML = '';
            selectedFiles.forEach((file, idx) => {
                const size = file.size < 1024 * 1024 ?
                    (file.size / 1024).toFixed(1) + ' Ko' :
                    (file.size / 1024 / 1024).toFixed(1) + ' Mo';
                const ext = file.name.split('.').pop().toUpperCase();
                const item = document.createElement('div');
                item.className = 'sj-file-item';
                item.innerHTML = `
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;color:#475569;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="sj-file-name">${file.name}</span>
            <span class="sj-file-size">${ext} · ${size}</span>
            <button type="button" class="sj-file-rm" onclick="removeFile(${idx})">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
                fileList.appendChild(item);
            });
        }

        function removeFile(idx) {
            selectedFiles.splice(idx, 1);
            renderFileList();
            syncFileInput();
        }

        function syncFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
        }

        function clearFiles() {
            selectedFiles = [];
            renderFileList();
            syncFileInput();
        }

        // Soumission avec progress visuel
        document.getElementById('sujetForm').addEventListener('submit', function() {
            if (selectedFiles.length === 0) return;
            const btn = document.getElementById('submitBtn');
            const prog = document.getElementById('uploadProgress');
            btn.disabled = true;
            btn.innerHTML =
                '<svg class="spin" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Envoi en cours…';
            prog.style.display = 'block';
            let w = 0;
            const iv = setInterval(() => {
                w = Math.min(w + Math.random() * 15, 92);
                document.getElementById('progressFill').style.width = w + '%';
            }, 200);
        });
    </script>
    <style>
        .spin {
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
