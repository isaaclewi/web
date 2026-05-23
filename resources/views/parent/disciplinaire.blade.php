@extends('parent.master')

@section('content')
<style>
:root {
    --red:#dc2626;--red-l:#fee2e2;
    --amb:#d97706;--amb-l:#fef3c7;
    --grn:#16a34a;--grn-l:#dcfce7;
    --blu:#2563eb;--blu-l:#dbeafe;
    --vio:#7c3aed;--vio-l:#ede9fe;
    --slate:#1e293b;--muted:#64748b;
    --border:#e2e8f0;--bg:#f8fafc;
}
.parent-disc-wrap { max-width:900px; margin:0 auto; }
.disc-banner {
    background:linear-gradient(135deg,#1e293b,#4c1d95);
    border-radius:14px;padding:1.5rem 2rem;
    display:flex;align-items:center;justify-content:space-between;
    gap:1rem;margin-bottom:1.5rem;
}
.disc-banner h1 { font-size:1.25rem;font-weight:800;color:#fff;margin:0 0 .25rem; }
.disc-banner p  { font-size:.82rem;color:rgba(255,255,255,.55);margin:0; }
.disc-banner-right { text-align:right; }
.disc-kpi { display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem; }
.disc-kpi-card {
    background:#fff;border:1px solid var(--border);border-radius:12px;
    padding:1.1rem 1.25rem;box-shadow:0 1px 3px rgba(0,0,0,.06);
    border-top:3px solid var(--vio);
}
.disc-kpi-card.red { border-top-color:var(--red); }
.disc-kpi-card.amb { border-top-color:var(--amb); }
.disc-kpi-card.grn { border-top-color:var(--grn); }
.disc-kpi-val   { font-size:1.75rem;font-weight:800;color:var(--slate); }
.disc-kpi-label { font-size:.72rem;color:var(--muted); }

.year-select {
    background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);
    color:#fff;border-radius:8px;padding:.4rem .75rem;font-size:.8rem;cursor:pointer;
}
.year-select option { color:#000; }

.incident-card {
    background:#fff;border:1px solid var(--border);border-radius:12px;
    padding:1.25rem 1.5rem;margin-bottom:1rem;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
    transition:box-shadow .2s;
}
.incident-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.08); }
.incident-header { display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:.75rem; }
.incident-meta { display:flex;gap:.5rem;flex-wrap:wrap;align-items:center; }
.badge {
    display:inline-flex;align-items:center;padding:.2rem .6rem;
    border-radius:20px;font-size:.68rem;font-weight:700;
}
.badge-red    { background:var(--red-l);color:var(--red); }
.badge-amber  { background:var(--amb-l);color:var(--amb); }
.badge-green  { background:var(--grn-l);color:var(--grn); }
.badge-violet { background:var(--vio-l);color:var(--vio); }
.badge-gray   { background:#f1f5f9;color:#64748b; }
.badge-orange { background:#fff7ed;color:#ea580c; }
.incident-date { font-size:.72rem;color:var(--muted); }
.incident-desc { font-size:.85rem;color:var(--slate);margin:.625rem 0 0; line-height:1.6; }
.incident-sanction {
    margin-top:.75rem;padding:.625rem .875rem;
    background:var(--bg);border-radius:8px;border-left:3px solid var(--vio);
    font-size:.82rem;color:var(--muted);
}
.incident-sanction strong { color:var(--slate); }
.info-note {
    background:var(--blu-l);border:1px solid #bfdbfe;border-radius:10px;
    padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:.83rem;color:#1d4ed8;
    display:flex;gap:.75rem;align-items:flex-start;
}
.empty-state { text-align:center;padding:3rem;color:var(--muted); }
.empty-state svg { width:48px;height:48px;opacity:.2;display:block;margin:0 auto .875rem; }

@media(max-width:640px) {
    .disc-kpi { grid-template-columns:1fr 1fr; }
    .disc-banner { flex-direction:column; }
}
</style>

<div class="parent-disc-wrap">

    {{-- Banner --}}
    <div class="disc-banner">
        <div>
            <h1>📋 Suivi disciplinaire</h1>
            <p>{{ $apprenant->prenom }} {{ $apprenant->nom }}
               @if($apprenant->classe) — {{ $apprenant->classe->name }} @endif
            </p>
        </div>
        <div class="disc-banner-right">
            <form method="GET" style="display:inline;">
                <select name="annee" class="year-select" onchange="this.form.submit()">
                    @foreach($anneesDispos as $a)
                    <option value="{{ $a }}" {{ $a == $annee ? 'selected':'' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Note info --}}
    <div class="info-note">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:.1rem;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Ce suivi est <strong>en lecture seule</strong>. Les incidents sont enregistrés
        par l'administration ou les enseignants de l'établissement.
        Pour toute question, contactez directement l'établissement.</span>
    </div>

    {{-- KPI --}}
    <div class="disc-kpi">
        <div class="disc-kpi-card vio">
            <div class="disc-kpi-val">{{ $statsApprenant['total'] }}</div>
            <div class="disc-kpi-label">Total incidents en {{ $annee }}</div>
        </div>
        <div class="disc-kpi-card red">
            <div class="disc-kpi-val">{{ $statsApprenant['graves'] }}</div>
            <div class="disc-kpi-label">Incidents graves</div>
        </div>
        <div class="disc-kpi-card {{ $statsApprenant['ouverts'] > 0 ? 'amb' : 'grn' }}">
            <div class="disc-kpi-val">{{ $statsApprenant['ouverts'] }}</div>
            <div class="disc-kpi-label">Dossiers encore ouverts</div>
        </div>
    </div>

    {{-- Liste des incidents --}}
    @forelse($incidents as $inc)
    <div class="incident-card">
        <div class="incident-header">
            <div class="incident-meta">
                <span class="badge badge-violet">{{ $typeLabels[$inc->type] ?? $inc->type }}</span>
                @if($inc->gravite == 1)
                    <span class="badge badge-amber">⚠️ Mineur</span>
                @elseif($inc->gravite == 2)
                    <span class="badge badge-orange">🔶 Modéré</span>
                @else
                    <span class="badge badge-red">🔴 Grave</span>
                @endif
                @if($inc->statut === 'ouvert')
                    <span class="badge badge-red">Ouvert</span>
                @elseif($inc->statut === 'en_suivi')
                    <span class="badge badge-amber">En suivi</span>
                @else
                    <span class="badge badge-green">Clos</span>
                @endif
                @if($inc->parents_notifies)
                    <span class="badge badge-green">✓ Vous avez été notifié(e)</span>
                @endif
            </div>
            <span class="incident-date">
                {{ $inc->date_incident?->format('d/m/Y') }}
            </span>
        </div>

        @if($inc->description)
        <div class="incident-desc">{{ $inc->description }}</div>
        @endif

        @if($inc->sanction && $inc->sanction !== 'aucune')
        <div class="incident-sanction">
            <strong>Sanction :</strong> {{ $sanctionLabels[$inc->sanction] ?? $inc->sanction }}
            @if($inc->sanction_detail) — {{ $inc->sanction_detail }} @endif
            @if($inc->sanction_executee)
                <span class="badge badge-green" style="margin-left:.5rem;">Exécutée</span>
            @endif
        </div>
        @endif

        @if($inc->observations)
        <div style="margin-top:.625rem;font-size:.78rem;color:var(--muted);font-style:italic;">
            Observations : {{ $inc->observations }}
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p style="font-weight:600;">Aucun incident pour {{ $annee }}</p>
        <p style="font-size:.8rem;margin-top:.25rem;">Votre enfant n'a aucun incident enregistré cette année.</p>
    </div>
    @endforelse

</div>
@endsection