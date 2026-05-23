@extends('admin.master')
@section('title', 'Gestion des tâches du staff')

@section('content')
<div style="max-width:1200px;margin:0 auto">

{{-- EN-TÊTE --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
        <h1 style="font-size:1.3rem;font-weight:600;color:#111827">Gestion des tâches du staff</h1>
        <p style="font-size:.85rem;color:#6b7280;margin-top:.2rem">
            Attribuez des modules à chaque membre du personnel selon son rôle.
        </p>
    </div>
    <a href="{{ route('admin.staff') }}"
       style="background:#f3f4f6;color:#374151;padding:.5rem 1.1rem;border-radius:8px;font-size:.85rem;text-decoration:none;border:1px solid #e5e7eb">
        ← Retour staff
    </a>
</div>

@if(session('success'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">{{ session('error') }}</div>
@endif

{{-- KPI --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem">
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:1rem">
        <p style="font-size:1.5rem;font-weight:800;color:#111827">{{ $stats['total_staff'] }}</p>
        <p style="font-size:.75rem;color:#9ca3af;margin-top:.2rem">Membres du staff</p>
    </div>
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:1rem">
        <p style="font-size:1.5rem;font-weight:800;color:#059669">{{ $stats['avec_taches'] }}</p>
        <p style="font-size:.75rem;color:#9ca3af;margin-top:.2rem">Avec tâches actives</p>
    </div>
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:1rem">
        <p style="font-size:1.5rem;font-weight:800;color:#dc2626">{{ $stats['sans_taches'] }}</p>
        <p style="font-size:.75rem;color:#9ca3af;margin-top:.2rem">Sans tâches</p>
    </div>
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:1rem">
        <p style="font-size:1.5rem;font-weight:800;color:#111827">{{ $stats['total_modules'] }}</p>
        <p style="font-size:.75rem;color:#9ca3af;margin-top:.2rem">Modules disponibles</p>
    </div>
</div>

{{-- LISTE DU STAFF --}}
<div style="display:grid;gap:1rem">
@forelse($staffMembers as $s)
@php
    $tachesActives = $s->taskAssignments->where('actif', true);
    $role          = $s->user?->roles->pluck('name')->first() ?? 'staff';
    $initiales     = strtoupper(mb_substr($s->prenom,0,1).mb_substr($s->nom,0,1));
    $colors        = ['#6366f1','#0891b2','#059669','#d97706','#dc2626','#7c3aed'];
    $color         = $colors[$s->id % count($colors)];
@endphp
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden">
    <div style="padding:1.125rem 1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;border-bottom:1px solid #f3f4f6">

        {{-- Avatar + infos --}}
        <div style="width:40px;height:40px;border-radius:10px;background:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:#fff;flex-shrink:0">{{ $initiales }}</div>
        <div style="flex:1;min-width:0">
            <p style="font-size:.9rem;font-weight:700;color:#111827">{{ $s->prenom }} {{ $s->nom }}</p>
            <p style="font-size:.75rem;color:#9ca3af;margin-top:.1rem">
                {{ $s->poste ?? 'Personnel' }}
                @if($s->administrativeUnit) · {{ $s->administrativeUnit->name }} @endif
                @if($s->matricule) · <span style="font-family:'JetBrains Mono',monospace">{{ $s->matricule }}</span> @endif
            </p>
        </div>

        {{-- Rôle --}}
        <span style="background:#dbeafe;color:#1e40af;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:4px">
            {{ ucfirst($role) }}
        </span>

        {{-- Nb tâches --}}
        <span style="background:{{ $tachesActives->count() > 0 ? '#d1fae5' : '#f3f4f6' }};color:{{ $tachesActives->count() > 0 ? '#065f46' : '#6b7280' }};font-size:.75rem;font-weight:700;padding:.2rem .65rem;border-radius:20px">
            {{ $tachesActives->count() }} module(s) actif(s)
        </span>

        {{-- Bouton configurer --}}
        <a href="{{ route('admin.staff_tasks.show', $s) }}"
           style="background:#1f2937;color:#fff;padding:.4rem .9rem;border-radius:8px;text-decoration:none;font-size:.8rem;font-weight:600">
            Configurer →
        </a>
    </div>

    {{-- Modules actifs (aperçu) --}}
    @if($tachesActives->count() > 0)
    <div style="padding:.75rem 1.5rem;display:flex;flex-wrap:wrap;gap:.5rem">
        @foreach($tachesActives as $assignment)
        <div style="display:inline-flex;align-items:center;gap:.4rem;background:#f8fafc;border:1px solid #e5e7eb;padding:.25rem .65rem;border-radius:6px">
            <span style="width:6px;height:6px;border-radius:50%;background:#059669;display:inline-block"></span>
            <span style="font-size:.75rem;color:#374151;font-weight:500">{{ $assignment->module?->label ?? '—' }}</span>
            {{-- Toggle rapide --}}
            <form method="POST" action="{{ route('admin.staff_tasks.toggle', $assignment) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit"
                    onclick="return confirm('Désactiver ce module ?')"
                    style="background:none;border:none;cursor:pointer;padding:0;font-size:.7rem;color:#dc2626;margin-left:.2rem"
                    title="Désactiver">✕</button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div style="padding:.75rem 1.5rem">
        <p style="font-size:.78rem;color:#9ca3af;font-style:italic">Aucun module assigné — cliquez sur "Configurer" pour attribuer des tâches.</p>
    </div>
    @endif
</div>
@empty
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:3rem;text-align:center">
    <p style="color:#9ca3af">Aucun membre du staff trouvé.</p>
    <a href="{{ route('admin.staff') }}" style="font-size:.85rem;color:#1f2937">Gérer le staff →</a>
</div>
@endforelse
</div>

</div>
@endsection
