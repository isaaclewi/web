@extends('admin.master')
@section('title', 'Notes & Bulletins')

@push('styles')
<style>
/* ════════════════════════════════════════════
   VARIABLES & RESET
════════════════════════════════════════════ */
:root {
    --night:   #080c14;
    --gold:    #f59e0b;
    --gold-d:  #d97706;
    --gold-l:  rgba(245,158,11,.1);
    --ok:      #10b981;
    --ok-l:    #d1fae5;
    --err:     #ef4444;
    --err-l:   #fee2e2;
    --warn:    #f59e0b;
    --warn-l:  #fef3c7;
    --info:    #3b82f6;
    --info-l:  #dbeafe;
    --brd:     #e5e7eb;
    --bg:      #f9fafb;
    --mist:    #9ca3af;
    --text:    #111827;
    --text-s:  #374151;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ════════════════════════════════════════════
   LAYOUT
════════════════════════════════════════════ */
.pw {
    max-width: 1440px;
    margin: 0 auto;
    padding: 1.25rem 1rem;
}

/* ════════════════════════════════════════════
   PAGE HEADER
════════════════════════════════════════════ */
.ph {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
    margin-bottom: 1.25rem;
}
.ph-title { font-size: 1.15rem; font-weight: 700; color: var(--text); }
.ph-sub   { font-size: .78rem; color: var(--mist); margin-top: .15rem; }
.ph-actions { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; }

/* ════════════════════════════════════════════
   FLASH
════════════════════════════════════════════ */
.flash {
    padding: .7rem 1rem;
    border-radius: 8px;
    font-size: .84rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.flash-ok  { background: var(--ok-l);   border: 1px solid #6ee7b7; color: #065f46; }
.flash-err { background: var(--err-l);  border: 1px solid #fca5a5; color: #991b1b; }

/* ════════════════════════════════════════════
   STAT ROW
════════════════════════════════════════════ */
.stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: .75rem;
    margin-bottom: 1.25rem;
}
.s-mini {
    background: #fff;
    border: 1px solid var(--brd);
    border-radius: 10px;
    padding: 1rem 1.125rem;
}
.s-mini-val  { font-size: 1.6rem; font-weight: 800; color: var(--text); line-height: 1; letter-spacing: -.04em; }
.s-mini-lbl  { font-size: .7rem;  color: var(--mist); margin-top: .2rem; }
.s-mini-sub  { display: flex; gap: .35rem; flex-wrap: wrap; margin-top: .5rem; }
.s-mini-sub span {
    padding: .12rem .45rem;
    border-radius: 4px;
    font-size: .64rem;
    font-weight: 600;
}
.sub-pub  { background: var(--ok-l);   color: #065f46; }
.sub-calc { background: var(--info-l); color: #1e3a8a; }

/* ════════════════════════════════════════════
   TABS
════════════════════════════════════════════ */
.n-tabs {
    display: flex;
    gap: 0;
    border-bottom: 2px solid var(--brd);
    margin-bottom: 1.5rem;
    overflow-x: auto;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.n-tabs::-webkit-scrollbar { display: none; }
.n-tab {
    padding: .6rem 1.1rem;
    font-size: .8rem;
    font-weight: 600;
    color: var(--mist);
    border: none;
    background: none;
    cursor: pointer;
    font-family: inherit;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all .15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.n-tab.on  { color: var(--text); border-bottom-color: var(--gold); }
.n-tab:hover:not(.on) { color: var(--text-s); }
.n-section { display: none; }
.n-section.on { display: block; }

/* ════════════════════════════════════════════
   CARDS
════════════════════════════════════════════ */
.card {
    background: #fff;
    border: 1px solid var(--brd);
    border-radius: 12px;
    overflow: hidden;
}
.card-hd {
    padding: .875rem 1.25rem;
    border-bottom: 1px solid var(--brd);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    flex-wrap: wrap;
}
.card-hd h3 { font-size: .875rem; font-weight: 700; color: var(--text); }
.card-body  { padding: 1.125rem 1.25rem; }

/* ════════════════════════════════════════════
   BUTTONS
════════════════════════════════════════════ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .45rem 1rem;
    border-radius: 8px;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all .15s;
    text-decoration: none;
    font-family: inherit;
    white-space: nowrap;
}
.btn-dk   { background: #1f2937; color: #fff; }
.btn-dk:hover { background: var(--night); }
.btn-gold { background: var(--gold); color: var(--night); }
.btn-gold:hover { background: var(--gold-d); }
.btn-ok   { background: var(--ok); color: #fff; }
.btn-ok:hover { background: #059669; }
.btn-err  { background: var(--err); color: #fff; }
.btn-err:hover { background: #dc2626; }
.btn-ot   { background: #fff; color: #6b7280; border: 1px solid var(--brd); }
.btn-ot:hover { background: var(--bg); color: var(--text); }
.btn-sm   { padding: .3rem .7rem; font-size: .74rem; border-radius: 7px; }
.btn-ico  { padding: .3rem .5rem; }
.btn-full { width: 100%; justify-content: center; }

/* ════════════════════════════════════════════
   FORM ELEMENTS
════════════════════════════════════════════ */
.inp {
    width: 100%;
    border: 1.5px solid var(--brd);
    border-radius: 8px;
    padding: .5rem .8rem;
    font-size: .82rem;
    font-family: inherit;
    color: var(--text);
    background: #fff;
    outline: none;
    transition: border-color .15s;
}
.inp:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-l); }
.inp::placeholder { color: var(--mist); }
.lbl {
    font-size: .73rem;
    font-weight: 600;
    color: #6b7280;
    display: block;
    margin-bottom: .3rem;
}
.fg  { display: flex; flex-direction: column; gap: .3rem; }
.fg2 { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
.fg3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .75rem; }
.fg-stack { display: flex; flex-direction: column; gap: .625rem; }

/* ════════════════════════════════════════════
   SEARCHABLE SELECT
════════════════════════════════════════════ */
.ss-wrap    { position: relative; }
.ss-input {
    width: 100%;
    border: 1.5px solid var(--brd);
    border-radius: 8px;
    padding: .5rem .8rem;
    font-size: .82rem;
    font-family: inherit;
    color: var(--text);
    background: #fff;
    outline: none;
    cursor: pointer;
    transition: border-color .15s;
}
.ss-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-l); }
.ss-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1.5px solid var(--gold);
    border-radius: 8px;
    z-index: 300;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    max-height: 240px;
    overflow: hidden;
    flex-direction: column;
}
.ss-dropdown.open { display: flex; }
.ss-search {
    border: none;
    border-bottom: 1px solid var(--brd);
    padding: .55rem .875rem;
    font-size: .82rem;
    font-family: inherit;
    outline: none;
    width: 100%;
    background: #fafafa;
}
.ss-search::placeholder { color: var(--mist); }
.ss-list  { overflow-y: auto; flex: 1; }
.ss-opt {
    padding: .5rem .875rem;
    font-size: .81rem;
    color: var(--text-s);
    cursor: pointer;
    transition: background .1s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ss-opt:hover  { background: var(--bg); }
.ss-opt.active { background: var(--gold-l); color: #92400e; font-weight: 600; }
.ss-opt.disabled {
    color: var(--mist);
    font-style: italic;
    pointer-events: none;
    font-size: .73rem;
    padding: .3rem .875rem;
    background: #fafafa;
}
.ss-empty { padding: 1rem .875rem; font-size: .79rem; color: var(--mist); text-align: center; }

/* ════════════════════════════════════════════
   GRADE INPUT
════════════════════════════════════════════ */
.grade-input {
    border: 1.5px solid var(--brd);
    border-radius: 7px;
    padding: .4rem .5rem;
    font-size: .85rem;
    font-weight: 700;
    text-align: center;
    width: 72px;
    outline: none;
    transition: all .15s;
    font-family: inherit;
}
.grade-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-l); }
.grade-input.valid { border-color: var(--ok);  background: var(--ok-l); }
.grade-input.over  { border-color: var(--err); background: var(--err-l); }

/* ════════════════════════════════════════════
   BADGES
════════════════════════════════════════════ */
.bdg {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    padding: .18rem .55rem;
    border-radius: 20px;
    font-size: .67rem;
    font-weight: 600;
}
.bdg::before {
    content: '';
    width: 5px; height: 5px;
    border-radius: 50%;
    background: currentColor;
    opacity: .7;
    flex-shrink: 0;
}
.bdg-g { background: var(--ok-l);   color: #065f46; }
.bdg-a { background: var(--warn-l); color: #92400e; }
.bdg-b { background: var(--info-l); color: #1e3a8a; }
.bdg-r { background: var(--err-l);  color: #7f1d1d; }
.bdg-n { background: #f1f5f9;       color: #64748b; }

/* ════════════════════════════════════════════
   SCORE CHIP
════════════════════════════════════════════ */
.score {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: .77rem;
    padding: .22rem .6rem;
    border-radius: 7px;
    min-width: 52px;
}
.score-hi  { background: var(--ok-l);   color: #065f46; }
.score-mid { background: var(--warn-l); color: #92400e; }
.score-lo  { background: var(--err-l);  color: #7f1d1d; }

/* ════════════════════════════════════════════
   TABLES
════════════════════════════════════════════ */
.tbl-outer { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.n-tbl { width: 100%; border-collapse: collapse; min-width: 560px; }
.n-tbl thead th {
    padding: .55rem 1rem;
    text-align: left;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--mist);
    border-bottom: 1px solid var(--brd);
    background: var(--bg);
    white-space: nowrap;
}
.n-tbl tbody td {
    padding: .75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: .83rem;
    color: var(--text-s);
    vertical-align: middle;
}
.n-tbl tbody tr:hover td { background: var(--bg); }
.n-tbl tbody tr:last-child td { border-bottom: none; }

/* ════════════════════════════════════════════
   PROGRESS
════════════════════════════════════════════ */
.prog-wrap { height: 4px; background: #e5e7eb; border-radius: 2px; overflow: hidden; margin-top: .3rem; }
.prog-bar  { height: 100%; border-radius: 2px; }

/* ════════════════════════════════════════════
   EMPTY STATE
════════════════════════════════════════════ */
.n-empty { text-align: center; padding: 2.5rem 1.5rem; color: var(--mist); }
.n-empty-ico { font-size: 2.2rem; margin-bottom: .6rem; }
.n-empty h4  { font-size: .9rem; font-weight: 600; color: var(--text-s); margin-bottom: .3rem; }
.n-empty p   { font-size: .8rem; }

/* ════════════════════════════════════════════
   CONF ITEMS
════════════════════════════════════════════ */
.conf-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .5rem 0;
    border-bottom: 1px solid #f3f4f6;
}
.conf-item:last-child { border-bottom: none; }
.conf-lbl { font-size: .76rem; color: var(--mist); }
.conf-val { font-size: .84rem; font-weight: 700; color: var(--text); }

/* ════════════════════════════════════════════
   CLASSE GRID
════════════════════════════════════════════ */
.classe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: .75rem;
}
.classe-card {
    background: var(--bg);
    border: 1px solid var(--brd);
    border-radius: 10px;
    padding: .875rem;
    transition: all .15s;
}
.classe-card:hover { border-color: #d1d5db; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
.classe-card-name { font-weight: 700; font-size: .84rem; color: var(--text); margin-bottom: .15rem; }
.classe-card-niv  { font-size: .68rem; color: var(--mist); }
.classe-card-count{ font-size: .68rem; color: var(--mist); margin-bottom: .65rem; }
.classe-card-acts { display: flex; gap: .3rem; flex-wrap: wrap; }

/* ════════════════════════════════════════════
   SAISIE GRID — responsive split
════════════════════════════════════════════ */
.saisie-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.25rem;
    align-items: start;
}

/* Info banner inside grade panel */
.eval-meta {
    padding: .5rem 1.25rem;
    background: var(--bg);
    border-bottom: 1px solid var(--brd);
    font-size: .77rem;
    color: var(--text-s);
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}
.grades-header {
    display: grid;
    grid-template-columns: 2fr 100px 70px;
    gap: .5rem;
    padding: .4rem 1.25rem;
    border-bottom: 2px solid var(--brd);
    background: #fafbfd;
}
.grades-header span {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--mist);
    text-align: center;
}
.grades-header span:first-child { text-align: left; }
.grade-row {
    display: grid;
    grid-template-columns: 2fr 100px 70px;
    gap: .5rem;
    padding: .5rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    align-items: center;
}
.grade-row:last-child { border-bottom: none; }
.grade-row:hover { background: var(--bg); }
.grade-actions {
    padding: .875rem 1.25rem;
    border-top: 1px solid var(--brd);
    display: flex;
    gap: .4rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

/* ════════════════════════════════════════════
   MODALS
════════════════════════════════════════════ */
.n-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 500;
    background: rgba(8,12,20,.6);
    backdrop-filter: blur(4px);
    align-items: flex-start;
    justify-content: center;
    padding: 5vh 1rem 2rem;
    overflow-y: auto;
}
.n-modal.open { display: flex; }
.n-modal-box {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: mIn .22s ease both;
    margin: auto;
}
.n-modal-box.sm { max-width: 400px; }
@keyframes mIn {
    from { transform: translateY(-14px); opacity: 0; }
    to   { transform: none; opacity: 1; }
}
.n-modal-hd {
    padding: 1.125rem 1.375rem;
    border-bottom: 1px solid var(--brd);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    background: #fff;
    border-radius: 16px 16px 0 0;
    z-index: 1;
}
.n-modal-hd h3 { font-size: .95rem; font-weight: 700; color: var(--text); }
.n-modal-body  { padding: 1.375rem; }
.n-modal-ft {
    padding: .875rem 1.375rem;
    border-top: 1px solid var(--brd);
    display: flex;
    gap: .625rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

/* Tip box */
.tip-box {
    background: var(--bg);
    border-left: 3px solid var(--gold);
    border-radius: 0 6px 6px 0;
    padding: .65rem .875rem;
    font-size: .74rem;
    color: var(--mist);
    margin-bottom: 1rem;
}
.tip-box strong { color: var(--text); }

/* ════════════════════════════════════════════
   RESPONSIVE BREAKPOINTS
════════════════════════════════════════════ */

/* ── 1100px: saisie grid → 260px + 1fr ── */
@media (max-width: 1100px) {
    .saisie-grid { grid-template-columns: 260px 1fr; gap: 1rem; }
}

/* ── 900px: saisie grid → 1 colonne ── */
@media (max-width: 900px) {
    .saisie-grid { grid-template-columns: 1fr; }
    .fg3 { grid-template-columns: 1fr 1fr; }
}

/* ── 768px: tablette ── */
@media (max-width: 768px) {
    .pw { padding: 1rem .75rem; }
    .ph { flex-direction: column; align-items: stretch; }
    .ph-actions { justify-content: flex-end; }
    .stat-row { grid-template-columns: repeat(2, 1fr); gap: .6rem; }
    .fg2, .fg3 { grid-template-columns: 1fr; }
    .classe-grid { grid-template-columns: repeat(2, 1fr); }
    .grades-header { grid-template-columns: 1fr 90px 60px; padding: .4rem 1rem; }
    .grade-row     { grid-template-columns: 1fr 90px 60px; padding: .45rem 1rem; }
    .grade-input   { width: 64px; font-size: .82rem; }
    .eval-meta     { padding: .45rem 1rem; gap: .6rem; font-size: .74rem; }
    .grade-actions { padding: .75rem 1rem; }
    .card-hd { padding: .75rem 1rem; }
    .card-body { padding: .875rem 1rem; }
    .n-modal-hd  { padding: 1rem; }
    .n-modal-body{ padding: 1rem; }
    .n-modal-ft  { padding: .75rem 1rem; }
}

/* ── 640px: mobile large ── */
@media (max-width: 640px) {
    .stat-row { grid-template-columns: repeat(2, 1fr); }
    .s-mini-val { font-size: 1.35rem; }
    .classe-grid { grid-template-columns: 1fr; }
    .n-tbl { min-width: 480px; }
    .n-tab { padding: .55rem .875rem; font-size: .76rem; }
    .ph-title { font-size: 1rem; }
    .ph-actions .btn { font-size: .74rem; padding: .4rem .75rem; }
}

/* ── 480px: mobile ── */
@media (max-width: 480px) {
    .pw { padding: .75rem .625rem; }
    .stat-row { grid-template-columns: 1fr 1fr; gap: .5rem; }
    .s-mini { padding: .75rem .875rem; }
    .s-mini-val { font-size: 1.2rem; }
    .grades-header { grid-template-columns: 1fr 76px 54px; }
    .grade-row     { grid-template-columns: 1fr 76px 54px; }
    .grade-input   { width: 58px; padding: .35rem .3rem; font-size: .8rem; }
    .n-modal-ft { flex-direction: column-reverse; }
    .n-modal-ft .btn { width: 100%; justify-content: center; }
}

/* scrollbar thin for grade list */
#grades-list { max-height: 55vh; overflow-y: auto; scrollbar-width: thin; }
#grades-list::-webkit-scrollbar { width: 4px; }
#grades-list::-webkit-scrollbar-track { background: transparent; }
#grades-list::-webkit-scrollbar-thumb { background: var(--brd); border-radius: 2px; }
</style>
@endpush

@section('content')
<div class="pw">

    {{-- ── HEADER ── --}}
    <div class="ph">
        <div>
            <div class="ph-title">Notes &amp; Bulletins</div>
            <div class="ph-sub">
                Année {{ $config->annee_academique }}
                &middot; {{ ucfirst($config->type_periodes) }}
                &middot; {{ $config->nb_periodes }} période(s)
            </div>
        </div>
        <div class="ph-actions">
            <button class="btn btn-ot btn-sm" onclick="openModal('modal-config')">⚙ Config</button>
            <button class="btn btn-gold btn-sm" onclick="openNewEval()">+ Nouvelle évaluation</button>
        </div>
    </div>

    {{-- ── FLASH ── --}}
    @if(session('success'))
        <div class="flash flash-ok">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash flash-err">⚠ {{ session('error') }}</div>
    @endif

    {{-- ── STATS PAR PÉRIODE ── --}}
    <div class="stat-row">
        @foreach($periodes as $p)
            @php $s = $statsParPeriode[$p['key']] ?? ['total'=>0,'publie'=>0,'calcule'=>0]; @endphp
            <div class="s-mini">
                <div class="s-mini-val">{{ $s['total'] }}</div>
                <div class="s-mini-lbl">{{ $p['label'] }}</div>
                <div class="s-mini-sub">
                    <span class="sub-pub">✓ {{ $s['publie'] }}</span>
                    <span class="sub-calc">⚙ {{ $s['calcule'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── TABS ── --}}
    <div class="n-tabs">
        <button class="n-tab on"  onclick="showTab('saisie',this)">📝 Saisie de notes</button>
        <button class="n-tab"     onclick="showTab('evaluations',this)">📋 Évaluations</button>
        <button class="n-tab"     onclick="showTab('bulletins',this)">📄 Bulletins</button>
        <button class="n-tab"     onclick="showTab('config',this)">⚙ Configuration</button>
    </div>

    {{-- ══════════════════════════════════════════
         TAB : SAISIE DE NOTES
    ══════════════════════════════════════════ --}}
    <div class="n-section on" id="tab-saisie">
        <div class="saisie-grid" id="saisie-grid">

            {{-- Panneau gauche --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">

                {{-- Sélecteur évaluation --}}
                <div class="card">
                    <div class="card-hd"><h3>Sélectionner une évaluation</h3></div>
                    <div class="card-body fg-stack">

                        <div class="fg">
                            <label class="lbl">Filtrer par niveau</label>
                            <div class="ss-wrap">
                                <input class="ss-input" id="fil-niveau-display"
                                       placeholder="Tous les niveaux" readonly
                                       onclick="toggleSS('fil-niveau')">
                                <div class="ss-dropdown" id="fil-niveau-dd">
                                    <input class="ss-search" placeholder="Rechercher un niveau…"
                                           oninput="filterSS('fil-niveau',this.value)">
                                    <div class="ss-list" id="fil-niveau-list">
                                        <div class="ss-opt active" data-val=""
                                             onclick="selectSS('fil-niveau','','Tous les niveaux');filterClasses()">
                                             Tous les niveaux</div>
                                        @foreach($classes->pluck('niveau')->filter()->unique('id') as $niv)
                                            <div class="ss-opt" data-val="{{ $niv->id }}"
                                                 onclick="selectSS('fil-niveau','{{ $niv->id }}','{{ $niv->name }}');filterClasses()">
                                                {{ $niv->name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" id="fil-niveau-val" value="">
                            </div>
                        </div>

                        <div class="fg">
                            <label class="lbl">Classe <span style="color:var(--err)">*</span></label>
                            <div class="ss-wrap">
                                <input class="ss-input" id="fil-classe-display"
                                       placeholder="Sélectionner une classe…" readonly
                                       onclick="toggleSS('fil-classe')">
                                <div class="ss-dropdown" id="fil-classe-dd">
                                    <input class="ss-search" placeholder="Rechercher une classe…"
                                           oninput="filterSS('fil-classe',this.value)">
                                    <div class="ss-list" id="fil-classe-list">
                                        <div class="ss-opt disabled">— Sélectionnez d'abord un niveau —</div>
                                        @foreach($classes as $c)
                                            <div class="ss-opt" data-val="{{ $c->id }}" data-niveau="{{ $c->niveau_id }}"
                                                 onclick="selectSS('fil-classe','{{ $c->id }}','{{ $c->name }}');filterEvals()">
                                                {{ $c->name }}
                                                @if($c->niveau)<span style="color:var(--mist);font-size:.68rem"> — {{ $c->niveau->name }}</span>@endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" id="fil-classe-val" value="">
                            </div>
                        </div>

                        <div class="fg">
                            <label class="lbl">Évaluation <span style="color:var(--err)">*</span></label>
                            <div class="ss-wrap">
                                <input class="ss-input" id="fil-eval-display"
                                       placeholder="Choisir une évaluation…" readonly
                                       onclick="toggleSS('fil-eval')">
                                <div class="ss-dropdown" id="fil-eval-dd">
                                    <input class="ss-search" placeholder="Rechercher une évaluation…"
                                           oninput="filterSS('fil-eval',this.value)">
                                    <div class="ss-list" id="fil-eval-list">
                                        <div class="ss-opt disabled">— Sélectionnez d'abord une classe —</div>
                                        @foreach($evaluations as $e)
                                            <div class="ss-opt" data-val="{{ $e->id }}"
                                                 data-classe="{{ $e->subject->class_id }}"
                                                 onclick="goToEval({{ $e->id }})">
                                                {{ $e->title }}
                                                <span style="color:var(--mist);font-size:.7rem"> — {{ $e->subject->name ?? '' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Nouvelle évaluation (panneau caché) --}}
                <div class="card" id="card-new-eval" style="display:none;">
                    <div class="card-hd">
                        <h3>Nouvelle évaluation</h3>
                        <button class="btn btn-ot btn-sm"
                                onclick="document.getElementById('card-new-eval').style.display='none'">✕</button>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.evaluations.store') }}" id="form-new-eval">
                            @csrf
                            <div class="tip-box">
                                Sélectionnez <strong>niveau</strong> → <strong>classe</strong> → <strong>matière</strong>.
                            </div>

                            <div class="fg-stack">
                                {{-- Niveau --}}
                                <div class="fg">
                                    <label class="lbl">Niveau</label>
                                    <div class="ss-wrap">
                                        <input class="ss-input" id="ev-niveau-display"
                                               placeholder="Tous les niveaux" readonly onclick="toggleSS('ev-niveau')">
                                        <div class="ss-dropdown" id="ev-niveau-dd">
                                            <input class="ss-search" placeholder="Rechercher…"
                                                   oninput="filterSS('ev-niveau',this.value)">
                                            <div class="ss-list" id="ev-niveau-list">
                                                <div class="ss-opt active" data-val=""
                                                     onclick="selectSS('ev-niveau','','Tous');filterEvClasses()">Tous</div>
                                                @foreach($classes->pluck('niveau')->filter()->unique('id') as $niv)
                                                    <div class="ss-opt" data-val="{{ $niv->id }}"
                                                         onclick="selectSS('ev-niveau','{{ $niv->id }}','{{ $niv->name }}');filterEvClasses()">
                                                         {{ $niv->name }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <input type="hidden" id="ev-niveau-val" value="">
                                    </div>
                                </div>

                                {{-- Classe --}}
                                <div class="fg">
                                    <label class="lbl">Classe <span style="color:var(--err)">*</span></label>
                                    <div class="ss-wrap">
                                        <input class="ss-input" id="ev-classe-display"
                                               placeholder="Sélectionner une classe…" readonly onclick="toggleSS('ev-classe')">
                                        <div class="ss-dropdown" id="ev-classe-dd">
                                            <input class="ss-search" placeholder="Rechercher une classe…"
                                                   oninput="filterSS('ev-classe',this.value)">
                                            <div class="ss-list" id="ev-classe-list">
                                                @foreach($classes as $c)
                                                    <div class="ss-opt" data-val="{{ $c->id }}" data-niveau="{{ $c->niveau_id }}"
                                                         onclick="selectSS('ev-classe','{{ $c->id }}','{{ $c->name }}');filterEvSubjects()">
                                                         {{ $c->name }}
                                                         @if($c->niveau)<span style="color:var(--mist);font-size:.68rem"> — {{ $c->niveau->name }}</span>@endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <input type="hidden" id="ev-classe-val" value="">
                                    </div>
                                </div>

                                {{-- Matière --}}
                                <div class="fg">
                                    <label class="lbl">Matière <span style="color:var(--err)">*</span></label>
                                    <div class="ss-wrap">
                                        <input class="ss-input" id="ev-subject-display"
                                               placeholder="Sélectionner une matière…" readonly onclick="toggleSS('ev-subject')">
                                        <div class="ss-dropdown" id="ev-subject-dd">
                                            <input class="ss-search" placeholder="Rechercher une matière…"
                                                   oninput="filterSS('ev-subject',this.value)">
                                            <div class="ss-list" id="ev-subject-list">
                                                <div class="ss-opt disabled">— Sélectionnez d'abord une classe —</div>
                                                @foreach($subjects as $s)
                                                    <div class="ss-opt" data-val="{{ $s->id }}" data-classe="{{ $s->class_id }}"
                                                         onclick="selectSS('ev-subject','{{ $s->id }}','{{ $s->name }}')">
                                                         {{ $s->name }}
                                                         @if($s->coefficient)<span style="color:var(--mist);font-size:.68rem"> (coef. {{ $s->coefficient }})</span>@endif
                                                         @if($s->teacher)<span style="color:var(--mist);font-size:.68rem"> — {{ $s->teacher->prenom }} {{ $s->teacher->nom }}</span>@endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <input type="hidden" id="ev-subject-val" name="subject_id" value="" required>
                                    </div>
                                </div>

                                {{-- Titre + Type --}}
                                <div class="fg2">
                                    <div class="fg">
                                        <label class="lbl">Titre <span style="color:var(--err)">*</span></label>
                                        <input class="inp" name="title" id="ev-title" placeholder="Contrôle Ch.1" required>
                                    </div>
                                    <div class="fg">
                                        <label class="lbl">Type <span style="color:var(--err)">*</span></label>
                                        <select class="inp" name="type" required>
                                            <option value="controle">Contrôle</option>
                                            <option value="examen">Examen</option>
                                            <option value="tp">TP</option>
                                            <option value="projet">Projet</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Note max + Période --}}
                                <div class="fg2">
                                    <div class="fg">
                                        <label class="lbl">Note max <span style="color:var(--err)">*</span></label>
                                        <input class="inp" name="max_score" type="number" value="20" min="1" max="1000" required>
                                    </div>
                                    <div class="fg">
                                        <label class="lbl">Période</label>
                                        <select class="inp" name="periode">
                                            <option value="">—</option>
                                            @foreach($periodes as $p)
                                                <option value="{{ $p['key'] }}">{{ $p['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Date --}}
                                <div class="fg">
                                    <label class="lbl">Date <span style="color:var(--err)">*</span></label>
                                    <input class="inp" name="date" type="date" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <button class="btn btn-gold btn-full" type="submit" id="btn-creer-eval">
                                    ✓ Créer l'évaluation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>{{-- /panneau gauche --}}

            {{-- Panneau droit : saisie --}}
            <div>
                <div class="card">
                    <div class="card-hd">
                        <h3>Saisie des notes</h3>
                        @if(isset($selectedEval) && $selectedEval)
                            <span style="font-size:.7rem;color:var(--mist);">
                                {{ $selectedEval->title }} &middot; /{{ $selectedEval->max_score }}
                            </span>
                        @endif
                    </div>

                    @if(isset($selectedEval) && $selectedEval && isset($evalApprenants) && $evalApprenants->count())

                        <div class="eval-meta">
                            <span>📋 <strong>{{ $selectedEval->title }}</strong></span>
                            <span>📚 {{ $selectedEval->subject->name ?? '—' }}</span>
                            <span>🏫 {{ $selectedEval->subject->classe->name ?? '—' }}</span>
                            <span class="bdg bdg-b">{{ $selectedEval->type_evaluation ?? $selectedEval->type }}</span>
                            <span>📊 /{{ $selectedEval->max_score }}</span>
                            @if($selectedEval->periode)
                                <span style="color:var(--mist)">📅 {{ $selectedEval->periode }}</span>
                            @endif
                            <span style="margin-left:auto;color:var(--ok);font-weight:600">
                                {{ $evalApprenants->count() }} apprenants
                            </span>
                        </div>

                        <div style="padding:.55rem 1.25rem;border-bottom:1px solid var(--brd);">
                            <input class="inp" id="search-ap"
                                   placeholder="🔍 Rechercher un apprenant…"
                                   oninput="filterApprenants(this.value)"
                                   style="max-width:300px;">
                        </div>

                        <form method="POST" action="{{ route('admin.grades.store') }}">
                            @csrf
                            <input type="hidden" name="evaluation_id" value="{{ $selectedEval->id }}">

                            <div class="grades-header">
                                <span>Apprenant</span>
                                <span>/ {{ $selectedEval->max_score }}</span>
                                <span>/ 20</span>
                            </div>

                            <div id="grades-list">
                                @foreach($evalApprenants as $ap)
                                    @php $eg = $selectedEval->grades->firstWhere('apprenant_id', $ap->id); @endphp
                                    <div class="grade-row ap-row"
                                         data-name="{{ strtolower($ap->prenom . ' ' . $ap->nom) }}">
                                        <div>
                                            <div style="font-weight:600;font-size:.82rem;">
                                                {{ $ap->prenom }} {{ $ap->nom }}
                                            </div>
                                            <div style="font-size:.66rem;color:var(--mist);font-family:monospace;">
                                                {{ $ap->matricule }}
                                            </div>
                                        </div>
                                        <div style="text-align:center;">
                                            <input type="number"
                                                   name="grades[{{ $ap->id }}]"
                                                   class="grade-input"
                                                   min="0"
                                                   max="{{ $selectedEval->max_score }}"
                                                   step="0.25"
                                                   value="{{ $eg ? $eg->score : '' }}"
                                                   placeholder="—"
                                                   data-max="{{ $selectedEval->max_score }}"
                                                   oninput="liveGrade(this)">
                                        </div>
                                        <div class="preview-20"
                                             style="font-size:.81rem;font-weight:700;color:var(--mist);text-align:center;">
                                            @if($eg)
                                                {{ round(($eg->score / $selectedEval->max_score) * 20, 1) }}
                                            @else —
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="grade-actions">
                                <button type="button" class="btn btn-ot btn-sm" onclick="fillAll()">Remplir tout</button>
                                <button type="button" class="btn btn-ot btn-sm" onclick="clearAll()">Effacer tout</button>
                                <button type="submit" class="btn btn-gold">
                                    💾 Enregistrer ({{ $evalApprenants->count() }})
                                </button>
                            </div>
                        </form>

                    @elseif(isset($selectedEval) && $selectedEval && $evalApprenants->isEmpty())
                        <div class="n-empty">
                            <div class="n-empty-ico">⚠️</div>
                            <h4>Aucun apprenant dans cette classe</h4>
                            <p>{{ $selectedEval->subject->classe->name ?? '' }} ne contient pas encore d'apprenants.</p>
                        </div>
                    @else
                        <div class="n-empty">
                            <div class="n-empty-ico">📝</div>
                            <h4>Sélectionnez une évaluation</h4>
                            <p>Choisissez une classe et une évaluation dans le panneau de gauche.</p>
                            <button class="btn btn-gold" style="margin-top:1rem" onclick="openNewEval()">
                                + Créer une évaluation
                            </button>
                        </div>
                    @endif
                </div>
            </div>{{-- /panneau droit --}}

        </div>{{-- /saisie-grid --}}
    </div>

    {{-- ══════════════════════════════════════════
         TAB : ÉVALUATIONS
    ══════════════════════════════════════════ --}}
    <div class="n-section" id="tab-evaluations">
        <div style="display:flex;gap:.625rem;flex-wrap:wrap;margin-bottom:1rem;align-items:center;">
            <div class="ss-wrap" style="min-width:180px;max-width:220px;">
                <input class="ss-input" id="fev-classe-display" placeholder="Toutes les classes"
                       readonly onclick="toggleSS('fev-classe')"
                       style="height:36px;font-size:.79rem;">
                <div class="ss-dropdown" id="fev-classe-dd">
                    <input class="ss-search" placeholder="Rechercher…" oninput="filterSS('fev-classe',this.value)">
                    <div class="ss-list" id="fev-classe-list">
                        <div class="ss-opt active" data-val=""
                             onclick="selectSS('fev-classe','','Toutes les classes');filterEvalTable()">
                             Toutes les classes</div>
                        @foreach($classes as $c)
                            <div class="ss-opt" data-val="{{ $c->id }}"
                                 onclick="selectSS('fev-classe','{{ $c->id }}','{{ $c->name }}');filterEvalTable()">
                                 {{ $c->name }}</div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" id="fev-classe-val" value="">
            </div>
            <select class="inp" style="max-width:160px;height:36px;font-size:.79rem;"
                    onchange="filterEvalTableType(this.value)">
                <option value="">Tous les types</option>
                <option value="controle">Contrôle</option>
                <option value="examen">Examen</option>
                <option value="tp">TP</option>
                <option value="projet">Projet</option>
                <option value="composition">Composition</option>
            </select>
            <button class="btn btn-gold btn-sm" onclick="openNewEval()">+ Nouvelle évaluation</button>
            <span id="eval-count" style="font-size:.76rem;color:var(--mist);margin-left:auto;"></span>
        </div>

        <div class="card tbl-outer">
            <table class="n-tbl" id="eval-table">
                <thead>
                    <tr>
                        <th>Évaluation</th>
                        <th>Matière</th>
                        <th>Classe</th>
                        <th>Niveau</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th>Barème</th>
                        <th>Complétion</th>
                        <th style="width:120px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $eval)
                        @php
                            $nbG = $eval->grades->count();
                            $nbA = $eval->subject->classe?->apprenants?->count() ?? 0;
                            $pct = $nbA > 0 ? round(($nbG / $nbA) * 100) : 0;
                        @endphp
                        <tr data-classe="{{ $eval->subject->class_id }}" data-type="{{ $eval->type }}">
                            <td>
                                <div style="font-weight:600;font-size:.82rem;">{{ $eval->title }}</div>
                                <div style="font-size:.67rem;color:var(--mist);">{{ $eval->date }}</div>
                            </td>
                            <td style="font-size:.79rem;">{{ $eval->subject->name ?? '—' }}</td>
                            <td style="font-size:.79rem;">{{ $eval->subject->classe->name ?? '—' }}</td>
                            <td style="font-size:.76rem;color:var(--mist);">{{ $eval->subject->classe->niveau->name ?? '—' }}</td>
                            <td><span class="bdg bdg-b">{{ $eval->type }}</span></td>
                            <td style="font-size:.76rem;color:var(--mist);">{{ $eval->periode ?? '—' }}</td>
                            <td style="font-weight:700;">{{ $eval->max_score }}</td>
                            <td>
                                <span style="font-size:.76rem;">{{ $nbG }}/{{ $nbA }}</span>
                                <div class="prog-wrap" style="width:80px;">
                                    <div class="prog-bar" style="width:{{ $pct }}%;background:{{ $pct >= 80 ? 'var(--ok)' : ($pct >= 50 ? 'var(--warn)' : 'var(--err)') }};"></div>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;gap:.3rem;flex-wrap:wrap;">
                                    <a href="{{ route('admin.grade_config', ['evaluation_id' => $eval->id]) }}"
                                       class="btn btn-ot btn-sm">📝 Saisir</a>
                                    <form method="POST" action="{{ route('admin.evaluations.destroy', $eval) }}" style="display:contents;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm"
                                                style="background:var(--err-l);color:var(--err);border:none;cursor:pointer;"
                                                onclick="return confirm('Supprimer cette évaluation et ses notes ?')">✕</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="n-empty">
                                    <div class="n-empty-ico">📋</div>
                                    <h4>Aucune évaluation</h4>
                                    <p>Créez une évaluation pour commencer à noter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($evaluations instanceof \Illuminate\Pagination\LengthAwarePaginator && $evaluations->hasPages())
            <div style="margin-top:1rem;">{{ $evaluations->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════
         TAB : BULLETINS
    ══════════════════════════════════════════ --}}
    <div class="n-section" id="tab-bulletins">

        {{-- Actions par classe --}}
        <div class="card" style="margin-bottom:1rem;">
            <div class="card-hd">
                <h3>Actions par classe</h3>
                <form method="POST" action="{{ route('admin.bulletins.calcul.tous') }}"
                      style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                    @csrf
                    <select name="periode" class="inp" style="max-width:170px;height:34px;font-size:.79rem;">
                        @foreach($periodes as $p)
                            <option value="{{ $p['key'] }}">{{ $p['label'] }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-dk btn-sm">⚙ Calculer toutes</button>
                </form>
            </div>
            <div class="card-body">
                <div class="classe-grid">
                    @foreach($classes as $classe)
                        <div class="classe-card">
                            <div class="classe-card-name">{{ $classe->name }}</div>
                            @if($classe->niveau)
                                <div class="classe-card-niv">{{ $classe->niveau->name }}</div>
                            @endif
                            <div class="classe-card-count">{{ $classe->apprenants_count ?? 0 }} apprenant(s)</div>
                            <div class="classe-card-acts">
                                <button class="btn btn-ot btn-sm"
                                        onclick="openCalcClasse({{ $classe->id }},'{{ addslashes($classe->name) }}')">
                                    ⚙ Calculer
                                </button>
                                <button class="btn btn-ok btn-sm"
                                        onclick="openPublierClasse({{ $classe->id }},'{{ addslashes($classe->name) }}')">
                                    ↑ Publier
                                </button>
                                <button class="btn btn-sm"
                                        style="background:var(--err-l);color:var(--err);border:none;cursor:pointer;"
                                        onclick="openDepublierClasse({{ $classe->id }},'{{ addslashes($classe->name) }}')">
                                    ↓ Dépublier
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Filtres bulletins --}}
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem;">
            <form method="GET" style="display:contents;">
                <input type="hidden" name="tab" value="bulletins">
                <select class="inp" name="periode" style="max-width:170px;height:36px;font-size:.79rem;" onchange="this.form.submit()">
                    @foreach($periodes as $p)
                        <option value="{{ $p['key'] }}" @selected(request('periode') == $p['key'])>{{ $p['label'] }}</option>
                    @endforeach
                </select>
                <select class="inp" name="classe_id" style="max-width:190px;height:36px;font-size:.79rem;" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('classe_id') == $c->id)>
                            {{ $c->name }}@if($c->niveau) — {{ $c->niveau->name }}@endif
                        </option>
                    @endforeach
                </select>
                <select class="inp" name="publie" style="max-width:130px;height:36px;font-size:.79rem;" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('publie') === '1')>Publiés</option>
                    <option value="0" @selected(request('publie') === '0')>Non publiés</option>
                </select>
            </form>
        </div>

        {{-- Table bulletins --}}
        <div class="card tbl-outer">
            @if(isset($bulletins) && $bulletins->count())
                <table class="n-tbl">
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Classe</th>
                            <th>Moyenne</th>
                            <th>Rang</th>
                            <th>Mention</th>
                            <th>Statut</th>
                            <th style="width:110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bulletins as $b)
                            @php
                                $pct = $config->note_max > 0 ? round(($b->moyenne_generale / $config->note_max) * 100) : 0;
                                $sc  = $pct >= 70 ? 'score-hi' : ($pct >= 50 ? 'score-mid' : 'score-lo');
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:.82rem;">
                                        {{ $b->apprenant?->prenom }} {{ $b->apprenant?->nom }}
                                    </div>
                                    <div style="font-size:.66rem;color:var(--mist);font-family:monospace;">
                                        {{ $b->apprenant?->matricule }}
                                    </div>
                                </td>
                                <td style="font-size:.79rem;">{{ $b->classe?->name ?? '—' }}</td>
                                <td>
                                    <span class="score {{ $sc }}">
                                        {{ $b->moyenne_generale }}/{{ $config->note_max }}
                                    </span>
                                </td>
                                <td style="font-weight:700;font-size:.84rem;">#{{ $b->rang ?? '—' }}</td>
                                <td style="font-size:.77rem;">{{ $b->mention ?? '—' }}</td>
                                <td>
                                    @if($b->publie)
                                        <span class="bdg bdg-g">Publié</span>
                                    @elseif($b->calcule_at)
                                        <span class="bdg bdg-b">Calculé</span>
                                    @else
                                        <span class="bdg bdg-n">Brouillon</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;gap:.3rem;flex-wrap:wrap;">
                                        <a href="{{ route('admin.bulletins.show', $b) }}"
                                           class="btn btn-ot btn-sm">Voir</a>
                                        @if(!$b->publie && $b->calcule_at)
                                            <form method="POST" action="{{ route('admin.bulletins.publier', $b) }}" style="display:contents;">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-ok btn-sm">↑</button>
                                            </form>
                                        @elseif($b->publie)
                                            <form method="POST" action="{{ route('admin.bulletins.depublier', $b) }}" style="display:contents;">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-sm"
                                                        style="background:var(--err-l);color:var(--err);border:none;cursor:pointer;">↓</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($bulletins->hasPages())
                    <div style="padding:.875rem 1.25rem;border-top:1px solid var(--brd);">
                        {{ $bulletins->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="n-empty">
                    <div class="n-empty-ico">📄</div>
                    <h4>Aucun bulletin</h4>
                    <p>Calculez les bulletins pour une classe et une période.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TAB : CONFIGURATION
    ══════════════════════════════════════════ --}}
    <div class="n-section" id="tab-config">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

            {{-- Formulaire config --}}
            <div class="card">
                <div class="card-hd"><h3>Règles de calcul</h3></div>
                <form method="POST" action="{{ route('admin.grade_config.update') }}" class="card-body">
                    @csrf @method('PATCH')
                    <div class="fg-stack">
                        <div class="fg2">
                            <div class="fg">
                                <label class="lbl">Note max *</label>
                                <input class="inp" type="number" name="note_max" value="{{ $config->note_max }}" min="1" required>
                            </div>
                            <div class="fg">
                                <label class="lbl">Note de passage *</label>
                                <input class="inp" type="number" name="note_passage" value="{{ $config->note_passage }}" min="0" required>
                            </div>
                        </div>
                        <div class="fg2">
                            <div class="fg">
                                <label class="lbl">% Devoirs</label>
                                <input class="inp" type="number" name="pct_devoirs" id="pct_d"
                                       value="{{ $config->pct_devoirs }}" min="0" max="100"
                                       oninput="document.getElementById('pct_e').value=100-this.value">
                            </div>
                            <div class="fg">
                                <label class="lbl">% Examen (auto)</label>
                                <input class="inp" type="number" name="pct_examen" id="pct_e"
                                       value="{{ $config->pct_examen }}" readonly style="background:var(--bg);">
                            </div>
                        </div>
                        <div class="fg2">
                            <div class="fg">
                                <label class="lbl">Type périodes</label>
                                <select class="inp" name="type_periodes">
                                    <option value="trimestres" @selected($config->type_periodes=='trimestres')>Trimestres</option>
                                    <option value="semestres"  @selected($config->type_periodes=='semestres')>Semestres</option>
                                </select>
                            </div>
                            <div class="fg">
                                <label class="lbl">Nb périodes</label>
                                <select class="inp" name="nb_periodes">
                                    @for($i=1;$i<=4;$i++)
                                        <option value="{{ $i }}" @selected($config->nb_periodes==$i)>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="fg2">
                            <div class="fg">
                                <label class="lbl">Décimales</label>
                                <select class="inp" name="decimales">
                                    @for($i=0;$i<=4;$i++)
                                        <option value="{{ $i }}" @selected($config->decimales==$i)>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="fg" style="justify-content:flex-end;padding-top:1.35rem;">
                                <label style="display:flex;align-items:center;gap:.5rem;font-size:.81rem;cursor:pointer;">
                                    <input type="checkbox" name="compensation_active" value="1"
                                           @checked($config->compensation_active ?? false)>
                                    Compensation active
                                </label>
                            </div>
                        </div>
                        <button class="btn btn-gold btn-full">Enregistrer la configuration</button>
                    </div>
                </form>
            </div>

            {{-- Récap + notes récentes --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div class="card">
                    <div class="card-hd"><h3>Paramètres actuels</h3></div>
                    <div class="card-body">
                        @foreach([
                            ['Note max',       $config->note_max],
                            ['Note de passage', $config->note_passage],
                            ['% Devoirs',      $config->pct_devoirs.'%'],
                            ['% Examen',       $config->pct_examen.'%'],
                            ['Type périodes',  ucfirst($config->type_periodes)],
                            ['Nb périodes',    $config->nb_periodes],
                            ['Décimales',      $config->decimales],
                            ['Année',          $config->annee_academique],
                        ] as [$l,$v])
                            <div class="conf-item">
                                <span class="conf-lbl">{{ $l }}</span>
                                <span class="conf-val">{{ $v }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card">
                    <div class="card-hd"><h3>Notes récentes</h3></div>
                    <div style="max-height:280px;overflow-y:auto;">
                        @forelse($notesRecentes as $n)
                            <div style="display:flex;align-items:center;gap:.75rem;padding:.575rem 1.25rem;border-bottom:1px solid #f3f4f6;">
                                <div style="width:30px;height:30px;border-radius:7px;background:var(--bg);border:1px solid var(--brd);display:flex;align-items:center;justify-content:center;font-size:.63rem;font-weight:700;color:var(--text-s);flex-shrink:0;">
                                    {{ mb_substr($n->prenom,0,1) }}{{ mb_substr($n->nom,0,1) }}
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:.77rem;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $n->prenom }} {{ $n->nom }}
                                    </div>
                                    <div style="font-size:.65rem;color:var(--mist);">{{ $n->matiere }}</div>
                                </div>
                                @php
                                    $r  = $n->max_score > 0 ? $n->score / $n->max_score : 0;
                                    $sc = $r >= 0.7 ? 'score-hi' : ($r >= 0.5 ? 'score-mid' : 'score-lo');
                                @endphp
                                <span class="score {{ $sc }}" style="font-size:.68rem;min-width:44px;">
                                    {{ $n->score }}/{{ $n->max_score }}
                                </span>
                            </div>
                        @empty
                            <div style="padding:1.5rem;text-align:center;color:var(--mist);font-size:.8rem;">Aucune note récente</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>{{-- /pw --}}

{{-- ══════════════════════════════════════════
     MODALS CALCUL / PUBLICATION
══════════════════════════════════════════ --}}
@foreach([
    ['modal-calc-classe', 'calc_classe_id', 'Calculer',  'calc-titre', route('admin.bulletins.calcul.classe'),    'btn-gold', 'Calculer'],
    ['modal-pub-classe',  'pub_classe_id',  'Publier',   'pub-titre',  route('admin.bulletins.publier.classe'),   'btn-ok',   'Publier'],
    ['modal-dep-classe',  'dep_classe_id',  'Dépublier', 'dep-titre',  route('admin.bulletins.depublier.classe'), 'btn-err',  'Dépublier'],
] as [$mid, $hid, $verb, $tid, $action, $bcls, $blbl])
<div class="n-modal" id="{{ $mid }}">
    <div class="n-modal-box sm">
        <div class="n-modal-hd">
            <h3 id="{{ $tid }}">{{ $verb }}</h3>
            <button class="btn btn-ot btn-sm" onclick="closeModal('{{ $mid }}')">✕</button>
        </div>
        <form method="POST" action="{{ $action }}">
            @csrf
            <input type="hidden" name="classe_id" id="{{ $hid }}">
            <div class="n-modal-body">
                <div class="fg">
                    <label class="lbl">Période *</label>
                    <select class="inp" name="periode" required>
                        @foreach($periodes as $p)
                            <option value="{{ $p['key'] }}">{{ $p['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="n-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeModal('{{ $mid }}')">Annuler</button>
                <button type="submit" class="btn {{ $bcls }}">{{ $blbl }}</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- MODAL CONFIG RAPIDE --}}
<div class="n-modal" id="modal-config">
    <div class="n-modal-box">
        <div class="n-modal-hd">
            <h3>Configuration notation</h3>
            <button class="btn btn-ot btn-sm" onclick="closeModal('modal-config')">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.grade_config.update') }}">
            @csrf @method('PATCH')
            <div class="n-modal-body">
                <div class="fg-stack">
                    <div class="fg2">
                        <div class="fg">
                            <label class="lbl">Note max *</label>
                            <input class="inp" type="number" name="note_max" value="{{ $config->note_max }}" min="1" required>
                        </div>
                        <div class="fg">
                            <label class="lbl">Note de passage *</label>
                            <input class="inp" type="number" name="note_passage" value="{{ $config->note_passage }}" min="0" required>
                        </div>
                    </div>
                    <div class="fg2">
                        <div class="fg">
                            <label class="lbl">% Devoirs</label>
                            <input class="inp" type="number" name="pct_devoirs" value="{{ $config->pct_devoirs }}" min="0" max="100">
                        </div>
                        <div class="fg">
                            <label class="lbl">Décimales</label>
                            <select class="inp" name="decimales">
                                @for($i=0;$i<=4;$i++)
                                    <option value="{{ $i }}" @selected($config->decimales==$i)>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="fg2">
                        <div class="fg">
                            <label class="lbl">Type périodes</label>
                            <select class="inp" name="type_periodes">
                                <option value="trimestres" @selected($config->type_periodes=='trimestres')>Trimestres</option>
                                <option value="semestres"  @selected($config->type_periodes=='semestres')>Semestres</option>
                            </select>
                        </div>
                        <div class="fg">
                            <label class="lbl">Nb périodes</label>
                            <select class="inp" name="nb_periodes">
                                @for($i=1;$i<=4;$i++)
                                    <option value="{{ $i }}" @selected($config->nb_periodes==$i)>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="n-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeModal('modal-config')">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
@php
    $subjectsData = $subjects->map(fn($s) => [
        'id'       => $s->id,
        'name'     => $s->name,
        'class_id' => $s->class_id,
        'coef'     => $s->coefficient,
        'teacher'  => $s->teacher ? $s->teacher->prenom.' '.$s->teacher->nom : null,
    ]);
    $classesData = $classes->map(fn($c) => [
        'id'       => $c->id,
        'name'     => $c->name,
        'niveau_id'=> $c->niveau_id,
        'niveau'   => $c->niveau?->name,
    ]);
    $evalsData = ($evaluations instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? $evaluations->getCollection()
        : $evaluations
    )->map(fn($e) => [
        'id'       => $e->id,
        'title'    => $e->title,
        'class_id' => $e->subject?->class_id,
        'subject'  => $e->subject?->name,
    ]);
@endphp
<script>
/* ════════════════ DATA ════════════════ */
const SUBJECTS_DATA = @json($subjectsData);
const CLASSES_DATA  = @json($classesData);
const EVALS_DATA    = @json($evalsData);

/* ════════════════ TABS ════════════════ */
function showTab(id, btn) {
    document.querySelectorAll('.n-section').forEach(s => s.classList.remove('on'));
    document.querySelectorAll('.n-tab').forEach(b => b.classList.remove('on'));
    document.getElementById('tab-' + id).classList.add('on');
    if (btn) btn.classList.add('on');
}
// Restore tab from URL
(function(){
    const tabMap = { saisie:0, evaluations:1, bulletins:2, config:3 };
    const t = new URLSearchParams(location.search).get('tab');
    if (t && tabMap[t] !== undefined) {
        showTab(t, document.querySelectorAll('.n-tab')[tabMap[t]]);
    }
})();

/* ════════════════ MODALS ════════════════ */
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
function openNewEval() {
    document.getElementById('card-new-eval').style.display = 'block';
    // Switch to saisie tab
    showTab('saisie', document.querySelectorAll('.n-tab')[0]);
    setTimeout(() => {
        document.getElementById('card-new-eval').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 120);
}
function openCalcClasse(id, name)     { document.getElementById('calc_classe_id').value = id; document.getElementById('calc-titre').textContent = 'Calculer — ' + name; openModal('modal-calc-classe'); }
function openPublierClasse(id, name)  { document.getElementById('pub_classe_id').value  = id; document.getElementById('pub-titre').textContent  = 'Publier — '   + name; openModal('modal-pub-classe'); }
function openDepublierClasse(id,name) { document.getElementById('dep_classe_id').value  = id; document.getElementById('dep-titre').textContent  = 'Dépublier — ' + name; openModal('modal-dep-classe'); }

// Close modal on backdrop click
document.querySelectorAll('.n-modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) { m.classList.remove('open'); document.body.style.overflow = ''; } });
});
// Close on Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.n-modal.open').forEach(m => { m.classList.remove('open'); document.body.style.overflow = ''; });
    }
});

/* ════════════════ SEARCHABLE SELECT ════════════════ */
function toggleSS(id) {
    const dd = document.getElementById(id + '-dd');
    const isOpen = dd.classList.contains('open');
    document.querySelectorAll('.ss-dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) {
        dd.classList.add('open');
        const si = dd.querySelector('.ss-search');
        if (si) { si.value = ''; si.focus(); filterSS(id, ''); }
    }
}
document.addEventListener('click', e => {
    if (!e.target.closest('.ss-wrap')) {
        document.querySelectorAll('.ss-dropdown.open').forEach(d => d.classList.remove('open'));
    }
});
function filterSS(id, q) {
    const list = document.getElementById(id + '-list');
    const opts = list.querySelectorAll('.ss-opt:not(.disabled)');
    const lq   = q.toLowerCase();
    let count  = 0;
    opts.forEach(o => {
        const show = !lq || o.textContent.toLowerCase().includes(lq);
        o.style.display = show ? '' : 'none';
        if (show) count++;
    });
    let empty = list.querySelector('.ss-empty');
    if (count === 0 && !empty) {
        empty = document.createElement('div');
        empty.className = 'ss-empty';
        empty.textContent = 'Aucun résultat';
        list.appendChild(empty);
    } else if (count > 0 && empty) {
        empty.remove();
    }
}
function selectSS(id, val, label) {
    const disp = document.getElementById(id + '-display');
    if (disp) disp.value = label;
    const hid = document.getElementById(id + '-val');
    if (hid) hid.value = val;
    document.getElementById(id + '-dd')?.classList.remove('open');
    document.querySelectorAll('#' + id + '-list .ss-opt').forEach(o =>
        o.classList.toggle('active', o.dataset.val === String(val))
    );
}

/* ════════════════ FILTRAGE CLASSES (panneau saisie) ════════════════ */
function filterClasses() {
    const niv  = document.getElementById('fil-niveau-val').value;
    const list = document.getElementById('fil-classe-list');
    list.innerHTML = '';
    const filtered = CLASSES_DATA.filter(c => !niv || String(c.niveau_id) === String(niv));
    if (!filtered.length) {
        list.innerHTML = '<div class="ss-opt disabled">Aucune classe pour ce niveau</div>';
        return;
    }
    filtered.forEach(c => {
        const d = document.createElement('div');
        d.className   = 'ss-opt';
        d.dataset.val = c.id;
        d.dataset.niveau = c.niveau_id || '';
        d.innerHTML   = c.name + (c.niveau ? ` <span style="color:var(--mist);font-size:.68rem"> — ${c.niveau}</span>` : '');
        d.onclick     = () => { selectSS('fil-classe', String(c.id), c.name); filterEvals(); };
        list.appendChild(d);
    });
    // Reset
    document.getElementById('fil-classe-display').value = '';
    document.getElementById('fil-classe-val').value = '';
    filterEvals();
}

/* ════════════════ FILTRAGE ÉVALS (panneau saisie) ════════════════ */
function filterEvals() {
    const classeId = document.getElementById('fil-classe-val').value;
    const list     = document.getElementById('fil-eval-list');
    list.innerHTML = '';
    if (!classeId) {
        list.innerHTML = '<div class="ss-opt disabled">— Sélectionnez d\'abord une classe —</div>';
        return;
    }
    const filtered = EVALS_DATA.filter(e => String(e.class_id) === String(classeId));
    if (!filtered.length) {
        list.innerHTML = '<div class="ss-opt disabled">Aucune évaluation pour cette classe</div>';
        return;
    }
    filtered.forEach(e => {
        const d = document.createElement('div');
        d.className = 'ss-opt';
        d.dataset.val = e.id;
        d.innerHTML = e.title + (e.subject ? ` <span style="color:var(--mist);font-size:.7rem"> — ${e.subject}</span>` : '');
        d.onclick   = () => goToEval(e.id);
        list.appendChild(d);
    });
}
function goToEval(id) {
    const u = new URL(location.href);
    u.searchParams.set('evaluation_id', id);
    u.searchParams.set('tab', 'saisie');
    location.href = u.toString();
}

/* ════════════════ FILTRAGE FORM NOUVELLE ÉVAL ════════════════ */
function filterEvClasses() {
    const niv  = document.getElementById('ev-niveau-val').value;
    const list = document.getElementById('ev-classe-list');
    list.innerHTML = '';
    const filtered = CLASSES_DATA.filter(c => !niv || String(c.niveau_id) === String(niv));
    if (!filtered.length) {
        list.innerHTML = '<div class="ss-opt disabled">Aucune classe pour ce niveau</div>';
        return;
    }
    filtered.forEach(c => {
        const d = document.createElement('div');
        d.className   = 'ss-opt';
        d.dataset.val = c.id;
        d.dataset.niveau = c.niveau_id || '';
        d.innerHTML   = c.name + (c.niveau ? ` <span style="color:var(--mist);font-size:.68rem"> — ${c.niveau}</span>` : '');
        d.onclick     = () => { selectSS('ev-classe', String(c.id), c.name); filterEvSubjects(); };
        list.appendChild(d);
    });
    document.getElementById('ev-classe-display').value = '';
    document.getElementById('ev-classe-val').value = '';
    filterEvSubjects();
}
function filterEvSubjects() {
    const classeId = document.getElementById('ev-classe-val').value;
    const list     = document.getElementById('ev-subject-list');
    list.innerHTML = '';
    if (!classeId) {
        list.innerHTML = '<div class="ss-opt disabled">— Sélectionnez d\'abord une classe —</div>';
        return;
    }
    const filtered = SUBJECTS_DATA.filter(s => String(s.class_id) === String(classeId));
    if (!filtered.length) {
        list.innerHTML = '<div class="ss-opt disabled">Aucune matière pour cette classe</div>';
        return;
    }
    filtered.forEach(s => {
        const d = document.createElement('div');
        d.className   = 'ss-opt';
        d.dataset.val = s.id;
        let lbl = s.name;
        if (s.coef)    lbl += ` <span style="color:var(--mist);font-size:.68rem">(coef. ${s.coef})</span>`;
        if (s.teacher) lbl += ` <span style="color:var(--mist);font-size:.68rem"> — ${s.teacher}</span>`;
        d.innerHTML = lbl;
        d.onclick   = () => selectSS('ev-subject', String(s.id), s.name);
        list.appendChild(d);
    });
    document.getElementById('ev-subject-display').value = '';
    document.getElementById('ev-subject-val').value = '';
}

/* ════════════════ VALIDATION FORM ÉVAL ════════════════ */
document.getElementById('form-new-eval')?.addEventListener('submit', function(e) {
    const subjectId = document.getElementById('ev-subject-val').value;
    const title     = document.getElementById('ev-title').value.trim();
    if (!subjectId) { e.preventDefault(); alert('Veuillez sélectionner une matière.'); return; }
    if (!title)     { e.preventDefault(); alert('Veuillez saisir un titre.'); return; }
    const btn = document.getElementById('btn-creer-eval');
    if (btn) { btn.disabled = true; btn.textContent = 'Création en cours…'; }
});

/* ════════════════ NOTES — LIVE PREVIEW ════════════════ */
function liveGrade(inp) {
    const max = parseFloat(inp.dataset.max) || 20;
    const val = parseFloat(inp.value);
    inp.classList.remove('valid', 'over');
    const p = inp.closest('.grade-row')?.querySelector('.preview-20');
    if (!isNaN(val)) {
        inp.classList.add(val > max ? 'over' : 'valid');
        if (p) p.textContent = val > max ? '⚠' : (val / max * 20).toFixed(1);
    } else if (p) {
        p.textContent = '—';
    }
}
function fillAll() {
    const maxEl = document.querySelector('.grade-input');
    if (!maxEl) return;
    const max = parseFloat(maxEl.dataset.max) || 20;
    const v   = prompt(`Note pour tous (sur ${max}) :`);
    if (v === null) return;
    document.querySelectorAll('.grade-input').forEach(inp => {
        inp.value = Math.min(Math.max(0, parseFloat(v) || 0), max);
        liveGrade(inp);
    });
}
function clearAll() {
    document.querySelectorAll('.grade-input').forEach(inp => { inp.value = ''; liveGrade(inp); });
}
function filterApprenants(q) {
    const lq = q.toLowerCase();
    document.querySelectorAll('.ap-row').forEach(row => {
        row.style.display = (!lq || row.dataset.name.includes(lq)) ? '' : 'none';
    });
}

/* ════════════════ FILTRAGE TABLE ÉVALS ════════════════ */
function filterEvalTable() {
    const classeId = document.getElementById('fev-classe-val').value;
    let count = 0;
    document.querySelectorAll('#eval-table tbody tr[data-classe]').forEach(r => {
        const show = !classeId || r.dataset.classe == classeId;
        r.style.display = show ? '' : 'none';
        if (show) count++;
    });
    const el = document.getElementById('eval-count');
    if (el) el.textContent = count + ' évaluation(s)';
}
function filterEvalTableType(type) {
    document.querySelectorAll('#eval-table tbody tr[data-type]').forEach(r => {
        r.style.display = (!type || r.dataset.type == type) ? '' : 'none';
    });
}

/* ════════════════ INIT ════════════════ */
(function init() {
    // Compteur évaluations
    setTimeout(() => {
        const rows = document.querySelectorAll('#eval-table tbody tr[data-classe]');
        const el   = document.getElementById('eval-count');
        if (el) el.textContent = rows.length + ' évaluation(s)';
    }, 80);

    // Pré-sélection si evaluation_id dans URL
    const evalIdInUrl = new URLSearchParams(location.search).get('evaluation_id');
    if (evalIdInUrl) {
        const ev = EVALS_DATA.find(e => String(e.id) === evalIdInUrl);
        if (ev) {
            document.getElementById('fil-eval-display').value = ev.title;
            const cl = CLASSES_DATA.find(c => String(c.id) === String(ev.class_id));
            if (cl) selectSS('fil-classe', String(cl.id), cl.name);
        }
    }

    // Responsive : saisie grid stack check
    function checkSaisieGrid() {
        const grid = document.getElementById('saisie-grid');
        if (!grid) return;
        if (window.innerWidth <= 900) {
            grid.style.gridTemplateColumns = '1fr';
        } else if (window.innerWidth <= 1100) {
            grid.style.gridTemplateColumns = '260px 1fr';
        } else {
            grid.style.gridTemplateColumns = '300px 1fr';
        }
    }
    checkSaisieGrid();
    window.addEventListener('resize', checkSaisieGrid);
})();
</script>
@endpush
@endsection