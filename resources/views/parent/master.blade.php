<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Parents') — EduParent</title>
    <link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --ink: #0c0f1a;
            --ink-70: #4a5068;
            --ink-40: #9ea8c0;
            --ink-10: #f0f2f7;
            --white: #ffffff;
            --gold: #d4a017;
            --gold-l: #fdf4dc;
            --teal: #0d8a6f;
            --teal-l: #d0f4ec;
            --red: #c9313b;
            --red-l: #fde8e9;
            --blue: #2563c0;
            --blue-l: #dceafd;
            --border: #e4e8f2;
            --bg: #f7f9fc;
            --sidebar-w: 248px;
            --header-h: 60px;
        }

        *,
        *::before,
        *::after {
            font-family: 'Outfit', sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg);
            color: var(--ink);
            overflow-x: hidden;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* OVERLAY */
        .p-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            z-index: 39;
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            opacity: 0;
            transition: opacity .3s ease;
        }

        .p-overlay.visible {
            opacity: 1;
        }

        /* SIDEBAR */
        .p-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-w);
            background: var(--ink);
            z-index: 40;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform .3s cubic-bezier(.4, 0, .2, 1);
            will-change: transform;
        }

        .p-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .p-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .1);
            border-radius: 4px;
        }

        /* Logo */
        .p-logo {
            padding: 1.25rem 1.25rem 1rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            flex-shrink: 0;
        }

        .p-logo-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--gold) 0%, #f5c842 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 800;
            color: var(--ink);
            flex-shrink: 0;
        }

        .p-logo-text {
            color: #fff;
            font-size: .9rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .p-logo-sub {
            color: rgba(255, 255, 255, .4);
            font-size: .65rem;
            font-weight: 500;
        }

        .p-sidebar-close {
            display: none;
            margin-left: auto;
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .6);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .18s, color .18s;
        }

        .p-sidebar-close:hover {
            background: rgba(255, 255, 255, .14);
            color: #fff;
        }

        .p-sidebar-close svg {
            width: 14px;
            height: 14px;
        }

        /* Children */
        .p-children {
            padding: .75rem;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            flex-shrink: 0;
        }

        .p-children-label {
            font-size: .6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(255, 255, 255, .28);
            margin-bottom: .5rem;
            padding: 0 .25rem;
        }

        .p-child-item {
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: .5rem .625rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background .18s;
            text-decoration: none;
        }

        .p-child-item:hover {
            background: rgba(255, 255, 255, .07);
        }

        .p-child-avatar {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .p-child-name {
            font-size: .78rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .8);
        }

        .p-child-class {
            font-size: .65rem;
            color: rgba(255, 255, 255, .4);
        }

        /* Nav */
        .p-nav {
            padding: 1rem .75rem;
            flex: 1;
        }

        .p-nav-section {
            margin-bottom: 1.5rem;
        }

        .p-nav-label {
            font-size: .6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(255, 255, 255, .28);
            padding: 0 .5rem;
            margin-bottom: .5rem;
        }

        .p-nav-item {
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: .6rem .75rem;
            border-radius: 8px;
            color: rgba(255, 255, 255, .55);
            font-size: .825rem;
            font-weight: 500;
            text-decoration: none;
            transition: all .18s;
            margin-bottom: 2px;
            cursor: pointer;
            border-left: 2px solid transparent;
        }

        .p-nav-item:hover {
            background: rgba(255, 255, 255, .07);
            color: #fff;
        }

        .p-nav-item.active {
            background: linear-gradient(90deg, rgba(212, 160, 23, .22) 0%, rgba(212, 160, 23, .08) 100%);
            color: var(--gold);
            border-left-color: var(--gold);
        }

        .p-nav-item svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .p-nav-badge {
            margin-left: auto;
            background: var(--red);
            color: #fff;
            font-size: .6rem;
            font-weight: 700;
            padding: .1rem .4rem;
            border-radius: 99px;
        }

        /* Footer */
        .p-footer {
            padding: .875rem .75rem;
            border-top: 1px solid rgba(255, 255, 255, .07);
            flex-shrink: 0;
        }

        .p-footer-user {
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: .5rem .625rem;
            border-radius: 8px;
        }

        .p-footer-avatar {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            font-size: .85rem;
            flex-shrink: 0;
        }

        .p-footer-name {
            font-size: .78rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .8);
        }

        .p-footer-role {
            font-size: .62rem;
            color: rgba(255, 255, 255, .35);
        }

        /* HEADER */
        .p-header {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-w);
            height: var(--header-h);
            background: var(--white);
            border-bottom: 1px solid var(--border);
            z-index: 30;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            justify-content: space-between;
            gap: .75rem;
            transition: left .3s cubic-bezier(.4, 0, .2, 1);
        }

        .p-header-left {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
        }

        .p-header-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .p-header-sub {
            font-size: .72rem;
            color: var(--ink-40);
            margin-top: .1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .p-header-right {
            display: flex;
            align-items: center;
            gap: .625rem;
            flex-shrink: 0;
        }

        .p-menu-btn {
            display: none;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--white);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s;
            flex-shrink: 0;
        }

        .p-menu-btn:hover {
            background: var(--bg);
        }

        .p-menu-btn svg {
            width: 18px;
            height: 18px;
            color: var(--ink-70);
        }

        .p-notif-btn {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: background .15s;
            flex-shrink: 0;
        }

        .p-notif-btn:hover {
            background: var(--bg);
        }

        .p-notif-btn svg {
            width: 17px;
            height: 17px;
            color: var(--ink-70);
        }

        .p-notif-dot {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--red);
            border: 1.5px solid var(--white);
        }

        .p-time-chip {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .3rem .75rem;
            font-size: .72rem;
            font-weight: 600;
            color: var(--ink-70);
            white-space: nowrap;
        }

        /* MAIN */
        .p-main {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
            transition: margin-left .3s cubic-bezier(.4, 0, .2, 1);
        }

        .p-content {
            padding: 1.5rem;
        }

        /* FLASH */
        .p-flash {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
            padding: .75rem 1.25rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: .625rem;
            font-size: .83rem;
            font-weight: 500;
            max-width: min(380px, calc(100vw - 2rem));
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
            animation: slideIn .3s ease, fadeOut .35s 4.5s ease forwards;
        }

        .p-flash.success {
            background: var(--teal-l);
            color: var(--teal);
            border: 1px solid #a7e8d8;
        }

        .p-flash.error {
            background: var(--red-l);
            color: var(--red);
            border: 1px solid #f9c0c3;
        }

        @keyframes slideIn {
            from {
                transform: translateX(20px);
                opacity: 0;
            }

            to {
                transform: none;
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                pointer-events: none;
            }
        }

        /* SHARED */
        .p-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
        }

        .p-card-header {
            padding: .9rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafbfd;
        }

        .p-card-header h3 {
            font-size: .875rem;
            font-weight: 700;
            color: var(--ink);
        }

        .p-card-body {
            padding: 1.25rem;
        }

        .p-badge {
            display: inline-flex;
            align-items: center;
            padding: .18rem .6rem;
            border-radius: 20px;
            font-size: .68rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .p-badge-green {
            background: var(--teal-l);
            color: var(--teal);
        }

        .p-badge-red {
            background: var(--red-l);
            color: var(--red);
        }

        .p-badge-amber {
            background: var(--gold-l);
            color: var(--gold);
        }

        .p-badge-blue {
            background: var(--blue-l);
            color: var(--blue);
        }

        .p-badge-gray {
            background: var(--ink-10);
            color: var(--ink-70);
        }

        .p-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem 1rem;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .18s;
            text-decoration: none;
        }

        .p-btn-primary {
            background: var(--ink);
            color: #fff;
        }

        .p-btn-primary:hover {
            background: #1e2435;
        }

        .p-btn-outline {
            background: var(--white);
            color: var(--ink-70);
            border: 1px solid var(--border);
        }

        .p-btn-outline:hover {
            background: var(--bg);
            border-color: #c8cfe0;
        }

        .p-btn-gold {
            background: var(--gold);
            color: var(--ink);
        }

        .p-btn-gold:hover {
            background: #c09014;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            :root {
                --sidebar-w: 220px;
            }
        }

        @media (max-width: 900px) {
            :root {
                --sidebar-w: 64px;
            }

            .p-logo-text,
            .p-logo-sub,
            .p-children-label,
            .p-child-name,
            .p-child-class,
            .p-nav-label,
            .p-nav-item span,
            .p-nav-badge,
            .p-footer-name,
            .p-footer-role {
                display: none !important;
            }

            .p-logo {
                padding: .875rem .75rem;
                justify-content: center;
            }

            .p-logo-mark {
                margin: 0;
            }

            .p-children {
                padding: .5rem;
            }

            .p-child-item {
                justify-content: center;
                padding: .45rem .5rem;
            }

            .p-child-avatar {
                width: 28px;
                height: 28px;
            }

            .p-nav {
                padding: .75rem .5rem;
            }

            .p-nav-item {
                justify-content: center;
                padding: .65rem .5rem;
                border-left: none;
                border-radius: 8px;
            }

            .p-nav-item.active {
                border-left: none;
            }

            .p-nav-item svg {
                width: 18px;
                height: 18px;
            }

            .p-footer {
                padding: .75rem .5rem;
            }

            .p-footer-user {
                justify-content: center;
                padding: .5rem;
            }

            .p-footer-avatar {
                width: 30px;
                height: 30px;
            }

            .p-footer form button {
                padding: .45rem .5rem;
                font-size: 0;
            }

            .p-footer form button::before {
                content: '↪';
                font-size: .85rem;
            }

            .p-header-sub {
                display: none;
            }
        }

        @media (max-width: 640px) {
            :root {
                --sidebar-w: 248px;
            }

            .p-logo-text,
            .p-logo-sub,
            .p-children-label,
            .p-child-name,
            .p-child-class,
            .p-nav-label,
            .p-nav-item span,
            .p-nav-badge,
            .p-footer-name,
            .p-footer-role {
                display: unset !important;
            }

            .p-nav-item {
                justify-content: flex-start;
            }

            .p-child-item {
                justify-content: flex-start;
            }

            .p-logo {
                justify-content: flex-start;
            }

            .p-footer-user {
                justify-content: flex-start;
            }

            .p-nav-item.active {
                border-left: 2px solid var(--gold);
            }

            .p-footer form button {
                font-size: .75rem;
            }

            .p-footer form button::before {
                content: '';
            }

            .p-sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .p-sidebar.open {
                transform: translateX(0);
                box-shadow: 4px 0 30px rgba(0, 0, 0, .25);
            }

            .p-sidebar-close {
                display: flex;
            }

            .p-overlay {
                display: block;
            }

            .p-header {
                left: 0;
            }

            .p-main {
                margin-left: 0;
            }

            .p-menu-btn {
                display: flex;
            }

            .p-time-chip {
                display: none;
            }

            .p-content {
                padding: 1rem;
            }
        }

        @media (max-width: 380px) {
            .p-header {
                padding: 0 .875rem;
            }

            .p-header-title {
                font-size: .875rem;
            }

            .p-content {
                padding: .75rem;
            }

            .p-flash {
                top: .75rem;
                right: .75rem;
                left: .75rem;
            }
        }

        @media (min-width: 641px) and (max-width: 900px) {
            .p-nav-item {
                position: relative;
            }

            .p-nav-item[data-label]:hover::after {
                content: attr(data-label);
                position: absolute;
                left: calc(100% + 10px);
                top: 50%;
                transform: translateY(-50%);
                background: var(--ink);
                color: #fff;
                font-size: .72rem;
                font-weight: 600;
                padding: .3rem .65rem;
                border-radius: 6px;
                white-space: nowrap;
                pointer-events: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, .2);
                z-index: 100;
            }

            .p-nav-item[data-label]:hover::before {
                content: '';
                position: absolute;
                left: calc(100% + 6px);
                top: 50%;
                transform: translateY(-50%);
                border: 4px solid transparent;
                border-right-color: var(--ink);
                pointer-events: none;
                z-index: 100;
            }
        }

        /* Fix affichage sidebar mobile */
        @media (max-width: 640px) {
            .sidebar {
                z-index: 50;
                /* plus haut que header */
            }

            .a-overlay {
                z-index: 45;
            }
        }

        .sidebar.open {
            transform: translateX(0) !important;
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- FLASH --}}
    @if (session('success'))
        <div class="p-flash success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-flash error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="p-overlay" id="p-overlay" onclick="closeSidebar()"></div>

    {{-- ── DONNÉES PHP — définies UNE SEULE FOIS, tout en haut ── --}}
    @php
        $parentUser = Auth::user();
        $schoolParent = \App\Models\SchoolParent::where('user_id', $parentUser?->id)
            ->with('apprenants.classe:id,name')
            ->first();
        $enfants = $schoolParent?->apprenants ?? collect();
        $initiales = $parentUser
            ? strtoupper(mb_substr($parentUser->name, 0, 1)) .
                strtoupper(mb_substr(explode(' ', $parentUser->name)[1] ?? 'P', 0, 1))
            : 'PA';

        // Résolution du paramètre de route — défini ici pour tout le layout
        $routeParam = request()->route('apprenant');
        $routeApprenantId = is_object($routeParam) ? $routeParam->id : (int) $routeParam;

        // Premier enfant (fallback pour les liens globaux)
        $premierEnfant = $enfants->first();
    @endphp

    {{-- ── SIDEBAR ── --}}
    <aside class="p-sidebar" id="p-sidebar">

        {{-- Logo --}}
        <div class="p-logo">
            <div class="p-logo-mark">
                @if (isset($institution) && $institution?->logo)
                    <img src="{{ asset('storage/' . $institution->logo) }}" alt=""
                        style="width:36px;height:36px;object-fit:cover;border-radius:8px;">
                @else
                    <svg style="width:18px;height:18px;" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                @endif
            </div>
            <div style="min-width:0;flex:1;">
                <div class="p-logo-text">{{ Str::limit($institution->name ?? 'Mon École', 18) }}</div>
                <div class="p-logo-sub">Espace famille</div>
            </div>
            <button class="p-sidebar-close" onclick="closeSidebar()" aria-label="Fermer">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mes enfants (avatars) --}}
        @if ($enfants->count())
            <div class="p-children">
                <div class="p-children-label">Mes enfants</div>
                @foreach ($enfants as $enf)
                    @php
                        $init2 = strtoupper(mb_substr($enf->prenom, 0, 1) . mb_substr($enf->nom, 0, 1));
                        $colors = ['#6366f1', '#0891b2', '#059669', '#d97706', '#dc2626'];
                        $color2 = $colors[$enf->id % count($colors)];
                    @endphp
                    <a href="{{ route('parent.dashboard') }}" class="p-child-item"
                        title="{{ $enf->prenom }} {{ $enf->nom }}">
                        <div class="p-child-avatar" style="background:{{ $color2 }}">{{ $init2 }}</div>
                        <div style="min-width:0">
                            <div class="p-child-name">{{ $enf->prenom }} {{ $enf->nom }}</div>
                            <div class="p-child-class">{{ $enf->classe?->name ?? 'Sans classe' }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Navigation --}}
        <nav class="p-nav">

            {{-- ── PRINCIPAL ── --}}
            <div class="p-nav-section">
                <div class="p-nav-label">Principal</div>

                <a href="{{ route('parent.dashboard') }}"
                    class="p-nav-item {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}"
                    data-label="Tableau de bord">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Tableau de bord</span>
                </a>

                <a href="{{ route('parent.planning') }}"
                    class="p-nav-item {{ request()->routeIs('parent.planning') ? 'active' : '' }}"
                    data-label="Planning">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Planning</span>
                </a>
            </div>

            {{-- ── SUIVI PAR ENFANT — $routeApprenantId est déjà défini ── --}}
            @if ($enfants->count())
                <div class="p-nav-section">
                    <div class="p-nav-label">Suivi par enfant</div>

                    @foreach ($enfants as $enf)
                        <a href="{{ route('parent.enfant.notes', $enf->id) }}"
                            class="p-nav-item {{ request()->routeIs('parent.enfant.notes') && $routeApprenantId === (int) $enf->id ? 'active' : '' }}"
                            data-label="Notes — {{ $enf->prenom }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Notes — {{ $enf->prenom }}</span>
                        </a>

                        <a href="{{ route('parent.enfant.finances', $enf->id) }}"
                            class="p-nav-item {{ request()->routeIs('parent.enfant.finances') && $routeApprenantId === (int) $enf->id ? 'active' : '' }}"
                            data-label="Finances — {{ $enf->prenom }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1" />
                            </svg>
                            <span>Finances — {{ $enf->prenom }}</span>
                        </a>

                        <a href="{{ route('parent.disciplinaire', $enf->id) }}"
                            class="p-nav-item {{ request()->routeIs('parent.disciplinaire') && $routeApprenantId === (int) $enf->id ? 'active' : '' }}"
                            data-label="Discipline — {{ $enf->prenom }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Discipline — {{ $enf->prenom }}</span>
                        </a>

                        <a href="{{ route('parent.enfant.bulletins', $enf->id) }}"
                            class="p-nav-item {{ request()->routeIs('parent.enfant.bulletins') && $routeApprenantId === (int) $enf->id ? 'active' : '' }}"
                            data-label="Bulletins — {{ $enf->prenom }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Bulletins — {{ $enf->prenom }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

        </nav>

        {{-- Footer --}}
        <div class="p-footer">
            <div class="p-footer-user">
                <div class="p-footer-avatar">{{ $initiales }}</div>
                <div style="min-width:0;flex:1;">
                    <div class="p-footer-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $parentUser?->name }}
                    </div>
                    <div class="p-footer-role">Parent d'élève</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:.5rem">
                @csrf
                <button type="submit"
                    style="width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
                       color:rgba(255,255,255,.5);border-radius:7px;padding:.45rem;
                       font-size:.75rem;font-weight:600;cursor:pointer;transition:all .18s;
                       display:flex;align-items:center;justify-content:center;gap:.4rem"
                    onmouseover="this.style.background='rgba(255,255,255,.1)';this.style.color='#fff'"
                    onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color='rgba(255,255,255,.5)'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
        
    </aside>

    {{-- HEADER --}}
    <header class="p-header">
        <div class="p-header-left" style="min-width:0;flex:1">
            <button class="p-menu-btn" onclick="openSidebar()" aria-label="Ouvrir le menu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div style="min-width:0">
                <div class="p-header-title">@yield('page-title', 'Tableau de bord')</div>
                <div class="p-header-sub">@yield('page-sub', 'Suivez les activités scolaires de vos enfants')</div>
            </div>
        </div>
        <div class="p-header-right">
            <div class="p-time-chip">{{ now()->locale('fr')->isoFormat('ddd D MMM YYYY') }}</div>
            <button class="p-notif-btn" title="Notifications">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="p-notif-dot"></span>
            </button>
        </div>
        <div class="p-header-right" style="gap:.75rem;">
            <a href="{{route('parent.profil')}} " class="p-btn p-btn-outline">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profil</span>
                </a>
        </div>
    </header>

    {{-- MAIN --}}
    <main class="p-main">
        <div class="p-content">
            @yield('content')
        </div>
    </main>

    <script>
        setTimeout(() => {
            document.querySelectorAll('.p-flash').forEach(el => el.remove());
        }, 5000);

        const sidebar = document.getElementById('p-sidebar');
        const overlay = document.getElementById('p-overlay');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('visible');
            overlay.style.display = 'block'; // 🔥 important
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('visible');

            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300); // sync avec transition

            document.body.style.overflow = '';
        }

        sidebar.querySelectorAll('.p-nav-item, .p-child-item').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 640) closeSidebar();
            });
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeSidebar();
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 640) {
                overlay.classList.remove('visible');
                document.body.style.overflow = '';
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
