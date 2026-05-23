@extends('teacher.master')

@section('title', 'Mon Planning — ' . ($teacher->prenom ?? '') . ' ' . ($teacher->nom ?? ''))
@section('page-title', 'Mon Planning')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');

        /* ══ DESIGN TOKENS ══ */
        :root {
            --tp-bg: #f4f6fb;
            --tp-surface: #ffffff;
            --tp-border: #e8ecf4;
            --tp-ink: #111827;
            --tp-muted: #6b7280;
            --tp-blue: #3b5bdb;
            --tp-blue-l: #eef2ff;
            --tp-green: #0ca678;
            --tp-green-l: #e6fcf5;
            --tp-amber: #e67700;
            --tp-amber-l: #fff4e6;
            --tp-red: #e03131;
            --tp-red-l: #fff5f5;
            --tp-violet: #7048e8;
            --tp-violet-l: #f3f0ff;
            --tp-teal: #0891b2;
            --tp-teal-l: #e0f7fa;

            /* Couleurs par jour */
            --c-lun: #3b5bdb;
            --c-mar: #7048e8;
            --c-mer: #0ca678;
            --c-jeu: #e67700;
            --c-ven: #e03131;
            --c-sam: #0891b2;
        }

        .tp-page {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        /* ── Hero banner ── */
        .tp-hero {
            background: linear-gradient(120deg, #1a1f36 0%, #232a4e 50%, #1a3a2e 100%);
            border-radius: 14px;
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            position: relative;
            overflow: hidden;
        }

        .tp-hero::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 91, 219, .25) 0%, transparent 70%);
            top: -80px;
            right: -60px;
            pointer-events: none;
        }

        .tp-hero-info {
            position: relative;
            z-index: 1;
        }

        .tp-hero-info h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            margin: 0 0 .3rem;
            letter-spacing: -.02em;
        }

        .tp-hero-info p {
            font-size: .85rem;
            color: rgba(255, 255, 255, .5);
            margin: 0;
        }

        .tp-hero-chips {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            margin-top: .875rem;
        }

        .tp-hero-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .28rem .75rem;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .8);
        }

        .tp-hero-right {
            position: relative;
            z-index: 1;
            text-align: right;
        }

        .tp-hero-stat-big {
            font-size: 3rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        .tp-hero-stat-label {
            font-size: .72rem;
            color: rgba(255, 255, 255, .4);
            margin-top: .2rem;
        }

        /* ── Nav semaine ── */
        .tp-week-nav {
            background: var(--tp-surface);
            border: 1px solid var(--tp-border);
            border-radius: 12px;
            padding: .875rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .tp-nav-btn {
            width: 34px;
            height: 34px;
            background: var(--tp-bg);
            border: 1px solid var(--tp-border);
            border-radius: 8px;
            display: grid;
            place-items: center;
            cursor: pointer;
            font-size: 1rem;
            color: var(--tp-muted);
            transition: .15s;
            text-decoration: none;
        }

        .tp-nav-btn:hover {
            background: var(--tp-blue);
            color: #fff;
            border-color: var(--tp-blue);
        }

        .tp-week-label {
            flex: 1;
            text-align: center;
            font-size: .95rem;
            font-weight: 600;
            color: var(--tp-ink);
        }

        .tp-today-btn {
            padding: .4rem .9rem;
            background: var(--tp-blue-l);
            border: 1px solid rgba(59, 91, 219, .2);
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 600;
            color: var(--tp-blue);
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
        }

        .tp-today-btn:hover {
            background: var(--tp-blue);
            color: #fff;
        }

        /* ── Vue toggles ── */
        .tp-view-toggle {
            display: flex;
            gap: 0;
            background: var(--tp-bg);
            border: 1px solid var(--tp-border);
            border-radius: 10px;
            padding: .2rem;
        }

        .tp-view-btn {
            padding: .4rem .9rem;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--tp-muted);
            transition: .15s;
        }

        .tp-view-btn.active {
            background: var(--tp-surface);
            color: var(--tp-ink);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }

        /* ── Vue grille ── */
        .tp-grid-wrap {
            overflow-x: auto;
        }

        .tp-grid {
            display: grid;
            grid-template-columns: 56px repeat(6, 1fr);
            min-width: 700px;
            border: 1px solid var(--tp-border);
            border-radius: 12px;
            overflow: hidden;
            background: var(--tp-surface);
        }

        /* En-têtes jours */
        .tp-gh {
            /* grid header */
            padding: .7rem .5rem;
            text-align: center;
            background: #fafbfe;
            border-bottom: 1px solid var(--tp-border);
            border-right: 1px solid var(--tp-border);
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--tp-muted);
        }

        .tp-gh:last-child {
            border-right: none;
        }

        .tp-gh.today {
            color: var(--tp-blue);
            background: var(--tp-blue-l);
        }

        .tp-gh .day-num {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--tp-ink);
            line-height: 1.1;
            margin-top: .2rem;
        }

        .tp-gh.today .day-num {
            background: var(--tp-blue);
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
        }

        /* Colonne heure */
        .tp-time-slot {
            height: 54px;
            padding: .25rem .4rem 0 0;
            text-align: right;
            font-size: .68rem;
            font-weight: 500;
            color: var(--tp-muted);
            border-top: 1px solid rgba(232, 236, 244, .7);
            border-right: 1px solid var(--tp-border);
        }

        .tp-time-slot:first-of-type {
            border-top: none;
        }

        /* Cellules jours */
        .tp-day-cell {
            height: 54px;
            border-top: 1px solid rgba(232, 236, 244, .7);
            border-right: 1px solid var(--tp-border);
            position: relative;
        }

        .tp-day-cell:last-child {
            border-right: none;
        }

        .tp-day-cell.today-col {
            background: rgba(59, 91, 219, .025);
        }

        /* Blocs cours */
        .tp-cours {
            position: absolute;
            left: 3px;
            right: 3px;
            border-radius: 7px;
            padding: .3rem .5rem;
            font-size: .72rem;
            cursor: pointer;
            overflow: hidden;
            transition: transform .12s, box-shadow .12s;
            z-index: 1;
        }

        .tp-cours:hover {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 4px 14px rgba(0, 0, 0, .15);
            z-index: 5;
        }

        .tp-cours-title {
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tp-cours-sub {
            opacity: .75;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: .1rem;
        }

        .tp-cours-time {
            opacity: .6;
            font-size: .65rem;
            margin-top: .1rem;
        }

        /* Couleurs par type */
        .type-cours {
            background: #eff2ff;
            border-left: 3px solid #3b5bdb;
            color: #1e40af;
        }

        .type-evaluation {
            background: #fff8e6;
            border-left: 3px solid #e67700;
            color: #92400e;
        }

        .type-examen {
            background: #fff5f5;
            border-left: 3px solid #e03131;
            color: #991b1b;
        }

        .type-rattrapage {
            background: #f3f0ff;
            border-left: 3px solid #7048e8;
            color: #4c1d95;
        }

        .type-activite {
            background: #e6fcf5;
            border-left: 3px solid #0ca678;
            color: #064e3b;
        }

        .type-pause {
            background: #f9fafb;
            border-left: 3px solid #9ca3af;
            color: #374151;
        }

        /* ── Vue liste ── */
        .tp-list {
            display: none;
            flex-direction: column;
            gap: 1rem;
        }

        .tp-list.active {
            display: flex;
        }

        .tp-jour-section {}

        .tp-jour-header {
            display: flex;
            align-items: center;
            gap: .625rem;
            margin-bottom: .6rem;
        }

        .tp-jour-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .tp-jour-title {
            font-size: .9rem;
            font-weight: 700;
            color: var(--tp-ink);
            font-family: 'Space Grotesk', sans-serif;
        }

        .tp-jour-count {
            padding: .15rem .5rem;
            border-radius: 99px;
            font-size: .7rem;
            font-weight: 700;
            background: var(--tp-bg);
            color: var(--tp-muted);
            border: 1px solid var(--tp-border);
        }

        .tp-cours-row {
            display: grid;
            grid-template-columns: 72px 4px 1fr auto;
            gap: .75rem;
            align-items: center;
            padding: .875rem 1rem;
            background: var(--tp-surface);
            border: 1px solid var(--tp-border);
            border-radius: 10px;
            margin-bottom: .4rem;
            cursor: pointer;
            transition: border-color .15s, box-shadow .15s;
        }

        .tp-cours-row:hover {
            border-color: var(--tp-blue);
            box-shadow: 0 2px 10px rgba(59, 91, 219, .1);
        }

        .tp-cr-time {
            font-size: .8rem;
            color: var(--tp-muted);
            text-align: center;
            line-height: 1.4;
        }

        .tp-cr-time strong {
            display: block;
            font-size: .95rem;
            color: var(--tp-ink);
            font-weight: 700;
        }

        .tp-cr-bar {
            height: 100%;
            min-height: 36px;
            border-radius: 99px;
        }

        .tp-cr-info-title {
            font-weight: 600;
            font-size: .9rem;
            color: var(--tp-ink);
        }

        .tp-cr-info-sub {
            font-size: .78rem;
            color: var(--tp-muted);
            margin-top: .15rem;
        }

        .tp-cr-badge {
            padding: .25rem .65rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        /* ── Semaine vide ── */
        .tp-empty {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--tp-muted);
            background: var(--tp-surface);
            border: 1px solid var(--tp-border);
            border-radius: 12px;
        }

        .tp-empty-icon {
            font-size: 2.5rem;
            margin-bottom: .75rem;
        }

        .tp-empty-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--tp-ink);
            margin-bottom: .3rem;
        }

        /* ── Modal ── */
        .tp-modal-bg {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(3px);
            z-index: 300;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .tp-modal-bg.open {
            display: flex;
        }

        .tp-modal {
            background: var(--tp-surface);
            border-radius: 16px;
            width: 440px;
            max-width: 95vw;
            padding: 1.75rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            animation: tpPop .2s ease;
        }

        @keyframes tpPop {
            from {
                transform: scale(.95) translateY(8px);
                opacity: 0;
            }

            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        .tp-modal-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.25rem;
        }

        .tp-modal-subject {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--tp-ink);
            line-height: 1.2;
        }

        .tp-modal-close {
            width: 28px;
            height: 28px;
            background: var(--tp-bg);
            border: 1px solid var(--tp-border);
            border-radius: 7px;
            cursor: pointer;
            color: var(--tp-muted);
            display: grid;
            place-items: center;
            font-size: .9rem;
        }

        .tp-modal-close:hover {
            color: var(--tp-ink);
        }

        .tp-modal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .6rem;
        }

        .tp-modal-item {
            background: var(--tp-bg);
            border: 1px solid var(--tp-border);
            border-radius: 9px;
            padding: .65rem .875rem;
        }

        .tp-modal-label {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--tp-muted);
        }

        .tp-modal-value {
            font-size: .88rem;
            font-weight: 600;
            color: var(--tp-ink);
            margin-top: .1rem;
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .tp-hero {
                padding: 1.1rem;
            }

            .tp-hero-stat-big {
                font-size: 2rem;
            }

            .tp-week-nav {
                flex-wrap: wrap;
                gap: .5rem;
            }

            .tp-week-label {
                order: -1;
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endpush

@section('content')

    @php
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
        $joursFr = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $heures = range(7, 18);
        $semaine = (int) request()->get('semaine', 0);
        $debutSemaine = \Carbon\Carbon::now()->startOfWeek()->addWeeks($semaine);

        // Compter les cours de l'enseignant
$totalCreneaux = collect($grille ?? [])
    ->flatten(1)
    ->filter(fn($cr) => $cr->teacher_id == $teacher->id)
    ->count();
$totalH = collect($grille ?? [])
    ->flatten(1)
    ->filter(fn($cr) => $cr->teacher_id == $teacher->id)
    ->sum(function ($cr) {
        try {
            return \Carbon\Carbon::parse($cr->heure_debut)->diffInMinutes(
                \Carbon\Carbon::parse($cr->heure_fin),
            ) / 60;
        } catch (\Throwable) {
            return 0;
        }
    });

$typeBadgeClass = [
    'cours' => 'type-cours',
    'evaluation' => 'type-evaluation',
    'examen' => 'type-examen',
    'rattrapage' => 'type-rattrapage',
    'activite' => 'type-activite',
    'pause' => 'type-pause',
];

$jourDotColors = [
    'lundi' => '#3b5bdb',
    'mardi' => '#7048e8',
    'mercredi' => '#0ca678',
    'jeudi' => '#e67700',
    'vendredi' => '#e03131',
    'samedi' => '#0891b2',
        ];
        $jourBarColors = $jourDotColors;
    @endphp

    <div class="tp-page">

        {{-- ── HERO ── --}}
        <div class="tp-hero">
            <div class="tp-hero-info">
                <h2>📅 Mon Emploi du Temps</h2>
                <p>{{ $teacher->prenom }} {{ $teacher->nom }} · {{ $institution->name }}</p>
                <div class="tp-hero-chips">
                    @if ($teacher->specialite)
                        <span class="tp-hero-chip">📚 {{ $teacher->specialite }}</span>
                    @endif
                    <span class="tp-hero-chip">🏛 {{ $teacher->classes->count() }} classe(s)</span>
                    <span class="tp-hero-chip">⏱ {{ number_format($totalH, 1) }}h / semaine</span>
                    <span class="tp-hero-chip">{{ $annee }}</span>
                </div>
            </div>
            <div class="tp-hero-right">
                <div class="tp-hero-stat-big">{{ $totalCreneaux }}</div>
                <div class="tp-hero-stat-label">créneaux cette semaine</div>
            </div>
        </div>

        {{-- ── NAVIGATION SEMAINE ── --}}
        <div class="tp-week-nav">
            <a href="{{ request()->fullUrlWithQuery(['semaine' => $semaine - 1]) }}" class="tp-nav-btn">‹</a>
            <span class="tp-week-label">
                Semaine du {{ $debutSemaine->format('d') }}
                au {{ $debutSemaine->copy()->endOfWeek()->subDay()->format('d M Y') }}
            </span>
            <a href="{{ request()->fullUrlWithQuery(['semaine' => $semaine + 1]) }}" class="tp-nav-btn">›</a>
            <a href="{{ request()->fullUrlWithQuery(['semaine' => 0]) }}" class="tp-today-btn">Aujourd'hui</a>
            <div class="tp-view-toggle">
                <button class="tp-view-btn active" id="btn-grille" onclick="switchView('grille')">Grille</button>
                <button class="tp-view-btn" id="btn-liste" onclick="switchView('liste')">Liste</button>
            </div>
        </div>

        {{-- ══════════ VUE GRILLE ══════════ --}}
        <div id="view-grille" class="tp-grid-wrap">
            <div class="tp-grid">

                {{-- En-têtes --}}
                <div class="tp-gh" style="border-right:1px solid var(--tp-border);"></div>
                @foreach ($jours as $i => $jour)
                    @php $date = $debutSemaine->copy()->addDays($i); @endphp
                    <div class="tp-gh {{ $date->isToday() ? 'today' : '' }}">
                        {{ mb_strtoupper(mb_substr($joursFr[$i], 0, 3)) }}
                        <span class="day-num">{{ $date->format('d') }}</span>
                    </div>
                @endforeach

                {{-- Lignes horaires --}}
                @foreach ($heures as $h)
                    <div class="tp-time-slot">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</div>
                    @foreach ($jours as $i => $jour)
                        @php
                            $date = $debutSemaine->copy()->addDays($i);
                            $creneauxDuSlot = collect($grille[$jour] ?? [])
                                ->filter(fn($cr) => $cr->teacher_id == $teacher->id)
                                ->filter(fn($cr) => (int) \Carbon\Carbon::parse($cr->heure_debut)->format('H') === $h);
                        @endphp
                        <div class="tp-day-cell {{ $date->isToday() ? 'today-col' : '' }}">
                            @foreach ($creneauxDuSlot as $cr)
                                @php
                                    $debut = \Carbon\Carbon::parse($cr->heure_debut);
                                    $fin = \Carbon\Carbon::parse($cr->heure_fin);
                                    $duree = max(44, $debut->diffInMinutes($fin));
                                    $top = ($debut->minute / 60) * 54;
                                    $height = ($duree / 60) * 54;

                                    // Préparer les données pour le modal
                                    $tpData = [
                                        'matiere' => $cr->subject?->name ?? 'Cours',
                                        'classe' => $cr->classe?->name ?? '—',
                                        'salle' => $cr->salle ?? '—',
                                        'type' => $typeLabels[$cr->type] ?? $cr->type,
                                        'debut' => substr($cr->heure_debut, 0, 5),
                                        'fin' => substr($cr->heure_fin, 0, 5),
                                        'jour' => $joursFr[$i],
                                        'notes' => $cr->notes ?? '—',
                                    ];
                                @endphp
                                <div class="tp-cours {{ $typeBadgeClass[$cr->type] ?? 'type-cours' }}"
                                    style="top:{{ $top }}px; height:{{ $height }}px;"
                                    onclick='openTpModal(@json($tpData))'>
                                    <div class="tp-cours-title">{{ $cr->subject?->name ?? 'Cours' }}</div>
                                    <div class="tp-cours-sub">{{ $cr->classe?->name ?? '' }}</div>
                                    <div class="tp-cours-time">
                                        {{ substr($cr->heure_debut, 0, 5) }}–{{ substr($cr->heure_fin, 0, 5) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- ══════════ VUE LISTE ══════════ --}}
        <div id="view-liste" class="tp-list">
            @php $hasAny = false; @endphp

            @foreach ($grille as $jour => $coursDuJour)
                @php $hasAny = true; @endphp
                <div class="tp-jour-section">
                    <div class="tp-jour-header">
                        <span class="tp-jour-title">{{ ucfirst($jour) }}</span>
                        <span class="tp-jour-count">{{ $coursDuJour->count() }} cours</span>
                    </div>

                    @foreach ($coursDuJour as $cr)
                        @php
                            $debut = \Carbon\Carbon::parse($cr->heure_debut);
                            $fin = \Carbon\Carbon::parse($cr->heure_fin);
                            $duree = $debut->diffInMinutes($fin);
                            $top = (($debut->hour * 60 + $debut->minute) / 60) * 54;
                            $height = ($duree / 60) * 54;

                            $tpData = [
                                'matiere' => $cr->subject?->name ?? 'Cours',
                                'classe' => $cr->classe?->name ?? '—',
                                'salle' => $cr->salle ?? '—',
                                'type' => $typeLabels[$cr->type] ?? $cr->type,
                                'debut' => $debut->format('H:i'),
                                'fin' => $fin->format('H:i'),
                                'jour' => ucfirst($jour),
                                'notes' => $cr->notes ?? '—',
                            ];

                            $color = $typeColors[$cr->type] ?? '#3b82f6';
                        @endphp

                        <div class="tp-cours {{ $typeBadgeClass[$cr->type] ?? 'type-cours' }}"
                            style="top:{{ $top }}px; height:{{ $height }}px; background:{{ $color }};"
                            onclick='openTpModal(@json($tpData))'>
                            <div class="tp-cours-title">{{ $cr->subject?->name ?? 'Cours' }}</div>
                            <div class="tp-cours-sub">
                                {{ $cr->classe?->name ?? '—' }} @if ($cr->salle)
                                    · 📍 {{ $cr->salle }}
                                @endif
                            </div>
                            <span class="tp-cours-badge">{{ $typeLabels[$cr->type] ?? $cr->type }}</span>
                        </div>
                    @endforeach
                </div>
            @endforeach

            @if (!$hasAny)
                <div class="tp-empty">
                    <div class="tp-empty-icon">📭</div>
                    <div class="tp-empty-title">Aucun cours cette semaine</div>
                    <div style="font-size:.85rem; margin-top:.3rem;">
                        Votre emploi du temps n'a pas encore été configuré pour cette période.
                    </div>
                </div>
            @endif
        </div>

    </div>{{-- /tp-page --}}

    {{-- ── MODAL DÉTAIL ── --}}
    <div class="tp-modal-bg" id="tp-modal-bg" onclick="closeTpModal(event)">
        <div class="tp-modal">
            <div class="tp-modal-head">
                <div>
                    <div class="tp-modal-subject" id="tp-m-subject">Cours</div>
                    <div style="font-size:.78rem;color:var(--tp-muted);margin-top:.2rem;" id="tp-m-jour"></div>
                </div>
                <button class="tp-modal-close"
                    onclick="document.getElementById('tp-modal-bg').classList.remove('open')">✕</button>
            </div>
            <div class="tp-modal-grid" id="tp-m-body"></div>
        </div>
    </div>

    @push('scripts')
        <script>
            function switchView(view) {
                const grille = document.getElementById('view-grille');
                const liste = document.getElementById('view-liste');
                const bg = document.getElementById('btn-grille');
                const bl = document.getElementById('btn-liste');

                if (view === 'grille') {
                    grille.style.display = 'block';
                    liste.classList.remove('active');
                    bg.classList.add('active');
                    bl.classList.remove('active');
                } else {
                    grille.style.display = 'none';
                    liste.classList.add('active');
                    bl.classList.add('active');
                    bg.classList.remove('active');
                }
            }

            function openTpModal(data) {
                document.getElementById('tp-m-subject').textContent = data.matiere;
                document.getElementById('tp-m-jour').textContent = data.jour;
                document.getElementById('tp-m-body').innerHTML = `
        <div class="tp-modal-item">
            <div class="tp-modal-label">Classe</div>
            <div class="tp-modal-value">${data.classe}</div>
        </div>
        <div class="tp-modal-item">
            <div class="tp-modal-label">Salle</div>
            <div class="tp-modal-value">${data.salle}</div>
        </div>
        <div class="tp-modal-item">
            <div class="tp-modal-label">Début</div>
            <div class="tp-modal-value">${data.debut}</div>
        </div>
        <div class="tp-modal-item">
            <div class="tp-modal-label">Fin</div>
            <div class="tp-modal-value">${data.fin}</div>
        </div>
        <div class="tp-modal-item" style="grid-column:span 2;">
            <div class="tp-modal-label">Type</div>
            <div class="tp-modal-value">${data.type}</div>
        </div>
        <div class="tp-modal-item" style="grid-column:span 2;">
            <div class="tp-modal-label">Notes</div>
            <div class="tp-modal-value">${data.notes || '—'}</div>
        </div>
    `;
                document.getElementById('tp-modal-bg').classList.add('open');
            }

            function closeTpModal(e) {
                if (e.target === document.getElementById('tp-modal-bg'))
                    document.getElementById('tp-modal-bg').classList.remove('open');
            }

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') document.getElementById('tp-modal-bg').classList.remove('open');
            });
        </script>
    @endpush

@endsection
