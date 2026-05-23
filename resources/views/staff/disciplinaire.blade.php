@extends('staff.master')

@section('title', 'Discipline')
@section('page-title', 'Gestion disciplinaire')
@section('page-sub', 'Suivi des incidents et sanctions')

@push('styles')
<style>
/* ── Grille 4 stats ── */
.disc-stat-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
.disc-stat {
    background:var(--white); border:1px solid var(--brd); border-radius:14px;
    padding:1.25rem 1.375rem; position:relative; overflow:hidden;
    transition:box-shadow .2s, border-color .2s;
}
.disc-stat:hover { box-shadow:0 4px 18px rgba(0,0,0,.07); border-color:var(--brd-d); }
.disc-stat-val { font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; line-height:1; letter-spacing:-.04em; color:var(--night); }
.disc-stat-lbl { font-size:.73rem; color:var(--mist); margin-top:.35rem; }
.disc-stat-icon {
    position:absolute; top:1rem; right:1rem;
    width:36px; height:36px; border-radius:10px;
    background:var(--bg); display:flex; align-items:center; justify-content:center;
}
.disc-stat-icon svg { width:16px; height:16px; }
.disc-stat.danger .disc-stat-val { color:var(--err); }
.disc-stat.warn   .disc-stat-val { color:var(--warn); }
.disc-stat.info   .disc-stat-val { color:var(--info); }
.disc-stat.ok     .disc-stat-val { color:var(--ok); }

/* ── Barre de gravité colorée en bas de chaque stat ── */
.disc-stat::after {
    content:''; position:absolute; bottom:0; left:0; right:0;
    height:3px; background:var(--brd);
}
.disc-stat.danger::after { background:var(--err); }
.disc-stat.warn::after   { background:var(--warn); }
.disc-stat.info::after   { background:var(--info); }
.disc-stat.ok::after     { background:var(--ok); }

/* ── Toolbar ── */
.disc-toolbar {
    display:flex; gap:.75rem; flex-wrap:wrap; align-items:center;
    margin-bottom:1.25rem;
}

/* ── Filter bar ── */
.disc-filters {
    background:var(--white); border:1px solid var(--brd); border-radius:14px;
    padding:1rem 1.375rem; margin-bottom:1.25rem;
    display:flex; gap:.75rem; flex-wrap:wrap; align-items:flex-end;
}
.disc-filters .inp { flex:1; min-width:160px; }

/* ── Table ── */
.disc-table-wrap { background:var(--white); border:1px solid var(--brd); border-radius:14px; overflow:hidden; }
.disc-table-hd {
    padding:.875rem 1.375rem; border-bottom:1px solid var(--brd);
    display:flex; align-items:center; justify-content:space-between; gap:.75rem;
}
.disc-table-hd h3 { font-family:'Syne',sans-serif; font-size:.9rem; font-weight:700; }
.disc-count {
    font-size:.72rem; font-weight:600; color:var(--mist);
    background:var(--bg); border:1px solid var(--brd);
    border-radius:20px; padding:.2rem .65rem;
}

/* ── Gravité indicator ── */
.grav-dot {
    display:inline-flex; align-items:center; gap:.4rem;
    font-size:.75rem; font-weight:600; padding:.25rem .6rem;
    border-radius:6px; white-space:nowrap;
}
.grav-1 { background:#fef3c7; color:#92400e; }
.grav-2 { background:#fee2e2; color:#7f1d1d; }
.grav-3 { background:rgba(239,68,68,.15); color:#dc2626; border:1px solid rgba(239,68,68,.3); }

/* ── Statut badge ── */
.st-ouvert  { background:var(--warn-l); color:#92400e; }
.st-suivi   { background:var(--info-l); color:#1e3a8a; }
.st-clos    { background:var(--ok-l);   color:#065f46; }

/* ── Actions dropdown ── */
.act-wrap { position:relative; display:inline-block; }
.act-btn { cursor:pointer; }
.act-menu {
    display:none; position:absolute; right:0; top:calc(100% + 4px); z-index:100;
    background:var(--white); border:1px solid var(--brd); border-radius:10px;
    box-shadow:0 8px 28px rgba(0,0,0,.1); min-width:160px; overflow:hidden;
}
.act-wrap:hover .act-menu, .act-wrap:focus-within .act-menu { display:block; }
.act-menu a, .act-menu button {
    display:flex; align-items:center; gap:.5rem; width:100%;
    padding:.6rem 1rem; font-size:.8rem; font-weight:500;
    color:#374151; border:none; background:none; cursor:pointer;
    font-family:inherit; text-decoration:none;
    transition:background .12s;
}
.act-menu a:hover, .act-menu button:hover { background:var(--bg); }
.act-menu .act-sep { height:1px; background:var(--brd); margin:.25rem 0; }
.act-menu .act-del { color:var(--err); }
.act-menu .act-del:hover { background:var(--err-l); }

/* ── Modal overlay ── */
.disc-modal {
    display:none; position:fixed; inset:0; z-index:500;
    background:rgba(8,12,20,.6); backdrop-filter:blur(4px);
    align-items:flex-start; justify-content:center; padding-top:4%;
}
.disc-modal.open { display:flex; }
.disc-modal-box {
    background:var(--white); border-radius:16px; width:640px; max-width:95%;
    max-height:90vh; overflow-y:auto;
    box-shadow:0 20px 60px rgba(0,0,0,.2);
    animation:modalIn .25s cubic-bezier(.4,0,.2,1) both;
}
@keyframes modalIn {
    from { transform:translateY(-16px); opacity:0; }
    to   { transform:none; opacity:1; }
}
.disc-modal-hd {
    padding:1.25rem 1.5rem; border-bottom:1px solid var(--brd);
    display:flex; align-items:center; justify-content:space-between;
    position:sticky; top:0; background:var(--white); z-index:1;
}
.disc-modal-hd h3 { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; }
.disc-modal-body { padding:1.5rem; }
.disc-modal-ft {
    padding:1rem 1.5rem; border-top:1px solid var(--brd);
    display:flex; gap:.75rem; justify-content:flex-end;
    position:sticky; bottom:0; background:var(--white);
}

/* ── Form grid ── */
.fg2 { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
.fg3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.875rem; }
.fg1 { display:grid; grid-template-columns:1fr; gap:.875rem; }
.fg-group { display:flex; flex-direction:column; gap:.35rem; }

/* ── Top apprenants ── */
.top-card { background:var(--white); border:1px solid var(--brd); border-radius:14px; overflow:hidden; }
.top-item {
    display:flex; align-items:center; gap:.875rem;
    padding:.875rem 1.375rem; border-bottom:1px solid var(--brd);
    transition:background .12s;
}
.top-item:last-child { border-bottom:none; }
.top-item:hover { background:var(--bg); }
.top-rank {
    font-family:'Syne',sans-serif; font-size:.65rem; font-weight:800;
    color:var(--mist); width:22px; text-align:center; flex-shrink:0;
}
.top-av {
    width:34px; height:34px; border-radius:9px; background:var(--bg);
    border:1px solid var(--brd); display:flex; align-items:center; justify-content:center;
    font-family:'Syne',sans-serif; font-size:.72rem; font-weight:700; color:var(--night);
    flex-shrink:0;
}
.top-info { flex:1; min-width:0; }
.top-name { font-size:.83rem; font-weight:600; color:var(--night); }
.top-sub { font-size:.7rem; color:var(--mist); margin-top:.1rem; }
.top-count {
    font-family:'Syne',sans-serif; font-size:1.1rem; font-weight:800;
    color:var(--err);
}

/* ── Par type chart ── */
.type-row {
    display:flex; align-items:center; gap:.75rem;
    padding:.5rem 1.375rem; border-bottom:1px solid var(--brd);
}
.type-row:last-child { border-bottom:none; }
.type-lbl { font-size:.78rem; font-weight:500; color:#374151; width:130px; flex-shrink:0; }
.type-bar-wrap { flex:1; height:6px; background:var(--bg); border-radius:3px; overflow:hidden; }
.type-bar { height:100%; border-radius:3px; background:var(--info); transition:width .6s cubic-bezier(.4,0,.2,1); }
.type-n { font-size:.72rem; font-weight:700; color:var(--mist); width:30px; text-align:right; flex-shrink:0; }

/* ── Annee selector ── */
.yr-tabs { display:flex; gap:.25rem; flex-wrap:wrap; }
.yr-tab {
    padding:.3rem .75rem; border-radius:7px; font-size:.76rem; font-weight:600;
    border:1px solid var(--brd); background:var(--white); color:#6b7280;
    cursor:pointer; text-decoration:none; transition:all .15s;
}
.yr-tab.on { background:var(--night); color:var(--white); border-color:var(--night); }
.yr-tab:hover:not(.on) { background:var(--bg); border-color:var(--brd-d); color:var(--night); }

/* ── Edit modal ── */
.edit-modal { display:none; position:fixed; inset:0; z-index:600; background:rgba(8,12,20,.6); backdrop-filter:blur(4px); align-items:flex-start; justify-content:center; padding-top:4%; }
.edit-modal.open { display:flex; }

@media(max-width:768px) {
    .fg2, .fg3 { grid-template-columns:1fr; }
    .disc-modal-box { max-height:95vh; }
    .disc-filters { flex-direction:column; }
}
</style>
@endpush

@section('content')

{{-- ══ STATS ══ --}}
<div class="disc-stat-grid">
    <div class="disc-stat">
        <div class="disc-stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div class="disc-stat-val">{{ $stats->total ?? 0 }}</div>
        <div class="disc-stat-lbl">Total incidents</div>
    </div>

    <div class="disc-stat danger">
        <div class="disc-stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="disc-stat-val">{{ $stats->graves ?? 0 }}</div>
        <div class="disc-stat-lbl">Graves (gravité 3)</div>
    </div>

    <div class="disc-stat warn">
        <div class="disc-stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <div class="disc-stat-val">{{ $stats->ouverts ?? 0 }}</div>
        <div class="disc-stat-lbl">Ouverts</div>
    </div>

    <div class="disc-stat info">
        <div class="disc-stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h8z"/></svg>
        </div>
        <div class="disc-stat-val">{{ $stats->notifies ?? 0 }}</div>
        <div class="disc-stat-lbl">Parents notifiés</div>
    </div>

    <div class="disc-stat ok">
        <div class="disc-stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="disc-stat-val">{{ $stats->clos ?? 0 }}</div>
        <div class="disc-stat-lbl">Clôturés</div>
    </div>

    <div class="disc-stat">
        <div class="disc-stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div class="disc-stat-val">{{ $stats->en_suivi ?? 0 }}</div>
        <div class="disc-stat-lbl">En suivi</div>
    </div>
</div>

{{-- ══ TOOLBAR ══ --}}
<div class="disc-toolbar">

    <div class="yr-tabs">
        @foreach($anneesDispos as $yr)
            <a href="{{ request()->fullUrlWithQuery(['annee' => $yr]) }}"
               class="yr-tab {{ $annee == $yr ? 'on' : '' }}">
                {{ $yr }}
            </a>
        @endforeach
    </div>

    <div style="display:flex;gap:.5rem;margin-left:auto">
        <a href="{{ route('staff.disciplinaire.export', ['annee' => $annee]) }}"
           class="btn btn-ot">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Exporter
        </a>
        <button class="btn btn-gold" onclick="openNewIncident()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvel incident
        </button>
    </div>
</div>

{{-- ══ LAYOUT GRILLE ══ --}}
<div style="display:grid;grid-template-columns:1fr 300px;gap:1.25rem;align-items:start">

{{-- ╔═ COLONNE GAUCHE — Table ═╗ --}}
<div>

    {{-- Filtres --}}
    <div class="disc-filters">
        <form method="GET" style="display:contents">
            <input type="hidden" name="annee" value="{{ $annee }}">

            <input class="inp" type="text" name="search" placeholder="Rechercher un apprenant…"
                   value="{{ $search }}">

            <select class="inp" name="classe_id">
                <option value="">Toutes les classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" @selected($classeId == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>

            <select class="inp" name="type">
                <option value="">Type</option>
                @foreach($typeLabels as $k => $v)
                    <option value="{{ $k }}" @selected($type == $k)>{{ $v }}</option>
                @endforeach
            </select>

            <select class="inp" name="gravite">
                <option value="">Gravité</option>
                @foreach($graviteLabels as $k => $v)
                    <option value="{{ $k }}" @selected($gravite == $k)>{{ $v }}</option>
                @endforeach
            </select>

            <select class="inp" name="statut">
                <option value="">Statut</option>
                <option value="ouvert"   @selected($statut=='ouvert')>Ouvert</option>
                <option value="en_suivi" @selected($statut=='en_suivi')>En suivi</option>
                <option value="clos"     @selected($statut=='clos')>Clôturé</option>
            </select>

            <button class="btn btn-dk" type="submit">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                Filtrer
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="disc-table-wrap">
        <div class="disc-table-hd">
            <h3>Incidents disciplinaires</h3>
            <span class="disc-count">{{ $incidents->total() }} résultat(s)</span>
        </div>

        <table class="s-tbl">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Apprenant</th>
                    <th>Classe</th>
                    <th>Type</th>
                    <th>Gravité</th>
                    <th>Sanction</th>
                    <th>Statut</th>
                    <th style="width:70px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $incident)
                <tr>
                    <td style="white-space:nowrap;color:var(--mist);font-size:.78rem">
                        {{ $incident->date_incident?->format('d/m/Y') ?? '—' }}
                    </td>

                    <td>
                        <a href="{{ route('staff.disciplinaire.apprenant', $incident->apprenant) }}"
                           style="font-weight:600;color:var(--night);text-decoration:none;font-size:.83rem">
                            {{ $incident->apprenant?->prenom }} {{ $incident->apprenant?->nom }}
                        </a>
                        @if($incident->apprenant?->matricule)
                        <div style="font-size:.68rem;color:var(--mist)">{{ $incident->apprenant->matricule }}</div>
                        @endif
                    </td>

                    <td style="font-size:.8rem">{{ $incident->apprenant?->classe?->name ?? '—' }}</td>

                    <td>
                        <span class="bdg bdg-n">{{ $typeLabels[$incident->type] ?? $incident->type }}</span>
                    </td>

                    <td>
                        <span class="grav-dot grav-{{ $incident->gravite }}">
                            @php $gd=['1'=>'●','2'=>'●●','3'=>'●●●']; @endphp
                            {{ $gd[$incident->gravite] ?? '' }}
                            {{ $graviteLabels[$incident->gravite] ?? $incident->gravite }}
                        </span>
                    </td>

                    <td style="font-size:.78rem;color:#374151">
                        {{ $sanctionLabels[$incident->sanction] ?? $incident->sanction }}
                        @if($incident->sanction_executee)
                            <span class="bdg bdg-g" style="font-size:.6rem;padding:.1rem .4rem">✓</span>
                        @endif
                    </td>

                    <td>
                        <span class="bdg st-{{ $incident->statut }}">
                            {{ match($incident->statut){ 'ouvert'=>'Ouvert','en_suivi'=>'En suivi','clos'=>'Clôturé',default=>$incident->statut } }}
                        </span>
                    </td>

                    <td>
                        <div class="act-wrap" tabindex="0">
                            <button class="btn btn-ot btn-sm act-btn">
                                •••
                            </button>
                            <div class="act-menu">
                                <button onclick="openEdit({{ json_encode($incident) }})">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Modifier
                                </button>
                                <a href="{{ route('staff.disciplinaire.apprenant', $incident->apprenant) }}">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Fiche apprenant
                                </a>
                                <div class="act-sep"></div>
                                <form method="POST" action="{{ route('staff.disciplinaire.destroy', $incident) }}">
                                    @csrf @method('DELETE')
                                    <button class="act-del" onclick="return confirm('Supprimer cet incident ?')">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="s-empty">
                            <div class="s-empty-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <h4>Aucun incident</h4>
                            <p>Aucun incident disciplinaire pour {{ $annee }}.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding:.875rem 1.375rem;border-top:1px solid var(--brd)">
            {{ $incidents->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- ╔═ COLONNE DROITE — Widgets ═╗ --}}
<div style="display:flex;flex-direction:column;gap:1rem">

    {{-- Par type --}}
    @if(isset($parType) && $parType->count())
    <div class="top-card">
        <div style="padding:.875rem 1.375rem;border-bottom:1px solid var(--brd)">
            <h3 style="font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700">Répartition par type</h3>
        </div>
        @php $maxType = $parType->max('total'); @endphp
        @foreach($parType as $pt)
        <div class="type-row">
            <div class="type-lbl">{{ $typeLabels[$pt->type] ?? $pt->type }}</div>
            <div class="type-bar-wrap">
                <div class="type-bar" style="width:{{ $maxType > 0 ? round($pt->total/$maxType*100) : 0 }}%"></div>
            </div>
            <div class="type-n">{{ $pt->total }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Top apprenants --}}
    @if(isset($topApprenants) && $topApprenants->count())
    <div class="top-card">
        <div style="padding:.875rem 1.375rem;border-bottom:1px solid var(--brd)">
            <h3 style="font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700">Top apprenants concernés</h3>
        </div>
        @foreach($topApprenants as $i => $ta)
        <div class="top-item">
            <div class="top-rank">#{{ $i+1 }}</div>
            <div class="top-av">
                {{ mb_substr($ta->apprenant->prenom ?? '?', 0, 1) }}{{ mb_substr($ta->apprenant->nom ?? '', 0, 1) }}
            </div>
            <div class="top-info">
                <div class="top-name">{{ $ta->apprenant->prenom }} {{ $ta->apprenant->nom }}</div>
                <div class="top-sub">{{ $ta->apprenant->classe?->name ?? '—' }}</div>
            </div>
            <div class="top-count">{{ $ta->nb_incidents }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Quick add depuis le panneau --}}
    <div class="s-card">
        <div class="s-card-hd">
            <h3>Incident rapide</h3>
        </div>
        <div class="s-card-body">
            <form method="POST" action="{{ route('staff.disciplinaire.store') }}">
                @csrf
                <div class="fg-group" style="margin-bottom:.75rem">
                    <label class="lbl">Apprenant</label>
                    <select class="inp" name="apprenant_id" required>
                        <option value="">Sélectionner…</option>
                        @foreach($apprenants as $a)
                            <option value="{{ $a->id }}">{{ $a->prenom }} {{ $a->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg-group" style="margin-bottom:.75rem">
                    <label class="lbl">Date</label>
                    <input class="inp" type="date" name="date_incident" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="fg2" style="margin-bottom:.75rem">
                    <div class="fg-group">
                        <label class="lbl">Type</label>
                        <select class="inp" name="type" required>
                            @foreach($typeLabels as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Gravité</label>
                        <select class="inp" name="gravite" required>
                            @foreach($graviteLabels as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="fg-group" style="margin-bottom:.75rem">
                    <label class="lbl">Sanction</label>
                    <select class="inp" name="sanction" required>
                        @foreach($sanctionLabels as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg-group" style="margin-bottom:.75rem">
                    <label class="lbl">Description</label>
                    <textarea class="inp" name="description" rows="2" placeholder="Optionnel…"></textarea>
                </div>
                <div class="fg-group" style="margin-bottom:1rem">
                    <label class="lbl">Statut</label>
                    <select class="inp" name="statut" required>
                        <option value="ouvert">Ouvert</option>
                        <option value="en_suivi">En suivi</option>
                        <option value="clos">Clôturé</option>
                    </select>
                </div>
                <button class="btn btn-gold" style="width:100%">
                    Enregistrer l'incident
                </button>
            </form>
        </div>
    </div>
</div>

</div>{{-- /grid --}}


{{-- ══ MODAL NOUVEL INCIDENT (complet) ══ --}}
<div class="disc-modal" id="modal-new">
    <div class="disc-modal-box">
        <div class="disc-modal-hd">
            <h3>Enregistrer un incident disciplinaire</h3>
            <button class="btn btn-ot btn-sm" onclick="closeNewIncident()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.disciplinaire.store') }}">
            @csrf
            <div class="disc-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Apprenant *</label>
                        <select class="inp" name="apprenant_id" required>
                            <option value="">Sélectionner…</option>
                            @foreach($apprenants as $a)
                                <option value="{{ $a->id }}">{{ $a->prenom }} {{ $a->nom }} ({{ $a->classe?->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Date de l'incident *</label>
                        <input class="inp" type="date" name="date_incident" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="fg3" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Type *</label>
                        <select class="inp" name="type" required>
                            @foreach($typeLabels as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Gravité *</label>
                        <select class="inp" name="gravite" required>
                            @foreach($graviteLabels as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Statut *</label>
                        <select class="inp" name="statut" required>
                            <option value="ouvert">Ouvert</option>
                            <option value="en_suivi">En suivi</option>
                            <option value="clos">Clôturé</option>
                        </select>
                    </div>
                </div>
                <div class="fg-group" style="margin-bottom:.875rem">
                    <label class="lbl">Description</label>
                    <textarea class="inp" name="description" rows="3" placeholder="Décrivez l'incident…"></textarea>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Sanction *</label>
                        <select class="inp" name="sanction" required>
                            @foreach($sanctionLabels as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Détail sanction</label>
                        <input class="inp" type="text" name="sanction_detail" placeholder="Précisions…">
                    </div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group" style="flex-direction:row;align-items:center;gap:.5rem">
                        <input type="checkbox" name="parents_notifies" value="1" id="pn_new">
                        <label for="pn_new" style="font-size:.82rem;color:#374151;cursor:pointer">Parents notifiés</label>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Date notification</label>
                        <input class="inp" type="date" name="date_notification">
                    </div>
                </div>
                <div class="fg-group">
                    <label class="lbl">Observations</label>
                    <textarea class="inp" name="observations" rows="2" placeholder="Notes internes…"></textarea>
                </div>
            </div>
            <div class="disc-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeNewIncident()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL EDIT INCIDENT ══ --}}
<div class="edit-modal" id="modal-edit">
    <div class="disc-modal-box">
        <div class="disc-modal-hd">
            <h3>Modifier l'incident</h3>
            <button class="btn btn-ot btn-sm" onclick="closeEdit()">✕</button>
        </div>
        <form method="POST" id="edit-form">
            @csrf @method('PUT')
            <div class="disc-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Date *</label>
                        <input class="inp" type="date" name="date_incident" id="edit_date" required>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Type *</label>
                        <select class="inp" name="type" id="edit_type" required>
                            @foreach($typeLabels as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Gravité *</label>
                        <select class="inp" name="gravite" id="edit_gravite" required>
                            @foreach($graviteLabels as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Statut *</label>
                        <select class="inp" name="statut" id="edit_statut" required>
                            <option value="ouvert">Ouvert</option>
                            <option value="en_suivi">En suivi</option>
                            <option value="clos">Clôturé</option>
                        </select>
                    </div>
                </div>
                <div class="fg-group" style="margin-bottom:.875rem">
                    <label class="lbl">Description</label>
                    <textarea class="inp" name="description" id="edit_desc" rows="3"></textarea>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Sanction *</label>
                        <select class="inp" name="sanction" id="edit_sanction" required>
                            @foreach($sanctionLabels as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Détail sanction</label>
                        <input class="inp" type="text" name="sanction_detail" id="edit_sanction_detail">
                    </div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group" style="flex-direction:row;align-items:center;gap:.5rem">
                        <input type="checkbox" name="sanction_executee" value="1" id="edit_exec">
                        <label for="edit_exec" style="font-size:.82rem;color:#374151;cursor:pointer">Sanction exécutée</label>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Date d'exécution</label>
                        <input class="inp" type="date" name="sanction_date_execution" id="edit_exec_date">
                    </div>
                </div>
                <div class="fg2">
                    <div class="fg-group" style="flex-direction:row;align-items:center;gap:.5rem">
                        <input type="checkbox" name="parents_notifies" value="1" id="edit_pn">
                        <label for="edit_pn" style="font-size:.82rem;color:#374151;cursor:pointer">Parents notifiés</label>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Date notification</label>
                        <input class="inp" type="date" name="date_notification" id="edit_notif_date">
                    </div>
                </div>
                <div class="fg-group" style="margin-top:.875rem">
                    <label class="lbl">Observations</label>
                    <textarea class="inp" name="observations" id="edit_obs" rows="2"></textarea>
                </div>
            </div>
            <div class="disc-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeEdit()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Modal Nouvel incident ──
function openNewIncident() {
    document.getElementById('modal-new').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeNewIncident() {
    document.getElementById('modal-new').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('modal-new').addEventListener('click', function(e) {
    if (e.target === this) closeNewIncident();
});

// ── Modal Edit ──
function openEdit(incident) {
    const form = document.getElementById('edit-form');
    form.action = `/staff/disciplinaire/${incident.id}`;

    document.getElementById('edit_date').value          = incident.date_incident?.substring(0, 10) ?? '';
    document.getElementById('edit_type').value          = incident.type ?? '';
    document.getElementById('edit_gravite').value       = incident.gravite ?? '';
    document.getElementById('edit_statut').value        = incident.statut ?? '';
    document.getElementById('edit_desc').value          = incident.description ?? '';
    document.getElementById('edit_sanction').value      = incident.sanction ?? '';
    document.getElementById('edit_sanction_detail').value = incident.sanction_detail ?? '';
    document.getElementById('edit_exec').checked        = !!incident.sanction_executee;
    document.getElementById('edit_exec_date').value     = incident.sanction_date_execution?.substring(0,10) ?? '';
    document.getElementById('edit_pn').checked          = !!incident.parents_notifies;
    document.getElementById('edit_notif_date').value    = incident.date_notification?.substring(0,10) ?? '';
    document.getElementById('edit_obs').value           = incident.observations ?? '';

    document.getElementById('modal-edit').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeEdit() {
    document.getElementById('modal-edit').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
</script>
@endpush