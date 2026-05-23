@extends('admin.master')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    :root {
        --c-green:  #16a34a; --c-green-lt: #dcfce7; --c-green-dk: #15803d;
        --c-amber:  #d97706; --c-amber-lt: #fef3c7;
        --c-red:    #dc2626; --c-red-lt:   #fee2e2;
        --c-blue:   #2563eb; --c-blue-lt:  #dbeafe;
        --c-gray:   #6b7280; --c-border:   #e5e7eb; --c-bg: #f8fafc;
        --radius:   .875rem;
    }
    * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
    code, .mono { font-family: 'DM Mono', monospace; }

    .stat-card {
        background: #fff; border: 1px solid var(--c-border);
        border-radius: var(--radius); padding: 1.25rem 1.5rem;
        display: flex; flex-direction: column; gap: .35rem;
        position: relative; overflow: hidden;
    }
    .stat-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; }
    .stat-card.blue::before   { background: var(--c-blue); }
    .stat-card.green::before  { background: var(--c-green); }
    .stat-card.red::before    { background: var(--c-red); }
    .stat-card.amber::before  { background: var(--c-amber); }
    .stat-label { font-size: .7rem; font-weight: 600; color: var(--c-gray); text-transform: uppercase; letter-spacing: .07em; }
    .stat-value { font-size: 1.55rem; font-weight: 700; color: #111827; line-height: 1.1; }
    .stat-sub   { font-size: .75rem; color: var(--c-gray); }

    .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .18rem .6rem; border-radius: 9999px; font-size: .7rem; font-weight: 600; white-space: nowrap; }
    .badge-dot { width: .45rem; height: .45rem; border-radius: 50%; display: inline-block; }
    .badge.green { background: var(--c-green-lt); color: #15803d; }
    .badge.green .badge-dot { background: var(--c-green); }
    .badge.amber { background: var(--c-amber-lt); color: #b45309; }
    .badge.amber .badge-dot { background: var(--c-amber); }
    .badge.red   { background: var(--c-red-lt);   color: #b91c1c; }
    .badge.red   .badge-dot { background: var(--c-red); }

    .prog-bar { height: 5px; background: #f3f4f6; border-radius: 9999px; overflow: hidden; }
    .prog-fill { height: 100%; border-radius: 9999px; transition: width .4s ease; }
    .prog-fill.green { background: var(--c-green); }
    .prog-fill.amber { background: var(--c-amber); }
    .prog-fill.red   { background: var(--c-red);   }

    .filter-panel { background: #fff; border: 1px solid var(--c-border); border-radius: var(--radius); padding: 1.125rem 1.5rem; }
    .filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto auto; gap: .75rem; align-items: end; }
    @media(max-width:1100px) { .filter-grid { grid-template-columns: 1fr 1fr 1fr; } }
    @media(max-width:700px)  { .filter-grid { grid-template-columns: 1fr 1fr; } }
    @media(max-width:460px)  { .filter-grid { grid-template-columns: 1fr; } }

    .fi { width:100%; border:1px solid var(--c-border); border-radius:.5rem; padding:.5rem .875rem; font-size:.825rem; color:#1f2937; background:#fff; font-family:'DM Sans',sans-serif; transition:border-color .15s,box-shadow .15s; }
    .fi:focus { outline:none; border-color:#9ca3af; box-shadow:0 0 0 3px rgba(156,163,175,.15); }
    .fi-label { display:block; font-size:.72rem; font-weight:600; color:#6b7280; margin-bottom:.3rem; text-transform:uppercase; letter-spacing:.04em; }

    .fin-table { width:100%; border-collapse:collapse; font-size:.825rem; }
    .fin-table thead th { background:#f9fafb; padding:.6rem 1rem; text-align:left; font-size:.68rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; border-bottom:1px solid var(--c-border); white-space:nowrap; }
    .fin-table thead th.center { text-align:center; }
    .fin-table tbody td { padding:.75rem 1rem; border-bottom:1px solid #f9fafb; vertical-align:middle; }
    .fin-table tbody tr:hover { background:#fdfefe; }
    .fin-table tbody tr:last-child td { border-bottom:none; }

    .avatar { width:2.125rem; height:2.125rem; border-radius:.5rem; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.72rem; flex-shrink:0; }

    .chart-wrap { display:flex; align-items:flex-end; gap:.4rem; height:100px; }
    .chart-col  { flex:1; display:flex; flex-direction:column; align-items:center; gap:.25rem; min-width:0; }
    .chart-bars { flex:1; width:100%; display:flex; align-items:flex-end; gap:2px; }
    .chart-bar  { flex:1; border-radius:.3rem .3rem 0 0; min-height:3px; }
    .chart-bar.du   { background:#e5e7eb; }
    .chart-bar.pay  { background:var(--c-green); }
    .chart-lbl { font-size:.6rem; color:#d1d5db; white-space:nowrap; }

    .btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:.5rem; font-size:.8rem; font-weight:500; cursor:pointer; transition:all .15s; border:none; padding:.5rem .95rem; white-space:nowrap; font-family:'DM Sans',sans-serif; }
    .btn-dark    { background:#111827; color:#fff; }
    .btn-dark:hover  { background:#374151; }
    .btn-success { background:var(--c-green); color:#fff; }
    .btn-success:hover { background:var(--c-green-dk); }
    .btn-ghost   { background:#fff; color:#374151; border:1px solid var(--c-border); }
    .btn-ghost:hover { background:#f9fafb; }
    .btn-icon    { padding:.4rem; background:none; border:none; border-radius:.375rem; color:#9ca3af; cursor:pointer; display:flex; align-items:center; transition:background .15s,color .15s; }
    .btn-icon:hover { background:#f3f4f6; color:#374151; }
    .btn-icon svg { width:1rem; height:1rem; }

    .modal-bd  { position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);z-index:300;display:none;align-items:center;justify-content:center;padding:1rem; }
    .modal-bd.open { display:flex; }
    .modal-box { background:#fff;border-radius:1.125rem;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;box-shadow:0 32px 72px rgba(0,0,0,.2);animation:mIn .2s ease; }
    @keyframes mIn { from{opacity:0;transform:translateY(14px) scale(.97)} to{opacity:1;transform:none} }
    .modal-head { display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f3f4f6; }
    .modal-body { padding:1.5rem; }
    .modal-foot { display:flex;justify-content:flex-end;gap:.75rem;padding:1rem 1.5rem;background:#f9fafb;border-top:1px solid #f3f4f6;border-radius:0 0 1.125rem 1.125rem; }

    .sig { background:#f8fafc;border:1px solid #e2e8f0;border-radius:.75rem;padding:.75rem 1rem;display:flex;align-items:flex-start;gap:.75rem;font-size:.78rem; }
    .sig-ico { width:2rem;height:2rem;background:#e0e7ff;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .sig-ico svg { width:1rem;height:1rem; }

    .chip { display:inline-flex;align-items:center;gap:.4rem;background:#f3f4f6;border-radius:9999px;padding:.25rem .7rem;font-size:.75rem;font-weight:500;color:#374151; }
    .chip button { border:none;background:none;cursor:pointer;color:#9ca3af;line-height:1;padding:0; }
    .chip button:hover { color:#374151; }

    /* ── FACTURE IMPRESSION ── */
    #invoice-print-area { display: none; }

    @media print {
        body * { visibility: hidden !important; }
        #invoice-print-area, #invoice-print-area * { visibility: visible !important; }
        #invoice-print-area {
            display: block !important;
            position: fixed;
            inset: 0;
            background: white;
            z-index: 9999;
            padding: 0;
        }
    }

    /* ── FACTURE STYLE ── */
    .invoice-wrap {
        font-family: 'DM Sans', sans-serif;
        max-width: 720px;
        margin: 0 auto;
        padding: 40px;
        background: #fff;
        color: #1a1a2e;
    }
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 3px solid #111827;
        padding-bottom: 24px;
        margin-bottom: 28px;
    }
    .invoice-logo-block {}
    .invoice-school-name {
        font-size: 20px;
        font-weight: 800;
        color: #111827;
        margin: 0 0 4px;
        letter-spacing: -.5px;
    }
    .invoice-school-sub {
        font-size: 11px;
        color: #6b7280;
        margin: 0;
        line-height: 1.6;
    }
    .invoice-meta {
        text-align: right;
    }
    .invoice-title {
        font-size: 28px;
        font-weight: 900;
        color: #111827;
        letter-spacing: -1px;
        margin: 0 0 6px;
    }
    .invoice-num {
        font-family: 'DM Mono', monospace;
        font-size: 13px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 3px 10px;
        border-radius: 6px;
        display: inline-block;
        margin-bottom: 4px;
    }
    .invoice-date {
        font-size: 11px;
        color: #9ca3af;
        display: block;
        margin-top: 4px;
    }

    .invoice-parties {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }
    .invoice-party-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
    }
    .invoice-party-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #9ca3af;
        margin-bottom: 8px;
    }
    .invoice-party-name {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 3px;
    }
    .invoice-party-detail {
        font-size: 12px;
        color: #6b7280;
        line-height: 1.6;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }
    .invoice-table thead th {
        padding: 10px 14px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #9ca3af;
        border-bottom: 2px solid #e5e7eb;
        background: #f9fafb;
    }
    .invoice-table thead th:last-child { text-align: right; }
    .invoice-table tbody td {
        padding: 12px 14px;
        font-size: 13px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }
    .invoice-table tbody td:last-child { text-align: right; font-weight: 600; }
    .invoice-table tbody tr:last-child td { border-bottom: none; }

    .invoice-totals {
        margin-left: auto;
        width: 280px;
        margin-bottom: 28px;
    }
    .invoice-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 13px;
        color: #6b7280;
        border-bottom: 1px solid #f3f4f6;
    }
    .invoice-total-row:last-child {
        border-bottom: none;
        font-size: 16px;
        font-weight: 800;
        color: #111827;
        padding-top: 10px;
        border-top: 2px solid #111827;
        margin-top: 4px;
    }
    .invoice-total-row .val { font-weight: 600; color: #111827; }
    .invoice-total-row.paid .val { color: #16a34a; }
    .invoice-total-row.rest .val { color: #dc2626; }

    .invoice-status-banner {
        text-align: center;
        padding: 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 24px;
        letter-spacing: .02em;
    }
    .invoice-status-banner.paid   { background: #dcfce7; color: #15803d; border: 2px solid #bbf7d0; }
    .invoice-status-banner.partial{ background: #fef3c7; color: #b45309; border: 2px solid #fde68a; }
    .invoice-status-banner.unpaid { background: #fee2e2; color: #b91c1c; border: 2px solid #fecaca; }

    .invoice-footer {
        border-top: 1px solid #e5e7eb;
        padding-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .invoice-footer-note {
        font-size: 11px;
        color: #9ca3af;
        line-height: 1.6;
        max-width: 340px;
    }
    .invoice-signature-block {
        text-align: center;
    }
    .invoice-signature-line {
        width: 160px;
        border-top: 1.5px solid #d1d5db;
        margin-bottom: 6px;
    }
    .invoice-signature-label {
        font-size: 10px;
        color: #9ca3af;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .invoice-signature-name {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-top: 2px;
    }

    .invoice-watermark-paid {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 80px;
        font-weight: 900;
        color: rgba(22, 163, 74, 0.08);
        letter-spacing: 8px;
        pointer-events: none;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- ── HEADER ── --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Suivi financier</h1>
            <p class="text-sm text-gray-400 mt-0.5">Scolarités &amp; paiements · {{ $institution->name }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('admin.financial') }}" id="anneeForm" class="flex items-center gap-1.5">
                @foreach(request()->except('annee') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <label class="text-xs text-gray-500 font-medium">Année :</label>
                <select name="annee" onchange="document.getElementById('anneeForm').submit()" class="fi" style="width:auto;padding:.4rem .7rem;font-size:.8rem">
                    @foreach($anneesDispos as $a)
                        <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.financial.export', array_merge(request()->query(), ['annee' => $annee])) }}"
               class="btn btn-ghost text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter CSV
            </a>
            <button onclick="openPayModal()" class="btn btn-success text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau paiement
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm" id="successAlert">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="flex-1">{{ session('success') }}</span>
        @if(session('last_record_id'))
        <button onclick="printInvoiceFromSession()" class="btn btn-ghost text-xs border-green-300 text-green-700 hover:bg-green-100">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimer le reçu
        </button>
        @endif
        <button class="text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- ── STATS ── --}}
    @php
        $totalDu    = $statsAnnee->total_du    ?? 0;
        $totalPaye  = $statsAnnee->total_paye  ?? 0;
        $totalReste = $statsAnnee->total_reste ?? 0;
        $tauxColl   = $totalDu > 0 ? round(($totalPaye / $totalDu) * 100) : 0;
        $barClass   = $tauxColl >= 80 ? 'green' : ($tauxColl >= 50 ? 'amber' : 'red');
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card blue">
            <span class="stat-label">Total attendu</span>
            <span class="stat-value">{{ number_format($totalDu, 0, ',', ' ') }}</span>
            <span class="stat-sub">FCFA · {{ $annee }}</span>
        </div>
        <div class="stat-card green">
            <span class="stat-label">Encaissé</span>
            <span class="stat-value">{{ number_format($totalPaye, 0, ',', ' ') }}</span>
            <span class="stat-sub">{{ $statsAnnee->nb_payes ?? 0 }} paiements complets</span>
        </div>
        <div class="stat-card red">
            <span class="stat-label">Reste à percevoir</span>
            <span class="stat-value">{{ number_format($totalReste, 0, ',', ' ') }}</span>
            <span class="stat-sub">{{ $statsAnnee->nb_impayes ?? 0 }} impayés · {{ $statsAnnee->nb_partiels ?? 0 }} partiels</span>
        </div>
        <div class="stat-card amber">
            <span class="stat-label">Taux de collecte</span>
            <span class="stat-value">{{ $tauxColl }}%</span>
            <div class="prog-bar mt-2">
                <div class="prog-fill {{ $barClass }}" style="width:{{ $tauxColl }}%"></div>
            </div>
        </div>
    </div>

    {{-- ── GRAPHIQUE + TRANSACTIONS ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">Collecte mensuelle</h3>
                    <p class="text-xs text-gray-400">Attendu vs Encaissé</p>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-400">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-gray-200 inline-block"></span>Attendu</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-green-500 inline-block"></span>Encaissé</span>
                </div>
            </div>
            @php $maxVal = $statsMois->max(fn($m) => max($m->du, $m->paye)) ?: 1; @endphp
            <div class="chart-wrap">
                @forelse($statsMois as $m)
                @php
                    $hDu  = max(3, round(($m->du  / $maxVal) * 96));
                    $hPay = max(3, round(($m->paye / $maxVal) * 96));
                @endphp
                <div class="chart-col">
                    <div class="chart-bars">
                        <div class="chart-bar du"  style="height:{{ $hDu }}px"></div>
                        <div class="chart-bar pay" style="height:{{ $hPay }}px"></div>
                    </div>
                    <span class="chart-lbl">{{ substr($m->mois_label, 0, 3) }}</span>
                </div>
                @empty
                <div class="flex-1 flex items-center justify-center text-xs text-gray-300">Aucune donnée</div>
                @endforelse
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h3 class="font-semibold text-gray-900 text-sm mb-3">Derniers paiements</h3>
            <div class="space-y-2.5">
                @forelse($recentPaiements as $p)
                <div class="flex items-center gap-2.5">
                    <div class="avatar bg-green-100 text-green-700">{{ strtoupper(substr($p->apprenant->nom ?? 'AP', 0, 2)) }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $p->apprenant->prenom ?? '' }} {{ $p->apprenant->nom ?? '' }}</p>
                        <p class="text-xs text-gray-400">{{ $p->mois_label }} · {{ $p->date_paiement?->format('d/m') }}</p>
                    </div>
                    <span class="text-xs font-bold text-green-600 shrink-0">+{{ number_format($p->montant_paye, 0, ',', ' ') }}</span>
                </div>
                @empty
                <p class="text-xs text-gray-300 text-center py-6">Aucune transaction</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── LISTE APPRENANTS ── --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <form method="GET" action="{{ route('admin.financial') }}" id="filterForm">
                <input type="hidden" name="annee" value="{{ $annee }}">
                <div class="filter-grid">
                    <div>
                        <label class="fi-label">Rechercher</label>
                        <div class="relative">
                            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Nom, prénom, matricule…" class="fi pl-8">
                        </div>
                    </div>
                    <div>
                        <label class="fi-label">Classe</label>
                        <select name="classe_id" class="fi">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $cl)
                                <option value="{{ $cl->id }}" {{ request('classe_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="fi-label">Niveau</label>
                        <select name="niveau_id" class="fi">
                            <option value="">Tous les niveaux</option>
                            @foreach($niveaux as $nv)
                                <option value="{{ $nv->id }}" {{ request('niveau_id') == $nv->id ? 'selected' : '' }}>{{ $nv->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="fi-label">Filière</label>
                        <select name="filiere_id" class="fi">
                            <option value="">Toutes filières</option>
                            @foreach($filieres as $fi)
                                <option value="{{ $fi->id }}" {{ request('filiere_id') == $fi->id ? 'selected' : '' }}>{{ $fi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="fi-label">Statut</label>
                        <select name="statut" class="fi">
                            <option value="">Tous</option>
                            <option value="paye"    {{ $statut==='paye'    ? 'selected' : '' }}>Payé</option>
                            <option value="partiel" {{ $statut==='partiel' ? 'selected' : '' }}>Partiel</option>
                            <option value="impaye"  {{ $statut==='impaye'  ? 'selected' : '' }}>Impayé</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-1.5">
                        <button type="submit" class="btn btn-dark text-xs flex-1">Filtrer</button>
                        @if($search || $statut || request('classe_id') || request('niveau_id') || request('filiere_id'))
                        <a href="{{ route('admin.financial', ['annee' => $annee]) }}" class="btn btn-ghost text-xs" title="Effacer filtres">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="px-5 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-500 font-medium">
                {{ $apprenants->total() }} apprenant{{ $apprenants->total() > 1 ? 's' : '' }} trouvé{{ $apprenants->total() > 1 ? 's' : '' }}
            </span>
            <span class="text-xs text-gray-400">Cliquez sur un badge mois pour saisir/modifier un paiement</span>
        </div>

        <div class="overflow-x-auto">
            <table class="fin-table">
                <thead>
                    <tr>
                        <th>Apprenant</th>
                        <th>Classe / Filière</th>
                        @foreach($moisLabels as $n => $label)
                            <th class="center">{{ substr($label, 0, 3) }}</th>
                        @endforeach
                        <th style="text-align:right">Dû</th>
                        <th style="text-align:right">Payé</th>
                        <th style="text-align:right">Reste</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apprenants as $a)
                    @php
                        $recsByMois = $a->financialRecords->keyBy('mois');
                        $totalDuA   = $a->financialRecords->sum('montant_du');
                        $totalPayeA = $a->financialRecords->sum('montant_paye');
                        $totalRestA = $a->financialRecords->sum('montant_reste');
                        $colors = ['bg-indigo-100 text-indigo-700','bg-violet-100 text-violet-700','bg-sky-100 text-sky-700','bg-teal-100 text-teal-700','bg-rose-100 text-rose-700'];
                        $avatarColor = $colors[$a->id % count($colors)];
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="avatar {{ $avatarColor }}">{{ strtoupper(substr($a->nom, 0, 2)) }}</div>
                                <div>
                                    <p class="font-semibold text-xs text-gray-900 leading-tight">{{ $a->prenom }} {{ $a->nom }}</p>
                                    <p class="text-xs text-gray-400 mono">{{ $a->matricule }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-medium text-gray-700">{{ $a->classe->name ?? '—' }}</p>
                            @if($a->filiere)
                                <p class="text-xs text-gray-400">{{ $a->filiere->name }}</p>
                            @elseif($a->niveau)
                                <p class="text-xs text-gray-400">{{ $a->niveau->name }}</p>
                            @endif
                        </td>

                        @foreach($moisLabels as $mNum => $mLabel)
                        @php $rec = $recsByMois[$mNum] ?? null; @endphp
                        <td class="text-center">
                            @if($rec)
                                @php
                                    $bc = $rec->statut === 'paye' ? 'green' : ($rec->statut === 'partiel' ? 'amber' : 'red');
                                    $sym = $rec->statut === 'paye' ? '✓' : ($rec->statut === 'partiel' ? '~' : '✗');
                                @endphp
                                <button class="badge {{ $bc }}"
                                        style="cursor:pointer;border:none"
                                        onclick="openPayModal(
                                            {{ $a->id }},
                                            '{{ addslashes($a->prenom) }} {{ addslashes($a->nom) }}',
                                            '{{ addslashes($a->matricule) }}',
                                            '{{ addslashes($a->classe->name ?? '') }}',
                                            {{ $mNum }},
                                            '{{ $mLabel }}',
                                            {{ $rec->montant_du }},
                                            {{ $rec->montant_paye }},
                                            '{{ $rec->mode_paiement ?? '' }}',
                                            '{{ addslashes($rec->reference ?? '') }}',
                                            '{{ $rec->date_paiement?->format('Y-m-d') ?? '' }}'
                                        )"
                                        title="{{ $mLabel }} : {{ number_format($rec->montant_paye,0,',',' ') }} / {{ number_format($rec->montant_du,0,',',' ') }} FCFA">
                                    <span class="badge-dot"></span>{{ $sym }}
                                </button>
                            @else
                                <button class="w-6 h-6 rounded text-gray-300 hover:bg-gray-100 hover:text-gray-500 transition border border-dashed border-gray-200 text-xs font-bold"
                                        onclick="openPayModal(
                                            {{ $a->id }},
                                            '{{ addslashes($a->prenom) }} {{ addslashes($a->nom) }}',
                                            '{{ addslashes($a->matricule) }}',
                                            '{{ addslashes($a->classe->name ?? '') }}',
                                            {{ $mNum }},
                                            '{{ $mLabel }}'
                                        )"
                                        title="Ajouter {{ $mLabel }}">+</button>
                            @endif
                        </td>
                        @endforeach

                        <td class="text-right text-xs font-medium text-gray-700">{{ number_format($totalDuA, 0, ',', ' ') }}</td>
                        <td class="text-right text-xs font-bold text-green-700">{{ number_format($totalPayeA, 0, ',', ' ') }}</td>
                        <td class="text-right text-xs font-bold {{ $totalRestA > 0 ? 'text-red-600' : 'text-gray-300' }}">
                            {{ $totalRestA > 0 ? number_format($totalRestA, 0, ',', ' ') : '—' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.financial.apprenant', $a->id) }}" class="btn-icon" title="Archives et détail">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 4 + count($moisLabels) }}" class="text-center py-14">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm">Aucun apprenant correspondant</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($apprenants->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $apprenants->links() }}
        </div>
        @endif
    </div>

</div>


{{-- ══════════════ MODAL PAIEMENT ══════════════ --}}
<div class="modal-bd" id="payModal">
<div class="modal-box">
    <div class="modal-head">
        <div>
            <h2 class="text-sm font-bold text-gray-900">Enregistrer un paiement</h2>
            <p class="text-xs text-gray-400 mt-0.5" id="payModalSub">—</p>
        </div>
        <button onclick="closeModal('payModal')" class="btn-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <form id="payForm" action="{{ route('admin.financial.store') }}" method="POST">
        @csrf
        {{-- Champs hidden portant les vraies valeurs soumises --}}
        <input type="hidden" name="annee_academique" value="{{ $annee }}">
        <input type="hidden" name="apprenant_id"     id="hiddenAppId">
        <input type="hidden" name="mois"             id="hiddenMois">

        <div class="modal-body space-y-4">

            {{-- Sélection apprenant (visible quand pas de contexte) --}}
            <div id="pAppSelectWrap">
                <label class="fi-label">Apprenant <span class="text-red-500">*</span></label>
                <select id="pAppSelect" class="fi">
                    <option value="">Choisir un apprenant…</option>
                    @foreach($apprenants as $a)
                    <option value="{{ $a->id }}"
                            data-nom="{{ $a->prenom }} {{ $a->nom }}"
                            data-matricule="{{ $a->matricule }}"
                            data-classe="{{ $a->classe->name ?? '' }}">
                        {{ $a->prenom }} {{ $a->nom }}
                        @if($a->matricule) ({{ $a->matricule }})@endif
                        @if($a->classe) — {{ $a->classe->name }}@endif
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Sélection mois (visible quand pas de contexte) --}}
            <div id="pMoisSelectWrap">
                <label class="fi-label">Mois <span class="text-red-500">*</span></label>
                <select id="pMoisSelect" class="fi">
                    <option value="">Choisir un mois…</option>
                    @foreach($moisLabels as $n => $label)
                    <option value="{{ $n }}" data-label="{{ $label }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Numéro de reçu auto --}}
            <div>
                <label class="fi-label">N° de reçu (généré automatiquement)</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="reference" id="pRef" class="fi mono" readonly
                           style="background:#f9fafb;color:#374151;font-size:.82rem;letter-spacing:.04em;">
                    <button type="button" onclick="generateReceiptNumber()" class="btn btn-ghost text-xs shrink-0" style="padding:.5rem .7rem" title="Régénérer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="fi-label">Montant dû (FCFA) <span class="text-red-500">*</span></label>
                    <input type="number" name="montant_du" id="pDu" class="fi" min="0" step="100" required placeholder="0" oninput="updateReste()">
                </div>
                <div>
                    <label class="fi-label">Montant payé (FCFA) <span class="text-red-500">*</span></label>
                    <input type="number" name="montant_paye" id="pPaye" class="fi" min="0" step="100" required placeholder="0" oninput="updateReste()">
                </div>
            </div>

            <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-2.5">
                <span class="text-xs text-gray-500">Reste :</span>
                <span id="pResteDisplay" class="font-bold text-sm text-red-600">0 FCFA</span>
                <span id="pStatutPreview" class="ml-auto text-xs font-semibold"></span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="fi-label">Date de paiement</label>
                    <input type="date" name="date_paiement" id="pDatePay" class="fi" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="fi-label">Mode</label>
                    <select name="mode_paiement" id="pMode" class="fi">
                        <option value="">—</option>
                        <option value="especes">Espèces</option>
                        <option value="virement">Virement</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="cheque">Chèque</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="fi-label">Notes / Observation</label>
                <textarea name="notes" rows="2" class="fi resize-none" placeholder="Observation…"></textarea>
            </div>

            <div class="sig">
                <div class="sig-ico">
                    <svg fill="none" stroke="#4338ca" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-xs text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ now()->format('d/m/Y à H:i') }}</p>
                    <p class="text-xs text-indigo-600 font-medium">Votre signature sera attachée à ce reçu</p>
                </div>
            </div>
        </div>

        <div class="modal-foot">
            <button type="button" onclick="closeModal('payModal')" class="btn btn-ghost">Annuler</button>
            <button type="submit" class="btn btn-success" id="paySubmitBtn" onclick="storePaymentDataForPrint()">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </form>
</div>
</div>


{{-- ══════════════ ZONE FACTURE (IMPRESSION) ══════════════ --}}
<div id="invoice-print-area">
    <div class="invoice-wrap" style="position:relative;">
        <div id="invoice-watermark" class="invoice-watermark-paid"></div>

        {{-- En-tête --}}
        <div class="invoice-header">
            <div class="invoice-logo-block">
                @if($institution->logo)
                <img src="{{ Storage::url($institution->logo) }}" alt="Logo" style="height:48px;object-fit:contain;margin-bottom:8px;display:block;">
                @endif
                <h1 class="invoice-school-name">{{ $institution->name }}</h1>
                <p class="invoice-school-sub">
                    {{ $institution->adresse ?? '' }}<br>
                    {{ $institution->telephone ?? '' }}
                    @if($institution->email) · {{ $institution->email }} @endif
                </p>
            </div>
            <div class="invoice-meta">
                <div class="invoice-title">REÇU</div>
                <div class="invoice-num" id="inv-num">REC-—</div>
                <div class="invoice-date" id="inv-date">—</div>
            </div>
        </div>

        {{-- Parties --}}
        <div class="invoice-parties">
            <div class="invoice-party-box">
                <div class="invoice-party-label">Établissement émetteur</div>
                <div class="invoice-party-name">{{ $institution->name }}</div>
                <div class="invoice-party-detail">
                    Année académique : <strong id="inv-annee">{{ $annee }}</strong><br>
                    Émis par : <strong>{{ Auth::user()->name }}</strong>
                </div>
            </div>
            <div class="invoice-party-box">
                <div class="invoice-party-label">Apprenant</div>
                <div class="invoice-party-name" id="inv-apprenant-name">—</div>
                <div class="invoice-party-detail">
                    Matricule : <strong id="inv-matricule">—</strong><br>
                    Classe : <strong id="inv-classe">—</strong>
                </div>
            </div>
        </div>

        {{-- Tableau --}}
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Période</th>
                    <th>Mode de paiement</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Frais de scolarité</td>
                    <td id="inv-mois">—</td>
                    <td id="inv-mode">—</td>
                    <td id="inv-paye">0 FCFA</td>
                </tr>
            </tbody>
        </table>

        {{-- Totaux --}}
        <div class="invoice-totals">
            <div class="invoice-total-row">
                <span>Total dû</span>
                <span class="val" id="inv-du">0 FCFA</span>
            </div>
            <div class="invoice-total-row paid">
                <span>Montant encaissé</span>
                <span class="val" id="inv-paye2">0 FCFA</span>
            </div>
            <div class="invoice-total-row rest" id="inv-reste-row">
                <span>Reste à payer</span>
                <span class="val" id="inv-reste">0 FCFA</span>
            </div>
            <div class="invoice-total-row">
                <span>Statut</span>
                <span class="val" id="inv-statut">—</span>
            </div>
        </div>

        {{-- Bannière statut --}}
        <div class="invoice-status-banner" id="inv-status-banner">—</div>

        {{-- Pied --}}
        <div class="invoice-footer">
            <div class="invoice-footer-note">
                Ce reçu est émis par <strong>{{ $institution->name }}</strong>.<br>
                Conservez ce document comme preuve de paiement.<br>
                Pour toute réclamation, contactez l'administration de l'établissement.
            </div>
            <div class="invoice-signature-block">
                <div class="invoice-signature-line"></div>
                <div class="invoice-signature-label">Signature &amp; Cachet</div>
                <div class="invoice-signature-name">{{ Auth::user()->name }}</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── État courant du modal ──
let currentPaymentData = {};

// ── Génération du numéro de reçu ──
function generateReceiptNumber() {
    const now = new Date();
    const yy  = String(now.getFullYear()).slice(2);
    const mm  = String(now.getMonth() + 1).padStart(2, '0');
    const dd  = String(now.getDate()).padStart(2, '0');
    const rnd = String(Math.floor(Math.random() * 9000) + 1000);
    const num = `REC-${yy}${mm}${dd}-${rnd}`;
    document.getElementById('pRef').value = num;
    return num;
}

// ── Ouvrir/Fermer modal ──
function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }

document.querySelectorAll('.modal-bd').forEach(el =>
    el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); })
);
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-bd.open').forEach(m => closeModal(m.id));
});

// ── Ouvrir le modal de paiement ──
// Signature :
//   openPayModal()                                   → nouveau paiement (selects visibles)
//   openPayModal(appId, appName, matricule, classe, mois, moisLabel, du, paye, mode, ref, datePay)
function openPayModal(appId, appName, matricule, classe, mois, moisLabel, du, paye, mode, ref, datePay) {
    const hasCtx = (appId !== undefined && appId !== null && appId !== '');

    // Gestion affichage selects
    const appWrap  = document.getElementById('pAppSelectWrap');
    const moisWrap = document.getElementById('pMoisSelectWrap');
    appWrap.style.display  = hasCtx ? 'none' : '';
    moisWrap.style.display = hasCtx ? 'none' : '';

    if (hasCtx) {
        // Contexte : on sait qui et quel mois
        document.getElementById('hiddenAppId').value = appId;
        document.getElementById('hiddenMois').value  = mois;
        document.getElementById('payModalSub').textContent = `${appName} — ${moisLabel}`;

        // Stocker pour la facture
        currentPaymentData.appId    = appId;
        currentPaymentData.appName  = appName;
        currentPaymentData.matricule= matricule || '';
        currentPaymentData.classe   = classe    || '';
        currentPaymentData.mois     = mois;
        currentPaymentData.moisLabel= moisLabel;
    } else {
        // Sans contexte : les selects sont visibles, on les lie aux hiddens
        document.getElementById('hiddenAppId').value = '';
        document.getElementById('hiddenMois').value  = '';
        document.getElementById('payModalSub').textContent = 'Sélectionnez l\'apprenant et le mois';
        currentPaymentData = {};

        // Synchronisation select → hidden apprenant
        const selApp = document.getElementById('pAppSelect');
        selApp.onchange = function () {
            const opt = this.options[this.selectedIndex];
            document.getElementById('hiddenAppId').value = this.value;
            currentPaymentData.appId     = this.value;
            currentPaymentData.appName   = opt.dataset.nom  || '';
            currentPaymentData.matricule = opt.dataset.matricule || '';
            currentPaymentData.classe    = opt.dataset.classe    || '';
        };

        // Synchronisation select → hidden mois
        const selMois = document.getElementById('pMoisSelect');
        selMois.onchange = function () {
            const opt = this.options[this.selectedIndex];
            document.getElementById('hiddenMois').value = this.value;
            currentPaymentData.mois      = this.value;
            currentPaymentData.moisLabel = opt.dataset.label || '';
        };
    }

    // Remplir les champs communs
    document.getElementById('pDu').value      = (du    !== undefined && du    !== '') ? du    : '';
    document.getElementById('pPaye').value    = (paye  !== undefined && paye  !== '') ? paye  : '';
    document.getElementById('pMode').value    = mode  || '';
    document.getElementById('pDatePay').value = datePay || new Date().toISOString().split('T')[0];

    // Générer un numéro de reçu si pas déjà rempli ou si c'est une nouvelle saisie
    if (!ref || ref === '') {
        generateReceiptNumber();
    } else {
        document.getElementById('pRef').value = ref;
    }

    updateReste();
    openModal('payModal');
}

// ── Mise à jour du reste ──
function updateReste() {
    const du    = parseFloat(document.getElementById('pDu').value)   || 0;
    const paye  = parseFloat(document.getElementById('pPaye').value) || 0;
    const reste = Math.max(0, du - paye);
    const elR   = document.getElementById('pResteDisplay');
    const elS   = document.getElementById('pStatutPreview');

    elR.textContent = reste.toLocaleString('fr-FR') + ' FCFA';
    elR.className   = 'font-bold text-sm ' + (reste > 0 ? 'text-red-600' : 'text-green-600');

    if (paye >= du && du > 0) {
        elS.textContent = '✓ Soldé';
        elS.style.color = '#16a34a';
    } else if (paye > 0) {
        elS.textContent = '~ Partiel';
        elS.style.color = '#d97706';
    } else {
        elS.textContent = '✗ Impayé';
        elS.style.color = '#dc2626';
    }
}

// ── Avant soumission : stocker les données pour impression --
function storePaymentDataForPrint() {
    const du    = parseFloat(document.getElementById('pDu').value)   || 0;
    const paye  = parseFloat(document.getElementById('pPaye').value) || 0;
    const reste = Math.max(0, du - paye);
    const ref   = document.getElementById('pRef').value;
    const mode  = document.getElementById('pMode').value;
    const date  = document.getElementById('pDatePay').value;

    // Stocker dans localStorage pour récupérer après rechargement
    const data = {
        ref,
        du, paye, reste,
        mode,
        date,
        appName:   currentPaymentData.appName   || '',
        matricule: currentPaymentData.matricule  || '',
        classe:    currentPaymentData.classe     || '',
        moisLabel: currentPaymentData.moisLabel  || '',
        annee:     '{{ $annee }}',
        emetteur:  '{{ Auth::user()->name }}',
        institution: '{{ $institution->name }}',
        printAfter: true,
    };

    try { localStorage.setItem('lastInvoice', JSON.stringify(data)); } catch(e) {}
}

// ── Construire et imprimer la facture ──
function printInvoice(data) {
    if (!data) return;

    const fmt = n => n.toLocaleString('fr-FR') + ' FCFA';
    const statut = data.paye >= data.du && data.du > 0 ? 'Soldé'
                 : data.paye > 0 ? 'Partiel' : 'Impayé';
    const statClass = statut === 'Soldé' ? 'paid' : statut === 'Partiel' ? 'partial' : 'unpaid';
    const statMsg   = statut === 'Soldé' ? '✓ PAIEMENT COMPLET — SOLDÉ'
                    : statut === 'Partiel' ? '⚠ PAIEMENT PARTIEL — RESTE À PAYER'
                    : '✗ AUCUN PAIEMENT ENREGISTRÉ';
    const modeLabels = {
        especes:'Espèces', virement:'Virement bancaire',
        mobile_money:'Mobile Money', cheque:'Chèque', autre:'Autre', '':'—'
    };

    document.getElementById('inv-num').textContent = data.ref || '—';
    document.getElementById('inv-date').textContent = data.date ? new Date(data.date).toLocaleDateString('fr-FR', {day:'2-digit',month:'long',year:'numeric'}) : '—';
    document.getElementById('inv-apprenant-name').textContent = data.appName || '—';
    document.getElementById('inv-matricule').textContent = data.matricule || '—';
    document.getElementById('inv-classe').textContent = data.classe || '—';
    document.getElementById('inv-mois').textContent = data.moisLabel || '—';
    document.getElementById('inv-mode').textContent = modeLabels[data.mode] || data.mode || '—';
    document.getElementById('inv-paye').textContent  = fmt(data.paye || 0);
    document.getElementById('inv-du').textContent    = fmt(data.du   || 0);
    document.getElementById('inv-paye2').textContent = fmt(data.paye || 0);
    document.getElementById('inv-reste').textContent = fmt(data.reste || 0);
    document.getElementById('inv-statut').textContent= statut;
    document.getElementById('inv-annee').textContent = data.annee || '—';

    // Ligne reste
    const resteRow = document.getElementById('inv-reste-row');
    resteRow.style.display = (data.reste > 0) ? 'flex' : 'none';

    // Bannière statut
    const banner = document.getElementById('inv-status-banner');
    banner.textContent = statMsg;
    banner.className = `invoice-status-banner ${statClass}`;

    // Filigrane
    const wm = document.getElementById('invoice-watermark');
    wm.textContent = statut === 'Soldé' ? 'PAYÉ' : '';
    wm.style.color = statut === 'Soldé' ? 'rgba(22,163,74,0.08)' : 'transparent';

    window.print();
}

// ── Impression depuis le bouton dans l'alerte de succès ──
function printInvoiceFromSession() {
    try {
        const data = JSON.parse(localStorage.getItem('lastInvoice') || '{}');
        if (data.ref) printInvoice(data);
        else alert('Aucune donnée de reçu disponible.');
    } catch(e) { alert('Impossible de récupérer les données du reçu.'); }
}

// ── Au chargement : vérifier si une impression est en attente ──
window.addEventListener('load', function () {
    try {
        const data = JSON.parse(localStorage.getItem('lastInvoice') || '{}');
        if (data && data.printAfter) {
            data.printAfter = false;
            localStorage.setItem('lastInvoice', JSON.stringify(data));
            // Petit délai pour laisser la page se charger
            setTimeout(() => printInvoice(data), 600);
        }
    } catch(e) {}
});

// ── Supprimer un filtre via chip ──
function removeFilter(name) {
    const url = new URL(window.location.href);
    url.searchParams.delete(name);
    window.location.href = url.toString();
}
</script>
@endpush