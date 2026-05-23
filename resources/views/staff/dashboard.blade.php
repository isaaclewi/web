@extends('staff.master')
@section('title', 'Tableau de bord')
@section('page-title', 'Bonjour, ' . $staff->prenom . ' !')
@section('page-sub', now()->locale('fr')->isoFormat('dddd D MMMM YYYY'))

@section('content')
<style>
@keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.fu { animation:fadeUp .4s cubic-bezier(.22,1,.36,1) both; }
.fu1{animation-delay:.04s}.fu2{animation-delay:.09s}.fu3{animation-delay:.14s}.fu4{animation-delay:.19s}

.hero {
    background:var(--ink);
    border-radius:14px;
    padding:1.75rem 2rem;
    margin-bottom:1.5rem;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1.5rem;
    flex-wrap:wrap;
    position:relative;
    overflow:hidden;
}
.hero::before {
    content:'';
    position:absolute;inset:0;
    background:radial-gradient(ellipse 50% 80% at 95% 50%, rgba(37,99,235,.35) 0%, transparent 65%);
    pointer-events:none;
}
.hero-name { font-size:1.5rem; font-weight:800; color:#fff; letter-spacing:-.03em; position:relative; }
.hero-meta { font-size:.8rem; color:rgba(255,255,255,.4); margin-top:.3rem; position:relative; }

.module-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1rem; margin-bottom:1.5rem; }
.module-card {
    background:#fff; border:1px solid var(--border); border-radius:12px;
    padding:1.25rem; display:flex; flex-direction:column; gap:.75rem;
    transition:box-shadow .2s, transform .2s; text-decoration:none; color:inherit;
    position:relative; overflow:hidden;
}
.module-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.08); transform:translateY(-2px); }
.module-icon {
    width:44px; height:44px; border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    background:var(--accent-l); flex-shrink:0;
}
.module-icon svg { width:22px; height:22px; color:var(--accent); }
.module-name  { font-size:.9rem; font-weight:700; color:var(--ink); }
.module-desc  { font-size:.78rem; color:var(--ink-40); line-height:1.5; flex:1; }
.module-arrow { font-size:.75rem; color:var(--accent); font-weight:600; display:flex; align-items:center; gap:.3rem; }

.kpi-strip { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:1rem; margin-bottom:1.5rem; }
.kpi-card { background:#fff; border:1px solid var(--border); border-radius:10px; padding:1rem; }
.kpi-val   { font-size:1.75rem; font-weight:800; color:var(--ink); font-family:'JetBrains Mono',monospace; line-height:1; }
.kpi-lbl   { font-size:.72rem; color:var(--ink-40); margin-top:.25rem; }

.aucun-module {
    background:#fff; border:2px dashed var(--border); border-radius:14px;
    padding:3rem 2rem; text-align:center;
}
</style>

{{-- HERO --}}
<div class="hero fu fu1">
    <div>
        <p style="font-size:.7rem;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.4rem">Espace staff</p>
        <p class="hero-name">Bonjour, {{ $staff->prenom }} !</p>
        <p class="hero-meta">
            {{ ucfirst($staff->poste ?? 'Personnel') }}
            @if($staff->administrativeUnit) · {{ $staff->administrativeUnit->name }} @endif
            · {{ $institution->name }}
        </p>
    </div>
    <div style="position:relative;text-align:right">
        <p style="font-size:.68rem;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.08em">Modules actifs</p>
        <p style="font-size:2.5rem;font-weight:900;color:#fff;font-family:'JetBrains Mono',monospace;line-height:1;margin-top:.15rem">
            {{ $modulesActifs->count() }}
        </p>
    </div>
</div>

{{-- KPI de base --}}
<div class="kpi-strip fu fu2">
    <div class="kpi-card">
        <p class="kpi-val">{{ $stats['apprenants'] }}</p>
        <p class="kpi-lbl">Apprenants inscrits</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-val">{{ $stats['actifs'] }}</p>
        <p class="kpi-lbl">Apprenants actifs</p>
    </div>
    <div class="kpi-card">
        <p class="kpi-val">{{ $stats['classes'] }}</p>
        <p class="kpi-lbl">Classes</p>
    </div>
    @if(isset($stats['impaye']))
    <div class="kpi-card" style="border-color:#fca5a5">
        <p class="kpi-val" style="color:#dc2626">{{ number_format($stats['impaye'], 0, ',', ' ') }}</p>
        <p class="kpi-lbl">FCFA impayés</p>
    </div>
    @endif
    @if(isset($stats['incidents_ouverts']))
    <div class="kpi-card" style="border-color:#fca5a5">
        <p class="kpi-val" style="color:#dc2626">{{ $stats['incidents_ouverts'] }}</p>
        <p class="kpi-lbl">Incidents ouverts</p>
    </div>
    @endif
</div>

{{-- MODULES --}}
@if($modulesActifs->isEmpty())

<div class="aucun-module fu fu3">
    <div style="width:56px;height:56px;background:#f1f5f9;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem">
        <svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
    <p style="font-size:1rem;font-weight:700;color:#475569;margin-bottom:.5rem">Aucun module assigné</p>
    <p style="font-size:.85rem;color:#94a3b8;max-width:340px;margin:0 auto;line-height:1.6">
        Votre directeur n'a pas encore activé de modules pour votre compte. Contactez l'administration.
    </p>
</div>

@else

<div class="fu fu3">
    <p style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ink-40);margin-bottom:.875rem">Mes modules actifs</p>
</div>

<div class="module-grid fu fu3">
@php
$moduleIcons = [
    'apprenants'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    'enseignants'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'paiements'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1"/>',
    'inscriptions' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
    'planning'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
    'disciplinaire'=> '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'rapports'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
    'bibliotheque' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
    'Notes et bulletins'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
    'parents'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'transferts'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18M13 7h6a2 2 0 012 2v6a2 2 0 01-2 2h-6a2 2 0 01-2-2V9a2 2 0 012-2z"/>',
    'academic'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zM12 14l6.16-3.422a12.083 12.083 0 01.665-.33m0 0L12 14m-6.16-3.422a12.083 12.083 0 00-.665-.33m0 0L12 14m0 7v-7m0 7a2.5 2.5 0 002.5-2.5h-5A2.5 2.5 0 0012 21z"/>',
];
$moduleRoutes = [
    'apprenants'   => 'staff.apprenants',
    'enseignants'  => 'staff.enseignants',
    'paiements'    => 'staff.finances',
    'disciplinaire'=> 'staff.disciplinaire',
    'planning'     => 'staff.planning',
    'bibliotheque' => 'staff.library',
    'inscriptions' => 'staff.inscriptions',
    'rapports'     => 'staff.rapports',
    'notes'    => 'staff.bulletins',
    'parents'    => 'staff.parents',
    'transferts'  => 'staff.transferts',
    'academic'    => 'staff.academic',
];
@endphp

@foreach($modulesActifs as $module)
@php
    $route     = $moduleRoutes[$module->key] ?? null;
    $iconPath  = $moduleIcons[$module->key] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>';
    $assignment = $staff->taskAssignments->firstWhere('module_id', $module->id);
@endphp
<a href="{{ $route ? route($route) : '#' }}" class="module-card">
    <div style="display:flex;align-items:center;gap:.875rem">
        <div class="module-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconPath !!}</svg>
        </div>
        <div>
            <p class="module-name">{{ $module->label }}</p>
        </div>
    </div>
    <p class="module-desc">{{ $module->description ?? '' }}</p>
    @if($assignment?->notes)
    <div style="background:#f8fafc;border-left:3px solid var(--accent);border-radius:0 6px 6px 0;padding:.5rem .75rem;font-size:.75rem;color:var(--ink-70);line-height:1.5">
        <span style="font-weight:600;color:var(--accent)">Instructions : </span>{{ $assignment->notes }}
    </div>
    @endif
    <div class="module-arrow">
        Accéder
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </div>
</a>
@endforeach
</div>

{{-- Activités récentes --}}
@if(!empty($activitesRecentes))
<div class="fu fu4">
<p style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ink-40);margin-bottom:.875rem">Activités récentes</p>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem">

@if(isset($activitesRecentes['apprenants']) && $activitesRecentes['apprenants']->isNotEmpty())
<div style="background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden">
    <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f3f4f6"><p style="font-size:.85rem;font-weight:700">Derniers apprenants inscrits</p></div>
    @foreach($activitesRecentes['apprenants'] as $a)
    <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem 1.25rem;border-bottom:1px solid #f8fafc">
        <div style="width:32px;height:32px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#1d4ed8;flex-shrink:0">
            {{ strtoupper(mb_substr($a->prenom,0,1).mb_substr($a->nom,0,1)) }}
        </div>
        <div>
            <p style="font-size:.82rem;font-weight:600;color:#111827">{{ $a->prenom }} {{ $a->nom }}</p>
            <p style="font-size:.7rem;color:#9ca3af">{{ $a->matricule }}</p>
        </div>
    </div>
    @endforeach
</div>
@endif

@if(isset($activitesRecentes['paiements']) && $activitesRecentes['paiements']->isNotEmpty())
<div style="background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden">
    <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f3f4f6"><p style="font-size:.85rem;font-weight:700">Derniers paiements</p></div>
    @foreach($activitesRecentes['paiements'] as $p)
    <div style="display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.75rem 1.25rem;border-bottom:1px solid #f8fafc">
        <div>
            <p style="font-size:.82rem;font-weight:600;color:#111827">{{ $p->apprenant?->prenom }} {{ $p->apprenant?->nom }}</p>
            <p style="font-size:.7rem;color:#9ca3af">{{ $p->mois_label }} · {{ $p->date_paiement?->format('d/m') }}</p>
        </div>
        <span style="font-size:.82rem;font-weight:700;color:#059669">{{ number_format($p->montant_paye, 0, ',', ' ') }} F</span>
    </div>
    @endforeach
</div>
@endif

@if(isset($activitesRecentes['incidents']) && $activitesRecentes['incidents']->isNotEmpty())
<div style="background:#fff;border:1px solid var(--border);border-radius:12px;overflow:hidden">
    <div style="padding:.875rem 1.25rem;border-bottom:1px solid #f3f4f6"><p style="font-size:.85rem;font-weight:700">Incidents récents</p></div>
    @foreach($activitesRecentes['incidents'] as $inc)
    <div style="display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.75rem 1.25rem;border-bottom:1px solid #f8fafc">
        <div>
            <p style="font-size:.82rem;font-weight:600;color:#111827">{{ $inc->apprenant?->prenom }} {{ $inc->apprenant?->nom }}</p>
            <p style="font-size:.7rem;color:#9ca3af">{{ $inc->date_incident?->format('d/m') }}</p>
        </div>
        <span style="font-size:.72rem;padding:.15rem .5rem;border-radius:4px;{{ $inc->statut==='ouvert' ? 'background:#fee2e2;color:#991b1b' : 'background:#d1fae5;color:#065f46' }}">
            {{ $inc->statut }}
        </span>
    </div>
    @endforeach
</div>
@endif

</div>
</div>
@endif

@endif
@endsection
