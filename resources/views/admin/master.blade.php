<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — EduAdmin</title>
<link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 260px;
            --header-h: 64px;
        }

        * {
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
        .a-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            z-index: 39;
            opacity: 0;
            transition: opacity .3s ease;
        }

        .a-overlay.visible {
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
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 40;
            display: flex;
            flex-direction: column;
            transition: transform .3s cubic-bezier(.4, 0, .2, 1), width .3s cubic-bezier(.4, 0, .2, 1);
            will-change: transform;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f9fafb;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* ─── Logo ─── */
        .sidebar-logo {
            display: flex;
            align-items: center;
            height: var(--header-h);
            border-bottom: 1px solid #f3f4f6;
            padding: 0 1rem;
            flex-shrink: 0;
            gap: .625rem;
            overflow: hidden;
        }

        .sidebar-logo-mark {
            width: 32px;
            height: 32px;
            background: #1f2937;
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-logo-text {
            font-size: .9rem;
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
            flex: 1;
        }

        .sidebar-section {
            padding: 0 .75rem;
            margin-bottom: 1.25rem;
        }

        .sidebar-section-label {
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #9ca3af;
            padding: 0 .5rem;
            margin-bottom: .375rem;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: .625rem .75rem;
            margin-bottom: 2px;
            border-radius: .5rem;
            color: #6b7280;
            font-size: .875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
            border-left: 2px solid transparent;
            white-space: nowrap;
            overflow: hidden;
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
            margin-left: .625rem;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Badge notif sur item sidebar */
        .sidebar-badge {
            margin-left: auto;
            background: #ef4444;
            color: white;
            font-size: .6rem;
            font-weight: 700;
            padding: .1rem .4rem;
            border-radius: 99px;
            line-height: 1.4;
            flex-shrink: 0;
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
            justify-content: space-between;
            padding: 0 1.5rem;
            gap: 1rem;
            transition: left .3s cubic-bezier(.4, 0, .2, 1);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: .75rem;
            min-width: 0;
            flex: 1;
        }

        .menu-btn {
            display: none;
            width: 36px;
            height: 36px;
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

        .search-wrapper {
            position: relative;
        }

        .search-input {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: .5rem 1rem .5rem 2.25rem;
            font-size: .875rem;
            width: 320px;
            max-width: 100%;
            transition: all .2s ease;
        }

        .search-input:focus {
            outline: none;
            background: #fff;
            border-color: #d1d5db;
        }

        .search-icon {
            position: absolute;
            left: .625rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .search-icon svg {
            width: 15px;
            height: 15px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-shrink: 0;
        }

        .icon-btn {
            position: relative;
            width: 38px;
            height: 38px;
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
        }

        .icon-btn svg {
            width: 20px;
            height: 20px;
        }

        .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid white;
        }

        /* User menu */
        .user-menu {
            position: relative;
        }

        .user-btn {
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: .375rem .625rem;
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
            width: 34px;
            height: 34px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .user-btn-info {
            text-align: left;
        }

        .user-btn-name {
            font-size: .8rem;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
        }

        .user-btn-role {
            font-size: .7rem;
            color: #9ca3af;
        }

        .user-btn-chevron {
            width: 14px;
            height: 14px;
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
            box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
            min-width: 210px;
            display: none;
            z-index: 50;
            overflow: hidden;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-header {
            padding: .875rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .dropdown-header p:first-child {
            font-size: .85rem;
            font-weight: 600;
            color: #111827;
        }

        .dropdown-header p:last-child {
            font-size: .75rem;
            color: #9ca3af;
            margin-top: .1rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .625rem 1rem;
            font-size: .85rem;
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
            background: #f3f4f6;
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
            transition: box-shadow .2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
        }

        .chart-card,
        .table-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
        }

        .chart-card {
            padding: 1.5rem;
        }

        .table-card {
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f9fafb;
            padding: .75rem 1rem;
            text-align: left;
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: .875rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: .875rem;
            color: #374151;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: .2rem .6rem;
            border-radius: 9999px;
            font-size: .72rem;
            font-weight: 500;
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

        .progress-bar {
            background: #e5e7eb;
            height: 8px;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill {
            background: #1f2937;
            height: 100%;
            border-radius: 9999px;
            transition: width .3s ease;
        }

        /* ─────────────────────────────────────────
           RESPONSIVE BREAKPOINTS
        ───────────────────────────────────────── */
        @media (max-width: 1200px) {
            :root {
                --sidebar-w: 220px;
            }

            .search-input {
                width: 240px;
            }
        }

        @media (max-width: 960px) {
            :root {
                --sidebar-w: 64px;
            }

            .sidebar-logo-text,
            .sidebar-section-label,
            .sidebar-item-text,
            .sidebar-badge {
                display: none !important;
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
                border-left: none;
            }

            .sidebar-item.active {
                border-radius: .5rem;
            }

            .search-input {
                width: 180px;
            }

            .user-btn-info,
            .user-btn-chevron {
                display: none;
            }
        }

        @media (min-width: 641px) and (max-width: 960px) {
            .sidebar-item {
                position: relative;
            }

            .sidebar-item[data-tip]:hover::after {
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

            .sidebar-item[data-tip]:hover::before {
                content: '';
                position: absolute;
                left: calc(100% + 6px);
                top: 50%;
                transform: translateY(-50%);
                border: 4px solid transparent;
                border-right-color: #1f2937;
                pointer-events: none;
                z-index: 100;
            }
        }

        @media (max-width: 640px) {
            :root {
                --sidebar-w: 260px;
            }

            .sidebar-logo-text,
            .sidebar-section-label,
            .sidebar-item-text {
                display: unset !important;
            }

            .sidebar-badge {
                display: inline-block !important;
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
                padding: .625rem .75rem;
                border-left: 2px solid transparent;
            }

            .sidebar-item.active {
                border-left-color: transparent;
            }

            .sidebar-close {
                display: flex;
            }

            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0, 0, 0, .15);
            }

            .a-overlay {
                display: block;
            }

            .header {
                left: 0;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .menu-btn {
                display: flex;
            }

            .search-wrapper {
                display: none;
            }

            .main-content {
                padding: 1rem;
            }

            .user-btn-info,
            .user-btn-chevron {
                display: none;
            }
        }

        @media (max-width: 380px) {
            .header {
                padding: 0 .875rem;
            }

            .main-content {
                padding: .75rem;
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

    {{-- OVERLAY --}}
    <div class="a-overlay" id="a-overlay" onclick="closeSidebar()"></div>

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside class="sidebar" id="a-sidebar">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="sidebar-logo-mark">
                @if (isset($institution) && $institution?->logo)
                    <img src="{{ asset('storage/' . $institution->logo) }}" alt="Logo"
                        style="width:32px;height:32px;object-fit:cover;border-radius:8px;">
                @else
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo"
                        style="width:18px;height:18px;filter:invert(1);">
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

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            {{-- ── PRINCIPAL ── --}}
            <div class="sidebar-section">
                <p class="sidebar-section-label">Principal</p>

                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    data-tip="Tableau de bord">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sidebar-item-text">Tableau de bord</span>
                </a>

                <a href="{{ route('admin.administrative') }}"
                    class="sidebar-item {{ request()->routeIs('admin.administrative') ? 'active' : '' }}"
                    data-tip="Gestion administrative">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="sidebar-item-text">Gestion administrative</span>
                </a>

                <a href="{{ route('admin.staff') }}"
                    class="sidebar-item {{ request()->routeIs('admin.staff') ? 'active' : '' }}"
                    data-tip="Gestion des staffs">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="sidebar-item-text">Gestion des staffs</span>
                </a>

                <a href="{{ route('admin.academic') }}"
                    class="sidebar-item {{ request()->routeIs('admin.academic') ? 'active' : '' }}"
                    data-tip="Gestion académique">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="sidebar-item-text">Gestion académique</span>
                </a>

                <a href="{{ route('admin.apprenants') }}"
                    class="sidebar-item {{ request()->routeIs('admin.apprenants') ? 'active' : '' }}"
                    data-tip="Gestion des élèves">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-item-text">Gestion des élèves</span>
                </a>
            </div>

            {{-- ── GESTION ── --}}
            <div class="sidebar-section">
                <p class="sidebar-section-label">Gestion</p>

                <a href="{{ route('admin.teachers') }}"
                    class="sidebar-item {{ request()->routeIs('admin.teachers') ? 'active' : '' }}"
                    data-tip="Enseignants">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="sidebar-item-text">Gestion des enseignants</span>
                </a>

                <a href="{{ route('admin.staff_tasks.index') }}"
                    class="sidebar-item {{ request()->routeIs('admin.staff_tasks*') ? 'active' : '' }}"
                    data-tip="Tâches du staff">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="sidebar-item-text">Tâches du staff</span>
                </a>
                <a href="{{ route('admin.grade_config') }}"
                    class="sidebar-item {{ request()->routeIs('admin.grade_config*', 'admin.bulletins*') ? 'active' : '' }}"
                    data-tip="Notes & Bulletins">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="sidebar-item-text">Notes & Bulletins</span>
                </a>

                <a href="{{ route('admin.financial') }}"
                    class="sidebar-item {{ request()->routeIs('admin.financial') ? 'active' : '' }}"
                    data-tip="Gestion financière">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                    <span class="sidebar-item-text">Gestion financière</span>
                </a>

                <a href="{{ route('admin.parents') }}"
                    class="sidebar-item {{ request()->routeIs('admin.parents') ? 'active' : '' }}"
                    data-tip="Parents d'élèves">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-item-text">Parents d'élèves</span>
                </a>

                <a href="{{ route('admin.sujets.index') }}"
                    class="sidebar-item {{ request()->routeIs('admin.sujets*') ? 'active' : '' }}"
                    data-tip="Sujets reçus">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                  a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="sidebar-item-text">Sujets reçus</span>
                    @php
                        try {
                            $pendingSujets = \App\Models\SujetExamen::where(
                                'institution_id',
                                Auth::user()?->institution_id,
                            )
                                ->where('statut', 'en_attente')
                                ->count();
                        } catch (\Exception) {
                            $pendingSujets = 0;
                        }
                    @endphp
                    @if ($pendingSujets > 0)
                        <span class="sidebar-badge">{{ $pendingSujets }}</span>
                    @endif
                </a>

                {{-- ── NOUVEAU : Bibliothèque ── --}}
                <a href="{{ route('admin.library') }}"
                    class="sidebar-item {{ request()->routeIs('admin.library*') ? 'active' : '' }}"
                    data-tip="Bibliothèque">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="sidebar-item-text">Bibliothèque</span>
                </a>

                {{-- ── NOUVEAU : Transferts inter-écoles ── --}}
                <a href="{{ route('admin.transfer.index') }}"
                    class="sidebar-item {{ request()->routeIs('admin.transfer*') ? 'active' : '' }}"
                    data-tip="Transferts inter-établissements">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span class="sidebar-item-text">Transferts inter-écoles</span>
                    {{-- Badge demandes en attente --}}
                    @php
                        try {
                            $pendingTransfers = \App\Models\TransferRequest::where(
                                'institution_source_id',
                                Auth::user()?->institution_id,
                            )
                                ->where('statut', 'pending')
                                ->count();
                        } catch (\Exception) {
                            $pendingTransfers = 0;
                        }
                    @endphp
                    @if ($pendingTransfers > 0)
                        <span class="sidebar-badge">{{ $pendingTransfers }}</span>
                    @endif
                </a>
            </div>

            {{-- ── PARAMÈTRES ── --}}
            <div class="sidebar-section">
                <p class="sidebar-section-label">Paramètres</p>

                <a href="{{ route('admin.disciplinaire') }}"
                    class="sidebar-item {{ request()->routeIs('admin.disciplinaire*') ? 'active' : '' }}"
                    data-tip="Gestion disciplinaire">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="sidebar-item-text">Gestion disciplinaire</span>
                </a>

                <a href="{{ route('admin.rapports') }}"
                    class="sidebar-item {{ request()->routeIs('admin.rapports') ? 'active' : '' }}"
                    data-tip="Rapports & statistiques">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="sidebar-item-text">Rapports & statistiques</span>
                </a>

                <a href="{{ route('admin.planning') }}"
                    class="sidebar-item {{ request()->routeIs('admin.planning*') ? 'active' : '' }}"
                    data-tip="Planning">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="sidebar-item-text">Planning</span>
                </a>
            </div>

            {{-- ── MON COMPTE ── --}}
            <div class="sidebar-section">
                <p class="sidebar-section-label">Mon compte</p>

                {{-- ── NOUVEAU : Mon profil ── --}}
                <a href="{{ route('admin.profil') }}"
                    class="sidebar-item {{ request()->routeIs('admin.profil*') ? 'active' : '' }}"
                    data-tip="Mon profil">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="sidebar-item-text">Mon profil</span>
                </a>

                {{-- ── NOUVEAU : Paramètres établissement ── --}}
                {{-- <a href="{{ route('admin.institution.settings') }}"
                    class="sidebar-item {{ request()->routeIs('admin.institution.settings') ? 'active' : '' }}"
                    data-tip="Mon établissement">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="sidebar-item-text">Mon établissement</span>
                </a> --}}
                <a href="{{ route('admin.bled.index') }}"
                    class="sidebar-item {{ request()->routeIs('admin.bled*') ? 'active' : '' }}"
                    data-tip="Archives BLED">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span class="sidebar-item-text">Archives</span>
                </a>
            </div>

        </nav>
    </aside>

    {{-- ══════════ HEADER ══════════ --}}
    <header class="header">
        <div class="header-left">
            <button class="menu-btn" onclick="openSidebar()" aria-label="Menu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="search-wrapper">
                <span class="search-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                </span>
                <input type="text" class="search-input" placeholder="Rechercher…">
            </div>
        </div>

        <div class="header-right">
            {{-- Notifications --}}
            <button class="icon-btn" title="Notifications" aria-label="Notifications">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="notif-dot"></span>
            </button>

            {{-- Messages --}}
            <button class="icon-btn" title="Messages" aria-label="Messages">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span class="notif-dot"></span>
            </button>

            {{-- User dropdown --}}
            <div class="user-menu">
                <button class="user-btn" onclick="toggleDropdown()" aria-label="Menu utilisateur">
                    {{-- Avatar : priorité au champ avatar du User, sinon initiales --}}
                    @php
                        $avatarSrc = Auth::user()?->avatar
                            ? asset('storage/' . Auth::user()->avatar)
                            : 'https://ui-avatars.com/api/?name=' .
                                urlencode(Auth::user()?->name ?? 'Admin') .
                                '&background=1f2937&color=fff&size=68&font-size=0.4';
                    @endphp
                    <img src="{{ $avatarSrc }}" alt="Avatar">
                    <div class="user-btn-info">
                        <div class="user-btn-name">{{ Auth::user()->name ?? 'Administrateur' }}</div>
                        <div class="user-btn-role">{{ $institution->name ?? 'Administration' }}</div>
                    </div>
                    <svg class="user-btn-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div class="dropdown-menu" id="userDropdown">
                    <div class="dropdown-header">
                        <p>{{ Auth::user()->name ?? 'Administrateur' }}</p>
                        <p>{{ Auth::user()->email ?? '' }}</p>
                    </div>

                    {{-- Mon profil --}}
                    <a href="{{ route('admin.profil') }}" class="dropdown-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Mon profil
                    </a>

                    {{-- Mon établissement --}}
                    {{-- <a href="{{ route('admin.institution.settings') }}" class="dropdown-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Mon établissement
                    </a> --}}

                    <div class="dropdown-divider"></div>

                    {{-- Déconnexion --}}
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
            @yield('content')
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('a-sidebar');
        const overlay = document.getElementById('a-overlay');
        const dropdown = document.getElementById('userDropdown');

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

        function toggleDropdown() {
            dropdown.classList.toggle('show');
        }

        document.addEventListener('click', e => {
            const menu = document.querySelector('.user-menu');
            if (menu && !menu.contains(e.target)) dropdown.classList.remove('show');
        });

        sidebar.querySelectorAll('.sidebar-item').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 640) closeSidebar();
            });
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeSidebar();
                dropdown.classList.remove('show');
            }
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
