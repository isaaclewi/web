@extends('parent.master')
@section('title', 'Notes — '.$apprenant->prenom)
@section('page-title', 'Notes de '.$apprenant->prenom.' '.$apprenant->nom)
@section('page-sub', ($apprenant->classe?->name ?? 'Sans classe').' · Suivi scolaire en temps réel')

@section('content')
<style>
.notes-wrap { display:flex; flex-direction:column; gap:1.25rem; }
.notes-banner {
    background:linear-gradient(135deg,var(--ink) 0%,#1a3560 100%);
    border-radius:14px; padding:1.5rem 1.75rem;
    display:flex; align-items:center; justify-content:space-between; gap:1rem;
    position:relative; overflow:hidden;
}
.notes-banner::after {
    content:'';position:absolute;top:-30px;right:-30px;
    width:160px;height:160px;border-radius:50%;
    background:rgba(255,255,255,.04);
}
.nb-avatar {
    width:54px;height:54px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.1rem;font-weight:800;color:#fff;flex-shrink:0;
}
.nb-name  { font-size:1.1rem;font-weight:800;color:#fff;margin:0; }
.nb-meta  { font-size:.78rem;color:rgba(255,255,255,.5);margin-top:.2rem; }

/* KPI row */
.notes-kpi { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem; }
.nkpi { background:#fff;border:1px solid var(--border);border-radius:12px;padding:.9rem 1.1rem; }
.nkpi-val { font-size:1.6rem;font-weight:800;color:var(--ink);font-family:'JetBrains Mono',monospace;line-height:1; }
.nkpi-lbl { font-size:.68rem;color:var(--ink-40);margin-top:.2rem; }

/* Matieres grid */
.mat-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.875rem; }
.mat-card {
    background:#fff;border:1px solid var(--border);border-radius:12px;padding:1rem;
    cursor:pointer;transition:all .18s;
}
.mat-card:hover { border-color:#b0bcda;box-shadow:0 4px 12px rgba(0,0,0,.07); }
.mat-card.selected { border-color:var(--blue);background:var(--blue-l); }
.mat-name { font-size:.83rem;font-weight:700;color:var(--ink); }
.mat-sub  { font-size:.72rem;color:var(--ink-40);margin-top:.1rem; }
.mat-avg  { font-size:1.5rem;font-weight:800;font-family:'JetBrains Mono',monospace;margin-top:.5rem; }

/* Grades table */
.gt { width:100%;border-collapse:collapse; }
.gt th {
    background:#fafbfd;padding:.6rem 1rem;
    text-align:left;font-size:.68rem;font-weight:700;
    text-transform:uppercase;color:var(--ink-40);letter-spacing:.06em;
    border-bottom:1px solid var(--border);
}
.gt td {
    padding:.75rem 1rem;border-bottom:1px solid var(--ink-10);
    font-size:.83rem;color:var(--ink);vertical-align:middle;
}
.gt tr:last-child td { border-bottom:none; }
.gt tr:hover td { background:var(--bg); }

.score-big {
    width:44px;height:30px;border-radius:7px;
    display:inline-flex;align-items:center;justify-content:center;
    font-size:.8rem;font-weight:800;font-family:'JetBrains Mono',monospace;
}
.s-A { background:#d0f4ec;color:#0d6b52; }
.s-B { background:var(--blue-l);color:#1a4a9e; }
.s-C { background:var(--gold-l);color:#7a5200; }
.s-D { background:var(--red-l);color:#7a1a1e; }

/* Filters */
.filters-row { display:flex;gap:.625rem;align-items:center;flex-wrap:wrap; }
.f-select {
    border:1px solid var(--border);border-radius:8px;
    padding:.4rem .75rem;font-size:.78rem;outline:none;
    background:#fff;color:var(--ink);font-family:'Outfit',sans-serif;
}
.f-select:focus { border-color:var(--blue); }

@media(max-width:640px) {
    .notes-kpi { grid-template-columns:1fr 1fr; }
    .notes-banner { flex-direction:column; }
}
</style>

@php
    $init  = strtoupper(mb_substr($apprenant->prenom,0,1).mb_substr($apprenant->nom,0,1));
    $colors = ['#6366f1','#0891b2','#059669','#d97706','#dc2626'];
    $color  = $colors[$apprenant->id % count($colors)];
    $moy    = $moyenneGenerale;
    $maxScore = 20;
@endphp

<div class="notes-wrap">

    {{-- Banner --}}
    <div class="notes-banner">
        <div style="display:flex;align-items:center;gap:1rem;position:relative;z-index:1;">
            <div class="nb-avatar" style="background:{{ $color }};">{{ $init }}</div>
            <div>
                <p class="nb-name">{{ $apprenant->prenom }} {{ $apprenant->nom }}</p>
                <p class="nb-meta">{{ $apprenant->classe?->name ?? 'Sans classe' }}
                    @if($apprenant->niveau) · {{ $apprenant->niveau->name }} @endif
                    @if($apprenant->filiere) · {{ $apprenant->filiere->name }} @endif
                </p>
            </div>
        </div>
        @if($moy)
        <div style="position:relative;z-index:1;text-align:right;">
            <div style="font-size:2.5rem;font-weight:800;color:#fff;font-family:'JetBrains Mono',monospace;line-height:1;">{{ $moy }}</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.45);">Moyenne générale</div>
        </div>
        @endif
    </div>

    {{-- KPI --}}
    <div class="notes-kpi">
        <div class="nkpi" style="border-top:3px solid var(--teal);">
            <div class="nkpi-val">{{ $grades->total() }}</div>
            <div class="nkpi-lbl">Notes enregistrées</div>
        </div>
        <div class="nkpi" style="border-top:3px solid var(--blue);">
            <div class="nkpi-val">{{ $moyennesParMatiere->whereNotNull('avg')->count() }}</div>
            <div class="nkpi-lbl">Matières évaluées</div>
        </div>
        <div class="nkpi" style="border-top:3px solid var(--gold);">
            <div class="nkpi-val">{{ $moyennesParMatiere->whereNotNull('avg')->max('avg') ?? '—' }}</div>
            <div class="nkpi-lbl">Meilleure moyenne</div>
        </div>
        <div class="nkpi" style="border-top:3px solid var(--red);">
            <div class="nkpi-val">{{ $moyennesParMatiere->whereNotNull('avg')->min('avg') ?? '—' }}</div>
            <div class="nkpi-lbl">Moyenne la plus basse</div>
        </div>
    </div>

    {{-- Moyennes par matière --}}
    <div class="p-card">
        <div class="p-card-header">
            <h3>Moyennes par matière</h3>
            <span style="font-size:.75rem;color:var(--ink-40);">{{ $moyennesParMatiere->count() }} matière(s)</span>
        </div>
        <div class="p-card-body">
            <div class="mat-grid">
                @forelse($moyennesParMatiere as $m)
                @php
                    $avg = $m['avg'];
                    $cl  = $avg>=14?'var(--teal)':($avg>=10?'var(--blue)':($avg>=8?'var(--gold)':'var(--red)'));
                @endphp
                <a href="{{ route('parent.enfant.notes', [$apprenant->id, 'subject_id' => $m['subject']->id]) }}"
                   class="mat-card {{ $subjectFilter == $m['subject']->id ? 'selected' : '' }}"
                   style="text-decoration:none;">
                    <div class="mat-name">{{ $m['subject']->name }}</div>
                    <div class="mat-sub">
                        Coef. {{ $m['subject']->coefficient ?? 1 }}
                        · {{ $m['count'] }} note(s)
                    </div>
                    <div class="mat-avg" style="color:{{ $avg ? $cl : 'var(--ink-40)' }};">
                        {{ $avg ?? '—' }}
                    </div>
                    @if($avg)
                    <div style="background:var(--ink-10);height:5px;border-radius:99px;overflow:hidden;margin-top:.5rem;">
                        <div style="height:100%;border-radius:99px;width:{{ min(100,round($avg/$maxScore*100)) }}%;background:{{ $cl }};"></div>
                    </div>
                    @endif
                </a>
                @empty
                <div style="grid-column:1/-1;text-align:center;padding:2rem;color:var(--ink-40);">
                    Aucune matière dans cette classe.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Liste des notes --}}
    <div class="p-card">
        <div class="p-card-header">
            <h3>Toutes les notes</h3>
            <div class="filters-row">
                <form method="GET" style="display:flex;gap:.5rem;">
                    <select name="subject_id" class="f-select" onchange="this.form.submit()">
                        <option value="">Toutes matières</option>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ $subjectFilter==$s->id?'selected':'' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @if($subjectFilter)
                    <a href="{{ route('parent.enfant.notes', $apprenant->id) }}" class="p-btn p-btn-outline" style="font-size:.75rem;padding:.35rem .75rem;">✕</a>
                    @endif
                </form>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="gt">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Évaluation</th>
                        <th>Matière</th>
                        <th>Enseignant</th>
                        <th>Type</th>
                        <th>Note</th>
                        <th>/ Max</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $g)
                    @php
                        $sc = $g->score >= 14?'A':($g->score>=10?'B':($g->score>=8?'C':'D'));
                        $max = $g->evaluation?->max_score ?? 20;
                    @endphp
                    <tr>
                        <td style="color:var(--ink-40);font-size:.75rem;white-space:nowrap;">
                            {{ $g->created_at?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td style="font-weight:600;">{{ $g->evaluation?->title ?? '—' }}</td>
                        <td>{{ $g->evaluation?->subject?->name ?? '—' }}</td>
                        <td style="font-size:.78rem;color:var(--ink-70);">
                            {{ $g->evaluation?->subject?->teacher?->prenom ?? '' }}
                            {{ $g->evaluation?->subject?->teacher?->nom ?? '—' }}
                        </td>
                        <td>
                            @php $type = $g->evaluation?->type ?? ''; @endphp
                            <span class="p-badge {{ in_array($type,['examen','exam'])?'p-badge-red':($type==='controle'?'p-badge-blue':'p-badge-gray') }}">
                                {{ $type ?: 'Note' }}
                            </span>
                        </td>
                        <td>
                            <span class="score-big s-{{ $sc }}">{{ $g->score }}</span>
                        </td>
                        <td style="color:var(--ink-40);font-size:.78rem;">/ {{ $max }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2.5rem;color:var(--ink-40);">
                            Aucune note enregistrée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($grades->hasPages())
        <div style="padding:.875rem 1.25rem;border-top:1px solid var(--border);">
            {{ $grades->links() }}
        </div>
        @endif
    </div>

</div>
@endsection