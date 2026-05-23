@extends('admin.master')
@section('title', 'Tâches — ' . $staff->prenom . ' ' . $staff->nom)

@section('content')
<div style="max-width:900px;margin:0 auto">

{{-- EN-TÊTE --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
        <a href="{{ route('admin.staff_tasks.index') }}"
           style="font-size:.82rem;color:#6b7280;text-decoration:none">← Retour à la liste</a>
        <h1 style="font-size:1.3rem;font-weight:600;color:#111827;margin-top:.3rem">
            Modules de {{ $staff->prenom }} {{ $staff->nom }}
        </h1>
        <p style="font-size:.85rem;color:#6b7280">
            {{ $staff->poste ?? 'Personnel' }}
            @if($staff->administrativeUnit) · {{ $staff->administrativeUnit->name }} @endif
        </p>
    </div>
</div>

@if(session('success'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">{{ session('success') }}</div>
@endif

{{-- FORMULAIRE D'ASSIGNATION EN MASSE --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
    <h2 style="font-size:.95rem;font-weight:600;margin-bottom:1.25rem">
        Modules disponibles
        <span style="font-size:.78rem;color:#9ca3af;font-weight:400;margin-left:.5rem">
            (cochez les modules à activer pour ce membre)
        </span>
    </h2>

    <form method="POST" action="{{ route('admin.staff_tasks.bulk_assign', $staff) }}">
        @csrf
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem;margin-bottom:1.5rem">
            @foreach($modulesDisponibles as $module)
            @php
                $checked = in_array($module->id, $modulesAssignesIds);
                $assignment = $staff->taskAssignments->firstWhere('module_id', $module->id);
            @endphp
            <label style="cursor:pointer;display:flex;gap:.875rem;padding:1rem;border:2px solid {{ $checked ? '#1f2937' : '#e5e7eb' }};border-radius:10px;transition:border-color .2s;background:{{ $checked ? '#f8f9ff' : '#fff' }}"
                   onclick="this.style.borderColor=this.querySelector('input').checked?'#e5e7eb':'#1f2937';this.style.background=this.querySelector('input').checked?'#fff':'#f8f9ff'">
                <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                       {{ $checked ? 'checked' : '' }}
                       style="width:18px;height:18px;margin-top:.1rem;flex-shrink:0;accent-color:#1f2937">
                <div style="flex:1">
                    <p style="font-size:.875rem;font-weight:700;color:#111827">{{ $module->label }}</p>
                    <p style="font-size:.75rem;color:#9ca3af;margin-top:.2rem;line-height:1.4">{{ $module->description }}</p>
                    @if($checked && $assignment?->assigne)
                    <p style="font-size:.68rem;color:#059669;margin-top:.3rem">
                        ✓ Assigné le {{ $assignment->assigne_at?->format('d/m/Y') }}
                        par {{ $assignment->assigne->name }}
                    </p>
                    @endif
                </div>
            </label>
            @endforeach
        </div>

        <button type="submit"
                style="background:#1f2937;color:#fff;padding:.6rem 1.5rem;border-radius:8px;border:none;cursor:pointer;font-size:.875rem;font-weight:600">
            Sauvegarder les modules
        </button>
        <a href="{{ route('admin.staff_tasks.index') }}"
           style="margin-left:1rem;font-size:.875rem;color:#6b7280;text-decoration:none">Annuler</a>
    </form>
</div>

{{-- MODULES ACTUELLEMENT ASSIGNÉS + INSTRUCTIONS --}}
@if($staff->taskAssignments->where('actif', true)->count() > 0)
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden">
    <div style="padding:1rem 1.5rem;border-bottom:1px solid #f3f4f6">
        <h2 style="font-size:.95rem;font-weight:600">Instructions par module</h2>
        <p style="font-size:.78rem;color:#9ca3af;margin-top:.2rem">Ajoutez des notes spécifiques visibles dans le dashboard du staff.</p>
    </div>

    @foreach($staff->taskAssignments->where('actif', true) as $assignment)
    <div style="padding:1.125rem 1.5rem;border-bottom:1px solid #f3f4f6">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem">
            <span style="background:#d1fae5;color:#065f46;font-size:.75rem;font-weight:700;padding:.2rem .6rem;border-radius:4px">
                ✓ Actif
            </span>
            <p style="font-size:.875rem;font-weight:700;color:#111827">{{ $assignment->module?->label }}</p>
            {{-- Toggle actif/inactif --}}
            <form method="POST" action="{{ route('admin.staff_tasks.toggle', $assignment) }}" style="display:inline;margin-left:auto">
                @csrf @method('PATCH')
                <button type="submit"
                    onclick="return confirm('Changer le statut de ce module ?')"
                    style="background:#fee2e2;color:#dc2626;border:none;padding:.3rem .7rem;border-radius:6px;font-size:.75rem;cursor:pointer">
                    Désactiver
                </button>
            </form>
            {{-- Supprimer --}}
            <form method="POST" action="{{ route('admin.staff_tasks.remove', $assignment) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit"
                    onclick="return confirm('Retirer définitivement ce module ?')"
                    style="background:#f3f4f6;color:#6b7280;border:none;padding:.3rem .7rem;border-radius:6px;font-size:.75rem;cursor:pointer">
                    Retirer
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.staff_tasks.notes', $assignment) }}" style="display:flex;gap:.75rem;align-items:flex-start">
            @csrf @method('PATCH')
            <textarea name="notes" rows="2" placeholder="Instructions spécifiques pour ce module (optionnel)…"
                style="flex:1;border:1px solid #e5e7eb;border-radius:8px;padding:.5rem .75rem;font-size:.8rem;resize:vertical;font-family:inherit">{{ $assignment->notes }}</textarea>
            <button type="submit"
                style="background:#f3f4f6;border:none;padding:.5rem .875rem;border-radius:8px;cursor:pointer;font-size:.8rem;white-space:nowrap;flex-shrink:0">
                Sauver
            </button>
        </form>
    </div>
    @endforeach
</div>
@endif

</div>
@endsection
