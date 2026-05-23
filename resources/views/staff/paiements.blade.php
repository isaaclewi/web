@extends('staff.master')

@section('title', 'Finances')
@section('page-title', 'Gestion financière')
@section('page-sub', 'Suivi des paiements et échéances')

@push('styles')
<style>
*, *::before, *::after { box-sizing: border-box; }

/* ══ STATS GRID ══ */
.stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .625rem; margin-bottom: 1.25rem; }
@media (min-width: 480px) { .stat-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 900px) { .stat-grid { grid-template-columns: repeat(6, 1fr); } }
.stat-card { background: var(--white); border: 1px solid var(--brd); border-radius: 12px; padding: .875rem 1rem; }
.stat-val { font-size: 1.1rem; font-weight: 800; color: var(--night); line-height: 1; word-break: break-all; }
.stat-lbl { font-size: .68rem; color: var(--mist); margin-top: .25rem; line-height: 1.3; }
@media (min-width: 640px) { .stat-val { font-size: 1.35rem; } }

/* ══ BARRE RECOUVREMENT ══ */
.recouv-card { background: var(--white); border: 1px solid var(--brd); border-radius: 14px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; }
.recouv-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: .5rem; flex-wrap: wrap; gap: .25rem; }
.recouv-label { font-size: .78rem; font-weight: 600; color: #374151; }
.recouv-pct   { font-size: 1rem; font-weight: 800; }

/* ══ PROGRESS BAR ══ */
.prog-wrap { flex: 1; height: 6px; background: var(--bg); border-radius: 3px; overflow: hidden; }
.prog-bar  { height: 100%; border-radius: 3px; transition: width .5s cubic-bezier(.4,0,.2,1); }
.prog-ok   { background: var(--ok); }
.prog-warn { background: var(--warn); }
.prog-err  { background: var(--err); }

/* ══ TOOLBAR ══ */
.fin-toolbar { display: flex; flex-direction: column; gap: .625rem; margin-bottom: 1.25rem; }
@media (min-width: 640px) { .fin-toolbar { flex-direction: row; align-items: center; flex-wrap: wrap; } }
.yr-tabs { display: flex; gap: .25rem; flex-wrap: wrap; }
.yr-tab { padding: .3rem .75rem; border-radius: 7px; font-size: .76rem; font-weight: 600; border: 1px solid var(--brd); background: var(--white); color: #6b7280; cursor: pointer; text-decoration: none; transition: all .15s; white-space: nowrap; }
.yr-tab.on { background: var(--night); color: var(--white); border-color: var(--night); }
.yr-tab:hover:not(.on) { background: var(--bg); border-color: var(--brd-d); color: var(--night); }
.fin-actions { display: flex; gap: .5rem; flex-wrap: wrap; width: 100%; }
@media (min-width: 640px) { .fin-actions { margin-left: auto; width: auto; } }

/* ══ RECHERCHE + FILTRES ══ */
.fin-search-bar {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    align-items: center;
    background: var(--white);
    border: 1px solid var(--brd);
    border-radius: 12px;
    padding: .75rem 1rem;
    margin-bottom: 1rem;
}
.fin-search-input-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.fin-search-icon {
    position: absolute;
    left: .75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--mist);
    pointer-events: none;
}
.fin-search-input {
    width: 100%;
    padding: .55rem .75rem .55rem 2.25rem;
    border: 1px solid var(--brd);
    border-radius: 8px;
    font-size: .82rem;
    font-family: inherit;
    background: var(--bg);
    color: var(--night);
    transition: border-color .15s, box-shadow .15s;
}
.fin-search-input:focus { outline: none; border-color: #9ca3af; box-shadow: 0 0 0 3px rgba(156,163,175,.12); background: var(--white); }
.fin-search-input::placeholder { color: var(--mist); }
.fin-filter-sel {
    padding: .55rem .75rem;
    border: 1px solid var(--brd);
    border-radius: 8px;
    font-size: .82rem;
    font-family: inherit;
    background: var(--white);
    color: var(--night);
    min-width: 110px;
    cursor: pointer;
}

/* ══ LAYOUT ══ */
.fin-layout { display: flex; flex-direction: column; gap: 1.25rem; }
@media (min-width: 960px) { .fin-layout { display: grid; grid-template-columns: 1fr 300px; align-items: start; } }
.fin-sidebar { display: flex; flex-direction: column; gap: 1rem; }
@media (min-width: 480px) and (max-width: 959px) { .fin-sidebar { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; } }

/* ══ TABLE ══ */
.fin-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.s-tbl { width: 100%; border-collapse: collapse; min-width: 580px; }
.s-tbl thead th { padding: .55rem .875rem; background: var(--bg); text-align: left; font-size: .67rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--mist); border-bottom: 1px solid var(--brd); white-space: nowrap; }
.s-tbl td { padding: .65rem .875rem; border-bottom: 1px solid #f1f5f9; font-size: .8rem; vertical-align: middle; }
.s-tbl tr:last-child td { border-bottom: none; }
.s-tbl tr:hover td { background: var(--bg); }

/* ══ PAGINATION ══ */
.fin-pagination { padding: .875rem 1rem; border-top: 1px solid var(--brd); }
.fin-pagination nav { display: flex; flex-wrap: wrap; align-items: center; gap: .375rem; }
.fin-pagination nav > div:first-child { width: 100%; font-size: .72rem; color: var(--mist); margin-bottom: .25rem; }
@media (min-width: 640px) { .fin-pagination nav > div:first-child { width: auto; margin-bottom: 0; margin-right: auto; } }
.fin-pagination span span, .fin-pagination a { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 .5rem; border-radius: 8px; font-size: .78rem; font-weight: 600; border: 1px solid var(--brd); background: var(--white); color: #374151; text-decoration: none; transition: all .15s; white-space: nowrap; }
.fin-pagination span[aria-current="page"] > span { background: var(--night); color: var(--white); border-color: var(--night); }
.fin-pagination a:hover { background: var(--bg); border-color: var(--brd-d); color: var(--night); }
.fin-pagination span[aria-disabled="true"] > span { background: var(--bg); color: var(--mist); cursor: default; }

/* ══ SIDEBAR TENDANCE ══ */
.trend-row { display: flex; align-items: center; gap: .625rem; padding: .5rem 1rem; border-bottom: 1px solid var(--brd); }
.trend-row:last-child { border-bottom: none; }
.trend-mois { font-size: .76rem; font-weight: 600; color: #374151; width: 52px; flex-shrink: 0; }
.trend-bar-wrap { flex: 1; height: 8px; background: var(--bg); border-radius: 4px; overflow: hidden; min-width: 0; }
.trend-bar { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--ok), #10b981cc); }
.trend-vals { font-size: .72rem; color: var(--mist); text-align: right; flex-shrink: 0; min-width: 68px; line-height: 1.4; }

/* ══ PAIEMENTS RÉCENTS ══ */
.recent-item { padding: .75rem 1rem; border-bottom: 1px solid var(--brd); display: flex; align-items: center; gap: .75rem; }
.recent-item:last-child { border-bottom: none; }
.recent-icon { width: 32px; height: 32px; border-radius: 9px; background: var(--ok-l); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.recent-info { flex: 1; min-width: 0; }
.recent-name { font-size: .78rem; font-weight: 600; color: var(--night); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.recent-sub  { font-size: .68rem; color: var(--mist); }
.recent-amount { font-size: .82rem; font-weight: 700; color: var(--ok); flex-shrink: 0; margin-left: auto; }

/* ══ CARD HEADER ══ */
.s-card-hd { padding: .875rem 1rem; border-bottom: 1px solid var(--brd); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem; }
.s-card-hd h3 { font-size: .86rem; font-weight: 700; margin: 0; }

/* ══ MODALS ══ */
.s-modal { display: none; position: fixed; inset: 0; z-index: 500; background: rgba(8,12,20,.6); backdrop-filter: blur(4px); align-items: flex-start; justify-content: center; padding: 1rem; overflow-y: auto; }
.s-modal.open { display: flex; }
.s-modal-box { background: var(--white); border-radius: 16px; width: 100%; max-width: 560px; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: modalIn .25s cubic-bezier(.4,0,.2,1) both; margin: auto 0; }
@keyframes modalIn { from { transform: translateY(-16px); opacity: 0; } to { transform: none; opacity: 1; } }
.s-modal-hd { padding: 1rem 1.25rem; border-bottom: 1px solid var(--brd); display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: var(--white); z-index: 1; }
.s-modal-hd h3 { font-size: .95rem; font-weight: 700; margin: 0; }
.s-modal-body { padding: 1rem 1.25rem; }
@media (min-width: 480px) { .s-modal-body { padding: 1.25rem 1.5rem; } }
.s-modal-ft { padding: .875rem 1.25rem; border-top: 1px solid var(--brd); display: flex; flex-wrap: wrap; gap: .5rem; justify-content: flex-end; position: sticky; bottom: 0; background: var(--white); }

/* ══ GRILLES FORMULAIRE ══ */
.fg2 { display: grid; grid-template-columns: 1fr; gap: .75rem; }
.fg-group { display: flex; flex-direction: column; gap: .35rem; }
@media (min-width: 420px) { .fg2 { grid-template-columns: 1fr 1fr; } }

/* ══ ACTION DROPDOWN ══ */
.act-wrap { position: relative; display: inline-block; }
.act-menu { display: none; position: absolute; right: 0; top: calc(100% + 4px); z-index: 100; background: var(--white); border: 1px solid var(--brd); border-radius: 10px; box-shadow: 0 8px 28px rgba(0,0,0,.1); min-width: 170px; overflow: hidden; }
.act-wrap:hover .act-menu, .act-wrap:focus-within .act-menu { display: block; }
@media (max-width: 480px) { .act-menu { right: auto; left: 0; } }
.act-menu a, .act-menu button { display: flex; align-items: center; gap: .5rem; width: 100%; padding: .6rem 1rem; font-size: .8rem; font-weight: 500; color: #374151; border: none; background: none; cursor: pointer; font-family: inherit; text-decoration: none; transition: background .12s; }
.act-menu a:hover, .act-menu button:hover { background: var(--bg); }

/* ══ EMPTY STATE ══ */
.s-empty { padding: 2rem 1rem; text-align: center; color: var(--mist); }
.s-empty h4 { font-size: .9rem; font-weight: 700; margin: 0 0 .35rem; }
.s-empty p  { font-size: .8rem; margin: 0; }

/* ══ NUMÉRO REÇU ══ */
.ref-wrap { display: flex; gap: .5rem; align-items: center; }
.ref-input { flex: 1; font-family: monospace; letter-spacing: .04em; background: var(--bg) !important; color: var(--night); }
.ref-regen { padding: .45rem .6rem; border: 1px solid var(--brd); border-radius: 8px; background: var(--white); cursor: pointer; color: var(--mist); transition: all .15s; display: flex; align-items: center; }
.ref-regen:hover { background: var(--bg); color: var(--night); border-color: var(--brd-d); }
.ref-regen svg { width: 14px; height: 14px; }

/* ══ RESTE PREVIEW ══ */
.reste-preview { display: flex; align-items: center; justify-content: space-between; background: var(--bg); border: 1px solid var(--brd); border-radius: 8px; padding: .55rem 1rem; font-size: .82rem; }
.reste-val { font-weight: 800; font-size: .95rem; }

/* ══ DRAWER (panneau dossier apprenant) ══ */
.drawer-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 600;
    background: rgba(8,12,20,.45);
    backdrop-filter: blur(3px);
}
.drawer-overlay.open { display: block; }
.drawer-panel {
    position: fixed;
    top: 0;
    right: 0;
    height: 100%;
    width: 100%;
    max-width: 520px;
    background: var(--white);
    z-index: 601;
    box-shadow: -16px 0 48px rgba(0,0,0,.18);
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform .3s cubic-bezier(.4,0,.2,1);
}
.drawer-overlay.open .drawer-panel { transform: translateX(0); }
.drawer-head {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--brd);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    background: var(--white);
    position: sticky;
    top: 0;
    z-index: 1;
}
.drawer-head h3 { font-size: .95rem; font-weight: 700; margin: 0; }
.drawer-body { flex: 1; overflow-y: auto; padding: 1.25rem; }
.drawer-close { padding: .4rem; background: none; border: 1px solid var(--brd); border-radius: 8px; cursor: pointer; color: var(--mist); display: flex; align-items: center; transition: all .15s; }
.drawer-close:hover { background: var(--bg); color: var(--night); }
.drawer-close svg { width: 16px; height: 16px; }

/* ── Dossier apprenant dans le drawer ── */
.ap-identity { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; padding: 1rem; background: var(--bg); border-radius: 12px; border: 1px solid var(--brd); }
.ap-avatar { width: 48px; height: 48px; border-radius: 12px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; flex-shrink: 0; }
.ap-info { min-width: 0; }
.ap-name { font-size: 1rem; font-weight: 700; color: var(--night); }
.ap-sub  { font-size: .75rem; color: var(--mist); font-family: monospace; }
.ap-badges { display: flex; flex-wrap: wrap; gap: .375rem; margin-top: .5rem; }
.ap-badge { font-size: .68rem; font-weight: 600; padding: .2rem .6rem; border-radius: 9999px; background: #e0e7ff; color: #4338ca; }

.dossier-section { margin-bottom: 1.25rem; }
.dossier-section-title { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--mist); margin-bottom: .625rem; }

.fin-recap-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .5rem; margin-bottom: 1rem; }
.fin-recap-card { background: var(--bg); border: 1px solid var(--brd); border-radius: 10px; padding: .625rem .75rem; }
.fin-recap-val { font-size: .95rem; font-weight: 800; }
.fin-recap-lbl { font-size: .65rem; color: var(--mist); margin-top: .15rem; }

.mois-list { display: flex; flex-direction: column; gap: .375rem; }
.mois-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem .875rem;
    border-radius: 10px;
    border: 1px solid var(--brd);
    background: var(--white);
    cursor: pointer;
    transition: all .15s;
}
.mois-item:hover { background: var(--bg); border-color: var(--brd-d); }
.mois-item.paye    { border-left: 3px solid var(--ok); }
.mois-item.partiel { border-left: 3px solid var(--warn); }
.mois-item.impaye  { border-left: 3px solid var(--err); }
.mois-item.vide    { border-style: dashed; border-color: var(--brd); opacity: .6; }
.mois-item-lbl { font-size: .8rem; font-weight: 600; color: var(--night); min-width: 60px; }
.mois-item-bar { flex: 1; height: 5px; background: var(--bg); border-radius: 3px; overflow: hidden; }
.mois-item-fill { height: 100%; border-radius: 3px; }
.mois-item-fill.ok   { background: var(--ok); }
.mois-item-fill.warn { background: var(--warn); }
.mois-item-fill.err  { background: var(--err); }
.mois-item-info { text-align: right; min-width: 80px; }
.mois-item-paye { font-size: .78rem; font-weight: 700; color: var(--ok); }
.mois-item-reste { font-size: .65rem; color: var(--mist); }
.mois-item-add { font-size: .72rem; font-weight: 600; color: var(--mist); margin-left: auto; }

/* ══ PRINT ZONE (FACTURE) ══ */
#invoice-print-area { display: none; }
@media print {
    body * { visibility: hidden !important; }
    #invoice-print-area, #invoice-print-area * { visibility: visible !important; }
    #invoice-print-area { display: block !important; position: fixed; inset: 0; background: white; z-index: 9999; padding: 0; }
}

/* ── Styles de la facture ── */
.inv-wrap { font-family: 'DM Sans', Georgia, sans-serif; max-width: 720px; margin: 0 auto; padding: 40px; background: #fff; color: #1a1a2e; position: relative; }
.inv-hd { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #111827; padding-bottom: 24px; margin-bottom: 28px; }
.inv-school-name { font-size: 20px; font-weight: 800; color: #111827; margin: 0 0 4px; }
.inv-school-sub { font-size: 11px; color: #6b7280; margin: 0; line-height: 1.6; }
.inv-meta { text-align: right; }
.inv-title { font-size: 28px; font-weight: 900; color: #111827; letter-spacing: -1px; margin: 0 0 6px; }
.inv-num  { font-family: monospace; font-size: 13px; color: #6b7280; background: #f3f4f6; padding: 3px 10px; border-radius: 6px; display: inline-block; }
.inv-date { font-size: 11px; color: #9ca3af; display: block; margin-top: 4px; }
.inv-parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
.inv-party { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; }
.inv-party-lbl  { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin-bottom: 8px; }
.inv-party-name { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 3px; }
.inv-party-det  { font-size: 12px; color: #6b7280; line-height: 1.6; }
.inv-tbl { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
.inv-tbl thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; border-bottom: 2px solid #e5e7eb; background: #f9fafb; }
.inv-tbl thead th:last-child { text-align: right; }
.inv-tbl tbody td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f3f4f6; color: #374151; }
.inv-tbl tbody td:last-child { text-align: right; font-weight: 600; }
.inv-totals { margin-left: auto; width: 280px; margin-bottom: 28px; }
.inv-total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #6b7280; border-bottom: 1px solid #f3f4f6; }
.inv-total-row:last-child { border-bottom: none; font-size: 16px; font-weight: 800; color: #111827; padding-top: 10px; border-top: 2px solid #111827; margin-top: 4px; }
.inv-total-row .v { font-weight: 600; color: #111827; }
.inv-total-row.paid .v { color: #16a34a; }
.inv-total-row.rest .v { color: #dc2626; }
.inv-banner { text-align: center; padding: 14px; border-radius: 10px; font-size: 14px; font-weight: 700; margin-bottom: 24px; }
.inv-banner.paid    { background: #dcfce7; color: #15803d; border: 2px solid #bbf7d0; }
.inv-banner.partial { background: #fef3c7; color: #b45309; border: 2px solid #fde68a; }
.inv-banner.unpaid  { background: #fee2e2; color: #b91c1c; border: 2px solid #fecaca; }
.inv-footer { border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
.inv-footer-note { font-size: 11px; color: #9ca3af; line-height: 1.6; max-width: 340px; }
.inv-sig-block { text-align: center; }
.inv-sig-line  { width: 160px; border-top: 1.5px solid #d1d5db; margin-bottom: 6px; }
.inv-sig-lbl   { font-size: 10px; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
.inv-sig-name  { font-size: 12px; font-weight: 700; color: #374151; margin-top: 2px; }
.inv-watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-30deg); font-size: 80px; font-weight: 900; color: rgba(22,163,74,.07); letter-spacing: 8px; pointer-events: none; white-space: nowrap; }

@media (max-width: 360px) {
    .stat-val { font-size: .95rem; }
    .yr-tab   { font-size: .7rem; padding: .25rem .55rem; }
}
</style>
@endpush

@section('content')

{{-- ══ FLASH ══ --}}
@if(session('success'))
<div style="display:flex;align-items:center;gap:.75rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:12px;padding:.875rem 1.125rem;font-size:.85rem;margin-bottom:1rem;" id="flashAlert">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <span style="flex:1">{{ session('success') }}</span>
    <button onclick="printLastInvoice()" style="display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .75rem;border:1px solid #bbf7d0;border-radius:7px;background:#fff;color:#15803d;font-size:.75rem;font-weight:600;cursor:pointer;font-family:inherit;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Imprimer le reçu
    </button>
    <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#15803d;padding:.2rem;">✕</button>
</div>
@endif

{{-- ══ STATS ══ --}}
@php
    $du    = $statsAnnee->total_du    ?? 0;
    $paye  = $statsAnnee->total_paye  ?? 0;
    $reste = $statsAnnee->total_reste ?? 0;
    $pct   = $du > 0 ? round($paye / $du * 100) : 0;
@endphp
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-val">{{ number_format($du, 0, ',', ' ') }}</div>
        <div class="stat-lbl">Total dû (FCFA)</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--ok)">{{ number_format($paye, 0, ',', ' ') }}</div>
        <div class="stat-lbl">Total encaissé · <b>{{ $pct }}%</b></div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--err)">{{ number_format($reste, 0, ',', ' ') }}</div>
        <div class="stat-lbl">Reste à percevoir</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--ok)">{{ $statsAnnee->nb_payes ?? 0 }}</div>
        <div class="stat-lbl">Soldés</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--warn)">{{ $statsAnnee->nb_partiels ?? 0 }}</div>
        <div class="stat-lbl">Partiels</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--err)">{{ $statsAnnee->nb_impayes ?? 0 }}</div>
        <div class="stat-lbl">Impayés</div>
    </div>
</div>

{{-- ══ BARRE RECOUVREMENT ══ --}}
<div class="recouv-card">
    <div class="recouv-head">
        <span class="recouv-label">Taux de recouvrement · {{ $annee }}</span>
        <span class="recouv-pct" style="color:{{ $pct >= 80 ? 'var(--ok)' : ($pct >= 50 ? 'var(--warn)' : 'var(--err)') }}">{{ $pct }}%</span>
    </div>
    <div class="prog-wrap" style="height:10px;">
        <div class="prog-bar {{ $pct >= 80 ? 'prog-ok' : ($pct >= 50 ? 'prog-warn' : 'prog-err') }}" style="width:{{ $pct }}%"></div>
    </div>
</div>

{{-- ══ TOOLBAR ══ --}}
<div class="fin-toolbar">
    <div class="yr-tabs">
        @foreach($anneesDispos as $yr)
            <a href="{{ request()->fullUrlWithQuery(['annee' => $yr]) }}" class="yr-tab {{ $annee == $yr ? 'on' : '' }}">{{ $yr }}</a>
        @endforeach
    </div>
    <div class="fin-actions">
        <a href="{{ route('staff.finances.export', ['annee' => $annee]) }}" class="btn btn-ot">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
        <button class="btn btn-gold" onclick="openPay()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Saisir un paiement
        </button>
    </div>
</div>

{{-- ══ LAYOUT ══ --}}
<div class="fin-layout">
    <div>
        {{-- ── BARRE DE RECHERCHE + FILTRES ── --}}
        <form method="GET" id="searchForm" class="fin-search-bar">
            <input type="hidden" name="annee" value="{{ $annee }}">

            {{-- Recherche texte --}}
            <div class="fin-search-input-wrap">
                <svg class="fin-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    class="fin-search-input"
                    type="text"
                    name="search"
                    id="searchInput"
                    value="{{ $search }}"
                    placeholder="Rechercher par nom, prénom ou matricule…"
                    autocomplete="off"
                >
            </div>

            {{-- Filtre statut --}}
            <select class="fin-filter-sel" name="statut" onchange="document.getElementById('searchForm').submit()">
                <option value="">Tous statuts</option>
                <option value="paye"    @selected($statut == 'paye')>✓ Soldé</option>
                <option value="partiel" @selected($statut == 'partiel')>~ Partiel</option>
                <option value="impaye"  @selected($statut == 'impaye')>✗ Impayé</option>
            </select>

            {{-- Filtre classe --}}
            <select class="fin-filter-sel" name="classe_id" onchange="document.getElementById('searchForm').submit()">
                <option value="">Toutes classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" @selected($classeId == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>

            {{-- Bouton rechercher --}}
            <button type="submit" class="btn btn-dk" style="white-space:nowrap;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Rechercher
            </button>

            @if($search || $statut || $classeId)
            <a href="{{ request()->fullUrlWithQuery(['search' => '', 'statut' => '', 'classe_id' => '']) }}"
               class="btn btn-ot" style="white-space:nowrap;" title="Effacer les filtres">✕ Effacer</a>
            @endif
        </form>

        {{-- ── TABLE ── --}}
        <div class="s-card">
            <div class="s-card-hd">
                <h3>Apprenants</h3>
                <span style="font-size:.72rem;color:var(--mist)">
                    {{ $apprenants->total() }} résultat(s)
                    @if($search) · "<strong>{{ $search }}</strong>" @endif
                </span>
            </div>

            <div class="fin-table-wrap">
                <table class="s-tbl">
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Classe</th>
                            <th>Payé</th>
                            <th>Reste</th>
                            <th style="min-width:110px">Recouvrement</th>
                            <th>Statut</th>
                            <th style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apprenants as $ap)
                            @php
                                $rec     = $ap->financialRecords;
                                $apPaye  = $rec->sum('montant_paye');
                                $apReste = $rec->sum('montant_reste');
                                $apDu    = $rec->sum('montant_du');
                                $apPct   = $apDu > 0 ? min(100, round($apPaye / $apDu * 100)) : 0;
                                $apSt    = $apReste <= 0 && $apDu > 0 ? 'paye' : ($apPaye > 0 ? 'partiel' : 'impaye');

                                // Préparer les données JSON pour le drawer
                                $recJson = $rec->map(fn($r) => [
                                    'mois'      => $r->mois,
                                    'mois_label'=> $r->mois_label,
                                    'montant_du'=> $r->montant_du,
                                    'montant_paye'=> $r->montant_paye,
                                    'montant_reste'=> $r->montant_reste,
                                    'statut'    => $r->statut,
                                    'mode_paiement' => $r->mode_paiement,
                                    'reference' => $r->reference,
                                    'date_paiement' => $r->date_paiement?->format('d/m/Y'),
                                ])->keyBy('mois')->toArray();
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:.83rem;">{{ $ap->prenom }} {{ $ap->nom }}</div>
                                    <div style="font-size:.68rem;color:var(--mist);font-family:monospace;">{{ $ap->matricule }}</div>
                                </td>
                                <td style="font-size:.8rem;">{{ $ap->classe?->name ?? '—' }}</td>
                                <td style="font-weight:700;color:var(--ok);font-size:.83rem;white-space:nowrap;">{{ number_format($apPaye, 0, ',', ' ') }}</td>
                                <td style="font-weight:700;color:{{ $apReste > 0 ? 'var(--err)' : 'var(--mist)' }};font-size:.83rem;white-space:nowrap;">{{ number_format($apReste, 0, ',', ' ') }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:.5rem;">
                                        <div class="prog-wrap">
                                            <div class="prog-bar {{ $apPct >= 100 ? 'prog-ok' : ($apPct >= 50 ? 'prog-warn' : 'prog-err') }}" style="width:{{ $apPct }}%"></div>
                                        </div>
                                        <span style="font-size:.7rem;font-weight:700;color:var(--mist);flex-shrink:0;">{{ $apPct }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="bdg {{ $apSt === 'paye' ? 'bdg-g' : ($apSt === 'partiel' ? 'bdg-a' : 'bdg-r') }}">
                                        {{ ['paye' => 'Soldé', 'partiel' => 'Partiel', 'impaye' => 'Impayé'][$apSt] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="act-wrap" tabindex="0">
                                        <button class="btn btn-ot btn-sm">•••</button>
                                        <div class="act-menu">
                                            {{-- VOIR DOSSIER : ouvre le drawer inline --}}
                                            <button onclick="openDossier(
                                                {{ $ap->id }},
                                                '{{ addslashes($ap->prenom . ' ' . $ap->nom) }}',
                                                '{{ addslashes($ap->matricule ?? '') }}',
                                                '{{ addslashes($ap->classe?->name ?? '') }}',
                                                '{{ addslashes($ap->niveau?->name ?? '') }}',
                                                '{{ addslashes($ap->filiere?->name ?? '') }}',
                                                {{ $apDu }}, {{ $apPaye }}, {{ $apReste }},
                                                {{ json_encode($recJson) }}
                                            )">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Voir le dossier
                                            </button>
                                            <button onclick="openPayFor({{ $ap->id }}, '{{ addslashes($ap->prenom . ' ' . $ap->nom) }}', '{{ addslashes($ap->matricule ?? '') }}', '{{ addslashes($ap->classe?->name ?? '') }}')">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Saisir paiement
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="s-empty">
                                        <h4>Aucun résultat</h4>
                                        @if($search)
                                        <p>Aucun apprenant ne correspond à "{{ $search }}".</p>
                                        @else
                                        <p>Modifiez vos filtres pour voir des résultats.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="fin-pagination">
                {{ $apprenants->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- ── SIDEBAR ── --}}
    <div class="fin-sidebar">
        @if($statsMois->count())
        <div class="s-card">
            <div class="s-card-hd"><h3>Par mois</h3></div>
            @php $maxMois = $statsMois->max('paye') ?: 1; @endphp
            @foreach($statsMois as $m)
                <div class="trend-row">
                    <div class="trend-mois">{{ mb_substr($m->mois_label, 0, 3) }}</div>
                    <div class="trend-bar-wrap">
                        <div class="trend-bar" style="width:{{ round($m->paye / $maxMois * 100) }}%"></div>
                    </div>
                    <div class="trend-vals">
                        {{ number_format($m->paye / 1000, 0, '.', ',') }}k<br>
                        <span style="color:var(--err);font-size:.65rem;">-{{ number_format(($m->du - $m->paye) / 1000, 0, '.', ',') }}k</span>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <div class="s-card">
            <div class="s-card-hd"><h3>Paiements récents</h3></div>
            @forelse($recentPaiements as $r)
                <div class="recent-item">
                    <div class="recent-icon">
                        <svg fill="none" stroke="var(--ok)" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="recent-info">
                        <div class="recent-name">{{ $r->apprenant?->prenom }} {{ $r->apprenant?->nom }}</div>
                        <div class="recent-sub">{{ $r->mois_label }} · {{ $r->date_paiement?->format('d/m/Y') }}</div>
                    </div>
                    <div class="recent-amount">+{{ number_format($r->montant_paye, 0, ',', ' ') }}</div>
                </div>
            @empty
                <div style="padding:1.5rem;text-align:center;color:var(--mist);font-size:.8rem;">Aucun paiement récent</div>
            @endforelse
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════
     DRAWER — DOSSIER APPRENANT (inline)
══════════════════════════════════════════ --}}
<div class="drawer-overlay" id="dossierOverlay" onclick="closeDossier()">
    <div class="drawer-panel" onclick="event.stopPropagation()">
        <div class="drawer-head">
            <h3 id="drawerTitle">Dossier financier</h3>
            <button class="drawer-close" onclick="closeDossier()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="drawer-body" id="drawerBody">
            {{-- Rempli dynamiquement par JS --}}
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════
     MODAL — SAISIE PAIEMENT
══════════════════════════════════════════ --}}
<div class="s-modal" id="modal-pay">
    <div class="s-modal-box">
        <div class="s-modal-hd">
            <h3 id="pay-title">Enregistrer un paiement</h3>
            <button class="btn btn-ot btn-sm" onclick="closePay()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.paiements.store') }}" id="payForm">
            @csrf
            {{-- Hidden fields --}}
            <input type="hidden" name="apprenant_id"     id="pay_ap_hidden">
            <input type="hidden" name="annee_academique" value="{{ $annee }}">
            <input type="hidden" name="mois"             id="pay_mois_hidden">

            <div class="s-modal-body">

                {{-- Sélect apprenant (visible si ouverture sans contexte) --}}
                <div class="fg-group" style="margin-bottom:.875rem;" id="pay_ap_wrap">
                    <label class="lbl">Apprenant *</label>
                    <select class="inp" id="pay_ap_sel">
                        <option value="">Sélectionner un apprenant…</option>
                        @foreach($apprenants as $ap)
                            <option value="{{ $ap->id }}"
                                    data-nom="{{ $ap->prenom }} {{ $ap->nom }}"
                                    data-matricule="{{ $ap->matricule }}"
                                    data-classe="{{ $ap->classe?->name ?? '' }}">
                                {{ $ap->prenom }} {{ $ap->nom }}
                                @if($ap->matricule)({{ $ap->matricule }})@endif
                                @if($ap->classe) — {{ $ap->classe->name }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sélect mois (visible si ouverture sans contexte) --}}
                <div class="fg-group" style="margin-bottom:.875rem;" id="pay_mois_wrap">
                    <label class="lbl">Mois *</label>
                    <select class="inp" id="pay_mois_sel">
                        <option value="">Choisir un mois…</option>
                        @foreach($moisLabels as $k => $v)
                            <option value="{{ $k }}" data-label="{{ $v }}" @selected($k == date('n'))>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- N° de reçu --}}
                <div class="fg-group" style="margin-bottom:.875rem;">
                    <label class="lbl">N° de reçu (généré automatiquement)</label>
                    <div class="ref-wrap">
                        <input class="inp ref-input" type="text" name="reference" id="pay_ref" readonly>
                        <button type="button" class="ref-regen" onclick="generateRef()" title="Régénérer">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>

                <div class="fg2" style="margin-bottom:.875rem;">
                    <div class="fg-group">
                        <label class="lbl">Montant dû (FCFA) *</label>
                        <input class="inp" type="number" name="montant_du" id="pay_du" min="0" step="100" required oninput="updateReste()">
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Montant payé (FCFA) *</label>
                        <input class="inp" type="number" name="montant_paye" id="pay_paye" min="0" step="100" required oninput="updateReste()">
                    </div>
                </div>

                {{-- Aperçu reste --}}
                <div class="reste-preview" style="margin-bottom:.875rem;">
                    <span style="font-size:.8rem;color:var(--mist);">Reste à payer :</span>
                    <span class="reste-val" id="pay_reste_display" style="color:var(--err)">0 FCFA</span>
                    <span id="pay_statut_preview" style="font-size:.75rem;font-weight:700;"></span>
                </div>

                <div class="fg2" style="margin-bottom:.875rem;">
                    <div class="fg-group">
                        <label class="lbl">Date de paiement</label>
                        <input class="inp" type="date" name="date_paiement" id="pay_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Mode de paiement</label>
                        <select class="inp" name="mode_paiement" id="pay_mode">
                            <option value="">—</option>
                            <option value="especes">Espèces</option>
                            <option value="virement">Virement</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Chèque</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>

                <div class="fg-group" style="margin-bottom:.875rem;">
                    <label class="lbl">Notes / Observation</label>
                    <textarea class="inp" name="notes" rows="2" style="resize:vertical;" placeholder="Observation…"></textarea>
                </div>

                {{-- Signature --}}
                <div style="display:flex;align-items:center;gap:.75rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.75rem 1rem;">
                    <div style="width:32px;height:32px;background:#e0e7ff;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg fill="none" stroke="#4338ca" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:.78rem;font-weight:600;color:var(--night);">{{ Auth::user()->name }}</div>
                        <div style="font-size:.68rem;color:var(--mist);">{{ now()->format('d/m/Y à H:i') }}</div>
                        <div style="font-size:.68rem;color:#4338ca;font-weight:500;">Signature attachée automatiquement</div>
                    </div>
                </div>

            </div>
            <div class="s-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closePay()">Annuler</button>
                <button type="submit" class="btn btn-gold" onclick="storeForPrint()">Enregistrer</button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════
     ZONE FACTURE (impression uniquement)
══════════════════════════════════════════ --}}
<div id="invoice-print-area">
    <div class="inv-wrap">
        <div id="inv-watermark" class="inv-watermark"></div>

        <div class="inv-hd">
            <div>
                @if($institution->logo)
                    <img src="{{ Storage::url($institution->logo) }}" alt="Logo" style="height:44px;object-fit:contain;display:block;margin-bottom:8px;">
                @endif
                <div class="inv-school-name">{{ $institution->name }}</div>
                <div class="inv-school-sub">
                    {{ $institution->adresse ?? '' }}<br>
                    {{ $institution->telephone ?? '' }}
                    @if($institution->email) · {{ $institution->email }}@endif
                </div>
            </div>
            <div class="inv-meta">
                <div class="inv-title">REÇU</div>
                <div class="inv-num" id="inv-num">—</div>
                <div class="inv-date" id="inv-date">—</div>
            </div>
        </div>

        <div class="inv-parties">
            <div class="inv-party">
                <div class="inv-party-lbl">Établissement</div>
                <div class="inv-party-name">{{ $institution->name }}</div>
                <div class="inv-party-det">Année : <strong id="inv-annee">{{ $annee }}</strong><br>Émis par : <strong>{{ Auth::user()->name }}</strong></div>
            </div>
            <div class="inv-party">
                <div class="inv-party-lbl">Apprenant</div>
                <div class="inv-party-name" id="inv-ap-name">—</div>
                <div class="inv-party-det">Matricule : <strong id="inv-matricule">—</strong><br>Classe : <strong id="inv-classe">—</strong></div>
            </div>
        </div>

        <table class="inv-tbl">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Période</th>
                    <th>Mode</th>
                    <th>Montant encaissé</th>
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

        <div class="inv-totals">
            <div class="inv-total-row"><span>Total dû</span><span class="v" id="inv-du">0 FCFA</span></div>
            <div class="inv-total-row paid"><span>Montant encaissé</span><span class="v" id="inv-paye2">0 FCFA</span></div>
            <div class="inv-total-row rest" id="inv-reste-row"><span>Reste à payer</span><span class="v" id="inv-reste">0 FCFA</span></div>
            <div class="inv-total-row"><span>Statut</span><span class="v" id="inv-statut">—</span></div>
        </div>

        <div class="inv-banner" id="inv-banner">—</div>

        <div class="inv-footer">
            <div class="inv-footer-note">
                Ce reçu est émis par <strong>{{ $institution->name }}</strong>.<br>
                Conservez ce document comme preuve de paiement.<br>
                Pour toute réclamation, contactez l'administration.
            </div>
            <div class="inv-sig-block">
                <div class="inv-sig-line"></div>
                <div class="inv-sig-lbl">Signature &amp; Cachet</div>
                <div class="inv-sig-name">{{ Auth::user()->name }}</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════════
// DONNÉES GLOBALES (injectées depuis PHP)
// ══════════════════════════════════════════════
const MOIS_LABELS = @json($moisLabels);
const ANNEE       = '{{ $annee }}';
const INSTITUTION = '{{ addslashes($institution->name) }}';
const EMETTEUR    = '{{ addslashes(Auth::user()->name) }}';

// État courant du modal paiement
let payCtx = {};

// ══════════════════════════════════════════════
// NUMÉRO DE REÇU AUTOMATIQUE
// ══════════════════════════════════════════════
function generateRef() {
    const now = new Date();
    const yy  = String(now.getFullYear()).slice(2);
    const mm  = String(now.getMonth() + 1).padStart(2, '0');
    const dd  = String(now.getDate()).padStart(2, '0');
    const rnd = String(Math.floor(Math.random() * 9000) + 1000);
    const ref = `REC-${yy}${mm}${dd}-${rnd}`;
    document.getElementById('pay_ref').value = ref;
    return ref;
}

// ══════════════════════════════════════════════
// MODAL PAIEMENT
// ══════════════════════════════════════════════
function openPay() {
    // Sans contexte : afficher les sélects
    showPaySelects(true);
    document.getElementById('pay_ap_hidden').value = '';
    document.getElementById('pay_mois_hidden').value = '';
    document.getElementById('pay-title').textContent = 'Enregistrer un paiement';
    resetPayForm();
    openModal('modal-pay');
}

function openPayFor(appId, appName, matricule, classe) {
    // Avec contexte apprenant : masquer le sélect apprenant, garder mois visible
    payCtx = { appId, appName, matricule, classe };
    document.getElementById('pay_ap_hidden').value = appId;
    document.getElementById('pay-title').textContent = `Paiement — ${appName}`;

    // Masquer select apprenant, garder select mois
    document.getElementById('pay_ap_wrap').style.display  = 'none';
    document.getElementById('pay_mois_wrap').style.display = '';

    // Synchro mois → hidden
    const selMois = document.getElementById('pay_mois_sel');
    document.getElementById('pay_mois_hidden').value = selMois.value;
    selMois.onchange = function() {
        document.getElementById('pay_mois_hidden').value = this.value;
        const opt = this.options[this.selectedIndex];
        payCtx.moisLabel = opt.dataset.label || opt.textContent.trim();
    };
    // Init moisLabel
    if (selMois.value) {
        const opt = selMois.options[selMois.selectedIndex];
        payCtx.moisLabel = opt.dataset.label || opt.textContent.trim();
    }

    resetPayForm();
    openModal('modal-pay');
}

function showPaySelects(show) {
    document.getElementById('pay_ap_wrap').style.display   = show ? '' : 'none';
    document.getElementById('pay_mois_wrap').style.display = show ? '' : 'none';

    if (show) {
        const selAp   = document.getElementById('pay_ap_sel');
        const selMois = document.getElementById('pay_mois_sel');

        selAp.onchange = function() {
            document.getElementById('pay_ap_hidden').value = this.value;
            const opt = this.options[this.selectedIndex];
            payCtx.appId     = this.value;
            payCtx.appName   = opt.dataset.nom       || '';
            payCtx.matricule = opt.dataset.matricule || '';
            payCtx.classe    = opt.dataset.classe    || '';
        };
        selMois.onchange = function() {
            document.getElementById('pay_mois_hidden').value = this.value;
            const opt = this.options[this.selectedIndex];
            payCtx.mois      = this.value;
            payCtx.moisLabel = opt.dataset.label || opt.textContent.trim();
        };
    }
}

function resetPayForm() {
    document.getElementById('pay_du').value    = '';
    document.getElementById('pay_paye').value  = '';
    document.getElementById('pay_mode').value  = '';
    document.getElementById('pay_date').value  = new Date().toISOString().split('T')[0];
    generateRef();
    updateReste();
}

function closePay() {
    closeModal('modal-pay');
}

function updateReste() {
    const du    = parseFloat(document.getElementById('pay_du').value)   || 0;
    const paye  = parseFloat(document.getElementById('pay_paye').value) || 0;
    const reste = Math.max(0, du - paye);
    const elR   = document.getElementById('pay_reste_display');
    const elS   = document.getElementById('pay_statut_preview');

    elR.textContent = reste.toLocaleString('fr-FR') + ' FCFA';
    elR.style.color = reste > 0 ? 'var(--err)' : 'var(--ok)';

    if (paye >= du && du > 0) { elS.textContent = '✓ Soldé';   elS.style.color = 'var(--ok)'; }
    else if (paye > 0)         { elS.textContent = '~ Partiel'; elS.style.color = 'var(--warn)'; }
    else                       { elS.textContent = '✗ Impayé';  elS.style.color = 'var(--err)'; }
}

// Stocker les données avant soumission (pour impression après rechargement)
function storeForPrint() {
    const moisSel = document.getElementById('pay_mois_sel');
    let moisLabel = payCtx.moisLabel || '';
    if (!moisLabel && moisSel.value) {
        const opt = moisSel.options[moisSel.selectedIndex];
        moisLabel = opt ? (opt.dataset.label || opt.textContent.trim()) : '';
    }

    const data = {
        ref:       document.getElementById('pay_ref').value,
        du:        parseFloat(document.getElementById('pay_du').value)   || 0,
        paye:      parseFloat(document.getElementById('pay_paye').value) || 0,
        reste:     Math.max(0, (parseFloat(document.getElementById('pay_du').value)||0) - (parseFloat(document.getElementById('pay_paye').value)||0)),
        mode:      document.getElementById('pay_mode').value,
        date:      document.getElementById('pay_date').value,
        moisLabel: moisLabel || 'Mois',
        appName:   payCtx.appName   || '',
        matricule: payCtx.matricule  || '',
        classe:    payCtx.classe     || '',
        annee:     ANNEE,
        printAfter: true,
    };
    try { localStorage.setItem('lastInvoice', JSON.stringify(data)); } catch(e) {}
}

// ══════════════════════════════════════════════
// DRAWER — DOSSIER APPRENANT
// ══════════════════════════════════════════════
function openDossier(appId, appName, matricule, classe, niveau, filiere, du, paye, reste, records) {
    const overlay = document.getElementById('dossierOverlay');
    document.getElementById('drawerTitle').textContent = `Dossier — ${appName}`;

    const initials = appName.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
    const pct = du > 0 ? Math.min(100, Math.round(paye / du * 100)) : 0;
    const statutClass = pct >= 100 ? 'bdg-g' : pct > 0 ? 'bdg-a' : 'bdg-r';
    const statutLabel = pct >= 100 ? 'Soldé' : pct > 0 ? 'Partiel' : 'Impayé';

    const fmt = n => (n || 0).toLocaleString('fr-FR') + ' FCFA';

    // Construire les lignes de mois
    let moisHTML = '';
    for (const [mNum, mLabel] of Object.entries(MOIS_LABELS)) {
        const r = records[mNum];
        if (r) {
            const mpct  = r.montant_du > 0 ? Math.min(100, Math.round(r.montant_paye / r.montant_du * 100)) : 0;
            const mcls  = r.statut === 'paye' ? 'ok' : r.statut === 'partiel' ? 'warn' : 'err';
            const mmode = { especes:'Espèces', virement:'Virement', mobile_money:'Mobile Money', cheque:'Chèque', autre:'Autre', '':'—' }[r.mode_paiement || ''] || r.mode_paiement || '—';
            moisHTML += `
            <div class="mois-item ${r.statut}" onclick="openPayForMois(${appId},'${appName.replace(/'/g,"\\'")}','${matricule.replace(/'/g,"\\'")}','${classe.replace(/'/g,"\\'")}',${mNum},'${mLabel}',${r.montant_du},${r.montant_paye},'${r.mode_paiement||''}','${(r.reference||'').replace(/'/g,"\\'")}')">
                <div class="mois-item-lbl">${mLabel}</div>
                <div class="mois-item-bar"><div class="mois-item-fill ${mcls}" style="width:${mpct}%"></div></div>
                <div class="mois-item-info">
                    <div class="mois-item-paye">${fmt(r.montant_paye)}</div>
                    ${r.montant_reste > 0 ? `<div class="mois-item-reste">-${fmt(r.montant_reste)}</div>` : ''}
                </div>
            </div>`;
        } else {
            moisHTML += `
            <div class="mois-item vide" onclick="openPayForMois(${appId},'${appName.replace(/'/g,"\\'")}','${matricule.replace(/'/g,"\\'")}','${classe.replace(/'/g,"\\'")}',${mNum},'${mLabel}')">
                <div class="mois-item-lbl">${mLabel}</div>
                <div class="mois-item-bar"></div>
                <div class="mois-item-info">
                    <span class="mois-item-add">+ Ajouter</span>
                </div>
            </div>`;
        }
    }

    document.getElementById('drawerBody').innerHTML = `
        <div class="ap-identity">
            <div class="ap-avatar">${initials}</div>
            <div class="ap-info">
                <div class="ap-name">${appName}</div>
                <div class="ap-sub">${matricule}</div>
                <div class="ap-badges">
                    ${classe    ? `<span class="ap-badge">${classe}</span>`    : ''}
                    ${niveau    ? `<span class="ap-badge" style="background:#dcfce7;color:#15803d">${niveau}</span>`    : ''}
                    ${filiere   ? `<span class="ap-badge" style="background:#fef3c7;color:#b45309">${filiere}</span>`   : ''}
                    <span class="bdg ${statutClass}" style="font-size:.68rem;">${statutLabel}</span>
                </div>
            </div>
        </div>

        <div class="dossier-section">
            <div class="dossier-section-title">Récapitulatif · ${ANNEE}</div>
            <div class="fin-recap-grid">
                <div class="fin-recap-card">
                    <div class="fin-recap-val">${fmt(du)}</div>
                    <div class="fin-recap-lbl">Total dû</div>
                </div>
                <div class="fin-recap-card">
                    <div class="fin-recap-val" style="color:var(--ok)">${fmt(paye)}</div>
                    <div class="fin-recap-lbl">Payé · ${pct}%</div>
                </div>
                <div class="fin-recap-card">
                    <div class="fin-recap-val" style="color:${reste > 0 ? 'var(--err)' : 'var(--mist)'}">${fmt(reste)}</div>
                    <div class="fin-recap-lbl">Reste</div>
                </div>
            </div>
            <div class="prog-wrap" style="height:8px;margin-top:.25rem;">
                <div class="prog-bar ${pct >= 100 ? 'prog-ok' : pct >= 50 ? 'prog-warn' : 'prog-err'}" style="width:${pct}%"></div>
            </div>
        </div>

        <div class="dossier-section">
            <div class="dossier-section-title">Mensualités (cliquer pour modifier)</div>
            <div class="mois-list">${moisHTML}</div>
        </div>

        <div style="margin-top:1rem;">
            <button class="btn btn-gold" style="width:100%;justify-content:center;"
                    onclick="closeDossier();openPayFor(${appId},'${appName.replace(/'/g,"\\'")}','${matricule.replace(/'/g,"\\'")}','${classe.replace(/'/g,"\\'")}')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Saisir un nouveau paiement
            </button>
        </div>
    `;

    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeDossier() {
    document.getElementById('dossierOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

// Ouvrir le modal paiement pré-rempli depuis le drawer (mois spécifique)
function openPayForMois(appId, appName, matricule, classe, moisNum, moisLabel, du, paye, mode, ref) {
    closeDossier();
    payCtx = { appId, appName, matricule, classe, moisLabel };

    document.getElementById('pay_ap_hidden').value   = appId;
    document.getElementById('pay_mois_hidden').value = moisNum;
    document.getElementById('pay-title').textContent = `Paiement — ${appName} · ${moisLabel}`;
    document.getElementById('pay_ap_wrap').style.display   = 'none';
    document.getElementById('pay_mois_wrap').style.display = 'none';

    document.getElementById('pay_du').value    = du    || '';
    document.getElementById('pay_paye').value  = paye  || '';
    document.getElementById('pay_mode').value  = mode  || '';
    document.getElementById('pay_date').value  = new Date().toISOString().split('T')[0];

    if (ref && ref !== '') {
        document.getElementById('pay_ref').value = ref;
    } else {
        generateRef();
    }

    updateReste();
    openModal('modal-pay');
}

// ══════════════════════════════════════════════
// UTILITAIRES MODALS
// ══════════════════════════════════════════════
function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }

document.getElementById('modal-pay').addEventListener('click', function(e) {
    if (e.target === this) closePay();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closePay(); closeDossier(); }
});

// ══════════════════════════════════════════════
// FACTURE — CONSTRUCTION DOM + IMPRESSION
// ══════════════════════════════════════════════
function buildInvoice(d) {
    const fmt = n => (n || 0).toLocaleString('fr-FR') + ' FCFA';
    const modeMap = { especes:'Espèces', virement:'Virement bancaire', mobile_money:'Mobile Money', cheque:'Chèque', autre:'Autre', '':'—' };
    const statut  = d.paye >= d.du && d.du > 0 ? 'Soldé' : d.paye > 0 ? 'Partiel' : 'Impayé';
    const bannerCls = statut === 'Soldé' ? 'paid' : statut === 'Partiel' ? 'partial' : 'unpaid';
    const bannerMsg = statut === 'Soldé' ? '✓ PAIEMENT COMPLET — SOLDÉ'
                    : statut === 'Partiel' ? '⚠ PAIEMENT PARTIEL — RESTE À PAYER'
                    : '✗ AUCUN PAIEMENT ENREGISTRÉ';

    document.getElementById('inv-num').textContent   = d.ref || '—';
    document.getElementById('inv-date').textContent  = d.date
        ? new Date(d.date).toLocaleDateString('fr-FR', {day:'2-digit',month:'long',year:'numeric'}) : '—';
    document.getElementById('inv-ap-name').textContent   = d.appName   || '—';
    document.getElementById('inv-matricule').textContent = d.matricule  || '—';
    document.getElementById('inv-classe').textContent    = d.classe     || '—';
    document.getElementById('inv-mois').textContent      = d.moisLabel  || '—';
    document.getElementById('inv-mode').textContent      = modeMap[d.mode] || d.mode || '—';
    document.getElementById('inv-paye').textContent      = fmt(d.paye);
    document.getElementById('inv-du').textContent        = fmt(d.du);
    document.getElementById('inv-paye2').textContent     = fmt(d.paye);
    document.getElementById('inv-reste').textContent     = fmt(d.reste);
    document.getElementById('inv-statut').textContent    = statut;
    document.getElementById('inv-annee').textContent     = d.annee || ANNEE;
    document.getElementById('inv-reste-row').style.display = d.reste > 0 ? 'flex' : 'none';

    const banner = document.getElementById('inv-banner');
    banner.textContent = bannerMsg;
    banner.className   = `inv-banner ${bannerCls}`;

    const wm = document.getElementById('inv-watermark');
    wm.textContent = statut === 'Soldé' ? 'PAYÉ' : '';
}

function printLastInvoice() {
    try {
        const d = JSON.parse(localStorage.getItem('lastInvoice') || '{}');
        if (d && d.ref) { buildInvoice(d); window.print(); }
        else alert('Aucun reçu disponible. Veuillez d\'abord enregistrer un paiement.');
    } catch(e) { alert('Impossible de récupérer le reçu.'); }
}

// ══════════════════════════════════════════════
// IMPRESSION AUTO APRÈS RECHARGEMENT
// ══════════════════════════════════════════════
window.addEventListener('load', function() {
    try {
        const d = JSON.parse(localStorage.getItem('lastInvoice') || '{}');
        if (d && d.printAfter) {
            d.printAfter = false;
            localStorage.setItem('lastInvoice', JSON.stringify(d));
            setTimeout(() => { buildInvoice(d); window.print(); }, 700);
        }
    } catch(e) {}
});

// ══════════════════════════════════════════════
// RECHERCHE EN TEMPS RÉEL (debounce 400ms)
// ══════════════════════════════════════════════
(function() {
    const input = document.getElementById('searchInput');
    const form  = document.getElementById('searchForm');
    if (!input || !form) return;

    let timer;
    input.addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 400);
    });
})();
</script>
@endpush