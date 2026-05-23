@extends('admin.master')

@section('title', 'Mon établissement')

@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&family=Satoshi:wght@300;400;500;700&display=swap');

        :root {
            --ink: #0f1623;
            --ink-soft: #3d4a62;
            --ink-muted: #8494ad;
            --surface: #ffffff;
            --surface-2: #f4f6fa;
            --surface-3: #eaecf3;
            --line: #e2e6ef;
            --accent: #2563eb;
            --accent-lt: #eff4ff;
            --accent-glow: rgba(37, 99, 235, 0.15);
            --ok: #059669;
            --warn: #d97706;
            --radius: 14px;
            --shadow-sm: 0 1px 4px rgba(15, 22, 35, .06), 0 4px 16px rgba(15, 22, 35, .07);
            --shadow-md: 0 2px 8px rgba(15, 22, 35, .08), 0 12px 32px rgba(15, 22, 35, .10);
        }

        .is-page * {
            font-family: 'Satoshi', sans-serif;
            box-sizing: border-box;
        }

        .is-page {
            padding: 2rem;
            background: var(--surface-2);
            min-height: 100vh;
        }

        /* ── HERO HEADER ─────────────────────────── */
        .is-hero {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 2rem 2.5rem;
            margin-bottom: 1.75rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
            position: relative;
            overflow: hidden;
        }

        .is-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), #60a5fa, #34d399);
        }

        .is-hero-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .is-logo-box {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: var(--accent-lt);
            border: 2px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .is-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .is-logo-box span {
            font-family: 'Clash Display', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
        }

        .is-hero h1 {
            font-family: 'Clash Display', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ink);
            margin: 0 0 .2rem;
        }

        .is-hero-meta {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .is-chip {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .25rem .65rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 500;
        }

        .chip-type {
            background: #eff4ff;
            color: var(--accent);
            border: 1px solid #bfdbfe;
        }

        .chip-year {
            background: #f0fdf4;
            color: var(--ok);
            border: 1px solid #bbf7d0;
        }

        .chip-code {
            background: var(--surface-2);
            color: var(--ink-soft);
            border: 1px solid var(--line);
            font-family: monospace;
        }

        .chip-status-on {
            background: #f0fdf4;
            color: var(--ok);
            border: 1px solid #bbf7d0;
        }

        .chip-status-off {
            background: #fff7ed;
            color: var(--warn);
            border: 1px solid #fed7aa;
        }

        /* ── TABS ─────────────────────────────────── */
        .is-tabs-bar {
            display: flex;
            gap: .25rem;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: .35rem;
            margin-bottom: 1.75rem;
            box-shadow: var(--shadow-sm);
            overflow-x: auto;
            scrollbar-width: none;
        }

        .is-tabs-bar::-webkit-scrollbar {
            display: none;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem 1rem;
            border-radius: 9px;
            font-size: .82rem;
            font-weight: 500;
            white-space: nowrap;
            border: none;
            cursor: pointer;
            background: transparent;
            color: var(--ink-muted);
            transition: background .15s, color .15s;
        }

        .tab-btn svg {
            flex-shrink: 0;
            opacity: .6;
            transition: opacity .15s;
        }

        .tab-btn:hover {
            background: var(--surface-2);
            color: var(--ink-soft);
        }

        .tab-btn.active {
            background: var(--accent);
            color: #fff;
            font-weight: 600;
        }

        .tab-btn.active svg {
            opacity: 1;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
            animation: fadeUp .2s ease both;
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

        /* ── FORM CARD ────────────────────────────── */
        .form-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .form-card-head {
            padding: 1.1rem 1.75rem;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .form-card-head h3 {
            font-family: 'Clash Display', sans-serif;
            font-size: .95rem;
            font-weight: 600;
            color: var(--ink);
            margin: 0;
        }

        .form-card-head p {
            font-size: .78rem;
            color: var(--ink-muted);
            margin: .1rem 0 0;
        }

        .form-card-body {
            padding: 1.75rem;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.1rem;
        }

        .field-grid .f-full {
            grid-column: 1 / -1;
        }

        .field-grid .f-third {
            grid-column: span 1;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: .35rem;
        }

        .field label {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--ink-muted);
        }

        .field input,
        .field select,
        .field textarea {
            padding: .6rem .9rem;
            background: var(--surface-2);
            border: 1.5px solid var(--line);
            border-radius: 9px;
            color: var(--ink);
            font-family: 'Satoshi', sans-serif;
            font-size: .875rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            -webkit-appearance: none;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .field textarea {
            resize: vertical;
            min-height: 90px;
        }

        .field input[readonly] {
            background: var(--surface-3);
            color: var(--ink-muted);
            cursor: not-allowed;
        }

        /* ── TOGGLE ───────────────────────────────── */
        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .85rem 1rem;
            background: var(--surface-2);
            border-radius: 10px;
            border: 1.5px solid var(--line);
        }

        .toggle-label {
            font-size: .875rem;
            font-weight: 500;
            color: var(--ink);
        }

        .toggle-sub {
            font-size: .75rem;
            color: var(--ink-muted);
            margin-top: .1rem;
        }

        .switch {
            position: relative;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: var(--surface-3);
            cursor: pointer;
            transition: background .2s;
        }

        .slider::before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #fff;
            top: 3px;
            left: 3px;
            transition: transform .2s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
        }

        .switch input:checked+.slider {
            background: var(--accent);
        }

        .switch input:checked+.slider::before {
            transform: translateX(20px);
        }

        /* ── SAVE BAR ─────────────────────────────── */
        .save-bar {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, .9);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--line);
            padding: 1rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            z-index: 100;
            box-shadow: 0 -4px 20px rgba(15, 22, 35, .08);
        }

        .save-bar p {
            font-size: .82rem;
            color: var(--ink-muted);
            margin: 0;
        }

        .save-bar-actions {
            display: flex;
            gap: .75rem;
        }

        .btn-reset {
            padding: .55rem 1.25rem;
            border-radius: 9px;
            border: 1.5px solid var(--line);
            background: transparent;
            color: var(--ink-soft);
            font-family: 'Satoshi', sans-serif;
            font-size: .85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-reset:hover {
            background: var(--surface-2);
        }

        .btn-save {
            padding: .55rem 1.5rem;
            border-radius: 9px;
            background: var(--accent);
            border: none;
            color: #fff;
            font-family: 'Clash Display', sans-serif;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 12px var(--accent-glow);
            transition: opacity .15s, transform .1s;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .btn-save:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        /* ── LOGO UPLOAD ──────────────────────────── */
        .logo-upload-zone {
            border: 2px dashed var(--line);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            background: var(--surface-2);
        }

        .logo-upload-zone:hover {
            border-color: var(--accent);
            background: var(--accent-lt);
        }

        .logo-upload-zone p {
            font-size: .82rem;
            color: var(--ink-muted);
            margin: .5rem 0 0;
        }

        .logo-upload-zone strong {
            color: var(--accent);
        }

        /* ── ALERT ────────────────────────────────── */
        .alert-success {
            display: flex;
            align-items: center;
            gap: .7rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: .85rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: .875rem;
            color: #15803d;
        }

        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 10px;
            padding: .85rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: .875rem;
            color: #be123c;
        }

        /* Remplace le @media existant par celui-ci, plus complet */
        @media (max-width: 768px) {
            .is-page {
                padding: 1rem;
            }

            .is-hero {
                padding: 1.25rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .is-hero>div:last-child {
                text-align: left;
                width: 100%;
                padding-top: .75rem;
                border-top: 1px solid var(--line);
            }

            .is-hero-meta {
                gap: .4rem;
            }

            .is-tabs-bar {
                gap: .15rem;
                padding: .25rem;
            }

            .tab-btn {
                padding: .45rem .7rem;
                font-size: .75rem;
            }

            /* Cache le texte des onglets, garde juste l'icône */
            .tab-btn span {
                display: none;
            }

            .field-grid {
                grid-template-columns: 1fr;
            }

            .field-grid .f-full,
            .field-grid .f-third {
                grid-column: 1 / -1;
            }

            .form-card-body {
                padding: 1.1rem;
            }

            .form-card-head {
                padding: .85rem 1.1rem;
            }

            .save-bar {
                flex-direction: column;
                align-items: stretch;
                padding: .85rem 1rem;
                gap: .75rem;
            }

            .save-bar p {
                text-align: center;
                font-size: .78rem;
            }

            .save-bar-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: .5rem;
            }

            .btn-save,
            .btn-reset {
                justify-content: center;
                text-align: center;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .is-logo-box {
                width: 52px;
                height: 52px;
            }

            .is-hero h1 {
                font-size: 1.15rem;
            }

            .is-chip {
                font-size: .65rem;
                padding: .2rem .5rem;
            }

            .tab-btn {
                padding: .4rem .55rem;
            }
        }
    </style>

    <div class="is-page">

        {{-- ── ALERTES ── --}}
        @if (session('success'))
            <div class="alert-success">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert-error">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                    <line x1="12" y1="8" x2="12" y2="12" stroke-width="2" />
                    <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2" />
                </svg>
                <div>
                    @foreach ($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── HERO ── --}}
        <div class="is-hero">
            <div class="is-hero-left">
                <div class="is-logo-box">
                    @if ($institution->logo)
                        <img src="{{ asset('storage/' . $institution->logo) }}" alt="Logo">
                    @else
                        <span>{{ strtoupper(substr($institution->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <h1>{{ $institution->name }}</h1>
                    <div class="is-hero-meta">
                        <span class="is-chip chip-code">{{ $institution->code }}</span>
                        <span class="is-chip chip-type">{{ ucfirst($institution->type) }}</span>
                        <span class="is-chip chip-year">{{ $institution->academic_year }}</span>
                        @if ($institution->status)
                            <span class="is-chip chip-status-on">● Actif</span>
                        @else
                            <span class="is-chip chip-status-off">● Inactif</span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="font-size:.78rem;color:var(--ink-muted);text-align:right">
                Dernière modification<br>
                <strong style="color:var(--ink-soft)">{{ $institution->updated_at->format('d/m/Y à H:i') }}</strong>
            </div>
        </div>

        {{-- ── ONGLETS ── --}}
        <div class="is-tabs-bar" role="tablist">
            <button class="tab-btn active" onclick="switchTab('general', this)" role="tab">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Général
            </button>
            <button class="tab-btn" onclick="switchTab('localisation', this)" role="tab">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Localisation
            </button>
            <button class="tab-btn" onclick="switchTab('contact', this)" role="tab">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Contact
            </button>
            <button class="tab-btn" onclick="switchTab('identite', this)" role="tab">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Identité &amp; Mission
            </button>
            <button class="tab-btn" onclick="switchTab('media', this)" role="tab">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" stroke-width="2" />
                    <polyline points="21 15 16 10 5 21" stroke-width="2" />
                </svg>
                Logo &amp; Médias
            </button>
            <button class="tab-btn" onclick="switchTab('parametres', this)" role="tab">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <circle cx="12" cy="12" r="3" stroke-width="2" />
                </svg>
                Paramètres
            </button>
        </div>

        {{-- Formulaire global englobant tous les onglets --}}
        <form method="POST" action="{{ route('admin.institution.update') }}" enctype="multipart/form-data"
            id="instForm">
            @csrf
            @method('PATCH')

            {{-- ════════════════════════════════════════
             ONGLET 1 – GÉNÉRAL
        ════════════════════════════════════════ --}}
            <div id="tab-general" class="tab-panel active">
                <div class="form-card">
                    <div class="form-card-head">
                        <svg width="16" height="16" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3>Informations générales</h3>
                            <p>Identité officielle de l'établissement</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-grid">
                            <div class="field f-full">
                                <label>Nom officiel de l'établissement</label>
                                <input type="text" name="name" value="{{ old('name', $institution->name) }}"
                                    required>
                            </div>
                            <div class="field">
                                <label>Code / Matricule</label>
                                <input type="text" name="code" value="{{ old('code', $institution->code) }}"
                                    readonly title="Non modifiable">
                            </div>
                            <div class="field">
                                <label>Année académique</label>
                                <input type="text" name="academic_year"
                                    value="{{ old('academic_year', $institution->academic_year) }}"
                                    placeholder="2025-2026" required>
                            </div>
                            <div class="field">
                                <label>Type d'établissement</label>
                                <select name="type">
                                    @foreach (['primaire', 'secondaire', 'lycee', 'universite', 'centre', 'autre'] as $t)
                                        <option value="{{ $t }}"
                                            {{ old('type', $institution->type) == $t ? 'selected' : '' }}>
                                            {{ ucfirst($t) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Statut juridique</label>
                                <select name="statut_juridique">
                                    @foreach (['public' => 'Public', 'prive' => 'Privé', 'confessionnel' => 'Confessionnel'] as $v => $l)
                                        <option value="{{ $v }}"
                                            {{ old('statut_juridique', $institution->statut_juridique) == $v ? 'selected' : '' }}>
                                            {{ $l }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Date de création</label>
                                <input type="date" name="date_creation"
                                    value="{{ old('date_creation', optional($institution->date_creation)->format('Y-m-d')) }}">
                            </div>
                            <div class="field">
                                <label>Monnaie</label>
                                <input type="text" name="devise" value="{{ old('devise', $institution->devise) }}"
                                    placeholder="Ex: L'excellence avant tout" readonly title="Non modifiable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════
             ONGLET 2 – LOCALISATION
        ════════════════════════════════════════ --}}
            <div id="tab-localisation" class="tab-panel">
                <div class="form-card">
                    <div class="form-card-head">
                        <svg width="16" height="16" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <div>
                            <h3>Localisation</h3>
                            <p>Adresse géographique de l'établissement</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-grid">
                            <div class="field">
                                <label>Pays</label>
                                <input type="text" name="pays" value="{{ old('pays', $institution->pays) }}"
                                    required>
                            </div>
                            <div class="field">
                                <label>Département / Province</label>
                                <input type="text" name="departement"
                                    value="{{ old('departement', $institution->departement) }}" required>
                            </div>
                            <div class="field">
                                <label>Commune / Arrondissement</label>
                                <input type="text" name="commune" value="{{ old('commune', $institution->commune) }}"
                                    required>
                            </div>
                            <div class="field f-full">
                                <label>Adresse complète</label>
                                <input type="text" name="adresse" value="{{ old('adresse', $institution->adresse) }}"
                                    placeholder="Rue, quartier, numéro…" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════
             ONGLET 3 – CONTACT
        ════════════════════════════════════════ --}}
            <div id="tab-contact" class="tab-panel">
                <div class="form-card">
                    <div class="form-card-head">
                        <svg width="16" height="16" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8" />
                        </svg>
                        <div>
                            <h3>Coordonnées de contact</h3>
                            <p>Canaux de communication de l'établissement</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-grid">
                            <div class="field">
                                <label>Email officiel</label>
                                <input type="email" name="email" value="{{ old('email', $institution->email) }}"
                                    placeholder="contact@ecole.cg">
                            </div>
                            <div class="field">
                                <label>Téléphone</label>
                                <input type="text" name="telephone"
                                    value="{{ old('telephone', $institution->telephone) }}" placeholder="+242 06 …">
                            </div>
                            <div class="field f-full">
                                <label>Site web</label>
                                <input type="url" name="site_web"
                                    value="{{ old('site_web', $institution->site_web) }}"
                                    placeholder="https://www.ecole.cg">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════
             ONGLET 4 – IDENTITÉ & MISSION
        ════════════════════════════════════════ --}}
            <div id="tab-identite" class="tab-panel">
                <div class="form-card">
                    <div class="form-card-head">
                        <svg width="16" height="16" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                            <h3>Identité &amp; Mission</h3>
                            <p>Présentation institutionnelle</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-grid">
                            <div class="field f-full">
                                <label>Description générale</label>
                                <textarea name="description">{{ old('description', $institution->description) }}</textarea>
                            </div>
                            <div class="field f-full">
                                <label>Historique</label>
                                <textarea name="historique">{{ old('historique', $institution->historique) }}</textarea>
                            </div>
                            <div class="field">
                                <label>Mission</label>
                                <textarea name="mission">{{ old('mission', $institution->mission) }}</textarea>
                            </div>
                            <div class="field">
                                <label>Vision</label>
                                <textarea name="vision">{{ old('vision', $institution->vision) }}</textarea>
                            </div>
                            <div class="field f-full">
                                <label>Valeurs</label>
                                <textarea name="valeurs" placeholder="Ex: Excellence, Intégrité, Solidarité…">{{ old('valeurs', $institution->valeurs) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════
             ONGLET 5 – LOGO & MÉDIAS
        ════════════════════════════════════════ --}}
            <div id="tab-media" class="tab-panel">
                <div class="form-card">
                    <div class="form-card-head">
                        <svg width="16" height="16" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" stroke-width="2" />
                            <polyline points="21 15 16 10 5 21" stroke-width="2" />
                        </svg>
                        <div>
                            <h3>Logo &amp; Identité visuelle</h3>
                            <p>Image de marque de votre établissement</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-grid">

                            {{-- Aperçu actuel --}}
                            @if ($institution->logo)
                                <div class="field">
                                    <label>Logo actuel</label>
                                    <div
                                        style="width:100px;height:100px;border-radius:12px;overflow:hidden;border:1.5px solid var(--line)">
                                        <img src="{{ asset('storage/' . $institution->logo) }}" alt="Logo actuel"
                                            style="width:100%;height:100%;object-fit:cover">
                                    </div>
                                </div>
                            @endif

                            <div class="field {{ $institution->logo ? '' : 'f-full' }}">
                                <label>{{ $institution->logo ? 'Remplacer le logo' : 'Téléverser un logo' }}</label>
                                <label class="logo-upload-zone" for="logoInput">
                                    <svg width="28" height="28" fill="none" stroke="var(--accent)"
                                        viewBox="0 0 24 24" style="margin:0 auto">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p>Glissez ou <strong>cliquez pour choisir</strong><br><span
                                            style="font-size:.7rem">PNG, JPG, SVG — max 2 Mo</span></p>
                                    <input type="file" id="logoInput" name="logo" accept="image/*"
                                        style="display:none" onchange="previewLogo(this)">
                                </label>
                                <div id="logoPreview" style="display:none;margin-top:.5rem">
                                    <img id="logoPreviewImg"
                                        style="width:80px;height:80px;border-radius:10px;object-fit:cover;border:1.5px solid var(--line)">
                                    <p style="font-size:.75rem;color:var(--ink-muted);margin-top:.3rem"
                                        id="logoPreviewName"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════
             ONGLET 6 – PARAMÈTRES
        ════════════════════════════════════════ --}}
            <div id="tab-parametres" class="tab-panel">
                <div class="form-card">
                    <div class="form-card-head">
                        <svg width="16" height="16" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <circle cx="12" cy="12" r="3" stroke-width="2" />
                        </svg>
                        <div>
                            <h3>Paramètres</h3>
                            <p>Options et autorisations</p>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div style="display:flex;flex-direction:column;gap:.85rem">

                            <div class="toggle-row">
                                <div>
                                    <div class="toggle-label">Autorisation de l'État</div>
                                    <div class="toggle-sub">L'établissement possède une autorisation officielle</div>
                                </div>
                                <label class="switch">
                                    <input type="hidden" name="autorisation_etat" value="0">
                                    <input type="checkbox" name="autorisation_etat" value="1"
                                        {{ old('autorisation_etat', $institution->autorisation_etat) ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ── BARRE DE SAUVEGARDE ── --}}
            <div class="save-bar">
                <p>Les modifications s'appliquent uniquement à <strong>{{ $institution->name }}</strong></p>
                <div class="save-bar-actions">
                    <button type="reset" class="btn-reset"
                        onclick="return confirm('Annuler toutes les modifications ?')">
                        Réinitialiser
                    </button>
                    <button type="submit" class="btn-save">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </div>

        </form>

    </div>

    <script>
        function switchTab(id, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + id).classList.add('active');
            btn.classList.add('active');
        }

        function previewLogo(input) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('logoPreviewImg').src = e.target.result;
                document.getElementById('logoPreviewName').textContent = file.name;
                document.getElementById('logoPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    </script>

@endsection
