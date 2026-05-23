@extends('staff.master')

@section('title', 'Absences')
@section('page-title', 'Gestion des absences')
@section('page-sub', 'Suivi et enregistrement des absences')

@push('styles')
<style>
.fg2{display:grid;grid-template-columns:1fr 1fr;gap:.875rem}.fg-group{display:flex;flex-direction:column;gap:.35rem}
.abs-modal{display:none;position:fixed;inset:0;z-index:500;background:rgba(8,12,20,.6);backdrop-filter:blur(4px);align-items:flex-start;justify-content:center;padding-top:3%}
.abs-modal.open{display:flex}
.abs-modal-box{background:var(--white);border-radius:16px;width:520px;max-width:95%;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .25s cubic-bezier(.4,0,.2,1) both}
@keyframes modalIn{from{transform:translateY(-16px);opacity:0}to{transform:none;opacity:1}}
.abs-modal-hd{padding:1.25rem 1.5rem;border-bottom:1px solid var(--brd);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--white);z-index:1}
.abs-modal-hd h3{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700}
.abs-modal-body{padding:1.5rem}
.abs-modal-ft{padding:1rem 1.5rem;border-top:1px solid var(--brd);display:flex;gap:.75rem;justify-content:flex-end;position:sticky;bottom:0;background:var(--white)}
.grav-dot{display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .55rem;border-radius:6px;font-size:.72rem;font-weight:600}
.grav-1{background:#fef9c3;color:#713f12}.grav-2{background:#fee2e2;color:#7f1d1d}.grav-3{background:rgba(239,68,68,.15);color:#dc2626;border:1px solid rgba(239,68,68,.3)}
.yr-tabs{display:flex;gap:.25rem;flex-wrap:wrap}.yr-tab{padding:.3rem .75rem;border-radius:7px;font-size:.76rem;font-weight:600;border:1px solid var(--brd);background:var(--white);color:#6b7280;cursor:pointer;text-decoration:none;transition:all .15s}
.yr-tab.on{background:var(--night);color:var(--white);border-color:var(--night)}.yr-tab:hover:not(.on){background:var(--bg);border-color:var(--brd-d);color:var(--night)}
@media(max-width:768px){.fg2{grid-template-columns:1fr}}
</style>
@endpush

@section('content')

{{-- STATS --}}
<div class="stat-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-val">{{ $absences->total() }}</div>
        <div class="stat-lbl">Total absences ({{ $annee }})</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--warn)">
            {{ $absences->where('statut','ouvert')->count() }}
        </div>
        <div class="stat-lbl">Ouvertes</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--ok)">
            {{ $absences->where('statut','clos')->count() }}
        </div>
        <div class="stat-lbl">Clôturées</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--err)">
            {{ $absences->where('gravite',3)->count() }}
        </div>
        <div class="stat-lbl">Graves (gravité 3)</div>
    </div>
</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:1.25rem">
    <div class="yr-tabs">
        @for($y = date('Y'); $y >= date('Y')-3; $y--)
        <a href="{{ request()->fullUrlWithQuery(['annee' => $y]) }}"
           class="yr-tab {{ $annee == $y ? 'on' : '' }}">{{ $y }}</a>
        @endfor
    </div>
    <div style="margin-left:auto">
        <button class="btn btn-gold" onclick="openModal()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Enregistrer une absence
        </button>
    </div>
</div>

{{-- LAYOUT --}}
<div style="display:grid;grid-template-columns:1fr 280px;gap:1.25rem;align-items:start">

{{-- TABLE --}}
<div>
    {{-- Filtres --}}
    <div style="background:var(--white);border:1px solid var(--brd);border-radius:14px;padding:.875rem 1.375rem;margin-bottom:1rem">
        <form method="GET" style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:flex-end">
            <input type="hidden" name="annee" value="{{ $annee }}">
            <input class="inp" name="search" value="{{ request('search') }}" placeholder="Rechercher un apprenant…" style="flex:1;min-width:160px">
            <select class="inp" name="classe_id" style="min-width:140px">
                <option value="">Toutes les classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" @selected(request('classe_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-dk">Filtrer</button>
        </form>
    </div>

    <div class="s-card">
        <div class="s-card-hd">
            <h3>Absences — {{ $annee }}</h3>
            <span style="font-size:.72rem;color:var(--mist)">{{ $absences->total() }} résultat(s)</span>
        </div>
        <table class="s-tbl">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Apprenant</th>
                    <th>Classe</th>
                    <th>Gravité</th>
                    <th>Statut</th>
                    <th>Parents notifiés</th>
                    <th style="width:80px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absences as $abs)
                <tr>
                    <td style="white-space:nowrap;color:var(--mist);font-size:.78rem">
                        {{ $abs->date_incident?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:.83rem">
                            {{ $abs->apprenant?->prenom }} {{ $abs->apprenant?->nom }}
                        </div>
                        <div style="font-size:.68rem;color:var(--mist);font-family:monospace">{{ $abs->apprenant?->matricule }}</div>
                    </td>
                    <td style="font-size:.8rem">{{ $abs->apprenant?->classe?->name ?? '—' }}</td>
                    <td>
                        @php $gd=['1'=>'Mineur','2'=>'Modéré','3'=>'Grave']; @endphp
                        <span class="grav-dot grav-{{ $abs->gravite }}">
                            {{ $gd[$abs->gravite] ?? $abs->gravite }}
                        </span>
                    </td>
                    <td>
                        <span class="bdg {{ match($abs->statut){'ouvert'=>'bdg-a','en_suivi'=>'bdg-b','clos'=>'bdg-g',default=>'bdg-n'} }}">
                            {{ match($abs->statut){'ouvert'=>'Ouvert','en_suivi'=>'En suivi','clos'=>'Clôturé',default=>$abs->statut} }}
                        </span>
                    </td>
                    <td>
                        @if($abs->parents_notifies)
                            <span class="bdg bdg-g">Oui</span>
                        @else
                            <span class="bdg bdg-n">Non</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('staff.disciplinaire.destroy',$abs) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-err btn-sm" onclick="return confirm('Supprimer cette absence ?')">✕</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="s-empty">
                        <div class="s-empty-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h4>Aucune absence</h4>
                        <p>Aucune absence enregistrée pour {{ $annee }}.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:.875rem 1.375rem;border-top:1px solid var(--brd)">
            {{ $absences->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- QUICK ADD SIDEBAR --}}
<div class="s-card">
    <div class="s-card-hd"><h3>Saisie rapide</h3></div>
    <div class="s-card-body">
        <form method="POST" action="{{ route('staff.absences.store') }}">
            @csrf
            <div class="fg-group" style="margin-bottom:.75rem">
                <label class="lbl">Apprenant *</label>
                <select class="inp" name="apprenant_id" required>
                    <option value="">Sélectionner…</option>
                    @foreach($apprenants as $a)
                        <option value="{{ $a->id }}">{{ $a->prenom }} {{ $a->nom }} ({{ $a->classe?->name ?? '—' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="fg-group" style="margin-bottom:.75rem">
                <label class="lbl">Date *</label>
                <input class="inp" type="date" name="date_incident" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="fg-group" style="margin-bottom:.75rem">
                <label class="lbl">Gravité *</label>
                <select class="inp" name="gravite" required>
                    <option value="1">1 — Mineur</option>
                    <option value="2">2 — Modéré</option>
                    <option value="3">3 — Grave</option>
                </select>
            </div>
            <div class="fg-group" style="margin-bottom:.75rem">
                <label class="lbl">Statut *</label>
                <select class="inp" name="statut" required>
                    <option value="ouvert">Ouvert</option>
                    <option value="en_suivi">En suivi</option>
                    <option value="clos">Clôturé</option>
                </select>
            </div>
            <div class="fg-group" style="margin-bottom:1rem">
                <label class="lbl">Motif / Description</label>
                <textarea class="inp" name="description" rows="2" placeholder="Optionnel…"></textarea>
            </div>
            <button class="btn btn-gold" style="width:100%">Enregistrer</button>
        </form>
    </div>
</div>

</div>

{{-- MODAL COMPLET --}}
<div class="abs-modal" id="modal-abs">
    <div class="abs-modal-box">
        <div class="abs-modal-hd">
            <h3>Enregistrer une absence</h3>
            <button class="btn btn-ot btn-sm" onclick="closeModal()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.absences.store') }}">
            @csrf
            <div class="abs-modal-body">
                <div class="fg-group" style="margin-bottom:.875rem">
                    <label class="lbl">Apprenant *</label>
                    <select class="inp" name="apprenant_id" required>
                        <option value="">Sélectionner…</option>
                        @foreach($apprenants as $a)
                            <option value="{{ $a->id }}">{{ $a->prenom }} {{ $a->nom }} ({{ $a->classe?->name ?? '—' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Date *</label>
                        <input class="inp" type="date" name="date_incident" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Gravité *</label>
                        <select class="inp" name="gravite" required>
                            <option value="1">1 — Mineur</option>
                            <option value="2">2 — Modéré</option>
                            <option value="3">3 — Grave</option>
                        </select>
                    </div>
                </div>
                <div class="fg-group" style="margin-bottom:.875rem">
                    <label class="lbl">Statut *</label>
                    <select class="inp" name="statut" required>
                        <option value="ouvert">Ouvert</option>
                        <option value="en_suivi">En suivi</option>
                        <option value="clos">Clôturé</option>
                    </select>
                </div>
                <div class="fg-group">
                    <label class="lbl">Motif / Description</label>
                    <textarea class="inp" name="description" rows="3" placeholder="Détails de l'absence…"></textarea>
                </div>
            </div>
            <div class="abs-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(){ document.getElementById('modal-abs').classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(){ document.getElementById('modal-abs').classList.remove('open'); document.body.style.overflow=''; }
document.getElementById('modal-abs').addEventListener('click',function(e){ if(e.target===this) closeModal(); });
</script>
@endpush
