@extends('student.master')
@section('title', 'Mes Notes')
@section('page-title', 'Mes Notes & Évaluations')
@section('page-sub', 'Toutes les notes saisies par vos enseignants')

@section('content')

<style>
    .t-scroll-y {
    max-height: 280px; /* ajuste selon ton design */
    overflow-y: auto;
    overflow-x: hidden;
    border-radius: .5rem;
}

.t-scroll-y thead th {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 2;
}

.t-scroll-y::-webkit-scrollbar {
    width: 6px;
}

.t-scroll-y::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 10px;
}

/* ─────────────────────────────────────────────
   RESPONSIVE — PAGE NOTES
───────────────────────────────────────────── */

/* Tablettes */
@media (max-width: 1024px) {

    .kpi-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: .75rem;
    }

    .two-col {
        grid-template-columns: 1fr !important;
    }

    .t-scroll-y {
        max-height: 240px;
    }
}

/* Mobile */
@media (max-width: 768px) {

    /* KPI */
    .kpi-grid {
        grid-template-columns: 1fr !important;
    }

    .kpi {
        padding: .75rem !important;
    }

    .kpi-val {
        font-size: 1.25rem !important;
    }

    .kpi-lbl {
        font-size: .75rem;
    }

    /* Graphs */
    .two-col {
        grid-template-columns: 1fr !important;
        gap: .75rem;
    }

    canvas {
        height: 150px !important;
    }

    /* Filter bar */
    .filter-bar {
        flex-wrap: wrap;
        gap: .5rem;
    }

    .filter-bar .f-input {
        width: 100% !important;
    }

    .filter-bar button,
    .filter-bar a {
        width: 100%;
        text-align: center;
    }

    /* Table */
    .t-table {
        font-size: .75rem;
    }

    .t-table th,
    .t-table td {
        padding: .4rem;
        white-space: nowrap;
    }

    .t-scroll-y {
        max-height: 200px;
    }

    /* Badges & pills */
    .badge,
    .gp {
        font-size: .65rem;
        padding: .2rem .4rem;
    }

    /* Progress bar */
    .prog {
        width: 50px !important;
    }

    /* Pager */
    .pager {
        flex-direction: column;
        gap: .5rem;
        align-items: flex-start;
    }
}

/* Petits mobiles */
@media (max-width: 480px) {

    .kpi-val {
        font-size: 1.1rem !important;
    }

    .kpi-lbl {
        font-size: .7rem;
    }

    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: .25rem;
    }

    .card-title {
        font-size: .9rem;
    }

    .t-table {
        font-size: .7rem;
    }

    .prog {
        width: 40px !important;
    }

    .mono {
        font-size: .65rem !important;
    }

    .badge {
        font-size: .6rem;
    }
}

/* Très petits écrans */
@media (max-width: 360px) {

    .t-table {
        font-size: .65rem;
    }

    .kpi-val {
        font-size: 1rem !important;
    }

    .kpi-lbl {
        font-size: .65rem;
    }
}
</style>

<nav class="bc">
    <a href="{{ route('student.dashboard') }}">Tableau de bord</a>
    <span class="bc-sep">›</span><span class="bc-cur">Mes Notes</span>
</nav>

{{-- KPI RAPIDES --}}
<div class="kpi-grid" style="grid-template-columns:repeat(4,1fr);">
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-accent" style="background:linear-gradient(90deg,var(--primary),var(--primary-mid));"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $moyenneGenerale ?? '—' }}</div>
        <div class="kpi-lbl">Moyenne générale</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-accent" style="background:linear-gradient(90deg,var(--success),#34d399);"></div>
        <div class="kpi-val" style="font-size:1.5rem;color:var(--success);">
            {{ $grades->getCollection()->filter(fn($g)=>$g->score>=$g->evaluation->max_score*.7)->count() }}
        </div>
        <div class="kpi-lbl">Notes ≥ 70%</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-accent" style="background:linear-gradient(90deg,var(--danger),#f87171);"></div>
        <div class="kpi-val" style="font-size:1.5rem;color:var(--danger);">
            {{ $grades->getCollection()->filter(fn($g)=>$g->score<$g->evaluation->max_score*.5)->count() }}
        </div>
        <div class="kpi-lbl">Notes &lt; 50%</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-accent" style="background:linear-gradient(90deg,var(--amber),#fbbf24);"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $grades->total() }}</div>
        <div class="kpi-lbl">Total évaluations</div>
    </div>
</div>

{{-- GRAPHIQUES --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;" class="two-col">
    {{-- Moyennes par matière --}}
    <div class="card">
        <div class="card-header"><div class="card-title">Moyennes par matière</div></div>
        <div class="card-body"><canvas id="barMatiere" height="180"></canvas></div>
    </div>
    {{-- Distribution --}}
    <div class="card">
        <div class="card-header"><div class="card-title">Distribution des notes</div></div>
        <div class="card-body"><canvas id="distChart" height="180"></canvas></div>
    </div>
</div>

{{-- FILTRES --}}
<form method="GET" action="{{ route('student.notes') }}">
<div class="filter-bar">
    <span class="filter-label">Filtrer</span>
    <div class="filter-sep"></div>

    <div style="position:relative;">
        <input type="text" name="search" value="{{ request('search') }}" class="f-input sm" style="width:180px;padding-left:1.75rem;" placeholder="Rechercher…">
        <span style="position:absolute;left:.5rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.8rem;">🔍</span>
    </div>

    <select name="subject_id" class="f-input sm" onchange="this.form.submit()">
        <option value="">Toutes les matières</option>
        @foreach($subjects as $sub)
            <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
        @endforeach
    </select>

    <select name="type" class="f-input sm" onchange="this.form.submit()">
        <option value="">Tous types</option>
        @foreach(['DS','DM','Interrogation','Examen'] as $t)
            <option value="{{ $t }}" {{ request('type')===$t ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
    </select>

    <select name="sort" class="f-input sm" onchange="this.form.submit()">
        <option value="date_desc"  {{ request('sort','date_desc')==='date_desc'  ? 'selected':'' }}>Date ↓</option>
        <option value="date_asc"   {{ request('sort')==='date_asc'   ? 'selected':'' }}>Date ↑</option>
        <option value="score_desc" {{ request('sort')==='score_desc' ? 'selected':'' }}>Note ↓</option>
        <option value="score_asc"  {{ request('sort')==='score_asc'  ? 'selected':'' }}>Note ↑</option>
    </select>

    <div class="filter-spacer"></div>
    <button type="submit" class="btn btn-p btn-sm">Appliquer</button>
    @if(request()->hasAny(['subject_id','type','sort','search']))
        <a href="{{ route('student.notes') }}" class="btn btn-o btn-sm">✕</a>
    @endif
</div>
</form>

{{-- TABLE DES NOTES --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Détail de mes évaluations</div>
        <span style="font-size:.75rem;color:var(--muted);">{{ $grades->total() }} résultat(s)</span>
    </div>

    @if($grades->isEmpty())
        <div class="empty"><div class="empty-icon">📝</div><div class="empty-text">Aucune note pour ces filtres</div></div>
    @else
    <div style="overflow-x:auto;">
    <div class="t-scroll-y">
    <table class="t-table">
        <thead><tr>
            <th>Évaluation</th><th>Matière</th><th>Enseignant</th>
            <th>Date</th><th>Type</th><th>Ma note</th><th>/ Max</th><th>%</th><th>Résultat</th>
        </tr></thead>
        <tbody>
            @foreach($grades as $grade)
                @php
                    $eval  = $grade->evaluation;
                    $sub   = $eval->subject;
                    $pct   = $eval->max_score > 0 ? round($grade->score / $eval->max_score * 100) : 0;
                    $pill  = $pct >= 70 ? 'gp-A' : ($pct >= 50 ? 'gp-B' : ($pct >= 40 ? 'gp-C' : 'gp-D'));
                    $tBadge = ['DS'=>'b-red','DM'=>'b-sky','Interrogation'=>'b-amber','Examen'=>'b-purple'][$eval->type] ?? 'b-gray';
                @endphp
                <tr>
                    <td style="font-weight:600;color:var(--ink);">{{ $eval->title }}</td>
                    <td><span class="badge b-indigo">{{ $sub->name ?? '—' }}</span></td>
                    <td style="font-size:.78rem;color:var(--muted);">
                        {{ $sub->teacher?->prenom }} {{ $sub->teacher?->nom }}
                    </td>
                    <td class="mono" style="font-size:.75rem;">{{ \Carbon\Carbon::parse($eval->date)->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $tBadge }}">{{ $eval->type }}</span></td>
                    <td><span class="gp {{ $pill }}">{{ $grade->score }}</span></td>
                    <td class="mono" style="font-size:.75rem;color:var(--muted);">{{ $eval->max_score }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <div class="prog" style="width:60px;"><div class="prog-fill" style="width:{{ $pct }}%;background:{{ $pct>=70?'var(--success)':($pct>=50?'var(--primary)':($pct>=40?'var(--amber)':'var(--danger)')) }};"></div></div>
                            <span class="mono" style="font-size:.72rem;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td>
                        @if($pct >= 70) <span class="badge b-green">Bien</span>
                        @elseif($pct >= 50) <span class="badge b-indigo">Passable</span>
                        @elseif($pct >= 40) <span class="badge b-amber">À améliorer</span>
                        @else <span class="badge b-red">Insuffisant</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>

    <div class="pager">
        <span class="pager-info">{{ $grades->firstItem() }}–{{ $grades->lastItem() }} sur {{ $grades->total() }}</span>
        <div>{{ $grades->withQueryString()->links() }}</div>
    </div>
    @endif
</div>

{{-- RÉSUMÉ PAR MATIÈRE --}}
<div class="card" style="margin-top:1.25rem;">
    <div class="card-header"><div class="card-title">Résumé par matière</div></div>
    @if(collect($moyennesParMatiere)->isEmpty())
        <div class="empty"><div class="empty-icon">📚</div><div class="empty-text">Aucune donnée</div></div>
    @else
    <table class="t-table">
        <thead><tr><th>Matière</th><th>Coeff.</th><th>Nb évals</th><th>Moy.</th><th>Min</th><th>Max</th><th>Progression</th></tr></thead>
        <tbody>
            @foreach($moyennesParMatiere as $m)
                @php
                    $avg = $m['avg'];
                    $pct = $avg ? round($avg/20*100) : 0;
                    $p   = $avg>=14?'gp-A':($avg>=10?'gp-B':($avg>=8?'gp-C':'gp-D'));
                @endphp
                <tr>
                    <td style="font-weight:600;">{{ $m['subject']->name }}</td>
                    <td class="mono">×{{ $m['subject']->coefficient }}</td>
                    <td class="mono">{{ $m['count'] }}</td>
                    <td>@if($avg)<span class="gp {{ $p }}">{{ $avg }}</span>@else<span style="color:var(--muted);">—</span>@endif</td>
                    <td class="mono" style="color:var(--danger);font-size:.78rem;">{{ $m['min'] ?? '—' }}</td>
                    <td class="mono" style="color:var(--success);font-size:.78rem;">{{ $m['max'] ?? '—' }}</td>
                    <td style="width:140px;">
                        <div class="prog" style="height:8px;">
                            <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $pct>=70?'var(--success)':($pct>=50?'var(--primary)':($pct>=40?'var(--amber)':'var(--danger)')) }};"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const gc = '#e2e8f0', tc = '#94a3b8';
const matieres = @json(collect($moyennesParMatiere)->map(fn($m)=>['name'=>$m['subject']->name,'avg'=>$m['avg']])->values());
const dist     = @json($distributionData);

// Bar matières
const bmCtx = document.getElementById('barMatiere');
if (matieres.length) {
    new Chart(bmCtx, {
        type: 'bar',
        data: { labels: matieres.map(m=>m.name),
            datasets: [{ data: matieres.map(m=>m.avg??0),
                backgroundColor: matieres.map(m => m.avg>=14?'rgba(16,185,129,.7)':m.avg>=10?'rgba(99,102,241,.7)':m.avg>=8?'rgba(245,158,11,.7)':'rgba(239,68,68,.7)'),
                borderRadius: 6, borderSkipped: false }]},
        options: { responsive: true, plugins: { legend: { display: false } },
            scales: { y: { min: 0, max: 20, grid: { color: gc }, ticks: { color: tc, font: { size: 10 } } },
                      x: { grid: { display: false }, ticks: { color: tc, font: { size: 10 }, maxRotation: 30 } } } }
    });
} else {
    bmCtx.parentElement.innerHTML = '<div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Pas de données</div></div>';
}

// Distribution
const dCtx = document.getElementById('distChart');
const hasD = dist.data.some(v=>v>0);
if (hasD) {
    new Chart(dCtx, {
        type: 'bar',
        data: { labels: dist.labels, datasets: [{
            data: dist.data,
            backgroundColor: (c) => {
                const i = c.dataIndex;
                if(i<=1) return '#fee2e2'; if(i===2) return '#fef3c7';
                if(i>=5) return 'rgba(99,102,241,.65)'; return '#dbeafe';
            },
            borderRadius: 5, borderSkipped: false
        }]},
        options: { responsive: true, plugins: { legend: { display: false } },
            scales: { y: { grid: { color: gc }, ticks: { color: tc, font: { size: 10 } } },
                      x: { grid: { display: false }, ticks: { color: tc, font: { size: 10 } } } } }
    });
} else {
    dCtx.parentElement.innerHTML = '<div class="empty"><div class="empty-icon">📊</div><div class="empty-text">Pas encore de notes</div></div>';
}
</script>
@endpush
@endsection