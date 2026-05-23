@extends('parent.master')
@section('title', 'Bulletins — ' . $apprenant->prenom . ' ' . $apprenant->nom)
@section('page-title', 'Bulletins de ' . $apprenant->prenom)
@section('page-sub', $institution->name . ' · ' . $institution->academic_year)

@section('content')
<style>
@keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
.fu  { animation: fadeUp .4s cubic-bezier(.22,1,.36,1) both; }
.fu1 { animation-delay:.04s } .fu2 { animation-delay:.09s }
.fu3 { animation-delay:.14s } .fu4 { animation-delay:.19s }
.fu5 { animation-delay:.24s }

/* ── CARTE ENFANT ── */
.enf-card {
    background: var(--ink);
    border-radius: 14px;
    padding: 1.375rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.enf-card::before {
    content:'';
    position:absolute;inset:0;
    background:
        radial-gradient(ellipse 50% 80% at 95% 50%, rgba(212,160,23,.3) 0%, transparent 65%),
        radial-gradient(ellipse 30% 60% at 5% 90%, rgba(99,102,241,.15) 0%, transparent 65%);
    pointer-events:none;
}
.enf-avatar {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; font-weight: 800; color: #fff;
    flex-shrink: 0;
    position: relative; z-index: 1;
}
.enf-info { position:relative;z-index:1;flex:1;min-width:0; }
.enf-name  { font-size:1.1rem;font-weight:800;color:#fff;letter-spacing:-.02em; }
.enf-meta  { font-size:.78rem;color:rgba(255,255,255,.45);margin-top:.25rem; }

/* ── BULLETIN CARD ── */
.bul-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
    margin-bottom: 1rem;
}
.bul-card:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,.08);
    transform: translateY(-2px);
}

.bul-header {
    display: grid;
    grid-template-columns: 1fr auto auto auto auto;
    gap: 1.5rem;
    align-items: center;
    padding: 1.25rem 1.5rem;
    cursor: pointer;
}
@media(max-width:640px) {
    .bul-header { grid-template-columns: 1fr 1fr; gap: .875rem; }
    .bul-rang, .bul-mention-wrap { display:none; }
}

.bul-periode { font-size:.95rem;font-weight:700;color:var(--ink); }
.bul-date    { font-size:.73rem;color:var(--ink-40);margin-top:.2rem; }

.bul-moy-val  { font-size:2rem;font-weight:900;letter-spacing:-.05em;line-height:1;font-family:'JetBrains Mono',monospace; }
.bul-moy-base { font-size:.72rem;color:var(--ink-40);margin-top:.1rem; }

.bul-rang-val { font-size:1.2rem;font-weight:800;color:var(--ink);font-family:'JetBrains Mono',monospace; }
.bul-rang-lbl { font-size:.68rem;color:var(--ink-40);margin-top:.1rem; }

.mini-prog { height:3px;background:#f1f5f9;border-radius:2px;overflow:hidden;margin-top:.4rem;width:60px; }
.mini-fill { height:100%;border-radius:2px; }

.chevron {
    width:28px;height:28px;border-radius:7px;
    background:var(--bg);border:1px solid var(--border);
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;transition:all .2s;
}
.chevron svg { width:14px;height:14px;color:var(--ink-40);transition:transform .25s; }
.bul-card.open .chevron { background:var(--ink);border-color:var(--ink); }
.bul-card.open .chevron svg { transform:rotate(180deg);color:#fff; }

/* ── DÉTAIL MATIÈRES (accordéon) ── */
.bul-detail { display:none;border-top:1px solid var(--border); }
.bul-card.open .bul-detail { display:block; }

.detail-table { width:100%;border-collapse:collapse; }
.detail-table th {
    background:#fafbfd;padding:.6rem 1.25rem;
    text-align:left;font-size:.65rem;font-weight:700;
    text-transform:uppercase;letter-spacing:.07em;color:var(--ink-40);
    border-bottom:1px solid var(--border);
}
.detail-table td {
    padding:.8rem 1.25rem;border-bottom:1px solid #f3f4f6;
    font-size:.82rem;color:var(--ink-70);vertical-align:middle;
}
.detail-table tr:last-child td { border-bottom:none; }
.detail-table tr:hover td { background:#fafbfc; }

.mat-name { font-weight:700;color:var(--ink);font-size:.82rem; }
.mat-coeff { font-size:.7rem;color:var(--ink-40); }

.grade-pill {
    display:inline-flex;align-items:center;justify-content:center;
    min-width:44px;height:26px;padding:0 8px;
    border-radius:6px;font-size:.82rem;font-weight:800;
    font-family:'JetBrains Mono',monospace;
}

/* Appréciations */
.bul-apprec {
    padding:1rem 1.5rem;
    background:#fafbfd;
    border-top:1px solid var(--border);
    display:grid;grid-template-columns:1fr 1fr;gap:1rem;
}
@media(max-width:640px) { .bul-apprec { grid-template-columns:1fr; } }

.apprec-block { }
.apprec-label { font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ink-40);margin-bottom:.35rem; }
.apprec-text  { font-size:.82rem;color:var(--ink-70);line-height:1.6;background:#fff;border:1px solid var(--border);border-left:3px solid var(--ink);border-radius:0 8px 8px 0;padding:.625rem .875rem; }

/* ── ÉTAT VIDE ── */
.empty-state {
    background:var(--white);border:1px solid var(--border);
    border-radius:14px;padding:4rem 2rem;text-align:center;
}
.empty-icon { width:64px;height:64px;background:var(--bg);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:1.75rem; }

/* ── RÉSUMÉ ANNUEL ── */
.annual-card {
    background:var(--gold-l);
    border:1px solid #f0d89a;
    border-radius:14px;
    padding:1.25rem 1.5rem;
    margin-bottom:1.5rem;
    display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;
}
</style>

{{-- ── CARTE ENFANT ── --}}
@php
    $init   = strtoupper(mb_substr($apprenant->prenom,0,1).mb_substr($apprenant->nom,0,1));
    $colors = ['#6366f1','#0891b2','#059669','#d97706','#dc2626'];
    $color  = $colors[$apprenant->id % count($colors)];
@endphp
<div class="enf-card fu fu1">
    <div class="enf-avatar" style="background:{{ $color }}">{{ $init }}</div>
    <div class="enf-info">
        <p class="enf-name">{{ $apprenant->prenom }} {{ $apprenant->nom }}</p>
        <p class="enf-meta">
            {{ $apprenant->matricule ?? '' }}
            @if($apprenant->classe) · {{ $apprenant->classe->name }} @endif
            · {{ $institution->name }}
        </p>
    </div>
    @if($bulletins->isNotEmpty())
    <div style="position:relative;z-index:1;text-align:right">
        <p style="font-size:.68rem;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em">Bulletins</p>
        <p style="font-size:2rem;font-weight:900;color:#fff;font-family:'JetBrains Mono',monospace;line-height:1">
            {{ $bulletins->count() }}
        </p>
        <p style="font-size:.72rem;color:rgba(255,255,255,.4)">publié(s)</p>
    </div>
    @endif
</div>

{{-- ── RÉSUMÉ ANNUEL ── --}}
@php $moyGlobale = $bulletins->whereNotNull('moyenne_generale')->avg('moyenne_generale'); @endphp
@if($bulletins->count() > 0 && $moyGlobale)
<div class="annual-card fu fu2">
    <div>
        <p style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--gold)">Moyenne annuelle</p>
        <p style="font-size:1.75rem;font-weight:900;color:var(--ink);font-family:'JetBrains Mono',monospace;margin-top:.15rem">
            {{ number_format($moyGlobale, $config?->decimales ?? 2) }}
            <span style="font-size:.9rem;font-weight:500;color:var(--ink-40)">/{{ $config?->note_max ?? 20 }}</span>
        </p>
    </div>
    <div style="width:1px;height:40px;background:#f0d89a"></div>
    <div>
        <p style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--gold)">Périodes reçues</p>
        <p style="font-size:1.75rem;font-weight:900;color:var(--ink);font-family:'JetBrains Mono',monospace;margin-top:.15rem">
            {{ $bulletins->count() }}
        </p>
    </div>
    @if($bulletins->count() > 0)
    <div style="width:1px;height:40px;background:#f0d89a"></div>
    <div>
        <p style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--gold)">Admis(e)</p>
        <p style="font-size:1rem;font-weight:800;color:{{ $bulletins->where('admis',true)->count() === $bulletins->count() ? 'var(--teal)' : 'var(--red)' }};margin-top:.3rem">
            {{ $bulletins->where('admis',true)->count() === $bulletins->count() ? '✓ Toutes périodes' : $bulletins->where('admis',true)->count().'/'.$bulletins->count().' périodes' }}
        </p>
    </div>
    @endif
</div>
@endif

{{-- ── LISTE DES BULLETINS ── --}}
@if($bulletins->isEmpty())

<div class="empty-state fu fu2">
    <div class="empty-icon">📋</div>
    <p style="font-size:1rem;font-weight:700;color:var(--ink);margin-bottom:.5rem">
        Aucun bulletin disponible
    </p>
    <p style="font-size:.85rem;color:var(--ink-40);line-height:1.6;max-width:320px;margin:0 auto">
        Les résultats de {{ $apprenant->prenom }} apparaîtront ici dès que l'administration les aura publiés.
    </p>
</div>

@else

@foreach($bulletins as $b)
@php
    $noteMax    = $config?->note_max ?? 20;
    $moy        = (float) $b->moyenne_generale;
    $pct        = $noteMax > 0 ? ($moy / $noteMax * 100) : 0;
    $couleurMoy = $pct >= 80 ? '#059669' : ($pct >= 50 ? '#d97706' : '#dc2626');
    $fillBar    = $pct >= 80 ? '#10b981' : ($pct >= 50 ? '#f59e0b' : '#ef4444');
    $delay      = min($loop->index + 2, 6);
@endphp

<div class="bul-card fu fu{{ $delay }}" id="bul-{{ $b->id }}" style="border-left:3px solid {{ $couleurMoy }}">

    {{-- ── EN-TÊTE CLIQUABLE (accordéon) ── --}}
    <div class="bul-header" onclick="toggleBulletin({{ $b->id }})">

        {{-- Période --}}
        <div>
            <p class="bul-periode">{{ $b->periodeLabel() }}</p>
            <p class="bul-date">Publié le {{ $b->publie_at?->format('d/m/Y') ?? '—' }}</p>
            @if($b->classe)
                <p style="font-size:.7rem;color:var(--ink-40);margin-top:.1rem">{{ $b->classe->name }}</p>
            @endif
        </div>

        {{-- Moyenne --}}
        <div style="text-align:center">
            @if($b->moyenne_generale !== null)
                <p class="bul-moy-val" style="color:{{ $couleurMoy }}">
                    {{ number_format($moy, $config?->decimales ?? 2) }}
                </p>
                <p class="bul-moy-base">/{{ $noteMax }}</p>
                <div class="mini-prog">
                    <div class="mini-fill" style="width:{{ min(100,$pct) }}%;background:{{ $fillBar }}"></div>
                </div>
            @else
                <p style="font-size:1.5rem;color:#d1d5db;font-family:'JetBrains Mono',monospace">—</p>
                <p class="bul-moy-base">/{{ $noteMax }}</p>
            @endif
        </div>

        {{-- Rang --}}
        <div class="bul-rang" style="text-align:center">
            @if($b->rang)
                <p class="bul-rang-val">{{ $b->rang }}<sup style="font-size:.5em;color:var(--ink-40)">e</sup></p>
                <p class="bul-rang-lbl">/ {{ $b->effectif_classe }} élèves</p>
            @else
                <p style="font-size:1.2rem;color:#d1d5db">—</p>
                <p class="bul-rang-lbl">rang</p>
            @endif
        </div>

        {{-- Mention + Admis --}}
        <div class="bul-mention-wrap" style="text-align:center">
            @if($b->mention)
                <span style="display:inline-block;background:var(--blue-l);color:var(--blue);font-size:.75rem;font-weight:700;padding:.2rem .6rem;border-radius:6px;margin-bottom:.35rem">
                    {{ $b->mention }}
                </span>
            @endif
            @if($b->calcule_at)
            <div>
                <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;font-weight:600;padding:.2rem .55rem;border-radius:20px;{{ $b->admis ? 'background:#d1fae5;color:#065f46' : 'background:#fee2e2;color:#991b1b' }}">
                    {{ $b->admis ? '✓ Admis(e)' : '✗ Non admis(e)' }}
                </span>
            </div>
            @endif
        </div>

        {{-- Chevron --}}
        <div class="chevron">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    {{-- ── DÉTAIL PAR MATIÈRE (accordéon) ── --}}
    <div class="bul-detail">
        @if(!empty($b->detail_matieres))
        <div style="overflow-x:auto">
        <table class="detail-table">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th style="text-align:center">Coeff.</th>
                    <th style="text-align:center">Devoirs<br><span style="font-weight:400;text-transform:none;letter-spacing:0">({{ $config?->pct_devoirs ?? 40 }}%)</span></th>
                    <th style="text-align:center">Examens<br><span style="font-weight:400;text-transform:none;letter-spacing:0">({{ $config?->pct_examen ?? 60 }}%)</span></th>
                    <th style="text-align:center">Moyenne</th>
                    <th style="text-align:left">Progression</th>
                </tr>
            </thead>
            <tbody>
            @php $noteMax = $config?->note_max ?? 20; @endphp
            @foreach($b->detail_matieres as $m)
            @php
                $mMoy  = (float) $m['moyenne'];
                $mPct  = $noteMax > 0 ? ($mMoy / $noteMax * 100) : 0;
                $mFg   = $mPct >= 80 ? '#065f46' : ($mPct >= 50 ? '#92400e' : '#991b1b');
                $mBg   = $mPct >= 80 ? '#d1fae5' : ($mPct >= 50 ? '#fef3c7' : '#fee2e2');
                $mFill = $mPct >= 80 ? '#10b981' : ($mPct >= 50 ? '#f59e0b' : '#ef4444');
            @endphp
            <tr>
                <td>
                    <p class="mat-name">{{ $m['nom'] }}</p>
                    <p class="mat-coeff">Coeff. {{ $m['coefficient'] }}</p>
                </td>
                <td style="text-align:center;font-size:.82rem;color:var(--ink-40)">
                    {{ $m['coefficient'] }}
                </td>
                <td style="text-align:center;font-size:.82rem;color:var(--ink-70)">
                    {{ isset($m['moy_devoirs']) && $m['moy_devoirs'] !== null
                        ? number_format($m['moy_devoirs'], $config?->decimales ?? 2)
                        : '—' }}
                </td>
                <td style="text-align:center;font-size:.82rem;color:var(--ink-70)">
                    {{ isset($m['moy_examens']) && $m['moy_examens'] !== null
                        ? number_format($m['moy_examens'], $config?->decimales ?? 2)
                        : '—' }}
                </td>
                <td style="text-align:center">
                    <span class="grade-pill" style="background:{{ $mBg }};color:{{ $mFg }}">
                        {{ number_format($mMoy, $config?->decimales ?? 2) }}
                    </span>
                </td>
                <td style="min-width:110px">
                    <div style="background:#f1f5f9;height:5px;border-radius:3px;width:90px;overflow:hidden">
                        <div style="background:{{ $mFill }};height:100%;width:{{ min(100,$mPct) }}%;border-radius:3px"></div>
                    </div>
                    <p style="font-size:.65rem;color:var(--ink-40);margin-top:.2rem">{{ round($mPct) }}%</p>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div style="padding:1.5rem;text-align:center;color:var(--ink-40);font-size:.85rem">
            Aucun détail disponible pour ce bulletin.
        </div>
        @endif

        {{-- Appréciations --}}
        @if($b->appreciation_conseil || $b->appreciation_directeur)
        <div class="bul-apprec">
            @if($b->appreciation_conseil)
            <div class="apprec-block">
                <p class="apprec-label">Conseil de classe</p>
                <p class="apprec-text">{{ $b->appreciation_conseil }}</p>
            </div>
            @endif
            @if($b->appreciation_directeur)
            <div class="apprec-block">
                <p class="apprec-label">Direction</p>
                <p class="apprec-text" style="border-left-color:var(--gold)">{{ $b->appreciation_directeur }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>

</div>
@endforeach

@endif

@push('scripts')
<script>
function toggleBulletin(id) {
    const card = document.getElementById('bul-' + id);
    card.classList.toggle('open');
}
</script>
@endpush

@endsection
