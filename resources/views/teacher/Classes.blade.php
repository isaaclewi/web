@extends('teacher.master')

@section('content')
@include('teacher.partials.css')

{{-- ── BREADCRUMB ── --}}
<nav class="bc">
    <a href="{{ route('teacher.dashboard') }}">Tableau de bord</a>
    <span class="bc-sep">›</span>
    <span class="bc-cur">Mes classes</span>
</nav>

@if(session('success'))
    <div class="alert alert-s">✅ {{ session('success') }}</div>
@endif

{{-- ── PAGE HEADER ── --}}
<div class="ph" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <div class="ph-title">Mes classes</div>
        <div class="ph-sub">{{ $classes->count() }} classe(s) · {{ $apprenants->count() }} élève(s) au total</div>
    </div>
    <a href="{{ route('teacher.apprenants.index') }}" class="btn btn-o btn-sm">👥 Voir tous les élèves</a>
</div>

{{-- ── FILTER BAR ── --}}
<div class="filter-bar">
    <span class="filter-bar-title">Filtrer</span>
    <div class="filter-sep"></div>

    <form method="GET" action="{{ route('teacher.classes.index') }}" style="display:flex;gap:.75rem;flex-wrap:wrap;flex:1;">
        <select name="niveau_id" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
            <option value="">Tous les niveaux</option>
            @foreach($niveaux as $niv)
                <option value="{{ $niv->id }}" {{ request('niveau_id') == $niv->id ? 'selected' : '' }}>
                    {{ $niv->name }}
                </option>
            @endforeach
        </select>

        <select name="filiere_id" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
            <option value="">Toutes les filières</option>
            @foreach($filieres as $fil)
                <option value="{{ $fil->id }}" {{ request('filiere_id') == $fil->id ? 'selected' : '' }}>
                    {{ $fil->name }}
                </option>
            @endforeach
        </select>

        <div class="filter-spacer"></div>

        @if(request()->hasAny(['niveau_id','filiere_id']))
            <a href="{{ route('teacher.classes.index') }}" class="btn btn-o btn-sm">✕ Réinitialiser</a>
        @endif
    </form>
</div>

{{-- ── GRILLE DES CLASSES ── --}}
@if($classes->isEmpty())
    <div class="t-card"><div class="empty"><div class="empty-icon">🏫</div><div class="empty-text">Aucune classe assignée</div></div></div>
@else
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:1.5rem;" class="three-col">
    @foreach($classes as $classe)
        @php
            $classeEvals    = $evaluations->filter(fn($e) => $e->subject->class_id === $classe->id);
            $classeSubjects = $subjects->where('class_id', $classe->id);
            $avg = null;
            if($classeEvals->isNotEmpty()) {
                $scores = $classeEvals->flatMap(fn($e) => $e->grades->pluck('score'));
                $avg = $scores->isNotEmpty() ? round($scores->avg(), 1) : null;
            }
        @endphp
        <div class="t-card" style="border-top:3px solid var(--teal);">
            <div class="t-body">
                {{-- Header --}}
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.875rem;">
                    <div>
                        <div style="font-family:var(--font-disp);font-size:1.2rem;font-weight:700;color:var(--ink);">{{ $classe->name }}</div>
                        <div style="font-size:.75rem;color:var(--muted);margin-top:.1rem;">
                            {{ $classe->apprenants->count() }} élève(s)
                            @if($classe->niveau) · <span class="badge b-blue" style="font-size:.65rem;">{{ $classe->niveau->name }}</span>@endif
                            @if($classe->filiere) · <span class="badge b-purple" style="font-size:.65rem;">{{ $classe->filiere->name }}</span>@endif
                        </div>
                    </div>
                    <span class="badge b-green">Active</span>
                </div>

                {{-- Matières --}}
                @if($classeSubjects->isNotEmpty())
                    <div style="margin-bottom:.875rem;">
                        <div style="font-size:.68rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.35rem;">Matières</div>
                        <div style="display:flex;flex-wrap:wrap;gap:.35rem;">
                            @foreach($classeSubjects as $sub)
                                <span class="badge b-teal">{{ $sub->name }} ×{{ $sub->coefficient }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Stats --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1rem;">
                    <div style="background:#f8fafc;border-radius:.5rem;padding:.625rem .75rem;text-align:center;">
                        <div class="mono" style="font-size:1.1rem;font-weight:700;color:var(--teal);">{{ $avg ?? '—' }}</div>
                        <div style="font-size:.65rem;color:var(--muted);">Moy. classe</div>
                    </div>
                    <div style="background:#f8fafc;border-radius:.5rem;padding:.625rem .75rem;text-align:center;">
                        <div class="mono" style="font-size:1.1rem;font-weight:700;color:#6366f1;">{{ $classeEvals->count() }}</div>
                        <div style="font-size:.65rem;color:var(--muted);">Évaluations</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:.5rem;">
                    <a href="{{ route('teacher.apprenants.index', ['class_id' => $classe->id]) }}"
                       class="btn btn-p btn-sm" style="flex:1;justify-content:center;">
                        👥 Élèves
                    </a>
                    <a href="{{ route('teacher.notes.index', ['class_id' => $classe->id]) }}"
                       class="btn btn-o btn-sm" style="flex:1;justify-content:center;">
                        ✏️ Notes
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- ── TABLEAU RÉCAPITULATIF ── --}}
<div class="t-card">
    <div class="t-header">
        <div class="t-title">Récapitulatif des classes</div>
    </div>
    <div class="table-wrap">
    <table class="t-table">
        <thead>
            <tr>
                <th>Classe</th>
                <th>Niveau</th>
                <th>Filière</th>
                <th>Élèves</th>
                <th>Matières assignées</th>
                <th>Évaluations</th>
                <th>Moyenne</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classes as $classe)
                @php
                    $ce = $evaluations->filter(fn($e) => $e->subject->class_id === $classe->id);
                    $cs = $subjects->where('class_id', $classe->id);
                    $scores2 = $ce->flatMap(fn($e) => $e->grades->pluck('score'));
                    $avg2 = $scores2->isNotEmpty() ? round($scores2->avg(), 1) : null;
                @endphp
                <tr>
                    <td style="font-weight:600;color:var(--ink);">{{ $classe->name }}</td>
                    <td>{{ $classe->niveau->name ?? '—' }}</td>
                    <td>{{ $classe->filiere->name ?? '—' }}</td>
                    <td class="mono">{{ $classe->apprenants->count() }}</td>
                    <td>
                        @foreach($cs as $s)
                            <span class="badge b-teal" style="margin:.1rem;">{{ $s->name }}</span>
                        @endforeach
                        @if($cs->isEmpty())<span style="color:var(--muted);">—</span>@endif
                    </td>
                    <td class="mono">{{ $ce->count() }}</td>
                    <td>
                        @if($avg2)
                            @php $pill = $avg2>=14?'gp-A':($avg2>=10?'gp-B':($avg2>=8?'gp-C':'gp-D')); @endphp
                            <span class="gp {{ $pill }}">{{ $avg2 }}</span>
                        @else
                            <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.35rem;">
                            <a href="{{ route('teacher.apprenants.index', ['class_id' => $classe->id]) }}" class="btn btn-o btn-xs">Élèves</a>
                            <a href="{{ route('teacher.evaluations.index', ['class_id' => $classe->id]) }}" class="btn btn-o btn-xs">Évals</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty"><div class="empty-icon">🏫</div><div class="empty-text">Aucune classe</div></div></td></tr>
            @endforelse
        </tbody>
        </table>
</div>
</div>
@endsection