@extends('superadmin.master')
@section('title', 'Tableau de bord Super Admin')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap');

    :root {
        --font-main: 'Outfit', sans-serif;
        --font-mono: 'IBM Plex Mono', monospace;
    }

    .sa-page { background: var(--c-bg); min-height: 100vh; margin: -1.5rem; padding: 1.5rem; }
    .sa-page * { font-family: var(--font-main); box-sizing: border-box; }
    .mono { font-family: var(--font-mono) !important; }

    /* ── HERO ── */
    .sa-hero {
        background: linear-gradient(135deg, #0d1117 0%, #0f172a 50%, #0a0c10 100%);
        border: 1px solid var(--c-border); border-radius: 1rem;
        padding: 1.75rem 2rem; margin-bottom: 1.25rem;
        position: relative; overflow: hidden;
    }
    .sa-hero::before {
        content: ''; position: absolute; top: -60px; left: -60px;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(0,212,255,.06) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-scanline {
        position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--c-accent), transparent);
        animation: scanline 3s ease-in-out infinite;
    }
    @keyframes scanline {
        0%  { transform: translateX(-100%); opacity: 0; }
        50% { opacity: 1; }
        100%{ transform: translateX(100%);  opacity: 0; }
    }
    .hero-grid-bg {
        position: absolute; inset: 0;
        background-image: linear-gradient(var(--c-border) 1px,transparent 1px),linear-gradient(90deg,var(--c-border) 1px,transparent 1px);
        background-size: 40px 40px; opacity: .3;
    }

    /* Hero layout responsive */
    .hero-inner {
        position: relative; z-index: 1;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 1rem;
    }
    .hero-left { display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap; }
    .hero-actions { display: flex; align-items: center; gap: .625rem; flex-wrap: wrap; }

    @media (max-width: 640px) {
        .sa-hero { padding: 1.25rem; }
        .hero-name { font-size: 1.25rem !important; }
        .hero-actions { width: 100%; }
        .hero-actions .btn-sa-primary,
        .hero-actions .btn-sa-outline { flex: 1; justify-content: center; }
    }

    .sa-badge-superadmin {
        display: inline-flex; align-items: center; gap: .4rem;
        background: rgba(0,212,255,.08); border: 1px solid rgba(0,212,255,.25);
        border-radius: .375rem; padding: .2rem .6rem;
        font-size: .68rem; font-weight: 700; color: var(--c-accent);
        letter-spacing: .08em; text-transform: uppercase; font-family: var(--font-mono);
    }
    .sa-badge-superadmin::before {
        content: ''; width: 6px; height: 6px; background: var(--c-accent);
        border-radius: 50%; animation: pulse 2s infinite;
    }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }

    .hero-name { font-size: 1.75rem; font-weight: 800; color: #f8fafc; letter-spacing: -.03em; margin: .5rem 0 .25rem; }
    .hero-name span { color: var(--c-accent); }
    .hero-sub { font-size: .8125rem; color: var(--c-muted); display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; }
    .hero-stat { display: inline-flex; align-items: center; gap: .4rem; color: #94a3b8; font-size: .8rem; }
    .hero-stat strong { color: #cbd5e1; font-weight: 600; }

    /* ── KPI GRID ── */
    .kpi-grid-sa {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem; margin-bottom: 1.25rem;
    }
                       
    @media (max-width: 1100px) { .kpi-grid-sa { grid-template-columns: repeat(3,1fr); } }
    @media (max-width: 700px)  { .kpi-grid-sa { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 400px)  { .kpi-grid-sa { grid-template-columns: 1fr; } }
/* ─────────────────────────────
   GLOBAL MOBILE FIX
───────────────────────────── */
@media (max-width: 768px) {
    .sa-page {
        margin: 0 !important;
        padding: 0.75rem !important;
    }

    .dark-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* ─────────────────────────────
   HERO
───────────────────────────── */
@media (max-width: 768px) {
    .hero-inner {
        flex-direction: column;
        align-items: flex-start;
    }

    .hero-left {
        flex-direction: column;
        align-items: flex-start;
    }

    .hero-name {
        font-size: 1.2rem !important;
    }

    .hero-sub {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.3rem;
    }

    .hero-actions {
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .hero-actions button {
        width: 100%;
        justify-content: center;
    }
}

/* ─────────────────────────────
   KPI GRID
───────────────────────────── */
@media (max-width: 768px) {
    .kpi-grid-sa {
        grid-template-columns: 1fr 1fr !important;
    }
}

@media (max-width: 480px) {
    .kpi-grid-sa {
        grid-template-columns: 1fr !important;
    }
}

/* ─────────────────────────────
   TABLES
───────────────────────────── */
@media (max-width: 768px) {
    .sa-table th,
    .sa-table td {
        padding: 0.5rem !important;
        font-size: 0.75rem !important;
    }

    .tbl-wrap {
        overflow-x: auto;
    }
}

/* ─────────────────────────────
   GRID LAYOUTS
───────────────────────────── */
@media (max-width: 992px) {
    .two-col-sa {
        grid-template-columns: 1fr !important;
    }

    .three-col-sa {
        grid-template-columns: 1fr 1fr !important;
    }
}

@media (max-width: 576px) {
    .three-col-sa {
        grid-template-columns: 1fr !important;
    }
}

/* ─────────────────────────────
   BUTTONS
───────────────────────────── */
@media (max-width: 576px) {
    .btn-sa-primary,
    .btn-sa-outline,
    .btn-sa-danger {
        width: 100%;
        justify-content: center;
    }
}

/* ─────────────────────────────
   INPUTS / FILTERS
───────────────────────────── */
@media (max-width: 768px) {
    .sa-input {
        width: 100% !important;
    }

    .sa-select {
        width: 100%;
    }
}

/* ─────────────────────────────
   LOGS
───────────────────────────── */
@media (max-width: 768px) {
    .log-line {
        flex-direction: column;
        gap: 0.25rem;
    }

    .log-time,
    .log-level {
        min-width: auto;
    }
}

/* ─────────────────────────────
   SYSTEM STATUS
───────────────────────────── */
@media (max-width: 768px) {
    .sys-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}

/* ─────────────────────────────
   DANGER ZONE
───────────────────────────── */
@media (max-width: 768px) {
    .danger-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .danger-item button {
        width: 100%;
    }
}
    .kpi-sa {
        background: var(--c-surface); border: 1px solid var(--c-border);
        border-radius: .875rem; padding: 1.125rem 1.25rem;
        position: relative; overflow: hidden; transition: all .2s;
    }
    .kpi-sa:hover { border-color: var(--c-border-b); transform: translateY(-1px); }
    .kpi-sa-glow { position: absolute; top: 0; left: 0; right: 0; height: 1px; }
    .kpi-sa-val { font-size: 1.875rem; font-weight: 800; color: #f1f5f9; letter-spacing: -.04em; font-family: var(--font-mono); line-height: 1; margin: .625rem 0 .25rem; }
    .kpi-sa-lbl { font-size: .75rem; color: var(--c-muted); font-weight: 500; }
    .kpi-sa-delta { font-size: .72rem; font-weight: 600; display: flex; align-items: center; gap: .25rem; margin-top: .5rem; }
    .kpi-sa-icon { width: 32px; height: 32px; border-radius: .5rem; display: flex; align-items: center; justify-content: center; }
    .up { color: var(--c-green); } .down { color: var(--c-red); } .flat { color: var(--c-yellow); }

    /* ── TABS ── */
    .sa-tabs {
        display: flex; gap: 0; overflow-x: auto;
        border-bottom: 1px solid var(--c-border);
        padding: 0 .25rem;
        background: var(--c-surface);
        border-radius: .875rem .875rem 0 0;
        scrollbar-width: none;
    }
    .sa-tabs::-webkit-scrollbar { display: none; }

    .sa-tab {
        padding: .875rem 1rem; font-size: .8375rem; font-weight: 500; color: var(--c-muted);
        background: none; border: none; cursor: pointer;
        border-bottom: 2px solid transparent; margin-bottom: -1px;
        white-space: nowrap; display: flex; align-items: center; gap: .4rem;
        transition: all .2s; flex-shrink: 0;
    }
    .sa-tab:hover { color: #94a3b8; }
    .sa-tab.active { color: var(--c-accent); border-bottom-color: var(--c-accent); font-weight: 600; }
    .sa-tab-badge {
        background: rgba(0,212,255,.12); color: var(--c-accent);
        font-size: .6rem; font-weight: 700; padding: .1rem .4rem; border-radius: 99px; font-family: var(--font-mono);
    }
    .sa-tab-badge.red { background: rgba(239,68,68,.15); color: #f87171; }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ── DARK CARD ── */
    .dark-card { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: .875rem; overflow: hidden; }
    .dark-card:hover { border-color: var(--c-border-b); }
    .dark-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--c-border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem; }
    .dark-title { font-size: .9rem; font-weight: 600; color: #e2e8f0; }
    .dark-sub { font-size: .72rem; color: var(--c-muted); margin-top: .15rem; }

    /* ── SA TABLE ── */
    .sa-table { width: 100%; border-collapse: collapse; }
    .sa-table th { background: rgba(255,255,255,.02); padding: .625rem 1.25rem; text-align: left; font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--c-muted); border-bottom: 1px solid var(--c-border); }
    .sa-table td { padding: .875rem 1.25rem; border-bottom: 1px solid rgba(30,34,48,.7); font-size: .8375rem; color: #cbd5e1; vertical-align: middle; }
    .sa-table tr:last-child td { border-bottom: none; }
    .sa-table tr:hover td { background: rgba(255,255,255,.02); }

    /* ── BADGES ── */
    .sa-badge { display: inline-block; padding: .2rem .6rem; border-radius: .35rem; font-size: .68rem; font-weight: 700; letter-spacing: .03em; font-family: var(--font-mono); }
    .sb-green  { background: rgba(16,185,129,.12);  color: #34d399; border: 1px solid rgba(16,185,129,.2); }
    .sb-red    { background: rgba(239,68,68,.12);   color: #f87171; border: 1px solid rgba(239,68,68,.2); }
    .sb-blue   { background: rgba(0,212,255,.1);    color: #67e8f9; border: 1px solid rgba(0,212,255,.2); }
    .sb-yellow { background: rgba(245,158,11,.12);  color: #fbbf24; border: 1px solid rgba(245,158,11,.2); }
    .sb-purple { background: rgba(124,58,237,.12);  color: #a78bfa; border: 1px solid rgba(124,58,237,.2); }
    .sb-gray   { background: rgba(100,116,139,.12); color: #94a3b8; border: 1px solid rgba(100,116,139,.2); }

    /* ── BUTTONS ── */
    .btn-sa-primary {
        background: var(--c-accent); color: #0a0c10; padding: .5rem 1.125rem;
        border-radius: .5rem; font-size: .8375rem; font-weight: 700; border: none;
        cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: .45rem; white-space: nowrap;
    }
    .btn-sa-primary:hover { background: #38e1ff; box-shadow: 0 0 20px rgba(0,212,255,.3); }
    .btn-sa-outline {
        background: transparent; color: #94a3b8; padding: .5rem 1rem;
        border-radius: .5rem; font-size: .8375rem; font-weight: 500;
        border: 1px solid var(--c-border-b); cursor: pointer; transition: all .2s;
        display: inline-flex; align-items: center; gap: .4rem; white-space: nowrap;
    }
    .btn-sa-outline:hover { border-color: #4b5563; color: #e2e8f0; background: rgba(255,255,255,.03); }

    /* ── METRIC BAR ── */
    .metric-bar { background: rgba(255,255,255,.05); height: 4px; border-radius: 99px; overflow: hidden; width: 80px; }
    .metric-fill { height: 100%; border-radius: 99px; }

    /* ── LAYOUT GRIDS ── */
    .two-col-sa { display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }
    .three-col-sa { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; }

    @media (max-width: 1100px) { .two-col-sa { grid-template-columns: 1fr; } }
    @media (max-width: 900px)  { .three-col-sa { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 640px)  { .three-col-sa { grid-template-columns: 1fr; } }

    /* ── INPUTS ── */
    .sa-input { background: rgba(255,255,255,.04); border: 1px solid var(--c-border-b); border-radius: .5rem; padding: .6rem .875rem; font-size: .8375rem; color: #e2e8f0; width: 100%; transition: border .2s; font-family: var(--font-main); }
    .sa-input:focus { outline: none; border-color: rgba(0,212,255,.4); background: rgba(0,212,255,.04); }
    .sa-input::placeholder { color: var(--c-muted); }
    .sa-select { background: rgba(255,255,255,.04); border: 1px solid var(--c-border-b); border-radius: .5rem; padding: .6rem .875rem; font-size: .8375rem; color: #e2e8f0; transition: border .2s; font-family: var(--font-main); cursor: pointer; }
    .sa-select:focus { outline: none; border-color: rgba(0,212,255,.4); }

    /* ── LOG STREAM ── */
    .log-line { display: flex; gap: 1rem; padding: .5rem 1.25rem; border-bottom: 1px solid rgba(30,34,48,.6); font-family: var(--font-mono); font-size: .72rem; align-items: flex-start; transition: background .1s; flex-wrap: wrap; }
    .log-line:hover { background: rgba(255,255,255,.02); }
    .log-time { color: var(--c-muted); flex-shrink: 0; min-width: 80px; }
    .log-level { flex-shrink: 0; min-width: 60px; font-weight: 600; }
    .log-msg { color: #94a3b8; flex: 1; min-width: 200px; }
    .log-user { color: var(--c-accent); flex-shrink: 0; }
    .ll-info { color: #67e8f9; } .ll-warn { color: #fbbf24; } .ll-error { color: #f87171; } .ll-success { color: #34d399; }

    /* ── SYS STATUS ── */
    .sys-row { display: flex; align-items: center; justify-content: space-between; padding: .875rem 1.25rem; border-bottom: 1px solid var(--c-border); }
    .sys-row:last-child { border-bottom: none; }
    .sys-name { font-size: .85rem; font-weight: 600; color: #cbd5e1; display: flex; align-items: center; gap: .625rem; }
    .sys-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .sys-dot.online { background: var(--c-green); box-shadow: 0 0 6px var(--c-green); animation: pulse 2s infinite; }
    .sys-dot.warn { background: var(--c-yellow); box-shadow: 0 0 6px var(--c-yellow); }
    .sys-dot.error { background: var(--c-red); box-shadow: 0 0 6px var(--c-red); }

    /* ── SCHOOL ROW ── */
    .school-row { display: flex; align-items: center; gap: 1rem; padding: .875rem 1.25rem; border-bottom: 1px solid var(--c-border); transition: background .15s; }
    .school-row:hover { background: rgba(255,255,255,.02); }
    .school-row:last-child { border-bottom: none; }

    /* ── DANGER ZONE ── */
    .danger-item { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.5rem; border: 1px solid rgba(239,68,68,.12); border-radius: .625rem; background: rgba(239,68,68,.04); flex-wrap: wrap; }
    .danger-title { font-size: .875rem; font-weight: 600; color: #fca5a5; }
    .danger-desc { font-size: .75rem; color: #f87171; opacity: .7; margin-top: .15rem; }
    .btn-sa-danger { background: rgba(239,68,68,.1); color: #f87171; border: 1px solid rgba(239,68,68,.25); padding: .4rem .875rem; border-radius: .5rem; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .2s; display: inline-flex; align-items: center; gap: .35rem; }
    .btn-sa-danger:hover { background: rgba(239,68,68,.2); }

    /* ── TOGGLE ── */
    .toggle-wrap { position: relative; display: inline-block; width: 40px; height: 22px; }
    .toggle-wrap input { display: none; }
    .toggle-slider { position: absolute; inset: 0; background: rgba(255,255,255,.1); border-radius: 99px; cursor: pointer; transition: all .25s; border: 1px solid var(--c-border-b); }
    .toggle-slider::before { content: ''; position: absolute; width: 16px; height: 16px; background: #64748b; border-radius: 50%; top: 2px; left: 2px; transition: all .25s; }
    .toggle-wrap input:checked + .toggle-slider { background: rgba(0,212,255,.2); border-color: rgba(0,212,255,.4); }
    .toggle-wrap input:checked + .toggle-slider::before { background: var(--c-accent); transform: translateX(18px); }

    /* Table responsive wrapper */
    .tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
    .sa-page > * { animation: fadeIn .4s ease forwards; }

    @media (max-width: 640px) {
        .sa-tabs { padding: 0; }
        .sa-tab { padding: .75rem .7rem; font-size: .75rem; }
        .tab-content.active { padding: 1rem !important; }
        .main-content { padding: .75rem; }
        .sa-page { margin: -.75rem; padding: .75rem; }
    }
</style>

<div class="sa-page">

    {{-- ── HERO ── --}}
    <div class="sa-hero mb-5">
        <div class="hero-grid-bg"></div>
        <div class="hero-scanline"></div>
        <div class="hero-inner">
            <div class="hero-left">
                <div style="position:relative;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'SA') }}&background=00d4ff&color=0a0c10&size=128"
                         alt="Admin"
                         style="width:64px;height:64px;border-radius:.875rem;object-fit:cover;border:2px solid rgba(0,212,255,.3);box-shadow:0 0 20px rgba(0,212,255,.15);">
                    <div style="position:absolute;bottom:-3px;right:-3px;width:14px;height:14px;background:#10b981;border-radius:50%;border:2px solid #0a0c10;box-shadow:0 0 8px #10b981;"></div>
                </div>
                <div>
                    <div style="margin-bottom:.35rem;"><span class="sa-badge-superadmin">Super Administrateur</span></div>
                    <h1 class="hero-name">Bienvenue, <span>{{ Auth::user()->name ?? 'Admin' }}</span></h1>
                    <div class="hero-sub">
                        <span class="hero-stat">
                            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                            <strong>{{ $totalInstitutions }}</strong> établissements
                        </span>
                        <span style="color:#334155;">·</span>
                        <span class="hero-stat">
                            <strong>{{ $activeUsers }}</strong> utilisateurs actifs
                        </span>
                        <span style="color:#334155;">·</span>
                        <span class="hero-stat" style="color:#34d399;font-weight:600;">
                            <svg style="width:12px;height:12px;" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Système opérationnel
                        </span>
                    </div>
                </div>
            </div>

            <div class="hero-actions">
                <button class="btn-sa-outline">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Rapports
                </button>
                <button class="btn-sa-primary" onclick="window.location='{{ route('superadmin.institutions') }}'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvel établissement
                </button>
            </div>
        </div>
    </div>

    {{-- ── KPI GRID ── --}}
    <div class="kpi-grid-sa">
        @php
        $kpis = [
            ['val' => $totalInstitutions, 'lbl' => 'Établissements',   'delta' => '+2 ce mois',    'dir' => 'up',   'color' => '#00d4ff'],
            ['val' => $activeUsers,       'lbl' => 'Utilisateurs actifs','delta'=> '+147 ce mois',  'dir' => 'up',   'color' => '#a78bfa'],
            ['val' => $totalStudents,     'lbl' => 'Élèves inscrits',   'delta' => '+89 ce mois',   'dir' => 'up',   'color' => '#34d399'],
            ['val' => '99.3%',            'lbl' => 'Disponibilité',     'delta' => 'SLA respecté',  'dir' => 'flat', 'color' => '#fbbf24'],
            ['val' => 7,                  'lbl' => 'Incidents ouverts', 'delta' => '+2 vs hier',    'dir' => 'down', 'color' => '#f87171'],
        ];
        @endphp

        @foreach($kpis as $k)
        <div class="kpi-sa">
            <div class="kpi-sa-glow" style="background:linear-gradient(90deg,transparent,{{ $k['color'] }}60,transparent);"></div>
            <div class="kpi-sa-icon" style="background:{{ $k['color'] }}14;">
                <svg style="width:16px;height:16px;color:{{ $k['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                </svg>
            </div>
            <div class="kpi-sa-val mono">{{ $k['val'] }}</div>
            <div class="kpi-sa-lbl">{{ $k['lbl'] }}</div>
            <div class="kpi-sa-delta {{ $k['dir'] }}">
                {{ $k['dir'] === 'up' ? '↑' : ($k['dir'] === 'down' ? '↑' : '→') }}
                {{ $k['delta'] }}
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── TABS ── --}}
    <div class="dark-card">
        <div class="sa-tabs">
            <button class="sa-tab active" onclick="switchTab('overview',this)">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Vue globale
            </button>
            <button class="sa-tab" onclick="switchTab('schools',this)">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                Établissements
            </button>
            <button class="sa-tab" onclick="switchTab('users',this)">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                Utilisateurs
                <span class="sa-tab-badge">{{ $totalUsers }}</span>
            </button>
            <button class="sa-tab" onclick="switchTab('system',this)">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2"/></svg>
                Système
            </button>
            <button class="sa-tab" onclick="switchTab('logs',this)">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Logs
                <span class="sa-tab-badge red">7</span>
            </button>
            <button class="sa-tab" onclick="switchTab('config',this)">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Config
            </button>
        </div>

        {{-- ── TAB VUE GLOBALE ── --}}
        <div class="tab-content active p-5" id="tab-overview">
            <div class="two-col-sa">
                <div class="dark-card">
                    <div class="dark-header">
                        <div><div class="dark-title">Inscriptions & activité</div><div class="dark-sub">6 derniers mois — tous établissements</div></div>
                    </div>
                    <div style="padding:1.25rem;"><canvas id="globalChart" height="160"></canvas></div>
                </div>
                <div class="dark-card">
                    <div class="dark-header"><div class="dark-title">Répartition des rôles</div></div>
                    <div style="padding:1.25rem;"><canvas id="donutChart" height="160"></canvas></div>
                    <div style="padding:0 1.25rem 1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                        @foreach([['Élèves', '#34d399', $totalStudents], ['Enseignants', '#67e8f9', $totalTeachers], ['Parents', '#fbbf24', $totalParents], ['Admins', '#a78bfa', $totalAdmins]] as [$lbl, $col, $val])
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div style="display:flex;align-items:center;gap:.5rem;">
                                <span style="width:10px;height:10px;background:{{ $col }};border-radius:2px;display:inline-block;"></span>
                                <span style="font-size:.78rem;color:#94a3b8;">{{ $lbl }}</span>
                            </div>
                            <span class="mono" style="font-size:.78rem;color:#e2e8f0;font-weight:600;">{{ $val }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="dark-card">
                <div class="dark-header">
                    <div class="dark-title">Performance par établissement</div>
                    <span class="sa-badge sb-blue">Top 5</span>
                </div>
                <div class="tbl-wrap">
                    <table class="sa-table">
                        <thead><tr><th>#</th><th>Établissement</th><th>Élèves</th><th>Moy.</th><th>Statut</th></tr></thead>
                        <tbody>
                            @foreach($institutions as $index => $inst)
                            <tr>
                                <td><span class="mono" style="color:#94a3b8;font-weight:700;">{{ str_pad($index+1,2,'0',STR_PAD_LEFT) }}</span></td>
                                <td>
                                    <div style="font-weight:600;color:#e2e8f0;">{{ $inst->name }}</div>
                                    <div style="font-size:.7rem;color:var(--c-muted);">{{ $inst->pays ?? '—' }}</div>
                                </td>
                                <td><span class="mono">{{ $inst->apprenants_count }}</span></td>
                                <td>
                                    @php $m = rand(12,16); $col = $m>=14?'#34d399':($m>=12?'#fbbf24':'#f87171'); @endphp
                                    <span class="mono" style="color:{{ $col }};font-weight:700;">{{ $m }}.0</span>
                                </td>
                                <td>
                                    @if($inst->status)
                                        <span class="sa-badge sb-green">Actif</span>
                                    @else
                                        <span class="sa-badge sb-red">Inactif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── TAB ÉTABLISSEMENTS ── --}}
        <div class="tab-content p-5" id="tab-schools">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <input type="text" class="sa-input" placeholder="🔍 Rechercher..." style="width:220px;max-width:100%;">
                    <select class="sa-select"><option>Tous statuts</option><option>Actif</option><option>Inactif</option></select>
                </div>
                <button class="btn-sa-primary" onclick="window.location='{{ route('superadmin.institutions') }}'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Ajouter
                </button>
            </div>
            <div class="dark-card">
                <div class="tbl-wrap">
                    <table class="sa-table">
                        <thead><tr><th>Établissement</th><th>Type</th><th>Ville</th><th>Élèves</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($schools as $school)
                            <tr>
                                <td>
                                    <div style="font-weight:600;color:#e2e8f0;">{{ $school->name }}</div>
                                    <div class="mono" style="font-size:.68rem;color:var(--c-muted);">{{ $school->code }}</div>
                                </td>
                                <td><span class="sa-badge sb-purple">{{ $school->type }}</span></td>
                                <td>{{ $school->commune ?? ($school->ville ?? '—') }}</td>
                                <td class="mono">{{ $school->students_count }}</td>
                                <td>
                                    @if($school->status)
                                        <span class="sa-badge sb-green">Actif</span>
                                    @else
                                        <span class="sa-badge sb-red">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;gap:.4rem;">
                                        <a href="{{ route('superadmin.institutions') }}" class="btn-sa-outline" style="padding:.3rem .65rem;font-size:.72rem;">Gérer</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;">
                    <span style="font-size:.78rem;color:var(--c-muted);">{{ $schools->total() }} établissements</span>
                    {{ $schools->links() }}
                </div>
            </div>
        </div>

        {{-- ── TAB UTILISATEURS ── --}}
        <div class="tab-content p-5" id="tab-users">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem;">
                <input type="text" class="sa-input" placeholder="🔍 Rechercher un utilisateur..." style="width:260px;max-width:100%;">
                <button class="btn-sa-primary" onclick="window.location='{{ route('superadmin.users') }}'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouveau directeur
                </button>
            </div>
            <div class="dark-card">
                <div class="tbl-wrap">
                    <table class="sa-table">
                        <thead><tr><th>Nom</th><th>Email</th><th>Institution</th><th>Statut</th></tr></thead>
                        <tbody>
                            @forelse($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td style="color:var(--c-muted);">{{ $u->email }}</td>
                                <td>{{ $u->institution?->name ?? '—' }}</td>
                                <td>
                                    @if($u->status == 1)
                                        <span class="sa-badge sb-green">Actif</span>
                                    @else
                                        <span class="sa-badge sb-red">Inactif</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center;color:var(--c-muted);padding:2rem;">Aucun utilisateur</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border);">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        {{-- ── TAB SYSTÈME ── --}}
        <div class="tab-content p-5" id="tab-system">
            <div class="three-col-sa">
                <div class="dark-card">
                    <div class="dark-header"><div class="dark-title">État des services</div><span class="sa-badge sb-green">OK</span></div>
                    @foreach([['API Principale','online','98ms','99.9%'],['Base de données','online','12ms','100%'],['Service Email','online','245ms','99.7%'],['Stockage PDF','warn','72% plein','⚠'],['SMS Service','error','Hors ligne','Incident']] as [$name,$dot,$val,$uptime])
                    <div class="sys-row">
                        <div class="sys-name"><div class="sys-dot {{ $dot }}"></div>{{ $name }}</div>
                        <div>
                            <div style="font-size:.78rem;color:var(--c-muted);font-family:var(--font-mono);">{{ $val }}</div>
                            <div style="font-size:.72rem;color:var(--c-muted);">{{ $uptime }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="dark-card">
                    <div class="dark-header"><div class="dark-title">Ressources serveur</div></div>
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1.25rem;">
                        @foreach([['CPU','67%','67','#f59e0b','#f97316'],['RAM','42% (6.7/16GB)','42','#10b981','#34d399'],['Disque','78% (390/500GB)','78','#ef4444','#f97316'],['Bande passante','24%','24','#00d4ff','#67e8f9']] as [$lbl,$txt,$pct,$c1,$c2])
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;">
                                <span style="font-size:.8rem;color:#94a3b8;">{{ $lbl }}</span>
                                <span class="mono" style="font-size:.8rem;color:{{ $c1 }};font-weight:600;">{{ $txt }}</span>
                            </div>
                            <div style="background:rgba(255,255,255,.05);height:6px;border-radius:99px;overflow:hidden;">
                                <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,{{ $c1 }},{{ $c2 }});border-radius:99px;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="dark-card">
                    <div class="dark-header"><div class="dark-title">Sauvegardes</div></div>
                    @foreach([['Dernière sauvegarde','Aujourd\'hui 04:00','#34d399'],['Prochaine','Demain 04:00',null],['Taille','12.4 GB',null],['Tentatives suspectes','14 aujourd\'hui','#f87171'],['IPs bloquées','7 actives','#fbbf24']] as [$lbl,$val,$col])
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;border-bottom:1px solid var(--c-border);">
                        <span style="font-size:.8rem;color:#94a3b8;">{{ $lbl }}</span>
                        <span class="mono" style="font-size:.875rem;font-weight:700;color:{{ $col ?? '#e2e8f0' }};">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── TAB LOGS ── --}}
        <div class="tab-content" id="tab-logs">
            <div style="padding:1.25rem;border-bottom:1px solid var(--c-border);display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                <input type="text" class="sa-input" placeholder="Filtrer les logs..." style="width:220px;max-width:100%;">
                <select class="sa-select"><option>Tous niveaux</option><option>ERROR</option><option>WARN</option><option>INFO</option></select>
            </div>
            <div style="max-height:500px;overflow-y:auto;">
                @foreach([['09:47','ERROR','[SMS] Connexion refusée — provider hors ligne','system'],['09:45','WARN','[AUTH] Tentative suspecte IP:41.202.x.x bloquée','auth_guard'],['09:43','SUCCESS','[BACKUP] Sauvegarde 12.4 GB terminée en 4m32s','cron_job'],['09:41','INFO','[USERS] Nouvel admin créé: admin@college.edu','ibrahim.sy'],['09:30','INFO','[API] 1 024 requêtes — latence moy. 98ms','api_monitor']] as [$t,$l,$m,$u])
                <div class="log-line">
                    <span class="log-time">{{ $t }}</span>
                    <span class="log-level {{ $l==='ERROR'?'ll-error':($l==='WARN'?'ll-warn':($l==='SUCCESS'?'ll-success':'ll-info')) }}">{{ $l }}</span>
                    <span class="log-msg">{{ $m }}</span>
                    <span class="log-user">{{ $u }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── TAB CONFIG ── --}}
        <div class="tab-content p-5" id="tab-config">
            <div class="two-col-sa">
                <div class="dark-card">
                    <div class="dark-header"><div class="dark-title">Paramètres généraux</div><button class="btn-sa-primary" style="font-size:.78rem;padding:.375rem .875rem;">Sauvegarder</button></div>
                    <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1rem;">
                        @foreach([['Nom de la plateforme','text','SyntriForge Edu'],['URL','text','https://edu.syntriforge.io'],['Email de contact','email','support@syntriforge.io'],['Année scolaire','text','2024-2025']] as [$lbl,$type,$val])
                        <div>
                            <label style="font-size:.72rem;font-weight:600;color:var(--c-muted);letter-spacing:.05em;text-transform:uppercase;display:block;margin-bottom:.35rem;">{{ $lbl }}</label>
                            <input type="{{ $type }}" class="sa-input" value="{{ $val }}">
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="dark-card">
                    <div class="dark-header"><div class="dark-title">Modules</div></div>
                    @foreach([['Messagerie',true],['Bulletins numériques',true],['Notifications SMS',false],['2FA obligatoire',true],['Mode maintenance',false]] as [$lbl,$on])
                    <div style="padding:.875rem 1.5rem;border-bottom:1px solid var(--c-border);display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:.875rem;color:#cbd5e1;">{{ $lbl }}</span>
                        <label class="toggle-wrap"><input type="checkbox" {{ $on?'checked':'' }}><span class="toggle-slider"></span></label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="dark-card" style="margin-top:1.25rem;border-color:rgba(239,68,68,.2);">
                <div class="dark-header" style="border-color:rgba(239,68,68,.15);">
                    <div class="dark-title" style="color:#f87171;">⚠ Zone de danger</div>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.875rem;">
                    @foreach([['Vider le cache','Supprime les caches. Ralentissement temporaire.','Vider'],['Réinitialiser mots de passe','Force la réinitialisation au prochain login.','Forcer']] as [$t,$d,$a])
                    <div class="danger-item">
                        <div><div class="danger-title">{{ $t }}</div><div class="danger-desc">{{ $d }}</div></div>
                        <button class="btn-sa-danger">{{ $a }}</button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>{{-- end dark-card tabs --}}

</div>{{-- end sa-page --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    function switchTab(id, btn) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.sa-tab').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + id).classList.add('active');
        btn.classList.add('active');
    }

    const gridColor = 'rgba(255,255,255,0.04)', tickColor = '#4b5563';

    // Line chart
    const gCtx = document.getElementById('globalChart');
    if (gCtx) new Chart(gCtx, {
        type: 'line',
        data: {
            labels: ['Sept','Oct','Nov','Déc','Jan','Fév'],
            datasets: [{
                label: 'Utilisateurs actifs', data: [3200,3450,3780,3950,4600,{{ $activeUsers }}],
                borderColor: '#00d4ff', backgroundColor: 'rgba(0,212,255,.06)',
                fill: true, tension: .4, pointRadius: 4, borderWidth: 2,
            },{
                label: 'Inscriptions', data: [280,312,340,290,470,412],
                borderColor: '#a78bfa', backgroundColor: 'transparent',
                tension: .4, pointRadius: 3, borderWidth: 2, borderDash: [5,4],
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { color: '#6b7280', font: { size: 11 }, boxWidth: 12 }}},
            scales: { y: { grid: { color: gridColor }, ticks: { color: tickColor, font: { size: 10 }}}, x: { grid: { display: false }, ticks: { color: tickColor, font: { size: 10 }}}}
        }
    });

    // Donut
    const dCtx = document.getElementById('donutChart');
    if (dCtx) new Chart(dCtx, {
        type: 'doughnut',
        data: {
            labels: ['Élèves','Enseignants','Parents','Admins'],
            datasets: [{ data: [{{ $totalStudents }},{{ $totalTeachers }},{{ $totalParents }},{{ $totalAdmins }}], backgroundColor: ['#34d399','#67e8f9','#fbbf24','#a78bfa'], borderColor: '#111318', borderWidth: 3 }]
        },
        options: { cutout: '68%', plugins: { legend: { display: false }}, responsive: true }
    });
</script>
@endsection