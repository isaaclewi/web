{{-- ================================================================
    BLED — Formulaire de filtres pour export PDF
    resources/views/admin/bled_pdf_filtres.blade.php
    Route : GET /admin/bled/pdf/filtres
    ================================================================ --}}
@extends('admin.master')
@section('title', 'BLED — Export PDF')

@push('styles')
<style>
:root {
    --pf-navy:  #0f172a;
    --pf-slate: #1e293b;
    --pf-steel: #334155;
    --pf-muted: #64748b;
    --pf-light: #f1f5f9;
    --pf-gold:  #d97706;
    --pf-blue:  #2563eb;
    --pf-green: #059669;
    --pf-red:   #dc2626;
}

.pf-wrap { font-family:'Inter',sans-serif; color:var(--pf-navy); max-width:900px; margin:0 auto; }

/* Hero */
.pf-hero {
    background:var(--pf-navy); color:#fff;
    border-radius:1rem; padding:1.75rem 2rem;
    margin-bottom:1.5rem; position:relative; overflow:hidden;
}
.pf-hero::before {
    content:''; position:absolute; inset:0;
    background:radial-gradient(ellipse at 80% 20%,rgba(217,119,6,.2) 0%,transparent 60%);
    pointer-events:none;
}
.pf-hero h1 { font-size:1.4rem; font-weight:700; }
.pf-hero p  { font-size:.85rem; color:#94a3b8; margin-top:.35rem; }
.pf-badge {
    display:inline-flex; align-items:center; gap:.4rem;
    background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15);
    padding:.3rem .75rem; border-radius:99px; font-size:.72rem;
    font-weight:600; letter-spacing:.06em; color:#cbd5e1; margin-bottom:.75rem;
}

/* Card */
.pf-card {
    background:#fff; border:1px solid #e2e8f0; border-radius:.875rem;
    overflow:hidden; margin-bottom:1.25rem;
}
.pf-card-hd {
    padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
    display:flex; align-items:center; gap:.65rem;
}
.pf-card-hd h2 { font-size:.95rem; font-weight:700; }
.pf-card-icon { width:32px; height:32px; border-radius:.4rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.pf-card-body { padding:1.5rem; }

/* Grid */
.pf-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.pf-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }

/* Form */
.pf-row { margin-bottom:1rem; }
.pf-label { display:block; font-size:.78rem; font-weight:600; color:var(--pf-steel); margin-bottom:.35rem; }
.pf-label span { color:var(--pf-red); margin-left:.2rem; }
.pf-input, .pf-select {
    width:100%; border:1px solid #e2e8f0; border-radius:.5rem;
    padding:.55rem .875rem; font-size:.85rem; color:var(--pf-navy);
    outline:none; transition:border-color .15s; background:#fff;
    appearance:none;
}
.pf-input:focus, .pf-select:focus { border-color:#94a3b8; box-shadow:0 0 0 3px rgba(148,163,184,.15); }
.pf-select-wrap { position:relative; }
.pf-select-wrap::after {
    content:''; position:absolute; right:.875rem; top:50%; transform:translateY(-50%);
    border:5px solid transparent; border-top-color:var(--pf-muted);
    pointer-events:none;
}

/* Catégorie tabs */
.pf-cats { display:grid; grid-template-columns:repeat(4,1fr); gap:.65rem; margin-bottom:1.5rem; }
.pf-cat {
    position:relative; border:2px solid #e2e8f0; border-radius:.75rem;
    padding:.875rem .75rem; cursor:pointer; text-align:center;
    transition:all .15s; background:#fff;
}
.pf-cat input[type=radio] { position:absolute; opacity:0; }
.pf-cat:hover { border-color:#94a3b8; background:#f8fafc; }
.pf-cat.selected { border-color:var(--pf-navy); background:var(--pf-navy); color:#fff; }
.pf-cat-icon { font-size:1.3rem; margin-bottom:.35rem; }
.pf-cat-label { font-size:.78rem; font-weight:600; display:block; }

/* Divider */
.pf-divider { display:flex; align-items:center; gap:.75rem; margin:1rem 0; }
.pf-divider::before, .pf-divider::after { content:''; flex:1; height:1px; background:#e2e8f0; }
.pf-divider span { font-size:.72rem; font-weight:600; color:var(--pf-muted); text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }

/* Filtre niveau */
.pf-funnel {
    background:#f8fafc; border:1px solid #e2e8f0; border-radius:.75rem;
    padding:1.25rem; margin-bottom:1rem;
}
.pf-funnel-title { font-size:.8rem; font-weight:700; color:var(--pf-navy); margin-bottom:1rem; display:flex; align-items:center; gap:.4rem; }

/* Info box */
.pf-info { background:#dbeafe; border-radius:.5rem; padding:.75rem 1rem; font-size:.8rem; color:#1e40af; margin-bottom:1rem; }
.pf-info strong { font-weight:700; }

/* Boutons */
.pf-btn {
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.65rem 1.25rem; border-radius:.5rem; font-size:.85rem;
    font-weight:600; border:none; cursor:pointer; transition:all .15s;
    text-decoration:none;
}
.pf-btn-primary { background:var(--pf-navy); color:#fff; }
.pf-btn-primary:hover { background:var(--pf-slate); }
.pf-btn-gold { background:#fef3c7; color:var(--pf-gold); }
.pf-btn-gold:hover { background:#fde68a; }
.pf-btn-ghost { background:#f1f5f9; color:var(--pf-navy); }
.pf-btn-ghost:hover { background:#e2e8f0; }
.pf-btn-green { background:#d1fae5; color:var(--pf-green); }
.pf-btn-green:hover { background:#a7f3d0; }
.pf-btn-lg { padding:.875rem 1.75rem; font-size:.92rem; }

.pf-foot {
    display:flex; gap:.75rem; justify-content:flex-end;
    padding-top:1.25rem; border-top:1px solid #f1f5f9;
    flex-wrap:wrap;
}

/* Section conditionnelle */
.pf-cond { display:none; }
.pf-cond.show { display:block; }

@media(max-width:768px) {
    .pf-cats { grid-template-columns:repeat(2,1fr); }
    .pf-grid-2, .pf-grid-3 { grid-template-columns:1fr; }
    .pf-foot { flex-direction:column-reverse; }
    .pf-foot .pf-btn { width:100%; justify-content:center; }
}
@media(max-width:480px) {
    .pf-cats { grid-template-columns:1fr 1fr; }
}
</style>
@endpush

@section('content')
@php
    $categorie = request('cat', 'apprenants');
    $annee     = request('annee', $institution->academic_year ?? date('Y').'-'.(date('Y') + 1));
@endphp

<div class="pf-wrap">

    {{-- Hero --}}
    <div class="pf-hero">
        <div class="pf-badge">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            BLED — Export PDF
        </div>
        <h1>Générer un rapport PDF</h1>
        <p>Configurez vos filtres du plus général au plus spécifique, puis générez votre document.</p>
    </div>

    <form id="pdfForm" method="GET" action="{{ route('admin.bled.pdf.apercu') }}" target="_blank">

        {{-- ══ ÉTAPE 1 : Catégorie ══ --}}
        <div class="pf-card">
            <div class="pf-card-hd">
                <div class="pf-card-icon" style="background:#dbeafe;">
                    <svg width="16" height="16" fill="none" stroke="#2563eb" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <h2>Étape 1 — Choisir la catégorie de données</h2>
            </div>
            <div class="pf-card-body">
                <div class="pf-cats" id="catGrid">
                    @php
                        $cats = [
                            ['val'=>'apprenants',   'icon'=>'🎓', 'label'=>'Apprenants'],
                            ['val'=>'enseignants',  'icon'=>'👨‍🏫', 'label'=>'Enseignants'],
                            ['val'=>'bulletins',    'icon'=>'📋', 'label'=>'Bulletins'],
                            ['val'=>'finances',     'icon'=>'💰', 'label'=>'Finances'],
                            ['val'=>'disciplinaire','icon'=>'⚠️', 'label'=>'Disciplinaire'],
                            ['val'=>'classes',      'icon'=>'🏫', 'label'=>'Classes'],
                            ['val'=>'planning',     'icon'=>'📅', 'label'=>'Planning / EDT'],
                            ['val'=>'staff',        'icon'=>'👥', 'label'=>'Staff admin.'],
                            ['val'=>'complet',      'icon'=>'📦', 'label'=>'Complet'],
                        ];
                    @endphp
                    @foreach($cats as $cat)
                        <label class="pf-cat {{ $categorie === $cat['val'] ? 'selected' : '' }}" data-val="{{ $cat['val'] }}">
                            <input type="radio" name="cat" value="{{ $cat['val'] }}" {{ $categorie === $cat['val'] ? 'checked' : '' }}>
                            <div class="pf-cat-icon">{{ $cat['icon'] }}</div>
                            <span class="pf-cat-label">{{ $cat['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ ÉTAPE 2 : Année ══ --}}
        <div class="pf-card">
            <div class="pf-card-hd">
                <div class="pf-card-icon" style="background:#d1fae5;">
                    <svg width="16" height="16" fill="none" stroke="#059669" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2>Étape 2 — Année académique</h2>
            </div>
            <div class="pf-card-body" style="padding-bottom:.75rem;">
                <div class="pf-row" style="max-width:280px;">
                    <label class="pf-label">Année académique <span>*</span></label>
                    <div class="pf-select-wrap">
                        <select name="annee" class="pf-select" required>
                            @foreach($anneesDispos as $a)
                                <option value="{{ $a }}" {{ $a === $annee ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ ÉTAPE 3 : Filtrage fin ══ --}}
        <div class="pf-card">
            <div class="pf-card-hd">
                <div class="pf-card-icon" style="background:#fef3c7;">
                    <svg width="16" height="16" fill="none" stroke="#d97706" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                </div>
                <h2>Étape 3 — Filtres (du général au particulier)</h2>
            </div>
            <div class="pf-card-body">

                <div class="pf-info">
                    <strong>💡 Conseil :</strong> Laissez tous les champs vides pour obtenir un export complet de la catégorie.
                    Plus vous précisez, plus le document sera ciblé.
                </div>

                {{-- Entonnoir : Filière → Niveau → Classe → Apprenant --}}
                <div class="pf-funnel">
                    <div class="pf-funnel-title">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Entonnoir de sélection — choisissez le niveau de granularité
                    </div>

                    {{-- Niveau 1 : Filière --}}
                    <div class="pf-row pf-cond show" id="cond-filiere" data-cats="apprenants,enseignants,bulletins,finances,classes">
                        <label class="pf-label">🔷 Filière (optionnel)</label>
                        <div class="pf-select-wrap">
                            <select name="filiere_id" id="sel-filiere" class="pf-select" onchange="cascadeFiliere()">
                                <option value="">— Toutes les filières —</option>
                                @foreach($filieres as $f)
                                    <option value="{{ $f->id }}" {{ request('filiere_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Niveau 2 : Niveau --}}
                    <div class="pf-row pf-cond show" id="cond-niveau" data-cats="apprenants,enseignants,bulletins,finances,classes">
                        <label class="pf-label">🔶 Niveau (optionnel)</label>
                        <div class="pf-select-wrap">
                            <select name="niveau_id" id="sel-niveau" class="pf-select" onchange="cascadeNiveau()">
                                <option value="">— Tous les niveaux —</option>
                                @foreach($niveaux as $n)
                                    <option value="{{ $n->id }}" {{ request('niveau_id') == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Niveau 3 : Classe --}}
                    <div class="pf-row pf-cond show" id="cond-classe" data-cats="apprenants,enseignants,bulletins,finances,disciplinaire,planning">
                        <label class="pf-label">🔹 Classe (optionnel)</label>
                        <div class="pf-select-wrap">
                            <select name="classe_id" id="sel-classe" class="pf-select" onchange="cascadeClasse()">
                                <option value="">— Toutes les classes —</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('classe_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                        @if($c->niveau) ({{ $c->niveau->name }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Niveau 4 : Apprenant spécifique --}}
                    <div class="pf-row pf-cond show" id="cond-apprenant" data-cats="apprenants,bulletins,finances,disciplinaire">
                        <label class="pf-label">🔸 Apprenant spécifique (optionnel)</label>
                        <div class="pf-select-wrap">
                            <select name="apprenant_id" id="sel-apprenant" class="pf-select">
                                <option value="">— Tous les apprenants —</option>
                                @foreach($apprenants as $ap)
                                    <option value="{{ $ap->id }}" {{ request('apprenant_id') == $ap->id ? 'selected' : '' }}>
                                        {{ $ap->prenom }} {{ $ap->nom }}
                                        @if($ap->matricule) ({{ $ap->matricule }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Filtres complémentaires selon catégorie --}}
                <div class="pf-divider"><span>Filtres complémentaires</span></div>

                <div class="pf-grid-2">
                    {{-- Période (bulletins) --}}
                    <div class="pf-row pf-cond" id="cond-periode" data-cats="bulletins,complet">
                        <label class="pf-label">Période scolaire</label>
                        <div class="pf-select-wrap">
                            <select name="periode" class="pf-select">
                                <option value="">— Toutes les périodes —</option>
                                <option value="trimestre1" {{ request('periode') === 'trimestre1' ? 'selected' : '' }}>1er Trimestre</option>
                                <option value="trimestre2" {{ request('periode') === 'trimestre2' ? 'selected' : '' }}>2ème Trimestre</option>
                                <option value="trimestre3" {{ request('periode') === 'trimestre3' ? 'selected' : '' }}>3ème Trimestre</option>
                                <option value="semestre1"  {{ request('periode') === 'semestre1'  ? 'selected' : '' }}>1er Semestre</option>
                                <option value="semestre2"  {{ request('periode') === 'semestre2'  ? 'selected' : '' }}>2ème Semestre</option>
                                <option value="annuel"     {{ request('periode') === 'annuel'     ? 'selected' : '' }}>Annuel</option>
                            </select>
                        </div>
                    </div>

                    {{-- Statut financier --}}
                    <div class="pf-row pf-cond" id="cond-statut" data-cats="finances,complet">
                        <label class="pf-label">Statut financier</label>
                        <div class="pf-select-wrap">
                            <select name="statut" class="pf-select">
                                <option value="">— Tous les statuts —</option>
                                <option value="paye"    {{ request('statut') === 'paye'    ? 'selected' : '' }}>✓ Payé</option>
                                <option value="partiel" {{ request('statut') === 'partiel' ? 'selected' : '' }}>△ Partiel</option>
                                <option value="impaye"  {{ request('statut') === 'impaye'  ? 'selected' : '' }}>✗ Impayé</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Recherche textuelle --}}
                <div class="pf-row pf-cond show" id="cond-search" data-cats="apprenants,enseignants,finances,staff,complet">
                    <label class="pf-label">Recherche textuelle (nom, prénom, matricule…)</label>
                    <input type="text" name="q" class="pf-input" placeholder="Saisir un nom, un matricule…" value="{{ request('q') }}">
                </div>
            </div>
        </div>

        {{-- ══ BOUTONS ══ --}}
        <div class="pf-card">
            <div class="pf-card-body">
                <div style="display:flex; gap:1rem; flex-wrap:wrap; justify-content:space-between; align-items:center;">
                    <div style="font-size:.82rem; color:var(--pf-muted);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align:middle;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        L'aperçu s'ouvre dans un nouvel onglet — utilisez <strong>Ctrl+P</strong> ou <strong>⌘+P</strong> pour imprimer en PDF.
                    </div>
                    <div class="pf-foot" style="border:none; padding:0; margin:0;">
                        <a href="{{ route('admin.bled.index') }}" class="pf-btn pf-btn-ghost">
                            ← Retour aux archives
                        </a>
                        <button type="submit" formtarget="_blank" class="pf-btn pf-btn-gold" id="btnApercu">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Aperçu HTML
                        </button>
                        <button type="button" class="pf-btn pf-btn-primary pf-btn-lg" onclick="printForm()">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Ouvrir &amp; Imprimer en PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
/* ── Sélection catégorie ── */
document.querySelectorAll('.pf-cat').forEach(label => {
    label.addEventListener('click', () => {
        document.querySelectorAll('.pf-cat').forEach(l => l.classList.remove('selected'));
        label.classList.add('selected');
        label.querySelector('input').checked = true;
        refreshConditions(label.dataset.val);
    });
});

function refreshConditions(cat) {
    document.querySelectorAll('.pf-cond').forEach(el => {
        const cats = (el.dataset.cats || '').split(',');
        if (cats.includes(cat) || cats.includes('all')) {
            el.classList.add('show');
        } else {
            el.classList.remove('show');
            // Réinitialiser les valeurs des champs cachés
            el.querySelectorAll('select').forEach(s => s.value = '');
            el.querySelectorAll('input').forEach(i => i.value = '');
        }
    });
}

/* Init au chargement */
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="cat"]:checked');
    if (checked) refreshConditions(checked.value);
});

/* ── Cascade filtres ── */
function cascadeFiliere() {
    // Si filière sélectionnée, réinitialiser niveau, classe, apprenant
    const v = document.getElementById('sel-filiere').value;
    if (v) {
        document.getElementById('sel-niveau').value    = '';
        document.getElementById('sel-classe').value    = '';
        document.getElementById('sel-apprenant').value = '';
    }
}
function cascadeNiveau() {
    const v = document.getElementById('sel-niveau').value;
    if (v) {
        document.getElementById('sel-classe').value    = '';
        document.getElementById('sel-apprenant').value = '';
    }
}
function cascadeClasse() {
    const v = document.getElementById('sel-classe').value;
    if (v) {
        document.getElementById('sel-apprenant').value = '';
    }
}

/* ── Impression ── */
function printForm() {
    const form = document.getElementById('pdfForm');
    const originalAction = form.action;
    const originalTarget = form.target;

    form.action = "{{ route('admin.bled.pdf.apercu') }}";
    form.target = '_blank';

    // Ouvrir dans nouvel onglet, puis déclencher l'impression
    const params = new URLSearchParams(new FormData(form));
    const url = form.action + '?' + params.toString();
    const win = window.open(url, '_blank');

    // Restaurer
    form.action = originalAction;
    form.target = originalTarget;

    if (win) {
        win.onload = () => {
            setTimeout(() => win.print(), 800);
        };
    }
}
</script>
@endpush