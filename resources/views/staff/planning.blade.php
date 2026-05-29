@extends('staff.master')

@section('title', 'Planning')
@section('page-title', 'Planning')
@section('page-sub', 'Emploi du temps · Séances · Programmes de paiement')

@push('styles')
<style>
.fg2 { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
.fg3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.875rem; }
.fg-group { display:flex; flex-direction:column; gap:.35rem; }

/* ── Tabs navigation ── */
.plan-tabs { display:flex; border-bottom:2px solid var(--brd); margin-bottom:1.5rem; gap:.25rem; }
.plan-tab {
    padding:.625rem 1.25rem; font-size:.82rem; font-weight:600;
    color:var(--mist); border:none; background:none; cursor:pointer;
    font-family:inherit; border-bottom:2px solid transparent;
    margin-bottom:-2px; transition:all .15s;
}
.plan-tab.on { color:var(--night); border-bottom-color:var(--gold); }
.plan-tab:hover:not(.on) { color:#374151; }

/* ── Tab sections ── */
.plan-section { display:none; }
.plan-section.on { display:block; }

/* ── EDT grille ── */
.edt-cell { background:var(--white); padding:.5rem .625rem; min-height:48px; font-size:.72rem; }
.edt-header { background:#fafbfd; font-weight:700; font-size:.65rem; text-transform:uppercase; letter-spacing:.08em; color:var(--mist); display:flex; align-items:center; justify-content:center; padding:.625rem; }
.edt-time { background:#fafbfd; font-size:.68rem; font-weight:600; color:var(--mist); display:flex; align-items:center; justify-content:center; padding:.4rem; }
.edt-slot {
    background:var(--white); padding:.35rem .5rem;
    border-radius:6px; margin:2px; font-size:.7rem;
    line-height:1.4; cursor:default;
}
.edt-cours    { background:var(--info-l); border-left:2px solid var(--info); }
.edt-eval     { background:#fef3c7; border-left:2px solid var(--warn); }
.edt-examen   { background:var(--err-l); border-left:2px solid var(--err); }
.edt-pause    { background:#f3f4f6; border-left:2px solid #9ca3af; }
.edt-activite { background:#f0fdf4; border-left:2px solid var(--ok); }
.edt-slot-sub { color:var(--mist); font-size:.63rem; }
.edt-slot-time{ font-weight:700; color:#374151; }

/* ── Jour vertical list ── */
.jour-block { margin-bottom:1.5rem; }
.jour-hd { display:flex; align-items:center; gap:.75rem; margin-bottom:.75rem; }
.jour-label { font-family:'Syne',sans-serif; font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--night); }
.jour-count { font-size:.68rem; color:var(--mist); background:var(--bg); border:1px solid var(--brd); border-radius:20px; padding:.1rem .5rem; }
.jour-line { flex:1; height:1px; background:var(--brd); }

/* ── Creneau card ── */
.creneau-card {
    display:grid; grid-template-columns:60px 1fr auto; gap:.75rem;
    align-items:center; background:var(--white); border:1px solid var(--brd);
    border-radius:10px; padding:.75rem 1rem; margin-bottom:.5rem;
    transition:box-shadow .15s; position:relative; overflow:hidden;
}
.creneau-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; }
.creneau-card.c-cours::before    { background:var(--info); }
.creneau-card.c-eval::before     { background:var(--warn); }
.creneau-card.c-examen::before   { background:var(--err); }
.creneau-card.c-pause::before    { background:#9ca3af; }
.creneau-card.c-activite::before { background:var(--ok); }
.creneau-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.08); }
.creneau-time { font-family:'Syne',sans-serif; font-size:.78rem; font-weight:700; color:var(--night); text-align:center; }
.creneau-time span { display:block; font-size:.65rem; color:var(--mist); font-weight:400; font-family:'DM Sans',sans-serif; }

/* ── Séance card ── */
.seance-card {
    display:flex; align-items:center; gap:.75rem;
    background:var(--white); border:1px solid var(--brd); border-radius:10px;
    padding:.75rem 1rem; margin-bottom:.5rem; transition:box-shadow .15s;
}
.seance-card:hover { box-shadow:0 2px 12px rgba(0,0,0,.08); }
.seance-date { flex-shrink:0; text-align:center; width:44px; background:var(--bg); border:1px solid var(--brd); border-radius:8px; padding:.35rem .25rem; }
.seance-date-day { font-family:'Syne',sans-serif; font-size:1rem; font-weight:800; color:var(--night); line-height:1; }
.seance-date-mois{ font-size:.6rem; color:var(--mist); text-transform:uppercase; letter-spacing:.05em; }

/* ── Programme card ── */
.prog-card {
    background:var(--white); border:1px solid var(--brd); border-radius:10px;
    padding:.875rem 1.125rem; margin-bottom:.625rem;
    display:flex; align-items:center; gap:.875rem; transition:box-shadow .15s;
}
.prog-card:hover { box-shadow:0 2px 10px rgba(0,0,0,.07); }
.prog-amount { font-family:'Syne',sans-serif; font-size:1rem; font-weight:800; color:var(--night); flex-shrink:0; min-width:90px; text-align:right; }
.prog-echeance { font-size:.7rem; color:var(--err); font-weight:600; }
.prog-echeance.ok { color:var(--ok); }

/* ── Modal planning ── */
.plan-modal { display:none; position:fixed; inset:0; z-index:500; background:rgba(8,12,20,.6); backdrop-filter:blur(4px); align-items:flex-start; justify-content:center; padding-top:3%; }
.plan-modal.open { display:flex; }
.plan-modal-box { background:var(--white); border-radius:16px; width:580px; max-width:95%; max-height:92vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:modalIn .25s cubic-bezier(.4,0,.2,1) both; }
@keyframes modalIn { from{transform:translateY(-16px);opacity:0} to{transform:none;opacity:1} }
.plan-modal-hd { padding:1.25rem 1.5rem; border-bottom:1px solid var(--brd); display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; background:var(--white); z-index:1; }
.plan-modal-hd h3 { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; }
.plan-modal-body { padding:1.5rem; }
.plan-modal-ft { padding:1rem 1.5rem; border-top:1px solid var(--brd); display:flex; gap:.75rem; justify-content:flex-end; position:sticky; bottom:0; background:var(--white); }

/* ── Statut séance ── */
.st-planifiee  { background:#f0f9ff; color:#0369a1; }
.st-realisee   { background:var(--ok-l); color:#065f46; }
.st-annulee    { background:var(--err-l); color:#7f1d1d; }
.st-reportee   { background:var(--warn-l); color:#92400e; }

/* ══════════════════════════════════════════
   FACTURE IMPRESSION
══════════════════════════════════════════ */
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
.invoice-school-name { font-size: 20px; font-weight: 800; color: #111827; margin: 0 0 4px; letter-spacing: -.5px; }
.invoice-school-sub  { font-size: 11px; color: #6b7280; margin: 0; line-height: 1.6; }
.invoice-meta { text-align: right; }
.invoice-title { font-size: 28px; font-weight: 900; color: #111827; letter-spacing: -1px; margin: 0 0 6px; }
.invoice-num  { font-family: monospace; font-size: 13px; color: #6b7280; background: #f3f4f6; padding: 3px 10px; border-radius: 6px; display: inline-block; margin-bottom: 4px; }
.invoice-date { font-size: 11px; color: #9ca3af; display: block; margin-top: 4px; }

.invoice-parties { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px; }
.invoice-party-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; }
.invoice-party-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin-bottom: 8px; }
.invoice-party-name  { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 3px; }
.invoice-party-detail{ font-size: 12px; color: #6b7280; line-height: 1.6; }

.invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
.invoice-table thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #9ca3af; border-bottom: 2px solid #e5e7eb; background: #f9fafb; }
.invoice-table thead th:last-child { text-align: right; }
.invoice-table tbody td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f3f4f6; color: #374151; }
.invoice-table tbody td:last-child { text-align: right; font-weight: 600; }
.invoice-table tbody tr:last-child td { border-bottom: none; }

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
.invoice-signature-line  { width: 160px; border-top: 1.5px solid #d1d5db; margin-bottom: 6px; }
.invoice-signature-label { font-size: 10px; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
.invoice-signature-name  { font-size: 12px; font-weight: 700; color: #374151; margin-top: 2px; }
.invoice-watermark-paid  { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-30deg); font-size: 80px; font-weight: 900; color: rgba(22,163,74,.08); letter-spacing: 8px; pointer-events: none; white-space: nowrap; }

@media(max-width:768px){
    .fg2,.fg3{grid-template-columns:1fr;}
    .creneau-card{grid-template-columns:auto 1fr;}
}
</style>
@endpush

@section('content')

{{-- ══ STATS ══ --}}
<div class="stat-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-val">{{ $stats['total_creneaux'] }}</div>
        <div class="stat-lbl">Créneaux EDT actifs</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['cours_semaine'] }}</div>
        <div class="stat-lbl">Cours cette semaine</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--ok)">{{ $stats['realisees'] }}</div>
        <div class="stat-lbl">Séances réalisées</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--warn)">{{ $stats['echeances_proch'] }}</div>
        <div class="stat-lbl">Échéances (<30j)</div>
    </div>
</div>

{{-- ══ FLASH ══ --}}
@if(session('success'))
<div style="display:flex;align-items:center;gap:.75rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:12px;padding:.875rem 1.25rem;margin-bottom:1rem;font-size:.83rem;" id="successAlert">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span style="flex:1">{{ session('success') }}</span>
    @if(session('last_prog_data'))
    <button onclick="printLastReceipt()" style="display:inline-flex;align-items:center;gap:.4rem;padding:.4rem .9rem;background:#fff;border:1px solid #bbf7d0;border-radius:.5rem;font-size:.75rem;font-weight:600;color:#15803d;cursor:pointer;">
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Imprimer le reçu
    </button>
    @endif
</div>
@endif

{{-- ══ FILTRES ══ --}}
<div style="background:var(--white);border:1px solid var(--brd);border-radius:14px;padding:.875rem 1.375rem;margin-bottom:1.25rem">
    <form method="GET" style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:flex-end">
        <div class="fg-group" style="flex:1;min-width:140px">
            <label class="lbl">Classe</label>
            <select class="inp" name="classe_id">
                <option value="">Toutes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" @selected($classeId==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="fg-group" style="flex:1;min-width:140px">
            <label class="lbl">Enseignant</label>
            <select class="inp" name="teacher_id">
                <option value="">Tous</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" @selected($teacherId==$t->id)>{{ $t->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="fg-group" style="flex:1;min-width:140px">
            <label class="lbl">Jour</label>
            <select class="inp" name="jour">
                <option value="">Tous</option>
                @foreach($jourLabels as $k => $v)
                    <option value="{{ $k }}" @selected($jour==$k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-dk" type="submit">Filtrer</button>
    </form>
</div>

{{-- ══ TABS ══ --}}
<div class="plan-tabs">
    <button class="plan-tab on" onclick="showTab('edt',this)">📅 Emploi du temps</button>
    <button class="plan-tab" onclick="showTab('seances',this)">📝 Séances (semaine)</button>
    <button class="plan-tab" onclick="showTab('paiements',this)">💳 Programmes de paiement</button>
</div>

{{-- ══ TAB: EMPLOI DU TEMPS ══ --}}
<div class="plan-section on" id="tab-edt">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
        <h3 style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700">Emploi du temps — {{ $annee }}</h3>
        <button class="btn btn-gold btn-sm" onclick="openEdtModal()">+ Ajouter créneau</button>
    </div>

    @foreach($grille as $jourKey => $items)
    @if(!$classeId || $items->count() > 0)
    <div class="jour-block">
        <div class="jour-hd">
            <div class="jour-label">{{ $jourLabels[$jourKey] ?? $jourKey }}</div>
            <div class="jour-count">{{ $items->count() }} créneau(x)</div>
            <div class="jour-line"></div>
        </div>
        @forelse($items as $e)
        <div class="creneau-card c-{{ $e->type }}">
            <div class="creneau-time">
                {{ $e->heure_debut ? \Carbon\Carbon::parse($e->heure_debut)->format('H:i') : '--:--' }}
                <span>{{ $e->heure_fin ? \Carbon\Carbon::parse($e->heure_fin)->format('H:i') : '--:--' }}</span>
            </div>
            <div>
                <div style="font-weight:600;font-size:.83rem">{{ $e->subject?->name ?? '—' }}</div>
                <div style="font-size:.72rem;color:var(--mist)">
                    {{ $e->classe?->name }} · {{ $e->teacher?->nom }}
                    @if($e->salle) · Salle {{ $e->salle }}@endif
                </div>
                <span class="bdg bdg-b" style="font-size:.62rem;margin-top:.2rem">{{ $typeLabels[$e->type] ?? $e->type }}</span>
            </div>
            <div style="display:flex;gap:.35rem;flex-shrink:0">
                <button class="btn btn-ot btn-sm" onclick="openEdtEdit({{ json_encode($e) }})">Modifier</button>
                <form method="POST" action="{{ route('staff.edt.destroy',$e) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-err btn-sm" onclick="return confirm('Supprimer ce créneau ?')">✕</button>
                </form>
            </div>
        </div>
        @empty
        <div style="padding:.75rem 1rem;color:var(--mist);font-size:.78rem;background:var(--bg);border-radius:9px;border:1px dashed var(--brd)">
            Aucun créneau ce jour
        </div>
        @endforelse
    </div>
    @endif
    @endforeach
</div>

{{-- ══ TAB: SÉANCES ══ --}}
<div class="plan-section" id="tab-seances">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
        <h3 style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700">Séances — semaine en cours</h3>
        <button class="btn btn-gold btn-sm" onclick="openSeanceModal()">+ Ajouter séance</button>
    </div>

    @forelse($seancesSemaine as $s)
    @php $date = \Carbon\Carbon::parse($s->date_seance); @endphp
    <div class="seance-card">
        <div class="seance-date">
            <div class="seance-date-day">{{ $date->format('d') }}</div>
            <div class="seance-date-mois">{{ $date->locale('fr')->isoFormat('MMM') }}</div>
        </div>
        <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:.83rem">{{ $s->subject?->name ?? '—' }}</div>
            <div style="font-size:.72rem;color:var(--mist)">
                {{ $s->classe?->name }} · {{ $s->teacher?->nom }}
                ·{{ $s->heure_debut ? \Carbon\Carbon::parse($s->heure_debut)->format('H:i') : '--' }}–{{ $s->heure_fin ? \Carbon\Carbon::parse($s->heure_fin)->format('H:i') : '--' }}
                @if($s->salle) · Salle {{ $s->salle }}@endif
            </div>
            @if($s->titre)<div style="font-size:.72rem;color:#374151;margin-top:.2rem">{{ $s->titre }}</div>@endif
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0">
            <span class="bdg st-{{ $s->statut }}">{{ ucfirst(str_replace('_',' ',$s->statut)) }}</span>
            <button class="btn btn-ot btn-sm"
                onclick="printSeanceReceipt(
                    '{{ addslashes($s->subject?->name ?? '') }}',
                    '{{ addslashes($s->classe?->name ?? '') }}',
                    '{{ addslashes($s->teacher?->nom ?? '') }}',
                    '{{ $date->format('d/m/Y') }}',
                    '{{ \Carbon\Carbon::parse($s->heure_debut)->format('H:i') }}–{{ \Carbon\Carbon::parse($s->heure_fin)->format('H:i') }}',
                    '{{ $s->statut }}',
                    '{{ addslashes($s->titre ?? '') }}'
                )"
                title="Imprimer le reçu">🖨️</button>
            <form method="POST" action="{{ route('staff.seance.destroy',$s) }}">
                @csrf @method('DELETE')
                <button class="btn btn-err btn-sm" onclick="return confirm('Supprimer ?')">✕</button>
            </form>
        </div>
    </div>
    @empty
    <div class="s-empty">
        <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
        <h4>Aucune séance cette semaine</h4>
        <p>Ajoutez des séances depuis votre emploi du temps.</p>
    </div>
    @endforelse
</div>

{{-- ══ TAB: PROGRAMMES PAIEMENT ══ --}}
<div class="plan-section" id="tab-paiements">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
        <h3 style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700">Programmes de paiement — {{ $annee }}</h3>
        <button class="btn btn-gold btn-sm" onclick="openProgModal()">+ Nouvelle échéance</button>
    </div>

    @forelse($programmes as $prog)
    @php $isLate = \Carbon\Carbon::parse($prog->date_echeance)->isPast(); @endphp
    <div class="prog-card">
        <div style="width:36px;height:36px;border-radius:9px;background:{{ $isLate?'var(--err-l)':'var(--ok-l)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg fill="none" stroke="{{ $isLate?'var(--err)':'var(--ok)' }}" viewBox="0 0 24 24" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:.83rem">{{ $prog->libelle }}</div>
            <div style="font-size:.72rem;color:var(--mist)">
                {{ $typeFraisLabels[$prog->type_frais] ?? $prog->type_frais }}
                · {{ $periodeLabels[$prog->periode] ?? $prog->periode }}
                @if($prog->classe) · {{ $prog->classe->name }}@elseif($prog->niveau) · {{ $prog->niveau->name }}@endif
            </div>
            <div class="prog-echeance {{ $isLate?'':'ok' }}">
                Échéance : {{ \Carbon\Carbon::parse($prog->date_echeance)->format('d/m/Y') }}
                {{ $isLate ? '⚠ En retard' : '' }}
            </div>
        </div>
        <div class="prog-amount">
            {{ number_format($prog->montant,0,' ',' ') }}<br>
            <span style="font-size:.62rem;font-weight:400;color:var(--mist)">{{ $prog->devise }}</span>
        </div>
        <div style="display:flex;gap:.35rem;flex-shrink:0">
            {{-- Bouton imprimer reçu de l'échéance --}}
            <button class="btn btn-ot btn-sm"
                onclick="printProgReceipt(
                    '{{ addslashes($prog->libelle) }}',
                    {{ $prog->montant }},
                    '{{ \Carbon\Carbon::parse($prog->date_echeance)->format('d/m/Y') }}',
                    '{{ $typeFraisLabels[$prog->type_frais] ?? $prog->type_frais }}',
                    '{{ $periodeLabels[$prog->periode] ?? $prog->periode }}',
                    '{{ $prog->classe?->name ?? ($prog->niveau?->name ?? 'Tous') }}',
                    '{{ $prog->devise }}',
                    {{ $isLate ? 'true' : 'false' }}
                )"
                title="Imprimer le reçu">🖨️</button>
            <form method="POST" action="{{ route('staff.paiement.destroy',$prog) }}">
                @csrf @method('DELETE')
                <button class="btn btn-err btn-sm" onclick="return confirm('Supprimer cette échéance ?')">✕</button>
            </form>
        </div>
    </div>
    @empty
    <div class="s-empty">
        <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <h4>Aucun programme de paiement</h4>
        <p>Créez vos échéances de paiement pour l'année.</p>
    </div>
    @endforelse
</div>


{{-- ══ MODAL EDT ══ --}}
<div class="plan-modal" id="modal-edt">
    <div class="plan-modal-box">
        <div class="plan-modal-hd">
            <h3>Ajouter un créneau</h3>
            <button class="btn btn-ot btn-sm" onclick="closeEdtModal()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.edt.store') }}">
            @csrf
            <div class="plan-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Classe *</label>
                        <select class="inp" name="classe_id" required>
                            <option value="">—</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Matière</label>
                        <select class="inp" name="subject_id">
                            <option value="">—</option>
                            @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Enseignant</label>
                        <select class="inp" name="teacher_id">
                            <option value="">—</option>
                            @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->nom }}</option>@endforeach
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Jour *</label>
                        <select class="inp" name="jour" required>
                            @foreach($jourLabels as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="fg3" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Heure début *</label><input class="inp" type="time" name="heure_debut" required></div>
                    <div class="fg-group"><label class="lbl">Heure fin *</label><input class="inp" type="time" name="heure_fin" required></div>
                    <div class="fg-group"><label class="lbl">Salle</label><input class="inp" name="salle" placeholder="A101…"></div>
                </div>
                <div class="fg2">
                    <div class="fg-group"><label class="lbl">Type *</label>
                        <select class="inp" name="type" required>
                            @foreach($typeLabels as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Période</label>
                        <select class="inp" name="periode">
                            <option value="">—</option>
                            <option value="trimestre1">Trimestre 1</option>
                            <option value="trimestre2">Trimestre 2</option>
                            <option value="trimestre3">Trimestre 3</option>
                            <option value="semestre1">Semestre 1</option>
                            <option value="semestre2">Semestre 2</option>
                            <option value="annee">Année entière</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="plan-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeEdtModal()">Annuler</button>
                <button type="submit" class="btn btn-gold">Ajouter</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL SÉANCE ══ --}}
<div class="plan-modal" id="modal-seance">
    <div class="plan-modal-box">
        <div class="plan-modal-hd">
            <h3>Enregistrer une séance</h3>
            <button class="btn btn-ot btn-sm" onclick="closeSeanceModal()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.seance.store') }}" onsubmit="storeSeanceForPrint(this)">
            @csrf
            <div class="plan-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Classe *</label>
                        <select class="inp" name="classe_id" id="sc-classe" required>
                            <option value="">—</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Date *</label><input class="inp" type="date" name="date_seance" id="sc-date" value="{{ date('Y-m-d') }}" required></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Matière</label>
                        <select class="inp" name="subject_id" id="sc-subject">
                            <option value="">—</option>
                            @foreach($subjects as $s)<option value="{{ $s->id }}" data-name="{{ $s->name }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Enseignant</label>
                        <select class="inp" name="teacher_id" id="sc-teacher">
                            <option value="">—</option>
                            @foreach($teachers as $t)<option value="{{ $t->id }}" data-name="{{ $t->nom }}">{{ $t->nom }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="fg3" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Heure début</label><input class="inp" type="time" name="heure_debut" id="sc-hdebut"></div>
                    <div class="fg-group"><label class="lbl">Heure fin</label><input class="inp" type="time" name="heure_fin" id="sc-hfin"></div>
                    <div class="fg-group"><label class="lbl">Salle</label><input class="inp" name="salle" id="sc-salle"></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Type *</label>
                        <select class="inp" name="type" id="sc-type" required>
                            <option value="cours">Cours</option>
                            <option value="evaluation">Évaluation</option>
                            <option value="examen">Examen</option>
                            <option value="rattrapage">Rattrapage</option>
                            <option value="activite">Activité</option>
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Statut *</label>
                        <select class="inp" name="statut" id="sc-statut" required>
                            <option value="planifiee">Planifiée</option>
                            <option value="realisee">Réalisée</option>
                            <option value="annulee">Annulée</option>
                            <option value="reportee">Reportée</option>
                        </select>
                    </div>
                </div>
                <div class="fg-group">
                    <label class="lbl">Titre / Description</label>
                    <input class="inp" name="titre" id="sc-titre" placeholder="Optionnel…">
                </div>
            </div>
            <div class="plan-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeSeanceModal()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL PROGRAMME PAIEMENT ══ --}}
<div class="plan-modal" id="modal-prog">
    <div class="plan-modal-box">
        <div class="plan-modal-hd">
            <h3>Nouvelle échéance de paiement</h3>
            <button class="btn btn-ot btn-sm" onclick="closeProgModal()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.paiement.store') }}" onsubmit="storeProgForPrint(this)">
            @csrf
            <div class="plan-modal-body">
                <div class="fg-group" style="margin-bottom:.875rem">
                    <label class="lbl">Libellé *</label>
                    <input class="inp" name="libelle" id="pg-libelle" placeholder="Ex: Frais de scolarité — Octobre" required>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Montant *</label><input class="inp" type="number" name="montant" id="pg-montant" min="0" required></div>
                    <div class="fg-group"><label class="lbl">Date d'échéance *</label><input class="inp" type="date" name="date_echeance" id="pg-date" required></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Type de frais *</label>
                        <select class="inp" name="type_frais" id="pg-type" required>
                            @foreach($typeFraisLabels as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Période *</label>
                        <select class="inp" name="periode" id="pg-periode" required>
                            @foreach($periodeLabels as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Classe (optionnel)</label>
                        <select class="inp" name="classe_id" id="pg-classe">
                            <option value="">— Toutes —</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}" data-name="{{ $c->name }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Jours de grâce</label><input class="inp" type="number" name="jours_grace" value="0" min="0"></div>
                </div>
                <div class="fg-group" style="flex-direction:row;align-items:center;gap:.5rem">
                    <input type="checkbox" name="obligatoire" value="1" id="oblig" checked>
                    <label for="oblig" style="font-size:.82rem;cursor:pointer">Paiement obligatoire</label>
                </div>
            </div>
            <div class="plan-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeProgModal()">Annuler</button>
                <button type="submit" class="btn btn-gold">Créer l'échéance</button>
            </div>
        </form>
    </div>
</div>


{{-- ══ ZONE FACTURE IMPRESSION ══ --}}
<div id="invoice-print-area">
    <div class="invoice-wrap" style="position:relative;">
        <div id="inv-watermark" class="invoice-watermark-paid"></div>

        <div class="invoice-header">
            <div>
                <h1 class="invoice-school-name" id="inv-school-name">{{ $institution->name ?? 'Établissement' }}</h1>
                <p class="invoice-school-sub">
                    {{ $institution->adresse ?? '' }}<br>
                    {{ $institution->telephone ?? '' }}
                    @if(!empty($institution->email)) · {{ $institution->email }} @endif
                </p>
            </div>
            <div class="invoice-meta">
                <div class="invoice-title" id="inv-doc-type">REÇU</div>
                <div class="invoice-num" id="inv-num">—</div>
                <div class="invoice-date" id="inv-date">—</div>
            </div>
        </div>

        <div class="invoice-parties">
            <div class="invoice-party-box">
                <div class="invoice-party-label">Établissement émetteur</div>
                <div class="invoice-party-name">{{ $institution->name ?? '—' }}</div>
                <div class="invoice-party-detail">
                    Année académique : <strong>{{ $annee }}</strong><br>
                    Émis par : <strong>{{ Auth::user()->name }}</strong>
                </div>
            </div>
            <div class="invoice-party-box">
                <div class="invoice-party-label" id="inv-party2-label">Destinataire</div>
                <div class="invoice-party-name" id="inv-party2-name">—</div>
                <div class="invoice-party-detail" id="inv-party2-detail">—</div>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Période / Date</th>
                    <th>Détails</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="inv-desc">—</td>
                    <td id="inv-periode">—</td>
                    <td id="inv-details">—</td>
                    <td id="inv-montant">—</td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-totals">
            <div class="invoice-total-row">
                <span id="inv-total-label">Montant total</span>
                <span class="val" id="inv-total-val">—</span>
            </div>
            <div class="invoice-total-row" id="inv-statut-row">
                <span>Statut</span>
                <span class="val" id="inv-statut-val">—</span>
            </div>
        </div>

        <div class="invoice-status-banner" id="inv-banner">—</div>

        <div class="invoice-footer">
            <div class="invoice-footer-note">
                Ce reçu est émis par <strong>{{ $institution->name ?? 'l\'établissement' }}</strong>.<br>
                Conservez ce document comme preuve officielle.<br>
                Pour toute réclamation, contactez l'administration.
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
// ── Tabs ──
function showTab(id, btn) {
    document.querySelectorAll('.plan-section').forEach(s => s.classList.remove('on'));
    document.querySelectorAll('.plan-tab').forEach(b => b.classList.remove('on'));
    document.getElementById('tab-'+id).classList.add('on');
    btn.classList.add('on');
}
if(location.hash) {
    const map = {'#edt':'edt','#seances':'seances','#paiements':'paiements'};
    const tabId = map[location.hash];
    if(tabId) {
        const btn = document.querySelector(`.plan-tab[onclick*="${tabId}"]`);
        if(btn) showTab(tabId, btn);
    }
}

// ── EDT modal ──
function openEdtModal(){ document.getElementById('modal-edt').classList.add('open'); document.body.style.overflow='hidden'; }
function closeEdtModal(){ document.getElementById('modal-edt').classList.remove('open'); document.body.style.overflow=''; }
function openEdtEdit(e) { openEdtModal(); }

// ── Séance modal ──
function openSeanceModal(){ document.getElementById('modal-seance').classList.add('open'); document.body.style.overflow='hidden'; }
function closeSeanceModal(){ document.getElementById('modal-seance').classList.remove('open'); document.body.style.overflow=''; }

// ── Programme modal ──
function openProgModal(){ document.getElementById('modal-prog').classList.add('open'); document.body.style.overflow='hidden'; }
function closeProgModal(){ document.getElementById('modal-prog').classList.remove('open'); document.body.style.overflow=''; }

// Backdrop
['modal-edt','modal-seance','modal-prog'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e){ if(e.target===this){this.classList.remove('open');document.body.style.overflow='';} });
});

// ────────────────────────────────────────────
// ── IMPRESSION REÇU — SÉANCE ──
// ────────────────────────────────────────────
function printSeanceReceipt(subject, classe, teacher, date, horaire, statut, titre) {
    const now = new Date();
    const ref = 'SNC-' + now.getFullYear().toString().slice(2)
              + String(now.getMonth()+1).padStart(2,'0')
              + String(now.getDate()).padStart(2,'0')
              + '-' + String(Math.floor(Math.random()*9000)+1000);

    const statutLabels = { planifiee:'Planifiée', realisee:'Réalisée', annulee:'Annulée', reportee:'Reportée' };
    const statutClass  = statut === 'realisee' ? 'paid' : statut === 'annulee' ? 'unpaid' : 'partial';
    const statutMsg    = statut === 'realisee'  ? '✓ SÉANCE RÉALISÉE'
                       : statut === 'planifiee' ? '📅 SÉANCE PLANIFIÉE'
                       : statut === 'annulee'   ? '✕ SÉANCE ANNULÉE'
                       : '↩ SÉANCE REPORTÉE';

    document.getElementById('inv-doc-type').textContent     = 'REÇU DE SÉANCE';
    document.getElementById('inv-num').textContent          = ref;
    document.getElementById('inv-date').textContent         = now.toLocaleDateString('fr-FR', {day:'2-digit',month:'long',year:'numeric'});
    document.getElementById('inv-party2-label').textContent = 'Séance';
    document.getElementById('inv-party2-name').textContent  = subject || 'Sans matière';
    document.getElementById('inv-party2-detail').innerHTML  = `Classe : <strong>${classe || '—'}</strong><br>Enseignant : <strong>${teacher || '—'}</strong>`;
    document.getElementById('inv-desc').textContent         = titre || ('Séance — ' + (subject || '—'));
    document.getElementById('inv-periode').textContent      = date;
    document.getElementById('inv-details').textContent      = horaire;
    document.getElementById('inv-montant').textContent      = '—';
    document.getElementById('inv-total-label').textContent  = 'Statut';
    document.getElementById('inv-total-val').textContent    = statutLabels[statut] || statut;
    document.getElementById('inv-statut-row').style.display = 'none';
    document.getElementById('inv-banner').textContent       = statutMsg;
    document.getElementById('inv-banner').className         = `invoice-status-banner ${statutClass}`;
    document.getElementById('inv-watermark').textContent    = '';

    window.print();
}

// ────────────────────────────────────────────
// ── IMPRESSION REÇU — PROGRAMME PAIEMENT ──
// ────────────────────────────────────────────
function printProgReceipt(libelle, montant, dateEch, typeFrais, periode, cible, devise, isLate) {
    const now = new Date();
    const ref = 'ECH-' + now.getFullYear().toString().slice(2)
              + String(now.getMonth()+1).padStart(2,'0')
              + String(now.getDate()).padStart(2,'0')
              + '-' + String(Math.floor(Math.random()*9000)+1000);

    const fmt = n => (n||0).toLocaleString('fr-FR') + ' ' + (devise || 'FCFA');
    const statut     = isLate ? 'En retard ⚠' : 'Actif ✓';
    const statClass  = isLate ? 'unpaid' : 'paid';
    const statMsg    = isLate ? '⚠ ÉCHÉANCE EN RETARD' : '✓ ÉCHÉANCE ACTIVE';

    document.getElementById('inv-doc-type').textContent     = 'AVIS D\'ÉCHÉANCE';
    document.getElementById('inv-num').textContent          = ref;
    document.getElementById('inv-date').textContent         = now.toLocaleDateString('fr-FR', {day:'2-digit',month:'long',year:'numeric'});
    document.getElementById('inv-party2-label').textContent = 'Programme de paiement';
    document.getElementById('inv-party2-name').textContent  = libelle;
    document.getElementById('inv-party2-detail').innerHTML  = `Type : <strong>${typeFrais}</strong><br>Cible : <strong>${cible}</strong>`;
    document.getElementById('inv-desc').textContent         = libelle;
    document.getElementById('inv-periode').textContent      = periode;
    document.getElementById('inv-details').textContent      = 'Échéance : ' + dateEch;
    document.getElementById('inv-montant').textContent      = fmt(montant);
    document.getElementById('inv-total-label').textContent  = 'Montant total';
    document.getElementById('inv-total-val').textContent    = fmt(montant);
    document.getElementById('inv-statut-row').style.display = 'flex';
    document.getElementById('inv-statut-val').textContent   = statut;
    document.getElementById('inv-banner').textContent       = statMsg;
    document.getElementById('inv-banner').className         = `invoice-status-banner ${statClass}`;
    document.getElementById('inv-watermark').textContent    = isLate ? '' : '✓';
    document.getElementById('inv-watermark').style.color    = isLate ? 'transparent' : 'rgba(22,163,74,.08)';

    window.print();
}

// ────────────────────────────────────────────
// ── STOCKER DONNÉES AVANT SOUMISSION (séance) ──
// ────────────────────────────────────────────
function storeSeanceForPrint(form) {
    const subjectOpt = document.getElementById('sc-subject');
    const teacherOpt = document.getElementById('sc-teacher');
    const classeOpt  = document.getElementById('sc-classe');
    const subject = subjectOpt.options[subjectOpt.selectedIndex]?.dataset?.name || '';
    const teacher = teacherOpt.options[teacherOpt.selectedIndex]?.dataset?.name || '';
    const classe  = classeOpt.options[classeOpt.selectedIndex]?.text || '';
    const data = {
        type:    'seance',
        subject, teacher, classe,
        date:    document.getElementById('sc-date').value,
        hdebut:  document.getElementById('sc-hdebut').value,
        hfin:    document.getElementById('sc-hfin').value,
        statut:  document.getElementById('sc-statut').value,
        titre:   document.getElementById('sc-titre').value,
        printAfter: true,
    };
    try { localStorage.setItem('lastPlanningReceipt', JSON.stringify(data)); } catch(e){}
}

// ────────────────────────────────────────────
// ── STOCKER DONNÉES AVANT SOUMISSION (prog) ──
// ────────────────────────────────────────────
function storeProgForPrint(form) {
    const typeOpt    = document.getElementById('pg-type');
    const periodeOpt = document.getElementById('pg-periode');
    const classeOpt  = document.getElementById('pg-classe');
    const typeFrais  = typeOpt.options[typeOpt.selectedIndex]?.text || '';
    const periode    = periodeOpt.options[periodeOpt.selectedIndex]?.text || '';
    const classe     = classeOpt.options[classeOpt.selectedIndex]?.dataset?.name || 'Tous';
    const data = {
        type:     'prog',
        libelle:  document.getElementById('pg-libelle').value,
        montant:  parseFloat(document.getElementById('pg-montant').value) || 0,
        dateEch:  document.getElementById('pg-date').value,
        typeFrais, periode, classe,
        printAfter: true,
    };
    try { localStorage.setItem('lastPlanningReceipt', JSON.stringify(data)); } catch(e){}
}

// ────────────────────────────────────────────
// ── IMPRIMER DERNIER REÇU (depuis flash) ──
// ────────────────────────────────────────────
function printLastReceipt() {
    try {
        const d = JSON.parse(localStorage.getItem('lastPlanningReceipt') || '{}');
        if (!d || !d.type) { alert('Aucun reçu disponible.'); return; }
        if (d.type === 'seance') {
            printSeanceReceipt(d.subject, d.classe, d.teacher, d.date,
                d.hdebut + '–' + d.hfin, d.statut, d.titre);
        } else {
            const now = new Date();
            const dateFormatted = d.dateEch
                ? new Date(d.dateEch).toLocaleDateString('fr-FR')
                : now.toLocaleDateString('fr-FR');
            printProgReceipt(d.libelle, d.montant, dateFormatted,
                d.typeFrais, d.periode, d.classe, 'FCFA', false);
        }
    } catch(e) { alert('Erreur lors de la récupération du reçu.'); }
}

// ── Auto-print au rechargement ──
window.addEventListener('load', function () {
    try {
        const d = JSON.parse(localStorage.getItem('lastPlanningReceipt') || '{}');
        if (d && d.printAfter) {
            d.printAfter = false;
            localStorage.setItem('lastPlanningReceipt', JSON.stringify(d));
            setTimeout(() => {
                if (d.type === 'seance') {
                    printSeanceReceipt(d.subject, d.classe, d.teacher, d.date,
                        d.hdebut + '–' + d.hfin, d.statut, d.titre);
                } else {
                    const dateFormatted = d.dateEch
                        ? new Date(d.dateEch).toLocaleDateString('fr-FR')
                        : new Date().toLocaleDateString('fr-FR');
                    printProgReceipt(d.libelle, d.montant, dateFormatted,
                        d.typeFrais, d.periode, d.classe, 'FCFA', false);
                }
            }, 700);
        }
    } catch(e) {}
});
</script>
@endpush
