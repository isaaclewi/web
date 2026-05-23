@extends('parent.master')
@section('title', 'Tableau de bord')
@section('page-title', 'Bonjour, '.(explode(' ', Auth::user()->name)[0] ?? 'Parent').' 👋')
@section('page-sub', 'Vue d\'ensemble de vos enfants — '.$institution?->name)

@section('content')
<style>
/* ── DASHBOARD ── */
.dash-grid { display:grid; gap:1.25rem; }

/* Hero banner */
.dash-hero {
    background: linear-gradient(135deg, var(--ink) 0%, #1a2744 60%, #0d3320 100%);
    border-radius: 16px; padding: 1.75rem 2rem;
    position: relative; overflow: hidden;
    display: flex; align-items: center; justify-content: space-between; gap: 1rem;
}
.dash-hero::before {
    content:''; position:absolute; inset:0;
    background: repeating-linear-gradient(45deg,
        rgba(255,255,255,.015) 0, rgba(255,255,255,.015) 1px,
        transparent 0, transparent 28px);
    background-size: 28px 28px;
}
.dash-hero-left { position:relative; z-index:1; }
.dash-hero-left h2 { font-size:1.5rem; font-weight:800; color:#fff; margin:0 0 .35rem; }
.dash-hero-left p  { font-size:.85rem; color:rgba(255,255,255,.5); margin:0; }
.dash-hero-badges  { display:flex; gap:.625rem; margin-top:1rem; flex-wrap:wrap; }
.dash-hero-badge {
    display:inline-flex; align-items:center; gap:.375rem;
    background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15);
    border-radius:20px; padding:.3rem .8rem; font-size:.75rem; font-weight:600;
    color:rgba(255,255,255,.85);
}
.dash-hero-badge .dot { width:6px; height:6px; border-radius:50%; }
.dash-hero-right { position:relative; z-index:1; }

/* Enfant cards */
.enfants-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(320px,1fr)); gap:1.25rem; }

.enfant-card {
    background:#fff; border:1px solid var(--border); border-radius:14px;
    overflow:hidden; transition:all .2s;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.enfant-card:hover { border-color:#c8cfe0; box-shadow:0 6px 20px rgba(0,0,0,.08); transform:translateY(-2px); }

.enfant-card-top {
    padding:1.25rem;
    display:flex; align-items:center; justify-content:space-between; gap:.875rem;
    border-bottom:1px solid var(--border);
}
.enfant-avatar {
    width:52px; height:52px; border-radius:13px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:1rem; font-weight:800; color:#fff;
}
.enfant-name  { font-size:.9375rem; font-weight:700; color:var(--ink); }
.enfant-class { font-size:.72rem; color:var(--ink-40); margin-top:.15rem; }
.enfant-lien  { font-size:.68rem; font-weight:600; margin-top:.3rem; }
.enfant-avg   {
    text-align:center;
    background:var(--bg); border:1px solid var(--border);
    border-radius:10px; padding:.5rem .875rem; flex-shrink:0;
}
.enfant-avg-val { font-size:1.4rem; font-weight:800; color:var(--ink); font-family:'JetBrains Mono',monospace; line-height:1; }
.enfant-avg-lbl { font-size:.62rem; color:var(--ink-40); margin-top:.2rem; }

.enfant-stats {
    display:grid; grid-template-columns:repeat(3,1fr);
    border-bottom:1px solid var(--border);
}
.enfant-stat {
    padding:.875rem; text-align:center;
    border-right:1px solid var(--border);
}
.enfant-stat:last-child { border-right:none; }
.enfant-stat-val { font-size:1.2rem; font-weight:800; color:var(--ink); font-family:'JetBrains Mono',monospace; }
.enfant-stat-lbl { font-size:.65rem; color:var(--ink-40); margin-top:.15rem; }

.enfant-actions {
    padding:.75rem 1rem; display:flex; gap:.5rem; flex-wrap:wrap;
}

/* Alertes */
.alertes-list { display:flex; flex-direction:column; gap:.625rem; }
.alerte-item {
    display:flex; align-items:center; gap:.75rem;
    padding:.75rem 1rem; border-radius:10px;
    font-size:.83rem; border:1px solid;
}
.alerte-item.amber { background:var(--gold-l); border-color:#f0d080; color:#7a5200; }
.alerte-item.red   { background:var(--red-l);  border-color:#f5b0b4; color:#7a1a1e; }
.alerte-icon { font-size:1.1rem; flex-shrink:0; }

/* Recent notes */
.note-row {
    display:flex; align-items:center; justify-content:space-between;
    padding:.625rem 0; border-bottom:1px solid var(--border);
    font-size:.83rem;
}
.note-row:last-child { border-bottom:none; }

/* Score chip */
.score-chip {
    width:38px; height:26px; border-radius:6px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.78rem; font-weight:800; font-family:'JetBrains Mono',monospace;
    flex-shrink:0;
}
.sc-A { background:#d0f4ec; color:#0d6b52; }
.sc-B { background:#dceafd; color:#1a4a9e; }
.sc-C { background:var(--gold-l); color:#7a5200; }
.sc-D { background:var(--red-l); color:#7a1a1e; }

/* Progress bar */
.p-bar { background:var(--ink-10); height:6px; border-radius:99px; overflow:hidden; margin-top:.375rem; }
.p-bar-fill { height:100%; border-radius:99px; }

@media(max-width:640px) {
    .enfants-grid { grid-template-columns:1fr; }
    .dash-hero { flex-direction:column; }
}
</style>

<div class="dash-grid">

    {{-- ── HERO ── --}}
    <div class="dash-hero">
        <div class="dash-hero-left">
            <h2>Espace Parent — {{ $institution?->name ?? 'Mon établissement' }}</h2>
            <p>Année académique {{ $institution?->academic_year ?? date('Y').'-'.(date('Y')+1) }}</p>
            <div class="dash-hero-badges">
                <span class="dash-hero-badge">
                    <span class="dot" style="background:#4ade80;"></span>
                    {{ $enfants->count() }} enfant(s) suivi(s)
                </span>
                @if(count($alertes) > 0)
                <span class="dash-hero-badge" style="border-color:rgba(212,160,23,.4);background:rgba(212,160,23,.15);">
                    <span class="dot" style="background:var(--gold);"></span>
                    {{ count($alertes) }} alerte(s)
                </span>
                @endif
                <span class="dash-hero-badge">
                    <span class="dot" style="background:#60a5fa;"></span>
                    Aujourd'hui {{ now()->locale('fr')->isoFormat('D MMMM YYYY') }}
                </span>
            </div>
        </div>
        <div class="dash-hero-right" style="text-align:right;">
            @php
                $moyGlobale = collect($enfantsData)->filter(fn($d)=>$d['moyenne']!==null)->avg('moyenne');
            @endphp
            @if($moyGlobale)
            <div style="font-size:2.5rem;font-weight:800;color:#fff;font-family:'JetBrains Mono',monospace;line-height:1;">
                {{ round($moyGlobale, 1) }}
            </div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.45);margin-top:.25rem;">Moyenne globale</div>
            @endif
        </div>
    </div>

    {{-- ── ALERTES ── --}}
    @if(count($alertes))
    <div class="p-card">
        <div class="p-card-header">
            <h3>🔔 Alertes récentes</h3>
            <span class="p-badge p-badge-red">{{ count($alertes) }}</span>
        </div>
        <div class="p-card-body">
            <div class="alertes-list">
                @foreach($alertes as $al)
                <div class="alerte-item {{ $al['color'] }}">
                    <span class="alerte-icon">{{ $al['icon'] }}</span>
                    <span>{{ $al['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── ENFANTS ── --}}
    <div>
        <div style="font-size:.8rem;font-weight:700;color:var(--ink-70);text-transform:uppercase;letter-spacing:.07em;margin-bottom:1rem;">
            Mes enfants
        </div>
        <div class="enfants-grid">
            @forelse($enfantsData as $d)
            @php
                $a = $d['apprenant'];
                $colors = ['#6366f1','#0891b2','#059669','#d97706','#dc2626','#7c3aed'];
                $color  = $colors[$a->id % count($colors)];
                $init   = strtoupper(mb_substr($a->prenom,0,1).mb_substr($a->nom,0,1));
                $moy    = $d['moyenne'];
                $grade  = $moy >= 14 ? 'A' : ($moy >= 10 ? 'B' : ($moy >= 8 ? 'C' : 'D'));
                $pct    = $d['totalDu'] > 0 ? round($d['totalPaye']/$d['totalDu']*100) : 100;
            @endphp
            <div class="enfant-card">
                {{-- Top --}}
                <div class="enfant-card-top">
                    <div style="display:flex;align-items:center;gap:.875rem;min-width:0;">
                        <div class="enfant-avatar" style="background:{{ $color }};">{{ $init }}</div>
                        <div style="min-width:0;">
                            <div class="enfant-name">{{ $a->prenom }} {{ $a->nom }}</div>
                            <div class="enfant-class">{{ $a->classe?->name ?? 'Sans classe' }}
                                @if($a->niveau) · {{ $a->niveau->name }} @endif
                            </div>
                            <div class="enfant-lien" style="color:{{ $color }};">
                                {{ ucfirst($schoolParent->apprenants->firstWhere('id',$a->id)?->pivot->lien ?? 'enfant') }}
                            </div>
                        </div>
                    </div>
                    <div class="enfant-avg">
                        @if($moy !== null)
                            <div class="enfant-avg-val">{{ $moy }}</div>
                        @else
                            <div class="enfant-avg-val" style="font-size:1rem;color:var(--ink-40);">—</div>
                        @endif
                        <div class="enfant-avg-lbl">Moyenne</div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="enfant-stats">
                    <div class="enfant-stat">
                        <div class="enfant-stat-val" style="{{ $d['totalReste']>0?'color:var(--red)':'color:var(--teal)' }}">
                            {{ $d['totalReste'] > 0 ? number_format($d['totalReste'],0,',',' ').' F' : '✓' }}
                        </div>
                        <div class="enfant-stat-lbl">Reste dû</div>
                    </div>
                    <div class="enfant-stat">
                        <div class="enfant-stat-val" style="{{ $d['incidentsOuverts']>0?'color:var(--red)':'color:var(--teal)' }}">
                            {{ $d['incidentsOuverts'] > 0 ? $d['incidentsOuverts'] : '✓' }}
                        </div>
                        <div class="enfant-stat-lbl">Incidents ouverts</div>
                    </div>
                    <div class="enfant-stat">
                        <div class="enfant-stat-val">{{ $d['dernieresNotes']->count() }}</div>
                        <div class="enfant-stat-lbl">Notes récentes</div>
                    </div>
                </div>

                {{-- Progress paiement --}}
                <div style="padding:.75rem 1rem;">
                    <div style="display:flex;justify-content:space-between;font-size:.72rem;color:var(--ink-40);margin-bottom:.25rem;">
                        <span>Paiements</span>
                        <span class="mono">{{ $pct }}%</span>
                    </div>
                    <div class="p-bar">
                        <div class="p-bar-fill" style="width:{{ $pct }}%;background:{{ $pct>=100?'var(--teal)':($pct>=50?'var(--gold)':'var(--red)') }};"></div>
                    </div>
                </div>

                {{-- Dernières notes --}}
                @if($d['dernieresNotes']->count())
                <div style="padding:0 1rem .625rem;">
                    <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--ink-40);margin-bottom:.5rem;">
                        Dernières notes
                    </div>
                    @foreach($d['dernieresNotes']->take(3) as $note)
                    @php
                        $sc = $note->score >= 14?'A':($note->score>=10?'B':($note->score>=8?'C':'D'));
                    @endphp
                    <div class="note-row">
                        <span style="color:var(--ink-70);font-size:.78rem;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $note->evaluation?->subject?->name ?? 'Matière' }}
                        </span>
                        <span class="score-chip sc-{{ $sc }}">{{ $note->score }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Actions --}}
                <div class="enfant-actions">
                    <a href="{{ route('parent.enfant.notes', $a->id) }}" class="p-btn p-btn-outline" style="font-size:.75rem;padding:.4rem .75rem;">
                        📚 Notes
                    </a>
                    <a href="{{ route('parent.enfant.finances', $a->id) }}" class="p-btn p-btn-outline" style="font-size:.75rem;padding:.4rem .75rem;">
                        💰 Finances
                    </a>
                    <a href="{{ route('parent.disciplinaire', $a->id) }}" class="p-btn p-btn-outline" style="font-size:.75rem;padding:.4rem .75rem;{{ $d['incidentsOuverts']>0?'border-color:var(--red);color:var(--red);':'' }}">
                        ⚖️ Discipline
                    </a>
                </div>
            </div>
            @empty
            <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--ink-40);">
                <div style="font-size:2rem;margin-bottom:.75rem;">👨‍👩‍👧</div>
                <div style="font-weight:700;">Aucun enfant associé à votre compte</div>
                <div style="font-size:.83rem;margin-top:.3rem;">Contactez l'administration pour lier vos enfants.</div>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection