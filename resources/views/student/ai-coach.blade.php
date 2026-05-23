@extends('student.master')

@section('title', 'Mon Coach IA')
@section('page-title', 'Coach IA')
@section('page-sub', 'Votre assistant personnel d\'apprentissage')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
<style>
    :root {
        --coach-bg: #f7f6f3;
        --coach-surface: #ffffff;
        --coach-border: #e8e6e0;
        --coach-ink: #1a1814;
        --coach-muted: #7a7670;
        --coach-accent: #2d5a27;
        --coach-accent-light: #eef5ec;
        --coach-accent-mid: #4a8a42;
        --coach-user-bg: #2d5a27;
        --coach-user-ink: #ffffff;
        --coach-ai-bg: #ffffff;
        --coach-ai-ink: #1a1814;
        --coach-shadow: 0 2px 12px rgba(0,0,0,.06);
        --coach-shadow-lg: 0 8px 32px rgba(0,0,0,.10);
        --font-body: 'DM Sans', sans-serif;
        --font-display: 'DM Serif Display', serif;
        --radius-bubble: 1.25rem;
    }

    /* ─── Layout ─── */
    .coach-shell {
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--header-h) - 3rem);
        max-width: 860px;
        margin: 0 auto;
        background: var(--coach-bg);
        border-radius: 1.5rem;
        border: 1px solid var(--coach-border);
        overflow: hidden;
        box-shadow: var(--coach-shadow-lg);
        font-family: var(--font-body);
    }

    /* ─── Header ─── */
    .coach-header {
        background: var(--coach-surface);
        border-bottom: 1px solid var(--coach-border);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-shrink: 0;
    }

    .coach-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--coach-accent) 0%, #4fa843 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        position: relative;
    }

    .coach-avatar::after {
        content: '';
        position: absolute;
        bottom: 1px;
        right: 1px;
        width: 10px;
        height: 10px;
        background: #22c55e;
        border-radius: 50%;
        border: 2px solid white;
    }

    .coach-header-info {
        flex: 1;
    }

    .coach-name {
        font-family: var(--font-display);
        font-size: 1.1rem;
        color: var(--coach-ink);
        font-style: italic;
    }

    .coach-status {
        font-size: .72rem;
        color: var(--coach-muted);
        display: flex;
        align-items: center;
        gap: .3rem;
        margin-top: .1rem;
    }

    .coach-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #22c55e;
        animation: pulse-dot 2s ease-in-out infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: .4; }
    }

    .coach-header-actions {
        display: flex;
        gap: .5rem;
    }

    .coach-action-btn {
        width: 34px;
        height: 34px;
        border-radius: .625rem;
        background: none;
        border: 1px solid var(--coach-border);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--coach-muted);
        transition: all .15s;
    }

    .coach-action-btn:hover {
        background: var(--coach-accent-light);
        color: var(--coach-accent);
        border-color: var(--coach-accent-light);
    }

    .coach-action-btn svg {
        width: 15px;
        height: 15px;
    }

    /* ─── Context bar ─── */
    .coach-context {
        background: var(--coach-accent-light);
        border-bottom: 1px solid #d5e8d1;
        padding: .6rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        overflow-x: auto;
        flex-shrink: 0;
    }

    .coach-context::-webkit-scrollbar { display: none; }

    .ctx-chip {
        display: flex;
        align-items: center;
        gap: .35rem;
        font-size: .71rem;
        font-weight: 600;
        color: var(--coach-accent);
        white-space: nowrap;
        flex-shrink: 0;
    }

    .ctx-chip svg {
        width: 12px;
        height: 12px;
        opacity: .7;
    }

    .ctx-sep {
        width: 1px;
        height: 14px;
        background: #c5ddc0;
        flex-shrink: 0;
    }

    /* ─── Messages ─── */
    .coach-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        scroll-behavior: smooth;
    }

    .coach-messages::-webkit-scrollbar { width: 4px; }
    .coach-messages::-webkit-scrollbar-track { background: transparent; }
    .coach-messages::-webkit-scrollbar-thumb { background: var(--coach-border); border-radius: 2px; }

    /* Message row */
    .msg-row {
        display: flex;
        gap: .75rem;
        animation: msg-in .3s ease;
    }

    @keyframes msg-in {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .msg-row.user { flex-direction: row-reverse; }

    .msg-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .9rem;
        margin-top: auto;
    }

    .msg-avatar.ai {
        background: linear-gradient(135deg, var(--coach-accent) 0%, #4fa843 100%);
    }

    .msg-avatar.user-av {
        background: #e2e0db;
        font-size: .7rem;
        font-weight: 700;
        color: var(--coach-muted);
        letter-spacing: .02em;
    }

    .msg-content {
        max-width: 72%;
        display: flex;
        flex-direction: column;
        gap: .25rem;
    }

    .msg-row.user .msg-content { align-items: flex-end; }

    .msg-bubble {
        padding: .875rem 1.125rem;
        border-radius: var(--radius-bubble);
        font-size: .875rem;
        line-height: 1.6;
        word-break: break-word;
    }

    .msg-row.ai .msg-bubble {
        background: var(--coach-ai-bg);
        color: var(--coach-ai-ink);
        border: 1px solid var(--coach-border);
        border-bottom-left-radius: .25rem;
        box-shadow: var(--coach-shadow);
    }

    .msg-row.user .msg-bubble {
        background: var(--coach-user-bg);
        color: var(--coach-user-ink);
        border-bottom-right-radius: .25rem;
    }

    /* Markdown-like inside bubble */
    .msg-bubble strong { font-weight: 600; }
    .msg-bubble em { font-style: italic; }
    .msg-bubble ul { padding-left: 1.1rem; margin: .4rem 0; }
    .msg-bubble li { margin: .2rem 0; }
    .msg-bubble p { margin: .3rem 0; }
    .msg-bubble p:first-child { margin-top: 0; }
    .msg-bubble p:last-child { margin-bottom: 0; }
    .msg-bubble .highlight-box {
        background: var(--coach-accent-light);
        border-left: 3px solid var(--coach-accent);
        border-radius: .25rem;
        padding: .5rem .75rem;
        margin: .5rem 0;
        font-size: .82rem;
        color: var(--coach-accent);
        font-weight: 500;
    }
    .msg-row.user .msg-bubble .highlight-box {
        background: rgba(255,255,255,.15);
        border-left-color: rgba(255,255,255,.5);
        color: white;
    }

    .msg-time {
        font-size: .63rem;
        color: var(--coach-muted);
        padding: 0 .25rem;
    }

    /* Typing indicator */
    .typing-indicator {
        display: flex;
        gap: .3rem;
        padding: .875rem 1.125rem;
        background: var(--coach-ai-bg);
        border: 1px solid var(--coach-border);
        border-radius: var(--radius-bubble);
        border-bottom-left-radius: .25rem;
        width: fit-content;
        box-shadow: var(--coach-shadow);
    }

    .typing-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--coach-muted);
        animation: typing 1.2s ease-in-out infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: .2s; }
    .typing-dot:nth-child(3) { animation-delay: .4s; }

    @keyframes typing {
        0%, 80%, 100% { transform: translateY(0); opacity: .4; }
        40% { transform: translateY(-6px); opacity: 1; }
    }

    /* Quick suggestions */
    .quick-suggestions {
        padding: .75rem 1.5rem;
        display: flex;
        gap: .5rem;
        overflow-x: auto;
        flex-shrink: 0;
        border-top: 1px solid var(--coach-border);
        background: var(--coach-surface);
    }

    .quick-suggestions::-webkit-scrollbar { display: none; }

    .quick-btn {
        background: var(--coach-bg);
        border: 1px solid var(--coach-border);
        border-radius: 99px;
        padding: .35rem .875rem;
        font-size: .75rem;
        font-weight: 500;
        color: var(--coach-muted);
        cursor: pointer;
        white-space: nowrap;
        font-family: var(--font-body);
        transition: all .15s;
        flex-shrink: 0;
    }

    .quick-btn:hover {
        background: var(--coach-accent-light);
        border-color: var(--coach-accent-mid);
        color: var(--coach-accent);
    }

    /* ─── Input ─── */
    .coach-input-zone {
        padding: 1rem 1.25rem;
        background: var(--coach-surface);
        border-top: 1px solid var(--coach-border);
        display: flex;
        gap: .75rem;
        align-items: flex-end;
        flex-shrink: 0;
    }

    .coach-textarea-wrap {
        flex: 1;
        background: var(--coach-bg);
        border: 1.5px solid var(--coach-border);
        border-radius: 1.125rem;
        display: flex;
        align-items: flex-end;
        padding: .625rem 1rem;
        transition: border-color .2s;
        gap: .5rem;
    }

    .coach-textarea-wrap:focus-within {
        border-color: var(--coach-accent-mid);
        background: white;
    }

    #coach-input {
        flex: 1;
        background: none;
        border: none;
        outline: none;
        resize: none;
        font-family: var(--font-body);
        font-size: .875rem;
        color: var(--coach-ink);
        max-height: 140px;
        min-height: 24px;
        line-height: 1.5;
        overflow-y: auto;
    }

    #coach-input::placeholder { color: #b0ada8; }

    .coach-send-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--coach-accent);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all .2s;
        flex-shrink: 0;
    }

    .coach-send-btn:hover:not(:disabled) {
        background: var(--coach-accent-mid);
        transform: scale(1.06);
    }

    .coach-send-btn:disabled {
        background: var(--coach-border);
        cursor: not-allowed;
    }

    .coach-send-btn svg { width: 17px; height: 17px; }

    /* ─── Empty state ─── */
    .coach-welcome {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 2rem;
        text-align: center;
        gap: 1rem;
    }

    .welcome-emoji {
        font-size: 3rem;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .welcome-title {
        font-family: var(--font-display);
        font-size: 1.5rem;
        color: var(--coach-ink);
        font-style: italic;
    }

    .welcome-sub {
        font-size: .875rem;
        color: var(--coach-muted);
        max-width: 420px;
        line-height: 1.6;
    }

    .welcome-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: .75rem;
        max-width: 500px;
        margin-top: .5rem;
        width: 100%;
    }

    .welcome-card {
        background: white;
        border: 1px solid var(--coach-border);
        border-radius: 1rem;
        padding: .875rem 1rem;
        text-align: left;
        cursor: pointer;
        transition: all .15s;
    }

    .welcome-card:hover {
        border-color: var(--coach-accent-mid);
        box-shadow: var(--coach-shadow);
        transform: translateY(-2px);
    }

    .welcome-card-icon { font-size: 1.25rem; margin-bottom: .35rem; }
    .welcome-card-title { font-size: .8rem; font-weight: 600; color: var(--coach-ink); }
    .welcome-card-desc { font-size: .72rem; color: var(--coach-muted); margin-top: .15rem; }

    /* ─── Clear btn ─── */
    .clear-chat-btn {
        font-size: .72rem;
        color: var(--coach-muted);
        background: none;
        border: none;
        cursor: pointer;
        font-family: var(--font-body);
        display: flex;
        align-items: center;
        gap: .3rem;
        padding: .35rem .625rem;
        border-radius: .5rem;
        transition: all .15s;
    }

    .clear-chat-btn:hover { background: var(--coach-accent-light); color: var(--coach-accent); }
    .clear-chat-btn svg { width: 13px; height: 13px; }

    /* ─── Responsive ─── */
    @media (max-width: 640px) {
        .coach-shell { height: calc(100vh - var(--header-h) - 2rem); border-radius: 1rem; }
        .msg-content { max-width: 85%; }
        .welcome-cards { grid-template-columns: 1fr; }
        .coach-context { gap: 1rem; }
    }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="bc">
    <a href="{{ route('student.dashboard') }}">Accueil</a>
    <span class="bc-sep">›</span>
    <span class="bc-cur">Coach IA</span>
</div>

{{-- Data for JS --}}
@php
    $userInitials = strtoupper(substr($apprenant->prenom, 0, 1) . substr($apprenant->nom, 0, 1));

    // Résumé scolaire pour le système prompt
    $moyenneTexte = $moyenneGenerale ? number_format($moyenneGenerale, 2) . '/20' : 'non disponible';
    $rangTexte = ($rang['rang'] !== '—') ? $rang['rang'] . 'e sur ' . $rang['total'] . ' élèves' : 'non calculé';

    // Top matières
    $topMatieres = collect($moyennesParMatiere)
        ->filter(fn($m) => $m['avg'] !== null)
        ->sortByDesc('avg')
        ->take(3)
        ->map(fn($m) => $m['subject']->name . ' (' . number_format($m['avg'], 1) . '/20)')
        ->join(', ');

    // Matières difficiles
    $weakMatieres = collect($moyennesParMatiere)
        ->filter(fn($m) => $m['avg'] !== null && $m['avg'] < 10)
        ->sortBy('avg')
        ->take(3)
        ->map(fn($m) => $m['subject']->name . ' (' . number_format($m['avg'], 1) . '/20)')
        ->join(', ');

    $systemPrompt = "Tu es EduCoach, un assistant pédagogique intelligent, bienveillant et motivant intégré dans la plateforme eSchool. Tu es le coach personnel de l'élève. Tu dois rester STRICTEMENT dans le cadre éducatif : apprentissage, méthodes d'étude, révisions, organisation, motivation, résultats scolaires, orientation, conseils de vie scolaire.

PROFIL DE L'ÉLÈVE :
- Nom complet : {$apprenant->prenom} {$apprenant->nom}
- Matricule : {$apprenant->matricule}
- Classe : " . ($apprenant->classe->name ?? 'Non assigné') . "
- Niveau : " . ($apprenant->niveau->name ?? 'N/A') . "
- Filière : " . ($apprenant->filiere->name ?? 'N/A') . "
- Institution : " . ($institution->name ?? 'N/A') . "
- Année académique : {$apprenant->annee_academique}

DONNÉES SCOLAIRES :
- Moyenne générale : {$moyenneTexte}
- Rang dans la classe : {$rangTexte}
- Points forts (meilleures matières) : " . ($topMatieres ?: 'aucune donnée') . "
- Points à améliorer (matières sous 10/20) : " . ($weakMatieres ?: 'aucun, excellent !') . "
- Nombre d'évaluations passées : {$stats['evals']}
- Nombre de matières : {$stats['matieres']}
- Bulletins disponibles : {$stats['bulletins']}

RÈGLES DE COMPORTEMENT :
1. Appelle l'élève par son prénom ({$apprenant->prenom}) pour personnaliser les échanges.
2. Sois positif, encourageant, mais honnête sur les points faibles.
3. Donne des conseils concrets et actionnables.
4. Si l'élève parle de sujets hors cadre scolaire (jeux vidéo, politique, violence, etc.), redirige-le poliment mais fermement vers les sujets éducatifs.
5. Utilise des émojis avec modération pour rendre les échanges vivants.
6. Réponds en français.
7. Propose régulièrement des exercices, des rappels ou des conseils de révision.
8. Si l'élève semble démotivé, utilise des techniques de motivation (objectifs SMART, témoignages, visualisation).
9. Tu peux analyser les résultats de l'élève et donner des alertes si nécessaire.
10. Garde tes réponses concises et lisibles (max 3-4 paragraphes sauf si une explication longue est nécessaire).";

    $welcomeMessage = "Bonjour **{$apprenant->prenom}** ! 👋 Je suis **EduCoach**, ton assistant pédagogique personnel.

Je connais ton profil : tu es en **" . ($apprenant->classe->name ?? 'ta classe') . "** avec une moyenne de **{$moyenneTexte}**" . ($rang['rang'] !== '—' ? " et tu es **{$rangTexte}**" : "") . ".

Je suis là pour t'aider à progresser, organiser tes révisions, te motiver et répondre à toutes tes questions scolaires. Comment puis-je t'aider aujourd'hui ?";
@endphp

<div id="coach-system-prompt" style="display:none;">{{ $systemPrompt }}</div>
<div id="apprenant-prenom" style="display:none;">{{ $apprenant->prenom }}</div>
<div id="user-initials" style="display:none;">{{ $userInitials }}</div>
<div id="welcome-message" style="display:none;">{{ $welcomeMessage }}</div>

{{-- ══ MAIN SHELL ══ --}}
<div class="coach-shell">

    {{-- Header --}}
    <div class="coach-header">
        <div class="coach-avatar">🎓</div>
        <div class="coach-header-info">
            <div class="coach-name">EduCoach</div>
            <div class="coach-status">
                <span class="coach-status-dot"></span>
                Votre coach personnel · toujours disponible
            </div>
        </div>
        <div class="coach-header-actions">
            <button class="clear-chat-btn" onclick="clearChat()" title="Nouvelle conversation">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Nouvelle discussion
            </button>
        </div>
    </div>

    {{-- Context bar --}}
    <div class="coach-context">
        <div class="ctx-chip">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            {{ $apprenant->prenom }} {{ $apprenant->nom }}
        </div>
        <div class="ctx-sep"></div>
        <div class="ctx-chip">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
            </svg>
            {{ $apprenant->classe->name ?? 'Classe N/A' }}
        </div>
        <div class="ctx-sep"></div>
        <div class="ctx-chip">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/>
            </svg>
            Moy. {{ $moyenneGenerale ? number_format($moyenneGenerale, 2) : 'N/A' }}/20
        </div>
        @if($rang['rang'] !== '—')
        <div class="ctx-sep"></div>
        <div class="ctx-chip">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438"/>
            </svg>
            Rang {{ $rang['rang'] }}/{{ $rang['total'] }}
        </div>
        @endif
    </div>

    {{-- Messages --}}
    <div class="coach-messages" id="coach-messages">
        {{-- Welcome state shown when chat empty --}}
        <div class="coach-welcome" id="welcome-state">
            <div class="welcome-emoji">🎓</div>
            <div class="welcome-title">Bonjour, {{ $apprenant->prenom }} !</div>
            <div class="welcome-sub">
                Je suis EduCoach, ton assistant pédagogique. Je connais ton parcours et je suis là pour t'aider à atteindre tes objectifs scolaires.
            </div>
            <div class="welcome-cards">
                <div class="welcome-card" onclick="sendQuick('Analyse mes résultats et donne-moi des conseils')">
                    <div class="welcome-card-icon">📊</div>
                    <div class="welcome-card-title">Analyser mes résultats</div>
                    <div class="welcome-card-desc">Comprendre mes forces et faiblesses</div>
                </div>
                <div class="welcome-card" onclick="sendQuick('Comment réviser efficacement pour mes prochains examens ?')">
                    <div class="welcome-card-icon">📚</div>
                    <div class="welcome-card-title">Méthodes de révision</div>
                    <div class="welcome-card-desc">Stratégies pour bien préparer</div>
                </div>
                <div class="welcome-card" onclick="sendQuick('Crée-moi un planning de révision hebdomadaire')">
                    <div class="welcome-card-icon">🗓️</div>
                    <div class="welcome-card-title">Planning de révision</div>
                    <div class="welcome-card-desc">Organiser mon temps d'étude</div>
                </div>
                <div class="welcome-card" onclick="sendQuick('J\'ai du mal à me motiver, aide-moi')">
                    <div class="welcome-card-icon">💪</div>
                    <div class="welcome-card-title">Boost de motivation</div>
                    <div class="welcome-card-desc">Retrouver l'élan pour travailler</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick suggestions --}}
    <div class="quick-suggestions" id="quick-suggestions" style="display:none;">
        <button class="quick-btn" onclick="sendQuick('Quelles sont mes matières à améliorer ?')">📉 Mes points faibles</button>
        <button class="quick-btn" onclick="sendQuick('Donne-moi une technique de mémorisation')">🧠 Mémorisation</button>
        <button class="quick-btn" onclick="sendQuick('Comment gérer le stress avant un examen ?')">😰 Gérer le stress</button>
        <button class="quick-btn" onclick="sendQuick('Explique-moi la méthode Pomodoro')">⏱️ Pomodoro</button>
        <button class="quick-btn" onclick="sendQuick('Conseille-moi pour mon orientation scolaire')">🎯 Orientation</button>
    </div>

    {{-- Input zone --}}
    <div class="coach-input-zone">
        <div class="coach-textarea-wrap">
            <textarea
                id="coach-input"
                placeholder="Pose ta question à EduCoach…"
                rows="1"
                onkeydown="handleKey(event)"
                oninput="autoResize(this)"
            ></textarea>
        </div>
        <button class="coach-send-btn" id="send-btn" onclick="sendMessage()" disabled>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
        </button>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // ── State ──────────────────────────────────────────
    const SYSTEM_PROMPT  = document.getElementById('coach-system-prompt').textContent.trim();
    const USER_INITIALS  = document.getElementById('user-initials').textContent.trim();
    const PRENOM         = document.getElementById('apprenant-prenom').textContent.trim();
    const WELCOME_MSG    = document.getElementById('welcome-message').textContent.trim();

    let history        = [];   // [{role, content}]
    let isStreaming    = false;

    // ── DOM refs ──────────────────────────────────────
    const messagesEl   = document.getElementById('coach-messages');
    const inputEl      = document.getElementById('coach-input');
    const sendBtn      = document.getElementById('send-btn');
    const welcomeState = document.getElementById('welcome-state');
    const quickSugg    = document.getElementById('quick-suggestions');

    // ── Input enable/disable ──────────────────────────
    inputEl.addEventListener('input', () => {
        sendBtn.disabled = inputEl.value.trim().length === 0 || isStreaming;
    });

    // ── Auto-resize textarea ──────────────────────────
    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 140) + 'px';
    }

    // ── Enter key ─────────────────────────────────────
    function handleKey(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) sendMessage();
        }
    }

    // ── Quick send ────────────────────────────────────
    function sendQuick(text) {
        inputEl.value = text;
        autoResize(inputEl);
        sendMessage();
    }

    // ── Clear chat ────────────────────────────────────
    function clearChat() {
        history = [];
        // Remove all message rows
        messagesEl.querySelectorAll('.msg-row, .msg-row-typing').forEach(el => el.remove());
        welcomeState.style.display = 'flex';
        quickSugg.style.display    = 'none';
        inputEl.value              = '';
        inputEl.style.height       = 'auto';
        sendBtn.disabled           = true;
    }

    // ── Send message ──────────────────────────────────
    async function sendMessage() {
        const text = inputEl.value.trim();
        if (!text || isStreaming) return;

        // Hide welcome, show suggestions
        welcomeState.style.display = 'none';
        quickSugg.style.display    = 'flex';

        // Append user message
        appendMessage('user', text);
        history.push({ role: 'user', content: text });

        // Reset input
        inputEl.value    = '';
        inputEl.style.height = 'auto';
        sendBtn.disabled = true;
        isStreaming      = true;

        // Show typing indicator
        const typingRow = appendTyping();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            const response = await fetch('/student/ai-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    system:   SYSTEM_PROMPT,
                    messages: history,
                })
            });

            // Remove typing bubble before streaming
            typingRow.remove();

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                appendMessage('ai', `⚠️ ${errData.error ?? "Erreur serveur. Assure-toi qu'Ollama est lancé."}`);
                isStreaming = false;
                sendBtn.disabled = inputEl.value.trim().length === 0;
                return;
            }

            // ── Streaming SSE reader ──────────────────────────
            const reader  = response.body.getReader();
            const decoder = new TextDecoder();
            let aiText    = '';

            // Create streaming bubble immediately
            const streamRow    = appendMessage('ai', '▌');
            const streamBubble = streamRow.querySelector('.msg-bubble');

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value, { stream: true });

                for (const line of chunk.split('\n')) {
                    const trimmed = line.trim();
                    if (!trimmed || trimmed === 'data: [DONE]') continue;
                    if (!trimmed.startsWith('data:')) continue;
                    try {
                        const json  = JSON.parse(trimmed.slice(5).trim());
                        // Ollama stream format: json.message.content
                        const token = json.message?.content ?? json.response ?? '';
                        aiText += token;
                        streamBubble.innerHTML = markdownLite(aiText) + '<span style="opacity:.5">▌</span>';
                        scrollBottom();
                    } catch (_) { /* skip malformed chunks */ }
                }
            }

            // Finalize — remove cursor, save to history
            streamBubble.innerHTML = markdownLite(aiText);
            if (aiText) history.push({ role: 'assistant', content: aiText });

        } catch (err) {
            typingRow.remove();
            appendMessage('ai', "⚠️ Impossible de joindre Ollama. Lance d'abord : <strong>ollama run mistral</strong>");
            console.error('EduCoach/Ollama error:', err);
        }

        isStreaming      = false;
        sendBtn.disabled = inputEl.value.trim().length === 0;
        scrollBottom();
    }

    // ── Append user / AI message ──────────────────────
    function appendMessage(role, text) {
        const row = document.createElement('div');
        row.className = `msg-row ${role}`;

        // Avatar
        const av = document.createElement('div');
        av.className = role === 'ai' ? 'msg-avatar ai' : 'msg-avatar user-av';
        av.textContent = role === 'ai' ? '🎓' : USER_INITIALS;

        // Content
        const content = document.createElement('div');
        content.className = 'msg-content';

        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble';
        bubble.innerHTML = markdownLite(text);

        const time = document.createElement('div');
        time.className = 'msg-time';
        time.textContent = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

        content.appendChild(bubble);
        content.appendChild(time);
        row.appendChild(av);
        row.appendChild(content);

        messagesEl.appendChild(row);
        scrollBottom();
        return row;
    }

    // ── Typing indicator ──────────────────────────────
    function appendTyping() {
        const row = document.createElement('div');
        row.className = 'msg-row ai msg-row-typing';

        const av = document.createElement('div');
        av.className = 'msg-avatar ai';
        av.textContent = '🎓';

        const content = document.createElement('div');
        content.className = 'msg-content';

        const ind = document.createElement('div');
        ind.className = 'typing-indicator';
        ind.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';

        content.appendChild(ind);
        row.appendChild(av);
        row.appendChild(content);
        messagesEl.appendChild(row);
        scrollBottom();
        return row;
    }

    // ── Scroll to bottom ──────────────────────────────
    function scrollBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // ── Lightweight markdown parser ───────────────────
    function markdownLite(text) {
        return text
            // Bold
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            // Italic
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            // Highlight box (> text)
            .replace(/^> (.+)$/gm, '<div class="highlight-box">$1</div>')
            // Bullet list
            .replace(/^[-•] (.+)$/gm, '<li>$1</li>')
            .replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>')
            // Line breaks to paragraphs
            .split(/\n{2,}/)
            .map(para => para.trim())
            .filter(p => p)
            .map(p => p.startsWith('<ul>') || p.startsWith('<div class') ? p : `<p>${p.replace(/\n/g, '<br>')}</p>`)
            .join('');
    }

    // ── Init : send welcome message automatically ─────
    window.addEventListener('DOMContentLoaded', () => {
        // Pré-peupler l'historique avec le message de bienvenue
        setTimeout(() => {
            appendMessage('ai', WELCOME_MSG);
            history.push({ role: 'assistant', content: WELCOME_MSG });
            welcomeState.style.display = 'none';
            quickSugg.style.display    = 'flex';
        }, 400);
    });
</script>
@endpush