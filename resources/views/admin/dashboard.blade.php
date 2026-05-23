@extends('admin.master')
@section('title', 'Tableau de bord')

@section('content')
    <style>
        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fu {
            animation: fadeUp .45s cubic-bezier(.22, 1, .36, 1) both;
        }

        .fu1 {
            animation-delay: .04s;
        }

        .fu2 {
            animation-delay: .09s;
        }

        .fu3 {
            animation-delay: .14s;
        }

        .fu4 {
            animation-delay: .19s;
        }

        .fu5 {
            animation-delay: .24s;
        }

        .fu6 {
            animation-delay: .30s;
        }

        /* ── HERO ── */
        .adm-hero {
            position: relative;
            background: #0f172a;
            border-radius: 1.25rem;
            padding: 1.875rem 2rem;
            margin-bottom: 1.5rem;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .adm-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 50% 80% at 92% 50%, rgba(37, 99, 192, .45) 0%, transparent 65%),
                radial-gradient(ellipse 35% 55% at 8% 85%, rgba(16, 185, 129, .2) 0%, transparent 65%);
            pointer-events: none;
        }

        .adm-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, .025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .025) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
        }

        .hero-body {
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #60a5fa;
            margin-bottom: .5rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .hero-eyebrow::before {
            content: '';
            display: inline-block;
            width: 18px;
            height: 2px;
            background: #60a5fa;
            border-radius: 1px;
        }

        .hero-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.04em;
            line-height: 1.1;
        }

        .hero-sub {
            font-size: .8rem;
            color: rgba(255, 255, 255, .42);
            margin-top: .45rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .hero-sep {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .22);
        }

        .hero-stat-group {
            position: relative;
            z-index: 1;
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .hero-stat {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: .875rem;
            padding: 1rem 1.375rem;
            text-align: center;
            min-width: 110px;
            backdrop-filter: blur(8px);
            transition: background .2s;
        }

        .hero-stat:hover {
            background: rgba(255, 255, 255, .11);
        }

        .hero-stat-val {
            font-size: 1.875rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: -.05em;
            line-height: 1;
            font-family: 'JetBrains Mono', monospace;
        }

        .hero-stat-lbl {
            font-size: .65rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .4);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-top: .3rem;
        }

        .hero-stat-note {
            font-size: .7rem;
            color: #4ade80;
            font-weight: 600;
            margin-top: .25rem;
        }

        /* ── SECTION HEAD ── */
        .sec-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .875rem;
        }

        .sec-title {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .sec-title::before {
            content: '';
            width: 3px;
            height: 14px;
            border-radius: 2px;
            background: #1f2937;
            display: inline-block;
        }

        /* ── KPI TILES ── */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media(max-width:960px) {
            .kpi-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:480px) {
            .kpi-row {
                grid-template-columns: 1fr;
            }
        }

        .kpi-tile {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.375rem;
            position: relative;
            overflow: hidden;
            transition: box-shadow .2s, transform .2s;
        }

        .kpi-tile:hover {
            box-shadow: 0 8px 28px rgba(0, 0, 0, .08);
            transform: translateY(-2px);
        }

        .kpi-stripe {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 1rem 1rem 0 0;
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            flex-shrink: 0;
        }

        .kpi-icon svg {
            width: 20px;
            height: 20px;
        }

        .kpi-val {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -.05em;
            line-height: 1;
            font-family: 'JetBrains Mono', monospace;
        }

        .kpi-label {
            font-size: .775rem;
            color: #64748b;
            font-weight: 500;
            margin-top: .25rem;
        }

        .kpi-note {
            display: flex;
            align-items: center;
            gap: .3rem;
            font-size: .7rem;
            font-weight: 600;
            margin-top: .5rem;
        }

        .kpi-note .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── CHARTS ROW ── */
        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        @media(max-width:768px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        .chart-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            overflow: hidden;
            transition: box-shadow .2s;
        }

        .chart-box:hover {
            box-shadow: 0 6px 24px rgba(0, 0, 0, .06);
        }

        .chart-head {
            padding: 1rem 1.375rem;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .chart-title {
            font-size: .875rem;
            font-weight: 700;
            color: #0f172a;
        }

        .chart-sub {
            font-size: .7rem;
            color: #64748b;
            margin-top: .15rem;
        }

        .chart-body {
            padding: 1.125rem 1.25rem;
        }

        /* Tag pill */
        .tag {
            display: inline-flex;
            align-items: center;
            padding: .18rem .65rem;
            border-radius: 99px;
            font-size: .67rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .tag-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .tag-green {
            background: #d1fae5;
            color: #065f46;
        }

        .tag-amber {
            background: #fef3c7;
            color: #92400e;
        }

        .tag-gray {
            background: #f3f4f6;
            color: #374151;
        }

        /* Bar chart CSS */
        .bar-chart {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 140px;
            padding-top: 1rem;
        }

        .bar-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .bar-fill {
            width: 100%;
            border-radius: 6px 6px 0 0;
            background: #1f2937;
            transition: background .2s;
            min-height: 4px;
            position: relative;
        }

        .bar-fill:hover {
            background: #374151;
        }

        .bar-fill .bar-tooltip {
            display: none;
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: #fff;
            font-size: .7rem;
            font-weight: 600;
            padding: .2rem .5rem;
            border-radius: 5px;
            white-space: nowrap;
            z-index: 10;
        }

        .bar-fill:hover .bar-tooltip {
            display: block;
        }

        .bar-lbl {
            font-size: .65rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Result bars */
        .result-bar-row {
            margin-bottom: 1rem;
        }

        .result-bar-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: .4rem;
        }

        .result-bar-label {
            font-size: .8rem;
            font-weight: 500;
            color: #374151;
        }

        .result-bar-pct {
            font-size: .8rem;
            font-weight: 700;
            color: #0f172a;
            font-family: 'JetBrains Mono', monospace;
        }

        .result-bar-track {
            height: 8px;
            background: #f1f5f9;
            border-radius: 99px;
            overflow: hidden;
        }

        .result-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width .6s cubic-bezier(.22, 1, .36, 1);
        }

        /* ── BOTTOM TABLES ── */
        .tables-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        @media(max-width:768px) {
            .tables-row {
                grid-template-columns: 1fr;
            }
        }

        .tbl-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            overflow: hidden;
        }

        .tbl-card-head {
            padding: 1rem 1.375rem;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tbl-card-title {
            font-size: .875rem;
            font-weight: 700;
            color: #0f172a;
        }

        .tbl-link {
            font-size: .75rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: color .15s;
        }

        .tbl-link:hover {
            color: #0f172a;
        }

        .d-table {
            width: 100%;
            border-collapse: collapse;
        }

        .d-table th {
            background: #f8fafc;
            padding: .6rem 1.125rem;
            text-align: left;
            font-size: .62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }

        .d-table td {
            padding: .8rem 1.125rem;
            border-bottom: 1px solid #f8fafc;
            font-size: .8125rem;
            color: #334155;
            vertical-align: middle;
        }

        .d-table tr:last-child td {
            border-bottom: none;
        }

        .d-table tr:hover td {
            background: #fafbfc;
        }

        /* Avatar */
        .av {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f1f5f9;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 700;
            color: #64748b;
        }

        .av img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Status badge */
        .s-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .18rem .6rem;
            border-radius: 99px;
            font-size: .68rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .s-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
        }

        .s-badge-on {
            background: #d1fae5;
            color: #065f46;
        }

        .s-badge-on::before {
            background: #10b981;
        }

        .s-badge-off {
            background: #fee2e2;
            color: #991b1b;
        }

        .s-badge-off::before {
            background: #ef4444;
        }

        /* Event type pills */
        .ev-badge {
            display: inline-block;
            padding: .18rem .6rem;
            border-radius: 99px;
            font-size: .67rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .ev-exam {
            background: #fef3c7;
            color: #92400e;
        }

        .ev-meeting {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .ev-trip {
            background: #d1fae5;
            color: #065f46;
        }

        .ev-council {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>

    {{-- ── HERO ── --}}
    <div class="adm-hero fu fu1">
        <div class="hero-body">
            <div class="hero-eyebrow">Espace Administration</div>
            <div class="hero-title">Tableau de bord</div>
            <div class="hero-sub">
                <span>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY, HH:mm') }}</span>
                @if (isset($institution))
                    <span class="hero-sep"></span>
                    <span>{{ $institution->name }}</span>
                @endif
            </div>
        </div>
        <div class="hero-stat-group">
            <div class="hero-stat">
                <div class="hero-stat-val">{{ $totalStudents }}</div>
                <div class="hero-stat-lbl">Élèves total</div>
                <div class="hero-stat-note">{{ $activeStudents }} actifs</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-val">{{ $totalTeachers }}</div>
                <div class="hero-stat-lbl">Enseignants</div>
            </div>
        </div>
    </div>

    {{-- ── KPI ── --}}
    <div class="sec-head fu fu2">
        <div class="sec-title">Indicateurs clés</div>
    </div>
    <div class="kpi-row fu fu2">

        {{-- Étudiants actifs --}}
        <div class="kpi-tile">
            <div class="kpi-stripe" style="background:linear-gradient(90deg,#2563c0,#60a5fa);"></div>
            <div class="kpi-icon" style="background:#dbeafe;">
                <svg style="color:#2563c0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="kpi-val">{{ $activeStudents }}</div>
            <div class="kpi-label">Étudiants actifs</div>
            <div class="kpi-note" style="color:#2563c0;">
                <div class="dot" style="background:#2563c0;"></div>
                {{ $totalStudents }} inscrits au total
            </div>
        </div>

        {{-- Enseignants --}}
        <div class="kpi-tile">
            <div class="kpi-stripe" style="background:linear-gradient(90deg,#059669,#34d399);"></div>
            <div class="kpi-icon" style="background:#d1fae5;">
                <svg style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div class="kpi-val">{{ $totalTeachers }}</div>
            <div class="kpi-label">Enseignants</div>
            <div class="kpi-note" style="color:#059669;">
                <div class="dot" style="background:#059669;"></div>
                Corps enseignant actif
            </div>
        </div>

        {{-- Taux d'activité --}}
        @php
            $tauxActivite = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100) : 0;
        @endphp
        <div class="kpi-tile">
            <div class="kpi-stripe" style="background:linear-gradient(90deg,#f59e0b,#fbbf24);"></div>
            <div class="kpi-icon" style="background:#fef3c7;">
                <svg style="color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="kpi-val">{{ $tauxActivite }}%</div>
            <div class="kpi-label">Taux d'activité élèves</div>
            <div class="kpi-note" style="color:#d97706;">
                <div class="dot" style="background:#f59e0b;"></div>
                {{ $totalStudents - $activeStudents }} inactif(s)
            </div>
        </div>

        {{-- Établissement --}}
        <div class="kpi-tile">
            <div class="kpi-stripe" style="background:linear-gradient(90deg,#6366f1,#818cf8);"></div>
            <div class="kpi-icon" style="background:#eef2ff;">
                <svg style="color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="kpi-val" style="font-size:1.1rem;letter-spacing:-.02em;margin-top:.25rem;">
                {{ Str::limit($institution->name ?? '—', 16) }}
            </div>
            <div class="kpi-label">{{ $institution->academic_year ?? '' }}</div>
            <div class="kpi-note" style="color:#6366f1;">
                <div class="dot" style="background:#6366f1;"></div>
                {{ ucfirst($institution->type ?? '') }}
            </div>
        </div>
    </div>

    {{-- ── GRAPHIQUES ── --}}
    <div class="sec-head fu fu3">
        <div class="sec-title">Analyse visuelle</div>
    </div>
    <div class="charts-row fu fu4">

        {{-- Présence : barres CSS (données statiques visuelles — à connecter via API si besoin) --}}
        <div class="chart-box">
            <div class="chart-head">
                <div>
                    <div class="chart-title">Activité mensuelle des élèves</div>
                    <div class="chart-sub">Présence estimée · 6 derniers mois</div>
                </div>
                <span class="tag tag-blue">Vue globale</span>
            </div>
            <div class="chart-body">
                @php
                    $maxVal = max($vals) ?: 1;
                @endphp
                <div class="bar-chart">
                    @foreach ($vals as $i => $v)
                        <div class="bar-item">
                            <div class="bar-fill" style="height:{{ round(($v / $maxVal) * 100) }}%;">
                                <div class="bar-tooltip">{{ $v }}%</div>
                            </div>
                            <span class="bar-lbl">{{ $months[$i] }}</span>
                        </div>
                    @endforeach
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:1rem;">
                    <span style="font-size:.72rem;color:#94a3b8;">Taux moyen : <strong
                            style="color:#0f172a;">{{ round(collect($vals)->avg()) }}%</strong></span>
                    <span style="font-size:.72rem;color:#94a3b8;">Meilleur mois : <strong
                            style="color:#059669;">@php
                                $max = max($vals);
                                $bestIndex = array_search($max, $vals);
                            @endphp

                            <strong style="color:#059669;">
                                {{ $months[$bestIndex] }} {{ $max }}
                            </strong></strong></span>
                </div>
            </div>
        </div>

        {{-- Répartition des résultats --}}
        {{-- Répartition des résultats --}}
        <div class="chart-box">
            <div class="chart-head">
                <div>
                    <div class="chart-title">Répartition des résultats</div>
                    <div class="chart-sub">Trimestre en cours · toutes classes</div>
                </div>
                <span class="tag tag-green">Ce trimestre</span>
            </div>

            <div class="chart-body">
                @forelse ($resultats as $r)
                    <div class="result-bar-row">
                        <div class="result-bar-head">
                            <span class="result-bar-label">{{ $r['label'] }}</span>
                            <span class="result-bar-pct">{{ $r['pct'] }}%</span>
                        </div>

                        <div class="result-bar-track">
                            <div class="result-bar-fill"
                                style="width:{{ $r['pct'] }}%;background:{{ $r['color'] }};">
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:2rem;color:#94a3b8;">
                        Aucune donnée disponible
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── TABLEAUX ── --}}
    <div class="sec-head fu fu5">
        <div class="sec-title">Données récentes</div>
    </div>
    <div class="tables-row fu fu5">

        {{-- Étudiants récents (données réelles) --}}
        <div class="tbl-card">
            <div class="tbl-card-head">
                <div class="tbl-card-title">Élèves récemment inscrits</div>
                <a href="{{ route('admin.apprenants') }}" class="tbl-link">
                    Voir tout →
                </a>
            </div>
            @if ($recentStudents->isEmpty())
                <div style="text-align:center;padding:3rem;color:#94a3b8;font-size:.8rem;">
                    <div style="font-size:2rem;opacity:.4;margin-bottom:.5rem;">👤</div>
                    Aucun élève enregistré
                </div>
            @else
                <table class="d-table">
                    <thead>
                        <tr>
                            <th>Élève</th>
                            <th>Classe</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentStudents as $s)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:.75rem;">
                                        <div class="av">
                                            @if ($s->photo)
                                                <img src="{{ asset('storage/' . $s->photo) }}" alt="">
                                            @else
                                                {{ strtoupper(mb_substr($s->prenom ?? $s->nom, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight:600;color:#0f172a;font-size:.8125rem;">
                                                {{ $s->prenom }} {{ $s->nom }}
                                            </div>
                                            @if ($s->matricule)
                                                <div
                                                    style="font-size:.68rem;color:#94a3b8;font-family:'JetBrains Mono',monospace;">
                                                    {{ $s->matricule }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($s->classe)
                                        <span class="tag tag-blue">{{ $s->classe->name }}</span>
                                    @else
                                        <span style="color:#94a3b8;font-size:.75rem;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="s-badge {{ $s->status ? 's-badge-on' : 's-badge-off' }}">
                                        {{ $s->status ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Événements à venir (données statiques) --}}
        <div class="tbl-card">
            <div class="tbl-card-head">
                <div class="tbl-card-title">Événements à venir</div>
                <span class="tag tag-amber">{{ $events->count() }} prévus</span>
            </div>

            @if ($events->isEmpty())
                <div style="text-align:center;padding:3rem;color:#94a3b8;font-size:.8rem;">
                    <div style="font-size:2rem;opacity:.4;margin-bottom:.5rem;">📅</div>
                    Aucun événement à venir
                </div>
            @else
                <table class="d-table">
                    <thead>
                        <tr>
                            <th>Épreuve</th>
                            <th>Date</th>
                            <th>Enseignant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $ev)
                            <tr>
                                {{-- TITRE + MATIÈRE --}}
                                <td>
                                    <div style="font-weight:600;color:#0f172a;">
                                        {{ $ev->title }}
                                    </div>
                                    <div style="font-size:.7rem;color:#94a3b8;">
                                        {{ $ev->subject?->name ?? '—' }}
                                    </div>
                                </td>

                                {{-- DATE --}}
                                <td style="font-size:.78rem;color:#64748b;font-family:'JetBrains Mono',monospace;">
                                    {{ \Carbon\Carbon::parse($ev->date)->format('d M Y') }}
                                </td>

                                {{-- ENSEIGNANT --}}
                                <td>
                                    @if ($ev->subject?->teacher)
                                        <span class="tag tag-blue">
                                            {{ $ev->subject->teacher->prenom }} {{ $ev->subject->teacher->nom }}
                                        </span>
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
