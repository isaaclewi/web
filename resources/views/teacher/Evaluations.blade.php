@extends('teacher.master')

@section('content')
@include('teacher.partials.css')

{{-- ── BREADCRUMB ── --}}
<nav class="bc">
    <a href="{{ route('teacher.dashboard') }}">Tableau de bord</a>
    <span class="bc-sep">›</span>
    <span class="bc-cur">Évaluations</span>
</nav>

@if(session('success'))
    <div class="alert alert-s">✅ {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-e">⚠️ <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

{{-- ── PAGE HEADER ── --}}
<div class="ph" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <div class="ph-title">Évaluations</div>
        <div class="ph-sub">{{ $evaluations->total() }} évaluation(s) créée(s)</div>
    </div>
    <button class="btn btn-p btn-sm" onclick="document.getElementById('createModal').classList.add('open')">
        + Nouvelle évaluation
    </button>
</div>

{{-- ── KPI ── --}}
<div class="kpi-grid" style="grid-template-columns:repeat(4,1fr);">
    @php
        $typeCount = fn($t) => $evaluations->getCollection()->where('type',$t)->count();
        $doneCount = $evaluations->getCollection()->filter(fn($e) => $e->grades->count() >= ($e->subject->classe?->apprenants->count() ?? 1) && ($e->subject->classe?->apprenants->count() ?? 0) > 0)->count();
    @endphp
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-bar" style="background:linear-gradient(90deg,#ef4444,#f87171);"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $evaluations->getCollection()->where('type','DS')->count() }}</div>
        <div class="kpi-lbl">Devoirs surveillés</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-bar" style="background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $evaluations->getCollection()->where('type','DM')->count() }}</div>
        <div class="kpi-lbl">Devoirs maison</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-bar" style="background:linear-gradient(90deg,var(--amber),#fbbf24);"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $evaluations->getCollection()->where('type','Interrogation')->count() }}</div>
        <div class="kpi-lbl">Interrogations</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-bar" style="background:linear-gradient(90deg,#10b981,#34d399);"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $doneCount }}</div>
        <div class="kpi-lbl">Corrections terminées</div>
    </div>
</div>

{{-- ── FILTER BAR ── --}}
<form method="GET" action="{{ route('teacher.evaluations.index') }}">
<div class="filter-bar">
    <span class="filter-bar-title">Filtrer</span>
    <div class="filter-sep"></div>

    <div style="position:relative;">
        <input type="text" name="search" value="{{ request('search') }}" class="f-input" style="width:200px;padding-left:2rem;" placeholder="Titre…">
        <span style="position:absolute;left:.6rem;top:50%;transform:translateY(-50%);color:var(--muted);">🔍</span>
    </div>

    <select name="class_id" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Toutes les classes</option>
        @foreach($classes as $cl)
            <option value="{{ $cl->id }}" {{ request('class_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
        @endforeach
    </select>

    <select name="subject_id" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Toutes les matières</option>
        @foreach($subjects as $sub)
            <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
        @endforeach
    </select>

    <select name="type" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Tous les types</option>
        @foreach(['DS','DM','Interrogation','Examen'] as $t)
            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
    </select>

    <select name="status" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        <option value="done"    {{ request('status')==='done'    ? 'selected' : '' }}>Terminé</option>
        <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>En attente</option>
    </select>

    <select name="sort" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="date_desc"  {{ request('sort','date_desc')==='date_desc' ? 'selected' : '' }}>Date ↓</option>
        <option value="date_asc"   {{ request('sort')==='date_asc'  ? 'selected' : '' }}>Date ↑</option>
        <option value="title"      {{ request('sort')==='title'     ? 'selected' : '' }}>Titre A→Z</option>
    </select>

    <div class="filter-spacer"></div>
    <button type="submit" class="btn btn-p btn-sm">Appliquer</button>
    @if(request()->hasAny(['search','class_id','subject_id','type','status','sort']))
        <a href="{{ route('teacher.evaluations.index') }}" class="btn btn-o btn-sm">✕</a>
    @endif
</div>
</form>

{{-- ── TABLE ── --}}
<div class="t-card">
    <div class="t-header">
        <div class="t-title">Liste des évaluations</div>
        <span style="font-size:.78rem;color:var(--muted);">{{ $evaluations->total() }} résultat(s)</span>
    </div>

    @if($evaluations->isEmpty())
        <div class="empty">
            <div class="empty-icon">📋</div>
            <div class="empty-text">Aucune évaluation trouvée</div>
        </div>
    @else
    <div style="overflow-x:auto;">
    <table class="t-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Matière</th>
                <th>Classe</th>
                <th>Date</th>
                <th>Type</th>
                <th>Max</th>
                <th>Moy. classe</th>
                <th>Min / Max</th>
                <th>Copies</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evaluations as $eval)
                @php
                    $total  = $eval->subject->classe?->apprenants->count() ?? 0;
                    $graded = $eval->grades->count();
                    $done   = $total > 0 && $graded >= $total;
                    $avg    = $eval->grades->isNotEmpty() ? round($eval->grades->avg('score'), 1) : null;
                    $min    = $eval->grades->isNotEmpty() ? $eval->grades->min('score') : null;
                    $max    = $eval->grades->isNotEmpty() ? $eval->grades->max('score') : null;
                    $typeBadges = ['DS'=>'b-red','DM'=>'b-blue','Interrogation'=>'b-amber','Examen'=>'b-purple'];
                    $tb = $typeBadges[$eval->type] ?? 'b-gray';
                    $pill = $avg ? ($avg >= 14 ? 'gp-A' : ($avg >= 10 ? 'gp-B' : ($avg >= 8 ? 'gp-C' : 'gp-D'))) : '';
                @endphp
                <tr>
                    <td style="font-weight:600;color:var(--ink);max-width:200px;">
                        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $eval->title }}</div>
                    </td>
                    <td><span class="badge b-teal" style="font-size:.68rem;">{{ $eval->subject->name ?? '—' }}</span></td>
                    <td><span class="badge b-blue" style="font-size:.68rem;">{{ $eval->subject->classe->name ?? '—' }}</span></td>
                    <td class="mono" style="font-size:.78rem;white-space:nowrap;">{{ \Carbon\Carbon::parse($eval->date)->format('d M Y') }}</td>
                    <td><span class="badge {{ $tb }}">{{ $eval->type }}</span></td>
                    <td class="mono">{{ $eval->max_score }}</td>
                    <td>
                        @if($avg) <span class="gp {{ $pill }}">{{ $avg }}</span>
                        @else <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($min !== null)
                            <span class="mono" style="color:#ef4444;font-size:.78rem;">{{ $min }}</span>
                            <span style="color:var(--muted);">/</span>
                            <span class="mono" style="color:#10b981;font-size:.78rem;">{{ $max }}</span>
                        @else
                            <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="mono" style="color:{{ $done?'#10b981':($graded>0?'#f59e0b':'#ef4444') }};font-weight:700;">{{ $graded }}</span>
                        <span class="mono" style="color:var(--muted);">/{{ $total }}</span>
                    </td>
                    <td>
                        @if($done) <span class="badge b-green">Terminé</span>
                        @elseif($graded > 0) <span class="badge b-amber">En cours</span>
                        @else <span class="badge b-red">À saisir</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.3rem;align-items:center;">
                            <a href="{{ route('teacher.notes.index', ['evaluation_id' => $eval->id]) }}" class="btn btn-p btn-xs">
                                {{ $done ? 'Voir' : 'Saisir' }}
                            </a>
                            <a href="{{ route('teacher.evaluations.export', $eval->id) }}" class="btn btn-o btn-xs" title="Export CSV">↓</a>
                            <form action="{{ route('teacher.evaluations.destroy', $eval->id) }}" method="POST"
                                onsubmit="return confirm('Supprimer « {{ addslashes($eval->title) }} » et toutes ses notes ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-d btn-xs" title="Supprimer">✕</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div class="pager">
        <span class="pager-info">{{ $evaluations->firstItem() }}–{{ $evaluations->lastItem() }} sur {{ $evaluations->total() }}</span>
        <div>{{ $evaluations->withQueryString()->links() }}</div>
    </div>
    @endif
</div>

{{-- ── MODAL CRÉER ÉVALUATION ── --}}
<div class="modal-bg" id="createModal">
    <div class="modal">
        <button class="modal-close" onclick="document.getElementById('createModal').classList.remove('open')">✕</button>
        <div class="modal-title">Nouvelle évaluation</div>
        <form action="{{ route('teacher.evaluations.store') }}" method="POST">
            @csrf
            <div class="f-group">
                <label class="f-label">Matière *</label>
                <select class="f-input" name="subject_id" required>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }} — {{ $sub->classe->name ?? '?' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="f-grid-2">
                <div class="f-group">
                    <label class="f-label">Type *</label>
                    <select class="f-input" name="type" required>
                        <option value="controle">Contrôle</option>
                        <option value="examen">Examen</option>
                        <option value="tp">Travaux pratiques</option>
                        <option value="projet">Projet</option>
                    </select>
                </div>
                <div class="f-group">
                    <label class="f-label">Date *</label>
                    <input type="date" class="f-input" name="date" required value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>
            <div class="f-group">
                <label class="f-label">Intitulé *</label>
                <input type="text" class="f-input" name="title" required placeholder="Ex : DS3 — Intégrales et primitives" value="{{ old('title') }}">
            </div>
            <div class="f-group">
                <label class="f-label">Note maximale *</label>
                <input type="number" class="f-input" name="max_score" value="{{ old('max_score',20) }}" min="1" max="100" required>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end;margin-top:1.25rem;">
                <button type="button" class="btn btn-o" onclick="document.getElementById('createModal').classList.remove('open')">Annuler</button>
                <button type="submit" class="btn btn-p">Créer l'évaluation</button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-open modal si erreurs de validation
@if($errors->any())
document.getElementById('createModal').classList.add('open');
@endif
</script>
@endsection