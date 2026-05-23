@extends('admin.master')

@push('styles')
<style>
/* ═══════════════════════════════════════════════
   SUIVI DISCIPLINAIRE — Responsive complet
   Mobile-first : 320px → 480px → 768px → 1024px+
═══════════════════════════════════════════════ */

html, body { height: auto !important; overflow-y: auto !important; }

:root {
    --slate:  #1e293b;
    --slate2: #334155;
    --muted:  #64748b;
    --border: #e2e8f0;
    --bg:     #f8fafc;
    --white:  #ffffff;
    --red:    #dc2626; --red-l:  #fee2e2;
    --amb:    #d97706; --amb-l:  #fef3c7;
    --grn:    #16a34a; --grn-l:  #dcfce7;
    --blu:    #2563eb; --blu-l:  #dbeafe;
    --vio:    #6d28d9; --vio-l:  #ede9fe;
    --r:      10px;
    --sh:     0 1px 4px rgba(0,0,0,.07);
}

/* ── RESET BOX-SIZING ── */
*, *::before, *::after { box-sizing: border-box; }

/* ── PAGE ── */
.dp { display: flex; flex-direction: column; gap: 1rem; padding: 0; }

/* ═══════════════ HERO ═══════════════ */
.dp-hero {
    background: linear-gradient(135deg, #1e293b 0%, #312e81 100%);
    border-radius: 14px;
    padding: 1.25rem;
    position: relative; overflow: hidden;
}
.dp-hero::before {
    content: ''; position: absolute; inset: 0;
    background: repeating-linear-gradient(45deg,
        rgba(255,255,255,.02) 0, rgba(255,255,255,.02) 1px,
        transparent 0, transparent 30px);
    pointer-events: none;
}
.dp-hero-inner {
    position: relative; z-index: 1;
    display: flex; flex-wrap: wrap;
    align-items: center; justify-content: space-between;
    gap: .875rem;
}
.dp-hero-title h1 {
    font-size: 1.15rem; font-weight: 800; color: #fff; margin: 0 0 .2rem;
}
.dp-hero-title p { font-size: .78rem; color: rgba(255,255,255,.5); margin: 0; }

.dp-hero-actions {
    display: flex; flex-wrap: wrap; gap: .5rem; align-items: center;
    width: 100%;
}
@media (min-width: 640px) {
    .dp-hero-actions { width: auto; }
    .dp-hero-title h1 { font-size: 1.25rem; }
}

.hero-select {
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff; border-radius: 8px;
    padding: .42rem .75rem; font-size: .8rem;
    cursor: pointer; outline: none; flex: 1; min-width: 130px;
}
.hero-select option { color: #000; }

.hero-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff; border-radius: 8px;
    padding: .42rem .875rem; font-size: .78rem; font-weight: 600;
    text-decoration: none; cursor: pointer;
    font-family: inherit; transition: background .2s;
    flex: 1; min-width: 130px;
}
.hero-btn:hover { background: rgba(255,255,255,.22); }

/* ═══════════════ KPI STRIP ═══════════════ */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: .625rem;
}
@media (min-width: 480px)  { .kpi-strip { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 900px)  { .kpi-strip { grid-template-columns: repeat(5, 1fr); } }

.kpi-card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: var(--r); padding: .875rem 1rem; box-shadow: var(--sh);
    position: relative; overflow: hidden;
}
.kpi-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0;
    height: 3px; border-radius: var(--r) var(--r) 0 0;
}
.kpi-card.red::before   { background: var(--red); }
.kpi-card.amber::before { background: var(--amb); }
.kpi-card.green::before { background: var(--grn); }
.kpi-card.blue::before  { background: var(--blu); }
.kpi-val   { font-size: 1.5rem; font-weight: 800; color: var(--slate); line-height: 1; }
.kpi-label { font-size: .69rem; color: var(--muted); margin-top: .2rem; line-height: 1.3; }

/* ═══════════════ LAYOUT ═══════════════ */
.dp-layout {
    display: flex; flex-direction: column; gap: 1rem;
}
@media (min-width: 900px) {
    .dp-layout { flex-direction: row; align-items: flex-start; }
    .dp-sidebar { width: 340px; flex-shrink: 0; }
    .dp-main    { flex: 1; min-width: 0; }
}
@media (min-width: 1100px) { .dp-sidebar { width: 360px; } }

/* ═══════════════ CARD ═══════════════ */
.card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: var(--r); box-shadow: var(--sh); overflow: hidden;
}
.card + .card { margin-top: .875rem; }
.card-head {
    padding: .75rem 1rem; border-bottom: 1px solid var(--border);
    background: var(--bg); display: flex; align-items: center; gap: .5rem;
}
.card-head h2 { font-size: .84rem; font-weight: 700; color: var(--slate); margin: 0; }
.card-body { padding: 1rem; }

/* ═══════════════ FORM FIELDS ═══════════════ */
.field { margin-bottom: .75rem; }
.field:last-child { margin-bottom: 0; }
.field label {
    display: block; font-size: .73rem; font-weight: 600;
    color: var(--slate2); margin-bottom: .28rem;
}
.field input,
.field select,
.field textarea {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: .5rem .75rem;
    font-size: .82rem;
    outline: none;
    background: var(--white);
    color: var(--slate);
    font-family: inherit;
    transition: border-color .2s;
    -webkit-appearance: none;
    appearance: none;
}
.field input:focus,
.field select:focus,
.field textarea:focus { border-color: #6366f1; }
.field textarea { resize: vertical; min-height: 70px; }

/* ── 2 colonnes sur ≥480px ── */
.field-row { display: flex; flex-direction: column; gap: .75rem; }
@media (min-width: 480px) {
    .field-row { flex-direction: row; }
    .field-row .field { flex: 1; margin-bottom: 0; }
}

/* ═══════════════ GRAVITÉ ═══════════════ */
.grav-btns { display: flex; gap: .4rem; }
.grav-btn {
    flex: 1; padding: .44rem .25rem; border-radius: 8px;
    border: 2px solid var(--border); background: var(--white);
    font-size: .73rem; font-weight: 600; cursor: pointer;
    text-align: center; transition: all .2s; font-family: inherit;
    line-height: 1.3;
}
.grav-btn[data-v="1"].active { border-color: var(--amb); background: var(--amb-l); color: var(--amb); }
.grav-btn[data-v="2"].active { border-color: #f97316; background: #fff7ed; color: #ea580c; }
.grav-btn[data-v="3"].active { border-color: var(--red); background: var(--red-l); color: var(--red); }

/* ═══════════════ CHECKBOX ROW ═══════════════ */
.check-row {
    display: flex; align-items: center; gap: .5rem;
    padding: .25rem 0;
}
.check-row input[type="checkbox"] {
    width: 16px; height: 16px; flex-shrink: 0;
    cursor: pointer; margin: 0;
    border: 1px solid var(--border);
    border-radius: 4px;
    -webkit-appearance: auto;
    appearance: auto;
}
.check-row label {
    font-size: .82rem; cursor: pointer; margin: 0; color: var(--slate);
}

/* ═══════════════ SUBMIT BTN ═══════════════ */
.btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
    background: var(--slate); color: #fff; border: none; border-radius: 8px;
    padding: .55rem 1.1rem; font-size: .82rem; font-weight: 600;
    cursor: pointer; font-family: inherit; transition: background .2s;
    width: 100%;
}
.btn-primary:hover { background: #374151; }
.btn-sm { padding: .36rem .75rem; font-size: .75rem; width: auto; }
.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
    background: var(--white); color: var(--slate);
    border: 1px solid var(--border); border-radius: 8px;
    padding: .48rem .9rem; font-size: .8rem; font-weight: 600;
    cursor: pointer; font-family: inherit; text-decoration: none;
    transition: all .2s;
}
.btn-outline:hover { border-color: #6366f1; color: #6366f1; }
.btn-outline.sm { padding: .36rem .7rem; font-size: .75rem; }

/* ═══════════════ FILTRES ═══════════════ */
.filters-form {
    border-bottom: 1px solid var(--border);
}
.filters-bar {
    display: flex; flex-direction: column; gap: .5rem;
    padding: .875rem 1rem; background: var(--bg);
}
@media (min-width: 640px) {
    .filters-bar { flex-direction: row; flex-wrap: wrap; align-items: center; }
}
.filters-bar select,
.filters-bar input[type="text"] {
    width: 100%;
    padding: .42rem .7rem; border: 1px solid var(--border);
    border-radius: 8px; font-size: .77rem; outline: none;
    background: var(--white); color: var(--slate);
    font-family: inherit; -webkit-appearance: none; appearance: none;
}
@media (min-width: 640px) {
    .filters-bar input[type="text"] { flex: 1; min-width: 160px; width: auto; }
    .filters-bar select { width: auto; min-width: 120px; }
}
.filters-actions {
    display: flex; gap: .4rem; flex-shrink: 0;
}

/* ═══════════════ TABLE MÉTA ═══════════════ */
.table-meta {
    padding: .5rem 1rem; border-bottom: 1px solid var(--border);
    font-size: .78rem; color: var(--muted);
}

/* ═══════════════ TABLE ═══════════════ */
.disc-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.disc-table {
    width: 100%; border-collapse: collapse;
    min-width: 680px; /* scroll horizontal sous cette largeur */
}
.disc-table thead th {
    position: sticky; top: 0; z-index: 2;
    background: var(--bg); padding: .55rem .875rem;
    text-align: left; font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--muted); border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.disc-table td {
    padding: .65rem .875rem; border-bottom: 1px solid #f1f5f9;
    font-size: .8rem; color: var(--slate); vertical-align: middle;
}
.disc-table tr:last-child td { border-bottom: none; }
.disc-table tr:hover td { background: var(--bg); }

/* ═══════════════ BADGES ═══════════════ */
.badge {
    display: inline-flex; align-items: center;
    padding: .18rem .52rem; border-radius: 20px;
    font-size: .65rem; font-weight: 700; white-space: nowrap;
}
.badge-red    { background: var(--red-l);  color: var(--red); }
.badge-amber  { background: var(--amb-l);  color: var(--amb); }
.badge-green  { background: var(--grn-l);  color: var(--grn); }
.badge-blue   { background: var(--blu-l);  color: var(--blu); }
.badge-violet { background: var(--vio-l);  color: var(--vio); }
.badge-gray   { background: #f1f5f9;       color: #64748b; }
.badge-orange { background: #fff7ed;       color: #ea580c; }

/* ═══════════════ ACTION BUTTONS ═══════════════ */
.act-btn {
    width: 28px; height: 28px; border-radius: 7px;
    border: 1px solid var(--border); background: var(--white);
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .18s; color: var(--muted);
    flex-shrink: 0;
}
.act-btn:hover       { border-color: #6366f1; color: #6366f1; background: var(--vio-l); }
.act-btn.danger:hover{ border-color: var(--red); color: var(--red); background: var(--red-l); }
.act-btn svg { width: 13px; height: 13px; }
.act-cell { display: flex; gap: .25rem; }

/* ═══════════════ STAT SIDE ═══════════════ */
.stat-side {
    background: var(--white); border: 1px solid var(--border);
    border-radius: var(--r); box-shadow: var(--sh);
    padding: 1rem; margin-top: .875rem;
}
.stat-side h3 {
    font-size: .71rem; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .06em; margin: 0 0 .75rem;
}
.bar-item { margin-bottom: .5rem; }
.bar-h { display: flex; justify-content: space-between; margin-bottom: .2rem; }
.bar-lbl, .bar-val { font-size: .73rem; color: var(--slate); }
.bar-val { font-weight: 700; }
.bar-track { height: 5px; background: var(--bg); border-radius: 99px; overflow: hidden; }
.bar-fill  { height: 100%; border-radius: 99px; background: #6366f1; }

/* ═══════════════ APPRENANT SEARCH ═══════════════ */
.appr-dropdown {
    display: none; position: absolute; left: 0; right: 0;
    top: calc(100% + 4px); z-index: 50;
    background: var(--white); border: 1px solid var(--border);
    border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.1);
    max-height: 200px; overflow-y: auto;
}
.appr-selected {
    display: none; margin-top: .45rem;
    padding: .42rem .8rem; background: var(--vio-l);
    border: 1px solid #c4b5fd; border-radius: 8px;
    font-size: .8rem; font-weight: 600; color: #5b21b6;
    align-items: center; justify-content: space-between;
}
.appr-clear {
    background: none; border: none; cursor: pointer;
    color: #7c3aed; font-size: 1rem; line-height: 1; padding: 0;
}

/* ═══════════════ MODAL ═══════════════ */
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 300;
    display: none; align-items: center; justify-content: center;
    padding: 1rem;
    overflow-y: auto;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: var(--white); border-radius: 14px;
    width: 100%; max-width: 540px;
    max-height: 90vh; overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: modalIn .22s ease;
    margin: auto; /* centre même si overflow */
}
@keyframes modalIn {
    from { opacity:0; transform: translateY(14px) scale(.97); }
    to   { opacity:1; transform: none; }
}
.modal-header {
    padding: .875rem 1.125rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; background: var(--white); z-index: 5;
}
.modal-header h2 { font-size: .9rem; font-weight: 700; color: var(--slate); margin: 0; }
.modal-body { padding: 1rem; }
.modal-footer {
    padding: .75rem 1.125rem; border-top: 1px solid var(--border);
    display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .5rem;
    position: sticky; bottom: 0; background: var(--white);
}
.modal-footer .btn-outline,
.modal-footer .btn-primary {
    flex: 1; min-width: 120px;
}
@media (min-width: 400px) {
    .modal-footer .btn-outline,
    .modal-footer .btn-primary { flex: none; width: auto; }
}
.modal-close {
    width: 28px; height: 28px; border-radius: 7px;
    border: 1px solid var(--border); background: var(--bg);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: var(--muted); transition: all .18s; flex-shrink: 0;
}
.modal-close:hover { background: var(--red-l); border-color: var(--red); color: var(--red); }

/* ═══════════════ EMPTY STATE ═══════════════ */
.empty-state { text-align: center; padding: 2.5rem 1rem; color: var(--muted); }
.empty-state svg { width: 40px; height: 40px; opacity: .2; display: block; margin: 0 auto .75rem; }
.empty-state p { font-size: .82rem; }

/* ═══════════════ FLASH TOAST ═══════════════ */
.flash-toast {
    position: fixed; bottom: 1rem; right: 1rem; left: 1rem;
    z-index: 9999; padding: .75rem 1rem;
    border-radius: 10px; display: flex; align-items: center; gap: .5rem;
    font-size: .82rem; font-weight: 500;
    box-shadow: 0 4px 20px rgba(0,0,0,.12);
    animation: slideUp .3s ease, fadeOut .4s 4.2s ease forwards;
}
@media (min-width: 540px) {
    .flash-toast { left: auto; width: auto; max-width: 360px; }
}
.flash-success { background: var(--grn-l); color: var(--grn); border: 1px solid #bbf7d0; }
.flash-error   { background: var(--red-l); color: var(--red); border: 1px solid #fecaca; }
@keyframes slideUp  { from { transform: translateY(12px); opacity: 0; } to { transform: none; opacity: 1; } }
@keyframes fadeOut  { from { opacity: 1; } to { opacity: 0; pointer-events: none; } }

/* ═══════════════ TOP APPRENANTS LIST ═══════════════ */
.top-item {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .45rem; font-size: .79rem; gap: .5rem;
}
.top-item a { color: var(--slate); text-decoration: none; font-weight: 500; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>
@endpush

@section('content')

{{-- ── FLASH ── --}}
@if(session('success'))
<div class="flash-toast flash-success">
    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flash-toast flash-error">
    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    {{ session('error') }}
</div>
@endif

<div class="dp">

    {{-- ════════ HERO ════════ --}}
    <div class="dp-hero">
        <div class="dp-hero-inner">
            <div class="dp-hero-title">
                <h1>⚖️ Suivi Disciplinaire</h1>
                <p>{{ $institution->name }} — Année {{ $annee }}</p>
            </div>
            <div class="dp-hero-actions">
                <form method="GET" action="{{ route('admin.disciplinaire') }}" style="display:flex;flex:1;min-width:130px;">
                    <select name="annee" onchange="this.form.submit()" class="hero-select">
                        @foreach($anneesDispos as $a)
                            <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.disciplinaire.export', ['annee' => $annee]) }}" class="hero-btn">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Exporter CSV
                </a>
            </div>
        </div>
    </div>

    {{-- ════════ KPI ════════ --}}
    <div class="kpi-strip">
        <div class="kpi-card red">
            <div class="kpi-val">{{ $stats->total ?? 0 }}</div>
            <div class="kpi-label">Total incidents</div>
        </div>
        <div class="kpi-card red">
            <div class="kpi-val">{{ $stats->graves ?? 0 }}</div>
            <div class="kpi-label">Incidents graves</div>
        </div>
        <div class="kpi-card amber">
            <div class="kpi-val">{{ $stats->ouverts ?? 0 }}</div>
            <div class="kpi-label">Dossiers ouverts</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-val">{{ $stats->clos ?? 0 }}</div>
            <div class="kpi-label">Dossiers clos</div>
        </div>
        <div class="kpi-card blue">
            <div class="kpi-val">{{ $stats->notifies ?? 0 }}</div>
            <div class="kpi-label">Parents notifiés</div>
        </div>
    </div>

    {{-- ════════ LAYOUT PRINCIPAL ════════ --}}
    <div class="dp-layout">

        {{-- ═══ SIDEBAR ═══ --}}
        <div class="dp-sidebar">

            {{-- Formulaire nouvel incident --}}
            <div class="card">
                <div class="card-head">
                    <svg width="15" height="15" fill="none" stroke="#6366f1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <h2>Nouvel incident</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.disciplinaire.store') }}" id="form-incident">
                        @csrf

                        {{-- Recherche élève --}}
                        <div class="field">
                            <label>Élève *</label>
                            <div style="position:relative;">
                                <input type="text" id="apprenant-search-input"
                                    placeholder="🔍 Nom ou matricule…"
                                    autocomplete="off"
                                    oninput="searchApprenantDisc(this.value)">
                                <div id="apprenant-search-results" class="appr-dropdown"></div>
                            </div>
                            <div id="apprenant-selected" class="appr-selected">
                                <span id="apprenant-selected-name"></span>
                                <button type="button" class="appr-clear" onclick="clearApprenant()">✕</button>
                            </div>
                            <input type="hidden" name="apprenant_id" id="apprenant-id-input" required>
                        </div>

                        {{-- Date + Type --}}
                        <div class="field-row">
                            <div class="field">
                                <label>Date incident *</label>
                                <input type="date" name="date_incident"
                                    value="{{ old('date_incident', date('Y-m-d')) }}"
                                    required max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="field">
                                <label>Type d'incident *</label>
                                <select name="type" required>
                                    @foreach($typeLabels as $val => $lbl)
                                        <option value="{{ $val }}" {{ old('type') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Gravité --}}
                        <div class="field">
                            <label>Gravité *</label>
                            <div class="grav-btns">
                                <button type="button" class="grav-btn active" data-v="1" onclick="setGravite(1)">⚠️ Mineur</button>
                                <button type="button" class="grav-btn" data-v="2" onclick="setGravite(2)">🔶 Modéré</button>
                                <button type="button" class="grav-btn" data-v="3" onclick="setGravite(3)">🔴 Grave</button>
                            </div>
                            <input type="hidden" name="gravite" id="gravite-input" value="{{ old('gravite', 1) }}">
                        </div>

                        {{-- Description --}}
                        <div class="field">
                            <label>Description</label>
                            <textarea name="description" placeholder="Décrire l'incident…">{{ old('description') }}</textarea>
                        </div>

                        {{-- Sanction + Statut --}}
                        <div class="field-row">
                            <div class="field">
                                <label>Sanction *</label>
                                <select name="sanction" required>
                                    @foreach($sanctionLabels as $val => $lbl)
                                        <option value="{{ $val }}" {{ old('sanction','aucune') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Statut *</label>
                                <select name="statut" required>
                                    <option value="ouvert"   {{ old('statut','ouvert') == 'ouvert'   ? 'selected':'' }}>🔴 Ouvert</option>
                                    <option value="en_suivi" {{ old('statut') == 'en_suivi' ? 'selected':'' }}>🟡 En suivi</option>
                                    <option value="clos"     {{ old('statut') == 'clos'     ? 'selected':'' }}>🟢 Clos</option>
                                </select>
                            </div>
                        </div>

                        {{-- Checkbox parents --}}
                        <div class="field">
                            <div class="check-row">
                                <input type="checkbox" name="parents_notifies" value="1"
                                    id="parents_notifies"
                                    {{ old('parents_notifies') ? 'checked' : '' }}>
                                <label for="parents_notifies">Parents notifiés</label>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Enregistrer l'incident
                        </button>
                    </form>
                </div>
            </div>

            {{-- Stats par type --}}
            @if($parType->count())
            <div class="stat-side">
                <h3>Incidents par type</h3>
                @php $maxT = $parType->max('total') ?: 1; @endphp
                @foreach($parType as $pt)
                    <div class="bar-item">
                        <div class="bar-h">
                            <span class="bar-lbl">{{ $typeLabels[$pt->type] ?? $pt->type }}</span>
                            <span class="bar-val">{{ $pt->total }}</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:{{ round($pt->total/$maxT*100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Top apprenants --}}
            @if($topApprenants->count())
            <div class="stat-side">
                <h3>Élèves les plus concernés</h3>
                @foreach($topApprenants as $ta)
                    <div class="top-item">
                        <a href="{{ route('admin.disciplinaire.apprenant', $ta->apprenant_id) }}">
                            {{ $ta->apprenant?->prenom }} {{ $ta->apprenant?->nom }}
                        </a>
                        <span style="background:var(--red-l);color:var(--red);padding:.12rem .45rem;border-radius:20px;font-size:.67rem;font-weight:700;flex-shrink:0;">
                            {{ $ta->nb_incidents }}
                        </span>
                    </div>
                @endforeach
            </div>
            @endif

        </div>{{-- fin sidebar --}}

        {{-- ═══ MAIN ═══ --}}
        <div class="dp-main">
            <div class="card">

                {{-- Filtres --}}
                <div class="filters-form">
                    <form method="GET" action="{{ route('admin.disciplinaire') }}">
                        <input type="hidden" name="annee" value="{{ $annee }}">
                        <div class="filters-bar">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="🔍 Rechercher un élève…">
                            <select name="type" onchange="this.form.submit()">
                                <option value="">Tous types</option>
                                @foreach($typeLabels as $v => $l)
                                    <option value="{{ $v }}" {{ $type == $v ? 'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                            <select name="gravite" onchange="this.form.submit()">
                                <option value="">Toutes gravités</option>
                                @foreach($graviteLabels as $v => $l)
                                    <option value="{{ $v }}" {{ $gravite == $v ? 'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                            <select name="statut" onchange="this.form.submit()">
                                <option value="">Tous statuts</option>
                                <option value="ouvert"   {{ $statut=='ouvert'   ? 'selected':'' }}>Ouvert</option>
                                <option value="en_suivi" {{ $statut=='en_suivi' ? 'selected':'' }}>En suivi</option>
                                <option value="clos"     {{ $statut=='clos'     ? 'selected':'' }}>Clos</option>
                            </select>
                            <select name="classe_id" onchange="this.form.submit()">
                                <option value="">Toutes classes</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ $classeId==$c->id ? 'selected':'' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            <div class="filters-actions">
                                <button type="submit" class="btn-primary btn-sm">Filtrer</button>
                                @if($search || $type || $gravite || $statut || $classeId)
                                    <a href="{{ route('admin.disciplinaire', ['annee' => $annee]) }}" class="btn-outline sm">Effacer</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-meta">{{ $incidents->total() }} incident(s)</div>

                {{-- Tableau avec scroll horizontal --}}
                <div class="disc-table-wrap">
                    <table class="disc-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Élève</th>
                                <th>Classe</th>
                                <th>Type</th>
                                <th>Gravité</th>
                                <th>Sanction</th>
                                <th>Parents</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incidents as $inc)
                            <tr>
                                <td style="white-space:nowrap;font-size:.75rem;color:var(--muted);">
                                    {{ $inc->date_incident?->format('d/m/Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.disciplinaire.apprenant', $inc->apprenant_id) }}"
                                       style="font-weight:600;color:var(--slate);text-decoration:none;white-space:nowrap;">
                                        {{ $inc->apprenant?->prenom }} {{ $inc->apprenant?->nom }}
                                    </a>
                                </td>
                                <td style="font-size:.75rem;color:var(--muted);white-space:nowrap;">
                                    {{ $inc->apprenant?->classe?->name ?? '—' }}
                                </td>
                                <td><span class="badge badge-violet">{{ $typeLabels[$inc->type] ?? $inc->type }}</span></td>
                                <td>
                                    @if($inc->gravite == 1)     <span class="badge badge-amber">Mineur</span>
                                    @elseif($inc->gravite == 2) <span class="badge badge-orange">Modéré</span>
                                    @else                       <span class="badge badge-red">Grave</span>
                                    @endif
                                </td>
                                <td style="font-size:.73rem;color:var(--muted);white-space:nowrap;">{{ $sanctionLabels[$inc->sanction] ?? $inc->sanction }}</td>
                                <td>
                                    @if($inc->parents_notifies) <span class="badge badge-green">✓ Oui</span>
                                    @else                       <span class="badge badge-gray">Non</span>
                                    @endif
                                </td>
                                <td>
                                    @if($inc->statut === 'ouvert')     <span class="badge badge-red">Ouvert</span>
                                    @elseif($inc->statut === 'en_suivi') <span class="badge badge-amber">En suivi</span>
                                    @else                               <span class="badge badge-green">Clos</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="act-cell">
                                        <button class="act-btn" title="Modifier" onclick="openEditModal({{ $inc->id }})">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.disciplinaire.destroy', $inc->id) }}"
                                              onsubmit="return confirm('Supprimer cet incident ?')" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="act-btn danger" title="Supprimer">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                                        <p>Aucun incident enregistré pour {{ $annee }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($incidents->hasPages())
                    <div style="padding:.75rem 1rem;border-top:1px solid var(--border);">
                        {{ $incidents->links() }}
                    </div>
                @endif

            </div>
        </div>{{-- fin main --}}

    </div>{{-- fin layout --}}

</div>{{-- fin dp --}}

{{-- ════════════════ MODAL ÉDITION ════════════════ --}}
<div class="modal-overlay" id="modal-edit-incident">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Modifier l'incident</h2>
            <button class="modal-close" onclick="closeModal('modal-edit-incident')">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="edit-incident-form">
            @csrf @method('PUT')
            <div class="modal-body">

                <div class="field-row">
                    <div class="field">
                        <label>Date *</label>
                        <input type="date" name="date_incident" id="edit-date" required max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="field">
                        <label>Type *</label>
                        <select name="type" id="edit-type" required>
                            @foreach($typeLabels as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Gravité *</label>
                    <div class="grav-btns" id="edit-grav-btns">
                        <button type="button" class="grav-btn" data-v="1" onclick="setEditGravite(1)">⚠️ Mineur</button>
                        <button type="button" class="grav-btn" data-v="2" onclick="setEditGravite(2)">🔶 Modéré</button>
                        <button type="button" class="grav-btn" data-v="3" onclick="setEditGravite(3)">🔴 Grave</button>
                    </div>
                    <input type="hidden" name="gravite" id="edit-gravite-input">
                </div>

                <div class="field">
                    <label>Description</label>
                    <textarea name="description" id="edit-description"></textarea>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label>Sanction *</label>
                        <select name="sanction" id="edit-sanction" required>
                            @foreach($sanctionLabels as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Statut *</label>
                        <select name="statut" id="edit-statut" required>
                            <option value="ouvert">🔴 Ouvert</option>
                            <option value="en_suivi">🟡 En suivi</option>
                            <option value="clos">🟢 Clos</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <div class="check-row">
                        <input type="checkbox" name="parents_notifies" value="1" id="edit-parents">
                        <label for="edit-parents">Parents notifiés</label>
                    </div>
                </div>

                <div class="field">
                    <label>Observations</label>
                    <textarea name="observations" id="edit-observations"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal-edit-incident')">Annuler</button>
                <button type="submit" class="btn-primary" style="width:auto;">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@php
$incidentsJs = $incidents->map(fn($i) => [
    'id'               => $i->id,
    'date_incident'    => $i->date_incident?->format('Y-m-d'),
    'type'             => $i->type,
    'gravite'          => $i->gravite,
    'description'      => $i->description,
    'sanction'         => $i->sanction,
    'parents_notifies' => $i->parents_notifies,
    'observations'     => $i->observations,
    'statut'           => $i->statut,
])->values()->all();
@endphp

<script>
const INCIDENTS  = @json($incidentsJs);
const SEARCH_URL = "{{ route('admin.academic.search.apprenants') }}";

/* ── MODAL ── */
function openModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }

document.querySelectorAll('.modal-overlay').forEach(el =>
    el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); })
);
document.addEventListener('keydown', e => {
    if (e.key === 'Escape')
        document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
});

/* ── GRAVITÉ FORMULAIRE PRINCIPAL ── */
function setGravite(v) {
    document.getElementById('gravite-input').value = v;
    document.querySelectorAll('#form-incident .grav-btn').forEach(b =>
        b.classList.toggle('active', +b.dataset.v === v)
    );
}

/* ── GRAVITÉ MODAL ÉDITION ── */
function setEditGravite(v) {
    document.getElementById('edit-gravite-input').value = v;
    document.querySelectorAll('#edit-grav-btns .grav-btn').forEach(b =>
        b.classList.toggle('active', +b.dataset.v === v)
    );
}

/* ── OUVRIR MODAL ÉDITION ── */
function openEditModal(id) {
    const inc = INCIDENTS.find(x => x.id === id);
    if (!inc) return;
    document.getElementById('edit-incident-form').action = '/admin/disciplinaire/' + id;
    document.getElementById('edit-date').value          = inc.date_incident || '';
    document.getElementById('edit-type').value          = inc.type || '';
    document.getElementById('edit-description').value   = inc.description || '';
    document.getElementById('edit-sanction').value      = inc.sanction || 'aucune';
    document.getElementById('edit-statut').value        = inc.statut || 'ouvert';
    document.getElementById('edit-observations').value  = inc.observations || '';
    document.getElementById('edit-parents').checked     = !!inc.parents_notifies;
    setEditGravite(inc.gravite || 1);
    openModal('modal-edit-incident');
}

/* ── RECHERCHE APPRENANT ── */
let _debounce = null;
function searchApprenantDisc(q) {
    clearTimeout(_debounce);
    const box = document.getElementById('apprenant-search-results');
    if (!q || q.length < 2) { box.style.display = 'none'; return; }
    _debounce = setTimeout(() => {
        fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(r => r.json()).then(({ data }) => renderResults(data));
    }, 280);
}

function renderResults(data) {
    const box = document.getElementById('apprenant-search-results');
    if (!data?.length) {
        box.innerHTML = '<div style="padding:.875rem;text-align:center;color:#94a3b8;font-size:.78rem;">Aucun élève trouvé</div>';
        box.style.display = 'block'; return;
    }
    box.style.display = 'block';
    box.innerHTML = data.map(a => {
        const init = ((a.prenom || '')[0] || '').toUpperCase() + ((a.nom || '')[0] || '').toUpperCase();
        const name = (a.prenom + ' ' + a.nom).replace(/'/g, "\\'");
        return `<div style="padding:.5rem .8rem;cursor:pointer;border-bottom:1px solid #f1f5f9;
                            display:flex;align-items:center;gap:.55rem;"
                     onmouseenter="this.style.background='#f0fdf4'" onmouseleave="this.style.background=''"
                     onclick="selectApprenant(${a.id},'${name}')">
                  <div style="width:28px;height:28px;border-radius:50%;background:#ede9fe;color:#6d28d9;
                              display:flex;align-items:center;justify-content:center;
                              font-size:.62rem;font-weight:700;flex-shrink:0;">${init}</div>
                  <div>
                    <div style="font-weight:600;font-size:.8rem;color:#1e293b;">${a.prenom} ${a.nom}</div>
                    <div style="font-size:.7rem;color:#94a3b8;">${a.classe || 'Sans classe'}${a.matricule ? ' · ' + a.matricule : ''}</div>
                  </div>
                </div>`;
    }).join('');
}

function selectApprenant(id, name) {
    document.getElementById('apprenant-id-input').value    = id;
    document.getElementById('apprenant-search-input').value = '';
    document.getElementById('apprenant-search-results').style.display = 'none';
    document.getElementById('apprenant-selected-name').textContent = name;
    document.getElementById('apprenant-selected').style.display = 'flex';
}

function clearApprenant() {
    document.getElementById('apprenant-id-input').value = '';
    document.getElementById('apprenant-selected').style.display = 'none';
}

document.addEventListener('click', e => {
    const box   = document.getElementById('apprenant-search-results');
    const input = document.getElementById('apprenant-search-input');
    if (box && !box.contains(e.target) && e.target !== input)
        box.style.display = 'none';
});

/* ── AUTO-DISMISS FLASH ── */
setTimeout(() => document.querySelectorAll('.flash-toast').forEach(el => el.remove()), 4800);
</script>
@endsection