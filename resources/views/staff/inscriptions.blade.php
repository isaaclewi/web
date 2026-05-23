@extends('staff.master')

@section('title', 'Personnel administratif')
@section('page-title', 'Personnel administratif')
@section('page-sub', 'Gestion du staff et des accès')

@push('styles')
<style>
.fg2 { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
.fg3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.875rem; }
.fg-group { display:flex; flex-direction:column; gap:.35rem; }

/* ── Modal ── */
.s-modal { display:none; position:fixed; inset:0; z-index:500; background:rgba(8,12,20,.6); backdrop-filter:blur(4px); align-items:flex-start; justify-content:center; padding-top:3%; }
.s-modal.open { display:flex; }
.s-modal-box { background:var(--white); border-radius:16px; width:600px; max-width:95%; max-height:92vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:modalIn .25s cubic-bezier(.4,0,.2,1) both; }
@keyframes modalIn { from{transform:translateY(-16px);opacity:0} to{transform:none;opacity:1} }
.s-modal-hd { padding:1.25rem 1.5rem; border-bottom:1px solid var(--brd); display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; background:var(--white); z-index:1; }
.s-modal-hd h3 { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; }
.s-modal-body { padding:1.5rem; }
.s-modal-ft { padding:1rem 1.5rem; border-top:1px solid var(--brd); display:flex; gap:.75rem; justify-content:flex-end; position:sticky; bottom:0; background:var(--white); }

/* ── Staff avatar ── */
.staff-av { width:36px; height:36px; border-radius:10px; background:var(--night); border:1.5px solid var(--steel); display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-size:.72rem; font-weight:700; color:var(--gold); flex-shrink:0; }

/* ── Actions dropdown ── */
.act-wrap { position:relative; display:inline-block; }
.act-menu { display:none; position:absolute; right:0; top:calc(100% + 4px); z-index:100; background:var(--white); border:1px solid var(--brd); border-radius:10px; box-shadow:0 8px 28px rgba(0,0,0,.1); min-width:180px; overflow:hidden; }
.act-wrap:hover .act-menu, .act-wrap:focus-within .act-menu { display:block; }
.act-menu button { display:flex; align-items:center; gap:.5rem; width:100%; padding:.6rem 1rem; font-size:.8rem; font-weight:500; color:#374151; border:none; background:none; cursor:pointer; font-family:inherit; transition:background .12s; }
.act-menu button:hover { background:var(--bg); }
.act-sep { height:1px; background:var(--brd); margin:.25rem 0; }
.act-del { color:var(--err) !important; }
.act-del:hover { background:var(--err-l) !important; }

/* ── Unit badge ── */
.unit-pill { display:inline-flex; align-items:center; gap:.3rem; padding:.2rem .55rem; border-radius:6px; font-size:.7rem; font-weight:600; background:var(--info-l); color:#1e3a8a; }
</style>
@endpush

@section('content')

{{-- ══ STATS ══ --}}
<div class="stat-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-val">{{ $stats['total'] }}</div>
        <div class="stat-lbl">Total staff</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--ok)">{{ $stats['actifs'] }}</div>
        <div class="stat-lbl">Actifs</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--err)">{{ $stats['inactifs'] }}</div>
        <div class="stat-lbl">Inactifs</div>
    </div>
</div>

{{-- ══ TOOLBAR ══ --}}
<div style="display:flex;justify-content:flex-end;margin-bottom:1.25rem">
    <button class="btn btn-gold" onclick="openCreate()">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajouter un membre
    </button>
</div>

{{-- ══ TABLE ══ --}}
<div class="s-card">
    <div class="s-card-hd">
        <h3>Membres du personnel</h3>
        <span style="font-size:.72rem;color:var(--mist)">{{ $staffMembers->total() }} membre(s)</span>
    </div>

    <div style="overflow-x:auto">
        <table class="s-tbl">
            <thead>
                <tr>
                    <th>Membre</th>
                    <th>Poste</th>
                    <th>Unité</th>
                    <th>Contact</th>
                    <th>Statut</th>
                    <th style="width:70px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffMembers as $member)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem">
                            <div class="staff-av">{{ mb_substr($member->prenom,0,1) }}{{ mb_substr($member->nom,0,1) }}</div>
                            <div>
                                <div style="font-weight:600;font-size:.83rem">{{ $member->prenom }} {{ $member->nom }}</div>
                                <div style="font-size:.68rem;color:var(--mist);font-family:monospace">{{ $member->matricule }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.8rem">{{ $member->poste ?? '—' }}</td>
                    <td>
                        @if($member->administrativeUnit)
                            <span class="unit-pill">{{ $member->administrativeUnit->name }}</span>
                        @else <span style="color:var(--mist);font-size:.78rem">—</span>@endif
                    </td>
                    <td style="font-size:.78rem;color:var(--mist)">
                        {{ $member->telephone ?? '—' }}<br>
                        @if($member->email)<span style="font-size:.68rem">{{ $member->email }}</span>@endif
                    </td>
                    <td>
                        @if($member->status)
                            <span class="bdg bdg-g">Actif</span>
                        @else
                            <span class="bdg bdg-r">Inactif</span>
                        @endif
                    </td>
                    <td>
                        <div class="act-wrap" tabindex="0">
                            <button class="btn btn-ot btn-sm">•••</button>
                            <div class="act-menu">
                                <button onclick="openEdit({{ json_encode($member) }})">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Modifier
                                </button>
                                <form method="POST" action="{{ route('staff.inscriptions.toggleStatus', $member) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $member->status ? 0 : 1 }}">
                                    <button>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                                        {{ $member->status ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                <button onclick="openPwd({{ $member->id }}, '{{ $member->prenom }} {{ $member->nom }}')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    Réinitialiser MDP
                                </button>
                                <div class="act-sep"></div>
                                <form method="POST" action="{{ route('staff.inscriptions.destroy', $member) }}">
                                    @csrf @method('DELETE')
                                    <button class="act-del" onclick="return confirm('Supprimer ce membre ?')">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">
                    <div class="s-empty">
                        <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                        <h4>Aucun membre</h4>
                        <p>Ajoutez votre premier membre du personnel.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:.875rem 1.375rem;border-top:1px solid var(--brd)">{{ $staffMembers->links() }}</div>
</div>


{{-- ══ MODAL CRÉER ══ --}}
<div class="s-modal" id="modal-create">
    <div class="s-modal-box">
        <div class="s-modal-hd">
            <h3>Ajouter un membre du personnel</h3>
            <button class="btn btn-ot btn-sm" onclick="closeCreate()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.inscriptions.store') }}">
            @csrf
            <div class="s-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Prénom *</label><input class="inp" name="prenom" required></div>
                    <div class="fg-group"><label class="lbl">Nom *</label><input class="inp" name="nom" required></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Poste</label><input class="inp" name="poste" placeholder="Ex: Secrétaire"></div>
                    <div class="fg-group"><label class="lbl">Téléphone</label><input class="inp" name="telephone"></div>
                </div>
                <div class="fg-group" style="margin-bottom:.875rem">
                    <label class="lbl">Unité administrative</label>
                    <select class="inp" name="administrative_unit_id">
                        <option value="">— Automatique (Direction) —</option>
                        @foreach($administrativeUnits as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="border-top:1px solid var(--brd);padding-top:.875rem">
                    <div class="lbl" style="margin-bottom:.75rem;color:var(--mist)">Compte utilisateur (optionnel)</div>
                    <div class="fg-group" style="margin-bottom:.75rem"><label class="lbl">Email</label><input class="inp" type="email" name="email"></div>
                    <div class="fg2">
                        <div class="fg-group"><label class="lbl">Mot de passe</label><input class="inp" type="password" name="password"></div>
                        <div class="fg-group"><label class="lbl">Confirmer</label><input class="inp" type="password" name="password_confirmation"></div>
                    </div>
                </div>
            </div>
            <div class="s-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeCreate()">Annuler</button>
                <button type="submit" class="btn btn-gold">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL EDIT ══ --}}
<div class="s-modal" id="modal-edit">
    <div class="s-modal-box">
        <div class="s-modal-hd">
            <h3>Modifier le membre</h3>
            <button class="btn btn-ot btn-sm" onclick="closeEdit()">✕</button>
        </div>
        <form method="POST" id="edit-form">
            @csrf @method('PUT')
            <div class="s-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Prénom *</label><input class="inp" name="prenom" id="e_prenom" required></div>
                    <div class="fg-group"><label class="lbl">Nom *</label><input class="inp" name="nom" id="e_nom" required></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Poste</label><input class="inp" name="poste" id="e_poste"></div>
                    <div class="fg-group"><label class="lbl">Téléphone</label><input class="inp" name="telephone" id="e_tel"></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Unité</label>
                        <select class="inp" name="administrative_unit_id" id="e_unit">
                            <option value="">—</option>
                            @foreach($administrativeUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Email</label><input class="inp" type="email" name="email" id="e_email"></div>
                </div>
            </div>
            <div class="s-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeEdit()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL RESET PASSWORD ══ --}}
<div class="s-modal" id="modal-pwd" style="align-items:center;padding-top:0">
    <div class="s-modal-box" style="max-width:400px">
        <div class="s-modal-hd">
            <h3 id="pwd-title">Réinitialiser le mot de passe</h3>
            <button class="btn btn-ot btn-sm" onclick="closePwd()">✕</button>
        </div>
        <form method="POST" id="pwd-form">
            @csrf
            <div class="s-modal-body">
                <div class="fg-group" style="margin-bottom:.75rem"><label class="lbl">Nouveau mot de passe</label><input class="inp" type="password" name="password" required minlength="8"></div>
                <div class="fg-group"><label class="lbl">Confirmer</label><input class="inp" type="password" name="password_confirmation" required></div>
            </div>
            <div class="s-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closePwd()">Annuler</button>
                <button type="submit" class="btn btn-gold">Réinitialiser</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openCreate(){ document.getElementById('modal-create').classList.add('open'); document.body.style.overflow='hidden'; }
function closeCreate(){ document.getElementById('modal-create').classList.remove('open'); document.body.style.overflow=''; }

function openEdit(m) {
    document.getElementById('e_prenom').value = m.prenom??'';
    document.getElementById('e_nom').value    = m.nom??'';
    document.getElementById('e_poste').value  = m.poste??'';
    document.getElementById('e_tel').value    = m.telephone??'';
    document.getElementById('e_unit').value   = m.administrative_unit_id??'';
    document.getElementById('e_email').value  = m.email??'';
    document.getElementById('edit-form').action = `/staff/inscriptions/${m.id}`;
    document.getElementById('modal-edit').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeEdit(){ document.getElementById('modal-edit').classList.remove('open'); document.body.style.overflow=''; }

function openPwd(id, name) {
    document.getElementById('pwd-title').textContent = `Réinitialiser : ${name}`;
    document.getElementById('pwd-form').action = `/staff/inscriptions/${id}/reset-password`;
    document.getElementById('modal-pwd').classList.add('open');
    document.body.style.overflow='hidden';
}
function closePwd(){ document.getElementById('modal-pwd').classList.remove('open'); document.body.style.overflow=''; }

['modal-create','modal-edit','modal-pwd'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e){ if(e.target===this){ this.classList.remove('open'); document.body.style.overflow=''; } });
});
</script>
@endpush