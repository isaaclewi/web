@extends('staff.master')
@section('title', 'Mon profil')
@section('page-title', 'Mon profil')
@section('page-sub', $institution->name)

@section('content')
<div style="max-width:800px;margin:0 auto">

@if(session('success'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">{{ session('success') }}</div>
@endif

{{-- CARTE IDENTITÉ --}}
<div style="background:var(--ink,#0f172a);border-radius:14px;padding:1.75rem 2rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap">
    @php
        $initiales = strtoupper(mb_substr($staff->prenom,0,1).mb_substr($staff->nom,0,1));
        $role      = $user->roles->pluck('name')->first() ?? 'staff';
    @endphp
    <div style="width:64px;height:64px;border-radius:16px;background:#2563eb;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#fff;flex-shrink:0">
        {{ $initiales }}
    </div>
    <div>
        <p style="font-size:1.25rem;font-weight:800;color:#fff;letter-spacing:-.02em">{{ $staff->prenom }} {{ $staff->nom }}</p>
        <p style="font-size:.82rem;color:rgba(255,255,255,.45);margin-top:.2rem">
            {{ ucfirst($role) }}
            @if($staff->poste) · {{ $staff->poste }} @endif
            @if($staff->administrativeUnit) · {{ $staff->administrativeUnit->name }} @endif
        </p>
        @if($staff->matricule)
        <p style="font-size:.75rem;color:rgba(255,255,255,.3);font-family:'JetBrains Mono',monospace;margin-top:.2rem">{{ $staff->matricule }}</p>
        @endif
    </div>
    <div style="margin-left:auto;text-align:right">
        <p style="font-size:.68rem;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.08em">Établissement</p>
        <p style="font-size:.875rem;font-weight:600;color:rgba(255,255,255,.7);margin-top:.2rem">{{ $institution->name }}</p>
    </div>
</div>

{{-- INFOS --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem">
        <h3 style="font-size:.85rem;font-weight:700;margin-bottom:.875rem;color:#111827">Informations personnelles</h3>
        <div style="display:flex;flex-direction:column;gap:.625rem">
            <div><p style="font-size:.72rem;color:#9ca3af">Email</p><p style="font-size:.85rem;color:#111827">{{ $user->email ?? '—' }}</p></div>
            <div><p style="font-size:.72rem;color:#9ca3af">Téléphone</p><p style="font-size:.85rem;color:#111827">{{ $staff->telephone ?? '—' }}</p></div>
            <div><p style="font-size:.72rem;color:#9ca3af">Poste</p><p style="font-size:.85rem;color:#111827">{{ $staff->poste ?? '—' }}</p></div>
            <div><p style="font-size:.72rem;color:#9ca3af">Unité admin.</p><p style="font-size:.85rem;color:#111827">{{ $staff->administrativeUnit?->name ?? '—' }}</p></div>
        </div>
    </div>
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem">
        <h3 style="font-size:.85rem;font-weight:700;margin-bottom:.875rem;color:#111827">Compte</h3>
        <div style="display:flex;flex-direction:column;gap:.625rem">
            <div><p style="font-size:.72rem;color:#9ca3af">Nom d'utilisateur</p><p style="font-size:.85rem;color:#111827">{{ $user->name }}</p></div>
            <div><p style="font-size:.72rem;color:#9ca3af">Rôle</p><p style="font-size:.85rem;color:#111827">{{ ucfirst($role) }}</p></div>
            <div><p style="font-size:.72rem;color:#9ca3af">Statut</p>
                <span style="font-size:.75rem;padding:.15rem .5rem;border-radius:4px;{{ $staff->status ? 'background:#d1fae5;color:#065f46' : 'background:#fee2e2;color:#991b1b' }}">
                    {{ $staff->status ? 'Actif' : 'Inactif' }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- MODULES ATTRIBUÉS --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden">
    <div style="padding:1rem 1.25rem;border-bottom:1px solid #f3f4f6">
        <h3 style="font-size:.875rem;font-weight:700;color:#111827">Mes modules assignés</h3>
        <p style="font-size:.75rem;color:#9ca3af;margin-top:.2rem">Modules activés par votre directeur</p>
    </div>

    @if($staff->taskAssignments->where('actif', true)->count() === 0)
    <div style="padding:2.5rem;text-align:center">
        <p style="color:#9ca3af;font-size:.875rem">Aucun module assigné pour le moment.</p>
    </div>
    @else
    <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.875rem">
        @foreach($staff->taskAssignments->where('actif', true) as $assignment)
        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:1rem;background:#f8fafc">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
                <span style="width:8px;height:8px;border-radius:50%;background:#059669;display:inline-block;flex-shrink:0"></span>
                <p style="font-size:.85rem;font-weight:700;color:#111827">{{ $assignment->module?->label }}</p>
            </div>
            @if($assignment->notes)
            <p style="font-size:.75rem;color:#475569;line-height:1.5;background:#fff;border-left:2px solid #2563eb;padding:.4rem .65rem;border-radius:0 6px 6px 0">
                {{ $assignment->notes }}
            </p>
            @else
            <p style="font-size:.72rem;color:#9ca3af">{{ $assignment->module?->description }}</p>
            @endif
            @if($assignment->assigne_at)
            <p style="font-size:.68rem;color:#9ca3af;margin-top:.5rem">
                Assigné le {{ $assignment->assigne_at->format('d/m/Y') }}
            </p>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

</div>
@endsection
