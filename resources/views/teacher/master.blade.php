<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Enseignant')</title>
<link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 256px;
            --header-h: 56px;
        }

        *,
        *::before,
        *::after {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* ─────────────────────────────────────────
           OVERLAY
        ───────────────────────────────────────── */
        .t-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            z-index: 39;
            opacity: 0;
            transition: opacity .3s ease;
        }

        .t-overlay.visible {
            opacity: 1;
        }

        /* ─────────────────────────────────────────
           SIDEBAR
        ───────────────────────────────────────── */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-w);
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            z-index: 40;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform .3s cubic-bezier(.4, 0, .2, 1),
                width .3s cubic-bezier(.4, 0, .2, 1);
            will-change: transform;
        }

        /* Scrollable nav area */
        .sidebar-inner {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-inner::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-inner::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-inner::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 2px;
        }

        /* ─── Logo ─── */
        .sidebar-logo {
            height: var(--header-h);
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 0 1rem;
            border-bottom: 1px solid #f3f4f6;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-logo-mark {
            width: 32px;
            height: 32px;
            background: #1f2937;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-logo-text {
            font-size: .875rem;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
        }

        .sidebar-close {
            display: none;
            margin-left: auto;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #f3f4f6;
            border: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .18s;
            color: #6b7280;
        }

        .sidebar-close:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .sidebar-close svg {
            width: 14px;
            height: 14px;
        }

        /* ─── Nav ─── */
        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-section {
            padding: 0 .75rem;
            margin-bottom: 1.25rem;
        }

        .nav-label {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #9ca3af;
            padding: 0 .625rem;
            margin-bottom: .375rem;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: .575rem .75rem;
            margin-bottom: 2px;
            border-radius: .5rem;
            color: #6b7280;
            font-size: .8125rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all .2s ease;
            border-left: 2px solid transparent;
            white-space: nowrap;
            overflow: hidden;
            gap: .625rem;
        }

        .sidebar-item:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .sidebar-item.active {
            background: #1f2937;
            color: #ffffff;
            border-left-color: transparent;
        }

        .sidebar-item svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke-width: 1.75;
        }

        .sidebar-item-text {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Active dot */
        .sidebar-item.active::after {
            content: '';
            display: block;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .4);
            flex-shrink: 0;
            margin-left: auto;
        }

        /* ─── Footer teacher card ─── */
        .sidebar-footer {
            border-top: 1px solid #f3f4f6;
            background: white;
            padding: .75rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-footer-inner {
            display: flex;
            align-items: center;
            gap: .625rem;
        }

        .sidebar-footer img {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .sidebar-footer-name {
            font-size: .75rem;
            font-weight: 600;
            color: #111827;
        }

        .sidebar-footer-sub {
            font-size: .7rem;
            color: #9ca3af;
        }

        .sidebar-footer-info {
            min-width: 0;
            flex: 1;
        }

        .sidebar-footer-cog {
            margin-left: auto;
            flex-shrink: 0;
            color: #9ca3af;
            transition: color .15s;
        }

        .sidebar-footer-cog:hover {
            color: #374151;
        }

        .sidebar-footer-cog svg {
            width: 16px;
            height: 16px;
        }

        /* ─────────────────────────────────────────
           HEADER
        ───────────────────────────────────────── */
        .header {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-w);
            height: var(--header-h);
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            z-index: 30;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            gap: .75rem;
            transition: left .3s cubic-bezier(.4, 0, .2, 1);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: .625rem;
            flex: 1;
            min-width: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: .375rem;
            flex-shrink: 0;
        }

        .menu-btn {
            display: none;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: white;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .15s;
            color: #6b7280;
        }

        .menu-btn:hover {
            background: #f9fafb;
        }

        .menu-btn svg {
            width: 18px;
            height: 18px;
        }

        .page-title {
            font-size: .875rem;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .icon-btn {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .15s;
            color: #6b7280;
        }

        .icon-btn:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .icon-btn svg {
            width: 18px;
            height: 18px;
        }

        .notif-badge {
            position: absolute;
            top: 3px;
            right: 3px;
            background: #ef4444;
            color: white;
            font-size: .55rem;
            font-weight: 700;
            padding: .1rem .3rem;
            border-radius: 9999px;
            border: 2px solid white;
            line-height: 1.4;
        }

        .header-divider {
            width: 1px;
            height: 24px;
            background: #e5e7eb;
            margin: 0 .25rem;
        }

        /* User menu */
        .user-menu {
            position: relative;
        }

        .user-btn {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .375rem .5rem;
            border-radius: 8px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: background .15s;
        }

        .user-btn:hover {
            background: #f3f4f6;
        }

        .user-btn img {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .user-btn-info {
            text-align: left;
        }

        .user-btn-name {
            font-size: .75rem;
            font-weight: 600;
            color: #111827;
            line-height: 1.2;
        }

        .user-btn-role {
            font-size: .65rem;
            color: #9ca3af;
        }

        .user-btn-chevron {
            width: 12px;
            height: 12px;
            color: #9ca3af;
            flex-shrink: 0;
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + .5rem);
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: .625rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
            min-width: 210px;
            display: none;
            z-index: 50;
            overflow: hidden;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-header {
            padding: .75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .dropdown-header p:first-child {
            font-size: .8rem;
            font-weight: 600;
            color: #111827;
        }

        .dropdown-header p:last-child {
            font-size: .72rem;
            color: #9ca3af;
            margin-top: .1rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .65rem 1rem;
            font-size: .8rem;
            color: #374151;
            text-decoration: none;
            transition: background .15s;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background: #f9fafb;
        }

        .dropdown-item svg {
            width: 15px;
            height: 15px;
            color: #9ca3af;
            flex-shrink: 0;
        }

        .dropdown-item.danger {
            color: #dc2626;
        }

        .dropdown-item.danger svg {
            color: #dc2626;
        }

        .dropdown-divider {
            height: 1px;
            background: #f1f5f9;
        }

        /* ─────────────────────────────────────────
           MAIN
        ───────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
            transition: margin-left .3s cubic-bezier(.4, 0, .2, 1);
        }

        .main-content {
            padding: 1.5rem;
        }

        /* ─────────────────────────────────────────
           SHARED COMPONENTS
        ───────────────────────────────────────── */
        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: 1.5rem;
            transition: box-shadow .2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f9fafb;
            padding: .7rem 1rem;
            text-align: left;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: .875rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: .8375rem;
            color: #374151;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover td {
            background: #fafafa;
        }

        .badge {
            display: inline-block;
            padding: .2rem .65rem;
            border-radius: 9999px;
            font-size: .7rem;
            font-weight: 600;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #374151;
        }

        .badge-purple {
            background: #ede9fe;
            color: #5b21b6;
        }

        .progress-bar {
            background: #e5e7eb;
            height: .5rem;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill {
            background: #1f2937;
            height: 100%;
            border-radius: 9999px;
            transition: width .3s;
        }

        .alert {
            border-radius: .625rem;
            padding: .875rem 1.125rem;
            font-size: .875rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            flex-direction: column;
        }

        .alert-error ul {
            margin: .25rem 0 0 1rem;
        }

        /* ─────────────────────────────────────────
           RESPONSIVE BREAKPOINTS
        ───────────────────────────────────────── */

        /* ── Large tablet (≤1200px) ── */
        @media (max-width: 1200px) {
            :root {
                --sidebar-w: 220px;
            }
        }

        /* ── Tablet (≤960px) : icon-only ── */
        @media (max-width: 960px) {
            :root {
                --sidebar-w: 60px;
            }

            .sidebar-logo-text,
            .nav-label,
            .sidebar-item-text,
            .sidebar-footer-info,
            .sidebar-footer-cog {
                display: none !important;
            }

            .sidebar-item.active::after {
                display: none;
            }

            .sidebar-logo {
                justify-content: center;
                padding: 0;
            }

            .sidebar-section {
                padding: 0 .5rem;
            }

            .sidebar-item {
                justify-content: center;
                padding: .65rem;
                gap: 0;
                border-left: none;
            }

            .sidebar-item.active {
                border-radius: .5rem;
            }

            .sidebar-footer {
                padding: .625rem .5rem;
            }

            .sidebar-footer-inner {
                justify-content: center;
            }

            .user-btn-info,
            .user-btn-chevron {
                display: none;
            }
        }

        /* Tooltips for icon-only mode */
        @media (min-width: 641px) and (max-width: 960px) {
            .sidebar-item {
                position: relative;
            }

            .sidebar-item[data-tip]:hover::before {
                content: attr(data-tip);
                position: absolute;
                left: calc(100% + 10px);
                top: 50%;
                transform: translateY(-50%);
                background: #1f2937;
                color: #fff;
                font-size: .72rem;
                font-weight: 500;
                padding: .3rem .65rem;
                border-radius: 6px;
                white-space: nowrap;
                pointer-events: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
                z-index: 100;
            }

            .sidebar-item[data-tip]:hover::after {
                content: '';
                position: absolute;
                left: calc(100% + 6px);
                top: 50%;
                transform: translateY(-50%);
                border: 4px solid transparent;
                border-right-color: #1f2937;
                pointer-events: none;
                z-index: 100;
                /* override active dot */
                width: 0;
                height: 0;
                border-radius: 0;
                background: transparent;
            }
        }

        /* ── Mobile (≤640px) : drawer ── */
        @media (max-width: 640px) {
            :root {
                --sidebar-w: 256px;
            }

            /* Restore all text in drawer */
            .sidebar-logo-text,
            .nav-label,
            .sidebar-item-text,
            .sidebar-footer-info,
            .sidebar-footer-cog {
                display: unset !important;
            }

            .sidebar-item.active::after {
                display: block;
            }

            .sidebar-logo {
                justify-content: flex-start;
                padding: 0 1rem;
            }

            .sidebar-section {
                padding: 0 .75rem;
            }

            .sidebar-item {
                justify-content: flex-start;
                padding: .575rem .75rem;
                gap: .625rem;
                border-left: 2px solid transparent;
            }

            .sidebar-item.active {
                border-left-color: transparent;
            }

            .sidebar-footer {
                padding: .75rem;
            }

            .sidebar-footer-inner {
                justify-content: flex-start;
            }

            /* Drawer hidden */
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0, 0, 0, .12);
            }

            .sidebar-close {
                display: flex;
            }

            .t-overlay {
                display: block;
            }

            /* Header full width */
            .header {
                left: 0;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .menu-btn {
                display: flex;
            }

            .main-content {
                padding: 1rem;
            }

            .user-btn-info,
            .user-btn-chevron {
                display: none;
            }
        }

        /* ── Extra small (≤380px) ── */
        @media (max-width: 380px) {
            .header {
                padding: 0 .875rem;
            }

            .main-content {
                padding: .75rem;
            }
        }

        /* Fix sidebar mobile affichage */
        @media (max-width: 640px) {
            .sidebar {
                z-index: 50;
                /* au-dessus du header */
            }

            .t-overlay {
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

    {{-- OVERLAY --}}
    <div class="t-overlay" id="t-overlay" onclick="closeSidebar()"></div>

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside class="sidebar" id="t-sidebar">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="sidebar-logo-mark">
                @if (isset($institution) && $institution?->logo)
                    <img src="{{ $institution->logo_url }}" alt="Logo"
                        style="width:32px;height:32px;object-fit:cover;">
                @else
                    <svg style="width:16px;height:16px;" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                @endif
            </div>
            <span class="sidebar-logo-text">
                {{ Str::limit($institution->name ?? 'Mon Établissement', 22) }}
            </span>
            <button class="sidebar-close" onclick="closeSidebar()" aria-label="Fermer">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Scrollable nav --}}
        <div class="sidebar-inner">
            <nav class="sidebar-nav">

                {{-- Tableau de bord --}}
                <div class="sidebar-section">
                    <p class="nav-label">Tableau de bord</p>
                    <a href="{{ route('teacher.dashboard') }}"
                        class="sidebar-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}"
                        data-tip="Dashboard">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="sidebar-item-text">Dashboard</span>
                    </a>
                </div>

                {{-- Enseignement --}}
                <div class="sidebar-section">
                    <p class="nav-label">Enseignement</p>
                    <a href="{{ route('teacher.classes.index') }}"
                        class="sidebar-item {{ request()->routeIs('teacher.classes.*') ? 'active' : '' }}"
                        data-tip="Mes Classes">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="sidebar-item-text">Mes Classes</span>
                    </a>
                    <a href="{{ route('teacher.apprenants.index') }}"
                        class="sidebar-item {{ request()->routeIs('teacher.apprenants.*') ? 'active' : '' }}"
                        data-tip="Mes Élèves">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="sidebar-item-text">Mes Élèves</span>
                    </a>
                </div>

                {{-- Évaluations --}}
                <div class="sidebar-section">
                    <p class="nav-label">Évaluations</p>
                    <a href="{{ route('teacher.evaluations.index') }}"
                        class="sidebar-item {{ request()->routeIs('teacher.evaluations.*') ? 'active' : '' }}"
                        data-tip="Évaluations">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span class="sidebar-item-text">Évaluations</span>
                    </a>
                    <a href="{{ route('teacher.notes.index') }}"
                        class="sidebar-item {{ request()->routeIs('teacher.notes.*', 'teacher.grades.*') ? 'active' : '' }}"
                        data-tip="Saisie des Notes">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span class="sidebar-item-text">Saisie des Notes</span>
                    </a>

                    <a href="{{ route('teacher.planning') }}" *
                        class="sidebar-item {{ request()->routeIs('teacher.planning') ? 'active' : '' }}" *
                        data-tip="Mon Planning">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" *
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

                        </svg>
                        <span class="sidebar-item-text">Mon Planning</span>
                    </a>

                    <a href="{{ route('teacher.sujets.index') }}"
                        class="sidebar-item {{ request()->routeIs('teacher.sujets*') ? 'active' : '' }}"
                        data-tip="Sujets d'examens">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                  a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="sidebar-item-text">Sujets d'examens</span>
                    </a>
                </div>

                {{-- Mon compte --}}
                <div class="sidebar-section">
                    <p class="nav-label">Mon compte</p>
                    <a href="{{ route('teacher.library') }}"
                        class="sidebar-item {{ request()->routeIs('teacher.library') ? 'active' : '' }}"
                        data-tip="Bibliothèque">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13..." />
                        </svg>
                        <span class="sidebar-item-text">Bibliothèque</span>
                    </a>
                    <a href="{{ route('teacher.profil') }}"
                        class="sidebar-item {{ request()->routeIs('teacher.profil') ? 'active' : '' }}"
                        data-tip="Mon Profil">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="sidebar-item-text">Mon Profil</span>
                    </a>
                </div>

            </nav>
        </div>

        {{-- Footer teacher card --}}
        @if (isset($teacher))
            <div class="sidebar-footer">
                <div class="sidebar-footer-inner">
                    <img src="{{ isset($teacher->photo) && $teacher->photo ? asset('storage/' . $teacher->photo) : 'https://i.pravatar.cc/40?img=10' }}"
                        alt="Photo">
                    <div class="sidebar-footer-info" style="min-width:0;">
                        <div class="sidebar-footer-name"
                            style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $teacher->prenom }} {{ $teacher->nom }}
                        </div>
                        <div class="sidebar-footer-sub"
                            style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $teacher->matricule ?? '' }}
                        </div>
                    </div>
                    <a href="{{ route('teacher.profil') }}" class="sidebar-footer-cog" title="Paramètres">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                </div>
            </div>
        @endif

    </aside>

    {{-- ══════════ HEADER ══════════ --}}
    <header class="header">
        <div class="header-left">
            {{-- Hamburger --}}
            <button class="menu-btn" onclick="openSidebar()" aria-label="Menu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            {{-- Page title (desktop) --}}
            <h1 class="page-title" style="display:none;" id="desktopTitle">
                @yield('page-title', 'Tableau de bord')
            </h1>
        </div>

        <div class="header-right">
            {{-- Notifications --}}
            <button class="icon-btn" title="Notifications" aria-label="Notifications">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="notif-badge">3</span>
            </button>

            {{-- Messages --}}
            <button class="icon-btn" title="Messages" aria-label="Messages">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span class="notif-badge">5</span>
            </button>

            <div class="header-divider"></div>

            {{-- User dropdown --}}
            <div class="user-menu">
                <button class="user-btn" onclick="toggleDropdown(event)" aria-label="Menu utilisateur">
                    <img src="{{ isset($teacher) && $teacher->photo ? asset('storage/' . $teacher->photo) : 'https://i.pravatar.cc/40?img=10' }}"
                        alt="Avatar">
                    <div class="user-btn-info">
                        <div class="user-btn-name">{{ Auth::user()->name ?? 'Enseignant' }}</div>
                        <div class="user-btn-role">
                            {{ isset($teacher) ? $teacher->specialite ?? 'Enseignant' : 'Enseignant' }}
                        </div>
                    </div>
                    <svg class="user-btn-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div class="dropdown-menu" id="userDropdown">
                    <div class="dropdown-header">
                        <p>{{ Auth::user()->name ?? '—' }}</p>
                        <p>{{ Auth::user()->email ?? '—' }}</p>
                    </div>
                    <a href="{{ route('teacher.profil') }}" class="dropdown-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Mon profil
                    </a>
                    <a href="{{ route('teacher.dashboard') }}" class="dropdown-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- ══════════ MAIN ══════════ --}}
    <div class="main-wrapper">
        <div class="main-content">

            {{-- Page title (mobile, inside content) --}}
            <h1 style="font-size:.9rem;font-weight:700;color:#1f2937;margin-bottom:1.25rem;" id="mobileTitle"
                class="md-title">
                @yield('page-title', 'Tableau de bord')
            </h1>

            @yield('content')
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('t-sidebar');
        const overlay = document.getElementById('t-overlay');
        const dropdown = document.getElementById('userDropdown');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.style.display = 'block'; // 🔥 clé
            requestAnimationFrame(() => {
                overlay.classList.add('visible');
            });
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('visible');

            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300); // sync animation

            document.body.style.overflow = '';
        }

        function toggleDropdown(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        }

        // Close dropdown on outside click
        document.addEventListener('click', () => dropdown.classList.remove('show'));

        // Close sidebar on nav link click (mobile)
        sidebar.querySelectorAll('.sidebar-item').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 640) closeSidebar();
            });
        });

        // Escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeSidebar();
                dropdown.classList.remove('show');
            }
        });

        // Show/hide desktop title
        function applyTitleVisibility() {
            const dt = document.getElementById('desktopTitle');
            const mt = document.getElementById('mobileTitle');
            if (!dt || !mt) return;
            if (window.innerWidth > 640) {
                dt.style.display = 'block';
                mt.style.display = 'none';
            } else {
                dt.style.display = 'none';
                mt.style.display = 'block';
            }
        }
        applyTitleVisibility();
        window.addEventListener('resize', () => {
            applyTitleVisibility();
            if (window.innerWidth > 640) {
                overlay.classList.remove('visible');
                document.body.style.overflow = '';
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
