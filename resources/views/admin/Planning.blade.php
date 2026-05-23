@extends('admin.master')
@php
use App\Models\SeanceCours;
@endphp
@push('styles')
<style>
/* ══ DESIGN SYSTEM PLANNING ══ */
:root {
    --p-ink:    #0c1220;
    --p-muted:  #64748b;
    --p-border: #e2e8f0;
    --p-bg:     #f8fafc;
    --p-white:  #ffffff;
    --p-blue:   #2563eb; --p-blue-l:  #dbeafe;
    --p-amber:  #d97706; --p-amber-l: #fef3c7;
    --p-red:    #dc2626; --p-red-l:   #fee2e2;
    --p-green:  #16a34a; --p-green-l: #dcfce7;
    --p-violet: #7c3aed; --p-violet-l:#ede9fe;
    --p-teal:   #0d9488; --p-teal-l:  #ccfbf1;
    --p-gray:   #64748b; --p-gray-l:  #f1f5f9;
}

/* ── PAGE ── */
.plan-page { display:flex;flex-direction:column;gap:1.5rem; }

/* ── HERO ── */
.plan-hero {
    background:linear-gradient(135deg,#0c1220 0%,#1e3a5f 55%,#0c2a1a 100%);
    border-radius:16px;padding:1.75rem 2rem;
    display:flex;align-items:center;justify-content:space-between;gap:1rem;
    position:relative;overflow:hidden;
}
.plan-hero::before {
    content:'';position:absolute;inset:0;
    background:repeating-linear-gradient(45deg,rgba(255,255,255,.015) 0,rgba(255,255,255,.015) 1px,transparent 0,transparent 24px);
    background-size:24px 24px;
}
.plan-hero-l { position:relative;z-index:1; }
.plan-hero-l h1 { font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 .3rem; }
.plan-hero-l p  { font-size:.83rem;color:rgba(255,255,255,.5);margin:0; }
.plan-hero-badges { display:flex;gap:.625rem;margin-top:1rem;flex-wrap:wrap; }
.plan-hero-badge {
    display:inline-flex;align-items:center;gap:.4rem;
    background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
    border-radius:20px;padding:.3rem .8rem;font-size:.75rem;font-weight:600;color:rgba(255,255,255,.85);
}
.plan-hero-badge .dot { width:6px;height:6px;border-radius:50%; }
.plan-hero-r { position:relative;z-index:1;display:flex;gap:.75rem;flex-wrap:wrap;align-items:center; }

/* ── TABS ── */
.plan-tabs {
    display:flex;gap:0;background:#f1f5f9;
    border-radius:12px;padding:.25rem;overflow-x:auto;scrollbar-width:none;
}
.plan-tabs::-webkit-scrollbar { display:none; }
.plan-tab {
    display:flex;align-items:center;gap:.5rem;
    padding:.6rem 1.1rem;border-radius:9px;
    font-size:.82rem;font-weight:500;color:var(--p-muted);
    white-space:nowrap;cursor:pointer;border:none;background:transparent;
    transition:all .2s;flex-shrink:0;
}
.plan-tab:hover { color:var(--p-ink);background:rgba(255,255,255,.6); }
.plan-tab.active { background:var(--p-white);color:var(--p-ink);font-weight:700;
    box-shadow:0 1px 4px rgba(0,0,0,.09); }
.plan-tab svg { width:15px;height:15px; }
.plan-tab .chip {
    background:rgba(0,0,0,.08);padding:.1rem .45rem;border-radius:99px;
    font-size:.68rem;font-weight:700;
}
.plan-tab.active .chip { background:var(--p-blue-l);color:var(--p-blue); }

.plan-panel { display:none; }
.plan-panel.active { display:block; animation:fadeUp .2s ease; }
@keyframes fadeUp { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:none} }

/* ── KPI STRIP ── */
.kpi-strip { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem; }
.kpi-box {
    background:var(--p-white);border:1px solid var(--p-border);border-radius:12px;
    padding:.9rem 1.1rem;box-shadow:0 1px 3px rgba(0,0,0,.04);
    position:relative;overflow:hidden;
}
.kpi-box::before {
    content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:12px 12px 0 0;
}
.kpi-box.blue::before   { background:var(--p-blue); }
.kpi-box.amber::before  { background:var(--p-amber); }
.kpi-box.green::before  { background:var(--p-green); }
.kpi-box.violet::before { background:var(--p-violet); }
.kpi-box-val   { font-size:1.75rem;font-weight:800;color:var(--p-ink);line-height:1; }
.kpi-box-label { font-size:.7rem;color:var(--p-muted);margin-top:.25rem; }

/* ── GRILLE EDT ── */
.edt-filters {
    display:flex;gap:.625rem;flex-wrap:wrap;align-items:center;
    padding:.875rem;background:var(--p-bg);border-bottom:1px solid var(--p-border);
}
.edt-select {
    border:1px solid var(--p-border);border-radius:8px;
    padding:.4rem .75rem;font-size:.78rem;outline:none;background:var(--p-white);
}
.edt-select:focus { border-color:var(--p-blue); }

.edt-grid {
    display:grid;grid-template-columns:repeat(6,1fr);
    border-top:1px solid var(--p-border);
    overflow-x:auto;
}
.edt-day-col {}
.edt-day-header {
    padding:.75rem .5rem;text-align:center;
    background:var(--p-bg);border-right:1px solid var(--p-border);
    border-bottom:1px solid var(--p-border);
    font-size:.75rem;font-weight:700;color:var(--p-ink);
    position:sticky;top:0;z-index:5;
}
.edt-day-body {
    padding:.5rem;border-right:1px solid var(--p-border);
    min-height:220px;display:flex;flex-direction:column;gap:.5rem;
    background:var(--p-white);
}
.edt-day-col:last-child .edt-day-header,
.edt-day-col:last-child .edt-day-body { border-right:none; }

/* Créneau */
.edt-creneau {
    border-radius:8px;padding:.5rem .625rem;font-size:.72rem;
    cursor:pointer;transition:all .18s;border-left:3px solid;
    position:relative;
}
.edt-creneau:hover { filter:brightness(.92);transform:scale(1.01); }
.edt-creneau .cren-time { font-weight:700;font-size:.68rem;color:rgba(0,0,0,.5); }
.edt-creneau .cren-subject { font-weight:700;color:var(--p-ink);margin:.15rem 0; }
.edt-creneau .cren-info  { font-size:.66rem;color:rgba(0,0,0,.5); }
.edt-creneau .cren-del {
    position:absolute;top:3px;right:3px;
    width:16px;height:16px;border-radius:50%;
    background:rgba(0,0,0,.12);color:rgba(0,0,0,.4);
    display:none;align-items:center;justify-content:center;
    font-size:.65rem;cursor:pointer;border:none;
}
.edt-creneau:hover .cren-del { display:flex; }

/* Slot vide — bouton + */
.edt-add-btn {
    border:1.5px dashed var(--p-border);border-radius:8px;
    padding:.5rem;text-align:center;font-size:.72rem;color:var(--p-muted);
    cursor:pointer;transition:all .18s;
}
.edt-add-btn:hover { border-color:var(--p-blue);color:var(--p-blue);background:var(--p-blue-l); }

/* ── SÉANCES TABLE ── */
.seances-table { width:100%;border-collapse:collapse; }
.seances-table th {
    background:var(--p-bg);padding:.6rem 1rem;
    text-align:left;font-size:.67rem;font-weight:700;
    text-transform:uppercase;color:var(--p-muted);letter-spacing:.06em;
    border-bottom:1px solid var(--p-border);
}
.seances-table td {
    padding:.75rem 1rem;border-bottom:1px solid #f1f5f9;
    font-size:.82rem;color:var(--p-ink);vertical-align:middle;
}
.seances-table tr:last-child td { border-bottom:none; }
.seances-table tr:hover td { background:var(--p-bg); }

/* ── PAIEMENTS TABLE ── */
.paie-table { width:100%;border-collapse:collapse; }
.paie-table th {
    background:var(--p-bg);padding:.6rem 1rem;
    text-align:left;font-size:.67rem;font-weight:700;
    text-transform:uppercase;color:var(--p-muted);letter-spacing:.06em;
    border-bottom:1px solid var(--p-border);
}
.paie-table td {
    padding:.75rem 1rem;border-bottom:1px solid #f1f5f9;
    font-size:.82rem;color:var(--p-ink);vertical-align:middle;
}
.paie-table tr:last-child td { border-bottom:none; }
.paie-table tr:hover td { background:var(--p-bg); }

/* Deadline urgency */
.deadline-chip {
    display:inline-flex;align-items:center;gap:.3rem;
    padding:.2rem .65rem;border-radius:20px;font-size:.68rem;font-weight:700;
}
.dl-past    { background:var(--p-red-l);   color:var(--p-red); }
.dl-soon    { background:var(--p-amber-l); color:var(--p-amber); }
.dl-ok      { background:var(--p-green-l); color:var(--p-green); }

/* ── BADGES ── */
.p-badge {
    display:inline-flex;align-items:center;padding:.18rem .6rem;
    border-radius:20px;font-size:.68rem;font-weight:700;white-space:nowrap;
}
.pb-blue   { background:var(--p-blue-l);  color:var(--p-blue); }
.pb-amber  { background:var(--p-amber-l); color:var(--p-amber); }
.pb-red    { background:var(--p-red-l);   color:var(--p-red); }
.pb-green  { background:var(--p-green-l); color:var(--p-green); }
.pb-violet { background:var(--p-violet-l);color:var(--p-violet); }
.pb-teal   { background:var(--p-teal-l);  color:var(--p-teal); }
.pb-gray   { background:var(--p-gray-l);  color:var(--p-gray); }

/* ── CARD ── */
.plan-card {
    background:var(--p-white);border:1px solid var(--p-border);
    border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);
}
.plan-card-header {
    padding:.875rem 1.25rem;border-bottom:1px solid var(--p-border);
    display:flex;align-items:center;justify-content:space-between;background:var(--p-bg);
}
.plan-card-header h3 { font-size:.875rem;font-weight:700;color:var(--p-ink);margin:0; }

/* ── FORM ── */
.pf-input, .pf-select, .pf-textarea {
    width:100%;border:1px solid var(--p-border);border-radius:8px;
    padding:.5rem .75rem;font-size:.82rem;outline:none;
    background:var(--p-white);transition:border-color .2s;
}
.pf-input:focus, .pf-select:focus, .pf-textarea:focus { border-color:var(--p-blue); }
.pf-label { display:block;font-size:.75rem;font-weight:600;color:var(--p-ink);margin-bottom:.3rem; }
.pf-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:.75rem; }
.pf-grid-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:.75rem; }

/* ── MODAL ── */
.plan-modal {
    position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);
    z-index:200;display:none;align-items:center;justify-content:center;padding:1rem;
}
.plan-modal.open { display:flex; }
.plan-modal-box {
    background:var(--p-white);border-radius:16px;
    width:100%;max-width:600px;max-height:90vh;overflow-y:auto;
    box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .22s ease;
}
@keyframes modalIn { from{opacity:0;transform:translateY(14px)scale(.97)} to{opacity:1;transform:none} }
.plan-modal-header {
    padding:1.1rem 1.5rem;border-bottom:1px solid var(--p-border);
    display:flex;align-items:center;justify-content:space-between;
    position:sticky;top:0;background:var(--p-white);z-index:5;
}
.plan-modal-header h2 { font-size:.95rem;font-weight:700;color:var(--p-ink);margin:0; }
.plan-modal-body   { padding:1.5rem; }
.plan-modal-footer {
    padding:.875rem 1.5rem;border-top:1px solid var(--p-border);
    display:flex;justify-content:flex-end;gap:.625rem;
    position:sticky;bottom:0;background:var(--p-white);
}
.modal-close {
    width:30px;height:30px;border-radius:8px;border:1px solid var(--p-border);
    background:var(--p-bg);cursor:pointer;display:flex;align-items:center;justify-content:center;
    color:var(--p-muted);transition:all .18s;
}
.modal-close:hover { background:var(--p-red-l);border-color:var(--p-red);color:var(--p-red); }

/* ── BUTTONS ── */
.btn-p {
    display:inline-flex;align-items:center;gap:.4rem;
    padding:.5rem 1rem;border-radius:8px;font-size:.8rem;font-weight:600;
    cursor:pointer;border:none;transition:all .18s;text-decoration:none;
}
.btn-primary-p { background:var(--p-ink);color:#fff; }
.btn-primary-p:hover { background:#1e2435; }
.btn-outline-p { background:var(--p-white);color:var(--p-muted);border:1px solid var(--p-border); }
.btn-outline-p:hover { background:var(--p-bg); }
.btn-sm-p { padding:.35rem .75rem;font-size:.74rem; }

.action-btn-p {
    width:28px;height:28px;border-radius:7px;border:1px solid var(--p-border);
    background:var(--p-white);display:inline-flex;align-items:center;justify-content:center;
    cursor:pointer;transition:all .18s;color:var(--p-muted);
}
.action-btn-p:hover { border-color:var(--p-blue);color:var(--p-blue);background:var(--p-blue-l); }
.action-btn-p.danger:hover { border-color:var(--p-red);color:var(--p-red);background:var(--p-red-l); }
.action-btn-p svg { width:13px;height:13px; }

/* ── FLASH ── */
.plan-flash {
    position:fixed;top:1rem;right:1rem;z-index:9999;
    padding:.75rem 1.25rem;border-radius:10px;
    display:flex;align-items:center;gap:.625rem;
    font-size:.83rem;font-weight:500;
    box-shadow:0 8px 24px rgba(0,0,0,.12);
    animation:slideIn .3s ease,fadeOut .35s 4.5s ease forwards;
}
.plan-flash.success { background:var(--p-green-l);color:var(--p-green);border:1px solid #bbf7d0; }
.plan-flash.error   { background:var(--p-red-l);color:var(--p-red);border:1px solid #fecaca; }
@keyframes slideIn { from{transform:translateX(20px);opacity:0} to{transform:none;opacity:1} }
@keyframes fadeOut { from{opacity:1} to{opacity:0;pointer-events:none} }

/* ── TIMELINE paiements ── */
.pay-timeline { display:flex;flex-direction:column;gap:0; }
.pay-tl-item {
    display:flex;gap:1rem;align-items:flex-start;
    padding:.875rem 1.25rem;border-bottom:1px solid var(--p-bg);
    transition:background .15s;
}
.pay-tl-item:last-child { border-bottom:none; }
.pay-tl-item:hover { background:var(--p-bg); }
.pay-tl-dot {
    width:36px;height:36px;border-radius:10px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:1rem;
}
.pay-tl-main { flex:1; }
.pay-tl-libelle { font-size:.875rem;font-weight:600;color:var(--p-ink); }
.pay-tl-sub { font-size:.75rem;color:var(--p-muted);margin-top:.15rem; }
.pay-tl-montant { font-size:1.1rem;font-weight:800;color:var(--p-ink);font-family:monospace; }
.pay-tl-date { font-size:.72rem;color:var(--p-muted); }

@media(max-width:1024px) {
    .edt-grid { grid-template-columns:repeat(3,1fr); }
    .kpi-strip { grid-template-columns:1fr 1fr; }
    .pf-grid-3 { grid-template-columns:1fr 1fr; }
}
@media(max-width:640px) {
    .edt-grid { grid-template-columns:1fr; }
    .kpi-strip { grid-template-columns:1fr 1fr; }
    .pf-grid-2,.pf-grid-3 { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')
@php
    $typeColorMap = [
        'cours'      => '#3b82f6',
        'evaluation' => '#f59e0b',
        'examen'     => '#ef4444',
        'rattrapage' => '#8b5cf6',
        'activite'   => '#10b981',
        'pause'      => '#9ca3af',
    ];
    $typeBgMap = [
        'cours'      => '#eff6ff',
        'evaluation' => '#fffbeb',
        'examen'     => '#fef2f2',
        'rattrapage' => '#f5f3ff',
        'activite'   => '#f0fdf4',
        'pause'      => '#f9fafb',
    ];
    $seanceStatutBadge = [
        'planifiee' => 'pb-blue',
        'realisee'  => 'pb-green',
        'annulee'   => 'pb-red',
        'reportee'  => 'pb-amber',
    ];
    $fraisIcon = [
        'inscription'=>'📋','scolarite'=>'🎓','examen'=>'📝',
        'tenue'=>'👕','transport'=>'🚌','cantine'=>'🍽️','activite'=>'⚽','autre'=>'💳',
    ];
@endphp

{{-- FLASH --}}
@if(session('success'))
<div class="plan-flash success">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="plan-flash error">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{ session('error') }}
</div>
@endif

<div class="plan-page">

{{-- ── HERO ── --}}
<div class="plan-hero">
    <div class="plan-hero-l">
        <h1>📅 Planning & Programmes</h1>
        <p>{{ $institution->name }} — Année {{ $annee }}</p>
        <div class="plan-hero-badges">
            <span class="plan-hero-badge"><span class="dot" style="background:#60a5fa;"></span>{{ $stats['total_creneaux'] }} créneaux EDT</span>
            <span class="plan-hero-badge"><span class="dot" style="background:#4ade80;"></span>{{ $stats['cours_semaine'] }} séances cette semaine</span>
            <span class="plan-hero-badge"><span class="dot" style="background:#fbbf24;"></span>{{ $stats['echeances_proch'] }} échéance(s) dans 30 jours</span>
        </div>
    </div>
    <div class="plan-hero-r">
        <div style="font-size:2.5rem;font-weight:800;color:#fff;font-family:monospace;line-height:1;">
            {{ $stats['realisees'] }}/{{ $stats['cours_semaine'] }}
        </div>
        <div style="font-size:.72rem;color:rgba(255,255,255,.45);">séances réalisées<br>cette semaine</div>
    </div>
</div>

{{-- ── KPI STRIP ── --}}
<div class="kpi-strip">
    <div class="kpi-box blue">
        <div class="kpi-box-val">{{ $stats['total_creneaux'] }}</div>
        <div class="kpi-box-label">Créneaux actifs</div>
    </div>
    <div class="kpi-box amber">
        <div class="kpi-box-val">{{ $stats['cours_semaine'] }}</div>
        <div class="kpi-box-label">Séances cette semaine</div>
    </div>
    <div class="kpi-box green">
        <div class="kpi-box-val">{{ $stats['realisees'] }}</div>
        <div class="kpi-box-label">Séances réalisées</div>
    </div>
    <div class="kpi-box violet">
        <div class="kpi-box-val">{{ $programmes->count() }}</div>
        <div class="kpi-box-label">Échéances de paiement</div>
    </div>
</div>

{{-- ── TABS ── --}}
<div class="plan-tabs" id="plan-tabs">
    <button class="plan-tab active" onclick="switchPlanTab('edt',this)" data-tab="edt">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Emploi du temps
        <span class="chip">{{ $stats['total_creneaux'] }}</span>
    </button>
    <button class="plan-tab" onclick="switchPlanTab('seances',this)" data-tab="seances">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Séances & Cours
        <span class="chip">{{ $stats['cours_semaine'] }}</span>
    </button>
    <button class="plan-tab" onclick="switchPlanTab('paiements',this)" data-tab="paiements">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1"/>
        </svg>
        Programmes de paiement
        <span class="chip">{{ $programmes->count() }}</span>
    </button>
</div>

{{-- ══════════ PANEL 1 : EMPLOI DU TEMPS ══════════ --}}
<div class="plan-panel active" id="panel-edt">
    <div class="plan-card">
        {{-- Filtres + bouton ajouter --}}
        <div class="edt-filters">
            <form method="GET" action="{{ route('admin.planning') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;flex:1;">
                <select name="classe_id" class="edt-select" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $classeId==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="teacher_id" class="edt-select" onchange="this.form.submit()">
                    <option value="">Tous les enseignants</option>
                    @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ $teacherId==$t->id?'selected':'' }}>{{ $t->prenom }} {{ $t->nom }}</option>
                    @endforeach
                </select>
                <select name="jour" class="edt-select" onchange="this.form.submit()">
                    <option value="">Tous les jours</option>
                    @foreach($jourLabels as $j => $jl)
                    <option value="{{ $j }}" {{ $jour==$j?'selected':'' }}>{{ $jl }}</option>
                    @endforeach
                </select>
                @if($classeId || $teacherId || $jour)
                <a href="{{ route('admin.planning') }}#edt" class="btn-p btn-outline-p btn-sm-p">✕ Effacer</a>
                @endif
            </form>
            <button class="btn-p btn-primary-p btn-sm-p" onclick="openPlanModal('modal-add-edt')">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter un créneau
            </button>
        </div>

        {{-- Grille hebdomadaire --}}
        <div style="overflow-x:auto;">
            <div class="edt-grid">
                @foreach($jourLabels as $jour => $jourLabel)
                <div class="edt-day-col">
                    <div class="edt-day-header">
                        {{ $jourLabel }}
                        @if($grille[$jour]->count())
                        <span style="display:block;font-size:.62rem;color:var(--p-muted);font-weight:500;">
                            {{ $grille[$jour]->count() }} créneau(x)
                        </span>
                        @endif
                    </div>
                    <div class="edt-day-body">
                        @forelse($grille[$jour] as $cr)
                        @php
                            $crColor = $typeColorMap[$cr->type] ?? '#64748b';
                            $crBg    = $typeBgMap[$cr->type]    ?? '#f9fafb';
                        @endphp
                        <div class="edt-creneau"
                            style="background:{{ $crBg }};border-left-color:{{ $crColor }};">
                            <div class="cren-time">{{ substr($cr->heure_debut,0,5) }} – {{ substr($cr->heure_fin,0,5) }}</div>
                            <div class="cren-subject">{{ $cr->subject?->name ?? 'Sans matière' }}</div>
                            <div class="cren-info">
                                @if($cr->teacher) {{ $cr->teacher->prenom }} {{ $cr->teacher->nom }} @endif
                                @if($cr->classe) · {{ $cr->classe->name }} @endif
                                @if($cr->salle) · 🏛️ {{ $cr->salle }} @endif
                            </div>
                            <span class="p-badge" style="background:{{ $crBg }};color:{{ $crColor }};margin-top:.3rem;display:inline-flex;">
                                {{ $typeLabels[$cr->type] ?? $cr->type }}
                            </span>
                            <form method="POST" action="{{ route('admin.planning.edt.destroy', $cr->id) }}" style="display:inline;"
                                onsubmit="return confirm('Supprimer ce créneau ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="cren-del" title="Supprimer">✕</button>
                            </form>
                        </div>
                        @empty
                        <div class="edt-add-btn" onclick="openAddEdtForDay('{{ $jour }}')">
                            + Ajouter
                        </div>
                        @endforelse
                        @if($grille[$jour]->count() > 0)
                        <div class="edt-add-btn" onclick="openAddEdtForDay('{{ $jour }}')">+ Ajouter</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ══════════ PANEL 2 : SÉANCES ══════════ --}}
<div class="plan-panel" id="panel-seances">
    <div style="display:grid;grid-template-columns:350px 1fr;gap:1.25rem;align-items:start;">

        {{-- Formulaire nouvelle séance --}}
        <div class="plan-card">
            <div class="plan-card-header"><h3>Enregistrer une séance</h3></div>
            <div style="padding:1.25rem;">
                <form method="POST" action="{{ route('admin.planning.seance.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="pf-label">Classe *</label>
                        <select name="classe_id" class="pf-select" required>
                            <option value="">Choisir…</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Matière</label>
                            <select name="subject_id" class="pf-select">
                                <option value="">—</option>
                                @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="pf-label">Enseignant</label>
                            <select name="teacher_id" class="pf-select">
                                <option value="">—</option>
                                @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->prenom }} {{ $t->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="pf-label">Date *</label>
                        <input type="date" name="date_seance" class="pf-input" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Début *</label>
                            <input type="time" name="heure_debut" class="pf-input" required>
                        </div>
                        <div>
                            <label class="pf-label">Fin *</label>
                            <input type="time" name="heure_fin" class="pf-input" required>
                        </div>
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Type *</label>
                            <select name="type" class="pf-select" required>
                                @foreach($typeLabels as $v => $l)
                                @if($v !== 'pause')
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="pf-label">Statut *</label>
                            <select name="statut" class="pf-select" required>
                                <option value="planifiee">Planifiée</option>
                                <option value="realisee">Réalisée</option>
                                <option value="annulee">Annulée</option>
                                <option value="reportee">Reportée</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="pf-label">Titre / Thème</label>
                        <input type="text" name="titre" class="pf-input" placeholder="Ex : Chapitre 3 — Dérivées">
                    </div>
                    <div>
                        <label class="pf-label">Salle</label>
                        <input type="text" name="salle" class="pf-input" placeholder="Ex : Salle B12">
                    </div>
                    <button type="submit" class="btn-p btn-primary-p" style="width:100%;justify-content:center;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer
                    </button>
                </form>
            </div>
        </div>

        {{-- Tableau séances --}}
        <div class="plan-card">
            <div class="plan-card-header">
                <h3>Séances de la semaine</h3>
                <span style="font-size:.75rem;color:var(--p-muted);">
                    {{ now()->startOfWeek()->format('d/m') }} – {{ now()->endOfWeek()->subDay()->format('d/m/Y') }}
                </span>
            </div>
            <div style="overflow-x:auto;">
                <table class="seances-table">
                    <thead>
                        <tr>
                            <th>Date</th><th>Classe</th><th>Matière</th>
                            <th>Enseignant</th><th>Horaire</th><th>Type</th>
                            <th>Statut</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seancesSemaine as $s)
                        <tr>
                            <td style="white-space:nowrap;font-size:.78rem;">{{ $s->date_seance?->format('D d/m') }}</td>
                            <td style="font-weight:600;">{{ $s->classe?->name ?? '—' }}</td>
                            <td>{{ $s->subject?->name ?? '—' }}</td>
                            <td style="font-size:.78rem;color:var(--p-muted);">
                                {{ $s->teacher?->prenom ?? '' }} {{ $s->teacher?->nom ?? '—' }}
                            </td>
                            <td style="font-size:.75rem;white-space:nowrap;">
                                {{ substr($s->heure_debut,0,5) }}–{{ substr($s->heure_fin,0,5) }}
                            </td>
                            <td>
                                <span class="p-badge pb-blue">{{ $typeLabels[$s->type] ?? $s->type }}</span>
                            </td>
                            <td>
                                <span class="p-badge {{ $seanceStatutBadge[$s->statut] ?? 'pb-gray' }}">
                                    {{ SeanceCours::statutLabels()[$s->statut] ?? $s->statut }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:.3rem;">
                                    <button class="action-btn-p" title="Modifier"
                                        onclick="openEditSeance({{ $s->id }}, '{{ $s->statut }}', '{{ addslashes($s->titre ?? '') }}', '{{ $s->salle ?? '' }}', '{{ addslashes($s->motif_annulation ?? '') }}')">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.planning.seance.destroy', $s->id) }}"
                                        onsubmit="return confirm('Supprimer cette séance ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn-p danger">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:2.5rem;color:var(--p-muted);">
                                Aucune séance enregistrée cette semaine.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ══════════ PANEL 3 : PROGRAMMES PAIEMENT ══════════ --}}
<div class="plan-panel" id="panel-paiements">
    <div style="display:grid;grid-template-columns:360px 1fr;gap:1.25rem;align-items:start;">

        {{-- Formulaire nouvelle échéance --}}
        <div class="plan-card">
            <div class="plan-card-header"><h3>Nouvelle échéance</h3></div>
            <div style="padding:1.25rem;">
                <form method="POST" action="{{ route('admin.planning.paiement.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="pf-label">Libellé *</label>
                        <input type="text" name="libelle" class="pf-input" required
                            placeholder="Ex : 1ère tranche — Inscription">
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Type de frais *</label>
                            <select name="type_frais" class="pf-select" required>
                                @foreach($typeFraisLabels as $v => $l)
                                <option value="{{ $v }}">{{ $fraisIcon[$v] ?? '' }} {{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="pf-label">Période *</label>
                            <select name="periode" class="pf-select" required>
                                @foreach($periodeLabels as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="pf-label">Montant ({{ $institution->devise ?? 'FCFA' }}) *</label>
                        <input type="number" name="montant" class="pf-input" required min="0" step="500"
                            placeholder="Ex : 50000">
                    </div>
                    <div>
                        <label class="pf-label">Date d'échéance *</label>
                        <input type="date" name="date_echeance" class="pf-input" required>
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Jours de grâce</label>
                            <input type="number" name="jours_grace" class="pf-input" value="0" min="0" max="60">
                        </div>
                        <div>
                            <label class="pf-label">Ordre</label>
                            <input type="number" name="ordre" class="pf-input"
                                value="{{ $programmes->max('ordre') + 1 }}" min="1">
                        </div>
                    </div>
                    <div>
                        <label class="pf-label">Cibler (optionnel)</label>
                        <select name="niveau_id" class="pf-select" style="margin-bottom:.5rem;">
                            <option value="">— Tous les niveaux —</option>
                            @foreach($niveaux as $n)
                            <option value="{{ $n->id }}">{{ $n->name }}</option>
                            @endforeach
                        </select>
                        <select name="classe_id" class="pf-select">
                            <option value="">— Toutes les classes —</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pf-label">Description</label>
                        <textarea name="description" class="pf-textarea" rows="2"
                            placeholder="Informations complémentaires…"></textarea>
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="obligatoire" value="1" id="oblig" checked style="width:auto;">
                        <label for="oblig" class="pf-label" style="margin:0;">Paiement obligatoire</label>
                    </div>
                    <button type="submit" class="btn-p btn-primary-p" style="width:100%;justify-content:center;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Créer l'échéance
                    </button>
                </form>
            </div>
        </div>

        {{-- Timeline paiements --}}
        <div class="plan-card">
            <div class="plan-card-header">
                <h3>Calendrier des échéances — {{ $annee }}</h3>
                <span class="p-badge pb-violet">{{ $programmes->count() }} échéance(s)</span>
            </div>

            @php
                $totalMontant = $programmes->sum('montant');
            @endphp
            @if($totalMontant > 0)
            <div style="padding:.875rem 1.25rem;background:#fafbfd;border-bottom:1px solid var(--p-border);
                        display:flex;gap:1.5rem;align-items:center;">
                <div>
                    <div style="font-size:1.4rem;font-weight:800;color:var(--p-ink);font-family:monospace;">
                        {{ number_format($totalMontant, 0, ',', ' ') }} {{ $institution->devise ?? 'FCFA' }}
                    </div>
                    <div style="font-size:.72rem;color:var(--p-muted);">Total des frais annuels</div>
                </div>
                <div style="flex:1;background:var(--p-bg);height:6px;border-radius:99px;overflow:hidden;">
                    @php
                        $payeCount = $programmes->where('statut','archive')->count();
                        $totalPay  = $programmes->count() ?: 1;
                    @endphp
                    <div style="height:100%;border-radius:99px;width:{{ round($payeCount/$totalPay*100) }}%;background:var(--p-green);"></div>
                </div>
            </div>
            @endif

            @forelse($programmes as $prog)
            @php
                $jr = $prog->jours_restants;
                $dlClass = $jr < 0 ? 'dl-past' : ($jr <= 7 ? 'dl-soon' : 'dl-ok');
                $dlText  = $jr < 0 ? abs($jr).'j en retard' : ($jr === 0 ? 'Aujourd\'hui !' : 'Dans '.$jr.'j');
                $icon = $fraisIcon[$prog->type_frais] ?? '💳';
                $bgColor = $prog->statut === 'archive' ? '#f0fdf4' : ($jr < 0 ? '#fef2f2' : ($jr <= 7 ? '#fffbeb' : '#fff'));
            @endphp
            <div class="pay-tl-item" style="background:{{ $bgColor }};">
                <div class="pay-tl-dot" style="background:{{ $jr < 0 ? 'var(--p-red-l)' : ($jr <= 7 ? 'var(--p-amber-l)' : 'var(--p-green-l)') }};">
                    {{ $icon }}
                </div>
                <div class="pay-tl-main">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
                        <div>
                            <div class="pay-tl-libelle">{{ $prog->libelle }}</div>
                            <div class="pay-tl-sub">
                                {{ $typeFraisLabels[$prog->type_frais] ?? $prog->type_frais }}
                                · {{ $periodeLabels[$prog->periode] ?? $prog->periode }}
                                @if($prog->classe) · {{ $prog->classe->name }}
                                @elseif($prog->niveau) · {{ $prog->niveau->name }}
                                @else · Tous @endif
                                @if(!$prog->obligatoire) <span class="p-badge pb-gray">Facultatif</span> @endif
                            </div>
                            @if($prog->description)
                            <div style="font-size:.72rem;color:var(--p-muted);margin-top:.2rem;font-style:italic;">{{ $prog->description }}</div>
                            @endif
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div class="pay-tl-montant">{{ number_format($prog->montant, 0, ',', ' ') }}</div>
                            <div class="pay-tl-date">{{ $prog->date_echeance?->format('d/m/Y') }}</div>
                            @if($prog->jours_grace > 0)
                            <div style="font-size:.65rem;color:var(--p-muted);">+{{ $prog->jours_grace }}j grâce</div>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.5rem;">
                        <div style="display:flex;gap:.4rem;align-items:center;">
                            <span class="deadline-chip {{ $dlClass }}">{{ $dlText }}</span>
                            @if($prog->statut === 'actif')
                                <span class="p-badge pb-green">Actif</span>
                            @elseif($prog->statut === 'suspendu')
                                <span class="p-badge pb-amber">Suspendu</span>
                            @else
                                <span class="p-badge pb-gray">Archivé</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:.3rem;">
                            <button class="action-btn-p" title="Modifier"
                                onclick="openEditPaiement({{ $prog->id }}, '{{ addslashes($prog->libelle) }}', {{ $prog->montant }}, '{{ $prog->date_echeance?->format('Y-m-d') }}', {{ $prog->jours_grace }}, '{{ $prog->statut }}', '{{ $prog->type_frais }}', '{{ addslashes($prog->description ?? '') }}', {{ $prog->obligatoire ? 'true':'false' }})">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('admin.planning.paiement.destroy', $prog->id) }}"
                                onsubmit="return confirm('Supprimer « {{ addslashes($prog->libelle) }} » ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn-p danger">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:3rem;color:var(--p-muted);">
                <div style="font-size:1.75rem;margin-bottom:.5rem;">💰</div>
                <div style="font-weight:700;">Aucune échéance de paiement</div>
                <div style="font-size:.82rem;margin-top:.25rem;">Créez votre premier programme depuis le formulaire.</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

</div>{{-- /plan-page --}}

{{-- ══ MODALS ══ --}}

{{-- Ajouter créneaux EDT --}}
<div class="plan-modal" id="modal-add-edt">
    <div class="plan-modal-box">
        <div class="plan-modal-header">
            <h2>Nouveau créneau EDT</h2>
            <button class="modal-close" onclick="closePlanModal('modal-add-edt')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.planning.edt.store') }}">
            @csrf
            <div class="plan-modal-body">
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Classe *</label>
                            <select name="classe_id" id="modal-edt-classe" class="pf-select" required>
                                <option value="">Choisir…</option>
                                @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="pf-label">Jour *</label>
                            <select name="jour" id="modal-edt-jour" class="pf-select" required>
                                @foreach($jourLabels as $j => $jl)
                                <option value="{{ $j }}">{{ $jl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Matière</label>
                            <select name="subject_id" class="pf-select">
                                <option value="">—</option>
                                @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="pf-label">Enseignant</label>
                            <select name="teacher_id" class="pf-select">
                                <option value="">—</option>
                                @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->prenom }} {{ $t->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="pf-grid-3">
                        <div>
                            <label class="pf-label">Début *</label>
                            <input type="time" name="heure_debut" class="pf-input" required value="08:00">
                        </div>
                        <div>
                            <label class="pf-label">Fin *</label>
                            <input type="time" name="heure_fin" class="pf-input" required value="10:00">
                        </div>
                        <div>
                            <label class="pf-label">Salle</label>
                            <input type="text" name="salle" class="pf-input" placeholder="Ex : B12">
                        </div>
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Type *</label>
                            <select name="type" class="pf-select" required>
                                @foreach($typeLabels as $v => $l)
                                <option value="{{ $v }}" {{ $v==='cours'?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="pf-label">Période</label>
                            <select name="periode" class="pf-select">
                                <option value="annee">Toute l'année</option>
                                <option value="trimestre1">Trimestre 1</option>
                                <option value="trimestre2">Trimestre 2</option>
                                <option value="trimestre3">Trimestre 3</option>
                                <option value="semestre1">Semestre 1</option>
                                <option value="semestre2">Semestre 2</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="pf-label">Notes</label>
                        <textarea name="notes" class="pf-textarea" rows="2" placeholder="Remarques…"></textarea>
                    </div>
                </div>
            </div>
            <div class="plan-modal-footer">
                <button type="button" class="btn-p btn-outline-p" onclick="closePlanModal('modal-add-edt')">Annuler</button>
                <button type="submit" class="btn-p btn-primary-p">Ajouter le créneau</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal édition séance --}}
<div class="plan-modal" id="modal-edit-seance">
    <div class="plan-modal-box" style="max-width:480px;">
        <div class="plan-modal-header">
            <h2>Modifier la séance</h2>
            <button class="modal-close" onclick="closePlanModal('modal-edit-seance')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="form-edit-seance">
            @csrf @method('PUT')
            <div class="plan-modal-body">
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    <div>
                        <label class="pf-label">Statut *</label>
                        <select name="statut" id="es-statut" class="pf-select" required>
                            <option value="planifiee">Planifiée</option>
                            <option value="realisee">Réalisée</option>
                            <option value="annulee">Annulée</option>
                            <option value="reportee">Reportée</option>
                        </select>
                    </div>
                    <div>
                        <label class="pf-label">Titre / Thème</label>
                        <input type="text" name="titre" id="es-titre" class="pf-input">
                    </div>
                    <div>
                        <label class="pf-label">Salle</label>
                        <input type="text" name="salle" id="es-salle" class="pf-input">
                    </div>
                    <div>
                        <label class="pf-label">Motif annulation / report</label>
                        <textarea name="motif_annulation" id="es-motif" class="pf-textarea" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="plan-modal-footer">
                <button type="button" class="btn-p btn-outline-p" onclick="closePlanModal('modal-edit-seance')">Annuler</button>
                <button type="submit" class="btn-p btn-primary-p">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal édition paiement --}}
<div class="plan-modal" id="modal-edit-paiement">
    <div class="plan-modal-box" style="max-width:500px;">
        <div class="plan-modal-header">
            <h2>Modifier l'échéance</h2>
            <button class="modal-close" onclick="closePlanModal('modal-edit-paiement')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="form-edit-paiement">
            @csrf @method('PUT')
            <div class="plan-modal-body">
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    <div>
                        <label class="pf-label">Libellé *</label>
                        <input type="text" name="libelle" id="ep-libelle" class="pf-input" required>
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Montant *</label>
                            <input type="number" name="montant" id="ep-montant" class="pf-input" required min="0">
                        </div>
                        <div>
                            <label class="pf-label">Date d'échéance *</label>
                            <input type="date" name="date_echeance" id="ep-date" class="pf-input" required>
                        </div>
                    </div>
                    <div class="pf-grid-2">
                        <div>
                            <label class="pf-label">Jours de grâce</label>
                            <input type="number" name="jours_grace" id="ep-grace" class="pf-input" min="0">
                        </div>
                        <div>
                            <label class="pf-label">Statut *</label>
                            <select name="statut" id="ep-statut" class="pf-select" required>
                                <option value="actif">Actif</option>
                                <option value="suspendu">Suspendu</option>
                                <option value="archive">Archivé</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="pf-label">Type de frais *</label>
                        <select name="type_frais" id="ep-type" class="pf-select" required>
                            @foreach($typeFraisLabels as $v => $l)
                            <option value="{{ $v }}">{{ $fraisIcon[$v] ?? '' }} {{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="pf-label">Description</label>
                        <textarea name="description" id="ep-desc" class="pf-textarea" rows="2"></textarea>
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="obligatoire" value="1" id="ep-oblig" style="width:auto;">
                        <label for="ep-oblig" class="pf-label" style="margin:0;">Paiement obligatoire</label>
                    </div>
                </div>
            </div>
            <div class="plan-modal-footer">
                <button type="button" class="btn-p btn-outline-p" onclick="closePlanModal('modal-edit-paiement')">Annuler</button>
                <button type="submit" class="btn-p btn-primary-p">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── TABS ──
function switchPlanTab(id, btn) {
    document.querySelectorAll('.plan-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.plan-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + id).classList.add('active');
    btn.classList.add('active');
    history.replaceState(null, '', '#' + id);
}
document.addEventListener('DOMContentLoaded', () => {
    const hash = location.hash.replace('#','');
    const valid = ['edt','seances','paiements'];
    if (valid.includes(hash)) {
        const btn = document.querySelector('[data-tab="'+hash+'"]');
        if (btn) switchPlanTab(hash, btn);
    }
});

// ── MODALS ──
function openPlanModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closePlanModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
document.querySelectorAll('.plan-modal').forEach(el =>
    el.addEventListener('click', e => { if(e.target===el) closePlanModal(el.id); })
);
document.addEventListener('keydown', e => {
    if(e.key==='Escape') document.querySelectorAll('.plan-modal.open').forEach(m => closePlanModal(m.id));
});

// Ouvrir modal EDT avec jour pré-sélectionné
function openAddEdtForDay(jour) {
    document.getElementById('modal-edt-jour').value = jour;
    openPlanModal('modal-add-edt');
}

// Éditer séance
function openEditSeance(id, statut, titre, salle, motif) {
    document.getElementById('form-edit-seance').action = '/admin/planning/seance/' + id;
    document.getElementById('es-statut').value = statut;
    document.getElementById('es-titre').value  = titre;
    document.getElementById('es-salle').value  = salle;
    document.getElementById('es-motif').value  = motif;
    openPlanModal('modal-edit-seance');
}

// Éditer paiement
function openEditPaiement(id, libelle, montant, date, grace, statut, type, desc, oblig) {
    document.getElementById('form-edit-paiement').action = '/admin/planning/paiement/' + id;
    document.getElementById('ep-libelle').value = libelle;
    document.getElementById('ep-montant').value = montant;
    document.getElementById('ep-date').value    = date;
    document.getElementById('ep-grace').value   = grace;
    document.getElementById('ep-statut').value  = statut;
    document.getElementById('ep-type').value    = type;
    document.getElementById('ep-desc').value    = desc;
    document.getElementById('ep-oblig').checked = oblig;
    openPlanModal('modal-edit-paiement');
}

// Auto-dismiss flash
setTimeout(() => document.querySelectorAll('.plan-flash').forEach(el => el.remove()), 5000);
</script>
@endpush