@extends('student.master')
@section('title', 'Mon bulletin — ' . $bulletin->periodeLabel())

@section('content')
<div style="max-width:800px;margin:0 auto">

{{-- En-tête --}}
<div style="margin-bottom:1.5rem">
    <a href="{{ route('student.bulletins') }}"
       style="font-size:.82rem;color:#6b7280;text-decoration:none">← Retour à mes bulletins</a>
    <h1 style="font-size:1.2rem;font-weight:600;color:#111827;margin-top:.3rem">
        {{ $bulletin->periodeLabel() }} — {{ $bulletin->annee_academique }}
    </h1>
    <p style="font-size:.85rem;color:#6b7280">
        {{ $apprenant->prenom }} {{ $apprenant->nom }}
        @if($bulletin->classe) · {{ $bulletin->classe->name }} @endif
    </p>
</div>

{{-- Carte résumé --}}
<div style="background:#1f2937;border-radius:14px;padding:1.75rem;margin-bottom:1.5rem;color:#fff">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:1.5rem;text-align:center">

        <div>
            <p style="font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">Moyenne</p>
            <p style="font-size:2.5rem;font-weight:800;line-height:1;color:#fff">
                {{ $bulletin->moyenne_generale !== null ? number_format($bulletin->moyenne_generale, $config?->decimales ?? 2) : '—' }}
            </p>
            <p style="font-size:.8rem;color:#6b7280">/{{ $config?->note_max ?? 20 }}</p>
        </div>

        <div>
            <p style="font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem">Rang</p>
            @if($bulletin->rang)
                <p style="font-size:2rem;font-weight:800;line-height:1;color:#fff">{{ $bulletin->rang }}<sup style="font-size:.5em">e</sup></p>
                <p style="font-size:.8rem;color:#6b7280">/ {{ $bulletin->effectif_classe }}</p>
            @else
                <p style="font-size:1.5rem;color:#4b5563">—</p>
            @endif
        </div>

        <div>
            <p style="font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.6rem">Mention</p>
            @if($bulletin->mention)
                <span style="background:rgba(255,255,255,.15);color:#fff;font-size:.9rem;font-weight:600;padding:.35rem .8rem;border-radius:6px">
                    {{ $bulletin->mention }}
                </span>
            @else
                <span style="color:#4b5563">—</span>
            @endif
        </div>

        <div>
            <p style="font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.6rem">Résultat</p>
            @if($bulletin->admis)
                <span style="background:#059669;color:#fff;font-size:.85rem;font-weight:600;padding:.35rem .75rem;border-radius:6px">
                    ✓ Admis(e)
                </span>
            @else
                <span style="background:#dc2626;color:#fff;font-size:.85rem;font-weight:600;padding:.35rem .75rem;border-radius:6px">
                    Non admis(e)
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Détail matières --}}
@if(!empty($bulletin->detail_matieres))
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:1.5rem">
    <div style="padding:1rem 1.25rem;border-bottom:1px solid #f3f4f6">
        <h2 style="font-size:.9rem;font-weight:600;color:#111827">Résultats par matière</h2>
    </div>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="background:#f9fafb">
                <th style="text-align:left;padding:.6rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Matière</th>
                <th style="text-align:center;padding:.6rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Coeff.</th>
                <th style="text-align:center;padding:.6rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Devoirs</th>
                <th style="text-align:center;padding:.6rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Examens</th>
                <th style="text-align:center;padding:.6rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Moyenne</th>
                <th style="text-align:left;padding:.6rem 1rem;font-size:.72rem;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb">Barre</th>
            </tr>
        </thead>
        <tbody>
        @php $noteMax = $config?->note_max ?? 20; @endphp
        @foreach($bulletin->detail_matieres as $m)
        @php
            $moy  = (float) $m['moyenne'];
            $pct  = $noteMax > 0 ? ($moy / $noteMax * 100) : 0;
            $fg   = $pct >= 80 ? '#065f46' : ($pct >= 50 ? '#92400e' : '#991b1b');
            $bg   = $pct >= 80 ? '#d1fae5' : ($pct >= 50 ? '#fef3c7' : '#fee2e2');
            $fill = $pct >= 80 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc2626');
        @endphp
        <tr style="border-bottom:1px solid #f3f4f6">
            <td style="padding:.8rem 1rem;font-size:.875rem;font-weight:500;color:#111827">{{ $m['nom'] }}</td>
            <td style="padding:.8rem;text-align:center;font-size:.85rem;color:#6b7280">{{ $m['coefficient'] }}</td>
            <td style="padding:.8rem;text-align:center;font-size:.85rem;color:#374151">
                {{ isset($m['moy_devoirs']) && $m['moy_devoirs'] !== null ? number_format($m['moy_devoirs'], $config?->decimales ?? 2) : '—' }}
            </td>
            <td style="padding:.8rem;text-align:center;font-size:.85rem;color:#374151">
                {{ isset($m['moy_examens']) && $m['moy_examens'] !== null ? number_format($m['moy_examens'], $config?->decimales ?? 2) : '—' }}
            </td>
            <td style="padding:.8rem;text-align:center">
                <span style="background:{{ $bg }};color:{{ $fg }};font-weight:700;font-size:.88rem;padding:.2rem .55rem;border-radius:5px">
                    {{ number_format($moy, $config?->decimales ?? 2) }}
                </span>
            </td>
            <td style="padding:.8rem 1rem">
                <div style="background:#e5e7eb;height:5px;border-radius:3px;width:100px;overflow:hidden">
                    <div style="background:{{ $fill }};height:100%;width:{{ min(100, $pct) }}%;border-radius:3px"></div>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif

{{-- Appréciations --}}
@if($bulletin->appreciation_conseil || $bulletin->appreciation_directeur)
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem">
    <h2 style="font-size:.9rem;font-weight:600;color:#111827;margin-bottom:1rem">Appréciations</h2>
    @if($bulletin->appreciation_conseil)
    <div style="margin-bottom:.875rem">
        <p style="font-size:.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem">Conseil de classe</p>
        <p style="font-size:.875rem;color:#374151;line-height:1.6;background:#f9fafb;padding:.75rem 1rem;border-radius:8px;border-left:3px solid #e5e7eb">
            {{ $bulletin->appreciation_conseil }}
        </p>
    </div>
    @endif
    @if($bulletin->appreciation_directeur)
    <div>
        <p style="font-size:.75rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem">Direction</p>
        <p style="font-size:.875rem;color:#374151;line-height:1.6;background:#f9fafb;padding:.75rem 1rem;border-radius:8px;border-left:3px solid #1f2937">
            {{ $bulletin->appreciation_directeur }}
        </p>
    </div>
    @endif
</div>
@endif

</div>
@endsection
