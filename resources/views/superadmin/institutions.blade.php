{{-- resources/views/superadmin/institutions.blade.php --}}

@extends('superadmin.master') {{-- adapte si ton layout a un autre nom --}}

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

    .inst-page * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }

    /* ── PAGE WRAPPER ── */
    .inst-page {
        min-height: 100vh;
        background: var(--bg-deep);
        padding: 2.5rem 2rem;
        color: var(--text-primary);
    }

    /* ── HEADER ── */
    .inst-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .inst-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        background: linear-gradient(135deg, #f0f4ff 0%, var(--accent2) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
    }
    .inst-header .subtitle {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
    }

    /* ── ADD BUTTON ── */
    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        color: #fff;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-family: 'Syne', sans-serif;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: opacity .2s, transform .15s, box-shadow .2s;
        box-shadow: 0 4px 20px var(--accent-glow);
        letter-spacing: 0.01em;
    }
    .btn-add:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 6px 28px var(--accent-glow); }
    .btn-add:active { transform: translateY(0); }
    .btn-add svg { flex-shrink: 0; }

    /* ── TABLE CARD ── */
    .table-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: 0 8px 40px rgba(0,0,0,0.4);
    }
    .table-card table { width: 100%; border-collapse: collapse; }

    .table-card thead tr {
        background: var(--bg-card2);
        border-bottom: 1px solid var(--border);
    }
    .table-card thead th {
        padding: 0.85rem 1.25rem;
        text-align: left;
        font-family: 'Syne', sans-serif;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--text-muted);
    }
    .table-card thead th:last-child { text-align: right; }

    .table-card tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background .15s;
    }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody tr:hover { background: rgba(255,255,255,0.03); }

    .table-card tbody td {
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        color: var(--text-primary);
        vertical-align: middle;
    }
    .table-card tbody td:last-child { text-align: right; }

    .cell-name { font-weight: 500; }
    .cell-code {
        font-family: 'DM Mono', monospace;
        font-size: 0.8rem;
        color: var(--accent2);
        background: rgba(6,182,212,0.1);
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .cell-type { color: var(--text-muted); font-size: 0.82rem; }

    /* ── STATUS BADGES ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.7rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .badge::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .badge-active  { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
    .badge-active::before  { background: #34d399; box-shadow: 0 0 6px #34d399; }
    .badge-inactive { background: rgba(239,68,68,0.1);  color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
    .badge-inactive::before { background: #f87171; }

    /* ── ACTION BUTTONS ── */
    .actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.4rem; flex-wrap: wrap; }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: opacity .15s, transform .1s;
        font-family: 'DM Sans', sans-serif;
        white-space: nowrap;
    }
    .btn-action:hover { opacity: .85; transform: translateY(-1px); }
    .btn-edit     { background: rgba(245,158,11,0.15);  color: #fbbf24; border: 1px solid rgba(245,158,11,0.25); }
    .btn-toggle   { background: rgba(99,102,241,0.15);  color: #a5b4fc; border: 1px solid rgba(99,102,241,0.25); }
    .btn-delete   { background: rgba(239,68,68,0.12);   color: #f87171; border: 1px solid rgba(239,68,68,0.2); }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-muted); font-size: 0.9rem; }

    /* ── MODAL OVERLAY ── */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(5,8,15,0.85);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        display: flex;
        justify-content: center;
        align-items: flex-start;
        z-index: 9999;
        padding: 2rem 1rem;
        overflow-y: auto;
    }
    .modal-overlay.hidden { display: none; }

    /* ── MODAL BOX ── */
    .modal-box {
        background: var(--bg-card);
        border: 1px solid var(--border-bright);
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        padding: 2rem;
        position: relative;
        box-shadow: 0 24px 60px rgba(0,0,0,0.6), 0 0 0 1px rgba(59,130,246,0.1);
        animation: modalIn .25s cubic-bezier(.34,1.2,.64,1) both;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: translateY(-16px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    .modal-header h2 {
        font-family: 'Syne', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .modal-close {
        background: rgba(255,255,255,0.06);
        border: 1px solid var(--border);
        color: var(--text-muted);
        width: 32px; height: 32px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: background .15s, color .15s;
        flex-shrink: 0;
    }
    .modal-close:hover { background: rgba(239,68,68,0.15); color: #f87171; border-color: rgba(239,68,68,0.3); }

    /* ── FORM GRID ── */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .form-grid .full { grid-column: 1 / -1; }

    .field label {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .field input,
    .field select,
    .field textarea {
        width: 100%;
        padding: 0.6rem 0.85rem;
        background: var(--bg-card2);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-primary);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
        -webkit-appearance: none;
    }
    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }
    .field textarea { resize: vertical; min-height: 72px; }

    /* ── SECTION DIVIDER ── */
    .form-section-label {
        grid-column: 1 / -1;
        font-family: 'Syne', sans-serif;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--accent2);
        padding: 0.25rem 0;
        border-bottom: 1px solid rgba(6,182,212,0.15);
        margin-top: 0.25rem;
    }

    /* ── MODAL FOOTER ── */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    .btn-cancel {
        padding: 0.6rem 1.2rem;
        background: transparent;
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-muted);
        font-family: 'Syne', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, color .15s;
    }
    .btn-cancel:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .btn-save {
        padding: 0.6rem 1.4rem;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        border: none;
        border-radius: 8px;
        color: #fff;
        font-family: 'Syne', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 16px var(--accent-glow);
        transition: opacity .15s, transform .1s;
    }
    .btn-save:hover { opacity: .9; transform: translateY(-1px); }
/* ═══════════════════════════════════════
   RESPONSIVE GLOBAL
═══════════════════════════════════════ */

/* TABLE SCROLL MOBILE */
.table-card {
    overflow-x: auto;
}

.table-card table {
    min-width: 650px;
}

/* HEADER STACK */
@media (max-width: 768px) {
    .inst-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .btn-add {
        width: 100%;
        justify-content: center;
    }
}

/* TABLE → VERSION MOBILE CARDS */
@media (max-width: 640px) {

    .table-card table,
    .table-card thead,
    .table-card tbody,
    .table-card th,
    .table-card td,
    .table-card tr {
        display: block;
        width: 100%;
    }

    .table-card thead {
        display: none;
    }

    .table-card tbody tr {
        background: var(--bg-card2);
        margin-bottom: 1rem;
        border-radius: 10px;
        padding: 0.75rem;
        border: 1px solid var(--border);
    }

    .table-card tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border: none;
        font-size: 0.82rem;
    }

    .table-card tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--text-muted);
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    .actions {
        justify-content: flex-start;
        gap: 0.3rem;
    }
}

/* BUTTONS WRAP */
.actions {
    flex-wrap: wrap;
}

/* MODAL MOBILE */
@media (max-width: 640px) {
    .modal-box {
        padding: 1.2rem;
        border-radius: 12px;
    }

    .modal-footer {
        flex-direction: column;
    }

    .btn-save,
    .btn-cancel {
        width: 100%;
        text-align: center;
    }
}

/* FORM GRID TABLET */
@media (max-width: 900px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
    /* ── RESPONSIVE ── */
    @media (max-width: 640px) {
        .form-grid { grid-template-columns: 1fr; }
        .inst-page  { padding: 1.25rem 1rem; }
    }
</style>

<div class="inst-page">

    {{-- ── HEADER ── --}}
    <div class="inst-header">
        <div>
            <h1>Institutions</h1>
            <p class="subtitle">Gestion des établissements enregistrés</p>
        </div>
        <button onclick="toggleAddForm()" class="btn-add">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter une institution
        </button>
    </div>

    {{-- ── TABLEAU ── --}}
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($institutions as $institution)
                    <tr>
                        <td data-label="Nom" class="cell-name">{{ $institution->name }}</td>
                        <td data-label="Code"><span class="cell-code">{{ $institution->code }}</span></td>
                        <td data-label="Type" class="cell-type">{{ $institution->type }}</td>
                        <td data-label="Statut">
                            @if ($institution->status)
                                <span class="badge badge-active">Actif</span>
                            @else
                                <span class="badge badge-inactive">Inactif</span>
                            @endif
                        </td>
                        <td data-label="actions">
                            <div class="actions">

                                {{-- Modifier --}}
                                <button
                                    onclick="editInstitution({{ $institution->id }}, '{{ $institution->name }}', '{{ $institution->code }}', '{{ $institution->type }}', '{{ $institution->academic_year }}', '{{ $institution->statut_juridique }}', '{{ $institution->departement }}', '{{ $institution->commune }}', '{{ $institution->adresse }}')"
                                    class="btn-action btn-edit">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Modifier
                                </button>

                                {{-- Activer / Désactiver --}}
                                <form action="{{ route('superadmin.institutions.toggleStatus', $institution->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn-action btn-toggle">
                                        @if ($institution->status == 1)
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                            Désactiver
                                        @else
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                            Activer
                                        @endif
                                    </button>
                                </form>

                                {{-- Supprimer --}}
                                <form action="{{ route('superadmin.institutions.destroy', $institution->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-action btn-delete" onclick="return confirm('Supprimer cette institution ?')">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        Supprimer
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">Aucune institution enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── MODAL AJOUT / MODIFICATION ── --}}
    <div id="institutionForm" class="modal-overlay hidden">
        <div class="modal-box">

            <div class="modal-header">
                <h2 id="formTitle">Ajouter une institution</h2>
                <button type="button" onclick="toggleAddForm()" class="modal-close" title="Fermer">✕</button>
            </div>

            <form id="formInstitution" method="POST" action="{{ route('superadmin.institutions.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="form-grid">

                    {{-- ── Informations principales ── --}}
                    <div class="form-section-label">Informations principales</div>

                    <div class="field full">
                        <label>Nom</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="field">
                        <label>Code</label>
                        <input type="text" name="code" id="code" required>
                    </div>
                    <div class="field">
                        <label>Type</label>
                        <select name="type" id="type">
                            <option value="universite">Université</option>
                            <option value="primaire">primaire</option>
                            <option value="secondaire">secondaire</option>
                            <option value="lycee">Lycée</option>
                            <option value="centre">Centre de formation</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Année académique</label>
                        <input type="text" name="academic_year" id="academic_year" required placeholder="2025-2026">
                    </div>
                    <div class="field">
                        <label>Statut juridique</label>
                        <select name="statut_juridique" id="statut_juridique">
                            <option value="public">Public</option>
                            <option value="prive">Privé</option>
                            <option value="confessionnel">Confessionnel</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Date de création</label>
                        <input type="date" name="date_creation" id="date_creation">
                    </div>
                    <div class="field">
                        <label>Autorisation État</label>
                        <select name="autorisation_etat" id="autorisation_etat">
                            <option value="1">Oui</option>
                            <option value="0">Non</option>
                        </select>
                    </div>

                    {{-- ── Localisation ── --}}
                    <div class="form-section-label">Localisation</div>

                    <div class="field">
                        <label>Pays</label>
                        <input type="text" name="pays" id="pays" value="Congo">
                    </div>
                    <div class="field">
                        <label>Département</label>
                        <input type="text" name="departement" id="departement">
                    </div>
                    <div class="field">
                        <label>Commune</label>
                        <input type="text" name="commune" id="commune">
                    </div>
                    <div class="field full">
                        <label>Adresse</label>
                        <input type="text" name="adresse" id="adresse">
                    </div>

                    {{-- ── Contact ── --}}
                    <div class="form-section-label">Contact</div>

                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" id="email">
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" id="telephone">
                    </div>
                    <div class="field">
                        <label>Site web</label>
                        <input type="text" name="site_web" id="site_web">
                    </div>
                    <div class="field">
                        <label>Devise</label>
                        <input type="text" name="devise" id="devise">
                    </div>

                    {{-- ── Identité ── --}}
                    <div class="form-section-label">Identité &amp; Mission</div>

                    <div class="field full">
                        <label>Description</label>
                        <textarea name="description" id="description"></textarea>
                    </div>
                    <div class="field full">
                        <label>Historique</label>
                        <textarea name="historique" id="historique"></textarea>
                    </div>
                    <div class="field full">
                        <label>Mission</label>
                        <textarea name="mission" id="mission"></textarea>
                    </div>
                    <div class="field full">
                        <label>Vision</label>
                        <textarea name="vision" id="vision"></textarea>
                    </div>
                    <div class="field full">
                        <label>Valeurs</label>
                        <textarea name="valeurs" id="valeurs"></textarea>
                    </div>

                </div>{{-- /form-grid --}}

                <div class="modal-footer">
                    <button type="button" onclick="toggleAddForm()" class="btn-cancel">Annuler</button>
                    <button type="submit" class="btn-save">Enregistrer</button>
                </div>

            </form>
        </div>
    </div>

</div>{{-- /inst-page --}}

{{-- ── JS (identique à l'original) ── --}}
<script>
    function toggleAddForm() {
        document.getElementById('institutionForm').classList.toggle('hidden');
        document.getElementById('formTitle').innerText = 'Ajouter une institution';
        document.getElementById('formInstitution').action = "{{ route('superadmin.institutions.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('name').value = '';
        document.getElementById('code').value = '';
        document.getElementById('type').value = '';
        document.getElementById('academic_year').value = '';
        document.getElementById('statut_juridique').value = 'public';
        document.getElementById('departement').value = '';
        document.getElementById('commune').value = '';
        document.getElementById('adresse').value = '';
    }

    function editInstitution(
        id,
        name,
        code,
        type,
        academic_year,
        statut_juridique,
        departement,
        commune,
        adresse
    ) {
        document.getElementById('institutionForm').classList.remove('hidden');
        document.getElementById('formTitle').innerText = "Modifier institution";
        document.getElementById('formInstitution').action = "/superadmin/institutions/" + id;
        document.getElementById('formMethod').value = "PATCH";
        document.getElementById('name').value = name;
        document.getElementById('code').value = code;
        document.getElementById('type').value = type;
        document.getElementById('academic_year').value = academic_year;
        document.getElementById('statut_juridique').value = statut_juridique;
        document.getElementById('departement').value = departement;
        document.getElementById('commune').value = commune;
        document.getElementById('adresse').value = adresse;
    }
</script>

@endsection