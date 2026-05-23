@extends('student.master')
@section('title', 'Ma Classe')
@section('page-title', 'Classe & Matières')
@section('page-sub', $classe->name ?? 'Aucune classe assignée')

@section('content')
<style>
    
    /* ─────────────────────────────────────────────
   RESPONSIVE — PAGE CLASSE
───────────────────────────────────────────── */

/* Tablettes */
@media (max-width: 1024px) {

    .three-col {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .card {
        padding: .75rem;
    }
}

/* Mobile */
@media (max-width: 768px) {

    /* Bloc info classe */
    .card > div[style*="display:flex"] {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }

    /* Stats (moyenne, matières, évaluations) */
    .card > div div[style*="display:flex;gap:1.25rem"] {
        width: 100%;
        justify-content: space-between;
    }

    .card .mono {
        font-size: 1.2rem !important;
    }

    /* Grid matières */
    .three-col {
        grid-template-columns: 1fr !important;
        gap: .75rem;
        padding: .75rem !important;
    }

    /* Cards matières */
    .three-col .card {
        padding: .75rem !important;
    }

    .three-col .card div[style*="font-weight:700"] {
        font-size: .85rem !important;
    }

    /* Progress bar */
    .prog {
        height: 6px !important;
    }

    /* Table enseignants */
    .t-table {
        font-size: .75rem;
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    .t-table th,
    .t-table td {
        padding: .4rem;
    }

    /* Badges */
    .badge {
        font-size: .6rem;
        padding: .2rem .4rem;
    }
}

/* Petits mobiles */
@media (max-width: 480px) {

    /* Header classe */
    .card > div div[style*="font-size:1.5rem"] {
        font-size: 1.2rem !important;
    }

    /* Icône classe */
    .card > div div[style*="width:64px"] {
        width: 50px !important;
        height: 50px !important;
        font-size: 1.5rem !important;
    }

    /* Stats */
    .card .mono {
        font-size: 1rem !important;
    }

    /* Matières */
    .three-col .card {
        padding: .65rem !important;
    }

    .three-col .card img {
        width: 20px !important;
        height: 20px !important;
    }

    .badge {
        font-size: .55rem;
    }

    .mono {
        font-size: .65rem !important;
    }
}

/* Très petits écrans */
@media (max-width: 360px) {

    .card .mono {
        font-size: .9rem !important;
    }

    .t-table {
        font-size: .65rem;
    }

    .badge {
        font-size: .5rem;
    }
}</style>
<nav class="bc">
    <a href="{{ route('student.dashboard') }}">Tableau de bord</a>
    <span class="bc-sep">›</span><span class="bc-cur">Classe & Matières</span>
</nav>

@if(! $classe)
    <div class="card">
        <div class="empty"><div class="empty-icon">🏫</div><div class="empty-text">Vous n'êtes encore assigné à aucune classe.</div></div>
    </div>
@else

{{-- INFO CLASSE --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div style="display:flex;align-items:center;gap:1.5rem;padding:1.5rem;flex-wrap:wrap;">
        <div style="width:64px;height:64px;background:var(--primary-light);border-radius:1rem;display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;">🏫</div>
        <div style="flex:1;">
            <div style="font-size:1.5rem;font-weight:700;color:var(--ink);font-family:var(--font);">{{ $classe->name }}</div>
            <div style="font-size:.82rem;color:var(--muted);margin-top:.25rem;display:flex;gap:.75rem;flex-wrap:wrap;">
                @if($classe->niveau) <span class="badge b-indigo">{{ $classe->niveau->name }}</span>@endif
                @if($classe->filiere) <span class="badge b-purple">{{ $classe->filiere->name }}</span>@endif
                <span>{{ $classe->apprenants->count() }} élève(s)</span>
                <span>·</span>
                <span>{{ $subjects->count() }} matière(s)</span>
                <span>·</span>
                <span>{{ $teachers->count() }} enseignant(s)</span>
            </div>
        </div>
        <div style="display:flex;gap:1.25rem;text-align:center;">
            <div>
                <div class="mono" style="font-size:1.5rem;font-weight:800;color:var(--primary);">{{ $subjectStats->whereNotNull('avg')->avg('avg') ? round($subjectStats->whereNotNull('avg')->avg('avg'),1) : '—' }}</div>
                <div style="font-size:.7rem;color:var(--muted);">Ma moyenne</div>
            </div>
            <div>
                <div class="mono" style="font-size:1.5rem;font-weight:800;color:var(--success);">{{ $subjects->count() }}</div>
                <div style="font-size:.7rem;color:var(--muted);">Matières</div>
            </div>
            <div>
                <div class="mono" style="font-size:1.5rem;font-weight:800;color:var(--amber);">{{ $subjectStats->sum('evals') }}</div>
                <div style="font-size:.7rem;color:var(--muted);">Évaluations</div>
            </div>
        </div>
    </div>
</div>

{{-- MATIÈRES --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Mes matières</div>
        <span class="badge b-indigo">{{ $subjects->count() }}</span>
    </div>

    @if($subjects->isEmpty())
        <div class="empty"><div class="empty-icon">📚</div><div class="empty-text">Aucune matière dans cette classe</div></div>
    @else
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;padding:1.25rem;" class="three-col">
        @foreach($subjectStats as $stat)
            @php
                $avg  = $stat['avg'];
                $pct  = $avg ? round($avg/20*100) : 0;
                $pill = $avg>=14?'gp-A':($avg>=10?'gp-B':($avg>=8?'gp-C':'gp-D'));
                $bar  = $avg>=14?'var(--success)':($avg>=10?'var(--primary)':($avg>=8?'var(--amber)':'var(--danger)'));
                $icons = ['Mathématiques'=>'📐','Physique'=>'⚗️','Chimie'=>'🧪','Sciences'=>'🔬','Français'=>'📖','Histoire'=>'🌍','Géographie'=>'🗺','Anglais'=>'🇬🇧','Philosophie'=>'🤔','Économie'=>'📈','SVT'=>'🌱','Informatique'=>'💻'];
                $icon = collect($icons)->first(fn($v,$k)=>str_contains($stat['subject']->name,$k)) ?? '📚';
            @endphp
            <div class="card" style="border:1px solid var(--border);">
                <div style="padding:1rem;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.875rem;">
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <div style="width:40px;height:40px;background:#f8fafc;border-radius:.625rem;display:flex;align-items:center;justify-content:center;font-size:1.25rem;">{{ $icon }}</div>
                            <div>
                                <div style="font-weight:700;color:var(--ink);font-size:.875rem;">{{ $stat['subject']->name }}</div>
                                <div style="font-size:.7rem;color:var(--muted);">Coeff. {{ $stat['subject']->coefficient }}</div>
                            </div>
                        </div>
                        @if($avg) <span class="gp {{ $pill }}">{{ $avg }}</span>@endif
                    </div>

                    <div style="font-size:.75rem;color:var(--muted);margin-bottom:.375rem;display:flex;justify-content:space-between;">
                        <span>Progression</span>
                        <span>{{ $pct }}%</span>
                    </div>
                    <div class="prog" style="height:7px;margin-bottom:.875rem;">
                        <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $bar }};"></div>
                    </div>

                    <div style="display:flex;gap:.5rem;font-size:.72rem;color:var(--muted);">
                        <span>{{ $stat['evals'] }} éval(s)</span>
                        <span>·</span>
                        <span>{{ $stat['graded'] }} corrigée(s)</span>
                    </div>

                    @if($stat['subject']->teacher)
                    <div style="display:flex;align-items:center;gap:.5rem;margin-top:.75rem;padding-top:.75rem;border-top:1px solid #f1f5f9;">
                        <img src="https://i.pravatar.cc/24?u={{ $stat['subject']->teacher->id }}" style="width:22px;height:22px;border-radius:50%;object-fit:cover;">
                        <span style="font-size:.72rem;color:var(--muted);">{{ $stat['subject']->teacher->prenom }} {{ $stat['subject']->teacher->nom }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ENSEIGNANTS DE LA CLASSE --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Enseignants de ma classe</div>
        <span class="badge b-green">{{ $teachers->count() }}</span>
    </div>
    @if($teachers->isEmpty())
        <div class="empty"><div class="empty-icon">👨‍🏫</div><div class="empty-text">Aucun enseignant assigné</div></div>
    @else
    <table class="t-table">
        <thead><tr><th>Enseignant</th><th>Spécialité</th><th>Matières ici</th><th>Contact</th><th>Statut</th></tr></thead>
        <tbody>
            @foreach($teachers as $teacher)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <img src="https://i.pravatar.cc/36?u=t{{ $teacher->id }}" style="width:34px;height:34px;border-radius:.5rem;object-fit:cover;">
                            <div>
                                <div style="font-weight:600;color:var(--ink);">{{ $teacher->prenom }} {{ $teacher->nom }}</div>
                                <div style="font-size:.7rem;color:var(--muted);">{{ $teacher->matricule ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge b-purple">{{ $teacher->specialite ?? '—' }}</span></td>
                    <td>
                        @foreach($subjects->where('teacher_id', $teacher->id) as $sub)
                            <span class="badge b-indigo" style="margin:.1rem;">{{ $sub->name }}</span>
                        @endforeach
                    </td>
                    <td style="font-size:.78rem;color:var(--muted);">{{ $teacher->email ?? $teacher->telephone ?? '—' }}</td>
                    <td>
                        @if($teacher->status === 'actif')
                            <span class="badge b-green">Actif</span>
                        @else
                            <span class="badge b-gray">{{ ucfirst($teacher->status) }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endif

@push('scripts')
<style>
.three-col { grid-template-columns: repeat(3,1fr); }
@media(max-width:900px){ .three-col { grid-template-columns: repeat(2,1fr); } }
@media(max-width:600px){ .three-col { grid-template-columns: 1fr; } }
</style>
@endpush
@endsection