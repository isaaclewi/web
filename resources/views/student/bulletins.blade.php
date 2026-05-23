@extends('student.master')
@section('title', 'Mes bulletins')

@section('content')
<div style="max-width:800px;margin:0 auto">

<div style="margin-bottom:1.5rem">
    <h1 style="font-size:1.2rem;font-weight:600;color:#111827">Mes bulletins</h1>
    <p style="font-size:.85rem;color:#6b7280;margin-top:.2rem">
        {{ $institution->name }} · Année {{ $institution->academic_year }}
    </p>
</div>

@if($bulletins->isEmpty())
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:3rem;text-align:center">
    <div style="width:48px;height:48px;background:#f3f4f6;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
        <svg width="22" height="22" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
    </div>
    <p style="color:#374151;font-size:.9rem;font-weight:500">Aucun bulletin disponible</p>
    <p style="color:#9ca3af;font-size:.82rem;margin-top:.4rem">
        Vos résultats seront affichés ici dès que l'administration les publiera.
    </p>
</div>

@else

<div style="display:grid;gap:1rem">
@foreach($bulletins as $b)
@php
    $noteMax = $config?->note_max ?? 20;
    $moy     = $b->moyenne_generale;
    $pct     = $moy && $noteMax > 0 ? ($moy / $noteMax * 100) : 0;
    $couleur = $pct >= 80 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc2626');
    $fill    = $pct >= 80 ? '#d1fae5' : ($pct >= 50 ? '#fef3c7' : '#fee2e2');
    $text    = $pct >= 80 ? '#065f46' : ($pct >= 50 ? '#92400e' : '#991b1b');
@endphp
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem 1.5rem;display:grid;grid-template-columns:1fr auto auto auto auto;gap:1.5rem;align-items:center">

    {{-- Période --}}
    <div>
        <p style="font-size:.95rem;font-weight:600;color:#111827">{{ $b->periodeLabel() }}</p>
        <p style="font-size:.78rem;color:#9ca3af;margin-top:.1rem">
            Publié le {{ $b->publie_at?->format('d/m/Y') ?? '—' }}
        </p>
    </div>

    {{-- Moyenne --}}
    <div style="text-align:center">
        <p style="font-size:1.75rem;font-weight:800;color:{{ $couleur }};line-height:1">
            {{ $moy !== null ? number_format($moy, $config?->decimales ?? 2) : '—' }}
        </p>
        <p style="font-size:.72rem;color:#9ca3af">/{{ $noteMax }}</p>
    </div>

    {{-- Rang --}}
    <div style="text-align:center">
        @if($b->rang)
            <p style="font-size:1.1rem;font-weight:700;color:#1f2937">{{ $b->rang }}<sup style="font-size:.6em">e</sup></p>
            <p style="font-size:.72rem;color:#9ca3af">/ {{ $b->effectif_classe }}</p>
        @else
            <p style="color:#d1d5db">—</p>
        @endif
    </div>

    {{-- Mention + admis --}}
    <div style="text-align:center">
        @if($b->mention)
            <span style="display:block;background:#dbeafe;color:#1e40af;font-size:.78rem;padding:.25rem .6rem;border-radius:4px;margin-bottom:.35rem">
                {{ $b->mention }}
            </span>
        @endif
        <span style="font-size:.75rem;font-weight:500;color:{{ $b->admis ? '#059669' : '#dc2626' }}">
            {{ $b->admis ? '✓ Admis(e)' : '✗ Non admis(e)' }}
        </span>
    </div>

    {{-- Lien --}}
    <a href="{{ route('student.bulletins.show', $b) }}"
       style="background:#1f2937;color:#fff;padding:.45rem 1rem;border-radius:8px;text-decoration:none;font-size:.82rem;white-space:nowrap">
        Voir →
    </a>
</div>
@endforeach
</div>
@endif

</div>
@endsection