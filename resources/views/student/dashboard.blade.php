@extends('student.master')
@section('title', 'Tableau de bord')
@section('page-title', 'Bonjour, ' . $apprenant->prenom . ' 👋')
@section('page-sub', now()->locale('fr')->isoFormat('dddd D MMMM YYYY'))

@section('content')

<style>
/* ── ANIMATIONS ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes countUp {
    from { opacity: 0; transform: scale(.85); }
    to   { opacity: 1; transform: scale(1); }
}
.fu  { animation: fadeUp .45s cubic-bezier(.22,1,.36,1) both; }
.fu1 { animation-delay: .04s; } .fu2 { animation-delay: .09s; }
.fu3 { animation-delay: .14s; } .fu4 { animation-delay: .19s; }
.fu5 { animation-delay: .24s; } .fu6 { animation-delay: .29s; }
.fu7 { animation-delay: .34s; }

/* ── HERO ── */
.s-hero {
    position: relative;
    background: var(--ink);
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
.s-hero::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 55% 90% at 95% 40%, rgba(99,102,241,.4) 0%, transparent 65%),
        radial-gradient(ellipse 35% 60% at 5% 90%, rgba(14,165,233,.2) 0%, transparent 65%);
    pointer-events: none;
}
.s-hero::after {
    content: '';
    position: absolute; inset: 0;
    background-image:
        radial-gradient(circle, rgba(255,255,255,.06) 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
}
.hero-content { position: relative; z-index: 1; }
.hero-eyebrow {
    font-size: .68rem; font-weight: 700; letter-spacing: .14em;
    text-transform: uppercase; color: var(--primary-mid);
    margin-bottom: .5rem; display: flex; align-items: center; gap: .4rem;
}
.hero-eyebrow::before {
    content: '';
    display: inline-block; width: 18px; height: 2px;
    background: var(--primary-mid); border-radius: 1px;
}
.hero-name {
    font-size: 1.875rem; font-weight: 800; color: #fff;
    letter-spacing: -.04em; line-height: 1.1;
}
.hero-meta {
    font-size: .8rem; color: rgba(255,255,255,.42);
    margin-top: .5rem;
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
}
.hero-sep { width: 3px; height: 3px; border-radius: 50%; background: rgba(255,255,255,.22); }

/* Rank badge in hero */
.hero-rank {
    position: relative; z-index: 1;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    text-align: center;
    min-width: 130px;
    flex-shrink: 0;
    backdrop-filter: blur(8px);
}
.hero-rank-num {
    font-size: 2.5rem; font-weight: 900; color: #fff;
    letter-spacing: -.06em; line-height: 1;
    font-family: 'JetBrains Mono', monospace;
}
.hero-rank-num sup {
    font-size: .9rem; font-weight: 700; vertical-align: super;
    color: var(--primary-mid);
}
.hero-rank-label { font-size: .68rem; color: rgba(255,255,255,.45); margin-top: .3rem; font-weight: 500; letter-spacing: .06em; text-transform: uppercase; }
.hero-rank-class { font-size: .72rem; color: rgba(255,255,255,.65); margin-top: .15rem; font-weight: 600; }

/* ── KPI STRIP ── */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media(max-width:768px) { .kpi-strip { grid-template-columns: 1fr 1fr; } }
@media(max-width:420px) { .kpi-strip { grid-template-columns: 1fr; } }

.kpi-tile {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.25rem;
    display: flex; align-items: center; gap: 1rem;
    transition: box-shadow .2s, transform .2s;
    cursor: default;
    position: relative;
    overflow: hidden;
}
.kpi-tile::before {
    content: '';
    position: absolute; right: -12px; bottom: -14px;
    width: 70px; height: 70px; border-radius: 50%;
    opacity: .06;
}
.kpi-tile:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,.08);
    transform: translateY(-2px);
}
.kpi-tile-icon {
    width: 44px; height: 44px; border-radius: .75rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.kpi-tile-icon svg { width: 20px; height: 20px; }
.kpi-tile-val {
    font-size: 1.625rem; font-weight: 800; color: var(--ink);
    letter-spacing: -.04em; line-height: 1;
    font-family: 'JetBrains Mono', monospace;
    animation: countUp .5s cubic-bezier(.22,1,.36,1) both;
}
.kpi-tile-label { font-size: .75rem; color: var(--muted); font-weight: 500; margin-top: .2rem; }
.kpi-tile-note  { font-size: .68rem; font-weight: 600; margin-top: .35rem; display: flex; align-items: center; gap: .3rem; }

/* ── SECTION HEADER ── */
.sec-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .875rem;
}
.sec-title {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .1em; color: var(--muted);
    display: flex; align-items: center; gap: .5rem;
}
.sec-title::before {
    content: '';
    width: 3px; height: 14px; border-radius: 2px;
    background: var(--primary); display: inline-block;
}

/* ── CHART CARDS ── */
.chart-row {
    display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;
    margin-bottom: 1.5rem;
}
@media(max-width:768px) { .chart-row { grid-template-columns: 1fr; } }

.ch-card {
    background: #fff; border: 1px solid var(--border);
    border-radius: 1rem; overflow: hidden;
    transition: box-shadow .2s;
}
.ch-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.06); }
.ch-card-head {
    padding: 1rem 1.375rem; border-bottom: 1px solid #f8fafc;
    display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem;
}
.ch-card-title { font-size: .875rem; font-weight: 700; color: var(--ink); }
.ch-card-sub   { font-size: .7rem; color: var(--muted); margin-top: .15rem; }
.ch-card-body  { padding: 1.125rem 1.25rem; }

/* ── TAG PILL ── */
.tag {
    display: inline-flex; align-items: center;
    padding: .2rem .65rem; border-radius: 99px;
    font-size: .67rem; font-weight: 700; white-space: nowrap;
}
.tag-indigo { background: var(--primary-light); color: var(--primary); }
.tag-sky    { background: var(--sky-light);     color: #0369a1; }
.tag-amber  { background: var(--amber-light);   color: #b45309; }
.tag-green  { background: var(--success-light); color: #15803d; }
.tag-red    { background: var(--danger-light);  color: #b91c1c; }
.tag-gray   { background: #f3f4f6;              color: #374151; }
.tag-purple { background: #ede9fe;              color: #6d28d9; }

/* ── BOTTOM GRID ── */
.bottom-row {
    display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem;
}
@media(max-width:1024px) { .bottom-row { grid-template-columns: 1fr; } }

/* ── SUBJECT TABLE ── */
.sub-card {
    background: #fff; border: 1px solid var(--border);
    border-radius: 1rem; overflow: hidden;
}
.sub-card-head {
    padding: 1rem 1.375rem; border-bottom: 1px solid #f8fafc;
    display: flex; align-items: center; justify-content: space-between;
}
.sub-card-title { font-size: .875rem; font-weight: 700; color: var(--ink); }

.sub-table { width: 100%; border-collapse: collapse; }
.sub-table th {
    background: #f8fafc; padding: .6rem 1.125rem;
    text-align: left; font-size: .62rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em; color: var(--muted);
    border-bottom: 1px solid var(--border); white-space: nowrap;
}
.sub-table td {
    padding: .8rem 1.125rem; border-bottom: 1px solid #f8fafc;
    font-size: .8125rem; color: var(--ink-mid); vertical-align: middle;
}
.sub-table tr:last-child td { border-bottom: none; }
.sub-table tr:hover td { background: #fafbfc; }

.sub-name   { font-weight: 700; color: var(--ink); font-size: .8125rem; }
.sub-teacher{ font-size: .7rem; color: var(--muted); margin-top: .1rem; }

/* Progress bar */
.prog-wrap { display: flex; align-items: center; gap: .5rem; }
.prog-bar  { flex: 1; height: 5px; background: #f1f5f9; border-radius: 99px; overflow: hidden; min-width: 50px; }
.prog-fill { height: 100%; border-radius: 99px; transition: width .6s cubic-bezier(.22,1,.36,1); }
.prog-pct  { font-size: .7rem; font-family: 'JetBrains Mono', monospace; color: var(--muted); min-width: 30px; text-align: right; }

/* Grade circle */
.grade-circle {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 38px; height: 28px; padding: 0 7px;
    border-radius: .4375rem; font-size: .8rem; font-weight: 800;
    font-family: 'JetBrains Mono', monospace;
}
.gc-A { background: #d1fae5; color: #065f46; }
.gc-B { background: #dbeafe; color: #1e40af; }
.gc-C { background: #fef3c7; color: #92400e; }
.gc-D { background: #fee2e2; color: #991b1b; }

/* ── EVAL FEED ── */
.eval-feed {
    background: #fff; border: 1px solid var(--border);
    border-radius: 1rem; overflow: hidden;
}
.eval-feed-head {
    padding: 1rem 1.375rem; border-bottom: 1px solid #f8fafc;
    display: flex; align-items: center; justify-content: space-between;
}
.eval-feed-title { font-size: .875rem; font-weight: 700; color: var(--ink); }

.eval-item {
    display: flex; align-items: center; gap: .875rem;
    padding: .875rem 1.25rem; border-bottom: 1px solid #f8fafc;
    transition: background .15s;
}
.eval-item:last-of-type { border-bottom: none; }
.eval-item:hover { background: #fafbfc; }

.eval-icon {
    width: 36px; height: 36px; border-radius: .625rem;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.eval-info { flex: 1; min-width: 0; }
.eval-title  { font-size: .8rem; font-weight: 600; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.eval-meta   { font-size: .68rem; color: var(--muted); margin-top: .15rem; }

/* CTA button */
.cta-btn {
    display: flex; align-items: center; justify-content: center; gap: .4rem;
    padding: .6rem 1rem; border-radius: .625rem;
    font-size: .78rem; font-weight: 700; cursor: pointer;
    border: none; transition: all .2s; text-decoration: none;
    font-family: inherit; width: 100%;
}
.cta-primary { background: var(--primary); color: #fff; }
.cta-primary:hover { background: #4f46e5; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,.3); }
.cta-outline { background: #fff; color: var(--ink-mid); border: 1px solid var(--border); }
.cta-outline:hover { background: #f8fafc; color: var(--ink); }
</style>

{{-- ── HERO ── --}}
<div class="s-hero fu fu1">
    <div class="hero-content">
        <div class="hero-eyebrow">Espace étudiant</div>
        <div class="hero-name">Bonjour, {{ $apprenant->prenom }} 👋</div>
        <div class="hero-meta">
            <span>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
            <span class="hero-sep"></span>
            <span>{{ $classe->name ?? '—' }}</span>
            @if(isset($institution))
                <span class="hero-sep"></span>
                <span>{{ $institution->name }}</span>
            @endif
        </div>
    </div>
    <div class="hero-rank">
        <div class="hero-rank-num">
            {{ $rang['rang'] }}<sup>e</sup>
        </div>
        <div class="hero-rank-label">Rang</div>
        <div class="hero-rank-class">sur {{ $rang['total'] }} élèves</div>
    </div>
</div>

{{-- ── KPI TILES ── --}}
<div class="sec-head fu fu2">
    <div class="sec-title">Indicateurs clés</div>
</div>
<div class="kpi-strip fu fu2">
    {{-- Moyenne --}}
    <div class="kpi-tile">
        <div class="kpi-tile-icon" style="background:var(--primary-light);">
            <svg style="color:var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <div class="kpi-tile-val">{{ $moyenneGenerale ?? '—' }}</div>
            <div class="kpi-tile-label">Moyenne générale / 20</div>
            <div class="kpi-tile-note" style="color:var(--primary);">
                <svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>
                {{ $subjects->count() }} matière(s)
            </div>
        </div>
    </div>

    {{-- Évaluations --}}
    <div class="kpi-tile">
        <div class="kpi-tile-icon" style="background:var(--amber-light);">
            <svg style="color:var(--amber);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
        <div>
            <div class="kpi-tile-val">{{ $stats['evals'] }}</div>
            <div class="kpi-tile-label">Notes saisies</div>
            <div class="kpi-tile-note" style="color:var(--amber);">
                <svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>
                {{ $recentEvals->count() }} récente(s)
            </div>
        </div>
    </div>

    {{-- Bulletins --}}
    <div class="kpi-tile">
        <div class="kpi-tile-icon" style="background:var(--success-light);">
            <svg style="color:var(--success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <div class="kpi-tile-val">{{ $stats['bulletins'] }}</div>
            <div class="kpi-tile-label">Bulletins disponibles</div>
            <div class="kpi-tile-note" style="color:var(--success);">
                <svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>
                {{ $stats['teachers'] }} enseignant(s)
            </div>
        </div>
    </div>
</div>

{{-- ── CHARTS ── --}}
<div class="sec-head fu fu4">
    <div class="sec-title">Analyse de mes performances</div>
</div>
<div class="chart-row fu fu4">
    {{-- Évolution --}}
    <div class="ch-card">
        <div class="ch-card-head">
            <div>
                <div class="ch-card-title">Évolution de ma moyenne</div>
                <div class="ch-card-sub">6 derniers mois</div>
            </div>
            <span class="tag tag-indigo">Tendance</span>
        </div>
        <div class="ch-card-body">
            <canvas id="evolutionChart" height="175"></canvas>
        </div>
    </div>

    {{-- Radar --}}
    <div class="ch-card">
        <div class="ch-card-head">
            <div>
                <div class="ch-card-title">Profil par matière</div>
                <div class="ch-card-sub">Note moyenne / 20</div>
            </div>
            <span class="tag tag-sky">Radar</span>
        </div>
        <div class="ch-card-body">
            <canvas id="radarChart" height="175"></canvas>
        </div>
    </div>
</div>

{{-- ── BOTTOM ── --}}
<div class="sec-head fu fu5">
    <div class="sec-title">Résultats détaillés</div>
</div>
<div class="bottom-row fu fu5">

    {{-- SUBJECT TABLE --}}
    <div class="sub-card">
        <div class="sub-card-head">
            <div class="sub-card-title">Résultats par matière</div>
            <a href="{{ route('student.notes') }}" class="cta-btn cta-outline" style="width:auto;padding:.35rem .875rem;font-size:.75rem;">
                Voir tout
                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if($moyennesParMatiere->isEmpty())
            <div style="text-align:center;padding:3.5rem 1rem;">
                <div style="font-size:2.5rem;opacity:.35;margin-bottom:.75rem;">📚</div>
                <div style="font-size:.875rem;color:var(--muted);">Aucune note disponible pour le moment</div>
            </div>
        @else
        <div style="overflow-x:auto;">
            <table class="sub-table">
                <thead><tr>
                    <th>Matière</th>
                    <th>Enseignant</th>
                    <th>Coeff.</th>
                    <th>Moy.</th>
                    <th>Progression</th>
                </tr></thead>
                <tbody>
                    @foreach($moyennesParMatiere as $m)
                    @php
                        $avg  = $m['avg'];
                        $pct  = $avg ? round($avg / 20 * 100) : 0;
                        $gc   = $avg >= 14 ? 'gc-A' : ($avg >= 10 ? 'gc-B' : ($avg >= 8 ? 'gc-C' : 'gc-D'));
                        $bar  = $avg >= 14 ? '#10b981' : ($avg >= 10 ? '#6366f1' : ($avg >= 8 ? '#f59e0b' : '#ef4444'));
                    @endphp
                    <tr>
                        <td>
                            <div class="sub-name">{{ $m['subject']->name }}</div>
                            <div style="font-size:.68rem;color:var(--muted);margin-top:.1rem;">{{ $m['count'] }} note(s)</div>
                        </td>
                        <td class="sub-teacher">
                            {{ $m['subject']->teacher?->prenom }} {{ $m['subject']->teacher?->nom }}
                        </td>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:.78rem;color:var(--muted);text-align:center;">
                            ×{{ $m['subject']->coefficient }}
                        </td>
                        <td>
                            @if($avg)
                                <span class="grade-circle {{ $gc }}">{{ $avg }}</span>
                            @else
                                <span style="color:var(--muted);font-size:.8rem;">—</span>
                            @endif
                        </td>
                        <td style="min-width:130px;">
                            <div class="prog-wrap">
                                <div class="prog-bar">
                                    <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $bar }};"></div>
                                </div>
                                <span class="prog-pct" style="color:{{ $bar }};">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- EVAL FEED --}}
    <div class="eval-feed">
        <div class="eval-feed-head">
            <div class="eval-feed-title">Dernières évaluations</div>
            <span class="tag tag-indigo">{{ $recentEvals->count() }} récentes</span>
        </div>

        @if($recentEvals->isEmpty())
            <div style="text-align:center;padding:3rem 1rem;">
                <div style="font-size:2rem;opacity:.35;margin-bottom:.625rem;">📋</div>
                <div style="font-size:.8rem;color:var(--muted);">Aucune évaluation</div>
            </div>
        @else
        @php
            $evalIcons = [
                'DS'            => ['bg' => '#fee2e2', 'emoji' => '📝'],
                'DM'            => ['bg' => '#dbeafe', 'emoji' => '📖'],
                'Interrogation' => ['bg' => '#fef3c7', 'emoji' => '✏️'],
                'Examen'        => ['bg' => '#f0fdf4', 'emoji' => '📋'],
            ];
        @endphp
        @foreach($recentEvals as $eval)
        @php
            $myGrade = $eval->grades->first();
            $ic = $evalIcons[$eval->type] ?? ['bg' => '#f1f5f9', 'emoji' => '📄'];
            if($myGrade) {
                $ratio = $eval->max_score > 0 ? $myGrade->score / $eval->max_score : 0;
                $gradeClass = $ratio >= .7 ? 'gc-A' : ($ratio >= .5 ? 'gc-B' : ($ratio >= .4 ? 'gc-C' : 'gc-D'));
            }
        @endphp
        <div class="eval-item">
            <div class="eval-icon" style="background:{{ $ic['bg'] }};">{{ $ic['emoji'] }}</div>
            <div class="eval-info">
                <div class="eval-title">{{ $eval->title }}</div>
                <div class="eval-meta">
                    <span style="font-weight:600;color:var(--ink-mid);">{{ $eval->subject->name ?? '' }}</span>
                    · {{ \Carbon\Carbon::parse($eval->date)->locale('fr')->diffForHumans() }}
                </div>
            </div>
            @if($myGrade)
                <span class="grade-circle {{ $gradeClass }}">{{ $myGrade->score }}</span>
            @else
                <span class="tag tag-gray" style="font-size:.62rem;">À venir</span>
            @endif
        </div>
        @endforeach
        @endif

        <div style="padding:.875rem 1.125rem;border-top:1px solid #f8fafc;">
            <a href="{{ route('student.notes') }}" class="cta-btn cta-primary">
                Toutes mes notes
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const gc = '#f1f5f9', tc = '#94a3b8';
const evo   = @json($evolutionData);
const radar = @json($radarData);

Chart.defaults.font.family = "'Sora', sans-serif";

/* ── Evolution line ── */
const evoCtx = document.getElementById('evolutionChart');
const hasEvo = evo.data.some(v => v !== null);
if (hasEvo) {
    const gradient = evoCtx.getContext('2d').createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0,   'rgba(99,102,241,.25)');
    gradient.addColorStop(1,   'rgba(99,102,241,.0)');
    new Chart(evoCtx, {
        type: 'line',
        data: {
            labels: evo.labels,
            datasets: [{
                label: 'Ma moyenne',
                data: evo.data,
                borderColor: '#6366f1',
                backgroundColor: gradient,
                fill: true,
                tension: .45,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2.5,
                borderWidth: 2.5,
                spanGaps: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a', titleColor: '#94a3b8',
                    bodyColor: '#fff', cornerRadius: 8, padding: 10,
                    callbacks: { label: ctx => ` Moyenne : ${ctx.parsed.y}/20` }
                }
            },
            scales: {
                y: { min: 0, max: 20, grid: { color: gc, drawBorder: false }, ticks: { color: tc, font: { size: 10 }, stepSize: 5 } },
                x: { grid: { display: false }, ticks: { color: tc, font: { size: 10 } } }
            }
        }
    });
} else {
    evoCtx.parentElement.innerHTML = '<div style="text-align:center;padding:3rem;color:#94a3b8;font-size:.8rem;">📊 Pas encore de données</div>';
}

/* ── Radar ── */
const rdCtx = document.getElementById('radarChart');
if (radar.labels.length) {
    new Chart(rdCtx, {
        type: 'radar',
        data: {
            labels: radar.labels,
            datasets: [{
                label: 'Mes moyennes',
                data: radar.data,
                backgroundColor: 'rgba(99,102,241,.12)',
                borderColor: '#6366f1',
                borderWidth: 2.5,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a', bodyColor: '#fff',
                    cornerRadius: 8, padding: 10
                }
            },
            scales: {
                r: {
                    min: 0, max: 20,
                    ticks: { stepSize: 5, font: { size: 9 }, color: tc, backdropColor: 'transparent' },
                    pointLabels: { font: { size: 10, weight: '600' }, color: '#334155' },
                    grid: { color: gc },
                    angleLines: { color: gc }
                }
            }
        }
    });
} else {
    rdCtx.parentElement.innerHTML = '<div style="text-align:center;padding:3rem;color:#94a3b8;font-size:.8rem;">🕸 Aucune donnée disponible</div>';
}
</script>
@endpush
@endsection