@extends('student.master')

@section('content')
<style>
:root {
    --red:#dc2626;--red-l:#fee2e2;
    --amb:#d97706;--amb-l:#fef3c7;
    --grn:#16a34a;--grn-l:#dcfce7;
    --vio:#7c3aed;--vio-l:#ede9fe;
    --slate:#1e293b;--muted:#64748b;
    --border:#e2e8f0;--bg:#f8fafc;
}
.appr-disc-wrap { max-width:860px; margin:0 auto; }

/* ── BANNER ── */
.appr-banner {
    background:linear-gradient(135deg,#1e293b 0%,#3b1c6b 100%);
    border-radius:14px;padding:1.5rem 2rem;margin-bottom:1.5rem;
    position:relative;overflow:hidden;
}
.appr-banner::before {
    content:'';position:absolute;inset:0;
    background:radial-gradient(circle at 80% 50%,rgba(124,58,237,.25) 0%,transparent 60%);
}
.appr-banner-inner { position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:1rem; }
.appr-avatar {
    width:54px;height:54px;border-radius:14px;
    background:rgba(255,255,255,.12);border:2px solid rgba(255,255,255,.2);
    display:flex;align-items:center;justify-content:center;
    font-size:1.2rem;font-weight:800;color:#fff;flex-shrink:0;
    letter-spacing:-.5px;
}
.appr-banner-info { flex:1; }
.appr-banner-info h1 { font-size:1.2rem;font-weight:800;color:#fff;margin:0 0 .2rem; }
.appr-banner-info p  { font-size:.8rem;color:rgba(255,255,255,.5);margin:0; }
.appr-banner-year    { display:flex;align-items:center;gap:.75rem;flex-shrink:0; }
.year-pill {
    background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);
    color:#fff;border-radius:8px;padding:.4rem .75rem;font-size:.8rem;cursor:pointer;outline:none;
}
.year-pill option { color:#000; }

/* ── KPI ── */
.kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:.875rem;margin-bottom:1.5rem; }
.kpi-tile {
    background:#fff;border:1px solid var(--border);border-radius:12px;
    padding:.9rem 1.1rem;box-shadow:0 1px 3px rgba(0,0,0,.05);text-align:center;
    position:relative;overflow:hidden;
}
.kpi-tile::after {
    content:'';position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 12px 12px;
}
.kpi-tile.t-vio::after { background:var(--vio); }
.kpi-tile.t-red::after { background:var(--red); }
.kpi-tile.t-amb::after { background:var(--amb); }
.kpi-tile.t-grn::after { background:var(--grn); }
.kpi-tile-val   { font-size:1.6rem;font-weight:800;color:var(--slate);line-height:1; }
.kpi-tile-label { font-size:.68rem;color:var(--muted);margin-top:.2rem; }

/* ── YEAR SELECTOR BAR ── */
.year-bar {
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:1rem;
}
.year-bar h2 { font-size:.9rem;font-weight:700;color:var(--slate);margin:0; }
.year-bar span { font-size:.78rem;color:var(--muted); }

/* ── INCIDENT CARD ── */
.inc-card {
    background:#fff;border:1px solid var(--border);border-radius:12px;
    margin-bottom:.875rem;overflow:hidden;
    box-shadow:0 1px 3px rgba(0,0,0,.04);
    transition:box-shadow .2s, transform .2s;
}
.inc-card:hover { box-shadow:0 4px 14px rgba(0,0,0,.09);transform:translateY(-1px); }

.inc-card-header {
    display:flex;align-items:center;gap:.875rem;
    padding:.9rem 1.25rem;border-bottom:1px solid var(--bg);
}
.inc-card-icon {
    width:40px;height:40px;border-radius:10px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:1.1rem;
}
.inc-card-meta { flex:1;min-width:0; }
.inc-card-meta .type-label { font-size:.87rem;font-weight:700;color:var(--slate);margin-bottom:.2rem; }
.inc-card-meta .date-lbl   { font-size:.72rem;color:var(--muted); }
.inc-card-badges           { display:flex;gap:.4rem;flex-wrap:wrap;justify-content:flex-end; }

.inc-card-body { padding:.875rem 1.25rem; }
.inc-desc {
    font-size:.85rem;color:var(--slate);line-height:1.65;
    background:var(--bg);border-radius:8px;padding:.75rem 1rem;
    border-left:3px solid var(--vio);
}
.inc-desc-empty { font-size:.82rem;color:var(--muted);font-style:italic; }
.inc-sanction {
    display:flex;align-items:center;gap:.5rem;
    margin-top:.75rem;padding:.625rem .875rem;
    background:#fafafa;border:1px solid var(--border);border-radius:8px;
    font-size:.82rem;color:var(--slate);
}
.inc-sanction svg { color:var(--vio);flex-shrink:0; }

.inc-card-footer {
    padding:.6rem 1.25rem;background:var(--bg);
    border-top:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
}
.inc-card-footer span { font-size:.72rem;color:var(--muted); }

/* ── BADGE ── */
.badge {
    display:inline-flex;align-items:center;padding:.18rem .6rem;
    border-radius:20px;font-size:.67rem;font-weight:700;white-space:nowrap;
}
.badge-red    { background:var(--red-l);color:var(--red); }
.badge-amber  { background:var(--amb-l);color:var(--amb); }
.badge-green  { background:var(--grn-l);color:var(--grn); }
.badge-violet { background:var(--vio-l);color:var(--vio); }
.badge-orange { background:#fff7ed;color:#ea580c; }
.badge-gray   { background:#f1f5f9;color:#64748b; }

/* ── NOTICE ── */
.notice {
    display:flex;gap:.75rem;align-items:flex-start;
    background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;
    padding:.875rem 1.1rem;margin-bottom:1.25rem;
    font-size:.82rem;color:#1d4ed8;line-height:1.55;
}
.notice svg { flex-shrink:0;margin-top:.1rem;color:#3b82f6; }

/* ── EMPTY ── */
.empty-block {
    text-align:center;padding:3.5rem 1rem;
    background:#fff;border:1px solid var(--border);border-radius:14px;
}
.empty-block .emoji { font-size:2.5rem;display:block;margin-bottom:.75rem; }
.empty-block h3 { font-size:1rem;font-weight:700;color:var(--slate);margin:0 0 .3rem; }
.empty-block p  { font-size:.82rem;color:var(--muted);margin:0; }

/* ── TIMELINE CONNECTOR ── */
.timeline-wrap { position:relative;padding-left:0; }

@media(max-width:640px) {
    .kpi-row { grid-template-columns:1fr 1fr; }
    .appr-banner-inner { flex-direction:column;align-items:flex-start; }
    .inc-card-badges { justify-content:flex-start;margin-top:.4rem; }
    .inc-card-header { flex-wrap:wrap; }
}
</style>

@php
$initiales = strtoupper(mb_substr($apprenant->prenom, 0, 1) . mb_substr($apprenant->nom, 0, 1));

// Icônes et couleurs par type
$typeIcone = [
    'absence'      => ['icon' => '🚫', 'bg' => '#fee2e2', 'border' => '#fca5a5'],
    'retard'       => ['icon' => '⏰', 'bg' => '#fef3c7', 'border' => '#fcd34d'],
    'insolence'    => ['icon' => '💬', 'bg' => '#ede9fe', 'border' => '#c4b5fd'],
    'violence'     => ['icon' => '⚡', 'bg' => '#fee2e2', 'border' => '#f87171'],
    'triche'       => ['icon' => '📋', 'bg' => '#fff7ed', 'border' => '#fdba74'],
    'perturbation' => ['icon' => '📢', 'bg' => '#fef9c3', 'border' => '#fde047'],
    'tenue'        => ['icon' => '👔', 'bg' => '#f0fdf4', 'border' => '#86efac'],
    'autre'        => ['icon' => '📌', 'bg' => '#f8fafc', 'border' => '#cbd5e1'],
];
@endphp

<div class="appr-disc-wrap">

    {{-- BANNER --}}
    <div class="appr-banner">
        <div class="appr-banner-inner">
            <div class="appr-avatar">{{ $initiales }}</div>
            <div class="appr-banner-info">
                <h1>Mon suivi disciplinaire</h1>
                <p>{{ $apprenant->prenom }} {{ $apprenant->nom }}
                   @if($apprenant->classe) · {{ $apprenant->classe->name }} @endif
                   @if($apprenant->matricule) · {{ $apprenant->matricule }} @endif
                </p>
            </div>
            <div class="appr-banner-year">
                <form method="GET" style="display:flex;align-items:center;gap:.5rem;">
                    <span style="font-size:.75rem;color:rgba(255,255,255,.5);">Année</span>
                    <select name="annee" class="year-pill" onchange="this.form.submit()">
                        @foreach($anneesDispos as $a)
                        <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- KPI --}}
    <div class="kpi-row">
        <div class="kpi-tile t-vio">
            <div class="kpi-tile-val">{{ $statsApprenant['total'] }}</div>
            <div class="kpi-tile-label">Incidents en {{ $annee }}</div>
        </div>
        <div class="kpi-tile t-red">
            <div class="kpi-tile-val">{{ $statsApprenant['graves'] }}</div>
            <div class="kpi-tile-label">Incidents graves</div>
        </div>
        <div class="kpi-tile {{ $statsApprenant['ouverts'] > 0 ? 't-amb' : 't-grn' }}">
            <div class="kpi-tile-val">{{ $statsApprenant['ouverts'] }}</div>
            <div class="kpi-tile-label">Dossiers ouverts</div>
        </div>
        <div class="kpi-tile t-grn">
            <div class="kpi-tile-val">
                {{ $incidents->where('statut', 'clos')->count() }}
            </div>
            <div class="kpi-tile-label">Dossiers clos</div>
        </div>
    </div>

    {{-- Notice --}}
    <div class="notice">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>
            Cette page affiche les incidents enregistrés par ton établissement.
            Si tu as des questions ou si tu penses qu'une information est inexacte,
            parles-en à un responsable ou à tes parents.
        </span>
    </div>

    {{-- Year bar --}}
    <div class="year-bar">
        <h2>Incidents de l'année {{ $annee }}</h2>
        <span>{{ $incidents->count() }} enregistrement(s)</span>
    </div>

    {{-- LISTE --}}
    @forelse($incidents->sortByDesc('date_incident') as $inc)
    @php
        $ti = $typeIcone[$inc->type] ?? $typeIcone['autre'];
    @endphp
    <div class="inc-card">

        {{-- Header --}}
        <div class="inc-card-header">
            <div class="inc-card-icon"
                style="background:{{ $ti['bg'] }};border:1.5px solid {{ $ti['border'] }};">
                {{ $ti['icon'] }}
            </div>
            <div class="inc-card-meta">
                <div class="type-label">{{ $typeLabels[$inc->type] ?? $inc->type }}</div>
                <div class="date-lbl">
                    Le {{ $inc->date_incident?->translatedFormat('l d F Y') ?? $inc->date_incident?->format('d/m/Y') }}
                </div>
            </div>
            <div class="inc-card-badges">
                {{-- Gravité --}}
                @if($inc->gravite == 1)
                    <span class="badge badge-amber">⚠️ Mineur</span>
                @elseif($inc->gravite == 2)
                    <span class="badge badge-orange">🔶 Modéré</span>
                @else
                    <span class="badge badge-red">🔴 Grave</span>
                @endif

                {{-- Statut --}}
                @if($inc->statut === 'ouvert')
                    <span class="badge badge-red">Ouvert</span>
                @elseif($inc->statut === 'en_suivi')
                    <span class="badge badge-amber">En suivi</span>
                @else
                    <span class="badge badge-green">✓ Clos</span>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="inc-card-body">

            {{-- Ce qui lui est reproché --}}
            @if($inc->description)
            <div class="inc-desc">
                <strong style="display:block;font-size:.72rem;color:var(--muted);
                               text-transform:uppercase;letter-spacing:.06em;margin-bottom:.35rem;">
                    Ce qui t'est reproché
                </strong>
                {{ $inc->description }}
            </div>
            @else
            <p class="inc-desc-empty">Aucune description détaillée fournie.</p>
            @endif

            {{-- Sanction --}}
            @if($inc->sanction && $inc->sanction !== 'aucune')
            <div class="inc-sanction">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <strong>Sanction :</strong>
                    {{ $sanctionLabels[$inc->sanction] ?? $inc->sanction }}
                    @if($inc->sanction_detail)
                    <span style="color:var(--muted);"> — {{ $inc->sanction_detail }}</span>
                    @endif
                    @if($inc->sanction_executee)
                    <span class="badge badge-green" style="margin-left:.4rem;vertical-align:middle;">Exécutée</span>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="inc-card-footer">
            <span>
                @if($inc->parents_notifies)
                    ✅ Tes parents ont été notifiés
                    @if($inc->date_notification)
                        le {{ $inc->date_notification->format('d/m/Y') }}
                    @endif
                @else
                    ℹ️ Tes parents n'ont pas encore été notifiés
                @endif
            </span>
            <span>
                @if($inc->recordedBy)
                    Enregistré par {{ $inc->recordedBy->name }}
                @endif
            </span>
        </div>
    </div>
    @empty
    <div class="empty-block">
        <span class="emoji">🎉</span>
        <h3>Aucun incident en {{ $annee }}</h3>
        <p>Tu n'as aucun incident disciplinaire enregistré cette année. Continue comme ça !</p>
    </div>
    @endforelse

</div>
@endsection