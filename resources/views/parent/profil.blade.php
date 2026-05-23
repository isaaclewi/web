@extends('parent.master')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')
@section('page-sub', 'Informations personnelles et gestion du compte')

@section('content')
<style>
    .prof-grid {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 900px) { .prof-grid { grid-template-columns: 1fr; } }

    .pcard {
        background: white;
        border: 1px solid var(--border, #e4e8f2);
        border-radius: 14px;
        overflow: hidden;
    }
    .pcard-head {
        padding: 1rem 1.375rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: .625rem;
    }
    .pcard-title { font-size: .875rem; font-weight: 700; color: #111827; }
    .pcard-sub   { font-size: .7rem; color: #6b7280; margin-top: .1rem; }
    .pcard-body  { padding: 1.375rem; }

    /* ── Avatar ── */
    .avatar-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 1.75rem 1.375rem;
        border-bottom: 1px solid #f3f4f6;
        text-align: center;
    }
    .avatar-ring { position: relative; width: 100px; height: 100px; }
    .avatar-img {
        width: 100px; height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #0c0f1a;
        box-shadow: 0 4px 16px rgba(0,0,0,.15);
    }
    .avatar-edit-btn {
        position: absolute; bottom: 2px; right: 2px;
        width: 30px; height: 30px;
        border-radius: 50%;
        background: #0c0f1a; color: white;
        border: 2px solid white;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: .85rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.2);
        transition: background .15s;
    }
    .avatar-edit-btn:hover { background: #374151; }

    .avatar-name  { font-size: 1rem; font-weight: 700; color: #111827; }
    .avatar-role  { font-size: .72rem; color: #6b7280; }
    .avatar-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        background: #d1fae5; color: #065f46;
        font-size: .68rem; font-weight: 700;
        padding: .25rem .75rem; border-radius: 99px;
    }

    /* ── Stat chips ── */
    .stat-chips { display: grid; grid-template-columns: 1fr 1fr; gap: .625rem; padding: 1rem 1.375rem; }
    .stat-chip {
        background: #f9fafb; border: 1px solid #e5e7eb;
        border-radius: .5rem; padding: .75rem; text-align: center;
    }
    .stat-chip-val { font-size: 1.25rem; font-weight: 700; color: #111827; }
    .stat-chip-lbl { font-size: .63rem; color: #6b7280; margin-top: .1rem; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }

    /* ── Enfants list ── */
    .enfant-row {
        display: flex; align-items: center; gap: .75rem;
        padding: .75rem 0; border-bottom: 1px solid #f9fafb;
    }
    .enfant-row:last-child { border-bottom: none; }
    .enfant-avatar {
        width: 36px; height: 36px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem; color: white; flex-shrink: 0;
    }

    /* ── Forms ── */
    .f-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .875rem; }
    @media (max-width: 600px) { .f-grid { grid-template-columns: 1fr; } }
    .f-full { grid-column: span 2; }
    @media (max-width: 600px) { .f-full { grid-column: span 1; } }

    .f-label {
        font-size: .68rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: .05em;
        display: block; margin-bottom: .3rem;
    }
    .f-field {
        background: #f9fafb; border: 1px solid #e5e7eb;
        border-radius: .5rem; padding: .575rem .875rem;
        font-size: .875rem; color: #374151;
        width: 100%; outline: none; transition: all .2s; font-family: inherit;
    }
    .f-field:focus { border-color: #0c0f1a; background: white; box-shadow: 0 0 0 3px rgba(12,15,26,.07); }

    .btn-save {
        background: #0c0f1a; color: white;
        font-weight: 700; border: none; padding: .6rem 1.5rem;
        border-radius: .5rem; cursor: pointer; font-size: .8rem;
        font-family: inherit; transition: all .2s;
        display: inline-flex; align-items: center; gap: .4rem;
    }
    .btn-save:hover { background: #374151; transform: translateY(-1px); }

    /* ── Tabs ── */
    .tabs { display: flex; gap: .375rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
    .tab-btn {
        padding: .475rem 1rem; border-radius: .5rem;
        font-size: .78rem; font-weight: 600; border: 1px solid #e5e7eb;
        background: white; color: #6b7280; cursor: pointer; transition: all .15s; font-family: inherit;
    }
    .tab-btn.active   { background: #0c0f1a; color: white; border-color: #0c0f1a; }
    .tab-btn:hover:not(.active) { background: #f3f4f6; color: #111827; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ── Flash ── */
    .flash-ok {
        background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46;
        border-radius: .5rem; padding: .75rem 1rem; font-size: .8rem;
        margin-bottom: 1.25rem; display: flex; align-items: center; gap: .5rem;
    }
    .flash-err {
        background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b;
        border-radius: .5rem; padding: .75rem 1rem; font-size: .8rem; margin-bottom: 1.25rem;
    }

    /* ── Section divider ── */
    .sdiv { display: flex; align-items: center; gap: .75rem; margin: 1.25rem 0 1rem; }
    .sdiv-line { flex: 1; height: 1px; background: #f3f4f6; }
    .sdiv-lbl { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #9ca3af; }

    #avatarFileInput { display: none; }
</style>

{{-- Flash --}}
@if(session('success'))
    <div class="flash-ok">
        <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="flash-err">
        ⚠️
        @foreach($errors->all() as $err)
            <div style="margin-top:.2rem;">{{ $err }}</div>
        @endforeach
    </div>
@endif

<div style="margin-bottom:1.25rem;">
    <h1 style="font-size:1.125rem;font-weight:700;color:#111827;">Mon Profil</h1>
    <p style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">Gérez vos informations et votre photo de profil</p>
</div>

<div class="prof-grid">

    {{-- ── COLONNE GAUCHE ── --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Avatar card --}}
        <div class="pcard">
            <div class="avatar-wrap">

                {{-- Formulaire upload avatar → root_storage --}}
                <form method="POST" action="{{ route('parent.profil.avatar') }}"
                      enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    <input type="file" id="avatarFileInput" name="avatar"
                           accept=".jpg,.jpeg,.png,.webp"
                           onchange="document.getElementById('avatarForm').submit()">
                </form>

                <div class="avatar-ring">
                    @php
                        $avatarUrl = $parent?->photo
                            ? asset('storage/' . $parent->photo)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0c0f1a&color=fff&size=100&font-size=0.4';
                    @endphp
                    <img id="avatarPreview" src="{{ $avatarUrl }}" alt="Avatar" class="avatar-img">
                    <label for="avatarFileInput" class="avatar-edit-btn" title="Changer la photo">📷</label>
                </div>

                <div>
                    <div class="avatar-name">{{ $user->name }}</div>
                    <div class="avatar-role" style="margin-top:.2rem;">Parent d'élève</div>
                    @if($parent?->matricule)
                        <div style="margin-top:.25rem;font-family:monospace;font-size:.7rem;color:#9ca3af;">{{ $parent->matricule }}</div>
                    @endif
                    <div style="margin-top:.625rem;">
                        <span class="avatar-badge">✅ Compte actif</span>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="stat-chips">
                <div class="stat-chip">
                    <div class="stat-chip-val">{{ $parent?->apprenants?->count() ?? 0 }}</div>
                    <div class="stat-chip-lbl">Enfants</div>
                </div>
                <div class="stat-chip">
                    <div class="stat-chip-val">
                        {{ $parent?->apprenants?->filter(fn($a) => $a->status == 1)->count() ?? 0 }}
                    </div>
                    <div class="stat-chip-lbl">Actifs</div>
                </div>
            </div>
        </div>

        {{-- Mes enfants --}}
        @if($parent?->apprenants?->count())
        <div class="pcard">
            <div class="pcard-head">
                <div>
                    <div class="pcard-title">Mes enfants</div>
                    <div class="pcard-sub">Apprenants rattachés à votre compte</div>
                </div>
            </div>
            <div class="pcard-body" style="padding:.875rem 1.375rem;">
                @php $colors = ['#6366f1','#0891b2','#059669','#d97706','#dc2626']; @endphp
                @foreach($parent->apprenants as $idx => $enf)
                    <div class="enfant-row">
                        <div class="enfant-avatar" style="background:{{ $colors[$idx % count($colors)] }}">
                            {{ strtoupper(mb_substr($enf->prenom,0,1).mb_substr($enf->nom,0,1)) }}
                        </div>
                        <div style="min-width:0;flex:1;">
                            <div style="font-size:.85rem;font-weight:600;color:#111827;">{{ $enf->prenom }} {{ $enf->nom }}</div>
                            <div style="font-size:.72rem;color:#6b7280;">
                                {{ $enf->classe?->name ?? 'Sans classe' }}
                                @if($enf->niveau) · {{ $enf->niveau->name }} @endif
                            </div>
                        </div>
                        <span style="font-size:.68rem;padding:.15rem .5rem;border-radius:4px;{{ $enf->status == 1 ? 'background:#d1fae5;color:#065f46' : 'background:#fee2e2;color:#991b1b' }}">
                            {{ $enf->status == 1 ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Infos établissement --}}
        @if($institution)
        <div class="pcard">
            <div class="pcard-head">
                <div>
                    <div class="pcard-title">Établissement</div>
                    <div class="pcard-sub">{{ $institution->academic_year ?? '' }}</div>
                </div>
            </div>
            <div class="pcard-body" style="font-size:.8rem;color:#374151;">
                <div style="font-weight:700;color:#111827;">{{ $institution->name }}</div>
                @if($institution->commune || $institution->departement)
                    <div style="color:#6b7280;margin-top:.25rem;">{{ $institution->commune }}{{ $institution->departement ? ', '.$institution->departement : '' }}</div>
                @endif
                @if($institution->telephone)
                    <div style="margin-top:.35rem;">📞 {{ $institution->telephone }}</div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- ── COLONNE DROITE ── --}}
    <div>
        {{-- Tabs --}}
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('infos', this)">👤 Informations</button>
            <button class="tab-btn" onclick="switchTab('password', this)">🔑 Mot de passe</button>
            <button class="tab-btn" onclick="switchTab('securite', this)">🛡 Sécurité</button>
        </div>

        {{-- ── TAB INFOS ── --}}
        <div class="tab-panel active" id="tab-infos">
            <div class="pcard">
                <div class="pcard-head">
                    <div>
                        <div class="pcard-title">Informations personnelles</div>
                        <div class="pcard-sub">Modifiez vos coordonnées</div>
                    </div>
                </div>
                <div class="pcard-body">
                    <form method="POST" action="{{ route('parent.profil.infos') }}">
                        @csrf @method('PATCH')

                        <div class="sdiv">
                            <span class="sdiv-lbl">Compte utilisateur</span>
                            <div class="sdiv-line"></div>
                        </div>

                        <div class="f-grid">
                            <div class="f-full">
                                <label class="f-label">Nom complet *</label>
                                <input type="text" name="name" class="f-field"
                                       value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div>
                                <label class="f-label">Email *</label>
                                <input type="email" name="email" class="f-field"
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div>
                                <label class="f-label">Téléphone</label>
                                <input type="tel" name="telephone" class="f-field"
                                       value="{{ old('telephone', $parent?->telephone) }}"
                                       placeholder="+242 06 000 0000">
                            </div>
                        </div>

                        @if($parent)
                        <div class="sdiv" style="margin-top:1.5rem;">
                            <span class="sdiv-lbl">Profil parent</span>
                            <div class="sdiv-line"></div>
                        </div>

                        <div class="f-grid">
                            <div>
                                <label class="f-label">Prénom</label>
                                <input type="text" name="prenom" class="f-field"
                                       value="{{ old('prenom', $parent->prenom) }}">
                            </div>
                            <div>
                                <label class="f-label">Nom de famille</label>
                                <input type="text" name="nom" class="f-field"
                                       value="{{ old('nom', $parent->nom) }}">
                            </div>
                            <div>
                                <label class="f-label">Profession</label>
                                <input type="text" name="profession" class="f-field"
                                       value="{{ old('profession', $parent->profession) }}"
                                       placeholder="Ex : Ingénieur">
                            </div>
                            <div>
                                <label class="f-label">Genre</label>
                                <select name="sexe" class="f-field">
                                    <option value="">— Sélectionner —</option>
                                    <option value="M" {{ $parent->sexe === 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ $parent->sexe === 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                            </div>
                            <div class="f-full">
                                <label class="f-label">Adresse</label>
                                <input type="text" name="adresse" class="f-field"
                                       value="{{ old('adresse', $parent->adresse) }}"
                                       placeholder="Adresse domicile">
                            </div>
                        </div>

                        {{-- Infos lecture seule --}}
                        <div class="sdiv" style="margin-top:1.5rem;">
                            <span class="sdiv-lbl">Informations système</span>
                            <div class="sdiv-line"></div>
                        </div>
                        <div class="f-grid">
                            @if($parent->matricule)
                            <div>
                                <label class="f-label">Matricule</label>
                                <input type="text" class="f-field" disabled
                                       value="{{ $parent->matricule }}"
                                       style="background:#f3f4f6;color:#6b7280;cursor:not-allowed;font-family:monospace;">
                            </div>
                            @endif
                            <div>
                                <label class="f-label">Compte créé le</label>
                                <input type="text" class="f-field" disabled
                                       value="{{ $user->created_at?->format('d/m/Y') ?? '—' }}"
                                       style="background:#f3f4f6;color:#6b7280;cursor:not-allowed;">
                            </div>
                        </div>
                        @endif

                        <div style="margin-top:1.5rem;display:flex;justify-content:flex-end;">
                            <button type="submit" class="btn-save">
                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── TAB MOT DE PASSE ── --}}
        <div class="tab-panel" id="tab-password">
            <div class="pcard">
                <div class="pcard-head">
                    <div>
                        <div class="pcard-title">Changer le mot de passe</div>
                        <div class="pcard-sub">Utilisez un mot de passe fort et unique</div>
                    </div>
                </div>
                <div class="pcard-body">
                    <form method="POST" action="{{ route('parent.profil.password') }}">
                        @csrf @method('PATCH')

                        <div style="display:flex;flex-direction:column;gap:1rem;max-width:480px;">
                            <div>
                                <label class="f-label">Mot de passe actuel *</label>
                                <input type="password" name="current_password" class="f-field" required>
                                @error('current_password')<p style="color:#dc2626;font-size:.72rem;margin-top:.25rem;">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="f-label">Nouveau mot de passe *</label>
                                <input type="password" name="password" class="f-field" required minlength="8">
                                @error('password')<p style="color:#dc2626;font-size:.72rem;margin-top:.25rem;">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="f-label">Confirmer le nouveau mot de passe *</label>
                                <input type="password" name="password_confirmation" class="f-field" required>
                            </div>
                            <div style="display:flex;justify-content:flex-end;">
                                <button type="submit" class="btn-save">🔑 Changer le mot de passe</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── TAB SÉCURITÉ ── --}}
        <div class="tab-panel" id="tab-securite">
            <div class="pcard">
                <div class="pcard-head">
                    <div>
                        <div class="pcard-title">Sécurité & accès</div>
                        <div class="pcard-sub">Informations sur votre session</div>
                    </div>
                </div>
                <div class="pcard-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;margin-bottom:1.5rem;">
                        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:.5rem;padding:1rem;">
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em;">Email</div>
                            <div style="font-size:.825rem;font-weight:600;color:#111827;margin-top:.25rem;">{{ $user->email }}</div>
                        </div>
                        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:.5rem;padding:1rem;">
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em;">Statut</div>
                            <div style="margin-top:.25rem;">
                                <span style="background:#d1fae5;color:#065f46;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:99px;">✅ Actif</span>
                            </div>
                        </div>
                        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:.5rem;padding:1rem;">
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em;">Membre depuis</div>
                            <div style="font-size:.825rem;font-weight:600;color:#111827;margin-top:.25rem;">
                                {{ $user->created_at?->locale('fr')->diffForHumans() ?? '—' }}
                            </div>
                        </div>
                        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:.5rem;padding:1rem;">
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em;">Rôle</div>
                            <div style="margin-top:.25rem;">
                                <span style="background:#dbeafe;color:#1e40af;font-size:.68rem;font-weight:700;padding:.15rem .5rem;border-radius:4px;">Parent</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.25rem;background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;border-radius:.5rem;cursor:pointer;font-size:.8rem;font-weight:600;font-family:inherit;">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- fin colonne droite --}}
</div>

<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

// Aperçu avatar avant envoi
document.getElementById('avatarFileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = evt => { document.getElementById('avatarPreview').src = evt.target.result; };
        reader.readAsDataURL(file);
    }
});

@if($errors->has('current_password') || $errors->has('password'))
    switchTab('password', document.querySelectorAll('.tab-btn')[1]);
@endif
</script>

@endsection
