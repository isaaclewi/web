@extends('admin.master')

@section('title', 'Mon Profil — ' . ($institution->name ?? 'EduAdmin'))

@push('styles')
<style>
    /* ── Palette & tokens ── */
    :root {
        --ink:     #111827;
        --ink-mid: #374151;
        --muted:   #6b7280;
        --border:  #e5e7eb;
        --bg:      #f8f9fa;
        --radius:  .75rem;
        --accent:  #1f2937;
    }

    /* ── Layout deux colonnes ── */
    .profil-grid {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 1.25rem;
        align-items: start;
    }

    @media (max-width: 900px)  { .profil-grid { grid-template-columns: 1fr; } }

    /* ── Card générique ── */
    .pcard {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .pcard-head {
        padding: 1rem 1.375rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: .625rem;
    }

    .pcard-head-icon {
        width: 32px; height: 32px;
        border-radius: .5rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }

    .pcard-title { font-size: .875rem; font-weight: 700; color: var(--ink); }
    .pcard-sub   { font-size: .7rem;   color: var(--muted); margin-top: .1rem; }
    .pcard-body  { padding: 1.375rem; }

    /* ── Avatar card ── */
    .avatar-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 1.75rem 1.375rem;
        border-bottom: 1px solid #f3f4f6;
        text-align: center;
    }

    .avatar-ring {
        position: relative;
        width: 100px; height: 100px;
    }

    .avatar-img {
        width: 100px; height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--accent);
        box-shadow: 0 4px 16px rgba(31,41,55,.15);
    }

    .avatar-edit-btn {
        position: absolute;
        bottom: 2px; right: 2px;
        width: 28px; height: 28px;
        border-radius: 50%;
        background: var(--accent);
        color: white;
        border: 2px solid white;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: .8rem;
        transition: background .15s;
        box-shadow: 0 2px 8px rgba(0,0,0,.2);
    }

    .avatar-edit-btn:hover { background: #374151; }

    .avatar-name  { font-size: 1rem;  font-weight: 700; color: var(--ink); }
    .avatar-role  { font-size: .72rem; color: var(--muted); }
    .avatar-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        background: #d1fae5; color: #065f46;
        font-size: .68rem; font-weight: 700;
        padding: .25rem .75rem; border-radius: 99px;
        letter-spacing: .04em;
    }

    /* ── Stat chips dans la sidebar ── */
    .stat-chips {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .625rem;
        padding: 1rem 1.375rem;
    }

    .stat-chip {
        background: #f9fafb;
        border: 1px solid var(--border);
        border-radius: .5rem;
        padding: .75rem;
        text-align: center;
    }

    .stat-chip-val { font-size: 1.25rem; font-weight: 700; color: var(--ink); }
    .stat-chip-lbl { font-size: .63rem; color: var(--muted); margin-top: .1rem; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }

    /* ── Institution info ── */
    .inst-row {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .75rem 0;
        border-bottom: 1px solid #f9fafb;
    }
    .inst-row:last-child { border-bottom: none; padding-bottom: 0; }

    .inst-row-icon {
        width: 28px; height: 28px; border-radius: .375rem;
        background: #f3f4f6;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; flex-shrink: 0; margin-top: .1rem;
    }

    .inst-row-label { font-size: .65rem; text-transform: uppercase; font-weight: 700; color: #9ca3af; letter-spacing: .05em; }
    .inst-row-val   { font-size: .8125rem; font-weight: 500; color: var(--ink); margin-top: .1rem; }

    /* ── Formulaires ── */
    .f-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .875rem; }
    @media (max-width: 600px) { .f-grid { grid-template-columns: 1fr; } }

    .f-full  { grid-column: span 2; }
    @media (max-width: 600px) { .f-full { grid-column: span 1; } }

    .f-label {
        font-size: .68rem; font-weight: 700; color: var(--muted);
        text-transform: uppercase; letter-spacing: .05em;
        display: block; margin-bottom: .3rem;
    }

    .f-field {
        background: #f9fafb; border: 1px solid var(--border);
        border-radius: .5rem; padding: .575rem .875rem;
        font-size: .875rem; color: var(--ink-mid);
        width: 100%; outline: none; transition: all .2s;
        font-family: inherit;
    }

    .f-field:focus {
        border-color: var(--accent);
        background: white;
        box-shadow: 0 0 0 3px rgba(31,41,55,.07);
    }

    .f-field::placeholder { color: #d1d5db; }

    .f-field.is-error { border-color: #ef4444; background: #fef2f2; }

    .f-hint { font-size: .68rem; color: var(--muted); margin-top: .25rem; }
    .f-err  { font-size: .7rem;  color: #dc2626;    margin-top: .25rem; }

    /* Password strength */
    .pw-strength { display: flex; gap: .25rem; margin-top: .375rem; }
    .pw-bar {
        flex: 1; height: 3px; border-radius: 99px;
        background: #e5e7eb; transition: background .3s;
    }
    .pw-bar.active-weak   { background: #ef4444; }
    .pw-bar.active-medium { background: #f59e0b; }
    .pw-bar.active-strong { background: #10b981; }
    .pw-label { font-size: .68rem; margin-top: .2rem; }

    /* ── Boutons ── */
    .btn-save {
        background: var(--accent); color: white;
        font-weight: 700; border: none; padding: .6rem 1.5rem;
        border-radius: .5rem; cursor: pointer; font-size: .8rem;
        font-family: inherit; transition: all .2s; display: inline-flex;
        align-items: center; gap: .4rem;
    }
    .btn-save:hover { background: #374151; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(31,41,55,.2); }

    .btn-danger {
        background: #fee2e2; color: #991b1b;
        font-weight: 600; border: 1px solid #fca5a5; padding: .6rem 1.25rem;
        border-radius: .5rem; cursor: pointer; font-size: .8rem;
        font-family: inherit; transition: all .2s; display: inline-flex;
        align-items: center; gap: .4rem;
    }
    .btn-danger:hover { background: #fecaca; }

    /* ── Tabs ── */
    .tabs { display: flex; gap: .375rem; margin-bottom: 1.25rem; flex-wrap: wrap; }
    .tab-btn {
        padding: .475rem 1rem; border-radius: .5rem;
        font-size: .78rem; font-weight: 600; border: 1px solid var(--border);
        background: white; color: var(--muted); cursor: pointer; transition: all .15s;
        font-family: inherit;
    }
    .tab-btn.active   { background: var(--accent); color: white; border-color: var(--accent); }
    .tab-btn:hover:not(.active) { background: #f3f4f6; color: var(--ink); }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ── Flash alerts ── */
    .flash-ok  {
        background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46;
        border-radius: .5rem; padding: .75rem 1rem;
        font-size: .8rem; margin-bottom: 1.25rem;
        display: flex; align-items: center; gap: .5rem;
    }
    .flash-err {
        background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b;
        border-radius: .5rem; padding: .75rem 1rem;
        font-size: .8rem; margin-bottom: 1.25rem;
    }

    /* ── Section divider ── */
    .section-divider {
        display: flex; align-items: center; gap: .75rem;
        margin: 1.25rem 0 1rem;
    }
    .section-divider-line { flex: 1; height: 1px; background: #f3f4f6; }
    .section-divider-label {
        font-size: .65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; color: #9ca3af;
    }

    /* ── Avatar upload zone ── */
    #avatarFileInput { display: none; }

    /* ── Activity log item ── */
    .log-item {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: .75rem 0; border-bottom: 1px solid #f9fafb;
    }
    .log-item:last-child { border-bottom: none; padding-bottom: 0; }
    .log-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--accent); flex-shrink: 0; margin-top: .35rem;
    }
    .log-text { font-size: .78rem; color: var(--ink-mid); line-height: 1.5; }
    .log-date { font-size: .66rem; color: var(--muted); margin-top: .1rem; }
</style>
@endpush

@section('content')

{{-- Flash messages --}}
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

{{-- Titre page --}}
<div style="margin-bottom:1.25rem;">
    <h1 style="font-size:1.125rem;font-weight:700;color:#111827;">Mon Profil</h1>
    <p style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">Gérez vos informations personnelles et vos accès</p>
</div>

<div class="profil-grid">

    {{-- ═══════════════════════════════════
         COLONNE GAUCHE — carte identité
    ═══════════════════════════════════ --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">

        {{-- Avatar + identité --}}
        <div class="pcard">
            <div class="avatar-wrap">
                {{-- Formulaire avatar invisible --}}
                <form method="POST" action="{{ route('admin.profil.avatar') }}"
                      enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    <input type="file" id="avatarFileInput" name="avatar"
                           accept=".jpg,.jpeg,.png,.webp"
                           onchange="document.getElementById('avatarForm').submit()">
                </form>

                <div class="avatar-ring">
                    @php use Illuminate\Support\Facades\Storage; @endphp

<img id="avatarPreview"
     src="{{ $user->avatar 
        ? Storage::disk('root_storage')->url($user->avatar) 
        : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1f2937&color=fff&size=100&font-size=0.4' }}"
     alt="Avatar"
     class="avatar-img">
                    <label for="avatarFileInput" class="avatar-edit-btn" title="Changer la photo">
                        📷
                    </label>
                </div>

                <div>
                    <div class="avatar-name">{{ $user->name }}</div>
                    <div class="avatar-role" style="margin-top:.2rem;">
                        {{ $staff?->poste ?? 'Directeur' }}
                        @if($staff?->matricule)
                            · <span style="font-family:monospace;font-size:.7rem;">{{ $staff->matricule }}</span>
                        @endif
                    </div>
                    <div style="margin-top:.625rem;">
                        <span class="avatar-badge">✅ Compte actif</span>
                    </div>
                </div>
            </div>

            {{-- Stats rapides --}}
            <div class="stat-chips">
                <div class="stat-chip">
                    <div class="stat-chip-val">{{ $stats['apprenants'] ?? 0 }}</div>
                    <div class="stat-chip-lbl">Élèves</div>
                </div>
                <div class="stat-chip">
                    <div class="stat-chip-val">{{ $stats['teachers'] ?? 0 }}</div>
                    <div class="stat-chip-lbl">Enseignants</div>
                </div>
                <div class="stat-chip">
                    <div class="stat-chip-val">{{ $stats['classes'] ?? 0 }}</div>
                    <div class="stat-chip-lbl">Classes</div>
                </div>
                <div class="stat-chip">
                    <div class="stat-chip-val">{{ $stats['staff'] ?? 0 }}</div>
                    <div class="stat-chip-lbl">Personnel</div>
                </div>
            </div>
        </div>

        {{-- Infos institution --}}
        @if($institution)
        <div class="pcard">
            <div class="pcard-head">
                <div class="pcard-head-icon" style="background:#f0fdf4;">🏫</div>
                <div>
                    <div class="pcard-title">Mon Établissement</div>
                    <div class="pcard-sub">Informations de l'institution</div>
                </div>
            </div>
            <div class="pcard-body" style="padding:1rem 1.375rem;">
                <div class="inst-row">
                    <div class="inst-row-icon">🏷</div>
                    <div>
                        <div class="inst-row-label">Nom</div>
                        <div class="inst-row-val">{{ $institution->name }}</div>
                    </div>
                </div>
                <div class="inst-row">
                    <div class="inst-row-icon">📋</div>
                    <div>
                        <div class="inst-row-label">Code</div>
                        <div class="inst-row-val" style="font-family:monospace;">{{ $institution->code }}</div>
                    </div>
                </div>
                <div class="inst-row">
                    <div class="inst-row-icon">🎓</div>
                    <div>
                        <div class="inst-row-label">Type</div>
                        <div class="inst-row-val">{{ ucfirst($institution->type ?? '—') }}</div>
                    </div>
                </div>
                <div class="inst-row">
                    <div class="inst-row-icon">📅</div>
                    <div>
                        <div class="inst-row-label">Année académique</div>
                        <div class="inst-row-val">{{ $institution->academic_year ?? '—' }}</div>
                    </div>
                </div>
                <div class="inst-row">
                    <div class="inst-row-icon">📍</div>
                    <div>
                        <div class="inst-row-label">Localisation</div>
                        <div class="inst-row-val">{{ $institution->commune ?? '' }}{{ $institution->departement ? ', '.$institution->departement : '' }}</div>
                    </div>
                </div>
                @if($institution->email)
                <div class="inst-row">
                    <div class="inst-row-icon">✉️</div>
                    <div>
                        <div class="inst-row-label">Email institution</div>
                        <div class="inst-row-val">{{ $institution->email }}</div>
                    </div>
                </div>
                @endif
                @if($institution->telephone)
                <div class="inst-row">
                    <div class="inst-row-icon">📞</div>
                    <div>
                        <div class="inst-row-label">Téléphone</div>
                        <div class="inst-row-val">{{ $institution->telephone }}</div>
                    </div>
                </div>
                @endif
                <div style="margin-top:1rem;">
                    <a href="{{ route('admin.institution.settings') }}"
                       style="font-size:.75rem;color:#1f2937;font-weight:600;text-decoration:none;
                              display:inline-flex;align-items:center;gap:.3rem;
                              background:#f3f4f6;padding:.4rem .875rem;border-radius:.375rem;
                              border:1px solid #e5e7eb;transition:all .15s;"
                       onmouseover="this.style.background='#e5e7eb'"
                       onmouseout="this.style.background='#f3f4f6'">
                        ⚙️ Paramètres de l'établissement
                    </a>
                </div>
            </div>
        </div>
        @endif

    </div>{{-- fin colonne gauche --}}

    {{-- ═══════════════════════════════════
         COLONNE DROITE — formulaires
    ═══════════════════════════════════ --}}
    <div>
        {{-- Tabs navigation --}}
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('infos', this)">
                👤 Informations personnelles
            </button>
            <button class="tab-btn" onclick="switchTab('password', this)">
                🔑 Mot de passe
            </button>
            <button class="tab-btn" onclick="switchTab('security', this)">
                🛡 Sécurité & accès
            </button>
        </div>

        {{-- ══ TAB 1 : Infos personnelles ══ --}}
        <div class="tab-panel active" id="tab-infos">
            <div class="pcard">
                <div class="pcard-head">
                    <div class="pcard-head-icon" style="background:#eff6ff;">👤</div>
                    <div>
                        <div class="pcard-title">Informations personnelles</div>
                        <div class="pcard-sub">Modifiez votre nom, email et coordonnées</div>
                    </div>
                </div>
                <div class="pcard-body">
                    <form method="POST" action="{{ route('admin.profil.infos') }}">
                        @csrf @method('PATCH')

                        <div class="section-divider">
                            <span class="section-divider-label">Compte utilisateur</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <div class="f-grid">
                            <div class="f-full">
                                <label class="f-label">Nom complet *</label>
                                <input type="text" name="name" class="f-field {{ $errors->has('name') ? 'is-error' : '' }}"
                                       value="{{ old('name', $user->name) }}" required placeholder="Votre nom complet">
                                @error('name')<p class="f-err">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="f-label">Adresse e-mail *</label>
                                <input type="email" name="email" class="f-field {{ $errors->has('email') ? 'is-error' : '' }}"
                                       value="{{ old('email', $user->email) }}" required placeholder="votre@email.com">
                                @error('email')<p class="f-err">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="f-label">Téléphone</label>
                                <input type="tel" name="phone" class="f-field"
                                       value="{{ old('phone', $user->phone) }}" placeholder="+242 06 000 0000">
                            </div>
                        </div>

                        @if($staff)
                        <div class="section-divider" style="margin-top:1.5rem;">
                            <span class="section-divider-label">Profil professionnel</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <div class="f-grid">
                            <div>
                                <label class="f-label">Prénom</label>
                                <input type="text" name="prenom" class="f-field"
                                       value="{{ old('prenom', $staff->prenom) }}" placeholder="Prénom">
                            </div>
                            <div>
                                <label class="f-label">Nom de famille</label>
                                <input type="text" name="nom" class="f-field"
                                       value="{{ old('nom', $staff->nom) }}" placeholder="Nom">
                            </div>
                            <div>
                                <label class="f-label">Poste / Fonction</label>
                                <input type="text" name="poste" class="f-field"
                                       value="{{ old('poste', $staff->poste) }}" placeholder="Ex : Directeur général">
                            </div>
                            <div>
                                <label class="f-label">Téléphone professionnel</label>
                                <input type="tel" name="telephone" class="f-field"
                                       value="{{ old('telephone', $staff->telephone) }}" placeholder="+242 06 000 0000">
                            </div>
                            @if($staff->administrativeUnit)
                            <div class="f-full">
                                <label class="f-label">Unité administrative</label>
                                <input type="text" class="f-field" disabled
                                       value="{{ $staff->administrativeUnit->name }}"
                                       style="background:#f3f4f6;color:#6b7280;cursor:not-allowed;">
                                <p class="f-hint">Contactez le superadmin pour modifier l'unité.</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        {{-- Matricule & compte (lecture seule) --}}
                        @if($staff?->matricule || $user->created_at)
                        <div class="section-divider" style="margin-top:1.5rem;">
                            <span class="section-divider-label">Informations système</span>
                            <div class="section-divider-line"></div>
                        </div>
                        <div class="f-grid">
                            @if($staff?->matricule)
                            <div>
                                <label class="f-label">Matricule</label>
                                <input type="text" class="f-field" disabled
                                       value="{{ $staff->matricule }}"
                                       style="background:#f3f4f6;color:#6b7280;cursor:not-allowed;font-family:monospace;">
                            </div>
                            @endif
                            <div>
                                <label class="f-label">Compte créé le</label>
                                <input type="text" class="f-field" disabled
                                       value="{{ $user->created_at?->format('d/m/Y') ?? '—' }}"
                                       style="background:#f3f4f6;color:#6b7280;cursor:not-allowed;">
                            </div>
                            @foreach($user->getRoleNames() as $role)
                            <div>
                                <label class="f-label">Rôle</label>
                                <input type="text" class="f-field" disabled
                                       value="{{ ucfirst($role) }}"
                                       style="background:#f3f4f6;color:#6b7280;cursor:not-allowed;">
                            </div>
                            @endforeach
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

        {{-- ══ TAB 2 : Mot de passe ══ --}}
        <div class="tab-panel" id="tab-password">
            <div class="pcard">
                <div class="pcard-head">
                    <div class="pcard-head-icon" style="background:#fef3c7;">🔑</div>
                    <div>
                        <div class="pcard-title">Changer le mot de passe</div>
                        <div class="pcard-sub">Utilisez un mot de passe fort et unique</div>
                    </div>
                </div>
                <div class="pcard-body">
                    <form method="POST" action="{{ route('admin.profil.password') }}" id="passwordForm">
                        @csrf @method('PATCH')

                        <div style="display:flex;flex-direction:column;gap:1rem;max-width:480px;">

                            <div>
                                <label class="f-label">Mot de passe actuel *</label>
                                <div style="position:relative;">
                                    <input type="password" name="current_password" id="currentPw"
                                           class="f-field {{ $errors->has('current_password') ? 'is-error' : '' }}"
                                           required placeholder="••••••••"
                                           style="padding-right:2.75rem;">
                                    <button type="button" onclick="togglePw('currentPw', this)"
                                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;font-size:.85rem;">👁</button>
                                </div>
                                @error('current_password')<p class="f-err">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="f-label">Nouveau mot de passe *</label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="newPw"
                                           class="f-field {{ $errors->has('password') ? 'is-error' : '' }}"
                                           required placeholder="Min. 8 caractères"
                                           oninput="checkStrength(this.value)"
                                           style="padding-right:2.75rem;">
                                    <button type="button" onclick="togglePw('newPw', this)"
                                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;font-size:.85rem;">👁</button>
                                </div>
                                {{-- Barre de force --}}
                                <div class="pw-strength">
                                    <div class="pw-bar" id="bar1"></div>
                                    <div class="pw-bar" id="bar2"></div>
                                    <div class="pw-bar" id="bar3"></div>
                                    <div class="pw-bar" id="bar4"></div>
                                </div>
                                <p class="pw-label" id="pwLabel" style="color:#9ca3af;">Saisissez un mot de passe</p>
                                @error('password')<p class="f-err">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="f-label">Confirmer le nouveau mot de passe *</label>
                                <div style="position:relative;">
                                    <input type="password" name="password_confirmation" id="confirmPw"
                                           class="f-field" required placeholder="Répétez le mot de passe"
                                           oninput="checkMatch()"
                                           style="padding-right:2.75rem;">
                                    <button type="button" onclick="togglePw('confirmPw', this)"
                                            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;font-size:.85rem;">👁</button>
                                </div>
                                <p class="f-hint" id="matchLabel"></p>
                            </div>

                            {{-- Règles --}}
                            <div style="background:#f9fafb;border:1px solid var(--border);border-radius:.5rem;padding:.875rem;font-size:.75rem;color:#6b7280;">
                                <p style="font-weight:600;color:#374151;margin-bottom:.4rem;">Règles de sécurité :</p>
                                <ul style="list-style:none;display:flex;flex-direction:column;gap:.25rem;">
                                    <li id="rule-len">⬜ Au moins 8 caractères</li>
                                    <li id="rule-upper">⬜ Au moins une majuscule (A-Z)</li>
                                    <li id="rule-lower">⬜ Au moins une minuscule (a-z)</li>
                                    <li id="rule-num">⬜ Au moins un chiffre (0-9)</li>
                                </ul>
                            </div>

                            <div style="display:flex;justify-content:flex-end;">
                                <button type="submit" class="btn-save">
                                    🔑 Changer le mot de passe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ══ TAB 3 : Sécurité ══ --}}
        <div class="tab-panel" id="tab-security">
            <div class="pcard">
                <div class="pcard-head">
                    <div class="pcard-head-icon" style="background:#fef2f2;">🛡</div>
                    <div>
                        <div class="pcard-title">Sécurité & accès</div>
                        <div class="pcard-sub">Informations sur votre compte et vos sessions</div>
                    </div>
                </div>
                <div class="pcard-body">

                    {{-- Résumé du compte --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;margin-bottom:1.5rem;">
                        <div style="background:#f9fafb;border:1px solid var(--border);border-radius:.5rem;padding:1rem;">
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em;">Email de connexion</div>
                            <div style="font-size:.825rem;font-weight:600;color:#111827;margin-top:.25rem;">{{ $user->email }}</div>
                        </div>
                        <div style="background:#f9fafb;border:1px solid var(--border);border-radius:.5rem;padding:1rem;">
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em;">Statut du compte</div>
                            <div style="margin-top:.25rem;">
                                @if($user->status)
                                    <span style="background:#d1fae5;color:#065f46;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:99px;">✅ Actif</span>
                                @else
                                    <span style="background:#fee2e2;color:#991b1b;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:99px;">❌ Inactif</span>
                                @endif
                            </div>
                        </div>
                        <div style="background:#f9fafb;border:1px solid var(--border);border-radius:.5rem;padding:1rem;">
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em;">Membre depuis</div>
                            <div style="font-size:.825rem;font-weight:600;color:#111827;margin-top:.25rem;">
                                {{ $user->created_at?->locale('fr')->diffForHumans() ?? '—' }}
                            </div>
                        </div>
                        <div style="background:#f9fafb;border:1px solid var(--border);border-radius:.5rem;padding:1rem;">
                            <div style="font-size:.65rem;text-transform:uppercase;font-weight:700;color:#9ca3af;letter-spacing:.05em;">Rôle(s)</div>
                            <div style="margin-top:.25rem;display:flex;flex-wrap:wrap;gap:.25rem;">
                                @forelse($user->getRoleNames() as $role)
                                    <span style="background:#dbeafe;color:#1e40af;font-size:.68rem;font-weight:700;padding:.15rem .5rem;border-radius:4px;">{{ ucfirst($role) }}</span>
                                @empty
                                    <span style="font-size:.8rem;color:#9ca3af;">Aucun rôle assigné</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Session & déconnexion --}}
                    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:.5rem;padding:1rem;margin-bottom:1.25rem;">
                        <div style="font-size:.78rem;font-weight:600;color:#92400e;margin-bottom:.25rem;">⚠️ Session active</div>
                        <p style="font-size:.75rem;color:#78350f;">
                            Vous êtes actuellement connecté(e). Si vous pensez que votre compte
                            est compromis, déconnectez-vous immédiatement et changez votre mot de passe.
                        </p>
                    </div>

                    {{-- Actions de sécurité --}}
                    <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-danger">
                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Se déconnecter
                            </button>
                        </form>
                        <button type="button" onclick="switchTab('password', document.querySelectorAll('.tab-btn')[1])"
                                class="btn-save" style="background:#374151;">
                            🔑 Changer le mot de passe
                        </button>
                    </div>

                    {{-- Conseils de sécurité --}}
                    <div style="margin-top:1.5rem;">
                        <div class="section-divider">
                            <span class="section-divider-label">Bonnes pratiques</span>
                            <div class="section-divider-line"></div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:.75rem;">
                            @foreach([
                                ['🔒', 'Mot de passe fort', 'Utilisez au moins 12 caractères avec des chiffres et symboles'],
                                ['📧', 'Email sécurisé', 'Assurez-vous que votre email de récupération est accessible'],
                                ['🚫', 'Ne partagez jamais', 'Ne communiquez jamais vos identifiants à un tiers'],
                                ['🔄', 'Mise à jour régulière', 'Changez votre mot de passe tous les 3 à 6 mois'],
                            ] as [$icon, $title, $desc])
                            <div style="background:#f9fafb;border:1px solid var(--border);border-radius:.5rem;padding:.875rem;display:flex;gap:.625rem;align-items:flex-start;">
                                <span style="font-size:1.1rem;flex-shrink:0;">{{ $icon }}</span>
                                <div>
                                    <div style="font-size:.75rem;font-weight:700;color:#111827;">{{ $title }}</div>
                                    <div style="font-size:.7rem;color:#6b7280;margin-top:.2rem;line-height:1.5;">{{ $desc }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>{{-- fin colonne droite --}}
</div>

@endsection

@push('scripts')
<script>
/* ── Tabs ── */
function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

/* ── Toggle password visibility ── */
function togglePw(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁';
    }
}

/* ── Password strength meter ── */
function checkStrength(val) {
    const bars  = [document.getElementById('bar1'), document.getElementById('bar2'),
                   document.getElementById('bar3'), document.getElementById('bar4')];
    const label = document.getElementById('pwLabel');

    // Règles visuelles
    document.getElementById('rule-len').textContent   = (val.length >= 8)         ? '✅ Au moins 8 caractères'            : '⬜ Au moins 8 caractères';
    document.getElementById('rule-upper').textContent = (/[A-Z]/.test(val))        ? '✅ Au moins une majuscule (A-Z)'     : '⬜ Au moins une majuscule (A-Z)';
    document.getElementById('rule-lower').textContent = (/[a-z]/.test(val))        ? '✅ Au moins une minuscule (a-z)'     : '⬜ Au moins une minuscule (a-z)';
    document.getElementById('rule-num').textContent   = (/[0-9]/.test(val))        ? '✅ Au moins un chiffre (0-9)'        : '⬜ Au moins un chiffre (0-9)';

    let score = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[a-z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;

    // Reset
    bars.forEach(b => { b.className = 'pw-bar'; });

    const cls = score <= 2 ? 'active-weak' : score <= 3 ? 'active-medium' : 'active-strong';
    const txt = score <= 2 ? '🔴 Faible' : score <= 3 ? '🟡 Moyen' : '🟢 Fort';
    const col = score <= 2 ? '#ef4444'  : score <= 3 ? '#f59e0b'  : '#10b981';

    for (let i = 0; i < Math.min(score, 4); i++) {
        bars[i].classList.add(cls);
    }
    label.textContent  = val ? txt : 'Saisissez un mot de passe';
    label.style.color  = val ? col : '#9ca3af';

    checkMatch(); // Recheck match on change
}

/* ── Match checker ── */
function checkMatch() {
    const pw1 = document.getElementById('newPw').value;
    const pw2 = document.getElementById('confirmPw').value;
    const lbl = document.getElementById('matchLabel');
    if (!pw2) { lbl.textContent = ''; return; }
    if (pw1 === pw2) {
        lbl.textContent = '✅ Les mots de passe correspondent';
        lbl.style.color = '#10b981';
    } else {
        lbl.textContent = '❌ Les mots de passe ne correspondent pas';
        lbl.style.color = '#ef4444';
    }
}

/* ── Ouvrir l'onglet avec erreur de validation ── */
@if($errors->has('current_password') || $errors->has('password'))
    switchTab('password', document.querySelectorAll('.tab-btn')[1]);
@endif

/* ── Avatar preview avant envoi ── */
document.getElementById('avatarFileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = evt => {
            document.getElementById('avatarPreview').src = evt.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
