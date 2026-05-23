@extends('teacher.master')

@section('content')
@include('teacher.partials.css')

<style>
/* ── PAGE-LEVEL OVERRIDES & ENHANCEMENTS ── */
:root {
    --teal:     #0d9488;
    --teal-mid: #14b8a6;
    --teal-dim: #f0fdfa;
    --indigo:   #6366f1;
    --amber:    #f59e0b;
    --rose:     #ef4444;
    --emerald:  #10b981;
    --ink:      #0f172a;
    --ink-mid:  #334155;
    --muted:    #64748b;
    --border:   #e2e8f0;
}

/* ── STAGGER ANIMATION ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}
.fade-up { animation: fadeUp .45s cubic-bezier(.22,1,.36,1) both; }
.d1  { animation-delay: .05s; }
.d2  { animation-delay: .10s; }
.d3  { animation-delay: .15s; }
.d4  { animation-delay: .20s; }
.d5  { animation-delay: .25s; }
.d6  { animation-delay: .30s; }
.d7  { animation-delay: .35s; }

/* ── HERO HEADER ── */
.dash-hero {
    position: relative;
    background: var(--ink);
    border-radius: 1.25rem;
    padding: 2rem 2rem 2rem 2rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.dash-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 80% at 90% 50%, rgba(13,148,136,.35) 0%, transparent 70%),
        radial-gradient(ellipse 40% 60% at 10% 80%, rgba(99,102,241,.2) 0%, transparent 70%);
    pointer-events: none;
}
/* Subtle grid lines */
.dash-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
    background-size: 32px 32px;
    pointer-events: none;
}
.hero-left { position: relative; z-index: 1; }
.hero-greeting {
    font-size: .75rem; font-weight: 700; letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--teal-mid); margin-bottom: .5rem;
}
.hero-name {
    font-size: 1.75rem; font-weight: 800; color: #fff;
    line-height: 1.15; letter-spacing: -.03em;
}
.hero-sub {
    font-size: .825rem; color: rgba(255,255,255,.45);
    margin-top: .375rem;
    display: flex; align-items: center; gap: .5rem;
}
.hero-dot { width: 4px; height: 4px; border-radius: 50%; background: rgba(255,255,255,.25); }
.hero-actions {
    display: flex; gap: .625rem; flex-wrap: wrap;
    position: relative; z-index: 1;
}
.hero-btn {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .575rem 1.125rem; border-radius: .625rem;
    font-size: .8rem; font-weight: 600; cursor: pointer;
    border: none; transition: all .2s; text-decoration: none;
    font-family: inherit;
}
.hero-btn-primary {
    background: var(--teal-mid); color: #fff;
}
.hero-btn-primary:hover { background: #0d9488; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(20,184,166,.4); }
.hero-btn-secondary {
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    color: rgba(255,255,255,.8);
}
.hero-btn-secondary:hover { background: rgba(255,255,255,.16); color: #fff; }

/* ── KPI GRID ── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media(max-width:960px) { .kpi-row { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px) { .kpi-row { grid-template-columns: 1fr; } }

.kpi-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.375rem;
    position: relative;
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
    cursor: default;
}
.kpi-card:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,.08);
    transform: translateY(-2px);
}
.kpi-top-stripe {
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    border-radius: 1rem 1rem 0 0;
}
.kpi-icon-wrap {
    width: 42px; height: 42px; border-radius: .75rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1rem;
}
.kpi-icon-wrap svg { width: 20px; height: 20px; }
.kpi-number {
    font-size: 2rem; font-weight: 800; color: var(--ink);
    letter-spacing: -.05em; line-height: 1;
    font-family: 'JetBrains Mono', monospace;
}
.kpi-label { font-size: .78rem; color: var(--muted); font-weight: 500; margin-top: .3rem; }
.kpi-footer {
    display: flex; align-items: center; gap: .3rem;
    font-size: .72rem; font-weight: 600; margin-top: .625rem;
}
.kpi-footer .dot {
    width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
}

/* ── SECTION TITLE ── */
.section-title {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; color: var(--muted);
    margin-bottom: .875rem;
    display: flex; align-items: center; gap: .5rem;
}
.section-title::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
}

/* ── CHART CARDS ── */
.chart-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
@media(max-width:768px) { .chart-grid { grid-template-columns: 1fr; } }

.chart-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 1rem;
    overflow: hidden;
    transition: box-shadow .2s;
}
.chart-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.06); }
.chart-header {
    padding: 1.125rem 1.375rem;
    border-bottom: 1px solid #f8fafc;
    display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem;
}
.chart-title { font-size: .875rem; font-weight: 700; color: var(--ink); }
.chart-sub   { font-size: .7rem; color: var(--muted); margin-top: .15rem; }
.chart-body  { padding: 1.125rem; }

/* ── BOTTOM GRID ── */
.bottom-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.25rem;
}
@media(max-width:1024px) { .bottom-grid { grid-template-columns: 1fr; } }

/* ── EVAL TABLE CARD ── */
.eval-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 1rem;
    overflow: hidden;
}
.eval-card-header {
    padding: 1.125rem 1.375rem;
    border-bottom: 1px solid #f8fafc;
    display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
}
.eval-card-title { font-size: .875rem; font-weight: 700; color: var(--ink); }

/* Enhanced table */
.eval-table { width: 100%; border-collapse: collapse; }
.eval-table th {
    background: #f8fafc;
    padding: .65rem 1.125rem;
    text-align: left; font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em; color: var(--muted);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.eval-table td {
    padding: .875rem 1.125rem;
    border-bottom: 1px solid #f8fafc;
    font-size: .8125rem; color: var(--ink-mid);
    vertical-align: middle;
}
.eval-table tr:last-child td { border-bottom: none; }
.eval-table tr:hover td { background: #fafbfc; }

/* Progress bar inline */
.mini-prog {
    display: flex; align-items: center; gap: .5rem;
    font-size: .75rem; font-family: 'JetBrains Mono', monospace;
}
.mini-prog-bar {
    flex: 1; height: 5px; background: #f1f5f9; border-radius: 99px; overflow: hidden; min-width: 40px;
}
.mini-prog-fill { height: 100%; border-radius: 99px; transition: width .5s; }

/* ── ACTIVITY CARD ── */
.activity-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 1rem;
    overflow: hidden;
}
.activity-header {
    padding: 1.125rem 1.375rem;
    border-bottom: 1px solid #f8fafc;
    display: flex; align-items: center; justify-content: space-between;
}
.activity-title { font-size: .875rem; font-weight: 700; color: var(--ink); }
.activity-body  { padding: 1.125rem; }
.activity-item  { display: flex; gap: .875rem; position: relative; padding-bottom: 1rem; }
.activity-item:not(:last-child)::after {
    content: '';
    position: absolute; left: 14px; top: 30px; bottom: 0;
    width: 1.5px; background: #f1f5f9;
}
.activity-icon {
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; flex-shrink: 0;
    position: relative; z-index: 1;
}
.activity-text { font-size: .8125rem; color: var(--ink-mid); font-weight: 500; }
.activity-meta { font-size: .7rem; color: var(--muted); margin-top: .15rem; }
.activity-empty { text-align: center; padding: 2rem; }

/* ── BADGES ── */
.pill {
    display: inline-flex; align-items: center;
    padding: .2rem .65rem; border-radius: 99px;
    font-size: .68rem; font-weight: 600; white-space: nowrap;
}
.pill-teal    { background: #f0fdfa; color: #0d9488; }
.pill-green   { background: #f0fdf4; color: #16a34a; }
.pill-amber   { background: #fffbeb; color: #d97706; }
.pill-red     { background: #fef2f2; color: #dc2626; }

/* ── ACTION BUTTON ── */
.action-btn {
    display: inline-flex; align-items: center; gap: .375rem;
    padding: .35rem .8rem; border-radius: .5rem;
    font-size: .75rem; font-weight: 600; cursor: pointer;
    border: none; transition: all .18s; text-decoration: none;
    font-family: inherit;
}
.action-btn-primary { background: var(--ink); color: #fff; }
.action-btn-primary:hover { background: #1e293b; }
.action-btn-outline { background: #fff; color: var(--muted); border: 1px solid var(--border); }
.action-btn-outline:hover { background: #f8fafc; color: var(--ink); }
.action-btn-teal { background: var(--teal-dim); color: var(--teal); }
.action-btn-teal:hover { background: #ccfbf1; }

/* ── RESPONSIVE ALERT ── */
.dash-alert {
    display: flex; align-items: flex-start; gap: .75rem;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: .75rem; padding: .875rem 1.125rem;
    margin-bottom: 1.25rem; font-size: .875rem; color: #15803d;
}
.dash-alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: .05rem; }
</style>

{{-- ── ALERT ── --}}
@if(session('success'))
<div class="dash-alert fade-up d1">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

{{-- ── HERO HEADER ── --}}
<div class="dash-hero fade-up d1">
    <div class="hero-left">
        <div class="hero-greeting">✦ Espace Enseignant</div>
        <div class="hero-name">Bonjour, {{ $teacher->prenom }} 👋</div>
        <div class="hero-sub">
            <span>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
            <span class="hero-dot"></span>
            <span>{{ $institution->name ?? '' }}</span>
            @if(isset($teacher->specialite))
                <span class="hero-dot"></span>
                <span>{{ $teacher->specialite }}</span>
            @endif
        </div>
    </div>
    <div class="hero-actions">
        <a href="{{ route('teacher.classes.index') }}" class="hero-btn hero-btn-secondary">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Mes classes
        </a>
        <a href="{{ route('teacher.notes.index') }}" class="hero-btn hero-btn-primary">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Saisir des notes
        </a>
    </div>
</div>

{{-- ── KPI CARDS ── --}}
<p class="section-title fade-up d2">Vue d'ensemble</p>
<div class="kpi-row">
    {{-- Élèves --}}
    <div class="kpi-card fade-up d2">
        <div class="kpi-top-stripe" style="background:linear-gradient(90deg,#0d9488,#14b8a6);"></div>
        <div class="kpi-icon-wrap" style="background:#f0fdfa;">
            <svg style="color:#0d9488;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
            </svg>
        </div>
        <div class="kpi-number">{{ $stats['students'] }}</div>
        <div class="kpi-label">Élèves encadrés</div>
        <div class="kpi-footer" style="color:#0d9488;">
            <div class="dot" style="background:#0d9488;"></div>
            {{ $stats['classes'] }} classe(s) assignée(s)
        </div>
    </div>

    {{-- Moyenne --}}
    <div class="kpi-card fade-up d3">
        <div class="kpi-top-stripe" style="background:linear-gradient(90deg,#6366f1,#818cf8);"></div>
        <div class="kpi-icon-wrap" style="background:#eef2ff;">
            <svg style="color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div class="kpi-number">{{ $stats['average_grade'] }}</div>
        <div class="kpi-label">Moyenne générale <span style="color:#94a3b8;font-size:.7rem;">/ 20</span></div>
        <div class="kpi-footer" style="color:#6366f1;">
            <div class="dot" style="background:#6366f1;"></div>
            Toutes classes confondues
        </div>
    </div>

    {{-- Évaluations --}}
    <div class="kpi-card fade-up d4">
        <div class="kpi-top-stripe" style="background:linear-gradient(90deg,#f59e0b,#fbbf24);"></div>
        <div class="kpi-icon-wrap" style="background:#fffbeb;">
            <svg style="color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <div class="kpi-number">{{ $evaluations->count() }}</div>
        <div class="kpi-label">Évaluations créées</div>
        <div class="kpi-footer" style="color:#d97706;">
            <div class="dot" style="background:#f59e0b;"></div>
            {{ $stats['hours'] }}h estimées / semaine
        </div>
    </div>

    {{-- Matières --}}
    <div class="kpi-card fade-up d5">
        <div class="kpi-top-stripe" style="background:linear-gradient(90deg,#10b981,#34d399);"></div>
        <div class="kpi-icon-wrap" style="background:#ecfdf5;">
            <svg style="color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div class="kpi-number">{{ $stats['subjects'] }}</div>
        <div class="kpi-label">Matières enseignées</div>
        <div class="kpi-footer" style="color:#059669;">
            <div class="dot" style="background:#10b981;"></div>
            {{ $stats['levels'] }} niveau(x) couverts
        </div>
    </div>
</div>

{{-- ── CHARTS ── --}}
<p class="section-title fade-up d5">Analyse des performances</p>
<div class="chart-grid fade-up d5">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Évolution des moyennes</div>
                <div class="chart-sub">6 derniers mois · par classe</div>
            </div>
            <span class="pill pill-teal">Temps réel</span>
        </div>
        <div class="chart-body">
            <canvas id="avgChart" height="170"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <div class="chart-title">Répartition des notes</div>
                <div class="chart-sub">Distribution · toutes classes</div>
            </div>
            <span class="pill pill-amber">Vue globale</span>
        </div>
        <div class="chart-body">
            <canvas id="distChart" height="170"></canvas>
        </div>
    </div>
</div>

{{-- ── BOTTOM : TABLE + ACTIVITY ── --}}
<p class="section-title fade-up d6">Évaluations & activité récente</p>
<div class="bottom-grid fade-up d6">

    {{-- TABLE DES ÉVALUATIONS --}}
    <div class="eval-card">
        <div class="eval-card-header">
            <div>
                <div class="eval-card-title">Évaluations en attente</div>
                @php $pending = $evaluations->filter(fn($e) => $e->grades->count() < ($e->subject->classe?->apprenants->count() ?? 0) && ($e->subject->classe?->apprenants->count() ?? 0) > 0); @endphp
                <div style="font-size:.72rem;color:var(--muted);margin-top:.2rem;">{{ $pending->count() }} correction(s) à finaliser</div>
            </div>
            <div style="display:flex;gap:.5rem;align-items:center;">
                <span class="pill pill-amber">{{ $pending->count() }} à traiter</span>
                <a href="{{ route('teacher.notes.index') }}" class="action-btn action-btn-primary">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Saisir notes
                </a>
            </div>
        </div>

        @if($evaluations->isEmpty())
            <div style="text-align:center;padding:3.5rem 1rem;">
                <div style="font-size:2.5rem;margin-bottom:.75rem;opacity:.4;">📋</div>
                <div style="font-size:.875rem;color:var(--muted);">Aucune évaluation créée</div>
                <a href="{{ route('teacher.evaluations.index') }}" class="action-btn action-btn-teal" style="margin-top:1rem;display:inline-flex;">
                    Créer une évaluation
                </a>
            </div>
        @else
        <div style="overflow-x:auto;">
            <table class="eval-table">
                <thead><tr>
                    <th>Évaluation</th>
                    <th>Classe</th>
                    <th>Date</th>
                    <th>Progression</th>
                    <th>Statut</th>
                    <th></th>
                </tr></thead>
                <tbody>
                    @foreach($evaluations->take(5) as $eval)
                    @php
                        $total  = $eval->subject->classe?->apprenants->count() ?? 0;
                        $graded = $eval->grades->count();
                        $pct    = $total > 0 ? round($graded / $total * 100) : 0;
                        $done   = $total > 0 && $graded >= $total;
                        $barColor = $done ? '#10b981' : ($graded > 0 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;color:var(--ink);font-size:.8125rem;">{{ $eval->title }}</div>
                            <div style="font-size:.7rem;color:var(--muted);margin-top:.1rem;">{{ $eval->subject->name ?? '' }}</div>
                        </td>
                        <td>
                            <span class="pill pill-teal">{{ $eval->subject->classe->name ?? '—' }}</span>
                        </td>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:.75rem;color:var(--muted);">
                            {{ \Carbon\Carbon::parse($eval->date)->format('d M Y') }}
                        </td>
                        <td style="min-width:130px;">
                            <div class="mini-prog">
                                <div class="mini-prog-bar">
                                    <div class="mini-prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
                                </div>
                                <span style="color:{{ $barColor }};font-weight:700;min-width:36px;">{{ $graded }}/{{ $total }}</span>
                            </div>
                        </td>
                        <td>
                            @if($done)
                                <span class="pill pill-green">✓ Terminé</span>
                            @elseif($graded > 0)
                                <span class="pill pill-amber">En cours</span>
                            @else
                                <span class="pill pill-red">À saisir</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('teacher.notes.index', ['evaluation_id' => $eval->id]) }}"
                               class="action-btn {{ $done ? 'action-btn-outline' : 'action-btn-teal' }}">
                                {{ $done ? 'Voir' : 'Saisir' }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:.875rem 1.25rem;border-top:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:.75rem;color:var(--muted);">{{ $evaluations->count() }} évaluation(s) au total</span>
            <a href="{{ route('teacher.evaluations.index') }}" class="action-btn action-btn-outline">
                Voir tout
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        @endif
    </div>

    {{-- ACTIVITÉ RÉCENTE --}}
    <div class="activity-card">
        <div class="activity-header">
            <div class="activity-title">Activité récente</div>
            <span class="pill pill-teal">{{ $evaluations->count() }} entrées</span>
        </div>
        <div class="activity-body">
            @php $icons = ['DS' => ['emoji' => '📝', 'bg' => '#fef3c7', 'color' => '#d97706'], 'DM' => ['emoji' => '📖', 'bg' => '#dbeafe', 'color' => '#2563eb'], 'Interrogation' => ['emoji' => '✏️', 'bg' => '#f3e8ff', 'color' => '#9333ea'], 'Examen' => ['emoji' => '📋', 'bg' => '#f0fdf4', 'color' => '#16a34a']]; @endphp
            @forelse($evaluations->take(7) as $eval)
            @php $ic = $icons[$eval->type] ?? ['emoji' => '📄', 'bg' => '#f1f5f9', 'color' => '#64748b']; @endphp
            <div class="activity-item">
                <div class="activity-icon" style="background:{{ $ic['bg'] }};">
                    {{ $ic['emoji'] }}
                </div>
                <div>
                    <div class="activity-text">{{ $eval->title }}</div>
                    <div class="activity-meta">
                        <span style="font-weight:500;color:{{ $ic['color'] }};">{{ $eval->subject->classe->name ?? '' }}</span>
                        · {{ \Carbon\Carbon::parse($eval->date)->locale('fr')->diffForHumans() }}
                    </div>
                </div>
            </div>
            @empty
            <div class="activity-empty">
                <div style="font-size:2rem;opacity:.4;margin-bottom:.5rem;">🕐</div>
                <div style="font-size:.8rem;color:var(--muted);">Aucune activité récente</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── CHARTS JS ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const gc = '#f1f5f9', tc = '#94a3b8';
const chartData = @json($chartData);
const distData  = @json($gradeDistribution);

Chart.defaults.font.family = "'JetBrains Mono', monospace";

/* ── Line chart ── */
const avgCtx = document.getElementById('avgChart');
if (chartData.datasets?.length) {
    const colors = ['#0d9488','#6366f1','#f59e0b','#10b981','#ef4444'];
    chartData.datasets.forEach((ds, i) => {
        ds.borderColor     = colors[i % colors.length];
        ds.backgroundColor = colors[i % colors.length] + '18';
        ds.fill            = true;
        ds.tension         = 0.4;
        ds.pointRadius     = 4;
        ds.pointHoverRadius = 6;
        ds.pointBackgroundColor = colors[i % colors.length];
        ds.borderWidth     = 2.5;
    });
    new Chart(avgCtx, {
        type: 'line',
        data: { labels: chartData.labels, datasets: chartData.datasets },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11, family: 'Sora' }, boxWidth: 10, padding: 16, color: '#64748b', usePointStyle: true }
                },
                tooltip: {
                    backgroundColor: '#0f172a', titleColor: '#94a3b8',
                    bodyColor: '#fff', cornerRadius: 8, padding: 10
                }
            },
            scales: {
                y: { min: 0, max: 20, grid: { color: gc, drawBorder: false }, ticks: { color: tc, font: { size: 10 }, stepSize: 5 } },
                x: { grid: { display: false }, ticks: { color: tc, font: { size: 10 } } }
            }
        }
    });
} else {
    avgCtx.parentElement.innerHTML = '<div style="text-align:center;padding:3rem;color:#94a3b8;font-size:.8rem;">📊 Aucune donnée de notes disponible</div>';
}

/* ── Bar chart ── */
const distCtx = document.getElementById('distChart');
const total = distData.data?.reduce((a,b) => a+b, 0) ?? 0;
if (total > 0) {
    const bgColors = distData.data.map((_, i) => {
        const palette = ['#fecaca','#fca5a5','#fcd34d','#dbeafe','#bfdbfe','#a7f3d0','#6ee7b7'];
        return palette[i] ?? '#e2e8f0';
    });
    new Chart(distCtx, {
        type: 'bar',
        data: {
            labels: distData.labels,
            datasets: [{
                data: distData.data,
                backgroundColor: bgColors,
                borderRadius: 6,
                borderSkipped: false,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a', titleColor: '#94a3b8',
                    bodyColor: '#fff', cornerRadius: 8, padding: 10
                }
            },
            scales: {
                y: { grid: { color: gc, drawBorder: false }, ticks: { color: tc, font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { color: tc, font: { size: 10 } } }
            }
        }
    });
} else {
    distCtx.parentElement.innerHTML = '<div style="text-align:center;padding:3rem;color:#94a3b8;font-size:.8rem;">📊 Aucune note saisie pour ce tableau</div>';
}
</script>
@endsection