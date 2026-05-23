@extends('admin.master')

@push('styles')
<style>
    /* ══ DESIGN SYSTEM ══ */
    :root {
        --ink:     #0d1117;
        --ink-60:  #57606a;
        --ink-20:  #d0d7de;
        --canvas:  #f6f8fa;
        --white:   #ffffff;
        --emerald: #1a7f5a;
        --em-l:    #d1fadf;
        --sapphire:#1a5fb4;
        --sap-l:   #dbeafe;
        --amber:   #c57d0e;
        --amb-l:   #fef9c3;
        --crimson: #c92a2a;
        --cri-l:   #ffe3e3;
        --violet:  #6741d9;
        --vio-l:   #e9d8fd;
        --card-r:  12px;
        --sh:      0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
        --sh-lg:   0 4px 16px rgba(0,0,0,.09);
    }

    .rpt-page { display:flex; flex-direction:column; gap:1.75rem; }

    /* ── HERO BANNER ── */
    .hero {
        background: linear-gradient(135deg, var(--ink) 0%, #1c2a3a 60%, #1a4a3a 100%);
        border-radius: 16px;
        padding: 2rem 2.5rem;
        position: relative; overflow: hidden;
        display: flex; align-items: center; justify-content: space-between; gap:1rem;
    }
    .hero::before {
        content:''; position:absolute; inset:0;
        background: repeating-linear-gradient(45deg,
            rgba(255,255,255,.015) 0, rgba(255,255,255,.015) 1px,
            transparent 0, transparent 50%);
        background-size: 20px 20px;
    }
    .hero-left { position:relative; z-index:1; }
    .hero-label { font-size:.7rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.12em; color:rgba(255,255,255,.45); margin-bottom:.5rem; }
    .hero-title { font-size:1.75rem; font-weight:800; color:#fff; line-height:1.2; margin:0 0 .4rem; }
    .hero-sub   { font-size:.875rem; color:rgba(255,255,255,.55); margin:0; }
    .hero-badges { display:flex; gap:.75rem; margin-top:1.25rem; flex-wrap:wrap; }
    .hero-badge {
        display:inline-flex; align-items:center; gap:.4rem;
        background: rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);
        border-radius:20px; padding:.35rem .875rem;
        font-size:.78rem; font-weight:600; color:rgba(255,255,255,.85);
    }
    .hero-badge .dot { width:7px; height:7px; border-radius:50%; }
    .hero-right { position:relative; z-index:1; text-align:right; }
    .hero-big { font-size:3rem; font-weight:800; color:#fff; line-height:1; }
    .hero-big-label { font-size:.75rem; color:rgba(255,255,255,.45); margin-top:.25rem; }
    .print-btn {
        display:inline-flex; align-items:center; gap:.5rem;
        background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15);
        color:#fff; border-radius:8px; padding:.5rem 1rem;
        font-size:.8rem; font-weight:600; cursor:pointer;
        transition:background .2s; margin-top:1rem;
        text-decoration:none;
    }
    .print-btn:hover { background:rgba(255,255,255,.2); }

    /* ── SECTION TITLE ── */
    .section-title {
        display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem;
    }
    .section-title .icon {
        width:36px; height:36px; border-radius:9px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .section-title .icon svg { width:18px; height:18px; }
    .section-title h2 { font-size:1rem; font-weight:700; color:var(--ink); margin:0; }
    .section-title p  { font-size:.78rem; color:var(--ink-60); margin:.15rem 0 0; }

    /* ── KPI GRID ── */
    .kpi-grid { display:grid; gap:1rem; }
    .kpi-grid-4 { grid-template-columns: repeat(4,1fr); }
    .kpi-grid-3 { grid-template-columns: repeat(3,1fr); }
    .kpi-grid-2 { grid-template-columns: 1fr 1fr; }

    .kpi {
        background:var(--white); border:1px solid var(--ink-20);
        border-radius:var(--card-r); padding:1.25rem 1.5rem;
        box-shadow:var(--sh); transition:box-shadow .2s;
        position:relative; overflow:hidden;
    }
    .kpi:hover { box-shadow:var(--sh-lg); }
    .kpi::after {
        content:''; position:absolute; top:0; left:0; right:0; height:3px;
        border-radius:var(--card-r) var(--card-r) 0 0;
    }
    .kpi.em::after  { background:var(--emerald); }
    .kpi.sap::after { background:var(--sapphire); }
    .kpi.amb::after { background:var(--amber); }
    .kpi.cri::after { background:var(--crimson); }
    .kpi.vio::after { background:var(--violet); }
    .kpi.slate::after { background:#64748b; }

    .kpi-icon {
        width:40px; height:40px; border-radius:10px;
        display:flex; align-items:center; justify-content:center; margin-bottom:.875rem;
    }
    .kpi-icon svg { width:20px; height:20px; }
    .kpi-val   { font-size:2rem; font-weight:800; color:var(--ink); line-height:1; }
    .kpi-label { font-size:.75rem; color:var(--ink-60); margin:.3rem 0 0; }
    .kpi-sub   { font-size:.72rem; margin-top:.5rem; display:flex; align-items:center; gap:.3rem; }
    .kpi-up    { color:var(--emerald); }
    .kpi-down  { color:var(--crimson); }
    .kpi-neutral { color:var(--ink-60); }

    /* ── CARDS ── */
    .card {
        background:var(--white); border:1px solid var(--ink-20);
        border-radius:var(--card-r); box-shadow:var(--sh);
        overflow:hidden;
    }
    .card-header {
        padding:1rem 1.5rem; border-bottom:1px solid var(--ink-20);
        display:flex; align-items:center; justify-content:space-between;
        background:var(--canvas);
    }
    .card-header h3 { font-size:.875rem; font-weight:600; color:var(--ink); margin:0; }
    .card-body { padding:1.5rem; }

    /* ── TWO COL ── */
    .two-col { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .three-col { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }

    /* ── BAR CHART ── */
    .bar-list { display:flex; flex-direction:column; gap:.75rem; }
    .bar-item {}
    .bar-header { display:flex; justify-content:space-between; margin-bottom:.3rem; }
    .bar-label { font-size:.8rem; color:var(--ink); font-weight:500;
        white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70%; }
    .bar-val { font-size:.8rem; font-weight:700; color:var(--ink); }
    .bar-track {
        height:8px; background:var(--canvas); border-radius:99px; overflow:hidden;
    }
    .bar-fill {
        height:100%; border-radius:99px;
        transition:width .6s cubic-bezier(.4,0,.2,1);
    }

    /* ── DONUT ── */
    .donut-wrap { display:flex; align-items:center; gap:1.5rem; }
    .donut-svg  { flex-shrink:0; }
    .donut-legend { display:flex; flex-direction:column; gap:.5rem; flex:1; }
    .donut-item { display:flex; align-items:center; gap:.5rem; font-size:.8rem; }
    .donut-dot  { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
    .donut-name { color:var(--ink-60); flex:1; }
    .donut-pct  { font-weight:700; color:var(--ink); }

    /* ── PROGRESS RINGS (SVG) ── */
    .ring-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; text-align:center; }
    .ring-wrap { display:flex; flex-direction:column; align-items:center; gap:.5rem; }
    .ring-label { font-size:.78rem; color:var(--ink-60); font-weight:500; }
    .ring-val   { font-size:1.1rem; font-weight:800; color:var(--ink); }

    /* ── TABLE ── */
    .rpt-table { width:100%; border-collapse:collapse; }
    .rpt-table th {
        background:var(--canvas); padding:.65rem 1rem;
        text-align:left; font-size:.7rem; font-weight:700;
        text-transform:uppercase; color:var(--ink-60);
        border-bottom:1px solid var(--ink-20);
    }
    .rpt-table td {
        padding:.75rem 1rem; border-bottom:1px solid var(--canvas);
        font-size:.83rem; color:var(--ink); vertical-align:middle;
    }
    .rpt-table tr:last-child td { border-bottom:none; }
    .rpt-table tr:hover td { background:var(--canvas); }

    /* ── BADGE PILLS ── */
    .pill {
        display:inline-flex; align-items:center; padding:.2rem .65rem;
        border-radius:20px; font-size:.7rem; font-weight:700;
    }
    .pill-green  { background:var(--em-l);  color:var(--emerald); }
    .pill-red    { background:var(--cri-l); color:var(--crimson); }
    .pill-amber  { background:var(--amb-l); color:var(--amber); }
    .pill-blue   { background:var(--sap-l); color:var(--sapphire); }

    /* ── FINANCE SUMMARY ── */
    .fin-summary {
        display:grid; grid-template-columns:repeat(3,1fr);
        gap:1px; background:var(--ink-20); border-radius:var(--card-r);
        overflow:hidden;
    }
    .fin-cell {
        background:var(--white); padding:1.25rem 1.5rem; text-align:center;
    }
    .fin-cell .val { font-size:1.4rem; font-weight:800; line-height:1; }
    .fin-cell .lbl { font-size:.72rem; color:var(--ink-60); margin-top:.3rem; }
    .fin-cell.du   .val { color:var(--ink); }
    .fin-cell.paye .val { color:var(--emerald); }
    .fin-cell.rest .val { color:var(--crimson); }

    /* ── MENSUEL CHART ── */
    .mensuel-bars {
        display:flex; align-items:flex-end; gap:6px;
        height:120px; padding:0 .5rem; overflow-x:auto;
    }
    .mensuel-col { display:flex; flex-direction:column; align-items:center; gap:4px; flex-shrink:0; min-width:32px; }
    .mensuel-bar-wrap { display:flex; gap:2px; align-items:flex-end; height:96px; }
    .mensuel-bar { width:14px; border-radius:3px 3px 0 0; transition:opacity .2s; }
    .mensuel-bar:hover { opacity:.8; }
    .mensuel-lbl { font-size:.6rem; color:var(--ink-60); text-align:center; }

    /* ── TAUX CIRCLE ── */
    .taux-ring { position:relative; width:90px; height:90px; }
    .taux-ring svg { transform:rotate(-90deg); }
    .taux-ring .center {
        position:absolute; inset:0;
        display:flex; flex-direction:column;
        align-items:center; justify-content:center;
        font-weight:800; font-size:.95rem; color:var(--ink);
    }
    .taux-ring .center small { font-size:.55rem; color:var(--ink-60); font-weight:500; }

    /* ── ACTIVITY FEED ── */
    .activity-list { display:flex; flex-direction:column; gap:0; }
    .activity-item {
        display:flex; align-items:center; gap:.875rem;
        padding:.75rem 0; border-bottom:1px solid var(--canvas);
    }
    .activity-item:last-child { border-bottom:none; }
    .activity-dot {
        width:8px; height:8px; border-radius:50%; flex-shrink:0;
    }
    .activity-text { font-size:.82rem; color:var(--ink); flex:1; }
    .activity-val  { font-size:.82rem; font-weight:700; color:var(--ink); }

    /* ── RESPONSIVE ── */
    @media (max-width:1024px) {
        .kpi-grid-4 { grid-template-columns:repeat(2,1fr); }
        .three-col  { grid-template-columns:1fr 1fr; }
    }
    @media (max-width:640px) {
        .kpi-grid-4, .kpi-grid-3, .two-col, .three-col { grid-template-columns:1fr; }
        .fin-summary { grid-template-columns:1fr; }
        .hero { flex-direction:column; }
        .hero-right { text-align:left; }
    }
    @media print {
        .hero { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        .print-btn { display:none; }
    }
</style>
@endpush

@section('content')
@php
    // Formatage monnaie
    function fcfa($n) {
        return number_format($n, 0, ',', ' ') . ' FCFA';
    }
    // Taux en %
    function pct($n, $total) {
        if (!$total) return 0;
        return round($n / $total * 100, 1);
    }
    // Couleur barre finance selon taux de recouvrement
    $tauxRecouvrement = $finStats && $finStats->total_du > 0
        ? round($finStats->total_paye / $finStats->total_du * 100, 1)
        : 0;
@endphp

<div class="rpt-page" id="rapport-content">

    {{-- ══ HERO ══ --}}
    <div class="hero">
        <div class="hero-left">
            <p class="hero-label">Tableau de bord analytique</p>
            <h1 class="hero-title">Rapports & Statistiques</h1>
            <p class="hero-sub">{{ $institution->name }} — Année académique {{ $annee }}</p>
            <div class="hero-badges">
                <span class="hero-badge">
                    <span class="dot" style="background:#4ade80;"></span>
                    {{ $totalApprenants }} élèves
                </span>
                <span class="hero-badge">
                    <span class="dot" style="background:#60a5fa;"></span>
                    {{ $totalTeachers }} enseignants
                </span>
                <span class="hero-badge">
                    <span class="dot" style="background:#f59e0b;"></span>
                    {{ $totalClasses }} classes
                </span>
                <span class="hero-badge">
                    <span class="dot" style="background:#c084fc;"></span>
                    {{ $totalStaff }} staff
                </span>
            </div>
        </div>
        <div class="hero-right">
            <div class="hero-big">{{ $tauxRecouvrement }}%</div>
            <div class="hero-big-label">taux de recouvrement</div>
            <button class="print-btn" onclick="window.print()">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimer / PDF
            </button>
        </div>
    </div>

    {{-- ══ SECTION 1 : EFFECTIFS ══ --}}
    <div>
        <div class="section-title">
            <div class="icon" style="background:var(--em-l);color:var(--emerald);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <h2>Effectifs des apprenants</h2>
                <p>Répartition et démographie scolaire</p>
            </div>
        </div>

        <div class="kpi-grid kpi-grid-4" style="margin-bottom:1rem;">
            <div class="kpi em">
                <div class="kpi-icon" style="background:var(--em-l);">
                    <svg fill="none" stroke="var(--emerald)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="kpi-val">{{ number_format($totalApprenants) }}</div>
                <div class="kpi-label">Total apprenants</div>
                <div class="kpi-sub kpi-up">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $actifsApprenants }} actifs
                </div>
            </div>
            <div class="kpi sap">
                <div class="kpi-icon" style="background:var(--sap-l);">
                    <svg fill="none" stroke="var(--sapphire)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="kpi-val">{{ number_format($garcons) }}</div>
                <div class="kpi-label">Garçons</div>
                <div class="kpi-sub kpi-neutral">{{ pct($garcons, $totalApprenants) }}% de l'effectif</div>
            </div>
            <div class="kpi vio">
                <div class="kpi-icon" style="background:var(--vio-l);">
                    <svg fill="none" stroke="var(--violet)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="kpi-val">{{ number_format($filles) }}</div>
                <div class="kpi-label">Filles</div>
                <div class="kpi-sub kpi-neutral">{{ pct($filles, $totalApprenants) }}% de l'effectif</div>
            </div>
            <div class="kpi {{ $apprenantsSansClasse > 0 ? 'amb' : 'em' }}">
                <div class="kpi-icon" style="background:{{ $apprenantsSansClasse > 0 ? 'var(--amb-l)' : 'var(--em-l)' }};">
                    <svg fill="none" stroke="{{ $apprenantsSansClasse > 0 ? 'var(--amber)' : 'var(--emerald)' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
                <div class="kpi-val">{{ $tauxAffectation }}%</div>
                <div class="kpi-label">Taux d'affectation en classe</div>
                <div class="kpi-sub {{ $apprenantsSansClasse > 0 ? 'kpi-down' : 'kpi-up' }}">
                    {{ $apprenantsSansClasse }} sans classe assignée
                </div>
            </div>
        </div>

        <div class="two-col">
            {{-- Répartition par classe --}}
            <div class="card">
                <div class="card-header">
                    <h3>Répartition par classe</h3>
                    <span style="font-size:.72rem;color:var(--ink-60);">{{ $apprenantsByClasse->count() }} classe(s)</span>
                </div>
                <div class="card-body">
                    @if($apprenantsByClasse->count())
                    @php $maxC = $apprenantsByClasse->max('total') ?: 1; @endphp
                    <div class="bar-list">
                        @foreach($apprenantsByClasse->take(8) as $row)
                        <div class="bar-item">
                            <div class="bar-header">
                                <span class="bar-label">{{ $row->classe }}</span>
                                <span class="bar-val">{{ $row->total }}</span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ round($row->total/$maxC*100) }}%;background:var(--emerald);"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="text-align:center;color:var(--ink-60);font-size:.85rem;padding:2rem 0;">Aucune donnée</p>
                    @endif
                </div>
            </div>

            {{-- Répartition par niveau --}}
            <div class="card">
                <div class="card-header">
                    <h3>Répartition par niveau</h3>
                </div>
                <div class="card-body">
                    @if($apprenantsByNiveau->count())
                    @php
                        $colors = ['#1a7f5a','#1a5fb4','#6741d9','#c57d0e','#c92a2a','#0c8599'];
                        $maxN = $apprenantsByNiveau->max('total') ?: 1;
                    @endphp
                    <div class="bar-list">
                        @foreach($apprenantsByNiveau as $i => $row)
                        <div class="bar-item">
                            <div class="bar-header">
                                <span class="bar-label">{{ $row->niveau }}</span>
                                <span class="bar-val">{{ $row->total }}</span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ round($row->total/$maxN*100) }}%;background:{{ $colors[$i % count($colors)] }};"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="text-align:center;color:var(--ink-60);font-size:.85rem;padding:2rem 0;">Aucune donnée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══ SECTION 2 : ENSEIGNANTS ══ --}}
    <div>
        <div class="section-title">
            <div class="icon" style="background:var(--sap-l);color:var(--sapphire);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                </svg>
            </div>
            <div>
                <h2>Corps enseignant</h2>
                <p>Effectif, genre et type de contrat</p>
            </div>
        </div>

        <div class="kpi-grid kpi-grid-4" style="margin-bottom:1rem;">
            <div class="kpi sap">
                <div class="kpi-icon" style="background:var(--sap-l);">
                    <svg fill="none" stroke="var(--sapphire)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7"/>
                    </svg>
                </div>
                <div class="kpi-val">{{ $totalTeachers }}</div>
                <div class="kpi-label">Total enseignants</div>
                <div class="kpi-sub kpi-up">{{ $actifsTeachers }} actifs</div>
            </div>
            <div class="kpi sap">
                <div class="kpi-icon" style="background:var(--sap-l);">
                    <svg fill="none" stroke="var(--sapphire)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="kpi-val">{{ $teachersBySexe->get('M')?->total ?? 0 }}</div>
                <div class="kpi-label">Hommes</div>
                <div class="kpi-sub kpi-neutral">{{ pct($teachersBySexe->get('M')?->total ?? 0, $totalTeachers) }}%</div>
            </div>
            <div class="kpi vio">
                <div class="kpi-icon" style="background:var(--vio-l);">
                    <svg fill="none" stroke="var(--violet)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="kpi-val">{{ $teachersBySexe->get('F')?->total ?? 0 }}</div>
                <div class="kpi-label">Femmes</div>
                <div class="kpi-sub kpi-neutral">{{ pct($teachersBySexe->get('F')?->total ?? 0, $totalTeachers) }}%</div>
            </div>
            <div class="kpi em">
                <div class="kpi-icon" style="background:var(--em-l);">
                    <svg fill="none" stroke="var(--emerald)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="kpi-val">{{ $totalClasses > 0 ? round($totalTeachers / $totalClasses, 1) : 0 }}</div>
                <div class="kpi-label">Enseignants / classe</div>
                <div class="kpi-sub kpi-neutral">ratio moyen</div>
            </div>
        </div>

        {{-- Contrats --}}
        @if($teachersByContrat->count())
        <div class="card">
            <div class="card-header"><h3>Répartition par type de contrat</h3></div>
            <div class="card-body">
                @php
                    $contratColors = ['CDI' => '#1a7f5a', 'CDD' => '#1a5fb4', 'vacataire' => '#c57d0e', 'benevole' => '#6741d9'];
                    $maxCon = $teachersByContrat->max('total') ?: 1;
                @endphp
                <div class="bar-list">
                    @foreach($teachersByContrat as $row)
                    <div class="bar-item">
                        <div class="bar-header">
                            <span class="bar-label">{{ ucfirst($row->type_contrat) }}</span>
                            <span class="bar-val">{{ $row->total }} — {{ pct($row->total, $totalTeachers) }}%</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:{{ round($row->total/$maxCon*100) }}%;background:{{ $contratColors[$row->type_contrat] ?? '#64748b' }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ══ SECTION 3 : FINANCES ══ --}}
    <div>
        <div class="section-title">
            <div class="icon" style="background:var(--amb-l);color:var(--amber);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2>Situation financière</h2>
                <p>Année académique {{ $annee }}</p>
            </div>
        </div>

        {{-- Résumé 3 colonnes --}}
        <div class="fin-summary" style="margin-bottom:1rem;">
            <div class="fin-cell du">
                <div class="val">{{ $finStats ? number_format($finStats->total_du, 0, ',', ' ') : 0 }}</div>
                <div class="lbl">Total dû (FCFA)</div>
            </div>
            <div class="fin-cell paye">
                <div class="val">{{ $finStats ? number_format($finStats->total_paye, 0, ',', ' ') : 0 }}</div>
                <div class="lbl">Total encaissé (FCFA)</div>
            </div>
            <div class="fin-cell rest">
                <div class="val">{{ $finStats ? number_format($finStats->total_reste, 0, ',', ' ') : 0 }}</div>
                <div class="lbl">Reste à recouvrer (FCFA)</div>
            </div>
        </div>

        <div class="two-col">
            {{-- Statuts paiements --}}
            <div class="card">
                <div class="card-header"><h3>Statuts de paiement</h3></div>
                <div class="card-body">
                    @if($finStats)
                    @php
                        $total_fin = ($finStats->nb_payes + $finStats->nb_partiels + $finStats->nb_impayes) ?: 1;
                    @endphp
                    <div class="bar-list">
                        <div class="bar-item">
                            <div class="bar-header">
                                <span class="bar-label" style="color:var(--emerald);">✓ Payés</span>
                                <span class="bar-val">{{ $finStats->nb_payes }} ({{ pct($finStats->nb_payes, $total_fin) }}%)</span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ pct($finStats->nb_payes, $total_fin) }}%;background:var(--emerald);"></div>
                            </div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-header">
                                <span class="bar-label" style="color:var(--amber);">◑ Partiels</span>
                                <span class="bar-val">{{ $finStats->nb_partiels }} ({{ pct($finStats->nb_partiels, $total_fin) }}%)</span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ pct($finStats->nb_partiels, $total_fin) }}%;background:var(--amber);"></div>
                            </div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-header">
                                <span class="bar-label" style="color:var(--crimson);">✗ Impayés</span>
                                <span class="bar-val">{{ $finStats->nb_impayes }} ({{ pct($finStats->nb_impayes, $total_fin) }}%)</span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width:{{ pct($finStats->nb_impayes, $total_fin) }}%;background:var(--crimson);"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Taux recouvrement --}}
                    <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--canvas);
                                display:flex;align-items:center;gap:1.25rem;">
                        <div class="taux-ring">
                            <svg width="90" height="90" viewBox="0 0 90 90">
                                <circle cx="45" cy="45" r="38" fill="none" stroke="var(--canvas)" stroke-width="10"/>
                                <circle cx="45" cy="45" r="38" fill="none"
                                    stroke="{{ $tauxRecouvrement >= 80 ? 'var(--emerald)' : ($tauxRecouvrement >= 50 ? 'var(--amber)' : 'var(--crimson)') }}"
                                    stroke-width="10" stroke-linecap="round"
                                    stroke-dasharray="{{ round($tauxRecouvrement * 2.387) }} 239"
                                    />
                            </svg>
                            <div class="center">
                                {{ $tauxRecouvrement }}%
                                <small>recouv.</small>
                            </div>
                        </div>
                        <div>
                            <div style="font-size:1rem;font-weight:700;color:var(--ink);">Taux de recouvrement</div>
                            <div style="font-size:.8rem;color:var(--ink-60);margin-top:.25rem;">
                                {{ number_format($finStats->total_paye, 0, ',', ' ') }} FCFA collectés
                                sur {{ number_format($finStats->total_du, 0, ',', ' ') }} FCFA attendus
                            </div>
                            @if($tauxRecouvrement >= 80)
                            <span class="pill pill-green" style="margin-top:.5rem;">Excellent</span>
                            @elseif($tauxRecouvrement >= 50)
                            <span class="pill pill-amber" style="margin-top:.5rem;">Moyen</span>
                            @else
                            <span class="pill pill-red" style="margin-top:.5rem;">Faible</span>
                            @endif
                        </div>
                    </div>
                    @else
                    <p style="text-align:center;color:var(--ink-60);padding:2rem 0;">Aucune donnée financière</p>
                    @endif
                </div>
            </div>

            {{-- Évolution mensuelle --}}
            <div class="card">
                <div class="card-header">
                    <h3>Encaissements mensuels</h3>
                    <div style="display:flex;gap:.75rem;font-size:.7rem;">
                        <span style="display:flex;align-items:center;gap:.3rem;">
                            <span style="width:10px;height:10px;border-radius:2px;background:var(--emerald);display:inline-block;"></span> Encaissé
                        </span>
                        <span style="display:flex;align-items:center;gap:.3rem;">
                            <span style="width:10px;height:10px;border-radius:2px;background:var(--ink-20);display:inline-block;"></span> Dû
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if($finMensuel->count())
                    @php $maxPaye = $finMensuel->max('paye') ?: 1; $maxDu = $finMensuel->max('du') ?: 1; $maxM = max($maxPaye, $maxDu); @endphp
                    <div class="mensuel-bars">
                        @foreach($finMensuel as $m)
                        @php
                            $hP = $maxM > 0 ? round($m->paye / $maxM * 88) : 0;
                            $hD = $maxM > 0 ? round($m->du   / $maxM * 88) : 0;
                        @endphp
                        <div class="mensuel-col">
                            <div class="mensuel-bar-wrap">
                                <div class="mensuel-bar" style="height:{{ $hD }}px;background:var(--ink-20);" title="Dû: {{ number_format($m->du,0,',',' ') }} FCFA"></div>
                                <div class="mensuel-bar" style="height:{{ $hP }}px;background:var(--emerald);" title="Payé: {{ number_format($m->paye,0,',',' ') }} FCFA"></div>
                            </div>
                            <div class="mensuel-lbl">{{ substr($m->mois_label ?? '', 0, 3) }}</div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="text-align:center;color:var(--ink-60);padding:2rem 0;">Aucune donnée mensuelle</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Top débiteurs --}}
        @if($topDebiteurs->count())
        <div class="card" style="margin-top:1rem;">
            <div class="card-header">
                <h3>Top débiteurs — Reste à payer</h3>
                <span class="pill pill-red">{{ $finStats->nb_impayes ?? 0 }} dossiers impayés</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="rpt-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Apprenant</th>
                            <th>Classe</th>
                            <th style="text-align:right;">Reste à payer</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topDebiteurs as $i => $d)
                        <tr>
                            <td style="color:var(--ink-60);font-size:.75rem;">{{ $i+1 }}</td>
                            <td>
                                <div style="font-weight:600;">{{ $d->prenom }} {{ $d->nom }}</div>
                            </td>
                            <td>{{ $d->classe?->name ?? '—' }}</td>
                            <td style="text-align:right;font-weight:700;color:var(--crimson);">
                                {{ number_format($d->total_reste, 0, ',', ' ') }} FCFA
                            </td>
                            <td><span class="pill pill-red">Impayé</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- ══ SECTION 4 : ACADÉMIQUE + STAFF + PARENTS ══ --}}
    <div class="three-col">

        {{-- Académique --}}
        <div class="card">
            <div class="card-header">
                <h3 style="display:flex;align-items:center;gap:.5rem;">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--sapphire);"></span>
                    Structure académique
                </h3>
            </div>
            <div class="card-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--emerald);"></div>
                        <span class="activity-text">Classes actives</span>
                        <span class="activity-val">{{ $totalClasses }}</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--sapphire);"></div>
                        <span class="activity-text">Niveaux couverts</span>
                        <span class="activity-val">{{ $totalNiveaux }}</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--violet);"></div>
                        <span class="activity-text">Filières</span>
                        <span class="activity-val">{{ $totalFilieres }}</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--amber);"></div>
                        <span class="activity-text">Matières enseignées</span>
                        <span class="activity-val">{{ $totalMatieres }}</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:{{ $tauxAffectation >= 90 ? 'var(--emerald)' : 'var(--amber)' }};"></div>
                        <span class="activity-text">Taux affectation</span>
                        <span class="activity-val">{{ $tauxAffectation }}%</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--crimson);"></div>
                        <span class="activity-text">Sans classe</span>
                        <span class="activity-val" style="color:var(--crimson);">{{ $apprenantsSansClasse }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Staff --}}
        <div class="card">
            <div class="card-header">
                <h3 style="display:flex;align-items:center;gap:.5rem;">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--amber);"></span>
                    Personnel administratif
                </h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom:1rem;">
                    <div style="font-size:2rem;font-weight:800;color:var(--ink);">{{ $totalStaff }}</div>
                    <div style="font-size:.75rem;color:var(--ink-60);">membres du staff</div>
                    <div style="margin-top:.4rem;">
                        <span class="pill pill-green">{{ $actifsStaff }} actifs</span>
                        @if($totalStaff - $actifsStaff > 0)
                        <span class="pill" style="background:#f1f5f9;color:#64748b;margin-left:.3rem;">{{ $totalStaff - $actifsStaff }} inactifs</span>
                        @endif
                    </div>
                </div>
                @if($staffByUnit->count())
                <div class="activity-list">
                    @foreach($staffByUnit as $u)
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--amber);"></div>
                        <span class="activity-text">{{ $u->unite }}</span>
                        <span class="activity-val">{{ $u->total }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Parents --}}
        <div class="card">
            <div class="card-header">
                <h3 style="display:flex;align-items:center;gap:.5rem;">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--violet);"></span>
                    Parents & tuteurs
                </h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom:1rem;">
                    <div style="font-size:2rem;font-weight:800;color:var(--ink);">{{ $totalParents }}</div>
                    <div style="font-size:.75rem;color:var(--ink-60);">parents enregistrés</div>
                </div>

                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                    <div class="taux-ring" style="width:70px;height:70px;">
                        <svg width="70" height="70" viewBox="0 0 70 70">
                            <circle cx="35" cy="35" r="28" fill="none" stroke="var(--canvas)" stroke-width="8"/>
                            <circle cx="35" cy="35" r="28" fill="none"
                                stroke="{{ $tauxCouvertureParents >= 80 ? 'var(--emerald)' : ($tauxCouvertureParents >= 50 ? 'var(--amber)' : 'var(--crimson)') }}"
                                stroke-width="8" stroke-linecap="round"
                                stroke-dasharray="{{ round($tauxCouvertureParents * 1.759) }} 176"
                                style="transform:rotate(-90deg);transform-origin:50% 50%;"/>
                        </svg>
                        <div class="center" style="font-size:.8rem;">
                            {{ $tauxCouvertureParents }}%
                            <small>couv.</small>
                        </div>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:.875rem;color:var(--ink);">Couverture parentale</div>
                        <div style="font-size:.75rem;color:var(--ink-60);margin-top:.2rem;">
                            {{ $totalApprenants - $apprenantsSansParent }} élèves suivis
                        </div>
                    </div>
                </div>

                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--emerald);"></div>
                        <span class="activity-text">Avec parent</span>
                        <span class="activity-val">{{ $totalApprenants - $apprenantsSansParent }}</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--crimson);"></div>
                        <span class="activity-text">Sans parent</span>
                        <span class="activity-val" style="{{ $apprenantsSansParent > 0 ? 'color:var(--crimson);' : '' }}">
                            {{ $apprenantsSansParent }}
                        </span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot" style="background:var(--sapphire);"></div>
                        <span class="activity-text">Ratio parents/élèves</span>
                        <span class="activity-val">
                            {{ $totalApprenants > 0 ? round($totalParents / $totalApprenants, 2) : 0 }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ FOOTER NOTE ══ --}}
    <div style="text-align:center;padding:1rem;font-size:.75rem;color:var(--ink-60);
                border-top:1px solid var(--ink-20);">
        Rapport généré le {{ now()->format('d/m/Y à H:i') }} —
        {{ $institution->name }} — Année {{ $annee }}
    </div>

</div>{{-- /rpt-page --}}
@endsection