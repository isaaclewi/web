@extends('admin.master')
@section('title', 'Bulletin — ' . ($bulletin->apprenant?->prenom . ' ' . $bulletin->apprenant?->nom))

@section('content')
<div style="max-width:900px;margin:0 auto">

{{-- ── EN-TÊTE ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
        <a href="{{ route('admin.bulletins.index', ['periode' => $bulletin->periode]) }}"
           style="font-size:.82rem;color:#6b7280;text-decoration:none">← Retour aux bulletins</a>
        <h1 style="font-size:1.3rem;font-weight:600;color:#111827;margin-top:.3rem">
            Bulletin — {{ $bulletin->apprenant?->prenom }} {{ $bulletin->apprenant?->nom }}
        </h1>
        <p style="font-size:.85rem;color:#6b7280">
            {{ $bulletin->periodeLabel() }} · {{ $bulletin->annee_academique }}
            @if($bulletin->classe) · {{ $bulletin->classe->name }} @endif
        </p>
    </div>
    <div style="display:flex;gap:.75rem">
        {{-- Publier / Dépublier --}}
        @if($bulletin->calcule_at)
            @if($bulletin->publie)
                <form method="POST" action="{{ route('admin.bulletins.depublier', $bulletin) }}">
                    @csrf @method('PATCH')
                    <button type="submit" onclick="return confirm('Dépublier ce bulletin ?')"
                            style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;padding:.5rem 1rem;border-radius:8px;cursor:pointer;font-size:.875rem">
                        Dépublier
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.bulletins.publier', $bulletin) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            style="background:#059669;color:#fff;border:none;padding:.5rem 1rem;border-radius:8px;cursor:pointer;font-size:.875rem">
                        ✓ Publier ce bulletin
                    </button>
                </form>
            @endif
        @endif
    </div>
</div>

{{-- ── FLASH ── --}}
@if(session('success'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.875rem">
    {{ session('success') }}
</div>
@endif

{{-- ── CARTE RÉSUMÉ ── --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1.5rem">

        {{-- Moyenne générale --}}
        <div style="text-align:center">
            @php
                $noteMax = $config->note_max ?? 20;
                $moy     = $bulletin->moyenne_generale;
                $pct     = $moy ? ($moy / $noteMax * 100) : 0;
                $couleur = $pct >= 80 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc2626');
            @endphp
            <p style="font-size:.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem">Moyenne générale</p>
            <p style="font-size:2.5rem;font-weight:800;color:{{ $couleur }};line-height:1">
                {{ $moy !== null ? number_format($moy, $config->decimales ?? 2) : '—' }}
            </p>
            <p style="font-size:.85rem;color:#9ca3af">/{{ $noteMax }}</p>
        </div>

        {{-- Rang --}}
        <div style="text-align:center">
            <p style="font-size:.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem">Rang</p>
            @if($bulletin->rang)
                <p style="font-size:2.5rem;font-weight:800;color:#1f2937;line-height:1">{{ $bulletin->rang }}</p>
                <p style="font-size:.85rem;color:#9ca3af">sur {{ $bulletin->effectif_classe }} élèves</p>
            @else
                <p style="font-size:1.5rem;color:#d1d5db">—</p>
            @endif
        </div>

        {{-- Mention --}}
        <div style="text-align:center">
            <p style="font-size:.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem">Mention</p>
            @if($bulletin->mention)
                <span style="background:#dbeafe;color:#1e40af;font-size:.95rem;font-weight:600;padding:.4rem .8rem;border-radius:6px">
                    {{ $bulletin->mention }}
                </span>
            @else
                <p style="font-size:1.5rem;color:#d1d5db">—</p>
            @endif
        </div>

        {{-- Résultat --}}
        <div style="text-align:center">
            <p style="font-size:.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem">Résultat</p>
            @if($bulletin->calcule_at)
                @if($bulletin->admis)
                    <span style="background:#d1fae5;color:#065f46;font-size:.95rem;font-weight:600;padding:.4rem .8rem;border-radius:6px">Admis(e)</span>
                @else
                    <span style="background:#fee2e2;color:#991b1b;font-size:.95rem;font-weight:600;padding:.4rem .8rem;border-radius:6px">Non admis(e)</span>
                @endif
            @else
                <p style="font-size:1.5rem;color:#d1d5db">—</p>
            @endif
        </div>

        {{-- Statut publication --}}
        <div style="text-align:center">
            <p style="font-size:.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem">Publication</p>
            @if($bulletin->publie)
                <span style="display:inline-flex;align-items:center;gap:.3rem;background:#d1fae5;color:#065f46;font-size:.85rem;padding:.35rem .7rem;border-radius:20px">
                    <span style="width:7px;height:7px;border-radius:50%;background:#059669"></span> Publié
                </span>
                @if($bulletin->publie_at)
                <p style="font-size:.72rem;color:#9ca3af;margin-top:.3rem">{{ $bulletin->publie_at->format('d/m/Y H:i') }}</p>
                @endif
            @elseif($bulletin->calcule_at)
                <span style="display:inline-flex;align-items:center;gap:.3rem;background:#fef3c7;color:#92400e;font-size:.85rem;padding:.35rem .7rem;border-radius:20px">
                    <span style="width:7px;height:7px;border-radius:50%;background:#d97706"></span> Non publié
                </span>
            @else
                <span style="color:#9ca3af;font-size:.85rem">Non calculé</span>
            @endif
        </div>
    </div>

    {{-- Sous-moyennes devoirs/examens --}}
    @if($bulletin->calcule_at)
    <div style="display:flex;gap:2rem;margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid #f3f4f6;flex-wrap:wrap">
        <div>
            <span style="font-size:.78rem;color:#9ca3af">Moy. devoirs </span>
            <strong style="font-size:.9rem;color:#374151">
                {{ $bulletin->moyenne_devoirs !== null ? number_format($bulletin->moyenne_devoirs, $config->decimales ?? 2) : '—' }}
            </strong>
            <span style="font-size:.75rem;color:#d1d5db">/{{ $config->note_max }}</span>
            <span style="font-size:.75rem;color:#9ca3af;margin-left:.3rem">({{ $config->pct_devoirs }}%)</span>
        </div>
        <div>
            <span style="font-size:.78rem;color:#9ca3af">Moy. examens </span>
            <strong style="font-size:.9rem;color:#374151">
                {{ $bulletin->moyenne_examens !== null ? number_format($bulletin->moyenne_examens, $config->decimales ?? 2) : '—' }}
            </strong>
            <span style="font-size:.75rem;color:#d1d5db">/{{ $config->note_max }}</span>
            <span style="font-size:.75rem;color:#9ca3af;margin-left:.3rem">({{ $config->pct_examen }}%)</span>
        </div>
        @if($bulletin->calcule_par)
        <div>
            <span style="font-size:.78rem;color:#9ca3af">Calculé par </span>
            <span style="font-size:.85rem;color:#374151">{{ $bulletin->calculePar?->name }}</span>
            <span style="font-size:.75rem;color:#9ca3af"> le {{ $bulletin->calcule_at->format('d/m/Y à H:i') }}</span>
        </div>
        @endif
    </div>
    @endif
</div>

{{-- ── DÉTAIL PAR MATIÈRE ── --}}
@if(!empty($bulletin->detail_matieres))
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:1.5rem">
    <div style="padding:1rem 1.5rem;border-bottom:1px solid #f3f4f6">
        <h2 style="font-size:.95rem;font-weight:600;color:#111827">Détail par matière</h2>
    </div>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="background:#f9fafb">
                <th style="text-align:left;padding:.65rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Matière</th>
                <th style="text-align:center;padding:.65rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Coeff.</th>
                <th style="text-align:center;padding:.65rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">
                    Devoirs <span style="font-weight:400;color:#9ca3af">({{ $config->pct_devoirs }}%)</span>
                </th>
                <th style="text-align:center;padding:.65rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">
                    Examens <span style="font-weight:400;color:#9ca3af">({{ $config->pct_examen }}%)</span>
                </th>
                <th style="text-align:center;padding:.65rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Moyenne</th>
                <th style="text-align:center;padding:.65rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Mention</th>
                <th style="text-align:left;padding:.65rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Progression</th>
            </tr>
        </thead>
        <tbody>
        @php $noteMax = $config->note_max ?? 20; @endphp
        @foreach($bulletin->detail_matieres as $m)
        @php
            $moy    = (float) $m['moyenne'];
            $pct    = $noteMax > 0 ? ($moy / $noteMax * 100) : 0;
            $bg     = $pct >= 80 ? '#d1fae5' : ($pct >= 50 ? '#fef3c7' : '#fee2e2');
            $fg     = $pct >= 80 ? '#065f46' : ($pct >= 50 ? '#92400e' : '#991b1b');
            $fill   = $pct >= 80 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc2626');
        @endphp
        <tr style="border-bottom:1px solid #f3f4f6">
            <td style="padding:.875rem 1rem">
                <p style="font-size:.875rem;font-weight:500;color:#111827">{{ $m['nom'] }}</p>
            </td>
            <td style="padding:.875rem 1rem;text-align:center;font-size:.875rem;color:#6b7280">
                {{ $m['coefficient'] }}
            </td>
            <td style="padding:.875rem 1rem;text-align:center;font-size:.875rem;color:#374151">
                {{ isset($m['moy_devoirs']) && $m['moy_devoirs'] !== null
                    ? number_format($m['moy_devoirs'], $config->decimales ?? 2)
                    : '—' }}
            </td>
            <td style="padding:.875rem 1rem;text-align:center;font-size:.875rem;color:#374151">
                {{ isset($m['moy_examens']) && $m['moy_examens'] !== null
                    ? number_format($m['moy_examens'], $config->decimales ?? 2)
                    : '—' }}
            </td>
            <td style="padding:.875rem 1rem;text-align:center">
                <span style="background:{{ $bg }};color:{{ $fg }};font-weight:700;font-size:.9rem;padding:.2rem .6rem;border-radius:6px">
                    {{ number_format($moy, $config->decimales ?? 2) }}
                </span>
                <span style="font-size:.72rem;color:#9ca3af">/{{ $noteMax }}</span>
            </td>
            <td style="padding:.875rem 1rem;text-align:center">
                @if(!empty($m['mention']))
                    <span style="font-size:.75rem;color:#1e40af;background:#dbeafe;padding:.15rem .45rem;border-radius:4px">
                        {{ $m['mention'] }}
                    </span>
                @else
                    <span style="color:#d1d5db">—</span>
                @endif
            </td>
            <td style="padding:.875rem 1.25rem">
                <div style="background:#e5e7eb;height:6px;border-radius:3px;width:120px;overflow:hidden">
                    <div style="background:{{ $fill }};height:100%;border-radius:3px;width:{{ min(100, $pct) }}%"></div>
                </div>
                <p style="font-size:.68rem;color:#9ca3af;margin-top:.2rem">{{ round($pct) }}%</p>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@else
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:2.5rem;text-align:center;margin-bottom:1.5rem">
    <p style="color:#9ca3af;font-size:.9rem">Aucun détail de matières — bulletin non calculé.</p>
</div>
@endif

{{-- ── APPRÉCIATIONS ── --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.5rem;margin-bottom:1.5rem">
    <h2 style="font-size:.95rem;font-weight:600;color:#111827;margin-bottom:1rem">Appréciations</h2>
    <form method="POST" action="{{ route('admin.bulletins.appreciation', $bulletin) }}">
        @csrf @method('PATCH')
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div>
                <label style="font-size:.8rem;font-weight:500;display:block;margin-bottom:.4rem;color:#6b7280">
                    Appréciation du conseil de classe
                </label>
                <textarea name="appreciation_conseil" rows="3"
                    placeholder="Ex: Bon trimestre. Élève sérieux et impliqué…"
                    style="width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:.6rem .75rem;font-size:.875rem;resize:vertical">{{ $bulletin->appreciation_conseil }}</textarea>
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:500;display:block;margin-bottom:.4rem;color:#6b7280">
                    Appréciation du directeur
                </label>
                <textarea name="appreciation_directeur" rows="3"
                    placeholder="Ex: Félicitations. Continuez vos efforts…"
                    style="width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:.6rem .75rem;font-size:.875rem;resize:vertical">{{ $bulletin->appreciation_directeur }}</textarea>
            </div>
        </div>
        <button type="submit"
                style="background:#1f2937;color:#fff;padding:.5rem 1.25rem;border-radius:8px;border:none;cursor:pointer;font-size:.875rem;margin-top:1rem">
            Sauvegarder les appréciations
        </button>
    </form>
</div>

</div>
@endsection
