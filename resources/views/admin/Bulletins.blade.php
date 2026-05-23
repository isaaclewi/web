@extends('admin.master')
@section('title', 'Bulletins — ' . ($periode ?? ''))

@section('content')
<div style="max-width:1200px;margin:0 auto">

{{-- ── EN-TÊTE ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
        <h1 style="font-size:1.3rem;font-weight:600;color:var(--color-text-primary,#111827)">
            Bulletins scolaires
        </h1>
        <p style="font-size:.85rem;color:#6b7280;margin-top:.2rem">
            Année {{ $config->annee_academique }}
            @if($periode) · {{ collect($periodes)->firstWhere('key', $periode)['label'] ?? $periode }} @endif
        </p>
    </div>
    <a href="{{ route('admin.grade_config') }}"
       style="background:#f3f4f6;color:#374151;padding:.5rem 1.1rem;border-radius:8px;font-size:.85rem;text-decoration:none;border:1px solid #e5e7eb">
        ← Configuration
    </a>
</div>

{{-- ── FLASH ── --}}
@if(session('success'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">
    {{ session('error') }}
</div>
@endif

{{-- ── FILTRES ── --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem">
    <form method="GET" action="{{ route('admin.bulletins.index') }}"
          style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">

        <div>
            <label style="font-size:.78rem;font-weight:500;display:block;margin-bottom:.3rem;color:#6b7280">Période</label>
            <select name="periode"
                    style="border:1px solid #e5e7eb;border-radius:8px;padding:.45rem .75rem;font-size:.875rem;background:#fff;min-width:160px">
                @foreach($periodes as $p)
                <option value="{{ $p['key'] }}" {{ $periode === $p['key'] ? 'selected' : '' }}>
                    {{ $p['label'] }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="font-size:.78rem;font-weight:500;display:block;margin-bottom:.3rem;color:#6b7280">Classe</label>
            <select name="classe_id"
                    style="border:1px solid #e5e7eb;border-radius:8px;padding:.45rem .75rem;font-size:.875rem;background:#fff;min-width:160px">
                <option value="">Toutes les classes</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ $classeId == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="font-size:.78rem;font-weight:500;display:block;margin-bottom:.3rem;color:#6b7280">Statut</label>
            <select name="publie"
                    style="border:1px solid #e5e7eb;border-radius:8px;padding:.45rem .75rem;font-size:.875rem;background:#fff;min-width:140px">
                <option value="">Tous</option>
                <option value="1" {{ $publie === '1' ? 'selected' : '' }}>Publiés</option>
                <option value="0" {{ $publie === '0' ? 'selected' : '' }}>Non publiés</option>
            </select>
        </div>

        <button type="submit"
                style="background:#1f2937;color:#fff;padding:.5rem 1.25rem;border-radius:8px;border:none;cursor:pointer;font-size:.875rem">
            Filtrer
        </button>
        <a href="{{ route('admin.bulletins.index') }}"
           style="color:#6b7280;font-size:.875rem;text-decoration:none;padding:.5rem 0">Réinitialiser</a>
    </form>
</div>

{{-- ── ACTIONS GROUPÉES (publication par classe) ── --}}
@if($classeId && $bulletins->total() > 0)
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
    <p style="font-size:.85rem;color:#92400e;flex:1">
        <strong>{{ $bulletins->total() }} bulletin(s)</strong> pour cette classe · {{ collect($periodes)->firstWhere('key', $periode)['label'] ?? $periode }}
    </p>
    <form method="POST" action="{{ route('admin.bulletins.publier.classe') }}" style="display:inline">
        @csrf
        <input type="hidden" name="classe_id" value="{{ $classeId }}">
        <input type="hidden" name="periode"   value="{{ $periode }}">
        <button type="submit"
                onclick="return confirm('Publier tous les bulletins calculés de cette classe ?')"
                style="background:#059669;color:#fff;border:none;padding:.45rem 1rem;border-radius:8px;cursor:pointer;font-size:.82rem">
            ✓ Tout publier
        </button>
    </form>
    <form method="POST" action="{{ route('admin.bulletins.depublier.classe') }}" style="display:inline">
        @csrf
        <input type="hidden" name="classe_id" value="{{ $classeId }}">
        <input type="hidden" name="periode"   value="{{ $periode }}">
        <button type="submit"
                onclick="return confirm('Dépublier tous les bulletins de cette classe ?')"
                style="background:#dc2626;color:#fff;border:none;padding:.45rem 1rem;border-radius:8px;cursor:pointer;font-size:.82rem">
            Dépublier
        </button>
    </form>
</div>
@endif

{{-- ── TABLEAU ── --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden">
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="background:#f9fafb">
                <th style="text-align:left;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Apprenant</th>
                <th style="text-align:left;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Classe</th>
                <th style="text-align:center;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Moyenne</th>
                <th style="text-align:center;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Rang</th>
                <th style="text-align:center;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Mention</th>
                <th style="text-align:center;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Admis</th>
                <th style="text-align:center;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Statut</th>
                <th style="text-align:center;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Calculé le</th>
                <th style="text-align:right;padding:.75rem 1rem;font-size:.75rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($bulletins as $b)
            <tr style="border-bottom:1px solid #f3f4f6;transition:background .15s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">

                {{-- Apprenant --}}
                <td style="padding:.875rem 1rem">
                    <p style="font-size:.875rem;font-weight:500;color:#111827">
                        {{ $b->apprenant?->prenom }} {{ $b->apprenant?->nom }}
                    </p>
                    <p style="font-size:.75rem;color:#9ca3af">{{ $b->apprenant?->matricule }}</p>
                </td>

                {{-- Classe --}}
                <td style="padding:.875rem 1rem;font-size:.875rem;color:#6b7280">
                    {{ $b->classe?->name ?? '—' }}
                </td>

                {{-- Moyenne --}}
                <td style="padding:.875rem 1rem;text-align:center">
                    @if($b->moyenne_generale !== null)
                        @php
                            $noteMax = $config->note_max ?? 20;
                            $pct     = ($b->moyenne_generale / $noteMax) * 100;
                            $couleur = $pct >= 80 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc2626');
                        @endphp
                        <span style="font-size:1rem;font-weight:700;color:{{ $couleur }}">
                            {{ number_format($b->moyenne_generale, $config->decimales ?? 2) }}
                        </span>
                        <span style="font-size:.75rem;color:#9ca3af">/{{ $noteMax }}</span>
                    @else
                        <span style="color:#9ca3af;font-size:.85rem">—</span>
                    @endif
                </td>

                {{-- Rang --}}
                <td style="padding:.875rem 1rem;text-align:center;font-size:.875rem;color:#374151">
                    @if($b->rang)
                        <strong>{{ $b->rang }}</strong>
                        <span style="color:#9ca3af;font-size:.75rem">/{{ $b->effectif_classe }}</span>
                    @else
                        —
                    @endif
                </td>

                {{-- Mention --}}
                <td style="padding:.875rem 1rem;text-align:center">
                    @if($b->mention)
                        <span style="background:#dbeafe;color:#1e40af;font-size:.75rem;padding:.2rem .55rem;border-radius:4px;white-space:nowrap">
                            {{ $b->mention }}
                        </span>
                    @else
                        <span style="color:#d1d5db;font-size:.85rem">—</span>
                    @endif
                </td>

                {{-- Admis --}}
                <td style="padding:.875rem 1rem;text-align:center">
                    @if($b->calcule_at)
                        @if($b->admis)
                            <span style="background:#d1fae5;color:#065f46;font-size:.75rem;padding:.2rem .55rem;border-radius:4px">Oui</span>
                        @else
                            <span style="background:#fee2e2;color:#991b1b;font-size:.75rem;padding:.2rem .55rem;border-radius:4px">Non</span>
                        @endif
                    @else
                        <span style="color:#d1d5db;font-size:.85rem">—</span>
                    @endif
                </td>

                {{-- Statut publication --}}
                <td style="padding:.875rem 1rem;text-align:center">
                    @if($b->publie)
                        <span style="display:inline-flex;align-items:center;gap:.3rem;background:#d1fae5;color:#065f46;font-size:.75rem;padding:.25rem .6rem;border-radius:20px">
                            <span style="width:6px;height:6px;border-radius:50%;background:#059669;display:inline-block"></span>
                            Publié
                        </span>
                        @if($b->publie_at)
                        <p style="font-size:.7rem;color:#9ca3af;margin-top:.2rem">
                            {{ $b->publie_at->format('d/m H:i') }}
                        </p>
                        @endif
                    @elseif($b->calcule_at)
                        <span style="display:inline-flex;align-items:center;gap:.3rem;background:#fef3c7;color:#92400e;font-size:.75rem;padding:.25rem .6rem;border-radius:20px">
                            <span style="width:6px;height:6px;border-radius:50%;background:#d97706;display:inline-block"></span>
                            Calculé
                        </span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:.3rem;background:#f3f4f6;color:#6b7280;font-size:.75rem;padding:.25rem .6rem;border-radius:20px">
                            <span style="width:6px;height:6px;border-radius:50%;background:#9ca3af;display:inline-block"></span>
                            Non calculé
                        </span>
                    @endif
                </td>

                {{-- Calculé le --}}
                <td style="padding:.875rem 1rem;text-align:center;font-size:.78rem;color:#9ca3af">
                    {{ $b->calcule_at ? $b->calcule_at->format('d/m/Y H:i') : '—' }}
                </td>

                {{-- Actions --}}
                <td style="padding:.875rem 1rem;text-align:right">
                    <div style="display:flex;gap:.4rem;justify-content:flex-end;align-items:center">
                        {{-- Voir détail --}}
                        <a href="{{ route('admin.bulletins.show', $b) }}"
                           style="background:#f3f4f6;border:none;padding:.35rem .7rem;border-radius:6px;font-size:.78rem;text-decoration:none;color:#374151">
                            Détail
                        </a>

                        {{-- Publier / Dépublier --}}
                        @if($b->calcule_at)
                            @if($b->publie)
                                <form method="POST" action="{{ route('admin.bulletins.depublier', $b) }}" style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            onclick="return confirm('Dépublier ce bulletin ?')"
                                            style="background:#fee2e2;color:#dc2626;border:none;padding:.35rem .7rem;border-radius:6px;font-size:.78rem;cursor:pointer">
                                        Dépublier
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.bulletins.publier', $b) }}" style="display:inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            style="background:#d1fae5;color:#065f46;border:none;padding:.35rem .7rem;border-radius:6px;font-size:.78rem;cursor:pointer">
                                        Publier
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:3rem;color:#9ca3af;font-size:.9rem">
                    <p>Aucun bulletin pour cette sélection.</p>
                    <p style="font-size:.8rem;margin-top:.4rem">
                        Lancez le calcul depuis
                        <a href="{{ route('admin.grade_config') }}" style="color:#1f2937">la configuration des notes</a>.
                    </p>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    </div>

    {{-- Pagination --}}
    @if($bulletins->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid #f3f4f6">
        {{ $bulletins->links() }}
    </div>
    @endif
</div>

</div>
@endsection