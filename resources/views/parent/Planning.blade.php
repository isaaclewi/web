@extends('parent.master')

@section('title', 'Planning scolaire')
@section('page-title', 'Planning de mes enfants')
@section('page-sub', 'Emploi du temps et programmes de paiement')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap');

    :root {
        --pp-bg:      #f7f8fc;
        --pp-surface: #ffffff;
        --pp-border:  #e5e9f4;
        --pp-ink:     #18202f;
        --pp-muted:   #7b859a;
        --pp-gold:    #d4a017;
        --pp-gold-l:  #fdf4dc;
        --pp-teal:    #0d8a6f;
        --pp-teal-l:  #d0f4ec;
        --pp-red:     #c9313b;
        --pp-red-l:   #fde8e9;
        --pp-blue:    #2563c0;
        --pp-blue-l:  #dceafd;
    }

    .pp-page { display: flex; flex-direction: column; gap: 1.25rem; font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── Sélecteur enfant ── */
    .pp-children-tabs {
        background: var(--pp-surface);
        border: 1px solid var(--pp-border);
        border-radius: 14px;
        padding: .5rem;
        display: flex;
        gap: .25rem;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .pp-children-tabs::-webkit-scrollbar { display: none; }

    .pp-child-tab {
        display: flex; align-items: center; gap: .6rem;
        padding: .625rem 1rem;
        border-radius: 10px;
        cursor: pointer; border: 1px solid transparent;
        font-size: .84rem; font-weight: 500; color: var(--pp-muted);
        background: transparent;
        transition: .15s; white-space: nowrap;
    }
    .pp-child-tab:hover { background: var(--pp-bg); color: var(--pp-ink); }
    .pp-child-tab.active {
        background: var(--pp-surface);
        border-color: var(--pp-border);
        color: var(--pp-ink);
        font-weight: 700;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
    }
    .pp-child-av {
        width: 30px; height: 30px; border-radius: 50%;
        display: grid; place-items: center;
        font-size: .75rem; font-weight: 700; color: #fff;
        flex-shrink: 0;
    }
    .pp-child-classe { font-size: .72rem; font-weight: 400; color: var(--pp-muted); }

    /* ── Panneaux enfant ── */
    .pp-panel { display: none; }
    .pp-panel.active { display: block; animation: ppFade .2s ease; }
    @keyframes ppFade { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; } }

    /* ── Info bar enfant ── */
    .pp-info-bar {
        display: flex; flex-wrap: wrap; gap: .6rem; align-items: center;
        padding: .875rem 1.25rem;
        background: var(--pp-surface);
        border: 1px solid var(--pp-border);
        border-radius: 12px;
    }
    .pp-info-pill {
        display: flex; align-items: center; gap: .35rem;
        padding: .3rem .75rem;
        background: var(--pp-bg);
        border: 1px solid var(--pp-border);
        border-radius: 8px;
        font-size: .8rem; color: var(--pp-muted);
    }
    .pp-info-pill strong { color: var(--pp-ink); font-weight: 600; }

    /* ── Nav semaine ── */
    .pp-week-nav {
        display: flex; align-items: center; gap: .625rem;
        padding: .75rem 1rem;
        background: var(--pp-surface);
        border: 1px solid var(--pp-border);
        border-radius: 12px;
    }
    .pp-nav-btn {
        width: 32px; height: 32px;
        background: var(--pp-bg); border: 1px solid var(--pp-border);
        border-radius: 8px; cursor: pointer;
        display: grid; place-items: center;
        font-size: .9rem; color: var(--pp-muted);
        text-decoration: none; transition: .15s;
    }
    .pp-nav-btn:hover { background: var(--pp-gold); color: #fff; border-color: var(--pp-gold); }
    .pp-week-label {
        flex: 1; text-align: center;
        font-size: .9rem; font-weight: 600; color: var(--pp-ink);
    }
    .pp-today-link {
        padding: .3rem .75rem;
        background: var(--pp-gold-l);
        border: 1px solid rgba(212,160,23,.25);
        border-radius: 8px;
        font-size: .78rem; font-weight: 600; color: var(--pp-gold);
        text-decoration: none; white-space: nowrap;
        transition: .15s;
    }
    .pp-today-link:hover { background: var(--pp-gold); color: #fff; }

    /* ── Vue tabs ── */
    .pp-view-tabs {
        display: flex; gap: .25rem;
        background: var(--pp-bg);
        border: 1px solid var(--pp-border);
        border-radius: 10px; padding: .2rem;
    }
    .pp-vtab {
        padding: .4rem .875rem;
        border-radius: 8px;
        font-size: .8rem; font-weight: 600;
        cursor: pointer; border: none;
        background: transparent; color: var(--pp-muted);
        transition: .15s;
    }
    .pp-vtab.active {
        background: var(--pp-surface); color: var(--pp-ink);
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
    }

    /* ── Grille semaine (lecture seule) ── */
    .pp-grille-outer { overflow-x: auto; }
    .pp-grille {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        min-width: 640px;
        border: 1px solid var(--pp-border);
        border-radius: 12px;
        overflow: hidden;
        background: var(--pp-surface);
    }
    .pp-day-col {}
    .pp-day-head {
        padding: .75rem .5rem;
        text-align: center;
        background: #fafbfe;
        border-bottom: 1px solid var(--pp-border);
        border-right: 1px solid var(--pp-border);
        font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .07em;
        color: var(--pp-muted);
    }
    .pp-day-col:last-child .pp-day-head,
    .pp-day-col:last-child .pp-day-body { border-right: none; }
    .pp-day-head .pph-date {
        display: block; font-size: 1.1rem; font-weight: 700;
        color: var(--pp-ink); margin-top: .15rem;
    }
    .pp-day-head.today { background: var(--pp-gold-l); color: var(--pp-gold); }
    .pp-day-head.today .pph-date { color: var(--pp-gold); }

    .pp-day-body {
        padding: .5rem;
        border-right: 1px solid var(--pp-border);
        min-height: 180px;
        display: flex; flex-direction: column; gap: .4rem;
        background: var(--pp-surface);
    }

    .pp-slot {
        border-radius: 8px;
        padding: .4rem .55rem;
        font-size: .72rem;
        cursor: pointer;
        border-left: 3px solid;
        transition: .12s;
    }
    .pp-slot:hover { filter: brightness(.94); }
    .pp-slot-time   { font-weight: 700; font-size: .66rem; opacity: .7; }
    .pp-slot-name   { font-weight: 700; color: var(--pp-ink); margin: .1rem 0; }
    .pp-slot-detail { font-size: .66rem; opacity: .7; }

    .pp-empty-day {
        flex: 1; display: flex; align-items: center; justify-content: center;
        color: var(--pp-muted); font-size: .78rem; font-style: italic;
        padding: 1.5rem .5rem; text-align: center;
    }

    /* ── Vue liste ── */
    .pp-list-view { display: none; flex-direction: column; gap: 1.2rem; }
    .pp-list-view.active { display: flex; }

    .pp-lv-jour-title {
        display: flex; align-items: center; gap: .5rem;
        font-size: .9rem; font-weight: 700; color: var(--pp-ink);
        margin-bottom: .6rem;
        padding-bottom: .5rem;
        border-bottom: 1px solid var(--pp-border);
    }
    .pp-lv-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    .pp-lv-item {
        display: grid;
        grid-template-columns: 66px 3px 1fr;
        gap: .75rem; align-items: start;
        padding: .875rem 1rem;
        background: var(--pp-surface);
        border: 1px solid var(--pp-border);
        border-radius: 10px;
        margin-bottom: .4rem;
        cursor: pointer; transition: .15s;
    }
    .pp-lv-item:hover {
        border-color: var(--pp-gold);
        box-shadow: 0 2px 10px rgba(212,160,23,.12);
    }
    .pp-lv-time {
        font-size: .8rem; color: var(--pp-muted); text-align: center; line-height: 1.5;
    }
    .pp-lv-time strong { display: block; font-size: .95rem; font-weight: 700; color: var(--pp-ink); }
    .pp-lv-bar { border-radius: 99px; align-self: stretch; min-height: 36px; }
    .pp-lv-name { font-weight: 700; font-size: .9rem; color: var(--pp-ink); }
    .pp-lv-sub  { font-size: .78rem; color: var(--pp-muted); margin-top: .15rem; }

    /* ── Section paiements ── */
    .pp-pay-section { margin-top: .5rem; }
    .pp-pay-title {
        font-size: .875rem; font-weight: 700; color: var(--pp-ink);
        display: flex; align-items: center; gap: .5rem;
        margin-bottom: .75rem;
    }
    .pp-pay-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: .75rem; }
    .pp-pay-card {
        background: var(--pp-surface);
        border: 1px solid var(--pp-border);
        border-radius: 12px; overflow: hidden;
        transition: box-shadow .15s;
    }
    .pp-pay-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); }
    .pp-pay-card-top {
        padding: .875rem 1rem;
        display: flex; align-items: flex-start; gap: .625rem;
    }
    .pp-pay-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: grid; place-items: center; font-size: 1rem; flex-shrink: 0;
    }
    .pp-pay-libelle { font-weight: 700; font-size: .875rem; color: var(--pp-ink); }
    .pp-pay-sub     { font-size: .75rem; color: var(--pp-muted); margin-top: .1rem; }
    .pp-pay-card-bottom {
        padding: .625rem 1rem;
        border-top: 1px solid var(--pp-border);
        display: flex; justify-content: space-between; align-items: center;
        background: #fafbfe;
    }
    .pp-pay-montant { font-size: 1.05rem; font-weight: 800; color: var(--pp-ink); font-variant-numeric: tabular-nums; }
    .pp-pay-deadline-chip {
        padding: .2rem .6rem; border-radius: 999px;
        font-size: .7rem; font-weight: 700;
    }
    .pp-dl-ok   { background: var(--pp-teal-l); color: var(--pp-teal); }
    .pp-dl-soon { background: var(--pp-gold-l); color: var(--pp-gold); }
    .pp-dl-past { background: var(--pp-red-l);  color: var(--pp-red); }

    /* ── Empty state ── */
    .pp-empty {
        padding: 3.5rem 2rem; text-align: center;
        background: var(--pp-surface);
        border: 1px solid var(--pp-border);
        border-radius: 12px;
        color: var(--pp-muted);
    }
    .pp-empty-icon { font-size: 2.5rem; margin-bottom: .75rem; }

    /* ── Modal ── */
    .pp-modal-bg {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.5);
        backdrop-filter: blur(3px);
        z-index: 300;
        display: none; align-items: center; justify-content: center;
        padding: 1rem;
    }
    .pp-modal-bg.open { display: flex; }
    .pp-modal {
        background: var(--pp-surface);
        border-radius: 16px;
        width: 400px; max-width: 95vw;
        padding: 1.75rem;
        box-shadow: 0 20px 60px rgba(0,0,0,.18);
        animation: ppPop .2s ease;
    }
    @keyframes ppPop {
        from { transform: scale(.95) translateY(10px); opacity: 0; }
        to   { transform: scale(1)   translateY(0);    opacity: 1; }
    }
    .pp-modal-head { display: flex; justify-content: space-between; margin-bottom: 1.25rem; }
    .pp-modal-title { font-size: 1.2rem; font-weight: 700; color: var(--pp-ink); }
    .pp-modal-child { font-size: .8rem; color: var(--pp-muted); margin-top: .2rem; }
    .pp-modal-x {
        width: 28px; height: 28px;
        background: var(--pp-bg); border: 1px solid var(--pp-border);
        border-radius: 7px; cursor: pointer; display: grid; place-items: center;
        color: var(--pp-muted); font-size: .9rem;
    }
    .pp-modal-x:hover { color: var(--pp-ink); }
    .pp-modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; }
    .pp-modal-box {
        background: var(--pp-bg); border: 1px solid var(--pp-border);
        border-radius: 9px; padding: .65rem .875rem;
    }
    .pp-modal-box .lbl { font-size: .68rem; text-transform: uppercase; letter-spacing: .06em; color: var(--pp-muted); }
    .pp-modal-box .val { font-size: .88rem; font-weight: 600; color: var(--pp-ink); margin-top: .1rem; }

    @media (max-width: 640px) {
        .pp-grille { grid-template-columns: repeat(3, 1fr); }
        .pp-pay-grid { grid-template-columns: 1fr; }
        .pp-week-nav { flex-wrap: wrap; }
        .pp-week-label { width: 100%; order: -1; }
    }
</style>
@endpush

@section('content')

@php
    $jours   = ['lundi','mardi','mercredi','jeudi','vendredi','samedi'];
    $joursFr = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    $semaine = (int) request()->get('semaine', 0);
    $debutSemaine = \Carbon\Carbon::now()->startOfWeek()->addWeeks($semaine);

    // Couleurs enfants
    $avatarColors = ['#d4a017','#2563c0','#0d8a6f','#7048e8','#c9313b','#0891b2'];

    // Couleurs par matière (rotation)
    $matColors = ['#2563c0','#0d8a6f','#7048e8','#d4a017','#c9313b','#0891b2','#e67700','#8b5cf6'];
    $matMap = []; $matIdx = 0;

    // Icônes frais
    $fraisIcon = [
        'inscription'=>'📋','scolarite'=>'🎓','examen'=>'📝',
        'tenue'=>'👕','transport'=>'🚌','cantine'=>'🍽️','activite'=>'⚽','autre'=>'💳',
    ];
@endphp

<div class="pp-page">

{{-- ── SÉLECTEUR ENFANT ── --}}
<div class="pp-children-tabs">
    @forelse($schoolParent->apprenants as $idx => $enfant)
        @php $avColor = $avatarColors[$idx % count($avatarColors)]; @endphp
        <button class="pp-child-tab {{ $idx === 0 ? 'active' : '' }}"
                onclick="switchChild('pp-{{ $enfant->id }}', this)">
            <span class="pp-child-av" style="background:{{ $avColor }};">
                {{ mb_strtoupper(mb_substr($enfant->prenom, 0, 1)) }}
            </span>
            <span>
                {{ $enfant->prenom }} {{ $enfant->nom }}
                <span class="pp-child-classe">{{ $enfant->classe?->name ?? '—' }}</span>
            </span>
        </button>
    @empty
        <div style="padding:.75rem 1rem; font-size:.85rem; color:var(--pp-muted);">
            Aucun enfant enregistré.
        </div>
    @endforelse
</div>

{{-- ══════════ PANNEAUX PAR ENFANT ══════════ --}}
@forelse($schoolParent->apprenants as $idx => $enfant)
    @php
        $avColor  = $avatarColors[$idx % count($avatarColors)];
        $classeId = $enfant->class_id;
        $anneeInst = $enfant->institution?->academic_year ?? date('Y').'-'.(date('Y')+1);

        // Emploi du temps de la classe de cet enfant
        $edtEnfant = \App\Models\EmploiDuTemps::where('classe_id', $classeId)
            ->where('annee_academique', $anneeInst)
            ->where('statut', 'actif')
            ->with(['subject', 'teacher'])
            ->get();

        // Organiser par jour
        $grilleEnfant = [];
        foreach($jours as $j) {
            $grilleEnfant[$j] = $edtEnfant->where('jour', $j)->sortBy('heure_debut')->values();
        }

        // Nombre de cours par semaine
        $totalCoursEnfant = $edtEnfant->count();

        // Programmes de paiement de la classe
        $programmesEnfant = $classeId
            ? \App\Models\ProgrammePaiement::where('institution_id', $enfant->institution_id ?? 0)
                ->where('annee_academique', $anneeInst)
                ->where('statut', 'actif')
                ->where(fn($q) => $q->whereNull('classe_id')->orWhere('classe_id', $classeId))
                ->orderBy('date_echeance')
                ->get()
            : collect();

        // Couleurs matières pour cet enfant
        $matMapEnfant = []; $mIdx = 0;
        foreach ($edtEnfant as $cr) {
            $mat = $cr->subject?->name ?? 'Cours';
            if (!isset($matMapEnfant[$mat])) {
                $matMapEnfant[$mat] = $matColors[$mIdx % count($matColors)];
                $mIdx++;
            }
        }
    @endphp

    <div class="pp-panel {{ $idx === 0 ? 'active' : '' }}" id="pp-{{ $enfant->id }}">

        {{-- Infos enfant --}}
        <div class="pp-info-bar">
            <div class="pp-info-pill">
                🎓 Classe <strong>{{ $enfant->classe?->name ?? '—' }}</strong>
            </div>
            @if($enfant->niveau)
                <div class="pp-info-pill">📚 <strong>{{ $enfant->niveau->name }}</strong></div>
            @endif
            @if($enfant->filiere)
                <div class="pp-info-pill">🗂 <strong>{{ $enfant->filiere->name }}</strong></div>
            @endif
            <div class="pp-info-pill">📋 <strong>{{ $totalCoursEnfant }}</strong> cours/semaine</div>
            @if($enfant->matricule)
                <div class="pp-info-pill">🪪 <strong>{{ $enfant->matricule }}</strong></div>
            @endif
        </div>

        {{-- Nav semaine + toggles vue --}}
        <div style="display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;">
            <div class="pp-week-nav" style="flex:1; min-width:280px;">
                <a href="{{ request()->fullUrlWithQuery(['semaine' => $semaine - 1, 'enfant' => $enfant->id]) }}" class="pp-nav-btn">‹</a>
                <span class="pp-week-label">
                    Semaine du {{ $debutSemaine->format('d') }}
                    au {{ $debutSemaine->copy()->endOfWeek()->subDay()->format('d M Y') }}
                </span>
                <a href="{{ request()->fullUrlWithQuery(['semaine' => $semaine + 1, 'enfant' => $enfant->id]) }}" class="pp-nav-btn">›</a>
                <a href="{{ request()->fullUrlWithQuery(['semaine' => 0, 'enfant' => $enfant->id]) }}" class="pp-today-link">Auj.</a>
            </div>
            <div class="pp-view-tabs">
                <button class="pp-vtab active" id="vbg-{{ $enfant->id }}"
                        onclick="switchView({{ $enfant->id }}, 'grille')">Grille</button>
                <button class="pp-vtab" id="vbl-{{ $enfant->id }}"
                        onclick="switchView({{ $enfant->id }}, 'liste')">Liste</button>
            </div>
        </div>

        {{-- ── VUE GRILLE ── --}}
        <div id="vg-{{ $enfant->id }}" class="pp-grille-outer">
            <div class="pp-grille" style="border-top: 3px solid {{ $avColor }};">
                @foreach($jours as $ji => $jour)
                    @php
                        $date  = $debutSemaine->copy()->addDays($ji);
                        $cours = $grilleEnfant[$jour];
                    @endphp
                    <div class="pp-day-col">
                        <div class="pp-day-head {{ $date->isToday() ? 'today' : '' }}">
                            {{ mb_strtoupper(mb_substr($joursFr[$ji], 0, 3)) }}
                            <span class="pph-date">{{ $date->format('d') }}</span>
                        </div>
                        <div class="pp-day-body">
                            @forelse($cours as $cr)
                                @php
                                    $mat   = $cr->subject?->name ?? 'Cours';
                                    $mc    = $matMapEnfant[$mat] ?? '#2563c0';
                                @endphp
                                @php
                                    $crData = json_encode([
                                        'matiere'    => $mat,
                                        'enseignant' => trim(($cr->teacher?->prenom ?? '').' '.($cr->teacher?->nom ?? '')),
                                        'salle'      => $cr->salle ?? '—',
                                        'debut'      => substr($cr->heure_debut,0,5),
                                        'fin'        => substr($cr->heure_fin,0,5),
                                        'type'       => $typeLabels[$cr->type] ?? $cr->type,
                                        'classe'     => $enfant->classe?->name ?? '—',
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT);
                                    $enfantPrenom = addslashes($enfant->prenom);
                                @endphp
                                <div class="pp-slot"
                                     style="background:{{ $mc }}12; border-left-color:{{ $mc }};"
                                     onclick="openPpModal({{ $crData }}, '{{ $enfantPrenom }}')"
                                     >
                                    <div class="pp-slot-time" style="color:{{ $mc }};">
                                        {{ substr($cr->heure_debut,0,5) }}–{{ substr($cr->heure_fin,0,5) }}
                                    </div>
                                    <div class="pp-slot-name">{{ $mat }}</div>
                                    @if($cr->teacher)
                                        <div class="pp-slot-detail">
                                            👤 {{ $cr->teacher->prenom }} {{ $cr->teacher->nom }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="pp-empty-day">—</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── VUE LISTE ── --}}
        <div id="vl-{{ $enfant->id }}" class="pp-list-view">
            @php $hasAny = false; @endphp

            @foreach($jours as $ji => $jour)
                @php
                    $cours = $grilleEnfant[$jour];
                    $date  = $debutSemaine->copy()->addDays($ji);
                @endphp

                @if($cours->isNotEmpty())
                    @php
                        $hasAny = true;
                        $firstMat = $cours->first()->subject?->name ?? 'Cours';
                        $dotColor = $matMapEnfant[$firstMat] ?? $avColor;
                    @endphp
                    <div>
                        <div class="pp-lv-jour-title">
                            <span class="pp-lv-dot" style="background:{{ $dotColor }};"></span>
                            {{ $joursFr[$ji] }}
                            <span style="font-size:.78rem;color:var(--pp-muted);">
                                · {{ $date->format('d M') }}
                            </span>
                        </div>

                        @foreach($cours as $cr)
                            @php
                                $mat   = $cr->subject?->name ?? 'Cours';
                                $mc    = $matMapEnfant[$mat] ?? '#2563c0';
                                $debut = \Carbon\Carbon::parse($cr->heure_debut);
                                $fin   = \Carbon\Carbon::parse($cr->heure_fin);
                                $crDataLv = json_encode([
                                    'matiere'    => $mat,
                                    'enseignant' => trim(($cr->teacher?->prenom ?? '').' '.($cr->teacher?->nom ?? '')),
                                    'salle'      => $cr->salle ?? '—',
                                    'debut'      => substr($cr->heure_debut,0,5),
                                    'fin'        => substr($cr->heure_fin,0,5),
                                    'type'       => $typeLabels[$cr->type] ?? $cr->type,
                                    'classe'     => $enfant->classe?->name ?? '—',
                                ], JSON_HEX_APOS | JSON_HEX_QUOT);
                                $enfantPrenomLv = addslashes($enfant->prenom);
                            @endphp
                            <div class="pp-lv-item"
                                 onclick="openPpModal({{ $crDataLv }}, '{{ $enfantPrenomLv }}')"
                                 >
                                <div class="pp-lv-time">
                                    <strong>{{ $debut->format('H:i') }}</strong>
                                    {{ $fin->format('H:i') }}
                                </div>
                                <div class="pp-lv-bar" style="background:{{ $mc }};"></div>
                                <div>
                                    <div class="pp-lv-name">{{ $mat }}</div>
                                    <div class="pp-lv-sub">
                                        @if($cr->teacher) 👤 {{ $cr->teacher->prenom }} {{ $cr->teacher->nom }} · @endif
                                        @if($cr->salle) 📍 {{ $cr->salle }} @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach

            @if(!$hasAny)
                <div class="pp-empty">
                    <div class="pp-empty-icon">📭</div>
                    <div style="font-size:.95rem;font-weight:700;color:var(--pp-ink);margin-bottom:.3rem;">
                        Aucun cours configuré
                    </div>
                    <div style="font-size:.85rem;">L'emploi du temps n'a pas encore été rempli.</div>
                </div>
            @endif
        </div>

        {{-- ── PROGRAMMES DE PAIEMENT ── --}}
        @if($programmesEnfant->isNotEmpty())
            <div class="pp-pay-section">
                <div class="pp-pay-title">
                    💰 Échéances de paiement — {{ $anneeInst }}
                    <span style="font-size:.78rem;font-weight:500;color:var(--pp-muted);">
                        ({{ $programmesEnfant->count() }} échéance(s))
                    </span>
                </div>
                <div class="pp-pay-grid">
                    @foreach($programmesEnfant as $prog)
                        @php
                            $jr = $prog->jours_restants ?? 999;
                            $dlClass = $jr < 0 ? 'pp-dl-past' : ($jr <= 7 ? 'pp-dl-soon' : 'pp-dl-ok');
                            $dlText  = $jr < 0 ? abs($jr).'j en retard' : ($jr === 0 ? 'Aujourd\'hui !' : 'Dans '.$jr.'j');
                            $icon    = $fraisIcon[$prog->type_frais] ?? '💳';
                            $iconBg  = $jr < 0 ? 'var(--pp-red-l)' : ($jr <= 7 ? 'var(--pp-gold-l)' : 'var(--pp-teal-l)');
                        @endphp
                        <div class="pp-pay-card">
                            <div class="pp-pay-card-top">
                                <div class="pp-pay-icon" style="background:{{ $iconBg }};">{{ $icon }}</div>
                                <div>
                                    <div class="pp-pay-libelle">{{ $prog->libelle }}</div>
                                    <div class="pp-pay-sub">
                                        {{ $prog->date_echeance?->format('d/m/Y') }}
                                        @if(!$prog->obligatoire)
                                            · <em>Facultatif</em>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="pp-pay-card-bottom">
                                <span class="pp-pay-montant">
                                    {{ number_format($prog->montant, 0, ',', ' ') }}
                                    <span style="font-size:.75rem;font-weight:500;color:var(--pp-muted);">
                                        {{ $prog->devise ?? 'FCFA' }}
                                    </span>
                                </span>
                                <span class="pp-pay-deadline-chip {{ $dlClass }}">{{ $dlText }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>{{-- /pp-panel --}}
@empty
    <div class="pp-empty">
        <div class="pp-empty-icon">👨‍👩‍👧</div>
        <div style="font-size:.95rem;font-weight:700;color:var(--pp-ink);margin-bottom:.3rem;">
            Aucun enfant associé
        </div>
        <div style="font-size:.85rem;">Contactez l'administration pour lier vos enfants à votre compte.</div>
    </div>
@endforelse

</div>{{-- /pp-page --}}

{{-- ── MODAL DÉTAIL COURS ── --}}
<div class="pp-modal-bg" id="pp-modal-bg" onclick="closePpModal(event)">
    <div class="pp-modal">
        <div class="pp-modal-head">
            <div>
                <div class="pp-modal-title" id="pp-m-title">Matière</div>
                <div class="pp-modal-child" id="pp-m-child"></div>
            </div>
            <button class="pp-modal-x"
                    onclick="document.getElementById('pp-modal-bg').classList.remove('open')">✕</button>
        </div>
        <div class="pp-modal-grid" id="pp-m-body"></div>
    </div>
</div>

@push('scripts')
<script>
function switchChild(id, btn) {
    document.querySelectorAll('.pp-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.pp-child-tab').forEach(t => t.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    btn.classList.add('active');
}

function switchView(enfantId, view) {
    const g  = document.getElementById('vg-' + enfantId);
    const l  = document.getElementById('vl-' + enfantId);
    const bg = document.getElementById('vbg-' + enfantId);
    const bl = document.getElementById('vbl-' + enfantId);

    if (view === 'grille') {
        g.style.display = 'block'; l.classList.remove('active');
        bg.classList.add('active'); bl.classList.remove('active');
    } else {
        g.style.display = 'none'; l.classList.add('active');
        bl.classList.add('active'); bg.classList.remove('active');
    }
}

function openPpModal(data, enfant) {
    document.getElementById('pp-m-title').textContent = data.matiere;
    document.getElementById('pp-m-child').textContent = '📚 ' + enfant;
    document.getElementById('pp-m-body').innerHTML = `
        <div class="pp-modal-box">
            <div class="lbl">Enseignant</div>
            <div class="val">${data.enseignant || '—'}</div>
        </div>
        <div class="pp-modal-box">
            <div class="lbl">Salle</div>
            <div class="val">${data.salle || '—'}</div>
        </div>
        <div class="pp-modal-box">
            <div class="lbl">Début</div>
            <div class="val">${data.debut}</div>
        </div>
        <div class="pp-modal-box">
            <div class="lbl">Fin</div>
            <div class="val">${data.fin}</div>
        </div>
        <div class="pp-modal-box" style="grid-column:span 2;">
            <div class="lbl">Type</div>
            <div class="val">${data.type}</div>
        </div>
    `;
    document.getElementById('pp-modal-bg').classList.add('open');
}

function closePpModal(e) {
    if (e.target === document.getElementById('pp-modal-bg'))
        document.getElementById('pp-modal-bg').classList.remove('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('pp-modal-bg').classList.remove('open');
});
</script>
@endpush

@endsection