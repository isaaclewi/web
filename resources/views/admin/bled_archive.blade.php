{{-- ================================================================
    BLED — Bureau de Liaison et d'Enregistrement des Données
    resources/views/admin/bled.blade.php
    Route : GET /admin/bled  →  BledController@index
    ================================================================ --}}
@extends('admin.master')

@section('title', 'BLED — Archives')

@push('styles')
<style>
    :root {
        --bled-navy:    #0f172a;
        --bled-slate:   #1e293b;
        --bled-steel:   #334155;
        --bled-muted:   #64748b;
        --bled-light:   #f1f5f9;
        --bled-white:   #ffffff;
        --bled-gold:    #d97706;
        --bled-gold-l:  #fef3c7;
        --bled-green:   #059669;
        --bled-green-l: #d1fae5;
        --bled-blue:    #2563eb;
        --bled-blue-l:  #dbeafe;
        --bled-red:     #dc2626;
        --bled-red-l:   #fee2e2;
    }

    .bled-wrap { font-family:'Inter',sans-serif; color:var(--bled-navy); }

    /* ── Hero ── */
    .bled-hero {
        background:var(--bled-navy); color:#fff;
        border-radius:1rem; padding:2rem 2.5rem;
        margin-bottom:1.5rem; position:relative; overflow:hidden;
    }
    .bled-hero::before {
        content:''; position:absolute; inset:0;
        background:radial-gradient(ellipse at 80% 20%,rgba(37,99,235,.18) 0%,transparent 60%);
        pointer-events:none;
    }
    .bled-hero h1 { font-size:1.6rem; font-weight:700; letter-spacing:-.02em; }
    .bled-hero p  { font-size:.875rem; color:#94a3b8; margin-top:.35rem; }
    .bled-hero-badge {
        display:inline-flex; align-items:center; gap:.4rem;
        background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);
        padding:.3rem .75rem; border-radius:99px; font-size:.72rem; font-weight:600;
        letter-spacing:.06em; text-transform:uppercase; color:#cbd5e1; margin-bottom:1rem;
    }

    /* ── KPIs ── */
    .bled-kpi-row {
        display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
        gap:1rem; margin-bottom:1.5rem;
    }
    .bled-kpi { background:#fff; border:1px solid #e2e8f0; border-radius:.75rem; padding:1.25rem 1.5rem; }
    .bled-kpi-label { font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--bled-muted); margin-bottom:.4rem; }
    .bled-kpi-val   { font-size:1.8rem; font-weight:700; color:var(--bled-navy); line-height:1; }
    .bled-kpi-sub   { font-size:.75rem; color:var(--bled-muted); margin-top:.3rem; }

    /* ── Toolbar ── */
    .bled-toolbar { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .bled-toolbar input,
    .bled-toolbar select {
        border:1px solid #e2e8f0; border-radius:.5rem;
        padding:.5rem .875rem; font-size:.85rem;
        background:#fff; color:var(--bled-navy); outline:none; transition:border-color .15s;
    }
    .bled-toolbar input:focus,
    .bled-toolbar select:focus { border-color:#94a3b8; }
    .bled-toolbar input { flex:1; min-width:200px; }

    /* ── Boutons ── */
    .bled-btn {
        display:inline-flex; align-items:center; gap:.4rem;
        padding:.5rem 1rem; border-radius:.5rem; font-size:.82rem; font-weight:600;
        border:none; cursor:pointer; transition:all .15s; text-decoration:none;
    }
    .bled-btn-primary { background:var(--bled-navy); color:#fff; }
    .bled-btn-primary:hover { background:var(--bled-slate); }
    .bled-btn-ghost { background:#f1f5f9; color:var(--bled-navy); }
    .bled-btn-ghost:hover { background:#e2e8f0; }
    .bled-btn-danger { background:var(--bled-red-l); color:var(--bled-red); }
    .bled-btn-danger:hover { background:#fecaca; }
    .bled-btn-success { background:var(--bled-green-l); color:var(--bled-green); }
    .bled-btn-success:hover { background:#a7f3d0; }
    .bled-btn-gold { background:var(--bled-gold-l); color:var(--bled-gold); }
    .bled-btn-gold:hover { background:#fde68a; }

    /* ── Année pills ── */
    .bled-years { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .bled-year-pill {
        padding:.35rem .9rem; border-radius:99px; font-size:.78rem; font-weight:600;
        border:1.5px solid #e2e8f0; cursor:pointer; color:var(--bled-muted);
        background:#fff; transition:all .15s; text-decoration:none;
    }
    .bled-year-pill:hover, .bled-year-pill.active {
        background:var(--bled-navy); color:#fff; border-color:var(--bled-navy);
    }

    /* ── Section card ── */
    .bled-section { background:#fff; border:1px solid #e2e8f0; border-radius:.875rem; overflow:hidden; margin-bottom:1.25rem; }
    .bled-section-hd {
        display:flex; align-items:center; justify-content:space-between;
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
    }
    .bled-section-title { font-size:.9rem; font-weight:700; display:flex; align-items:center; gap:.5rem; }
    .bled-icon { width:30px; height:30px; border-radius:.4rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

    /* ── Table ── */
    .bled-table-outer { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .bled-table { width:100%; border-collapse:collapse; font-size:.82rem; min-width:600px; }
    .bled-table th {
        padding:.65rem 1.25rem; background:#f8fafc; font-weight:600; text-align:left;
        color:var(--bled-muted); font-size:.72rem; text-transform:uppercase;
        letter-spacing:.04em; border-bottom:1px solid #e2e8f0;
    }
    .bled-table td { padding:.8rem 1.25rem; border-bottom:1px solid #f8fafc; vertical-align:middle; color:var(--bled-slate); }
    .bled-table tr:last-child td { border-bottom:none; }
    .bled-table tr:hover td { background:#f8fafc; }

    /* ── Badge ── */
    .bled-badge { display:inline-block; padding:.2rem .55rem; border-radius:99px; font-size:.68rem; font-weight:600; }
    .bled-badge-blue  { background:var(--bled-blue-l);  color:var(--bled-blue); }
    .bled-badge-green { background:var(--bled-green-l); color:var(--bled-green); }
    .bled-badge-gold  { background:var(--bled-gold-l);  color:var(--bled-gold); }
    .bled-badge-red   { background:var(--bled-red-l);   color:var(--bled-red); }
    .bled-badge-slate { background:#f1f5f9; color:var(--bled-steel); }

    /* ── Tabs ── */
    .bled-tabs {
        display:flex; gap:0; border-bottom:1px solid #e2e8f0;
        overflow-x:auto; scrollbar-width:none;
    }
    .bled-tabs::-webkit-scrollbar { display:none; }
    .bled-tab {
        padding:.75rem 1.25rem; font-size:.82rem; font-weight:600; color:var(--bled-muted);
        cursor:pointer; border-bottom:2px solid transparent; transition:all .15s;
        white-space:nowrap; background:none; border-top:none; border-left:none; border-right:none;
    }
    .bled-tab.active { color:var(--bled-navy); border-bottom-color:var(--bled-navy); }
    .bled-tab-panel { display:none; padding:0; }
    .bled-tab-panel.active { display:block; }

    /* ── Empty ── */
    .bled-empty { padding:3rem 2rem; text-align:center; color:var(--bled-muted); font-size:.85rem; }

    /* ── Flash ── */
    .bled-flash { padding:.75rem 1.25rem; border-radius:.5rem; margin-bottom:1rem; font-size:.85rem; font-weight:500; }
    .bled-flash-success { background:var(--bled-green-l); color:var(--bled-green); }
    .bled-flash-error   { background:var(--bled-red-l);   color:var(--bled-red); }

    /* ── Search highlight ── */
    .bled-highlight { background:#fef3c7; border-radius:.2rem; padding:0 .1rem; }

    /* ── No-result message ── */
    .bled-no-result { display:none; padding:2.5rem; text-align:center; color:var(--bled-muted); font-size:.85rem; }
    .bled-no-result.show { display:block; }

    /* ── Modal ── */
    .bled-modal-backdrop {
        display:none; position:fixed; inset:0;
        background:rgba(15,23,42,.5); z-index:200; align-items:center; justify-content:center;
    }
    .bled-modal-backdrop.show { display:flex; }
    .bled-modal {
        background:#fff; border-radius:1rem;
        width:min(540px,94vw); max-height:88vh; overflow-y:auto; padding:1.75rem;
    }
    .bled-modal h2 { font-size:1.1rem; font-weight:700; margin-bottom:1.25rem; }
    .bled-form-row { margin-bottom:1rem; }
    .bled-form-row label { display:block; font-size:.78rem; font-weight:600; color:var(--bled-steel); margin-bottom:.35rem; }
    .bled-form-row input,
    .bled-form-row select,
    .bled-form-row textarea {
        width:100%; border:1px solid #e2e8f0; border-radius:.5rem;
        padding:.55rem .875rem; font-size:.85rem; color:var(--bled-navy); outline:none; transition:border-color .15s;
    }
    .bled-form-row input:focus,
    .bled-form-row select:focus,
    .bled-form-row textarea:focus { border-color:#94a3b8; }
    .bled-form-row textarea { resize:vertical; min-height:80px; }
    .bled-form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .bled-modal-foot {
        display:flex; gap:.75rem; justify-content:flex-end;
        margin-top:1.5rem; border-top:1px solid #f1f5f9; padding-top:1.25rem;
    }

    /* ── Responsive ── */
    @media(max-width:1024px) {
        .bled-kpi-row { grid-template-columns:repeat(2,1fr); }
    }
    @media(max-width:768px) {
        .bled-hero { padding:1.25rem; border-radius:.75rem; }
        .bled-hero h1 { font-size:1.2rem; }
        .bled-toolbar { flex-direction:column; align-items:stretch; }
        .bled-toolbar input, .bled-toolbar select, .bled-toolbar .bled-btn { width:100%; }
        .bled-section-hd { flex-direction:column; align-items:flex-start; gap:.75rem; }
        .bled-form-grid { grid-template-columns:1fr; }
        .bled-modal-foot { flex-direction:column-reverse; }
        .bled-modal-foot .bled-btn { width:100%; justify-content:center; }
    }
    @media(max-width:480px) {
        .bled-kpi-row { grid-template-columns:1fr 1fr; gap:.65rem; }
        .bled-kpi-val { font-size:1.4rem; }
        .bled-modal { padding:1.25rem; }
    }
</style>
@endpush

@section('content')
@php
    $anneeActive = request('annee', $institution->academic_year ?? date('Y').'-'.(date('Y') + 1));
    $categorie   = request('cat', 'apprenants');
    $search      = request('q', '');
    $typeExport  = request('type_export', '');
@endphp

<div class="bled-wrap">

    {{-- Flash --}}
    @if(session('success'))
        <div class="bled-flash bled-flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bled-flash bled-flash-error">{{ session('error') }}</div>
    @endif

    {{-- ══ HERO ══ --}}
    <div class="bled-hero">
        <div class="bled-hero-badge">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            BLED — Bureau de Liaison et d'Enregistrement des Données
        </div>
        <h1>Archives de l'établissement</h1>
        <p>Toutes les données archivées, consultables et téléchargeables par année académique.</p>
        <div style="display:flex;gap:.75rem;margin-top:1.25rem;flex-wrap:wrap;">
    <button class="bled-btn" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.18);"
        onclick="document.getElementById('modalCreerArchive').classList.add('show')">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Créer une archive
    </button>
 
    <a href="{{ route('admin.bled.export.global', ['annee' => $anneeActive]) }}"
       class="bled-btn" style="background:rgba(255,255,255,.08);color:#cbd5e1;border:1px solid rgba(255,255,255,.12);">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Tout exporter CSV ({{ $anneeActive }})
    </a>
 
    {{-- ✅ NOUVEAU BOUTON PDF --}}
    <a href="{{ route('admin.bled.pdf.filtres', ['annee' => $anneeActive, 'cat' => $categorie]) }}"
       class="bled-btn" style="background:rgba(220,38,38,.25);color:#fca5a5;border:1px solid rgba(220,38,38,.35);">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        Export PDF / Impression
    </a>
</div>
    </div>

    {{-- ══ KPIs ══ --}}
    <div class="bled-kpi-row">
        <div class="bled-kpi">
            <div class="bled-kpi-label">Archives totales</div>
            <div class="bled-kpi-val">{{ $statsGlobaux['total_archives'] ?? 0 }}</div>
            <div class="bled-kpi-sub">toutes années confondues</div>
        </div>
        <div class="bled-kpi">
            <div class="bled-kpi-label">Années archivées</div>
            <div class="bled-kpi-val">{{ $statsGlobaux['nb_annees'] ?? 0 }}</div>
            <div class="bled-kpi-sub">depuis la création</div>
        </div>
        <div class="bled-kpi">
            <div class="bled-kpi-label">Apprenants archivés</div>
            <div class="bled-kpi-val">{{ number_format($statsGlobaux['total_apprenants'] ?? 0) }}</div>
            <div class="bled-kpi-sub">toutes promotions</div>
        </div>
        <div class="bled-kpi">
            <div class="bled-kpi-label">Dernière archive</div>
            <div class="bled-kpi-val" style="font-size:1rem;padding-top:.35rem;">
                {{ $statsGlobaux['derniere_archive'] ?? '—' }}
            </div>
            <div class="bled-kpi-sub">année {{ $statsGlobaux['derniere_annee'] ?? '—' }}</div>
        </div>
    </div>

    {{-- ══ SÉLECTEUR D'ANNÉE ══ --}}
    <div style="margin-bottom:.5rem;">
        <span style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--bled-muted);">Filtrer par année académique</span>
    </div>
    <div class="bled-years">
        @forelse($anneesDispos as $annee)
            <a href="{{ route('admin.bled.index', ['annee' => $annee, 'cat' => $categorie, 'q' => $search]) }}"
               class="bled-year-pill {{ $annee === $anneeActive ? 'active' : '' }}">
                {{ $annee }}
            </a>
        @empty
            <span class="bled-year-pill active">{{ $anneeActive }}</span>
        @endforelse
    </div>

    {{-- ══ TOOLBAR — Recherche + Filtre ══ --}}
    <form method="GET" action="{{ route('admin.bled.index') }}" class="bled-toolbar" id="bledSearchForm">
        <input type="hidden" name="annee" value="{{ $anneeActive }}">
        <input type="hidden" name="cat"   value="{{ $categorie }}">

        <input type="text" name="q" id="bledSearchInput"
               value="{{ $search }}"
               placeholder="Rechercher nom, matricule, spécialité…"
               autocomplete="off">

        <select name="type_export" onchange="this.form.submit()">
            <option value="">Tous les types</option>
            <option value="annuel"      {{ $typeExport === 'annuel'      ? 'selected' : '' }}>Annuel</option>
            <option value="trimestriel" {{ $typeExport === 'trimestriel' ? 'selected' : '' }}>Trimestriel</option>
            <option value="manuel"      {{ $typeExport === 'manuel'      ? 'selected' : '' }}>Manuel</option>
        </select>

        <button type="submit" class="bled-btn bled-btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            Filtrer
        </button>
        <a href="{{ route('admin.bled.index', ['annee' => $anneeActive, 'cat' => $categorie]) }}"
           class="bled-btn bled-btn-ghost">Réinitialiser</a>
    </form>

    {{-- Résultat de la recherche --}}
    @if($search)
        <div style="margin-bottom:.75rem;font-size:.82rem;color:var(--bled-muted);">
            Résultats pour <strong style="color:var(--bled-navy);">« {{ $search }} »</strong>
            — <a href="{{ route('admin.bled.index', ['annee' => $anneeActive, 'cat' => $categorie]) }}" style="color:var(--bled-blue);">Effacer</a>
        </div>
    @endif

    {{-- ══ ONGLETS + PANNEAUX ══ --}}
    <div class="bled-section">
        <div class="bled-tabs" id="bledTabs">
            @php
                $tabsMeta = [
                    'apprenants'   => 'Apprenants',
                    'enseignants'  => 'Enseignants',
                    'notes'        => 'Notes & Bulletins',
                    'finances'     => 'Finances',
                    'disciplinaire'=> 'Disciplinaire',
                    'classes'      => 'Classes',
                    'planning'     => 'Planning / EDT',
                    'staff'        => 'Staff admin.',
                ];
            @endphp
            @foreach($tabsMeta as $key => $label)
                <button class="bled-tab {{ $categorie === $key ? 'active' : '' }}"
                        onclick="switchTab('{{ $key }}', event)">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- ══ PANNEAU : APPRENANTS ══ --}}
        <div id="tab-apprenants" class="bled-tab-panel {{ $categorie === 'apprenants' ? 'active' : '' }}">
            <div class="bled-section-hd">
                <div class="bled-section-title">
                    <div class="bled-icon" style="background:#dbeafe;">
                        <svg width="16" height="16" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    Apprenants — {{ $anneeActive }}
                    <span class="bled-badge bled-badge-blue">{{ ($archivesApprenants instanceof \Illuminate\Pagination\AbstractPaginator) ? $archivesApprenants->total() : (is_countable($archivesApprenants) ? count($archivesApprenants) : 0) }} entrées</span>
                </div>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.bled.export', ['cat' => 'apprenants', 'annee' => $anneeActive, 'q' => $search]) }}"
                       class="bled-btn bled-btn-gold" style="font-size:.78rem;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        CSV
                    </a>
                    <a href="{{ route('admin.bled.export', ['cat' => 'apprenants', 'annee' => $anneeActive, 'format' => 'pdf']) }}"
                       class="bled-btn bled-btn-ghost" style="font-size:.78rem;">PDF</a>
                    <a href="{{ route('admin.bled.pdf.filtres', ['cat' => 'apprenants', 'annee' => $anneeActive]) }}"
   class="bled-btn" style="background:#fee2e2;color:#dc2626;font-size:.78rem;">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
    </svg>
    PDF
</a>
                </div>
            </div>
            <div class="bled-table-outer">
                <table class="bled-table">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom complet</th>
                            <th>Sexe</th>
                            <th>Niveau</th>
                            <th>Filière</th>
                            <th>Classe</th>
                            <th>Statut</th>
                            <th>Année</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivesApprenants ?? [] as $ap)
                            <tr>
                                <td><code style="font-size:.78rem;background:#f8fafc;padding:.1rem .4rem;border-radius:.3rem;">{{ $ap->matricule }}</code></td>
                                <td style="font-weight:600;">{{ $ap->prenom }} {{ $ap->nom }}</td>
                                <td>{{ $ap->sexe === 'M' ? 'Masculin' : ($ap->sexe === 'F' ? 'Féminin' : '—') }}</td>
                                <td>{{ optional($ap->niveau)->name ?? '—' }}</td>
                                <td>{{ optional($ap->filiere)->name ?? '—' }}</td>
                                <td>{{ optional($ap->classe)->name ?? '—' }}</td>
                                <td>
                                    @if($ap->status)
                                        <span class="bled-badge bled-badge-green">Actif</span>
                                    @else
                                        <span class="bled-badge bled-badge-slate">Inactif</span>
                                    @endif
                                </td>
                                <td>{{ $ap->annee_academique }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="bled-empty">
                                @if($search)
                                    Aucun apprenant correspondant à « {{ $search }} » pour {{ $anneeActive }}.
                                @else
                                    Aucun apprenant archivé pour cette période.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivesApprenants instanceof \Illuminate\Pagination\LengthAwarePaginator && $archivesApprenants->hasPages())
                <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
                    {{ $archivesApprenants->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- ══ PANNEAU : ENSEIGNANTS ══ --}}
        <div id="tab-enseignants" class="bled-tab-panel {{ $categorie === 'enseignants' ? 'active' : '' }}">
            <div class="bled-section-hd">
                <div class="bled-section-title">
                    <div class="bled-icon" style="background:#d1fae5;">
                        <svg width="16" height="16" fill="none" stroke="#059669" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    Enseignants — {{ $anneeActive }}
                    <span class="bled-badge bled-badge-green">{{ ($archivesEnseignants instanceof \Illuminate\Pagination\AbstractPaginator) ? $archivesEnseignants->total() : (is_countable($archivesEnseignants) ? count($archivesEnseignants) : 0) }} entrées</span>
                </div>
                <a href="{{ route('admin.bled.export', ['cat' => 'enseignants', 'annee' => $anneeActive, 'q' => $search]) }}"
                   class="bled-btn bled-btn-gold" style="font-size:.78rem;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    CSV
                </a>
            </div>
            <div class="bled-table-outer">
                <table class="bled-table">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom complet</th>
                            <th>Sexe</th>
                            <th>Spécialité</th>
                            <th>Contrat</th>
                            <th>Recrutement</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivesEnseignants ?? [] as $t)
                            <tr>
                                <td><code style="font-size:.78rem;background:#f8fafc;padding:.1rem .4rem;border-radius:.3rem;">{{ $t->matricule }}</code></td>
                                <td style="font-weight:600;">{{ $t->prenom }} {{ $t->nom }}</td>
                                <td>{{ $t->sexe === 'M' ? 'Masculin' : ($t->sexe === 'F' ? 'Féminin' : '—') }}</td>
                                <td>{{ $t->specialite ?? '—' }}</td>
                                <td>
                                    @php $cLabels = ['CDI'=>'CDI','CDD'=>'CDD','vacataire'=>'Vacataire','benevole'=>'Bénévole']; @endphp
                                    <span class="bled-badge bled-badge-slate">{{ $cLabels[$t->type_contrat] ?? ($t->type_contrat ?? '—') }}</span>
                                </td>
                                <td>{{ $t->date_recrutement ? \Carbon\Carbon::parse($t->date_recrutement)->format('d/m/Y') : '—' }}</td>
                                <td>
                                    @if($t->status)
                                        <span class="bled-badge bled-badge-green">Actif</span>
                                    @else
                                        <span class="bled-badge bled-badge-red">Inactif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="bled-empty">
                                @if($search)
                                    Aucun enseignant correspondant à « {{ $search }} ».
                                @else
                                    Aucun enseignant archivé.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivesEnseignants instanceof \Illuminate\Pagination\LengthAwarePaginator && $archivesEnseignants->hasPages())
                <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
                    {{ $archivesEnseignants->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- ══ PANNEAU : NOTES & BULLETINS ══ --}}
        <div id="tab-notes" class="bled-tab-panel {{ $categorie === 'notes' ? 'active' : '' }}">
            <div class="bled-section-hd">
                <div class="bled-section-title">
                    <div class="bled-icon" style="background:#fef3c7;">
                        <svg width="16" height="16" fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    Bulletins — {{ $anneeActive }}
                    <span class="bled-badge bled-badge-gold">{{ ($archivesBulletins instanceof \Illuminate\Pagination\AbstractPaginator) ? $archivesBulletins->total() : (is_countable($archivesBulletins) ? count($archivesBulletins) : 0) }} bulletins</span>
                </div>
                <a href="{{ route('admin.bled.export', ['cat' => 'bulletins', 'annee' => $anneeActive]) }}"
                   class="bled-btn bled-btn-gold" style="font-size:.78rem;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    CSV
                </a>
            </div>
            <div class="bled-table-outer">
                <table class="bled-table">
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Classe</th>
                            <th>Période</th>
                            <th>Moy. générale</th>
                            <th>Rang</th>
                            <th>Mention</th>
                            <th>Admis</th>
                            <th>Publié</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivesBulletins ?? [] as $b)
                            <tr>
                                <td style="font-weight:600;">{{ optional($b->apprenant)->prenom }} {{ optional($b->apprenant)->nom }}</td>
                                <td>{{ optional($b->classe)->name ?? '—' }}</td>
                                <td>
                                    @php
                                        $pLabels = ['trimestre1'=>'1er Trim.','trimestre2'=>'2ème Trim.','trimestre3'=>'3ème Trim.','semestre1'=>'1er Sem.','semestre2'=>'2ème Sem.','annuel'=>'Annuel'];
                                    @endphp
                                    <span class="bled-badge bled-badge-slate">{{ $pLabels[$b->periode] ?? $b->periode }}</span>
                                </td>
                                <td style="font-weight:700;">{{ number_format($b->moyenne_generale, 2) }}</td>
                                <td>{{ $b->rang ?? '—' }}<span style="color:var(--bled-muted);font-size:.75rem;">/{{ $b->effectif_classe }}</span></td>
                                <td>{{ $b->mention ?? '—' }}</td>
                                <td>
                                    @if($b->admis)
                                        <span class="bled-badge bled-badge-green">Admis</span>
                                    @else
                                        <span class="bled-badge bled-badge-red">Non admis</span>
                                    @endif
                                </td>
                                <td>
                                    @if($b->publie)
                                        <span class="bled-badge bled-badge-green">Oui</span>
                                    @else
                                        <span class="bled-badge bled-badge-slate">Non</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="bled-empty">Aucun bulletin archivé pour cette période.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivesBulletins instanceof \Illuminate\Pagination\LengthAwarePaginator && $archivesBulletins->hasPages())
                <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
                    {{ $archivesBulletins->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- ══ PANNEAU : FINANCES ══ --}}
        <div id="tab-finances" class="bled-tab-panel {{ $categorie === 'finances' ? 'active' : '' }}">
            <div class="bled-section-hd">
                <div class="bled-section-title">
                    <div class="bled-icon" style="background:#ede9fe;">
                        <svg width="16" height="16" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    </div>
                    Finances — {{ $anneeActive }}
                    <span class="bled-badge bled-badge-slate">{{ ($archivesFinances instanceof \Illuminate\Pagination\AbstractPaginator) ? $archivesFinances->total() : (is_countable($archivesFinances) ? count($archivesFinances) : 0) }} paiements</span>
                </div>
                <a href="{{ route('admin.bled.export', ['cat' => 'finances', 'annee' => $anneeActive, 'q' => $search]) }}"
                   class="bled-btn bled-btn-gold" style="font-size:.78rem;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    CSV
                </a>
            </div>
            <div class="bled-table-outer">
                <table class="bled-table">
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Mois</th>
                            <th>Dû (FCFA)</th>
                            <th>Payé (FCFA)</th>
                            <th>Reste (FCFA)</th>
                            <th>Statut</th>
                            <th>Mode</th>
                            <th>Date paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivesFinances ?? [] as $f)
                            <tr>
                                <td style="font-weight:600;">{{ optional($f->apprenant)->prenom }} {{ optional($f->apprenant)->nom }}</td>
                                <td>{{ $f->mois_label }}</td>
                                <td>{{ number_format($f->montant_du, 0, ',', ' ') }}</td>
                                <td>{{ number_format($f->montant_paye, 0, ',', ' ') }}</td>
                                <td style="{{ $f->montant_reste > 0 ? 'color:var(--bled-red);font-weight:600;' : '' }}">
                                    {{ number_format($f->montant_reste, 0, ',', ' ') }}
                                </td>
                                <td>
                                    @php
                                        $sc = match($f->statut) {'paye'=>'bled-badge-green','partiel'=>'bled-badge-gold',default=>'bled-badge-red'};
                                        $sl = match($f->statut) {'paye'=>'Payé','partiel'=>'Partiel',default=>'Impayé'};
                                    @endphp
                                    <span class="bled-badge {{ $sc }}">{{ $sl }}</span>
                                </td>
                                <td>{{ $f->mode_paiement ? ucfirst(str_replace('_',' ',$f->mode_paiement)) : '—' }}</td>
                                <td>{{ $f->date_paiement ? \Carbon\Carbon::parse($f->date_paiement)->format('d/m/Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="bled-empty">
                                @if($search)
                                    Aucun enregistrement financier pour « {{ $search }} ».
                                @else
                                    Aucun enregistrement financier archivé.
                                @endif
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivesFinances instanceof \Illuminate\Pagination\LengthAwarePaginator && $archivesFinances->hasPages())
                <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
                    {{ $archivesFinances->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- ══ PANNEAU : DISCIPLINAIRE ══ --}}
        <div id="tab-disciplinaire" class="bled-tab-panel {{ $categorie === 'disciplinaire' ? 'active' : '' }}">
            <div class="bled-section-hd">
                <div class="bled-section-title">
                    <div class="bled-icon" style="background:#fee2e2;">
                        <svg width="16" height="16" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    Suivi disciplinaire — {{ $anneeActive }}
                    <span class="bled-badge bled-badge-red">{{ ($archivesDisciplinaire instanceof \Illuminate\Pagination\AbstractPaginator) ? $archivesDisciplinaire->total() : (is_countable($archivesDisciplinaire) ? count($archivesDisciplinaire) : 0) }} incidents</span>
                </div>
                <a href="{{ route('admin.bled.export', ['cat' => 'disciplinaire', 'annee' => $anneeActive]) }}"
                   class="bled-btn bled-btn-gold" style="font-size:.78rem;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    CSV
                </a>
            </div>
            <div class="bled-table-outer">
                <table class="bled-table">
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Gravité</th>
                            <th>Sanction</th>
                            <th>Parents notifiés</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivesDisciplinaire ?? [] as $d)
                            <tr>
                                <td style="font-weight:600;">{{ optional($d->apprenant)->prenom }} {{ optional($d->apprenant)->nom }}</td>
                                <td>{{ isset($d->date_incident) ? $d->date_incident->format('d/m/Y') : '—' }}</td>
                                <td>{{ $d->type_label ?? $d->type ?? '—' }}</td>
                                <td>
                                    @php $gc = match((int)($d->gravite ?? 0)) {1=>'bled-badge-gold',2=>'bled-badge-red',default=>'bled-badge-red'}; @endphp
                                    <span class="bled-badge {{ $gc }}">{{ $d->gravite_label ?? $d->gravite ?? '—' }}</span>
                                </td>
                                <td>{{ $d->sanction_label ?? $d->sanction ?? '—' }}</td>
                                <td>
                                    @if($d->parents_notifies)
                                        <span class="bled-badge bled-badge-green">Oui</span>
                                    @else
                                        <span class="bled-badge bled-badge-slate">Non</span>
                                    @endif
                                </td>
                                <td><span class="bled-badge bled-badge-slate">{{ ucfirst($d->statut ?? '—') }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="bled-empty">Aucun incident disciplinaire archivé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivesDisciplinaire instanceof \Illuminate\Pagination\LengthAwarePaginator && $archivesDisciplinaire->hasPages())
                <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
                    {{ $archivesDisciplinaire->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- ══ PANNEAU : CLASSES ══ --}}
        <div id="tab-classes" class="bled-tab-panel {{ $categorie === 'classes' ? 'active' : '' }}">
            <div class="bled-section-hd">
                <div class="bled-section-title">
                    <div class="bled-icon" style="background:#e0f2fe;">
                        <svg width="16" height="16" fill="none" stroke="#0284c7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    Classes — {{ $anneeActive }}
                    <span class="bled-badge bled-badge-blue">{{ is_countable($archivesClasses) ? count($archivesClasses) : 0 }} classes</span>
                </div>
                <a href="{{ route('admin.bled.export', ['cat' => 'classes', 'annee' => $anneeActive]) }}"
                   class="bled-btn bled-btn-gold" style="font-size:.78rem;">CSV</a>
            </div>
            <div class="bled-table-outer">
                <table class="bled-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Code</th>
                            <th>Niveau</th>
                            <th>Filière</th>
                            <th>Effectif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivesClasses ?? [] as $c)
                            <tr>
                                <td style="font-weight:600;">{{ $c->name }}</td>
                                <td><code style="font-size:.78rem;background:#f8fafc;padding:.1rem .4rem;border-radius:.3rem;">{{ $c->code ?? '—' }}</code></td>
                                <td>{{ optional($c->niveau)->name ?? '—' }}</td>
                                <td>{{ optional($c->filiere)->name ?? '—' }}</td>
                                <td><span class="bled-badge bled-badge-blue">{{ $c->apprenants_count ?? 0 }} élèves</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="bled-empty">Aucune classe archivée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ══ PANNEAU : PLANNING / EDT ══ --}}
        <div id="tab-planning" class="bled-tab-panel {{ $categorie === 'planning' ? 'active' : '' }}">
            <div class="bled-section-hd">
                <div class="bled-section-title">
                    <div class="bled-icon" style="background:#ccfbf1;">
                        <svg width="16" height="16" fill="none" stroke="#0f766e" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    Emplois du temps — {{ $anneeActive }}
                    <span class="bled-badge bled-badge-slate">{{ ($archivesPlanning instanceof \Illuminate\Pagination\AbstractPaginator) ? $archivesPlanning->total() : (is_countable($archivesPlanning) ? count($archivesPlanning) : 0) }} séances</span>
                </div>
                <a href="{{ route('admin.bled.export', ['cat' => 'planning', 'annee' => $anneeActive]) }}"
                   class="bled-btn bled-btn-gold" style="font-size:.78rem;">CSV</a>
            </div>
            <div class="bled-table-outer">
                <table class="bled-table">
                    <thead>
                        <tr>
                            <th>Classe</th>
                            <th>Matière</th>
                            <th>Enseignant</th>
                            <th>Jour</th>
                            <th>Heure début</th>
                            <th>Heure fin</th>
                            <th>Type</th>
                            <th>Salle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivesPlanning ?? [] as $e)
                            <tr>
                                <td style="font-weight:600;">{{ optional($e->classe)->name ?? '—' }}</td>
                                <td>{{ optional($e->subject)->name ?? '—' }}</td>
                                <td>{{ $e->teacher ? $e->teacher->prenom.' '.$e->teacher->nom : '—' }}</td>
                                <td>{{ ucfirst($e->jour ?? '—') }}</td>
                                <td>{{ isset($e->heure_debut) ? substr($e->heure_debut, 0, 5) : '—' }}</td>
                                <td>{{ isset($e->heure_fin)   ? substr($e->heure_fin,   0, 5) : '—' }}</td>
                                <td><span class="bled-badge bled-badge-slate">{{ $e->type_label ?? $e->type ?? '—' }}</span></td>
                                <td>{{ $e->salle ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="bled-empty">Aucun planning archivé pour cette année.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivesPlanning instanceof \Illuminate\Pagination\LengthAwarePaginator && $archivesPlanning->hasPages())
                <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
                    {{ $archivesPlanning->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- ══ PANNEAU : STAFF ══ --}}
        <div id="tab-staff" class="bled-tab-panel {{ $categorie === 'staff' ? 'active' : '' }}">
            <div class="bled-section-hd">
                <div class="bled-section-title">
                    <div class="bled-icon" style="background:#fef3c7;">
                        <svg width="16" height="16" fill="none" stroke="#b45309" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    Staff administratif — {{ $anneeActive }}
                    <span class="bled-badge bled-badge-gold">{{ ($archivesStaff instanceof \Illuminate\Pagination\AbstractPaginator) ? $archivesStaff->total() : (is_countable($archivesStaff) ? count($archivesStaff) : 0) }} membres</span>
                </div>
                <a href="{{ route('admin.bled.export', ['cat' => 'staff', 'annee' => $anneeActive]) }}"
                   class="bled-btn bled-btn-gold" style="font-size:.78rem;">CSV</a>
            </div>
            <div class="bled-table-outer">
                <table class="bled-table">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom complet</th>
                            <th>Poste</th>
                            <th>Unité admin.</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archivesStaff ?? [] as $s)
                            <tr>
                                <td><code style="font-size:.78rem;background:#f8fafc;padding:.1rem .4rem;border-radius:.3rem;">{{ $s->matricule }}</code></td>
                                <td style="font-weight:600;">{{ $s->prenom }} {{ $s->nom }}</td>
                                <td>{{ $s->poste ?? '—' }}</td>
                                <td>{{ optional($s->administrativeUnit)->name ?? '—' }}</td>
                                <td>{{ $s->telephone ?? '—' }}</td>
                                <td>{{ $s->email ?? '—' }}</td>
                                <td>
                                    @if($s->status)
                                        <span class="bled-badge bled-badge-green">Actif</span>
                                    @else
                                        <span class="bled-badge bled-badge-red">Inactif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="bled-empty">Aucun membre du staff archivé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivesStaff instanceof \Illuminate\Pagination\LengthAwarePaginator && $archivesStaff->hasPages())
                <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
                    {{ $archivesStaff->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>{{-- fin .bled-section --}}

    {{-- ══ ARCHIVES CRÉÉES MANUELLEMENT ══ --}}
    <div class="bled-section">
        <div class="bled-section-hd">
            <div class="bled-section-title">
                <div class="bled-icon" style="background:#f1f5f9;">
                    <svg width="16" height="16" fill="none" stroke="#334155" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                Archives créées manuellement
                @if(isset($archives) && method_exists($archives, 'total'))
                    <span class="bled-badge bled-badge-slate">{{ $archives->total() }}</span>
                @endif
            </div>
            <button class="bled-btn bled-btn-primary"
                    onclick="document.getElementById('modalCreerArchive').classList.add('show')">
                + Nouvelle archive
            </button>
        </div>

        {{-- Filtre archives créées --}}
        <div style="padding:.75rem 1.5rem;border-bottom:1px solid #f1f5f9;">
            <form method="GET" action="{{ route('admin.bled.index') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
                <input type="hidden" name="annee" value="{{ $anneeActive }}">
                <input type="hidden" name="cat"   value="{{ $categorie }}">
                <input type="hidden" name="q"     value="{{ $search }}">
                <select name="type_export" style="border:1px solid #e2e8f0;border-radius:.4rem;padding:.4rem .75rem;font-size:.8rem;color:var(--bled-navy);" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="annuel"      {{ $typeExport === 'annuel'      ? 'selected' : '' }}>Annuel</option>
                    <option value="trimestriel" {{ $typeExport === 'trimestriel' ? 'selected' : '' }}>Trimestriel</option>
                    <option value="manuel"      {{ $typeExport === 'manuel'      ? 'selected' : '' }}>Manuel</option>
                </select>
                @if($typeExport)
                    <a href="{{ route('admin.bled.index', ['annee' => $anneeActive, 'cat' => $categorie, 'q' => $search]) }}"
                       style="font-size:.78rem;color:var(--bled-blue);">Effacer le filtre</a>
                @endif
            </form>
        </div>

        <div class="bled-table-outer">
            <table class="bled-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Année</th>
                        <th>Type</th>
                        <th>Créée le</th>
                        <th>Taille</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archives ?? [] as $archive)
                        <tr>
                            <td style="font-weight:600;">{{ $archive->nom }}</td>
                            <td>
                                @php
                                    $catColors = ['apprenants'=>'bled-badge-blue','enseignants'=>'bled-badge-green','bulletins'=>'bled-badge-gold','finances'=>'bled-badge-slate','disciplinaire'=>'bled-badge-red','complet'=>'bled-badge-blue','classes'=>'bled-badge-blue','planning'=>'bled-badge-slate','staff'=>'bled-badge-gold'];
                                @endphp
                                <span class="bled-badge {{ $catColors[$archive->categorie] ?? 'bled-badge-slate' }}">
                                    {{ ucfirst($archive->categorie) }}
                                </span>
                            </td>
                            <td>{{ $archive->annee_academique }}</td>
                            <td><span class="bled-badge bled-badge-slate">{{ ucfirst($archive->type_export) }}</span></td>
                            <td>{{ $archive->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $octets = $archive->taille_octets ?? 0;
                                    $taille = $octets >= 1048576
                                        ? number_format($octets/1048576,2).' Mo'
                                        : ($octets >= 1024 ? number_format($octets/1024,1).' Ko' : $octets.' o');
                                @endphp
                                {{ method_exists($archive, 'getTailleFormateeAttribute') ? $archive->taille_formatee : $taille }}
                            </td>
                            <td>
                                <div style="display:flex;gap:.4rem;align-items:center;flex-wrap:wrap;">
                                    <a href="{{ route('admin.bled.download', $archive->id) }}"
                                       class="bled-btn bled-btn-success" style="font-size:.72rem;padding:.3rem .6rem;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Télécharger
                                    </a>
                                    <a href="{{ route('admin.bled.preview', $archive->id) }}"
                                       class="bled-btn bled-btn-ghost" style="font-size:.72rem;padding:.3rem .6rem;">
                                        Aperçu
                                    </a>
                                    <form method="POST" action="{{ route('admin.bled.destroy', $archive->id) }}"
                                          onsubmit="return confirm('Supprimer cette archive définitivement ?');" style="margin:0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bled-btn bled-btn-danger" style="font-size:.72rem;padding:.3rem .6rem;">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="bled-empty">
                                @if($typeExport)
                                    Aucune archive de type « {{ ucfirst($typeExport) }} ». Cliquez sur « Nouvelle archive » pour en créer une.
                                @else
                                    Aucune archive créée. Cliquez sur « Nouvelle archive » pour commencer.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($archives) && method_exists($archives, 'hasPages') && $archives->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
                {{ $archives->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>{{-- fin .bled-wrap --}}

{{-- ══ MODAL — CRÉER UNE ARCHIVE ══ --}}
<div class="bled-modal-backdrop" id="modalCreerArchive"
     onclick="if(event.target===this)this.classList.remove('show')">
    <div class="bled-modal">
        <h2>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:.4rem;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            Créer une nouvelle archive
        </h2>

        <form method="POST" action="{{ route('admin.bled.store') }}">
            @csrf

            <div class="bled-form-grid">
                <div class="bled-form-row">
                    <label>Nom de l'archive *</label>
                    <input type="text" name="nom" placeholder="Ex : Promotion 2023-2024" required
                           value="{{ old('nom') }}">
                </div>
                <div class="bled-form-row">
                    <label>Année académique *</label>
                    <select name="annee_academique" required>
                        @forelse($anneesDispos as $a)
                            <option value="{{ $a }}" {{ $a === $anneeActive ? 'selected' : '' }}>{{ $a }}</option>
                        @empty
                            <option value="{{ $anneeActive }}" selected>{{ $anneeActive }}</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="bled-form-grid">
                <div class="bled-form-row">
                    <label>Catégorie *</label>
                    <select name="categorie" required id="modalCategorie"
                            onchange="toggleModalCategorie(this.value)">
                        <option value="complet">Tout l'établissement (complet)</option>
                        <option value="apprenants" {{ old('categorie') === 'apprenants' ? 'selected' : '' }}>Apprenants</option>
                        <option value="enseignants" {{ old('categorie') === 'enseignants' ? 'selected' : '' }}>Enseignants</option>
                        <option value="bulletins" {{ old('categorie') === 'bulletins' ? 'selected' : '' }}>Notes & Bulletins</option>
                        <option value="finances" {{ old('categorie') === 'finances' ? 'selected' : '' }}>Finances</option>
                        <option value="disciplinaire" {{ old('categorie') === 'disciplinaire' ? 'selected' : '' }}>Disciplinaire</option>
                        <option value="classes" {{ old('categorie') === 'classes' ? 'selected' : '' }}>Classes</option>
                        <option value="planning" {{ old('categorie') === 'planning' ? 'selected' : '' }}>Planning / EDT</option>
                        <option value="staff" {{ old('categorie') === 'staff' ? 'selected' : '' }}>Staff administratif</option>
                    </select>
                </div>
                <div class="bled-form-row">
                    <label>Type d'export *</label>
                    <select name="type_export" required>
                        <option value="annuel">Annuel</option>
                        <option value="trimestriel">Trimestriel</option>
                        <option value="manuel">Manuel</option>
                    </select>
                </div>
            </div>

            {{-- Filtre optionnel par classe --}}
            <div class="bled-form-row" id="modalFiltreClasse" style="display:none;">
                <label>Filtrer par classe (optionnel)</label>
                <select name="classe_id">
                    <option value="">— Toutes les classes —</option>
                    @foreach($classesDispos ?? [] as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre optionnel par période (bulletins) --}}
            <div class="bled-form-row" id="modalFiltrePeriode" style="display:none;">
                <label>Filtrer par période (optionnel)</label>
                <select name="periode">
                    <option value="">— Toutes les périodes —</option>
                    <option value="trimestre1">1er Trimestre</option>
                    <option value="trimestre2">2ème Trimestre</option>
                    <option value="trimestre3">3ème Trimestre</option>
                    <option value="semestre1">1er Semestre</option>
                    <option value="semestre2">2ème Semestre</option>
                    <option value="annuel">Annuel</option>
                </select>
            </div>

            <div class="bled-form-row">
                <label>Description (optionnel)</label>
                <textarea name="description" placeholder="Notes internes sur cette archive…">{{ old('description') }}</textarea>
            </div>

            <div class="bled-modal-foot">
                <button type="button" class="bled-btn bled-btn-ghost"
                        onclick="document.getElementById('modalCreerArchive').classList.remove('show')">
                    Annuler
                </button>
                <button type="submit" class="bled-btn bled-btn-primary" id="bledStoreBtn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Créer et archiver
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ══════════════════════════════════════════════
   BLED — JavaScript
══════════════════════════════════════════════ */

/* ── Changement d'onglet ── */
function switchTab(key, event) {
    // Mettre à jour les tabs visuellement
    document.querySelectorAll('.bled-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.bled-tab-panel').forEach(p => p.classList.remove('active'));

    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }

    const panel = document.getElementById('tab-' + key);
    if (panel) panel.classList.add('active');

    // Mettre à jour l'URL (sans rechargement) + le champ caché cat
    const url = new URL(window.location.href);
    url.searchParams.set('cat', key);
    window.history.replaceState({}, '', url.toString());

    // Mettre à jour le champ caché du formulaire de recherche
    const catInputs = document.querySelectorAll('input[name="cat"]');
    catInputs.forEach(inp => inp.value = key);

    // Sauvegarder dans sessionStorage pour conserver l'onglet actif
    sessionStorage.setItem('bled_active_tab', key);
}

/* ── Restaurer l'onglet depuis l'URL au chargement ── */
document.addEventListener('DOMContentLoaded', function () {
    const params  = new URLSearchParams(window.location.search);
    const catFromUrl = params.get('cat');
    if (catFromUrl) {
        // L'onglet est déjà géré côté Blade via la variable $categorie
        // On s'assure juste que le bon panel est visible
        document.querySelectorAll('.bled-tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.bled-tab').forEach(t => t.classList.remove('active'));

        const targetPanel = document.getElementById('tab-' + catFromUrl);
        if (targetPanel) targetPanel.classList.add('active');

        const targetBtn = document.querySelector(`.bled-tab[onclick*="'${catFromUrl}'"]`);
        if (targetBtn) targetBtn.classList.add('active');
    }

    // Rouvrir le modal si erreurs de validation
    @if($errors->any())
        document.getElementById('modalCreerArchive').classList.add('show');
    @endif

    // Afficher les erreurs de validation dans le modal
    @if($errors->has('nom') || $errors->has('categorie') || $errors->has('type_export') || $errors->has('annee_academique'))
        document.getElementById('modalCreerArchive').classList.add('show');
    @endif
});

/* ── Recherche live (debounce 400ms) ── */
(function () {
    const searchInput = document.getElementById('bledSearchInput');
    if (!searchInput) return;

    let debounceTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const val = this.value.trim();

        debounceTimer = setTimeout(function () {
            // Si le champ est vide, soumettre immédiatement pour réinitialiser
            if (val.length === 0 || val.length >= 2) {
                document.getElementById('bledSearchForm').submit();
            }
        }, 400);
    });

    // Soumission sur Entrée
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            clearTimeout(debounceTimer);
            document.getElementById('bledSearchForm').submit();
        }
        if (e.key === 'Escape') {
            this.value = '';
            clearTimeout(debounceTimer);
            document.getElementById('bledSearchForm').submit();
        }
    });
})();

/* ── Affichage conditionnel des filtres dans le modal ── */
function toggleModalCategorie(val) {
    const needsClasse  = ['apprenants', 'bulletins', 'planning'].includes(val);
    const needsPeriode = ['bulletins'].includes(val);
    document.getElementById('modalFiltreClasse').style.display  = needsClasse  ? '' : 'none';
    document.getElementById('modalFiltrePeriode').style.display = needsPeriode ? '' : 'none';
}

/* ── Prévenir double-clic sur le bouton Créer ── */
document.getElementById('bledStoreBtn')?.closest('form')?.addEventListener('submit', function () {
    const btn = document.getElementById('bledStoreBtn');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Archivage en cours…';
    }
});

/* ── Fermer modal avec Escape ── */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('modalCreerArchive')?.classList.remove('show');
    }
});
</script>
@endpush