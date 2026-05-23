@extends('teacher.master')

@section('content')
@include('teacher.partials.css')

{{-- ── BREADCRUMB ── --}}
<nav class="bc">
    <a href="{{ route('teacher.dashboard') }}">Tableau de bord</a>
    <span class="bc-sep">›</span>
    <a href="{{ route('teacher.evaluations.index') }}">Évaluations</a>
    <span class="bc-sep">›</span>
    <span class="bc-cur">Saisie des notes</span>
</nav>

@if(session('success'))
    <div class="alert alert-s">✅ {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-e">⚠️ <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

{{-- ── PAGE HEADER ── --}}
<div class="ph">
    <div class="ph-title">Saisie des notes</div>
    <div class="ph-sub">Sélectionnez une évaluation pour saisir ou modifier les notes</div>
</div>

{{-- ── FILTER / SÉLECTEUR D'ÉVALUATION ── --}}
<form method="GET" action="{{ route('teacher.notes.index') }}" id="filterForm">
<div class="filter-bar">
    <span class="filter-bar-title">Choisir l'évaluation</span>
    <div class="filter-sep"></div>

    {{-- Filtre par classe --}}
    <select name="class_id" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Toutes les classes</option>
        @foreach($classes as $cl)
            <option value="{{ $cl->id }}" {{ request('class_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
        @endforeach
    </select>

    {{-- Filtre par matière --}}
    <select name="subject_id" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Toutes les matières</option>
        @foreach($subjects as $sub)
            <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
        @endforeach
    </select>

    {{-- Filtre par type --}}
    <select name="type" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Tous types</option>
        @foreach(['controle','examen','tp','projet'] as $t)
            <option value="{{ $t }}" {{ request('type')===$t ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
    </select>

    {{-- Sélecteur évaluation spécifique --}}
    <select name="evaluation_id" class="f-input f-input inline" style="width:260px;" onchange="this.form.submit()">
        <option value="">-- Sélectionner une évaluation --</option>
        @foreach($filteredEvaluations as $eval)
            <option value="{{ $eval->id }}" {{ $selectedEval?->id == $eval->id ? 'selected' : '' }}>
                {{ $eval->title }} · {{ $eval->subject->classe->name ?? '' }} ({{ $eval->type }})
            </option>
        @endforeach
    </select>

    <div class="filter-spacer"></div>
    @if(request()->hasAny(['class_id','subject_id','type','evaluation_id']))
        <a href="{{ route('teacher.notes.index') }}" class="btn btn-o btn-sm">✕ Réinitialiser</a>
    @endif
</div>
</form>

@if(!$selectedEval)
{{-- ── VUE PAR DÉFAUT : liste des évaluations à traiter ── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;" class="three-col">
    @foreach($filteredEvaluations->take(6) as $eval)
        @php
            $total  = $eval->subject->classe?->apprenants->count() ?? 0;
            $graded = $eval->grades->count();
            $done   = $total > 0 && $graded >= $total;
            $pct    = $total > 0 ? round($graded / $total * 100) : 0;
        @endphp
        <div class="t-card" style="border-top:3px solid {{ $done ? '#10b981' : ($graded > 0 ? '#f59e0b' : '#ef4444') }};">
            <div class="t-body">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.75rem;">
                    <div>
                        <div style="font-weight:700;color:var(--ink);font-size:.9rem;">{{ $eval->title }}</div>
                        <div style="font-size:.72rem;color:var(--muted);margin-top:.1rem;">
                            {{ $eval->subject->name ?? '' }} · {{ $eval->subject->classe->name ?? '' }}
                        </div>
                    </div>
                    @php $typeBadges=['DS'=>'b-red','DM'=>'b-blue','Interrogation'=>'b-amber','Examen'=>'b-purple']; @endphp
                    <span class="badge {{ $typeBadges[$eval->type] ?? 'b-gray' }}">{{ $eval->type }}</span>
                </div>
                <div style="margin-bottom:.875rem;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;">
                        <span style="font-size:.72rem;color:var(--muted);">Progression</span>
                        <span class="mono" style="font-size:.72rem;font-weight:700;">{{ $graded }}/{{ $total }}</span>
                    </div>
                    <div class="prog">
                        <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $done ? '#10b981' : ($graded > 0 ? '#f59e0b' : '#ef4444') }};"></div>
                    </div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:.72rem;color:var(--muted);">{{ \Carbon\Carbon::parse($eval->date)->format('d M Y') }}</span>
                    <a href="{{ route('teacher.notes.index', ['evaluation_id' => $eval->id]) }}"
                       class="btn {{ $done ? 'btn-o' : 'btn-p' }} btn-sm">
                        {{ $done ? '👁 Voir' : '✏️ Saisir' }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

@else
{{-- ── SAISIE POUR UNE ÉVALUATION SÉLECTIONNÉE ── --}}
@php
    $totalCopies = $evalApprenants->count();
    $doneCount   = 0;
    $existingGrades = $selectedEval->grades->keyBy('apprenant_id');
    foreach($evalApprenants as $ap) { if($existingGrades->has($ap->id)) $doneCount++; }
    $pct = $totalCopies > 0 ? round($doneCount / $totalCopies * 100) : 0;
@endphp

{{-- Info évaluation --}}
<div class="t-card" style="margin-bottom:1.25rem;">
    <div class="t-body" style="display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
        <div>
            <div style="font-family:var(--font-disp);font-size:1.3rem;font-weight:700;color:var(--ink);">{{ $selectedEval->title }}</div>
            <div style="font-size:.8rem;color:var(--muted);margin-top:.2rem;">
                {{ $selectedEval->subject->name ?? '' }} · {{ $selectedEval->subject->classe->name ?? '' }} ·
                {{ \Carbon\Carbon::parse($selectedEval->date)->format('d M Y') }}
            </div>
        </div>
        <div style="display:flex;gap:1.5rem;margin-left:auto;flex-wrap:wrap;">
            <div style="text-align:center;">
                <div class="mono" style="font-size:1.5rem;font-weight:800;color:var(--teal);">{{ $selectedEval->max_score }}</div>
                <div style="font-size:.7rem;color:var(--muted);">Note max</div>
            </div>
            <div style="text-align:center;">
                <div class="mono" style="font-size:1.5rem;font-weight:800;color:#6366f1;">{{ $doneCount }}/{{ $totalCopies }}</div>
                <div style="font-size:.7rem;color:var(--muted);">Saisies</div>
            </div>
            <div style="text-align:center;">
                <div class="mono" style="font-size:1.5rem;font-weight:800;color:{{ $doneCount===$totalCopies && $totalCopies>0 ? '#10b981' : '#f59e0b' }};">{{ $pct }}%</div>
                <div style="font-size:.7rem;color:var(--muted);">Complet</div>
            </div>
        </div>
        <div style="min-width:200px;">
            <div class="prog" style="height:8px;">
                <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $pct===100 ? '#10b981' : ($pct>0 ? '#f59e0b' : '#ef4444') }};transition:width .4s;"></div>
            </div>
        </div>
    </div>
</div>

{{-- FILTRE élèves dans la saisie --}}
<div class="filter-bar" style="margin-bottom:1rem;">
    <span class="filter-bar-title">Affichage</span>
    <div class="filter-sep"></div>
    <div style="position:relative;">
        <input type="text" id="gradeSearch" oninput="filterGradeRows()" class="f-input" style="width:200px;padding-left:2rem;" placeholder="Rechercher élève…">
        <span style="position:absolute;left:.6rem;top:50%;transform:translateY(-50%);color:var(--muted);">🔍</span>
    </div>
    <select id="gradeStatusFilter" onchange="filterGradeRows()" class="f-input f-input inline" style="width:auto;">
        <option value="">Tous les élèves</option>
        <option value="saisie">Notes saisies</option>
        <option value="vide">Notes manquantes</option>
    </select>
    <div class="filter-spacer"></div>
    <span id="gradeCounter" style="font-size:.78rem;color:var(--muted);">{{ $totalCopies }} élève(s)</span>
</div>

{{-- FORMULAIRE SAISIE --}}
<form action="{{ route('teacher.grades.store') }}" method="POST" id="gradeForm">
    @csrf
    <input type="hidden" name="evaluation_id" value="{{ $selectedEval->id }}">

    <div class="t-card">
        <div class="t-header">
            <div class="t-title">Saisie des notes — {{ $selectedEval->title }}</div>
            <div style="display:flex;gap:.5rem;">
                <button type="button" class="btn btn-o btn-sm" onclick="fillAll(0)">Mettre 0</button>
                <button type="button" class="btn btn-o btn-sm" onclick="clearAll()">Effacer tout</button>
            </div>
        </div>

        @if($evalApprenants->isEmpty())
            <div class="empty">
                <div class="empty-icon">👥</div>
                <div class="empty-text">Aucun élève dans cette classe</div>
            </div>
        @else
        <div style="overflow-x:auto;">
        <table class="t-table" id="gradeTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Élève</th>
                    <th>Matricule</th>
                    <th>Note /{{ $selectedEval->max_score }}</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evalApprenants as $i => $ap)
                    @php $existing = $existingGrades->get($ap->id); @endphp
                    <tr data-name="{{ strtolower($ap->nom.' '.$ap->prenom) }}" data-has="{{ $existing ? 'saisie' : 'vide' }}">
                        <td class="mono" style="color:var(--muted);font-size:.78rem;">{{ $i + 1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:.625rem;">
                                <img src="https://i.pravatar.cc/34?u={{ $ap->id }}" class="av av-sm">
                                <div>
                                    <div style="font-weight:600;color:var(--ink);">{{ $ap->nom }} {{ $ap->prenom }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="mono" style="font-size:.75rem;color:var(--muted);">{{ $ap->matricule }}</td>
                        <td>
                            <input type="hidden" name="grades[{{ $i }}][apprenant_id]" value="{{ $ap->id }}">
                            <input type="number"
                                name="grades[{{ $i }}][score]"
                                class="f-input grade-input"
                                style="width:90px;padding:.4rem .6rem;text-align:center;font-family:var(--font-mono);font-weight:700;"
                                placeholder="—"
                                min="0" max="{{ $selectedEval->max_score }}" step="0.5"
                                value="{{ $existing?->score ?? old("grades.{$i}.score") }}"
                                oninput="updateStatus(this, {{ $selectedEval->max_score }})">
                        </td>
                        <td>
                            @if($existing)
                                @php
                                    $s = $existing->score;
                                    $m = $selectedEval->max_score;
                                    $p = $s / $m * 100;
                                    $pill = $p >= 70 ? 'gp-A' : ($p >= 50 ? 'gp-B' : ($p >= 40 ? 'gp-C' : 'gp-D'));
                                @endphp
                                <span class="gp {{ $pill }}" id="pill-{{ $ap->id }}">{{ $s }}</span>
                            @else
                                <span class="badge b-gray" id="pill-{{ $ap->id }}" style="display:none;"></span>
                                <span style="font-size:.75rem;color:var(--muted);" id="empty-{{ $ap->id }}">Non saisi</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <div id="liveStats" style="font-size:.8rem;color:var(--muted);">
                <span id="countFilled">0</span> note(s) saisies · Moy. live : <span id="liveMoy" class="mono" style="font-weight:700;color:var(--teal);">—</span>
            </div>
            <div style="display:flex;gap:.5rem;">
                <a href="{{ route('teacher.evaluations.index') }}" class="btn btn-o btn-sm">← Retour</a>
                <button type="submit" class="btn btn-p">💾 Enregistrer les notes</button>
            </div>
        </div>
        @endif
    </div>
</form>

{{-- HISTORIQUE DES NOTES POUR CETTE ÉVAL --}}
@if($existingGrades->isNotEmpty())
<div class="t-card" style="margin-top:1.25rem;">
    <div class="t-header">
        <div class="t-title">Synthèse — {{ $selectedEval->title }}</div>
        <a href="{{ route('teacher.evaluations.export', $selectedEval->id) }}" class="btn btn-o btn-sm">↓ Export CSV</a>
    </div>
    <div class="t-body" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;" >
        @php
            $allScores = $existingGrades->pluck('score');
            $evalAvg   = round($allScores->avg(), 2);
            $evalMin   = $allScores->min();
            $evalMax   = $allScores->max();
            $above10   = $allScores->filter(fn($s) => $s >= $selectedEval->max_score * 0.5)->count();
        @endphp
        <div style="background:#f8fafc;border-radius:.625rem;padding:1rem;text-align:center;">
            <div class="mono" style="font-size:1.5rem;font-weight:800;color:var(--teal);">{{ $evalAvg }}</div>
            <div style="font-size:.7rem;color:var(--muted);">Moyenne</div>
        </div>
        <div style="background:#f8fafc;border-radius:.625rem;padding:1rem;text-align:center;">
            <div class="mono" style="font-size:1.5rem;font-weight:800;color:#ef4444;">{{ $evalMin }}</div>
            <div style="font-size:.7rem;color:var(--muted);">Note min</div>
        </div>
        <div style="background:#f8fafc;border-radius:.625rem;padding:1rem;text-align:center;">
            <div class="mono" style="font-size:1.5rem;font-weight:800;color:#10b981;">{{ $evalMax }}</div>
            <div style="font-size:.7rem;color:var(--muted);">Note max</div>
        </div>
        <div style="background:#f8fafc;border-radius:.625rem;padding:1rem;text-align:center;">
            <div class="mono" style="font-size:1.5rem;font-weight:800;color:#6366f1;">{{ $above10 }}/{{ $existingGrades->count() }}</div>
            <div style="font-size:.7rem;color:var(--muted);">≥ moyenne</div>
        </div>
    </div>
</div>
@endif

@endif {{-- end selectedEval --}}

<script>
/* ── Filtre élèves dans la grille de saisie ── */
function filterGradeRows() {
    const search = document.getElementById('gradeSearch')?.value.toLowerCase() ?? '';
    const status = document.getElementById('gradeStatusFilter')?.value ?? '';
    let count = 0;
    document.querySelectorAll('#gradeTable tbody tr').forEach(row => {
        const name    = row.dataset.name ?? '';
        const hasNote = row.dataset.has ?? '';
        const matchS  = name.includes(search);
        const matchN  = !status || hasNote === status;
        const show    = matchS && matchN;
        row.style.display = show ? '' : 'none';
        if(show) count++;
    });
    const el = document.getElementById('gradeCounter');
    if(el) el.textContent = count + ' élève(s)';
}

/* ── Mise à jour du statut pill en live ── */
function updateStatus(input, max) {
    const val   = parseFloat(input.value);
    const row   = input.closest('tr');
    const apId  = input.closest('tr').querySelector('input[name*="apprenant_id"]')?.value;

    if (!isNaN(val) && val >= 0) {
        row.dataset.has = 'saisie';
    } else {
        row.dataset.has = 'vide';
    }
    updateLiveStats(max);
}

function updateLiveStats(max) {
    const inputs = document.querySelectorAll('.grade-input');
    let filled = 0, total = 0;
    inputs.forEach(inp => {
        const v = parseFloat(inp.value);
        if(!isNaN(v) && inp.value !== '') { filled++; total += v; }
    });
    const cEl = document.getElementById('countFilled');
    const mEl = document.getElementById('liveMoy');
    if(cEl) cEl.textContent = filled;
    if(mEl) mEl.textContent = filled > 0 ? (total / filled).toFixed(1) : '—';
}

function fillAll(val) {
    document.querySelectorAll('.grade-input:not([style*="display:none"])').forEach(i => {
        i.value = val;
    });
    const maxScore = {{ $selectedEval?->max_score ?? 20 }};
    updateLiveStats(maxScore);
}

function clearAll() {
    document.querySelectorAll('.grade-input').forEach(i => i.value = '');
    updateLiveStats({{ $selectedEval?->max_score ?? 20 }});
}

// Init stats
document.addEventListener('DOMContentLoaded', () => {
    updateLiveStats({{ $selectedEval?->max_score ?? 20 }});
});
</script>
@endsection