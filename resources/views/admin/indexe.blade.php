@extends('admin.master')

@section('title', 'Transfert inter-établissements')

@push('styles')
<style>
    /* ── Tokens & reset ── */
    :root {
        --ink:    #111827; --ink-mid: #374151; --muted: #6b7280;
        --border: #e5e7eb; --bg: #f8f9fa; --radius: .75rem;
        --accent: #1f2937; --green: #10b981; --amber: #f59e0b;
        --red: #ef4444; --blue: #3b82f6;
    }

    /* ── Layout ── */
    .tr-grid { display:grid; grid-template-columns:400px 1fr; gap:1.25rem; align-items:start; }
    @media(max-width:1024px) { .tr-grid { grid-template-columns:1fr; } }

    /* ── Cards ── */
    .card { background:white; border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
    .card+.card { margin-top:1rem; }
    .card-head {
        padding:.875rem 1.375rem; border-bottom:1px solid #f3f4f6;
        display:flex; align-items:center; justify-content:space-between; gap:.5rem; flex-wrap:wrap;
    }
    .card-title { font-size:.875rem; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:.5rem; }
    .card-body  { padding:1.25rem; }

    /* ── Stats ── */
    .stats-row { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; margin-bottom:1.25rem; }
    .stat-chip { background:white; border:1px solid var(--border); border-radius:.625rem; padding:.875rem; text-align:center; }
    .stat-val  { font-size:1.5rem; font-weight:700; color:var(--ink); }
    .stat-lbl  { font-size:.65rem; color:var(--muted); margin-top:.15rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }

    /* ── Search panel ── */
    .search-panel { background:linear-gradient(135deg,#f8fafc,#f1f5f9); border:1px solid var(--border); border-radius:var(--radius); padding:1.5rem; }
    .search-panel h2 { font-size:.95rem; font-weight:700; color:var(--ink); margin-bottom:.25rem; }
    .search-panel p  { font-size:.75rem; color:var(--muted); margin-bottom:1.25rem; }

    .f-label  { font-size:.68rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem; }
    .f-field  { background:white; border:1px solid var(--border); border-radius:.5rem; padding:.575rem .875rem; font-size:.875rem; color:var(--ink-mid); width:100%; outline:none; transition:all .2s; font-family:inherit; }
    .f-field:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(31,41,55,.07); }
    .f-field::placeholder { color:#d1d5db; }

    .btn-search { background:var(--accent); color:white; font-weight:700; border:none; padding:.6rem 1.25rem; border-radius:.5rem; cursor:pointer; font-size:.8rem; font-family:inherit; transition:all .2s; display:inline-flex; align-items:center; gap:.4rem; width:100%; justify-content:center; margin-top:.75rem; }
    .btn-search:hover { background:#374151; }
    .btn-primary { background:var(--accent); color:white; font-weight:700; border:none; padding:.5rem 1rem; border-radius:.5rem; cursor:pointer; font-size:.78rem; font-family:inherit; transition:all .2s; display:inline-flex; align-items:center; gap:.35rem; }
    .btn-primary:hover { background:#374151; }
    .btn-ghost { background:#f3f4f6; color:var(--ink-mid); font-weight:600; border:1px solid var(--border); padding:.5rem 1rem; border-radius:.5rem; cursor:pointer; font-size:.78rem; font-family:inherit; transition:all .15s; }
    .btn-ghost:hover { background:#e5e7eb; }
    .btn-danger { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; font-weight:600; padding:.4rem .875rem; border-radius:.375rem; cursor:pointer; font-size:.75rem; font-family:inherit; }

    /* ── Résultats recherche ── */
    #searchResults { margin-top:1rem; }
    .result-card { background:white; border:1px solid var(--border); border-radius:.625rem; padding:1rem; margin-bottom:.625rem; }
    .result-card:hover { border-color:#c7d2fe; }
    .result-name { font-size:.875rem; font-weight:700; color:var(--ink); }
    .result-meta { font-size:.72rem; color:var(--muted); margin-top:.25rem; }
    .result-school { font-size:.72rem; background:#dbeafe; color:#1e40af; padding:.15rem .5rem; border-radius:4px; display:inline-block; margin-top:.35rem; }

    /* ── Tableau demandes ── */
    .t-table { width:100%; border-collapse:collapse; }
    .t-table th { background:#f9fafb; padding:.65rem 1rem; text-align:left; font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); border-bottom:1px solid var(--border); }
    .t-table td { padding:.8rem 1rem; border-bottom:1px solid #f9fafb; font-size:.8125rem; color:var(--ink-mid); vertical-align:middle; }
    .t-table tr:last-child td { border-bottom:none; }
    .t-table tr:hover td { background:#fafafa; }

    /* ── Badges ── */
    .badge { display:inline-block; padding:.2rem .6rem; border-radius:99px; font-size:.68rem; font-weight:600; white-space:nowrap; }
    .badge-pending  { background:#fef3c7; color:#92400e; }
    .badge-approved { background:#d1fae5; color:#065f46; }
    .badge-rejected { background:#fee2e2; color:#991b1b; }
    .badge-completed{ background:#dbeafe; color:#1e40af; }

    /* ── Tabs ── */
    .tabs { display:flex; gap:.375rem; margin-bottom:1rem; flex-wrap:wrap; border-bottom:1px solid var(--border); padding-bottom:.75rem; }
    .tab-btn { padding:.45rem 1rem; border-radius:.5rem; font-size:.78rem; font-weight:600; border:1px solid transparent; background:transparent; color:var(--muted); cursor:pointer; transition:all .15s; font-family:inherit; }
    .tab-btn.active { background:var(--accent); color:white; }
    .tab-btn:hover:not(.active) { background:#f3f4f6; color:var(--ink); }
    .tab-panel { display:none; }
    .tab-panel.active { display:block; }

    /* ── Modal ── */
    .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:900; align-items:center; justify-content:center; padding:1rem; }
    .modal-bg.open { display:flex; }
    .modal { background:white; border-radius:1rem; padding:1.75rem; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 20px 60px rgba(0,0,0,.15); }
    .modal-close { position:absolute; top:1rem; right:1rem; background:#f1f5f9; border:none; cursor:pointer; color:var(--muted); width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.9rem; }
    .modal-close:hover { background:#fee2e2; color:#dc2626; }
    .modal-title { font-size:1rem; font-weight:700; color:var(--ink); margin-bottom:1.25rem; }

    /* ── Flash ── */
    .flash-ok  { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; border-radius:.5rem; padding:.75rem 1rem; font-size:.8rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:.5rem; }
    .flash-err { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; border-radius:.5rem; padding:.75rem 1rem; font-size:.8rem; margin-bottom:1.25rem; }

    /* Scope checkboxes */
    .scope-grid { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; }
    .scope-item { display:flex; align-items:center; gap:.5rem; padding:.5rem .75rem; background:#f9fafb; border:1px solid var(--border); border-radius:.375rem; cursor:pointer; transition:all .15s; }
    .scope-item:hover { border-color:#93c5fd; background:#eff6ff; }
    .scope-item input:checked + span { font-weight:600; color:var(--ink); }
    .scope-item span { font-size:.78rem; color:var(--ink-mid); }
</style>
@endpush

@section('content')

{{-- Flash --}}
@if(session('success'))
    <div class="flash-ok">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-err">⚠️ {{ session('error') }}</div>
@endif

{{-- En-tête --}}
<div style="margin-bottom:1.25rem;">
    <h1 style="font-size:1.125rem;font-weight:700;color:#111827;">🔄 Transfert & Consultation inter-établissements</h1>
    <p style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">Consultez le dossier d'un apprenant venant d'un autre établissement</p>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-chip">
        <div class="stat-val" style="color:#f59e0b;">{{ $stats['recues_pending'] }}</div>
        <div class="stat-lbl">Demandes reçues en attente</div>
    </div>
    <div class="stat-chip">
        <div class="stat-val" style="color:#3b82f6;">{{ $stats['envoyees_pending'] }}</div>
        <div class="stat-lbl">Mes demandes en attente</div>
    </div>
    <div class="stat-chip">
        <div class="stat-val" style="color:#10b981;">{{ $stats['approuvees'] }}</div>
        <div class="stat-lbl">Dossiers consultables</div>
    </div>
</div>

<div class="tr-grid">

    {{-- ════════════════════════════════════
         COLONNE GAUCHE — Recherche
    ════════════════════════════════════ --}}
    <div>
        <div class="search-panel">
            <h2>🔍 Rechercher un apprenant</h2>
            <p>Saisissez le matricule de l'apprenant et sélectionnez l'établissement source</p>

            <div style="display:flex;flex-direction:column;gap:.75rem;">
                <div>
                    <label class="f-label">Matricule de l'apprenant *</label>
                    <input type="text" id="searchMatricule" class="f-field"
                           placeholder="Ex : APP-2023-001" autocomplete="off">
                </div>
                <div>
                    <label class="f-label">Établissement source (optionnel)</label>
                    <select id="searchInstitution" class="f-field">
                        <option value="">— Tous les établissements —</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-search" onclick="doSearch()" id="searchBtn">
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    Rechercher
                </button>
            </div>

            {{-- Résultats --}}
            <div id="searchResults"></div>
        </div>
    </div>

    {{-- ════════════════════════════════════
         COLONNE DROITE — Demandes
    ════════════════════════════════════ --}}
    <div>
        <div class="card">
            <div class="card-head">
                <span class="card-title">📋 Mes demandes</span>
            </div>
            <div class="card-body">
                <div class="tabs">
                    <button class="tab-btn active" onclick="switchTab('envoyees', this)">
                        📤 Envoyées
                        @if($stats['envoyees_pending'] > 0)
                            <span style="background:#fef3c7;color:#92400e;font-size:.62rem;padding:.1rem .4rem;border-radius:99px;margin-left:.25rem;">{{ $stats['envoyees_pending'] }}</span>
                        @endif
                    </button>
                    <button class="tab-btn" onclick="switchTab('recues', this)">
                        📥 Reçues
                        @if($stats['recues_pending'] > 0)
                            <span style="background:#fee2e2;color:#991b1b;font-size:.62rem;padding:.1rem .4rem;border-radius:99px;margin-left:.25rem;">{{ $stats['recues_pending'] }}</span>
                        @endif
                    </button>
                </div>

                {{-- Demandes ENVOYÉES --}}
                <div class="tab-panel active" id="tab-envoyees">
                    @if($demandesEnvoyees->isEmpty())
                        <div style="text-align:center;padding:2.5rem 1rem;color:var(--muted);">
                            <div style="font-size:2rem;opacity:.3;margin-bottom:.5rem;">📤</div>
                            <p style="font-size:.8rem;">Vous n'avez encore envoyé aucune demande.</p>
                        </div>
                    @else
                        <div style="overflow-x:auto;">
                            <table class="t-table">
                                <thead>
                                    <tr>
                                        <th>Apprenant</th>
                                        <th>École source</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demandesEnvoyees as $dem)
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;color:#111827;">
                                                {{ $dem->apprenant?->prenom }} {{ $dem->apprenant?->nom }}
                                            </div>
                                            <div style="font-size:.68rem;color:#9ca3af;font-family:monospace;">
                                                {{ $dem->apprenant?->matricule }}
                                            </div>
                                        </td>
                                        <td style="font-size:.75rem;">{{ $dem->institutionSource?->name ?? '—' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $dem->statut }}">
                                                {{ $dem->statut_label }}
                                            </span>
                                        </td>
                                        <td style="font-size:.72rem;color:#9ca3af;">
                                            {{ $dem->created_at->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:.375rem;align-items:center;">
                                                @if(in_array($dem->statut, ['approved','completed']))
                                                    <a href="{{ route('admin.transfer.dossier', $dem) }}"
                                                       class="btn-primary" style="font-size:.72rem;padding:.35rem .75rem;">
                                                        📂 Dossier
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.transfer.show', $dem) }}"
                                                   class="btn-ghost" style="font-size:.72rem;padding:.35rem .75rem;">
                                                    👁
                                                </a>
                                                @if($dem->statut === 'pending')
                                                    <form method="POST" action="{{ route('admin.transfer.destroy', $dem) }}"
                                                          onsubmit="return confirm('Annuler cette demande ?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn-danger" style="padding:.3rem .6rem;">✕</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top:.875rem;">{{ $demandesEnvoyees->links() }}</div>
                    @endif
                </div>

                {{-- Demandes REÇUES --}}
                <div class="tab-panel" id="tab-recues">
                    @if($demandesRecues->isEmpty())
                        <div style="text-align:center;padding:2.5rem 1rem;color:var(--muted);">
                            <div style="font-size:2rem;opacity:.3;margin-bottom:.5rem;">📥</div>
                            <p style="font-size:.8rem;">Aucune demande reçue pour le moment.</p>
                        </div>
                    @else
                        <div style="overflow-x:auto;">
                            <table class="t-table">
                                <thead>
                                    <tr>
                                        <th>Apprenant</th>
                                        <th>Demandeur</th>
                                        <th>Données demandées</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demandesRecues as $dem)
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;color:#111827;">
                                                {{ $dem->apprenant?->prenom }} {{ $dem->apprenant?->nom }}
                                            </div>
                                            <div style="font-size:.68rem;color:#9ca3af;font-family:monospace;">
                                                {{ $dem->apprenant?->matricule }}
                                            </div>
                                        </td>
                                        <td style="font-size:.72rem;">
                                            <div style="font-weight:600;">{{ $dem->institutionDest?->name }}</div>
                                            <div style="color:#9ca3af;">{{ $dem->created_at->format('d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            <div style="display:flex;flex-wrap:wrap;gap:.2rem;">
                                                @foreach($dem->scope ?? [] as $s)
                                                    <span style="background:#f3f4f6;color:#374151;font-size:.62rem;padding:.1rem .4rem;border-radius:4px;">{{ $s }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $dem->statut }}">
                                                {{ $dem->statut_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:.375rem;align-items:center;">
                                                @if($dem->statut === 'pending')
                                                    <form method="POST" action="{{ route('admin.transfer.approve', $dem) }}">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn-primary" style="font-size:.72rem;padding:.35rem .75rem;background:#10b981;"
                                                                onclick="return confirm('Approuver cette demande ? L\'établissement aura 72h pour consulter.')">
                                                            ✅ Approuver
                                                        </button>
                                                    </form>
                                                    <button class="btn-danger" onclick="openRejectModal({{ $dem->id }})"
                                                            style="padding:.35rem .75rem;">
                                                        ✕ Refuser
                                                    </button>
                                                @endif
                                                <a href="{{ route('admin.transfer.show', $dem) }}"
                                                   class="btn-ghost" style="font-size:.72rem;padding:.35rem .75rem;">
                                                    👁
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top:.875rem;">{{ $demandesRecues->links() }}</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL DEMANDE DE CONSULTATION ═══ --}}
<div class="modal-bg" id="requestModal">
    <div class="modal">
        <button class="modal-close" onclick="closeModal('requestModal')">✕</button>
        <div class="modal-title">📋 Demander la consultation du dossier</div>

        <form method="POST" action="{{ route('admin.transfer.store') }}">
            @csrf
            <input type="hidden" name="apprenant_id" id="modalApprenantId">

            <div id="modalApprenantInfo" style="background:#f9fafb;border:1px solid var(--border);border-radius:.5rem;padding:.875rem;margin-bottom:1.25rem;font-size:.8rem;"></div>

            <div style="margin-bottom:1rem;">
                <label class="f-label">Données à consulter *</label>
                <div class="scope-grid">
                    @foreach(\App\Models\TransferRequest::scopeLabels() as $key => $label)
                    <label class="scope-item">
                        <input type="checkbox" name="scope[]" value="{{ $key }}"
                               {{ $key === 'identity' ? 'checked disabled' : '' }}
                               style="accent-color:#1f2937;flex-shrink:0;">
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <p style="font-size:.68rem;color:#9ca3af;margin-top:.4rem;">* L'identité est toujours incluse.</p>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label class="f-label">Motif de la demande *</label>
                <textarea name="motif" class="f-field" rows="3" required
                          placeholder="Ex : Inscription de l'apprenant dans notre établissement, vérification des antécédents..."></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:.75rem;">
                <button type="button" class="btn-ghost" onclick="closeModal('requestModal')">Annuler</button>
                <button type="submit" class="btn-primary">📤 Envoyer la demande</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ MODAL REFUS ═══ --}}
<div class="modal-bg" id="rejectModal">
    <div class="modal" style="max-width:420px;">
        <button class="modal-close" onclick="closeModal('rejectModal')">✕</button>
        <div class="modal-title">✕ Refuser la demande</div>
        <form method="POST" id="rejectForm">
            @csrf @method('PATCH')
            <div style="margin-bottom:1.25rem;">
                <label class="f-label">Motif du refus *</label>
                <textarea name="motif_refus" class="f-field" rows="3" required
                          placeholder="Expliquez pourquoi vous refusez cette demande..."></textarea>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:.75rem;">
                <button type="button" class="btn-ghost" onclick="closeModal('rejectModal')">Annuler</button>
                <button type="submit" style="background:#ef4444;color:white;font-weight:700;border:none;padding:.6rem 1.25rem;border-radius:.5rem;cursor:pointer;font-size:.8rem;">
                    Confirmer le refus
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const SEARCH_URL   = "{{ route('admin.transfer.search') }}";
const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

/* ── Tabs ── */
function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

/* ── Modals ── */
function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
document.querySelectorAll('.modal-bg').forEach(bg => bg.addEventListener('click', e => { if (e.target === bg) closeModal(bg.id); }));

/* ── Recherche AJAX ── */
async function doSearch() {
    const matricule    = document.getElementById('searchMatricule').value.trim();
    const institutionId= document.getElementById('searchInstitution').value;
    const resultsDiv   = document.getElementById('searchResults');
    const btn          = document.getElementById('searchBtn');

    if (! matricule) { resultsDiv.innerHTML = '<p style="color:#ef4444;font-size:.78rem;margin-top:.75rem;">Veuillez saisir un matricule.</p>'; return; }

    btn.disabled = true;
    btn.innerHTML = '⏳ Recherche en cours…';
    resultsDiv.innerHTML = '';

    const params = new URLSearchParams({ matricule });
    if (institutionId) params.append('institution_source', institutionId);

    try {
        const res  = await fetch(SEARCH_URL + '?' + params, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.found === 0) {
            resultsDiv.innerHTML = `<div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:.5rem;padding:.875rem;font-size:.8rem;margin-top:.75rem;">
                Aucun apprenant trouvé avec le matricule <strong>${matricule}</strong> dans les autres établissements.
            </div>`;
        } else {
            let html = `<div style="font-size:.72rem;color:var(--muted);margin-top:.875rem;margin-bottom:.5rem;">${data.found} résultat(s) trouvé(s)</div>`;
            data.apprenants.forEach(a => {
                html += `
                <div class="result-card">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;">
                        <div>
                            <div class="result-name">${a.prenom} ${a.nom}</div>
                            <div class="result-meta">
                                🎓 ${a.matricule}
                                ${a.date_naissance ? ' · 📅 ' + a.date_naissance : ''}
                                ${a.sexe ? ' · ' + (a.sexe === 'M' ? '👨' : '👩') : ''}
                            </div>
                            <div class="result-meta" style="margin-top:.2rem;">
                                ${a.classe ? '🏫 ' + a.classe + ' · ' : ''}
                                ${a.niveau ? a.niveau : ''}
                                ${a.annee_academique ? ' (' + a.annee_academique + ')' : ''}
                            </div>
                            <div class="result-school">${a.institution}</div>
                        </div>
                        <button class="btn-primary" style="flex-shrink:0;font-size:.72rem;padding:.4rem .875rem;"
                                onclick="openRequestModal(${a.id}, '${a.prenom} ${a.nom}', '${a.matricule}', '${a.institution}')">
                            📋 Demander le dossier
                        </button>
                    </div>
                </div>`;
            });
            resultsDiv.innerHTML = html;
        }
    } catch (e) {
        resultsDiv.innerHTML = '<p style="color:#ef4444;font-size:.78rem;margin-top:.75rem;">Erreur lors de la recherche.</p>';
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg> Rechercher`;
    }
}

/* ── Entrée sur "Enter" ── */
document.getElementById('searchMatricule').addEventListener('keydown', e => {
    if (e.key === 'Enter') doSearch();
});

/* ── Ouvrir modal demande ── */
function openRequestModal(id, nom, matricule, institution) {
    document.getElementById('modalApprenantId').value = id;
    document.getElementById('modalApprenantInfo').innerHTML = `
        <strong>Apprenant :</strong> ${nom}<br>
        <strong>Matricule :</strong> <span style="font-family:monospace;">${matricule}</span><br>
        <strong>École actuelle :</strong> ${institution}
    `;
    openModal('requestModal');
}

/* ── Ouvrir modal refus ── */
function openRejectModal(transferId) {
    document.getElementById('rejectForm').action = `/admin/transfer/${transferId}/reject`;
    openModal('rejectModal');
}
</script>
@endpush