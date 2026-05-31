@extends('admin.master')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');
    :root {
        --c-green:#16a34a; --c-green-lt:#dcfce7; --c-green-dk:#15803d;
        --c-amber:#d97706; --c-amber-lt:#fef3c7;
        --c-red:#dc2626;   --c-red-lt:#fee2e2;
        --c-blue:#2563eb;  --c-border:#e5e7eb;
    }
    * { font-family:'DM Sans',sans-serif; box-sizing:border-box; }
    .mono { font-family:'DM Mono',monospace; }

    .year-tabs { display:flex; gap:.5rem; flex-wrap:wrap; }
    .year-tab { padding:.4rem .9rem; border-radius:.5rem; font-size:.8rem; font-weight:600; border:1.5px solid var(--c-border); background:#fff; color:#6b7280; cursor:pointer; transition:all .15s; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; }
    .year-tab:hover { border-color:#9ca3af; color:#374151; }
    .year-tab.active { background:#111827; color:#fff; border-color:#111827; }
    .year-tab .yr-badge { font-size:.65rem; background:rgba(255,255,255,.2); padding:.1rem .4rem; border-radius:9999px; font-weight:500; }
    .year-tab:not(.active) .yr-badge { background:#f3f4f6; color:#9ca3af; }

    .stat-card { background:#fff; border:1px solid var(--c-border); border-radius:.875rem; padding:1.1rem 1.25rem; position:relative; overflow:hidden; }
    .stat-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; }
    .stat-card.blue::before  { background:var(--c-blue); }
    .stat-card.green::before { background:var(--c-green); }
    .stat-card.red::before   { background:var(--c-red); }
    .stat-label { font-size:.68rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.07em; display:block; }
    .stat-value { font-size:1.45rem; font-weight:700; color:#111827; line-height:1.1; display:block; margin:.3rem 0 .2rem; }

    .mois-grid { display:flex; flex-direction:column; }
    .mois-row { display:grid; grid-template-columns:90px 1fr; border-bottom:1px solid #f3f4f6; }
    .mois-row:last-child { border-bottom:none; }
    .mois-head { padding:.875rem 1rem; background:#fafafa; border-right:1px solid #f3f4f6; display:flex; flex-direction:column; justify-content:center; }
    .mois-head .mn { font-size:.8rem; font-weight:700; color:#374151; }
    .mois-head .mx { font-size:.68rem; color:#d1d5db; }
    .mois-body { padding:.875rem 1.25rem; }

    .badge { display:inline-flex; align-items:center; gap:.3rem; padding:.18rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:600; }
    .badge-dot { width:.45rem; height:.45rem; border-radius:50%; display:inline-block; }
    .badge.green { background:var(--c-green-lt); color:#15803d; } .badge.green .badge-dot { background:var(--c-green); }
    .badge.amber { background:var(--c-amber-lt); color:#b45309; } .badge.amber .badge-dot { background:var(--c-amber); }
    .badge.red   { background:var(--c-red-lt);   color:#b91c1c; } .badge.red   .badge-dot { background:var(--c-red); }

    .prog-bar  { height:5px; background:#f3f4f6; border-radius:9999px; overflow:hidden; }
    .prog-fill { height:100%; border-radius:9999px; transition:width .4s; }
    .prog-fill.green { background:var(--c-green); }
    .prog-fill.amber { background:var(--c-amber); }
    .prog-fill.red   { background:var(--c-red);   }

    .sig { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem; padding:.65rem .9rem; display:flex; align-items:flex-start; gap:.6rem; font-size:.75rem; }
    .sig-ico { width:1.75rem; height:1.75rem; background:#e0e7ff; border-radius:.375rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .sig-ico.ok { background:#dcfce7; }
    .sig-ico svg { width:.875rem; height:.875rem; }

    .fi { width:100%; border:1px solid var(--c-border); border-radius:.5rem; padding:.5rem .875rem; font-size:.825rem; color:#1f2937; background:#fff; font-family:'DM Sans',sans-serif; transition:border-color .15s; }
    .fi:focus { outline:none; border-color:#9ca3af; box-shadow:0 0 0 3px rgba(156,163,175,.15); }
    .fi-label { display:block; font-size:.72rem; font-weight:600; color:#6b7280; margin-bottom:.3rem; text-transform:uppercase; letter-spacing:.04em; }

    .btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:.5rem; font-size:.8rem; font-weight:500; cursor:pointer; transition:all .15s; border:none; padding:.5rem .95rem; white-space:nowrap; font-family:'DM Sans',sans-serif; }
    .btn-dark    { background:#111827; color:#fff; }
    .btn-dark:hover  { background:#374151; }
    .btn-success { background:var(--c-green); color:#fff; }
    .btn-success:hover { background:var(--c-green-dk); }
    .btn-ghost   { background:#fff; color:#374151; border:1px solid var(--c-border); }
    .btn-ghost:hover { background:#f9fafb; }
    .btn-icon { padding:.4rem; background:none; border:none; border-radius:.375rem; color:#9ca3af; cursor:pointer; display:flex; align-items:center; transition:background .15s,color .15s; }
    .btn-icon:hover { background:#f3f4f6; color:#374151; }
    .btn-icon.danger:hover { background:var(--c-red-lt); color:var(--c-red); }
    .btn-icon svg { width:1rem; height:1rem; }

    .modal-bd  { position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);z-index:300;display:none;align-items:center;justify-content:center;padding:1rem; }
    .modal-bd.open { display:flex; }
    .modal-box { background:#fff;border-radius:1.125rem;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;box-shadow:0 32px 72px rgba(0,0,0,.2);animation:mIn .2s ease; }
    @keyframes mIn { from{opacity:0;transform:translateY(14px) scale(.97)} to{opacity:1;transform:none} }
    .modal-head { display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f3f4f6; }
    .modal-body { padding:1.5rem; }
    .modal-foot { display:flex;justify-content:flex-end;gap:.75rem;padding:1rem 1.5rem;background:#f9fafb;border-top:1px solid #f3f4f6;border-radius:0 0 1.125rem 1.125rem; }

    .arch-table { width:100%; border-collapse:collapse; font-size:.8rem; }
    .arch-table th { padding:.5rem .875rem; text-align:left; font-size:.68rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; background:#f9fafb; border-bottom:1px solid var(--c-border); }
    .arch-table td { padding:.6rem .875rem; border-bottom:1px solid #f9fafb; vertical-align:middle; }
    .arch-table tbody tr:hover { background:#fdfefe; }
    .arch-table tbody tr:last-child td { border-bottom:none; }

    .timeline-dot { width:.5rem; height:.5rem; border-radius:50%; display:inline-block; flex-shrink:0; margin-top:.3rem; }

    /* ── PRINT ── */
/* ══════════════════════════════════════════
   IMPRESSION — BASE
══════════════════════════════════════════ */
#invoice-print-area { display: none; }

@media print {
    body * { visibility: hidden !important; }
    #invoice-print-area,
    #invoice-print-area * { visibility: visible !important; }
    #invoice-print-area {
        display: block !important;
        position: fixed;
        inset: 0;
        background: white;
        z-index: 9999;
        padding: 0;
        overflow: hidden;
    }

    /* Conteneur principal — toujours 100% de la page */
    .invoice-wrap,
    .inv-wrap {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        word-break: break-word !important;
        padding: 10mm !important;
    }

    /* Tableaux : ne jamais dépasser la largeur */
    .invoice-table,
    .inv-tbl {
        width: 100% !important;
        table-layout: fixed !important;
        word-break: break-word !important;
    }
    .invoice-table td,
    .invoice-table th,
    .inv-tbl td,
    .inv-tbl th {
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: normal !important;
        word-break: break-word !important;
    }

    /* Totaux : pleine largeur sur toutes tailles */
    .invoice-totals,
    .inv-totals {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Filigrane masqué par défaut sur ticket */
    .invoice-watermark-paid,
    .inv-watermark {
        display: none !important;
    }
}

/* ══════════════════════════════════════════
   FORMAT A4 — filigrane visible
══════════════════════════════════════════ */
@media print and (min-width: 81mm) {
    .invoice-watermark-paid,
    .inv-watermark {
        display: block !important;
    }
    .invoice-wrap,
    .inv-wrap {
        max-width: 720px !important;
        margin: 0 auto !important;
        padding: 10mm 15mm !important;
    }
}

/* ══════════════════════════════════════════
   FORMAT TICKET 80mm
══════════════════════════════════════════ */
@media print and (max-width: 80mm) {

    .invoice-wrap,
    .inv-wrap {
        padding: 4mm 3mm !important;
        font-size: 9pt !important;
    }

    /* En-tête : empilé verticalement */
    .invoice-header,
    .inv-hd {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 3px !important;
        border-bottom-width: 1px !important;
        padding-bottom: 5px !important;
        margin-bottom: 6px !important;
    }
    .invoice-meta,
    .inv-meta {
        text-align: left !important;
        width: 100% !important;
    }
    .invoice-title,
    .inv-title {
        font-size: 14pt !important;
        letter-spacing: 0 !important;
    }
    .invoice-school-name,
    .inv-school-name {
        font-size: 10pt !important;
        letter-spacing: 0 !important;
    }
    .invoice-school-sub,
    .inv-school-sub {
        font-size: 7pt !important;
        line-height: 1.3 !important;
    }
    .invoice-num,
    .inv-num {
        font-size: 8pt !important;
        padding: 1px 4px !important;
        display: block !important;
        width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .invoice-date,
    .inv-date {
        font-size: 7pt !important;
    }

    /* Logo masqué pour gagner de la place */
    .invoice-logo-block img,
    .inv-hd img {
        max-height: 20px !important;
        object-fit: contain !important;
    }

    /* Parties : colonne unique */
    .invoice-parties,
    .inv-parties {
        display: block !important;
        margin-bottom: 6px !important;
    }
    .invoice-party-box,
    .inv-party {
        padding: 4px 5px !important;
        border-radius: 3px !important;
        margin-bottom: 4px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .invoice-party-label,
    .inv-party-lbl {
        font-size: 6pt !important;
        margin-bottom: 3px !important;
    }
    .invoice-party-name,
    .inv-party-name {
        font-size: 9pt !important;
        margin-bottom: 1px !important;
        white-space: normal !important;
        word-break: break-word !important;
    }
    .invoice-party-detail,
    .inv-party-det {
        font-size: 7pt !important;
        line-height: 1.3 !important;
    }

    /* Tableau des lignes */
    .invoice-table,
    .inv-tbl {
        font-size: 8pt !important;
        margin-bottom: 5px !important;
    }
    .invoice-table thead th,
    .inv-tbl thead th {
        padding: 3px 4px !important;
        font-size: 6pt !important;
    }
    .invoice-table tbody td,
    .inv-tbl tbody td {
        padding: 3px 4px !important;
        font-size: 8pt !important;
    }

    /* Totaux */
    .invoice-totals,
    .inv-totals {
        margin-bottom: 6px !important;
    }
    .invoice-total-row,
    .inv-total-row {
        font-size: 8pt !important;
        padding: 2px 0 !important;
    }
    .invoice-total-row:last-child,
    .inv-total-row:last-child {
        font-size: 10pt !important;
        padding-top: 4px !important;
    }

    /* Bannière statut */
    .invoice-status-banner,
    .inv-banner {
        font-size: 8pt !important;
        padding: 4px 5px !important;
        margin-bottom: 6px !important;
        border-radius: 4px !important;
        border-width: 1px !important;
    }

    /* Pied de page */
    .invoice-footer,
    .inv-footer {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 5px !important;
        padding-top: 5px !important;
    }
    .invoice-footer-note,
    .inv-footer-note {
        font-size: 7pt !important;
        max-width: 100% !important;
        line-height: 1.3 !important;
    }
    .invoice-signature-block,
    .inv-sig-block {
        width: 100% !important;
    }
    .invoice-signature-line,
    .inv-sig-line {
        width: 100% !important;
        margin-bottom: 3px !important;
    }
    .invoice-signature-label,
    .inv-sig-lbl {
        font-size: 6pt !important;
    }
    .invoice-signature-name,
    .inv-sig-name {
        font-size: 8pt !important;
    }
}

/* ══════════════════════════════════════════
   FORMAT TICKET 58mm
══════════════════════════════════════════ */
@media print and (max-width: 58mm) {

    .invoice-wrap,
    .inv-wrap {
        padding: 3mm 2mm !important;
        font-size: 8pt !important;
    }

    /* En-tête ultra-compact */
    .invoice-school-name,
    .inv-school-name {
        font-size: 9pt !important;
    }
    .invoice-school-sub,
    .inv-school-sub {
        display: none !important;
    }
    .invoice-title,
    .inv-title {
        font-size: 13pt !important;
    }
    .invoice-num,
    .inv-num {
        font-size: 7pt !important;
    }

    /* Logo très compact */
    .invoice-logo-block img,
    .inv-hd img {
        max-height: 16px !important;
    }

    /* Parties encore plus compactes */
    .invoice-party-box,
    .inv-party {
        padding: 3px 4px !important;
        margin-bottom: 3px !important;
    }
    .invoice-party-detail,
    .inv-party-det {
        font-size: 6.5pt !important;
    }
    .invoice-party-name,
    .inv-party-name {
        font-size: 8pt !important;
    }

    /* Tableau : masquer l'entête, afficher en blocs */
    .invoice-table thead,
    .inv-tbl thead {
        display: none !important;
    }
    .invoice-table tbody tr,
    .inv-tbl tbody tr {
        display: block !important;
        border-bottom: 1px dashed #ccc !important;
        padding-bottom: 3px !important;
        margin-bottom: 3px !important;
    }
    .invoice-table tbody td,
    .inv-tbl tbody td {
        display: block !important;
        padding: 1px 3px !important;
        font-size: 7pt !important;
        border: none !important;
        text-align: left !important;
    }
    .invoice-table tbody td:last-child,
    .inv-tbl tbody td:last-child {
        font-size: 10pt !important;
        font-weight: 700 !important;
        text-align: left !important;
    }

    /* Totaux très serrés */
    .invoice-total-row,
    .inv-total-row {
        font-size: 7.5pt !important;
        padding: 1.5px 0 !important;
    }
    .invoice-total-row:last-child,
    .inv-total-row:last-child {
        font-size: 9.5pt !important;
    }

    /* Bannière */
    .invoice-status-banner,
    .inv-banner {
        font-size: 7pt !important;
        padding: 3px 4px !important;
        letter-spacing: 0 !important;
    }

    /* Pied : supprimer signature pour gagner de l'espace */
    .invoice-footer .invoice-signature-block,
    .inv-footer .inv-sig-block {
        display: none !important;
    }
    .invoice-footer-note,
    .inv-footer-note {
        font-size: 6.5pt !important;
    }
}
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

    /* ── FACTURE ── */
    .invoice-wrap { font-family: 'DM Sans', sans-serif; max-width: 720px; margin: 0 auto; padding: 40px; background: #fff; color: #1a1a2e; }
    .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #111827; padding-bottom: 24px; margin-bottom: 28px; }
    .invoice-school-name { font-size: 20px; font-weight: 800; color: #111827; margin: 0 0 4px; letter-spacing: -.5px; }
    .invoice-school-sub { font-size: 11px; color: #6b7280; margin: 0; line-height: 1.6; }
    .invoice-meta { text-align: right; }
    .invoice-title { font-size: 28px; font-weight: 900; color: #111827; letter-spacing: -1px; margin: 0 0 6px; }
    .invoice-num { font-family: 'DM Mono', monospace; font-size: 13px; color: #6b7280; background: #f3f4f6; padding: 3px 10px; border-radius: 6px; display: inline-block; margin-bottom: 4px; }
    .invoice-date { font-size: 11px; color: #9ca3af; display: block; margin-top: 4px; }
    .invoice-parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
    .invoice-party-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; }
    .invoice-party-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin-bottom: 8px; }
    .invoice-party-name { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 3px; }
    .invoice-party-detail { font-size: 12px; color: #6b7280; line-height: 1.6; }
    .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .invoice-table thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; border-bottom: 2px solid #e5e7eb; background: #f9fafb; }
    .invoice-table thead th:last-child { text-align: right; }
    .invoice-table tbody td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f3f4f6; color: #374151; }
    .invoice-table tbody td:last-child { text-align: right; font-weight: 600; }
    .invoice-totals { margin-left: auto; width: 280px; margin-bottom: 28px; }
    .invoice-total-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 13px; color: #6b7280; border-bottom: 1px solid #f3f4f6; }
    .invoice-total-row:last-child { border-bottom: none; font-size: 16px; font-weight: 800; color: #111827; padding-top: 10px; border-top: 2px solid #111827; margin-top: 4px; }
    .invoice-total-row .val { font-weight: 600; color: #111827; }
    .invoice-total-row.paid .val { color: #16a34a; }
    .invoice-total-row.rest .val { color: #dc2626; }
    .invoice-status-banner { text-align: center; padding: 14px; border-radius: 10px; font-size: 14px; font-weight: 700; margin-bottom: 24px; letter-spacing: .02em; }
    .invoice-status-banner.paid    { background: #dcfce7; color: #15803d; border: 2px solid #bbf7d0; }
    .invoice-status-banner.partial { background: #fef3c7; color: #b45309; border: 2px solid #fde68a; }
    .invoice-status-banner.unpaid  { background: #fee2e2; color: #b91c1c; border: 2px solid #fecaca; }
    .invoice-footer { border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
    .invoice-footer-note { font-size: 11px; color: #9ca3af; line-height: 1.6; max-width: 340px; }
    .invoice-signature-block { text-align: center; }
    .invoice-signature-line { width: 160px; border-top: 1.5px solid #d1d5db; margin-bottom: 6px; }
    .invoice-signature-label { font-size: 10px; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
    .invoice-signature-name { font-size: 12px; font-weight: 700; color: #374151; margin-top: 2px; }
    .invoice-watermark-paid { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 80px; font-weight: 900; color: rgba(22,163,74,.08); letter-spacing: 8px; pointer-events: none; white-space: nowrap; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- ── HEADER ── --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('admin.financial') }}" class="btn-icon mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-xl font-bold text-gray-900">{{ $apprenant->prenom }} {{ $apprenant->nom }}</h1>
                @if($apprenant->classe)
                    <span class="text-xs bg-indigo-100 text-indigo-700 font-semibold px-2.5 py-0.5 rounded-full">{{ $apprenant->classe->name }}</span>
                @endif
                @if($apprenant->filiere)
                    <span class="text-xs bg-violet-100 text-violet-700 font-semibold px-2.5 py-0.5 rounded-full">{{ $apprenant->filiere->name }}</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-0.5 mono">{{ $apprenant->matricule }}</p>
        </div>
        <button onclick="openPayModal()" class="btn btn-success text-xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter paiement
        </button>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="flex-1">{{ session('success') }}</span>
        <button onclick="printLastInvoice()" class="btn btn-ghost text-xs border-green-300 text-green-700 hover:bg-green-100">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimer le reçu
        </button>
    </div>
    @endif

    {{-- ── SÉLECTEUR D'ANNÉE ── --}}
    @if($anneesDispos->count() > 0)
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Archives par année académique</h3>
            <span class="text-xs text-gray-400">{{ $anneesDispos->count() }} année{{ $anneesDispos->count() > 1 ? 's' : '' }}</span>
        </div>
        <div class="year-tabs">
            @foreach($anneesDispos as $yr)
            @php
                $yrRecords = $allRecords->where('annee_academique', $yr);
                $yrPct = $yrRecords->sum('montant_du') > 0
                    ? round(($yrRecords->sum('montant_paye') / $yrRecords->sum('montant_du')) * 100) : 0;
            @endphp
            <a href="{{ route('admin.financial.apprenant', ['apprenant' => $apprenant->id, 'annee' => $yr]) }}"
               class="year-tab {{ $yr == $annee ? 'active' : '' }}">
                {{ $yr }}
                <span class="yr-badge">{{ $yrPct }}%</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── VUE D'ENSEMBLE ── --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
            <h3 class="font-semibold text-gray-900 text-sm">Récapitulatif toutes années</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="arch-table">
                <thead>
                    <tr>
                        <th>Année</th>
                        <th style="text-align:right">Total dû</th>
                        <th style="text-align:right">Payé</th>
                        <th style="text-align:right">Reste</th>
                        <th>Taux</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anneesDispos as $yr)
                    @php
                        $yrRec  = $allRecords->where('annee_academique', $yr);
                        $yrDu   = $yrRec->sum('montant_du');
                        $yrPay  = $yrRec->sum('montant_paye');
                        $yrRest = $yrRec->sum('montant_reste');
                        $yrPct  = $yrDu > 0 ? min(100, round(($yrPay / $yrDu) * 100)) : 0;
                        $yrBc   = $yrPct >= 100 ? 'green' : ($yrPct > 0 ? 'amber' : 'red');
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="timeline-dot {{ $yr == $annee ? 'bg-indigo-500' : 'bg-gray-300' }}"></div>
                                <span class="font-semibold text-gray-800 {{ $yr == $annee ? 'text-indigo-600' : '' }}">{{ $yr }}</span>
                                @if($yr == $annee)
                                    <span class="text-xs bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded font-medium">En cours</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-right font-medium text-gray-700">{{ $yrDu > 0 ? number_format($yrDu,0,',',' ').' F' : '—' }}</td>
                        <td class="text-right font-bold text-green-700">{{ $yrPay > 0 ? number_format($yrPay,0,',',' ').' F' : '—' }}</td>
                        <td class="text-right font-bold {{ $yrRest > 0 ? 'text-red-600' : 'text-gray-300' }}">{{ $yrRest > 0 ? number_format($yrRest,0,',',' ').' F' : '—' }}</td>
                        <td style="width:120px">
                            <div class="flex items-center gap-2">
                                <div class="prog-bar flex-1">
                                    <div class="prog-fill {{ $yrBc }}" style="width:{{ $yrPct }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 w-8 text-right shrink-0">{{ $yrPct }}%</span>
                            </div>
                        </td>
                        <td>
                            @if($yrPct >= 100) <span class="badge green"><span class="badge-dot"></span>Soldé</span>
                            @elseif($yrPct > 0) <span class="badge amber"><span class="badge-dot"></span>Partiel</span>
                            @elseif($yrDu > 0)  <span class="badge red"><span class="badge-dot"></span>Impayé</span>
                            @else <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.financial.apprenant', ['apprenant' => $apprenant->id, 'annee' => $yr]) }}" class="btn-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-8 text-gray-300 text-xs">Aucun enregistrement</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── DÉTAIL ANNÉE ── --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-gray-900">Détail — {{ $annee }}</h2>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="stat-card blue">
                <span class="stat-label">Total dû</span>
                <span class="stat-value">{{ number_format($totaux['du'], 0, ',', ' ') }}</span>
                <span class="text-xs text-gray-400">FCFA</span>
            </div>
            <div class="stat-card green">
                <span class="stat-label">Total payé</span>
                <span class="stat-value" style="color:var(--c-green)">{{ number_format($totaux['paye'], 0, ',', ' ') }}</span>
                <span class="text-xs text-gray-400">FCFA</span>
            </div>
            <div class="stat-card {{ $totaux['reste'] > 0 ? 'red' : '' }}">
                <span class="stat-label">Reste à payer</span>
                <span class="stat-value" style="color:{{ $totaux['reste'] > 0 ? 'var(--c-red)' : '#d1d5db' }}">
                    {{ $totaux['reste'] > 0 ? number_format($totaux['reste'], 0, ',', ' ') : '0' }}
                </span>
                <span class="text-xs text-gray-400">FCFA</span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-900">Mensualités · {{ $annee }}</span>
                <span class="text-xs text-gray-400">{{ $records->count() }} / 12 mois renseignés</span>
            </div>

            <div class="mois-grid">
                @foreach($moisLabels as $mNum => $mLabel)
                @php $rec = $records[$mNum] ?? null; @endphp
                <div class="mois-row">
                    <div class="mois-head">
                        <span class="mn">{{ substr($mLabel, 0, 3) }}.</span>
                        <span class="mx">M{{ $mNum }}</span>
                    </div>
                    <div class="mois-body">
                        @if($rec)
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="space-y-2 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @php $bc = $rec->statut === 'paye' ? 'green' : ($rec->statut === 'partiel' ? 'amber' : 'red'); @endphp
                                    <span class="badge {{ $bc }}"><span class="badge-dot"></span>{{ $rec->statut_label }}</span>
                                    @if($rec->mode_paiement)
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ ucfirst(str_replace('_',' ',$rec->mode_paiement)) }}</span>
                                    @endif
                                    @if($rec->reference)
                                        <span class="text-xs font-mono text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $rec->reference }}</span>
                                    @endif
                                </div>

                                @php
                                    $pct  = $rec->montant_du > 0 ? min(100, round(($rec->montant_paye / $rec->montant_du) * 100)) : 0;
                                    $barC = $pct >= 100 ? 'green' : ($pct > 0 ? 'amber' : 'red');
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="prog-bar flex-1"><div class="prog-fill {{ $barC }}" style="width:{{ $pct }}%"></div></div>
                                    <span class="text-xs text-gray-400 w-8 text-right shrink-0">{{ $pct }}%</span>
                                </div>

                                <div class="flex items-center gap-4 text-xs flex-wrap">
                                    <span class="text-gray-500">Dû : <strong class="text-gray-800">{{ number_format($rec->montant_du,0,',',' ') }} F</strong></span>
                                    <span class="text-gray-500">Payé : <strong class="text-green-700">{{ number_format($rec->montant_paye,0,',',' ') }} F</strong></span>
                                    @if($rec->montant_reste > 0)
                                        <span class="text-gray-500">Reste : <strong class="text-red-600">{{ number_format($rec->montant_reste,0,',',' ') }} F</strong></span>
                                    @endif
                                    @if($rec->date_paiement)
                                        <span class="text-gray-400">le {{ $rec->date_paiement->format('d/m/Y') }}</span>
                                    @endif
                                </div>

                                @if($rec->notes)
                                <p class="text-xs text-gray-400 italic">{{ $rec->notes }}</p>
                                @endif

                                <div class="flex flex-wrap gap-2 mt-1">
                                    @if($rec->recordedBy)
                                    <div class="sig">
                                        <div class="sig-ico">
                                            <svg fill="none" stroke="#4338ca" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $rec->recordedBy->name }}</p>
                                            <p class="text-gray-400">Enregistré{{ $rec->recorded_at ? ' le '.$rec->recorded_at->format('d/m/Y H:i') : '' }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($rec->validatedBy)
                                    <div class="sig" style="background:#f0fdf4;border-color:#bbf7d0">
                                        <div class="sig-ico ok">
                                            <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-green-800">{{ $rec->validatedBy->name }}</p>
                                            <p class="text-gray-400">Validé{{ $rec->validated_at ? ' le '.$rec->validated_at->format('d/m/Y H:i') : '' }}</p>
                                        </div>
                                    </div>
                                    @else
                                    <form action="{{ route('admin.financial.validate', $rec->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-1.5 hover:bg-indigo-100 transition font-medium">
                                            ✓ Valider &amp; signer
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1 shrink-0">
                                {{-- Bouton imprimer ce reçu --}}
                                <button class="btn-icon" title="Imprimer le reçu"
                                        onclick="printExistingRecord(
                                            '{{ addslashes($rec->reference ?? '') }}',
                                            {{ $rec->montant_du }},
                                            {{ $rec->montant_paye }},
                                            {{ $rec->montant_reste }},
                                            '{{ $rec->mode_paiement ?? '' }}',
                                            '{{ $rec->date_paiement?->format('Y-m-d') ?? '' }}',
                                            '{{ $mLabel }}'
                                        )">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                </button>
                                <button class="btn-icon" title="Modifier"
                                        onclick="openPayModal({{ $mNum }}, '{{ $mLabel }}', {{ $rec->montant_du }}, {{ $rec->montant_paye }}, '{{ $rec->mode_paiement ?? '' }}', '{{ addslashes($rec->reference ?? '') }}', '{{ $rec->date_paiement?->format('Y-m-d') ?? '' }}')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('admin.financial.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('Supprimer cet enregistrement ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon danger">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-300 italic">Non renseigné</span>
                            <button onclick="openPayModal({{ $mNum }}, '{{ $mLabel }}')"
                                    class="text-xs text-indigo-500 hover:text-indigo-700 font-semibold transition">
                                + Ajouter
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>


{{-- ══════════════ MODAL PAIEMENT ══════════════ --}}
<div class="modal-bd" id="payModal">
<div class="modal-box">
    <div class="modal-head">
        <div>
            <h2 class="text-sm font-bold text-gray-900">Paiement mensuel</h2>
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
        <input type="hidden" name="apprenant_id"     value="{{ $apprenant->id }}">
        <input type="hidden" name="annee_academique" value="{{ $annee }}">
        <input type="hidden" name="mois"             id="pMois">

        <div class="modal-body space-y-4">

            {{-- N° de reçu automatique --}}
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
                <label class="fi-label">Notes</label>
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
                    <p class="text-xs text-indigo-600 font-medium">Signature attachée automatiquement</p>
                </div>
            </div>
        </div>

        <div class="modal-foot">
            <button type="button" onclick="closeModal('payModal')" class="btn btn-ghost">Annuler</button>
            <button type="submit" class="btn btn-success" onclick="storeForPrint()">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer
            </button>
        </div>
    </form>
</div>
</div>


{{-- ══ ZONE FACTURE IMPRESSION ══ --}}
<div id="invoice-print-area">
    <div class="invoice-wrap" style="position:relative;">
        <div id="invoice-watermark" class="invoice-watermark-paid"></div>

        <div class="invoice-header">
            <div>
                @if($institution->logo)
                <img src="{{ $institution->logo_url }}" alt="Logo" style="height:48px;object-fit:contain;margin-bottom:8px;display:block;">
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
                <div class="invoice-num" id="inv-num">—</div>
                <div class="invoice-date" id="inv-date">—</div>
            </div>
        </div>

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
                <div class="invoice-party-name">{{ $apprenant->prenom }} {{ $apprenant->nom }}</div>
                <div class="invoice-party-detail">
                    Matricule : <strong>{{ $apprenant->matricule }}</strong><br>
                    Classe : <strong>{{ $apprenant->classe->name ?? '—' }}</strong>
                </div>
            </div>
        </div>

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

        <div class="invoice-status-banner" id="inv-status-banner">—</div>

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
// ── Infos de l'apprenant (injectées depuis PHP) ──
const APPRENANT = {
    id:        {{ $apprenant->id }},
    name:      '{{ addslashes($apprenant->prenom . ' ' . $apprenant->nom) }}',
    matricule: '{{ addslashes($apprenant->matricule ?? '') }}',
    classe:    '{{ addslashes($apprenant->classe->name ?? '') }}',
    annee:     '{{ $annee }}',
};

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

// ── Modal ──
function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
document.querySelectorAll('.modal-bd').forEach(el =>
    el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); })
);
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-bd.open').forEach(m => closeModal(m.id));
});

// openPayModal(mois, moisLabel, du, paye, mode, ref, datePay)
// ou openPayModal() → nouveau
function openPayModal(mois, moisLabel, du, paye, mode, ref, datePay) {
    const hasCtx = (mois !== undefined);

    document.getElementById('pMois').value     = hasCtx ? mois    : '';
    document.getElementById('pDu').value       = (du    !== undefined && du    !== '') ? du    : '';
    document.getElementById('pPaye').value     = (paye  !== undefined && paye  !== '') ? paye  : '';
    document.getElementById('pMode').value     = mode   || '';
    document.getElementById('pDatePay').value  = datePay || new Date().toISOString().split('T')[0];

    document.getElementById('payModalSub').textContent = hasCtx
        ? `${APPRENANT.name} — ${moisLabel} (${APPRENANT.annee})`
        : `${APPRENANT.name} — Choisissez le mois`;

    // Référence : régénérer si vide ou si nouveau
    if (!ref || ref === '') {
        generateReceiptNumber();
    } else {
        document.getElementById('pRef').value = ref;
    }

    // Si pas de contexte mois, on a besoin d'un select de mois → montrer une alerte ?
    // Non : on laisse simplement le champ hidden vide et on ajoute un select inline
    if (!hasCtx) {
        // Injecter dynamiquement un select mois si absent
        let mWrap = document.getElementById('_dynMoisWrap');
        if (!mWrap) {
            mWrap = document.createElement('div');
            mWrap.id = '_dynMoisWrap';
            mWrap.innerHTML = `
                <label class="fi-label">Mois <span class="text-red-500">*</span></label>
                <select id="_dynMoisSel" class="fi" required>
                    <option value="">Choisir un mois…</option>
                    @foreach($moisLabels as $n => $label)
                    <option value="{{ $n }}" data-label="{{ $label }}">{{ $label }}</option>
                    @endforeach
                </select>`;
            document.getElementById('pRef').closest('.space-y-4').prepend(mWrap);
        }
        mWrap.style.display = '';
        const dynSel = document.getElementById('_dynMoisSel');
        dynSel.onchange = function () {
            document.getElementById('pMois').value = this.value;
        };
    } else {
        const mWrap = document.getElementById('_dynMoisWrap');
        if (mWrap) mWrap.style.display = 'none';
    }

    updateReste();
    openModal('payModal');
}

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

// ── Stocker avant soumission pour impression post-reload ──
function storeForPrint() {
    const moisSel = document.getElementById('pMois').value
        || (document.getElementById('_dynMoisSel') ? document.getElementById('_dynMoisSel').value : '');
    const moisOpt = document.querySelector(`#_dynMoisSel option[value="${moisSel}"]`)
                 || document.querySelector(`.mois-head .mn`);
    // Chercher le label du mois
    let moisLabel = '';
    const allMoisOpts = document.querySelectorAll('#_dynMoisSel option');
    allMoisOpts.forEach(o => { if (o.value == moisSel) moisLabel = o.dataset.label || o.textContent; });
    if (!moisLabel) {
        // Essayer le sous-titre du modal
        const sub = document.getElementById('payModalSub').textContent;
        const m = sub.match(/— (.+?) \(/);
        if (m) moisLabel = m[1];
    }

    const data = {
        ref:       document.getElementById('pRef').value,
        du:        parseFloat(document.getElementById('pDu').value)   || 0,
        paye:      parseFloat(document.getElementById('pPaye').value) || 0,
        reste:     Math.max(0, (parseFloat(document.getElementById('pDu').value)||0) - (parseFloat(document.getElementById('pPaye').value)||0)),
        mode:      document.getElementById('pMode').value,
        date:      document.getElementById('pDatePay').value,
        moisLabel: moisLabel || 'Mois',
        appName:   APPRENANT.name,
        matricule: APPRENANT.matricule,
        classe:    APPRENANT.classe,
        annee:     APPRENANT.annee,
        institution: '{{ $institution->name }}',
        emetteur:    '{{ Auth::user()->name }}',
        printAfter: true,
    };
    try { localStorage.setItem('lastInvoice', JSON.stringify(data)); } catch(e) {}
}

// ── Imprimer un enregistrement existant directement ──
function printExistingRecord(ref, du, paye, reste, mode, date, moisLabel) {
    const data = { ref, du, paye, reste, mode, date, moisLabel,
        appName: APPRENANT.name, matricule: APPRENANT.matricule,
        classe: APPRENANT.classe, annee: APPRENANT.annee };
    buildInvoiceDom(data);
    choosePrintFormat(); // ← remplace window.print()
}


// ── Imprimer depuis le bouton succès ──
function printLastInvoice() {
    try {
        const data = JSON.parse(localStorage.getItem('lastInvoice') || '{}');
        if (data && data.ref) { buildInvoiceDom(data); choosePrintFormat(); }
        else alert('Aucun reçu disponible.');
    } catch(e) { alert('Erreur lors de la récupération du reçu.'); }
}

// ── Construire le DOM de la facture ──
function buildInvoiceDom(d) {
    const fmt = n => (n||0).toLocaleString('fr-FR') + ' FCFA';
    const modeLabels = { especes:'Espèces', virement:'Virement bancaire', mobile_money:'Mobile Money', cheque:'Chèque', autre:'Autre', '':'—' };
    const statut = d.paye >= d.du && d.du > 0 ? 'Soldé' : d.paye > 0 ? 'Partiel' : 'Impayé';
    const statClass = statut === 'Soldé' ? 'paid' : statut === 'Partiel' ? 'partial' : 'unpaid';
    const statMsg   = statut === 'Soldé' ? '✓ PAIEMENT COMPLET — SOLDÉ'
                    : statut === 'Partiel' ? '⚠ PAIEMENT PARTIEL — RESTE À PAYER'
                    : '✗ AUCUN PAIEMENT ENREGISTRÉ';

    document.getElementById('inv-num').textContent   = d.ref || '—';
    document.getElementById('inv-date').textContent  = d.date
        ? new Date(d.date).toLocaleDateString('fr-FR', {day:'2-digit',month:'long',year:'numeric'})
        : '—';
    document.getElementById('inv-mois').textContent  = d.moisLabel || '—';
    document.getElementById('inv-mode').textContent  = modeLabels[d.mode] || d.mode || '—';
    document.getElementById('inv-paye').textContent  = fmt(d.paye);
    document.getElementById('inv-du').textContent    = fmt(d.du);
    document.getElementById('inv-paye2').textContent = fmt(d.paye);
    document.getElementById('inv-reste').textContent = fmt(d.reste);
    document.getElementById('inv-statut').textContent= statut;
    document.getElementById('inv-annee').textContent = d.annee || '—';

    document.getElementById('inv-reste-row').style.display = (d.reste > 0) ? 'flex' : 'none';

    const banner = document.getElementById('inv-status-banner');
    banner.textContent = statMsg;
    banner.className   = `invoice-status-banner ${statClass}`;

    const wm = document.getElementById('invoice-watermark');
    wm.textContent = statut === 'Soldé' ? 'PAYÉ' : '';
}

// ── Au chargement : impression auto si en attente ──
window.addEventListener('load', function () {
    try {
        const data = JSON.parse(localStorage.getItem('lastInvoice') || '{}');
        if (data && data.printAfter) {
            data.printAfter = false;
            localStorage.setItem('lastInvoice', JSON.stringify(data));
            setTimeout(() => { buildInvoiceDom(data); choosePrintFormat(); }, 700);
        }
    } catch(e) {}
});

function choosePrintFormat() {
    let saved = 'a4';
    try { saved = localStorage.getItem('printFormat') || 'a4'; } catch(e) {}

    const formats = [
        { key:'a4',  label:'A4',         size:'210 × 297 mm', sub:'Laser / Jet d\'encre',  pageSize:'A4' },
        { key:'t80', label:'Ticket 80mm', size:'80mm × auto',  sub:'Imprimante thermique', pageSize:'80mm 297mm' },
        { key:'t58', label:'Ticket 58mm', size:'58mm × auto',  sub:'Petite thermique USB', pageSize:'58mm 297mm' },
    ];

    const overlay = document.createElement('div');
    overlay.setAttribute('data-print-dialog', '');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';

    const cards = formats.map(f => `
        <div data-fmt="${f.key}"
             onclick="window._selectFmt('${f.key}',this)"
             style="background:#fff;border:${saved===f.key?'2px solid #2563eb':'1.5px solid #e5e7eb'};border-radius:12px;padding:1rem;cursor:pointer;min-width:130px;text-align:center;transition:border .15s;flex:1;">
            <div style="font-size:24px;margin-bottom:6px">${f.key==='a4'?'📄':'🧾'}</div>
            <div style="font-size:13px;font-weight:700;color:#111827">${f.label}</div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px">${f.size}</div>
            <div style="font-size:10px;color:#9ca3af">${f.sub}</div>
            ${saved===f.key?'<div style="margin-top:6px;font-size:10px;background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:9999px;display:inline-block">Dernier utilisé</div>':''}
        </div>`).join('');

    overlay.innerHTML = `
        <div style="background:#fff;border-radius:16px;padding:1.5rem;max-width:500px;width:100%;box-shadow:0 24px 60px rgba(0,0,0,.2);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
                <span style="font-size:15px;font-weight:700;color:#111827">Format d'impression</span>
                <button onclick="document.querySelector('[data-print-dialog]').remove()" style="border:none;background:none;font-size:20px;cursor:pointer;color:#9ca3af;line-height:1;">×</button>
            </div>
            <div style="display:flex;gap:.75rem;margin-bottom:1.25rem;">${cards}</div>
            <div style="display:flex;justify-content:flex-end;gap:.75rem;">
                <button onclick="document.querySelector('[data-print-dialog]').remove()" style="padding:.5rem 1rem;border:1px solid #e5e7eb;border-radius:8px;background:#fff;font-size:.82rem;font-weight:500;cursor:pointer;font-family:inherit;">Annuler</button>
                <button onclick="window._confirmPrint()" style="padding:.5rem 1.25rem;border:none;border-radius:8px;background:#111827;color:#fff;font-size:.82rem;font-weight:500;cursor:pointer;font-family:inherit;">Imprimer</button>
            </div>
        </div>`;

    document.body.appendChild(overlay);
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.remove(); });

    window._selectedFormat = saved;

    window._selectFmt = function(fmt, el) {
        document.querySelectorAll('[data-fmt]').forEach(c => {
            c.style.border = '1.5px solid #e5e7eb';
            const b = c.querySelector('[style*="dbeafe"]');
            if (b) b.remove();
        });
        el.style.border = '2px solid #2563eb';
        const badge = document.createElement('div');
        badge.style.cssText = 'margin-top:6px;font-size:10px;background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:9999px;display:inline-block';
        badge.textContent = 'Sélectionné';
        el.appendChild(badge);
        window._selectedFormat = fmt;
    };

    window._confirmPrint = function() {
        const fmt = window._selectedFormat || 'a4';
        try { localStorage.setItem('printFormat', fmt); } catch(e) {}
        document.querySelector('[data-print-dialog]')?.remove();
        _applyPageFormat(fmt);
        setTimeout(() => {
            window.print();
            setTimeout(() => _applyPageFormat('a4'), 1500);
        }, 120);
    };
}

function _applyPageFormat(fmt) {
    let el = document.getElementById('_pageStyle');
    if (!el) { el = document.createElement('style'); el.id = '_pageStyle'; document.head.appendChild(el); }
    const sizes = { a4:'@page{size:A4 portrait;margin:10mm}', t80:'@page{size:80mm auto;margin:3mm}', t58:'@page{size:58mm auto;margin:2mm}' };
    el.textContent = sizes[fmt] || sizes.a4;
}
</script>
@endpush
