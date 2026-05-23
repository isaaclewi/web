@extends('superadmin.master')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

    :root {
        --bg-deep:       #080c14;
        --bg-card:       #0e1420;
        --bg-card2:      #111827;
        --border:        rgba(255,255,255,0.07);
        --border-bright: rgba(99,179,237,0.35);
        --accent:        #3b82f6;
        --accent-glow:   rgba(59,130,246,0.25);
        --accent2:       #06b6d4;
        --text-primary:  #f0f4ff;
        --text-muted:    #8896b3;
        --success:       #10b981;
        --danger:        #ef4444;
        --warning:       #f59e0b;
        --indigo:        #6366f1;
        --radius:        12px;
    }

    .usr-page * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
    .usr-page {
        min-height: 100vh;
        background: var(--bg-deep);
        padding: 2.5rem 2rem;
        color: var(--text-primary);
    }

    .usr-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;
    }
    .usr-header h1 {
        font-family: 'Syne', sans-serif; font-size: 1.75rem; font-weight: 800;
        letter-spacing: -0.02em;
        background: linear-gradient(135deg, #f0f4ff 0%, var(--accent2) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0;
    }
    .usr-header .subtitle { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; }

    .btn-add {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px;
        font-family: 'Syne', sans-serif; font-weight: 600; font-size: 0.875rem;
        cursor: pointer; transition: opacity .2s, transform .15s, box-shadow .2s;
        box-shadow: 0 4px 20px var(--accent-glow);
    }
    .btn-add:hover { opacity: .9; transform: translateY(-1px); }

    /* ── Alertes flash ── */
    .alert {
        border-radius: 10px; padding: 0.875rem 1.25rem; margin-bottom: 1.25rem;
        font-size: 0.875rem; display: flex; align-items: flex-start; gap: 0.5rem;
    }
    .alert-success {
        background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #34d399;
    }
    .alert-error {
        background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); color: #f87171;
        flex-direction: column;
    }
    .alert-error ul { margin: 0.25rem 0 0 1rem; padding: 0; }
    .alert-error li { margin-bottom: 0.15rem; }

    .table-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.4);
    }
    .table-card table { width: 100%; border-collapse: collapse; }
    .table-card thead tr { background: var(--bg-card2); border-bottom: 1px solid var(--border); }
    .table-card thead th {
        padding: 0.85rem 1.25rem; text-align: left;
        font-family: 'Syne', sans-serif; font-size: 0.7rem; font-weight: 700;
        letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-muted);
    }
    .table-card thead th:last-child { text-align: right; }
    .table-card tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody tr:hover { background: rgba(255,255,255,0.03); }
    .table-card tbody td {
        padding: 1rem 1.25rem; font-size: 0.875rem;
        color: var(--text-primary); vertical-align: middle;
    }
    .table-card tbody td:last-child { text-align: right; }

    .cell-name { font-weight: 500; }
    .cell-email { color: var(--text-muted); font-size: 0.82rem; }
    .cell-matricule { font-family: monospace; font-size: 0.8rem; color: #fbbf24; }
    .cell-institution {
        font-size: 0.8rem; color: var(--accent2);
        background: rgba(6,182,212,0.1); padding: 2px 8px; border-radius: 4px; display: inline-block;
    }
    .cell-role {
        font-size: 0.75rem; color: #a78bfa;
        background: rgba(139,92,246,0.12); border: 1px solid rgba(139,92,246,0.2);
        padding: 2px 8px; border-radius: 4px; display: inline-block;
    }

    .badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.7rem; border-radius: 999px; font-size: 0.75rem; font-weight: 500; }
    .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .badge-active   { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
    .badge-active::before { background: #34d399; box-shadow: 0 0 6px #34d399; }
    .badge-inactive { background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
    .badge-inactive::before { background: #f87171; }

    .actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.4rem; }
    .btn-action {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.35rem 0.75rem; border-radius: 6px;
        font-size: 0.78rem; font-weight: 500; border: none; cursor: pointer;
        transition: opacity .15s, transform .1s; font-family: 'DM Sans', sans-serif; white-space: nowrap;
    }
    .btn-action:hover { opacity: .85; transform: translateY(-1px); }
    .btn-delete { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }

    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-muted); font-size: 0.9rem; }

    /* ── Modal ── */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(5,8,15,0.85); backdrop-filter: blur(6px);
        display: flex; justify-content: center; align-items: flex-start;
        z-index: 9999; padding: 2rem 1rem; overflow-y: auto;
    }
    .modal-overlay.hidden { display: none; }
    .modal-box {
        background: var(--bg-card); border: 1px solid var(--border-bright);
        border-radius: 16px; width: 100%; max-width: 580px;
        padding: 2rem; position: relative;
        box-shadow: 0 24px 60px rgba(0,0,0,0.6), 0 0 0 1px rgba(59,130,246,0.1);
        animation: modalIn .25s cubic-bezier(.34,1.2,.64,1) both;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(-16px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
    }
    .modal-header h2 { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 700; margin: 0; }
    .modal-close {
        background: rgba(255,255,255,0.06); border: 1px solid var(--border); color: var(--text-muted);
        width: 32px; height: 32px; border-radius: 8px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; font-size: 1rem;
        transition: background .15s, color .15s;
    }
    .modal-close:hover { background: rgba(239,68,68,0.15); color: #f87171; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-grid .full { grid-column: 1 / -1; }

    .field label {
        display: block; font-size: 0.73rem; font-weight: 500; color: var(--text-muted);
        margin-bottom: 0.35rem; letter-spacing: 0.04em; text-transform: uppercase;
    }
    .field input, .field select {
        width: 100%; padding: 0.6rem 0.85rem;
        background: var(--bg-card2); border: 1px solid var(--border);
        border-radius: 8px; color: var(--text-primary);
        font-family: 'DM Sans', sans-serif; font-size: 0.875rem;
        transition: border-color .15s, box-shadow .15s; outline: none; -webkit-appearance: none;
    }
    .field input:focus, .field select:focus {
        border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow);
    }
    /* Champ en erreur */
    .field input.is-error, .field select.is-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.15) !important;
    }
    .field .field-error {
        font-size: 0.72rem; color: #f87171; margin-top: 0.3rem;
    }

    .role-locked {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: rgba(139,92,246,0.12); border: 1px solid rgba(139,92,246,0.25);
        color: #a78bfa; padding: 0.5rem 0.85rem; border-radius: 8px;
        font-size: 0.85rem; font-weight: 500; width: 100%;
    }
    .form-section-label {
        grid-column: 1 / -1; font-family: 'Syne', sans-serif; font-size: 0.68rem;
        font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
        color: var(--accent2); padding: 0.25rem 0;
        border-bottom: 1px solid rgba(6,182,212,0.15); margin-top: 0.25rem;
    }
    .modal-footer {
        display: flex; justify-content: flex-end; gap: 0.75rem;
        margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border);
    }
    .btn-cancel {
        padding: 0.6rem 1.2rem; background: transparent; border: 1px solid var(--border);
        border-radius: 8px; color: var(--text-muted);
        font-family: 'Syne', sans-serif; font-size: 0.85rem; font-weight: 600;
        cursor: pointer; transition: background .15s, color .15s;
    }
    .btn-cancel:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .btn-save {
        padding: 0.6rem 1.4rem;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        border: none; border-radius: 8px; color: #fff;
        font-family: 'Syne', sans-serif; font-size: 0.85rem; font-weight: 700;
        cursor: pointer; box-shadow: 0 4px 16px var(--accent-glow);
        transition: opacity .15s, transform .1s;
    }
    .btn-save:hover { opacity: .9; transform: translateY(-1px); }
    .btn-save:disabled { opacity: .5; cursor: not-allowed; transform: none; }
/* ─────────────────────────────
   GLOBAL MOBILE
───────────────────────────── */
@media (max-width: 768px) {
    .usr-page {
        padding: 1.25rem 1rem !important;
    }

    .usr-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .btn-add {
        width: 100%;
        justify-content: center;
    }
}

/* ─────────────────────────────
   TABLE RESPONSIVE (SCROLL)
───────────────────────────── */
@media (max-width: 768px) {
    .table-card {
        overflow-x: auto;
    }

    .table-card table {
        min-width: 700px;
    }

    .table-card th,
    .table-card td {
        padding: 0.6rem 0.75rem;
        font-size: 0.75rem;
    }

    .actions {
        flex-direction: column;
        align-items: stretch;
    }

    .btn-action {
        width: 100%;
        justify-content: center;
    }
}

/* ─────────────────────────────
   MOBILE STACK (ULTRA SMALL)
───────────────────────────── */
@media (max-width: 480px) {
    .table-card table {
        min-width: 600px;
    }
}

/* ─────────────────────────────
   MODAL RESPONSIVE
───────────────────────────── */
@media (max-width: 640px) {
    .modal-box {
        padding: 1.25rem;
        border-radius: 12px;
    }

    .modal-header {
        flex-direction: row;
    }

    .modal-footer {
        flex-direction: column;
    }

    .btn-save,
    .btn-cancel {
        width: 100%;
        justify-content: center;
    }
}

/* ─────────────────────────────
   FORM GRID
───────────────────────────── */
@media (max-width: 640px) {
    .form-grid {
        grid-template-columns: 1fr !important;
    }
}

/* ─────────────────────────────
   PAGINATION STYLE
───────────────────────────── */
.pagination {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.4rem;
    padding: 1rem;
}

.pagination .page-item {
    list-style: none;
}

.pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    padding: 0 0.6rem;
    border-radius: 8px;
    background: var(--bg-card2);
    border: 1px solid var(--border);
    color: var(--text-muted);
    font-size: 0.8rem;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination .page-link:hover {
    background: rgba(59,130,246,0.15);
    border-color: var(--border-bright);
    color: var(--text-primary);
}

.pagination .active .page-link {
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 12px var(--accent-glow);
}

.pagination .disabled .page-link {
    opacity: 0.4;
    cursor: not-allowed;
}

/* ─────────────────────────────
   PAGINATION MOBILE
───────────────────────────── */
@media (max-width: 480px) {
    .pagination {
        gap: 0.25rem;
    }

    .pagination .page-link {
        min-width: 30px;
        height: 30px;
        font-size: 0.7rem;
    }
}
    @media (max-width: 640px) {
        .form-grid { grid-template-columns: 1fr; }
        .usr-page  { padding: 1.25rem 1rem; }
    }
</style>

<div class="usr-page">

    {{-- HEADER --}}
    <div class="usr-header">
        <div>
            <h1>Directeurs</h1>
            <p class="subtitle">Création et gestion des directeurs d'établissement</p>
        </div>
        <button onclick="openCreateModal()" class="btn-add">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nouveau directeur
        </button>
    </div>

    {{-- ── Flash success ── --}}
    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Erreurs de validation (affichées hors modal après redirect) ── --}}
    @if($errors->any())
        <div class="alert alert-error">
            <strong>⚠ Erreur de validation :</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="table-card">
        <table>
        
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Matricule</th>
                    <th>Institution</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="cell-name">
                        {{ $user->staff?->prenom ?? '' }} {{ $user->staff?->nom ?? $user->name }}
                    </td>
                    <td class="cell-email">{{ $user->email }}</td>
                    <td>
                        @if($user->staff?->matricule)
                            <span class="cell-matricule">{{ $user->staff->matricule }}</span>
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($user->institution)
                            <span class="cell-institution">{{ $user->institution->name }}</span>
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td><span class="cell-role">Directeur</span></td>
                    <td>
                        @if($user->status == 1)
                            <span class="badge badge-active">Actif</span>
                        @else
                            <span class="badge badge-inactive">Inactif</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn-action btn-delete"
                                        onclick="return confirm('Supprimer ce directeur et son profil ?')">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">Aucun directeur enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding: 0.75rem 1rem; border-top: 1px solid var(--border);">
    {{ $users->links() }}
</div>
    </div>

</div>

{{-- ═══════════════════════════════════════
     MODAL NOUVEAU DIRECTEUR
═══════════════════════════════════════ --}}
<div id="userModal" class="modal-overlay hidden">
    <div class="modal-box">

        <div class="modal-header">
            <h2 id="modalTitle">Nouveau directeur</h2>
            <button type="button" onclick="closeModal()" class="modal-close">✕</button>
        </div>

        <form id="userForm" method="POST" action="{{ route('superadmin.users.store') }}">
            @csrf

            {{-- Erreurs inline dans le modal si on rouvre après échec --}}
            @if($errors->any())
            <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#f87171;border-radius:8px;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.8rem;">
                @foreach($errors->all() as $error)
                    <div>⚠ {{ $error }}</div>
                @endforeach
            </div>
            @endif

            <div class="form-grid">

                {{-- Section Identité --}}
                <div class="form-section-label">Identité</div>

                <div class="field">
                    <label>Nom complet *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="{{ $errors->has('name') ? 'is-error' : '' }}"
                           required placeholder="Ex: Jean Mbemba">
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="{{ $errors->has('email') ? 'is-error' : '' }}"
                           required placeholder="directeur@ecole.cg">
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field" id="passwordField">
                    <label>Mot de passe * (min. 8 caractères)</label>
                    <input type="password" name="password"
                           class="{{ $errors->has('password') ? 'is-error' : '' }}"
                           required minlength="8" placeholder="Min. 8 caractères">
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label>Statut</label>
                    <select name="status">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                {{-- Rôle verrouillé --}}
                <div class="field full">
                    <label>Rôle attribué</label>
                    <div class="role-locked">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Directeur d'établissement
                    </div>
                </div>

                {{-- Section Institution --}}
                <div class="form-section-label">Institution rattachée</div>

                <div class="field full">
                    <label>Établissement *</label>
                    <select name="institution_id"
                            class="{{ $errors->has('institution_id') ? 'is-error' : '' }}"
                            required>
                        <option value="">— Sélectionner un établissement —</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}{{ $inst->code ? ' ('.$inst->code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('institution_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- Section Profil Staff (utilisé pour l'authentification) --}}
                <div class="form-section-label">Informations professionnelles</div>

                <div class="field">
                    <label>Matricule * <span style="color:#8896b3;font-size:0.65rem;text-transform:none;letter-spacing:0;">(utilisé pour la connexion)</span></label>
                    <input type="text" name="matricule" value="{{ old('matricule') }}"
                           class="{{ $errors->has('matricule') ? 'is-error' : '' }}"
                           required placeholder="DIR-2025-001">
                    @error('matricule')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}"
                           placeholder="+242 06 …">
                </div>

                <div class="field">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}"
                           placeholder="Prénom">
                </div>

                <div class="field">
                    <label>Nom de famille</label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                           placeholder="Nom">
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-cancel">Annuler</button>
                <button type="submit" class="btn-save" id="submitBtn">
                    Enregistrer le directeur
                </button>
            </div>
        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Rouvrir automatiquement le modal s'il y a des erreurs de validation
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('userModal').classList.remove('hidden');
        });
    @endif

    function openCreateModal() {
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    // Fermer le modal en cliquant sur l'overlay
    document.getElementById('userModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // Empêcher double soumission
    document.getElementById('userForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Enregistrement…';
    });
</script>
@endpush