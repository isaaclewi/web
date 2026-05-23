@extends('teacher.master')

@section('content')
@include('teacher.partials.css')

{{-- ── BREADCRUMB ── --}}
<nav class="bc">
    <a href="{{ route('teacher.dashboard') }}">Tableau de bord</a>
    <span class="bc-sep">›</span>
    <a href="{{ route('teacher.classes.index') }}">Mes classes</a>
    <span class="bc-sep">›</span>
    <span class="bc-cur">Élèves</span>
</nav>

{{-- ── PAGE HEADER ── --}}
<div class="ph" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <div class="ph-title">Élèves</div>
        <div class="ph-sub">{{ $apprenants->total() }} élève(s)
            @if(request('class_id')) · Classe : <strong>{{ $classes->find(request('class_id'))->name ?? '' }}</strong>@endif
        </div>
    </div>
</div>

{{-- ── FILTER BAR ── --}}
<form method="GET" action="{{ route('teacher.apprenants.index') }}">
<div class="filter-bar">
    <span class="filter-bar-title">Filtrer</span>
    <div class="filter-sep"></div>

    {{-- Recherche --}}
    <div style="position:relative;">
        <input type="text" name="search" value="{{ request('search') }}" class="f-input" style="width:220px;padding-left:2rem;" placeholder="Nom, prénom, matricule…">
        <span style="position:absolute;left:.6rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.85rem;">🔍</span>
    </div>

    {{-- Classe --}}
    <select name="class_id" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Toutes les classes</option>
        @foreach($classes as $cl)
            <option value="{{ $cl->id }}" {{ request('class_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
        @endforeach
    </select>

    {{-- Sexe --}}
    <select name="sexe" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Tous</option>
        <option value="M" {{ request('sexe')==='M' ? 'selected' : '' }}>♂ Masculin</option>
        <option value="F" {{ request('sexe')==='F' ? 'selected' : '' }}>♀ Féminin</option>
    </select>

    {{-- Statut --}}
    <select name="status" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        <option value="actif"    {{ request('status')==='actif'    ? 'selected' : '' }}>Actif</option>
        <option value="inactif"  {{ request('status')==='inactif'  ? 'selected' : '' }}>Inactif</option>
    </select>

    {{-- Tri --}}
    <select name="sort" class="f-input f-input inline" style="width:auto;" onchange="this.form.submit()">
        <option value="nom"       {{ request('sort','nom')==='nom'       ? 'selected' : '' }}>Trier : Nom A→Z</option>
        <option value="prenom"    {{ request('sort')==='prenom'    ? 'selected' : '' }}>Trier : Prénom A→Z</option>
        <option value="matricule" {{ request('sort')==='matricule' ? 'selected' : '' }}>Trier : Matricule</option>
    </select>

    <div class="filter-spacer"></div>

    <button type="submit" class="btn btn-p btn-sm">Appliquer</button>
    @if(request()->hasAny(['search','class_id','sexe','status','sort']))
        <a href="{{ route('teacher.apprenants.index') }}" class="btn btn-o btn-sm">✕ Réinitialiser</a>
    @endif
</div>
</form>

{{-- ── KPI RAPIDES ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.875rem;margin-bottom:1.25rem;" class="kpi-grid">
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-bar" style="background:linear-gradient(90deg,var(--teal),var(--teal-mid));"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $stats['students'] }}</div>
        <div class="kpi-lbl">Total élèves</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-bar" style="background:linear-gradient(90deg,#6366f1,#818cf8);"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $apprenants->where('sexe','M')->count() }}</div>
        <div class="kpi-lbl">♂ Garçons</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-bar" style="background:linear-gradient(90deg,#ec4899,#f472b6);"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $apprenants->where('sexe','F')->count() }}</div>
        <div class="kpi-lbl">♀ Filles</div>
    </div>
    <div class="kpi" style="padding:1rem;">
        <div class="kpi-bar" style="background:linear-gradient(90deg,#10b981,#34d399);"></div>
        <div class="kpi-val" style="font-size:1.5rem;">{{ $stats['classes'] }}</div>
        <div class="kpi-lbl">Classes</div>
    </div>
</div>

{{-- ── TABLE ÉLÈVES ── --}}
<div class="t-card">
    <div class="t-header">
        <div class="t-title">Liste des élèves</div>
        <span style="font-size:.78rem;color:var(--muted);">Page {{ $apprenants->currentPage() }} / {{ $apprenants->lastPage() }}</span>
    </div>

    @if($apprenants->isEmpty())
        <div class="empty">
            <div class="empty-icon">👥</div>
            <div class="empty-text">Aucun élève trouvé pour ces filtres</div>
        </div>
    @else
    <div class="table-wrap">
    <table class="t-table">
        <thead>
            <tr>
                <th>Élève</th>
                <th>Matricule</th>
                <th>Classe</th>
                <th>Sexe</th>
                <th>Naissance</th>
                <th>Statut</th>
                <th>Moy. (mes matières)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apprenants as $ap)
                @php
                    $apGrades = $gradesByApprenant[$ap->id] ?? collect();
                    $apAvg    = $apGrades->isNotEmpty() ? round($apGrades->avg('score'), 1) : null;
                    $pill     = $apAvg ? ($apAvg>=14?'gp-A':($apAvg>=10?'gp-B':($apAvg>=8?'gp-C':'gp-D'))) : '';
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <img src="https://i.pravatar.cc/40?u={{ $ap->id }}" class="av av-sm">
                            <div>
                                <div style="font-weight:600;color:var(--ink);">{{ $ap->nom }} {{ $ap->prenom }}</div>
                                <div style="font-size:.72rem;color:var(--muted);">{{ $ap->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="mono" style="font-size:.78rem;">{{ $ap->matricule }}</td>
                    <td><span class="badge b-teal">{{ $ap->classe->name ?? '—' }}</span></td>
                    <td>
                        @if($ap->sexe === 'F') <span class="badge b-pink">♀ F</span>
                        @else <span class="badge b-blue">♂ M</span>
                        @endif
                    </td>
                    <td style="font-size:.8rem;">
                        {{ $ap->date_naissance ? \Carbon\Carbon::parse($ap->date_naissance)->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        @if($ap->status === 'actif') <span class="badge b-green">Actif</span>
                        @else <span class="badge b-gray">{{ ucfirst($ap->status) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($apAvg)
                            <span class="gp {{ $pill }}">{{ $apAvg }}</span>
                        @else
                            <span style="color:var(--muted);font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-o btn-xs" onclick="openProfile({{ $ap->id }})">Profil</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    {{-- PAGINATION --}}
    <div class="pager">
        <span class="pager-info">{{ $apprenants->firstItem() }}–{{ $apprenants->lastItem() }} sur {{ $apprenants->total() }} élève(s)</span>
        <div class="pager-btns">{{ $apprenants->withQueryString()->links() }}</div>
    </div>
    @endif
</div>

{{-- ── MODAL PROFIL ── --}}
<div class="modal-bg" id="profileModal">
    <div class="modal modal-lg">
        <button class="modal-close" onclick="document.getElementById('profileModal').classList.remove('open')">✕</button>
        <div class="modal-title">Profil élève</div>
        <div id="profileContent" style="color:var(--muted);">Chargement…</div>
    </div>
</div>

<script>
function openProfile(id) {
    document.getElementById('profileModal').classList.add('open');
    document.getElementById('profileContent').innerHTML = '<p style="text-align:center;padding:2rem;">Chargement…</p>';

    fetch(`/teacher/apprenants/${id}`)
        .then(r => r.json())
        .then(({ apprenant: a, grades, average }) => {
            const pill = average >= 14 ? 'gp-A' : average >= 10 ? 'gp-B' : average >= 8 ? 'gp-C' : 'gp-D';
            let gradesRows = '';
            (grades || []).forEach(g => {
                const score = g.score;
                const max   = g.evaluation?.max_score ?? 20;
                const pct   = Math.round(score / max * 100);
                const p2    = score >= max*.7 ? 'gp-A' : score >= max*.5 ? 'gp-B' : score >= max*.4 ? 'gp-C' : 'gp-D';
                gradesRows += `<tr>
                    <td style="font-weight:600;">${g.evaluation?.title ?? '—'}</td>
                    <td><span class="badge b-teal">${g.evaluation?.subject?.name ?? '—'}</span></td>
                    <td><span class="badge b-gray">${g.evaluation?.type ?? '—'}</span></td>
                    <td><span class="gp ${p2}">${score}</span><span style="color:var(--muted);font-size:.72rem;"> /${max}</span></td>
                    <td>
                        <div class="prog" style="width:80px;"><div class="prog-fill" style="width:${pct}%;background:var(--teal);"></div></div>
                    </td>
                </tr>`;
            });

            document.getElementById('profileContent').innerHTML = `
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.25rem;border-bottom:1px solid var(--border);">
                    <img src="https://i.pravatar.cc/72?u=${a.id}" class="av av-lg">
                    <div>
                        <div style="font-family:var(--font-disp);font-size:1.3rem;font-weight:700;color:var(--ink);">${a.nom} ${a.prenom}</div>
                        <div style="font-size:.8rem;color:var(--muted);margin-top:.2rem;">${a.matricule} · ${a.classe?.name ?? ''}</div>
                    </div>
                    <div style="margin-left:auto;text-align:center;">
                        <div class="mono" style="font-size:2rem;font-weight:800;color:var(--teal);">${average ?? '—'}</div>
                        <div style="font-size:.7rem;color:var(--muted);">Moyenne / 20</div>
                    </div>
                </div>
                <div class="info-grid" style="margin-bottom:1.25rem;">
                    <div class="info-item"><div class="info-key">Sexe</div><div class="info-val">${a.sexe === 'F' ? 'Féminin' : 'Masculin'}</div></div>
                    <div class="info-item"><div class="info-key">Naissance</div><div class="info-val">${a.date_naissance ?? '—'}</div></div>
                    <div class="info-item"><div class="info-key">Statut</div><div class="info-val">${a.status}</div></div>
                    <div class="info-item"><div class="info-key">Année acad.</div><div class="info-val">${a.annee_academique ?? '—'}</div></div>
                </div>
                ${grades?.length ? `
                <div style="font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.625rem;">Notes dans mes matières</div>
                <table class="t-table compact">
                    <thead><tr><th>Évaluation</th><th>Matière</th><th>Type</th><th>Note</th><th>Progression</th></tr></thead>
                    <tbody>${gradesRows}</tbody>
                </table>` : '<p style="color:var(--muted);font-size:.875rem;">Aucune note saisie pour cet élève.</p>'}
            `;
        })
        .catch(() => {
            document.getElementById('profileContent').innerHTML = '<p style="color:#ef4444;">Erreur lors du chargement.</p>';
        });
}
</script>
@endsection