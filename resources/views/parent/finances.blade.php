@extends('parent.master')
@section('title', 'Finances — '.$apprenant->prenom)
@section('page-title', 'Finances de '.$apprenant->prenom.' '.$apprenant->nom)
@section('page-sub', 'Situation des paiements — Année '.$annee)

@section('content')
<style>
.fin-wrap { display:flex;flex-direction:column;gap:1.25rem; }

.fin-banner {
    background:linear-gradient(135deg,#0c2a1a 0%,#0d3320 60%,var(--teal) 100%);
    border-radius:14px;padding:1.5rem 1.75rem;
    display:flex;align-items:center;justify-content:space-between;gap:1rem;
    position:relative;overflow:hidden;
}
.fin-banner::before {
    content:'';position:absolute;inset:0;
    background:radial-gradient(circle at 80% 50%,rgba(13,138,111,.35) 0%,transparent 55%);
}

/* Résumé 3 colonnes */
.fin-summary { display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--border);border-radius:14px;overflow:hidden; }
.fin-cell    { background:#fff;padding:1.25rem 1.5rem;text-align:center; }
.fin-cell-val { font-size:1.4rem;font-weight:800;font-family:'JetBrains Mono',monospace;line-height:1; }
.fin-cell-lbl { font-size:.72rem;color:var(--ink-40);margin-top:.3rem; }
.fin-du   .fin-cell-val { color:var(--ink); }
.fin-paye .fin-cell-val { color:var(--teal); }
.fin-rest .fin-cell-val { color:var(--red); }

/* Taux anneau */
.taux-wrap { display:flex;align-items:center;gap:1.25rem; }
.taux-ring { position:relative;width:80px;height:80px;flex-shrink:0; }
.taux-ring svg { transform:rotate(-90deg); }
.taux-center {
    position:absolute;inset:0;display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    font-weight:800;font-size:.9rem;color:var(--ink);font-family:'JetBrains Mono',monospace;
}
.taux-center small { font-size:.55rem;color:var(--ink-40);font-weight:500; }

/* Mois table */
.fm-table { width:100%;border-collapse:collapse; }
.fm-table th {
    background:#fafbfd;padding:.6rem 1rem;
    text-align:left;font-size:.67rem;font-weight:700;
    text-transform:uppercase;color:var(--ink-40);letter-spacing:.06em;
    border-bottom:1px solid var(--border);
}
.fm-table td {
    padding:.75rem 1rem;border-bottom:1px solid var(--ink-10);
    font-size:.83rem;color:var(--ink);
}
.fm-table tr:last-child td { border-bottom:none; }
.fm-table tr:hover td { background:var(--bg); }

/* Progress mini */
.prog-mini { background:var(--ink-10);height:5px;border-radius:99px;overflow:hidden;margin-top:.375rem; }
.prog-mini-fill { height:100%;border-radius:99px; }

/* Year selector */
.year-sel {
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);
    color:#fff;border-radius:8px;padding:.4rem .75rem;font-size:.8rem;cursor:pointer;outline:none;
}
.year-sel option { color:#000; }

@media(max-width:640px) {
    .fin-summary { grid-template-columns:1fr; }
    .fin-banner { flex-direction:column; }
}
</style>

@php
    $init  = strtoupper(mb_substr($apprenant->prenom,0,1).mb_substr($apprenant->nom,0,1));
    $colors = ['#6366f1','#0891b2','#059669','#d97706','#dc2626'];
    $color  = $colors[$apprenant->id % count($colors)];
    $pct    = $totaux['du'] > 0 ? round($totaux['paye']/$totaux['du']*100,1) : 100;
    $devise = $apprenant->institution?->devise ?? 'FCFA';
@endphp

<div class="fin-wrap">

    {{-- Banner --}}
    <div class="fin-banner">
        <div style="position:relative;z-index:1;display:flex;align-items:center;gap:1rem;">
            <div style="width:54px;height:54px;border-radius:14px;background:{{ $color }};display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:800;color:#fff;flex-shrink:0;">
                {{ $init }}
            </div>
            <div>
                <div style="font-size:1.1rem;font-weight:800;color:#fff;">{{ $apprenant->prenom }} {{ $apprenant->nom }}</div>
                <div style="font-size:.78rem;color:rgba(255,255,255,.5);">
                    {{ $apprenant->classe?->name ?? 'Sans classe' }} · Année {{ $annee }}
                </div>
            </div>
        </div>
        <div style="position:relative;z-index:1;display:flex;gap:.75rem;align-items:center;">
            <form method="GET">
                <select name="annee" class="year-sel" onchange="this.form.submit()">
                    @foreach($anneesDispos as $a)
                    <option value="{{ $a }}" {{ $a==$annee?'selected':'' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Résumé --}}
    <div class="fin-summary">
        <div class="fin-cell fin-du">
            <div class="fin-cell-val">{{ number_format($totaux['du'],0,',',' ') }}</div>
            <div class="fin-cell-lbl">Total dû ({{ $devise }})</div>
        </div>
        <div class="fin-cell fin-paye">
            <div class="fin-cell-val">{{ number_format($totaux['paye'],0,',',' ') }}</div>
            <div class="fin-cell-lbl">Total payé ({{ $devise }})</div>
        </div>
        <div class="fin-cell fin-rest">
            <div class="fin-cell-val">{{ number_format($totaux['reste'],0,',',' ') }}</div>
            <div class="fin-cell-lbl">Reste à payer ({{ $devise }})</div>
        </div>
    </div>

    {{-- Taux + progress --}}
    <div class="p-card">
        <div class="p-card-header"><h3>Taux de paiement</h3></div>
        <div class="p-card-body">
            <div class="taux-wrap">
                <div class="taux-ring">
                    <svg width="80" height="80" viewBox="0 0 80 80">
                        <circle cx="40" cy="40" r="32" fill="none" stroke="var(--ink-10)" stroke-width="9"/>
                        <circle cx="40" cy="40" r="32" fill="none"
                            stroke="{{ $pct>=80?'var(--teal)':($pct>=50?'var(--gold)':'var(--red)') }}"
                            stroke-width="9" stroke-linecap="round"
                            stroke-dasharray="{{ round($pct*2.011) }} 201"/>
                    </svg>
                    <div class="taux-center">
                        {{ $pct }}%
                        <small>payé</small>
                    </div>
                </div>
                <div style="flex:1;">
                    <div style="font-size:1rem;font-weight:700;color:var(--ink);">
                        @if($pct >= 100) 🎉 Tout est à jour !
                        @elseif($pct >= 80) ✅ Presque complet
                        @elseif($pct >= 50) ⚠️ Paiement partiel
                        @else 🔴 Paiement insuffisant
                        @endif
                    </div>
                    <div style="font-size:.82rem;color:var(--ink-40);margin-top:.25rem;">
                        {{ number_format($totaux['paye'],0,',',' ') }} {{ $devise }} payés
                        sur {{ number_format($totaux['du'],0,',',' ') }} {{ $devise }} attendus
                    </div>
                    <div style="background:var(--ink-10);height:8px;border-radius:99px;overflow:hidden;margin-top:.75rem;">
                        <div style="height:100%;border-radius:99px;width:{{ min(100,$pct) }}%;background:{{ $pct>=80?'var(--teal)':($pct>=50?'var(--gold)':'var(--red)') }};transition:width .6s;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau mois par mois --}}
    <div class="p-card">
        <div class="p-card-header">
            <h3>Détail par mois</h3>
            <span style="font-size:.75rem;color:var(--ink-40);">{{ $finances->count() }} enregistrement(s)</span>
        </div>
        @if($finances->count())
        <div style="overflow-x:auto;">
            <table class="fm-table">
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Montant dû</th>
                        <th>Payé</th>
                        <th>Reste</th>
                        <th>Date paiement</th>
                        <th>Mode</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($finances as $f)
                    @php $fp = $f->montant_du>0?round($f->montant_paye/$f->montant_du*100):100; @endphp
                    <tr>
                        <td style="font-weight:600;">{{ $moisLabels[$f->mois] ?? $f->mois_label ?? "Mois {$f->mois}" }}</td>
                        <td class="mono">{{ number_format($f->montant_du,0,',',' ') }}</td>
                        <td>
                            <div class="mono" style="font-weight:700;color:var(--teal);">
                                {{ number_format($f->montant_paye,0,',',' ') }}
                            </div>
                            <div class="prog-mini">
                                <div class="prog-mini-fill" style="width:{{ $fp }}%;background:{{ $fp>=100?'var(--teal)':($fp>=50?'var(--gold)':'var(--red)') }};"></div>
                            </div>
                        </td>
                        <td class="mono" style="font-weight:700;color:{{ $f->montant_reste>0?'var(--red)':'var(--teal)' }};">
                            {{ $f->montant_reste > 0 ? number_format($f->montant_reste,0,',',' ') : '—' }}
                        </td>
                        <td style="font-size:.78rem;color:var(--ink-70);">
                            {{ $f->date_paiement?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td style="font-size:.75rem;color:var(--ink-40);">
                            {{ $f->mode_paiement ?? '—' }}
                        </td>
                        <td>
                            @if($f->statut==='paye')
                                <span class="p-badge p-badge-green">✓ Payé</span>
                            @elseif($f->statut==='partiel')
                                <span class="p-badge p-badge-amber">◑ Partiel</span>
                            @else
                                <span class="p-badge p-badge-red">✗ Impayé</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center;padding:2.5rem;color:var(--ink-40);">
            <div style="font-size:1.5rem;margin-bottom:.5rem;">💰</div>
            Aucun enregistrement financier pour {{ $annee }}.
        </div>
        @endif
    </div>

    {{-- Note info --}}
    <div style="background:var(--blue-l);border:1px solid #b3cffc;border-radius:12px;padding:1rem 1.25rem;
                display:flex;gap:.75rem;font-size:.82rem;color:var(--blue);">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:.1rem;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Pour toute question concernant les paiements, contactez directement l'administration de l'établissement.</span>
    </div>

</div>
@endsection