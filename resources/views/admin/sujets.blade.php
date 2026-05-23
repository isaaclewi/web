{{-- ================================================================
    Vue Admin — Réception des sujets d'examens
    resources/views/admin/sujets.blade.php
    Route : GET /admin/sujets  →  admin.sujets.index
    ================================================================ --}}
@extends('admin.master')

@section('title', 'Sujets reçus')

@push('styles')
<style>
    .adm-wrap        { font-family:'Inter',sans-serif; color:#0f172a; }
    .adm-hero        { display:flex; align-items:center; justify-content:space-between; background:#fff; border:1px solid #e2e8f0; border-radius:.875rem; padding:1.25rem 1.75rem; margin-bottom:1.25rem; flex-wrap:wrap; gap:1rem; }
    .adm-hero-left h1{ font-size:1.2rem; font-weight:700; letter-spacing:-.02em; }
    .adm-hero-left p { font-size:.8rem; color:#64748b; margin-top:.2rem; }
    .adm-hero-badge  { display:inline-flex; align-items:center; gap:.35rem; background:#fee2e2; color:#991b1b; font-size:.7rem; font-weight:700; padding:.25rem .65rem; border-radius:99px; }
    .adm-hero-badge.ok { background:#d1fae5; color:#065f46; }

    /* KPIs */
    .adm-kpis        { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:.875rem; margin-bottom:1.25rem; }
    .adm-kpi         { background:#fff; border:1px solid #e2e8f0; border-radius:.75rem; padding:1rem 1.25rem; }
    .adm-kpi-label   { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; }
    .adm-kpi-val     { font-size:1.6rem; font-weight:700; color:#0f172a; line-height:1.1; margin:.2rem 0 .15rem; }
    .adm-kpi-sub     { font-size:.7rem; color:#94a3b8; }

    /* Toolbar */
    .adm-toolbar     { display:flex; align-items:center; gap:.625rem; flex-wrap:wrap; margin-bottom:1.1rem; }
    .adm-toolbar input, .adm-toolbar select {
        border:1px solid #e2e8f0; border-radius:.5rem; padding:.5rem .875rem; font-size:.8rem;
        color:#0f172a; background:#fff; outline:none; transition:border-color .15s;
    }
    .adm-toolbar input:focus, .adm-toolbar select:focus { border-color:#94a3b8; }
    .adm-toolbar input { flex:1; min-width:180px; }

    /* Cards sujets */
    .adm-grid        { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:1rem; }
    .adm-sujet-card  { background:#fff; border:1px solid #e2e8f0; border-radius:.875rem; overflow:hidden; transition:box-shadow .2s; }
    .adm-sujet-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.07); }
    .adm-sujet-card.urgent { border-left:3px solid #f59e0b; }
    .adm-sujet-card.new    { border-left:3px solid #3b82f6; }

    .adm-card-top    { padding:1rem 1.1rem; border-bottom:1px solid #f8fafc; }
    .adm-card-meta   { display:flex; align-items:center; justify-content:space-between; margin-bottom:.625rem; }
    .adm-card-type   { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; }
    .adm-card-title  { font-size:.88rem; font-weight:700; color:#0f172a; line-height:1.3; margin-bottom:.4rem; }
    .adm-card-info   { display:flex; flex-wrap:wrap; gap:.4rem; align-items:center; font-size:.75rem; color:#64748b; }
    .adm-card-info svg { width:13px; height:13px; flex-shrink:0; }

    .adm-card-body   { padding:.875rem 1.1rem; }
    .adm-teacher-row { display:flex; align-items:center; gap:.625rem; margin-bottom:.75rem; }
    .adm-teacher-ava { width:32px; height:32px; border-radius:.375rem; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:700; color:#475569; flex-shrink:0; text-transform:uppercase; }
    .adm-teacher-name{ font-size:.8rem; font-weight:600; color:#0f172a; }
    .adm-teacher-sub { font-size:.72rem; color:#64748b; }

    .adm-files       { display:flex; flex-wrap:wrap; gap:.375rem; margin-bottom:.875rem; }
    .adm-file-chip   { display:inline-flex; align-items:center; gap:.3rem; background:#f8fafc; border:1px solid #e2e8f0; padding:.25rem .55rem; border-radius:.375rem; font-size:.72rem; color:#334155; text-decoration:none; transition:background .15s; }
    .adm-file-chip:hover { background:#f1f5f9; }
    .adm-file-chip svg { width:12px; height:12px; }

    .adm-instructions{ background:#fffbeb; border:1px solid #fde68a; border-radius:.375rem; padding:.5rem .75rem; font-size:.78rem; color:#92400e; margin-bottom:.875rem; }

    .adm-card-foot   { padding:.75rem 1.1rem; border-top:1px solid #f8fafc; display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }

    /* Feedback modal */
    .adm-modal-bg    { display:none; position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:200; align-items:center; justify-content:center; }
    .adm-modal-bg.show { display:flex; }
    .adm-modal       { background:#fff; border-radius:.875rem; width:min(480px,94vw); padding:1.5rem; }
    .adm-modal h2    { font-size:1rem; font-weight:700; margin-bottom:1rem; }
    .adm-modal label { display:block; font-size:.75rem; font-weight:600; color:#334155; margin-bottom:.3rem; }
    .adm-modal textarea { width:100%; border:1px solid #e2e8f0; border-radius:.5rem; padding:.55rem .875rem; font-size:.84rem; resize:vertical; min-height:90px; outline:none; }
    .adm-modal textarea:focus { border-color:#94a3b8; }
    .adm-modal-foot  { display:flex; gap:.625rem; justify-content:flex-end; margin-top:1rem; padding-top:1rem; border-top:1px solid #f1f5f9; }

    /* Buttons */
    .adm-btn         { display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .875rem; border-radius:.5rem; font-size:.78rem; font-weight:600; border:none; cursor:pointer; transition:all .15s; text-decoration:none; }
    .adm-btn-green   { background:#d1fae5; color:#065f46; }
    .adm-btn-green:hover { background:#a7f3d0; }
    .adm-btn-red     { background:#fee2e2; color:#991b1b; }
    .adm-btn-red:hover { background:#fecaca; }
    .adm-btn-blue    { background:#dbeafe; color:#1d4ed8; }
    .adm-btn-blue:hover { background:#bfdbfe; }
    .adm-btn-ghost   { background:#f1f5f9; color:#334155; }
    .adm-btn-ghost:hover { background:#e2e8f0; }
    .adm-btn-dark    { background:#0f172a; color:#fff; }
    .adm-btn-dark:hover { background:#1e293b; }

    /* Badges */
    .adm-badge       { display:inline-block; padding:.18rem .55rem; border-radius:99px; font-size:.68rem; font-weight:600; }
    .adm-badge-gold  { background:#fef3c7; color:#92400e; }
    .adm-badge-blue  { background:#dbeafe; color:#1d4ed8; }
    .adm-badge-green { background:#d1fae5; color:#065f46; }
    .adm-badge-red   { background:#fee2e2; color:#991b1b; }
    .adm-badge-slate { background:#f1f5f9; color:#475569; }

    /* Flash */
    .adm-flash       { padding:.75rem 1.1rem; border-radius:.5rem; font-size:.84rem; font-weight:500; margin-bottom:1rem; display:flex; align-items:center; gap:.5rem; }
    .adm-flash-ok    { background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; }
    .adm-flash-err   { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

    /* Empty */
    .adm-empty       { padding:3.5rem 2rem; text-align:center; color:#94a3b8; }

    /* Tab filter */
    .adm-tabs        { display:flex; gap:0; border-bottom:1px solid #e2e8f0; margin-bottom:1.25rem; overflow-x:auto; }
    .adm-tab         { padding:.65rem 1.1rem; font-size:.8rem; font-weight:600; color:#64748b; cursor:pointer; border-bottom:2px solid transparent; white-space:nowrap; background:none; border-top:none; border-left:none; border-right:none; transition:all .15s; }
    .adm-tab.active  { color:#0f172a; border-bottom-color:#0f172a; }
    .adm-tab .cnt    { display:inline-block; background:#f1f5f9; color:#64748b; font-size:.65rem; padding:.1rem .45rem; border-radius:99px; margin-left:.3rem; }
    .adm-tab.active .cnt { background:#0f172a; color:#fff; }

    @media(max-width:640px){ .adm-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
@php
    $typeLabels = [
        'controle'    => 'Contrôle',
        'examen'      => 'Examen',
        'composition' => 'Composition',
        'tp'          => 'TP',
        'interro'     => 'Interrogation',
        'devoir'      => 'Devoir',
        'rattrapage'  => 'Rattrapage',
    ];
    $statutColors = [
        'en_attente' => 'adm-badge-gold',
        'recu'       => 'adm-badge-blue',
        'valide'     => 'adm-badge-green',
        'rejete'     => 'adm-badge-red',
        'archive'    => 'adm-badge-slate',
    ];
    $statutLabels = [
        'en_attente' => 'En attente',
        'recu'       => 'Reçu',
        'valide'     => 'Validé',
        'rejete'     => 'Rejeté',
        'archive'    => 'Archivé',
    ];
    $activeStatut = request('statut', 'all');
@endphp

<div class="adm-wrap">

    @if(session('success'))
        <div class="adm-flash adm-flash-ok">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="adm-flash adm-flash-err">{{ session('error') }}</div>
    @endif

    {{-- ── Hero ── --}}
    <div class="adm-hero">
        <div class="adm-hero-left">
            <h1>Sujets d'examens reçus</h1>
            <p>Gérez les sujets envoyés par vos enseignants — validez, commentez ou rejetez.</p>
        </div>
        @if(($stats['en_attente'] ?? 0) > 0)
            <div class="adm-hero-badge">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $stats['en_attente'] }} en attente de validation
            </div>
        @else
            <div class="adm-hero-badge ok">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Tout est traité
            </div>
        @endif
    </div>

    {{-- ── KPIs ── --}}
    <div class="adm-kpis">
        <div class="adm-kpi">
            <div class="adm-kpi-label">Total reçus</div>
            <div class="adm-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            <div class="adm-kpi-sub">toutes périodes</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi-label">En attente</div>
            <div class="adm-kpi-val" style="color:#d97706;">{{ $stats['en_attente'] ?? 0 }}</div>
            <div class="adm-kpi-sub">à traiter</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi-label">Validés</div>
            <div class="adm-kpi-val" style="color:#059669;">{{ $stats['valide'] ?? 0 }}</div>
            <div class="adm-kpi-sub">cette année</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi-label">Enseignants</div>
            <div class="adm-kpi-val">{{ $stats['nb_teachers'] ?? 0 }}</div>
            <div class="adm-kpi-sub">ont envoyé</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi-label">Ce mois</div>
            <div class="adm-kpi-val">{{ $stats['ce_mois'] ?? 0 }}</div>
            <div class="adm-kpi-sub">{{ now()->locale('fr')->isoFormat('MMM') }}</div>
        </div>
    </div>

    {{-- ── Toolbar ── --}}
    <div class="adm-toolbar">
        <form method="GET" action="{{ route('admin.sujets.index') }}" style="display:contents;">
            <input type="hidden" name="statut" value="{{ $activeStatut }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher titre, enseignant, matière…">
            <select name="type_filter" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                @foreach($typeLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('type_filter') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="teacher_id" onchange="this.form.submit()">
                <option value="">Tous les enseignants</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>
                        {{ $t->prenom }} {{ $t->nom }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="adm-btn adm-btn-dark" style="padding:.5rem .875rem;">Filtrer</button>
            <a href="{{ route('admin.sujets.index') }}" class="adm-btn adm-btn-ghost" style="padding:.5rem .875rem;">Reset</a>
        </form>
    </div>

    {{-- ── Onglets statut ── --}}
    <div class="adm-tabs">
        @php
            $tabDefs = [
                'all'        => ['label' => 'Tous', 'count' => $stats['total'] ?? 0],
                'en_attente' => ['label' => 'En attente', 'count' => $stats['en_attente'] ?? 0],
                'recu'       => ['label' => 'Reçus', 'count' => $stats['recu'] ?? 0],
                'valide'     => ['label' => 'Validés', 'count' => $stats['valide'] ?? 0],
                'rejete'     => ['label' => 'Rejetés', 'count' => $stats['rejete'] ?? 0],
            ];
        @endphp
        @foreach($tabDefs as $key => $def)
            <a href="{{ route('admin.sujets.index', array_merge(request()->except('statut','page'), ['statut' => $key])) }}"
               style="text-decoration:none;">
                <button class="adm-tab {{ $activeStatut === $key ? 'active' : '' }}">
                    {{ $def['label'] }}
                    <span class="cnt">{{ $def['count'] }}</span>
                </button>
            </a>
        @endforeach
    </div>

    {{-- ── Grille de cards ── --}}
    @if($sujets->count())
        <div class="adm-grid">
            @foreach($sujets as $sujet)
                @php
                    $isNew     = $sujet->statut === 'en_attente';
                    $isUrgent  = $isNew && $sujet->date_evaluation && \Carbon\Carbon::parse($sujet->date_evaluation)->diffInDays(now()) <= 3;
                    $initiales = strtoupper(substr($sujet->teacher->prenom ?? '?', 0, 1).substr($sujet->teacher->nom ?? '', 0, 1));
                @endphp
                <div class="adm-sujet-card {{ $isUrgent ? 'urgent' : ($isNew ? 'new' : '') }}">

                    <div class="adm-card-top">
                        <div class="adm-card-meta">
                            <span class="adm-card-type">{{ $typeLabels[$sujet->type] ?? $sujet->type }}</span>
                            <span class="adm-badge {{ $statutColors[$sujet->statut] ?? 'adm-badge-slate' }}">
                                {{ $statutLabels[$sujet->statut] ?? $sujet->statut }}
                            </span>
                        </div>
                        <div class="adm-card-title">{{ $sujet->titre }}</div>
                        <div class="adm-card-info">
                            @if($sujet->subject)
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
                                {{ $sujet->subject->name }}
                            @endif
                            @if($sujet->classe)
                                <span>·</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3"/></svg>
                                {{ $sujet->classe->name }}
                            @endif
                            @if($sujet->date_evaluation)
                                <span>·</span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @php
                                    $dateEval = \Carbon\Carbon::parse($sujet->date_evaluation);
                                    $diff     = $dateEval->diffInDays(now(), false);
                                @endphp
                                <span style="{{ $diff >= 0 && $diff <= 3 ? 'color:#d97706;font-weight:600;' : '' }}">
                                    {{ $dateEval->format('d/m/Y') }}
                                    @if($diff >= 0 && $diff <= 3) <em style="font-style:normal;">(dans {{ 3-$diff }}j)</em> @endif
                                </span>
                            @endif
                            @if($sujet->duree_minutes)
                                <span>·</span> {{ $sujet->duree_minutes }} min
                            @endif
                        </div>
                    </div>

                    <div class="adm-card-body">
                        {{-- Enseignant ── --}}
                        <div class="adm-teacher-row">
                            <div class="adm-teacher-ava">{{ $initiales }}</div>
                            <div>
                                <div class="adm-teacher-name">{{ $sujet->teacher->prenom ?? '' }} {{ $sujet->teacher->nom ?? '—' }}</div>
                                <div class="adm-teacher-sub">{{ $sujet->teacher->specialite ?? '' }} · {{ $sujet->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        {{-- Fichiers ── --}}
                        @if($sujet->fichiers->count())
                            <div class="adm-files">
                                @foreach($sujet->fichiers as $f)
                                    <a href="{{ route('admin.sujets.download', $f->id) }}" class="adm-file-chip">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        {{ \Str::limit($f->nom_original, 24) }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        {{-- Instructions ── --}}
                        @if($sujet->instructions)
                            <div class="adm-instructions">
                                <strong style="font-weight:700;">Instructions :</strong> {{ $sujet->instructions }}
                            </div>
                        @endif

                        {{-- Feedback existant ── --}}
                        @if($sujet->feedback_admin)
                            <div style="font-size:.78rem; color:#475569; font-style:italic; border-left:2px solid #e2e8f0; padding-left:.625rem; margin-bottom:.875rem;">
                                <strong style="font-style:normal; font-weight:600; color:#334155;">Votre feedback :</strong> {{ $sujet->feedback_admin }}
                            </div>
                        @endif
                    </div>

                    {{-- Pied de card ── --}}
                    <div class="adm-card-foot">
                        @if($sujet->statut === 'en_attente' || $sujet->statut === 'recu')
                            {{-- Marquer reçu --}}
                            @if($sujet->statut === 'en_attente')
                                <form method="POST" action="{{ route('admin.sujets.statut', $sujet->id) }}" style="margin:0;">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="statut" value="recu">
                                    <button type="submit" class="adm-btn adm-btn-blue">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Marquer reçu
                                    </button>
                                </form>
                            @endif

                            {{-- Valider --}}
                            <button class="adm-btn adm-btn-green" onclick="openFeedback({{ $sujet->id }}, 'valide')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Valider
                            </button>

                            {{-- Rejeter --}}
                            <button class="adm-btn adm-btn-red" onclick="openFeedback({{ $sujet->id }}, 'rejete')">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Rejeter
                            </button>

                        @elseif($sujet->statut === 'valide')
                            <button class="adm-btn adm-btn-ghost" onclick="openFeedback({{ $sujet->id }}, 'archive')">Archiver</button>
                            <span style="font-size:.75rem; color:#94a3b8;">Validé {{ $sujet->updated_at->diffForHumans() }}</span>
                        @else
                            <span style="font-size:.75rem; color:#94a3b8;">Traité {{ $sujet->updated_at->diffForHumans() }}</span>
                        @endif

                        {{-- Modifier feedback --}}
                        @if(in_array($sujet->statut, ['recu','valide','rejete','archive']))
                            <button class="adm-btn adm-btn-ghost" onclick="openFeedback({{ $sujet->id }}, '{{ $sujet->statut }}')" style="margin-left:auto;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Feedback
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($sujets->hasPages())
            <div style="margin-top:1.25rem;">{{ $sujets->appends(request()->query())->links() }}</div>
        @endif

    @else
        <div class="adm-empty">
            <svg style="width:40px;height:40px;margin:0 auto .875rem;display:block;color:#e2e8f0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Aucun sujet ne correspond à votre recherche.
        </div>
    @endif

</div>

{{-- ── Modal Feedback ── --}}
<div class="adm-modal-bg" id="feedbackModal" onclick="if(event.target===this)closeFeedback()">
    <div class="adm-modal">
        <h2 id="modalTitle">Feedback</h2>
        <form method="POST" id="feedbackForm" action="">
            @csrf @method('PATCH')
            <input type="hidden" name="statut" id="feedbackStatut">
            <div style="margin-bottom:1rem;">
                <label>Message pour l'enseignant (optionnel)</label>
                <textarea name="feedback_admin" id="feedbackText" placeholder="Commentaires, corrections demandées, instructions…"></textarea>
            </div>
            <div class="adm-modal-foot">
                <button type="button" class="adm-btn adm-btn-ghost" onclick="closeFeedback()">Annuler</button>
                <button type="submit" class="adm-btn adm-btn-dark" id="feedbackSubmit">Confirmer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const statutTitles = {
    valide:'Valider ce sujet', rejete:'Rejeter ce sujet',
    archive:'Archiver ce sujet', recu:'Marquer comme reçu',
    en_attente:'Remettre en attente',
};

function openFeedback(id, statut) {
    document.getElementById('feedbackForm').action = `/admin/sujets/${id}/statut`;
    document.getElementById('feedbackStatut').value = statut;
    document.getElementById('feedbackText').value = '';
    document.getElementById('modalTitle').textContent = statutTitles[statut] || 'Feedback';
    document.getElementById('feedbackModal').classList.add('show');
}
function closeFeedback() {
    document.getElementById('feedbackModal').classList.remove('show');
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeFeedback(); });
</script>
@endpush
