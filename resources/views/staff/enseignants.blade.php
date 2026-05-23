@extends('staff.master')

@section('title', 'Enseignants')
@section('page-title', 'Gestion des enseignants')
@section('page-sub', 'Liste, recrutement et gestion')

@push('styles')
<style>
/* ── Layout ── */
.teach-grid { display:grid; grid-template-columns:1fr 340px; gap:1.25rem; align-items:start; }

/* ── Teacher card compact ── */
.teach-row-av {
    width:34px; height:34px; border-radius:9px;
    background:var(--night-4); border:1.5px solid var(--steel);
    display:flex; align-items:center; justify-content:center;
    font-family:'Syne',sans-serif; font-size:.72rem; font-weight:700; color:var(--gold);
    flex-shrink:0;
}
.contrat-pill {
    display:inline-flex; align-items:center;
    padding:.2rem .55rem; border-radius:5px; font-size:.67rem; font-weight:600;
    white-space:nowrap;
}
.c-cdi      { background:#dcfce7; color:#166534; }
.c-cdd      { background:#dbeafe; color:#1e3a8a; }
.c-vacataire{ background:#fef9c3; color:#713f12; }
.c-benevole { background:#f3e8ff; color:#6b21a8; }

/* ── Modal ── */
.t-modal {
    display:none; position:fixed; inset:0; z-index:500;
    background:rgba(8,12,20,.6); backdrop-filter:blur(4px);
    align-items:flex-start; justify-content:center; padding-top:3%;
}
.t-modal.open { display:flex; }
.t-modal-box {
    background:var(--white); border-radius:16px; width:680px; max-width:95%;
    max-height:92vh; overflow-y:auto;
    box-shadow:0 20px 60px rgba(0,0,0,.2);
    animation:modalIn .25s cubic-bezier(.4,0,.2,1) both;
}
@keyframes modalIn { from{transform:translateY(-16px);opacity:0} to{transform:none;opacity:1} }
.t-modal-hd {
    padding:1.25rem 1.5rem; border-bottom:1px solid var(--brd);
    display:flex; align-items:center; justify-content:space-between;
    position:sticky; top:0; background:var(--white); z-index:1;
}
.t-modal-hd h3 { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; }
.t-modal-body { padding:1.5rem; }
.t-modal-ft {
    padding:1rem 1.5rem; border-top:1px solid var(--brd);
    display:flex; gap:.75rem; justify-content:flex-end;
    position:sticky; bottom:0; background:var(--white);
}

/* ── Form helpers ── */
.fg2 { display:grid; grid-template-columns:1fr 1fr; gap:.875rem; }
.fg3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:.875rem; }
.fg-group { display:flex; flex-direction:column; gap:.35rem; }

/* ── Reset pwd inline ── */
.pwd-modal {
    display:none; position:fixed; inset:0; z-index:600;
    background:rgba(8,12,20,.6); backdrop-filter:blur(4px);
    align-items:center; justify-content:center;
}
.pwd-modal.open { display:flex; }
.pwd-box {
    background:var(--white); border-radius:14px; width:400px; max-width:95%;
    padding:1.5rem; box-shadow:0 16px 48px rgba(0,0,0,.18);
    animation:modalIn .2s cubic-bezier(.4,0,.2,1) both;
}
.pwd-box h3 { font-family:'Syne',sans-serif; font-size:.95rem; font-weight:700; margin-bottom:1rem; }

/* ── Checkbox group ── */
.chk-group {
    display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.25rem;
}
.chk-item {
    display:flex; align-items:center; gap:.35rem;
    background:var(--bg); border:1px solid var(--brd);
    border-radius:7px; padding:.3rem .65rem; font-size:.76rem; cursor:pointer;
    transition:all .12s;
}
.chk-item:hover { border-color:var(--brd-d); }
.chk-item input { accent-color:var(--gold); }

/* ── Actions dropdown ── */
.act-wrap { position:relative; display:inline-block; }
.act-menu {
    display:none; position:absolute; right:0; top:calc(100% + 4px); z-index:100;
    background:var(--white); border:1px solid var(--brd); border-radius:10px;
    box-shadow:0 8px 28px rgba(0,0,0,.1); min-width:180px; overflow:hidden;
}
.act-wrap:hover .act-menu, .act-wrap:focus-within .act-menu { display:block; }
.act-menu a, .act-menu button {
    display:flex; align-items:center; gap:.5rem; width:100%;
    padding:.6rem 1rem; font-size:.8rem; font-weight:500;
    color:#374151; border:none; background:none; cursor:pointer;
    font-family:inherit; text-decoration:none; transition:background .12s;
}
.act-menu a:hover, .act-menu button:hover { background:var(--bg); }
.act-sep { height:1px; background:var(--brd); margin:.25rem 0; }
.act-del { color:var(--err) !important; }
.act-del:hover { background:var(--err-l) !important; }

/* ── Search bar ── */
.teach-search {
    background:var(--white); border:1px solid var(--brd); border-radius:14px;
    padding:.875rem 1.375rem; margin-bottom:1.25rem;
    display:flex; gap:.75rem; flex-wrap:wrap; align-items:center;
}

@media(max-width:1024px) { .teach-grid { grid-template-columns:1fr; } }
@media(max-width:768px)  { .fg2,.fg3 { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

{{-- ══ STATS ══ --}}
<div class="stat-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <div class="stat-val">{{ $stats['total'] }}</div>
        <div class="stat-lbl">Total enseignants</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="color:var(--ok)"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div class="stat-val" style="color:var(--ok)">{{ $stats['active'] }}</div>
        <div class="stat-lbl">Actifs</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['hommes'] }}</div>
        <div class="stat-lbl">Hommes</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['femmes'] }}</div>
        <div class="stat-lbl">Femmes</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['cdi'] }}</div>
        <div class="stat-lbl">CDI</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--warn)">{{ $stats['vacataire'] }}</div>
        <div class="stat-lbl">Vacataires</div>
    </div>
</div>

{{-- ══ TOOLBAR ══ --}}
<div style="display:flex;justify-content:flex-end;margin-bottom:1.25rem">
    <button class="btn btn-gold" onclick="openCreate()">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajouter un enseignant
    </button>
</div>

{{-- ══ TABLE ══ --}}
<div class="s-card">
    <div class="s-card-hd">
        <h3>Liste des enseignants</h3>
        <span style="font-size:.72rem;color:var(--mist)">{{ $teachers->total() }} enseignant(s)</span>
    </div>

    <div style="overflow-x:auto">
        <table class="s-tbl">
            <thead>
                <tr>
                    <th>Enseignant</th>
                    <th>Spécialité</th>
                    <th>Contact</th>
                    <th>Contrat</th>
                    <th>Classes</th>
                    <th>Statut</th>
                    <th style="width:70px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $t)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem">
                            <div class="teach-row-av">
                                {{ mb_substr($t->prenom,0,1) }}{{ mb_substr($t->nom,0,1) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.83rem">{{ $t->prenom }} {{ $t->nom }}</div>
                                <div style="font-size:.68rem;color:var(--mist);font-family:monospace">{{ $t->matricule }}</div>
                            </div>
                        </div>
                    </td>

                    <td style="font-size:.8rem;color:#374151">{{ $t->specialite ?? '—' }}</td>

                    <td style="font-size:.78rem;color:var(--mist)">
                        {{ $t->telephone ?? '—' }}<br>
                        @if($t->email)<span style="font-size:.68rem">{{ $t->email }}</span>@endif
                    </td>

                    <td>
                        @if($t->type_contrat)
                        <span class="contrat-pill c-{{ strtolower($t->type_contrat) }}">
                            {{ strtoupper($t->type_contrat) }}
                        </span>
                        @else <span style="color:var(--mist);font-size:.78rem">—</span>@endif
                    </td>

                    <td>
                        @forelse($t->classes->take(2) as $cls)
                            <span class="bdg bdg-b" style="margin-right:.25rem">{{ $cls->name }}</span>
                        @empty <span style="color:var(--mist);font-size:.78rem">Aucune</span>
                        @endforelse
                        @if($t->classes->count() > 2)
                            <span class="bdg bdg-n">+{{ $t->classes->count()-2 }}</span>
                        @endif
                    </td>

                    <td>
                        @if($t->status)
                            <span class="bdg bdg-g">Actif</span>
                        @else
                            <span class="bdg bdg-r">Inactif</span>
                        @endif
                    </td>

                    <td>
                        <div class="act-wrap" tabindex="0">
                            <button class="btn btn-ot btn-sm act-btn">•••</button>
                            <div class="act-menu">
                                <button onclick="openEdit({{ json_encode($t) }})">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Modifier
                                </button>
                                <button onclick="openPwd({{ $t->id }}, '{{ $t->prenom }} {{ $t->nom }}')">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    Réinitialiser MDP
                                </button>
                                <form method="POST" action="{{ route('staff.enseignants.toggleStatus', $t) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $t->status ? 'inactive' : 'active' }}">
                                    <button>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                                        {{ $t->status ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                <div class="act-sep"></div>
                                <form method="POST" action="{{ route('staff.enseignants.destroy', $t) }}">
                                    @csrf @method('DELETE')
                                    <button class="act-del" onclick="return confirm('Supprimer cet enseignant ?')">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="s-empty">
                        <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                        <h4>Aucun enseignant</h4>
                        <p>Ajoutez votre premier enseignant.</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:.875rem 1.375rem;border-top:1px solid var(--brd)">
        {{ $teachers->links() }}
    </div>
</div>


{{-- ══ MODAL CRÉER ══ --}}
<div class="t-modal" id="modal-create">
    <div class="t-modal-box">
        <div class="t-modal-hd">
            <h3>Ajouter un enseignant</h3>
            <button class="btn btn-ot btn-sm" onclick="closeCreate()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.enseignants.store') }}">
            @csrf
            <div class="t-modal-body">

                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Prénom *</label><input class="inp" name="prenom" required></div>
                    <div class="fg-group"><label class="lbl">Nom *</label><input class="inp" name="nom" required></div>
                </div>

                <div class="fg3" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Sexe</label>
                        <select class="inp" name="sexe">
                            <option value="">—</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Téléphone</label><input class="inp" name="telephone" placeholder="06…"></div>
                    <div class="fg-group"><label class="lbl">Spécialité</label><input class="inp" name="specialite"></div>
                </div>

                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Type de contrat</label>
                        <select class="inp" name="type_contrat">
                            <option value="">—</option>
                            <option value="CDI">CDI</option>
                            <option value="CDD">CDD</option>
                            <option value="vacataire">Vacataire</option>
                            <option value="benevole">Bénévole</option>
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Date de recrutement</label><input class="inp" type="date" name="date_recrutement"></div>
                </div>

                <div style="border-top:1px solid var(--brd);padding-top:.875rem;margin-bottom:.875rem">
                    <div class="lbl" style="margin-bottom:.5rem">Niveaux assignés</div>
                    <div class="chk-group">
                        @foreach($niveaux as $n)
                        <label class="chk-item">
                            <input type="checkbox" name="niveaux[]" value="{{ $n->id }}">
                            {{ $n->name }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom:.875rem">
                    <div class="lbl" style="margin-bottom:.5rem">Classes assignées</div>
                    <div class="chk-group">
                        @foreach($classes as $c)
                        <label class="chk-item">
                            <input type="checkbox" name="classes[]" value="{{ $c->id }}">
                            {{ $c->name }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="border-top:1px solid var(--brd);padding-top:.875rem">
                    <div class="lbl" style="margin-bottom:.75rem;font-size:.8rem;color:var(--mist)">Compte utilisateur (optionnel)</div>
                    <div class="fg2">
                        <div class="fg-group"><label class="lbl">Email</label><input class="inp" type="email" name="email"></div>
                        <div class="fg-group"><label class="lbl">Mot de passe</label><input class="inp" type="password" name="password" placeholder="Min. 8 caractères"></div>
                    </div>
                    <div style="margin-top:.5rem">
                        <input class="inp" type="password" name="password_confirmation" placeholder="Confirmer le mot de passe">
                    </div>
                </div>

            </div>
            <div class="t-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeCreate()">Annuler</button>
                <button type="submit" class="btn btn-gold">Créer l'enseignant</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL EDIT ══ --}}
<div class="t-modal" id="modal-edit">
    <div class="t-modal-box">
        <div class="t-modal-hd">
            <h3>Modifier l'enseignant</h3>
            <button class="btn btn-ot btn-sm" onclick="closeEdit()">✕</button>
        </div>
        <form method="POST" id="edit-form">
            @csrf @method('PUT')
            <div class="t-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Prénom *</label><input class="inp" name="prenom" id="e_prenom" required></div>
                    <div class="fg-group"><label class="lbl">Nom *</label><input class="inp" name="nom" id="e_nom" required></div>
                </div>
                <div class="fg3" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Sexe</label>
                        <select class="inp" name="sexe" id="e_sexe">
                            <option value="">—</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                    </div>
                    <div class="fg-group"><label class="lbl">Téléphone</label><input class="inp" name="telephone" id="e_tel"></div>
                    <div class="fg-group"><label class="lbl">Spécialité</label><input class="inp" name="specialite" id="e_spe"></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group">
                        <label class="lbl">Contrat</label>
                        <select class="inp" name="type_contrat" id="e_contrat">
                            <option value="">—</option>
                            <option value="CDI">CDI</option>
                            <option value="CDD">CDD</option>
                            <option value="vacataire">Vacataire</option>
                            <option value="benevole">Bénévole</option>
                        </select>
                    </div>
                    <div class="fg-group">
                        <label class="lbl">Statut</label>
                        <select class="inp" name="status" id="e_status">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="t-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeEdit()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL RESET PASSWORD ══ --}}
<div class="pwd-modal" id="modal-pwd">
    <div class="pwd-box">
        <h3 id="pwd-title">Réinitialiser le mot de passe</h3>
        <form method="POST" id="pwd-form">
            @csrf
            <div class="fg-group" style="margin-bottom:.75rem">
                <label class="lbl">Nouveau mot de passe</label>
                <input class="inp" type="password" name="password" required minlength="8">
            </div>
            <div class="fg-group" style="margin-bottom:1rem">
                <label class="lbl">Confirmer</label>
                <input class="inp" type="password" name="password_confirmation" required>
            </div>
            <div style="display:flex;gap:.5rem;justify-content:flex-end">
                <button type="button" class="btn btn-ot" onclick="closePwd()">Annuler</button>
                <button type="submit" class="btn btn-gold">Réinitialiser</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Create
function openCreate() { document.getElementById('modal-create').classList.add('open'); document.body.style.overflow='hidden'; }
function closeCreate(){ document.getElementById('modal-create').classList.remove('open'); document.body.style.overflow=''; }

// Edit
function openEdit(t) {
    document.getElementById('e_prenom').value  = t.prenom ?? '';
    document.getElementById('e_nom').value     = t.nom ?? '';
    document.getElementById('e_sexe').value    = t.sexe ?? '';
    document.getElementById('e_tel').value     = t.telephone ?? '';
    document.getElementById('e_spe').value     = t.specialite ?? '';
    document.getElementById('e_contrat').value = t.type_contrat ?? '';
    document.getElementById('e_status').value  = t.status ?? '1';
    document.getElementById('edit-form').action = `/staff/enseignants/${t.id}`;
    document.getElementById('modal-edit').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeEdit() { document.getElementById('modal-edit').classList.remove('open'); document.body.style.overflow=''; }

// Password
function openPwd(id, name) {
    document.getElementById('pwd-title').textContent = `Réinitialiser : ${name}`;
    document.getElementById('pwd-form').action = `/staff/enseignants/reset-password/${id}`;
    document.getElementById('modal-pwd').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closePwd() { document.getElementById('modal-pwd').classList.remove('open'); document.body.style.overflow=''; }

// Backdrop close
['modal-create','modal-edit','modal-pwd'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e){ if(e.target===this){ this.classList.remove('open'); document.body.style.overflow=''; } });
});
</script>
@endpush