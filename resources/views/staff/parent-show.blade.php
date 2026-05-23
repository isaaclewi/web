@extends('staff.master')

@section('title', 'Fiche parent')
@section('page-title', 'Fiche parent')
@section('page-sub', 'Détails et enfants associés')

@push('styles')
<style>
.fg2{display:grid;grid-template-columns:1fr 1fr;gap:.875rem}.fg-group{display:flex;flex-direction:column;gap:.35rem}
.ps-modal{display:none;position:fixed;inset:0;z-index:500;background:rgba(8,12,20,.6);backdrop-filter:blur(4px);align-items:flex-start;justify-content:center;padding-top:3%}
.ps-modal.open{display:flex}
.ps-modal-box{background:var(--white);border-radius:16px;width:500px;max-width:95%;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .25s cubic-bezier(.4,0,.2,1) both}
@keyframes modalIn{from{transform:translateY(-16px);opacity:0}to{transform:none;opacity:1}}
.ps-modal-hd{padding:1.25rem 1.5rem;border-bottom:1px solid var(--brd);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--white);z-index:1}
.ps-modal-hd h3{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700}
.ps-modal-body{padding:1.5rem}
.ps-modal-ft{padding:1rem 1.5rem;border-top:1px solid var(--brd);display:flex;gap:.75rem;justify-content:flex-end;position:sticky;bottom:0;background:var(--white)}
.enfant-card{background:var(--white);border:1px solid var(--brd);border-radius:14px;overflow:hidden;margin-bottom:1rem}
.enfant-card-hd{padding:1rem 1.375rem;border-bottom:1px solid var(--brd);background:#fafbfd;display:flex;align-items:center;gap:.875rem}
.enfant-av{width:42px;height:42px;border-radius:10px;background:var(--night);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:.85rem;font-weight:800;color:var(--gold);flex-shrink:0}
.prog-wrap{height:5px;background:var(--bg);border-radius:3px;overflow:hidden;margin-top:.25rem}
.prog-bar{height:100%;border-radius:3px}
</style>
@endpush

@section('content')

{{-- RETOUR --}}
<div style="margin-bottom:1.5rem">
    <a href="{{ route('staff.parents') }}" class="btn btn-ot btn-sm">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour à la liste
    </a>
</div>

{{-- PROFIL HEADER --}}
<div style="background:var(--night);border-radius:16px;padding:1.75rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1.375rem;flex-wrap:wrap">
    <div style="width:64px;height:64px;border-radius:14px;background:var(--gold-dim);border:2px solid var(--gold);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:var(--gold);flex-shrink:0">
        {{ mb_substr($parent->prenom,0,1) }}{{ mb_substr($parent->nom,0,1) }}
    </div>
    <div style="flex:1;min-width:0">
        <div style="font-family:'Syne',sans-serif;font-size:1.25rem;font-weight:800;color:var(--white)">{{ $parent->prenom }} {{ $parent->nom }}</div>
        <div style="font-size:.75rem;color:var(--mist);margin-top:.2rem;display:flex;gap:1rem;flex-wrap:wrap">
            <span style="font-family:monospace">{{ $parent->matricule }}</span>
            @if($parent->telephone)<span>📞 {{ $parent->telephone }}</span>@endif
            @if($parent->email)<span>✉️ {{ $parent->email }}</span>@endif
            @if($parent->profession)<span>💼 {{ $parent->profession }}</span>@endif
        </div>
    </div>
    <div style="display:flex;gap:.625rem;flex-wrap:wrap;flex-shrink:0">
        <span class="bdg {{ $parent->status?'bdg-g':'bdg-r' }}">{{ $parent->status?'Actif':'Inactif' }}</span>
        @if($parent->user_id)
        <span class="bdg bdg-b">✓ Compte actif</span>
        @endif
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;flex-shrink:0">
        <button class="btn btn-gold btn-sm" onclick="openEdit()">Modifier</button>
        @if($parent->user_id)
        <button class="btn btn-ot btn-sm" onclick="openPwd()">Reset MDP</button>
        @endif
        <form method="POST" action="{{ route('staff.parents.destroy',$parent) }}">
            @csrf @method('DELETE')
            <button class="btn btn-err btn-sm" onclick="return confirm('Supprimer ce parent et son compte ?')">Supprimer</button>
        </form>
    </div>
</div>

{{-- INFOS + ENFANTS --}}
<div style="display:grid;grid-template-columns:300px 1fr;gap:1.25rem;align-items:start">

{{-- SIDEBAR INFO --}}
<div style="display:flex;flex-direction:column;gap:1rem">

    <div class="s-card">
        <div class="s-card-hd"><h3>Informations</h3></div>
        <div class="s-card-body" style="padding:.75rem 1.375rem">
            @foreach([
                ['Prénom', $parent->prenom],
                ['Nom', $parent->nom],
                ['Matricule', $parent->matricule],
                ['Sexe', $parent->sexe ?? '—'],
                ['Téléphone', $parent->telephone ?? '—'],
                ['Email', $parent->email ?? '—'],
                ['Profession', $parent->profession ?? '—'],
                ['Adresse', $parent->adresse ?? '—'],
            ] as [$lbl, $val])
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.45rem 0;border-bottom:1px solid var(--brd)">
                <span style="font-size:.75rem;color:var(--mist);flex-shrink:0">{{ $lbl }}</span>
                <span style="font-size:.78rem;font-weight:500;color:var(--night);text-align:right;word-break:break-all;max-width:160px">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Affecter un enfant --}}
    <div class="s-card">
        <div class="s-card-hd"><h3>Affecter un enfant</h3></div>
        <div class="s-card-body">
            <form method="POST" action="{{ route('staff.parents.affect') }}">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                <div class="fg-group" style="margin-bottom:.75rem">
                    <label class="lbl">Apprenant</label>
                    <select class="inp" name="apprenant_id" required>
                        <option value="">Sélectionner…</option>
                        @foreach($parent->apprenants->pluck('id') as $exId)@endforeach
                        {{-- On ne peut pas filtrer facilement ici sans la liste complète --}}
                        @if($parent->apprenants->count())
                        @foreach($parent->apprenants as $a)
                        {{-- Already linked --}}
                        @endforeach
                        @endif
                        <optgroup label="Apprenants de l'établissement">
                            @foreach(\App\Models\Apprenant::where('institution_id',$institution->id)->orderBy('nom')->get() as $a)
                            @if(!$parent->apprenants->contains('id',$a->id))
                            <option value="{{ $a->id }}">{{ $a->prenom }} {{ $a->nom }} ({{ $a->classe?->name ?? '—' }})</option>
                            @endif
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="fg-group" style="margin-bottom:1rem">
                    <label class="lbl">Lien</label>
                    <select class="inp" name="lien">
                        <option value="pere">Père</option>
                        <option value="mere">Mère</option>
                        <option value="tuteur" selected>Tuteur/Tutrice</option>
                    </select>
                </div>
                <button class="btn btn-gold" style="width:100%">Affecter</button>
            </form>
        </div>
    </div>
</div>

{{-- ENFANTS --}}
<div>
    <h3 style="font-family:'Syne',sans-serif;font-size:.9rem;font-weight:700;margin-bottom:1rem">
        {{ $parent->apprenants->count() }} enfant(s) suivi(s)
    </h3>

    @forelse($parent->apprenants as $apprenant)
    @php
        $records = $apprenant->financialRecords ?? collect();
        $paye    = $records->sum('montant_paye');
        $reste   = $records->sum('montant_reste');
        $du      = $records->sum('montant_du');
        $pct     = $du > 0 ? min(100,round($paye/$du*100)) : 0;
        $pivot   = $apprenant->pivot ?? null;
    @endphp
    <div class="enfant-card">
        <div class="enfant-card-hd">
            <div class="enfant-av">{{ mb_substr($apprenant->prenom,0,1) }}{{ mb_substr($apprenant->nom,0,1) }}</div>
            <div style="flex:1;min-width:0">
                <div style="font-weight:700;font-size:.9rem;color:var(--night)">{{ $apprenant->prenom }} {{ $apprenant->nom }}</div>
                <div style="font-size:.72rem;color:var(--mist);margin-top:.1rem">
                    <span style="font-family:monospace">{{ $apprenant->matricule }}</span>
                    · {{ $apprenant->classe?->name ?? '—' }}
                    · {{ $apprenant->niveau?->name ?? '—' }}
                    @if($apprenant->filiere) · {{ $apprenant->filiere->name }}@endif
                </div>
            </div>
            @if($pivot && $pivot->lien)
            <span class="bdg bdg-b">{{ ucfirst($pivot->lien) }}</span>
            @endif
            <div style="display:flex;gap:.375rem;flex-shrink:0">
                <a href="{{ route('staff.finances.show',$apprenant->id) }}" class="btn btn-ot btn-sm">Finances</a>
                <a href="{{ route('staff.disciplinaire.apprenant',$apprenant) }}" class="btn btn-ot btn-sm">Discipline</a>
                <form method="POST" action="{{ route('staff.parents.detach') }}">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                    <input type="hidden" name="apprenant_id" value="{{ $apprenant->id }}">
                    <button class="btn btn-err btn-sm" onclick="return confirm('Détacher cet enfant ?')">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:11px;height:11px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101"/></svg>
                        Détacher
                    </button>
                </form>
            </div>
        </div>

        {{-- Résumé financier --}}
        @if($records->count())
        <div style="padding:.875rem 1.375rem">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-bottom:.75rem">
                <div style="text-align:center;padding:.5rem;background:var(--bg);border-radius:8px">
                    <div style="font-family:'Syne',sans-serif;font-size:.9rem;font-weight:800">{{ number_format($du,0,' ',' ') }}</div>
                    <div style="font-size:.65rem;color:var(--mist)">Dû (FCFA)</div>
                </div>
                <div style="text-align:center;padding:.5rem;background:var(--ok-l);border-radius:8px">
                    <div style="font-family:'Syne',sans-serif;font-size:.9rem;font-weight:800;color:var(--ok)">{{ number_format($paye,0,' ',' ') }}</div>
                    <div style="font-size:.65rem;color:#065f46">Payé</div>
                </div>
                <div style="text-align:center;padding:.5rem;background:{{ $reste>0?'var(--err-l)':'var(--bg)' }};border-radius:8px">
                    <div style="font-family:'Syne',sans-serif;font-size:.9rem;font-weight:800;color:{{ $reste>0?'var(--err)':'var(--mist)' }}">{{ number_format($reste,0,' ',' ') }}</div>
                    <div style="font-size:.65rem;color:{{ $reste>0?'#7f1d1d':'var(--mist)' }}">Reste</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:.75rem">
                <div class="prog-wrap" style="flex:1">
                    <div class="prog-bar" style="width:{{ $pct }}%;background:{{ $pct>=100?'var(--ok)':($pct>=50?'var(--warn)':'var(--err)') }}"></div>
                </div>
                <span style="font-size:.72rem;font-weight:700;color:var(--mist)">{{ $pct }}%</span>
                <span class="bdg {{ $reste<=0&&$du>0?'bdg-g':($paye>0?'bdg-a':'bdg-r') }}" style="font-size:.65rem">
                    {{ $reste<=0&&$du>0?'Soldé':($paye>0?'Partiel':'Impayé') }}
                </span>
            </div>
        </div>
        @else
        <div style="padding:.875rem 1.375rem;font-size:.78rem;color:var(--mist)">
            Aucun enregistrement financier pour cette année.
        </div>
        @endif
    </div>
    @empty
    <div class="s-empty">
        <div class="s-empty-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <h4>Aucun enfant lié</h4>
        <p>Utilisez le formulaire à gauche pour affecter un apprenant.</p>
    </div>
    @endforelse
</div>
</div>


{{-- MODAL EDIT --}}
<div class="ps-modal" id="modal-edit">
    <div class="ps-modal-box">
        <div class="ps-modal-hd">
            <h3>Modifier le parent</h3>
            <button class="btn btn-ot btn-sm" onclick="closeEdit()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.parents.update',$parent) }}">
            @csrf @method('PUT')
            <div class="ps-modal-body">
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Prénom *</label><input class="inp" name="prenom" value="{{ $parent->prenom }}" required></div>
                    <div class="fg-group"><label class="lbl">Nom *</label><input class="inp" name="nom" value="{{ $parent->nom }}" required></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Téléphone</label><input class="inp" name="telephone" value="{{ $parent->telephone }}"></div>
                    <div class="fg-group"><label class="lbl">Email</label><input class="inp" type="email" name="email" value="{{ $parent->email }}"></div>
                </div>
                <div class="fg2" style="margin-bottom:.875rem">
                    <div class="fg-group"><label class="lbl">Profession</label><input class="inp" name="profession" value="{{ $parent->profession }}"></div>
                    <div class="fg-group">
                        <label class="lbl">Statut</label>
                        <select class="inp" name="status">
                            <option value="1" @selected($parent->status)>Actif</option>
                            <option value="0" @selected(!$parent->status)>Inactif</option>
                        </select>
                    </div>
                </div>
                <div class="fg-group">
                    <label class="lbl">Adresse</label>
                    <input class="inp" name="adresse" value="{{ $parent->adresse }}">
                </div>
            </div>
            <div class="ps-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeEdit()">Annuler</button>
                <button type="submit" class="btn btn-gold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RESET MDP --}}
<div class="ps-modal" id="modal-pwd" style="align-items:center;padding-top:0">
    <div class="ps-modal-box" style="max-width:400px">
        <div class="ps-modal-hd">
            <h3>Réinitialiser le mot de passe</h3>
            <button class="btn btn-ot btn-sm" onclick="closePwd()">✕</button>
        </div>
        <form method="POST" action="{{ route('staff.parents.reset',$parent) }}">
            @csrf
            <div class="ps-modal-body">
                <div class="fg-group" style="margin-bottom:.75rem"><label class="lbl">Nouveau mot de passe</label><input class="inp" type="password" name="password" required minlength="8"></div>
                <div class="fg-group"><label class="lbl">Confirmer</label><input class="inp" type="password" name="password_confirmation" required></div>
            </div>
            <div class="ps-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closePwd()">Annuler</button>
                <button type="submit" class="btn btn-gold">Réinitialiser</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEdit(){ document.getElementById('modal-edit').classList.add('open'); document.body.style.overflow='hidden'; }
function closeEdit(){ document.getElementById('modal-edit').classList.remove('open'); document.body.style.overflow=''; }
function openPwd(){ document.getElementById('modal-pwd').classList.add('open'); document.body.style.overflow='hidden'; }
function closePwd(){ document.getElementById('modal-pwd').classList.remove('open'); document.body.style.overflow=''; }
['modal-edit','modal-pwd'].forEach(id=>{
    document.getElementById(id).addEventListener('click',function(e){ if(e.target===this){this.classList.remove('open');document.body.style.overflow='';} });
});
</script>
@endpush
