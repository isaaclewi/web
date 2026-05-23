<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — SyntriForge Edu</title>
    <link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --c-bg: #0a0c10;
            --c-surface: #111318;
            --c-surface-2: #15181f;
            --c-border: #1e2230;
            --c-border-b: #2d3348;
            --c-text: #e2e8f0;
            --c-muted: #64748b;
            --c-accent: #00d4ff;
            --c-accent-dim: rgba(0,212,255,.08);
            --c-accent-glow: rgba(0,212,255,.25);
            --c-green: #10b981;
            --c-red: #ef4444;
            --c-yellow: #f59e0b;
            --sidebar-w: 260px;
            --header-h: 60px;
        }

        *, *::before, *::after {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            margin: 0; padding: 0;
        }

        body { background: var(--c-bg); color: var(--c-text); overflow-x: hidden; }

        /* ─── OVERLAY ─── */
        .sa-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.65);
            backdrop-filter: blur(3px);
            z-index: 39;
            opacity: 0;
            transition: opacity .3s ease;
        }
        .sa-overlay.visible { opacity: 1; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--c-surface);
            border-right: 1px solid var(--c-border);
            display: flex; flex-direction: column;
            z-index: 40; overflow: hidden;
            transition: transform .3s cubic-bezier(.4,0,.2,1), width .3s cubic-bezier(.4,0,.2,1);
        }

        .sidebar-scroll { flex: 1; overflow-y: auto; overflow-x: hidden; }
        .sidebar-scroll::-webkit-scrollbar { width: 3px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: var(--c-surface); }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: var(--c-border-b); border-radius: 2px; }

        .sidebar-logo {
            display: flex; align-items: center;
            height: var(--header-h); border-bottom: 1px solid var(--c-border);
            padding: 0 1.25rem; gap: .625rem; flex-shrink: 0; overflow: hidden;
        }

        .logo-icon {
            width: 32px; height: 32px;
            background: var(--c-accent); color: var(--c-bg);
            border-radius: .5rem;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .8125rem; flex-shrink: 0;
            box-shadow: 0 0 12px var(--c-accent-glow);
        }

        .logo-name {
            font-size: .9375rem; font-weight: 600; color: var(--c-text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;
        }

        .sidebar-close {
            display: none; margin-left: auto;
            width: 28px; height: 28px; border-radius: 6px;
            background: rgba(255,255,255,.05); border: 1px solid var(--c-border);
            color: var(--c-muted); align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0; transition: background .15s, color .15s;
        }
        .sidebar-close:hover { background: rgba(239,68,68,.15); color: #f87171; }
        .sidebar-close svg { width: 14px; height: 14px; }

        .sidebar-nav { padding: 1rem 0; }
        .nav-group { padding: 0 .75rem; margin-bottom: 1.5rem; }
        .nav-label {
            font-size: .6875rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .08em; color: var(--c-border-b);
            padding: 0 .25rem; margin-bottom: .375rem;
            white-space: nowrap; overflow: hidden;
        }

        .sidebar-item {
            display: flex; align-items: center;
            padding: .575rem .75rem; margin: .1rem 0;
            border-radius: .5rem; gap: .625rem;
            color: var(--c-muted); font-size: .8125rem; font-weight: 500;
            text-decoration: none; cursor: pointer;
            transition: all .2s ease; border: 1px solid transparent;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar-item:hover { background: rgba(255,255,255,.04); color: var(--c-text); }
        .sidebar-item.active {
            background: var(--c-accent-dim); color: var(--c-accent);
            border-color: rgba(0,212,255,.15);
        }
        .sidebar-item svg { width: 18px; height: 18px; flex-shrink: 0; stroke-width: 1.75; }
        .sidebar-item-text { overflow: hidden; text-overflow: ellipsis; }

        /* ─── HEADER ─── */
        .sa-header {
            position: fixed; top: 0; right: 0; left: var(--sidebar-w);
            height: var(--header-h);
            background: var(--c-surface); border-bottom: 1px solid var(--c-border);
            z-index: 30; display: flex; align-items: center;
            padding: 0 1.5rem; justify-content: space-between; gap: 1rem;
            transition: left .3s cubic-bezier(.4,0,.2,1);
        }

        .header-left { display: flex; align-items: center; gap: .75rem; flex: 1; min-width: 0; }
        .header-right { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }

        .menu-btn {
            display: none; width: 36px; height: 36px; border-radius: .5rem;
            background: transparent; border: 1px solid var(--c-border);
            color: var(--c-muted); align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0; transition: background .15s, color .15s;
        }
        .menu-btn:hover { background: rgba(255,255,255,.05); color: var(--c-text); }
        .menu-btn svg { width: 18px; height: 18px; }

        .icon-btn {
            position: relative; width: 36px; height: 36px; border-radius: .5rem;
            background: transparent; border: none; color: var(--c-muted);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .2s; flex-shrink: 0;
        }
        .icon-btn:hover { background: rgba(255,255,255,.05); color: var(--c-text); }
        .icon-btn svg { width: 18px; height: 18px; }

        .notif-dot {
            position: absolute; top: 3px; right: 3px;
            background: var(--c-red); color: white;
            font-size: .55rem; font-weight: 700;
            padding: .1rem .3rem; border-radius: 99px;
            border: 2px solid var(--c-surface); line-height: 1.3;
        }

        .user-menu { position: relative; }
        .user-btn {
            display: flex; align-items: center; gap: .625rem;
            padding: .375rem .625rem; border-radius: .5rem;
            background: transparent; border: 1px solid var(--c-border);
            cursor: pointer; transition: all .2s;
        }
        .user-btn:hover { background: rgba(255,255,255,.04); border-color: var(--c-border-b); }
        .user-btn img {
            width: 30px; height: 30px; border-radius: .375rem;
            object-fit: cover; border: 1px solid var(--c-border-b); flex-shrink: 0;
        }
        .user-name { font-size: .8rem; font-weight: 600; color: var(--c-text); white-space: nowrap; }
        .user-role { font-size: .68rem; color: var(--c-muted); }
        .user-chevron { width: 12px; height: 12px; color: var(--c-muted); flex-shrink: 0; }

        .dropdown-menu {
            position: absolute; top: calc(100% + .5rem); right: 0;
            background: var(--c-surface-2); border: 1px solid var(--c-border);
            border-radius: .625rem; box-shadow: 0 16px 40px rgba(0,0,0,.5);
            min-width: 220px; display: none; z-index: 50; overflow: hidden;
        }
        .dropdown-menu.show { display: block; }
        .dropdown-head { padding: .875rem 1rem; border-bottom: 1px solid var(--c-border); }
        .dropdown-head-name { font-size: .875rem; font-weight: 600; color: var(--c-text); }
        .dropdown-head-email { font-size: .75rem; color: var(--c-muted); margin-top: .1rem; }
        .dropdown-item {
            display: block; padding: .625rem 1rem;
            font-size: .8rem; color: var(--c-muted); cursor: pointer;
            transition: all .2s; text-decoration: none;
            width: 100%; text-align: left; background: transparent; border: none;
        }
        .dropdown-item:hover { background: rgba(255,255,255,.04); color: var(--c-text); }
        .dropdown-item.danger { color: var(--c-red); }
        .dropdown-item.danger:hover { background: rgba(239,68,68,.08); }
        .dropdown-divider { height: 1px; background: var(--c-border); margin: .25rem 0; }

        /* ─── MAIN ─── */
        .main-wrap { margin-left: var(--sidebar-w); padding-top: var(--header-h); min-height: 100vh; transition: margin-left .3s cubic-bezier(.4,0,.2,1); }
        .main-content { padding: 1.5rem; }

        /* ─── SHARED COMPONENTS ─── */
        .stat-card { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: .75rem; padding: 1.5rem; transition: all .2s; }
        .stat-card:hover { border-color: var(--c-border-b); box-shadow: 0 4px 20px rgba(0,0,0,.3); }
        .chart-card { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: .75rem; padding: 1.5rem; }
        .table-card { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: .75rem; overflow: hidden; }

        .table { width: 100%; border-collapse: collapse; }
        .table th { background: var(--c-surface-2); padding: .75rem 1rem; text-align: left; font-size: .6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--c-muted); border-bottom: 1px solid var(--c-border); }
        .table td { padding: .875rem 1rem; border-bottom: 1px solid var(--c-border); font-size: .8125rem; color: var(--c-text); }
        .table tr:last-child td { border-bottom: none; }
        .table tr:hover td { background: rgba(255,255,255,.02); }

        .badge { display: inline-block; padding: .2rem .625rem; border-radius: 99px; font-size: .6875rem; font-weight: 500; }
        .badge-success { background: rgba(16,185,129,.12); color: #34d399; border: 1px solid rgba(16,185,129,.2); }
        .badge-warning { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.2); }
        .badge-info { background: var(--c-accent-dim); color: var(--c-accent); border: 1px solid rgba(0,212,255,.15); }
        .badge-danger { background: rgba(239,68,68,.12); color: #f87171; border: 1px solid rgba(239,68,68,.2); }

        .progress-bar { background: var(--c-border); height: 6px; border-radius: 99px; overflow: hidden; }
        .progress-fill { background: var(--c-accent); height: 100%; border-radius: 99px; transition: width .3s; }

        /* ═══════════════════════════════════════════
           RESPONSIVE — 4 breakpoints
        ═══════════════════════════════════════════ */

        /* ── Large tablet (≤ 1200px) ── */
        @media (max-width: 1200px) {
            :root { --sidebar-w: 220px; }
        }

        /* ── Tablet (≤ 960px) : icônes seules ── */
        @media (max-width: 960px) {
            :root { --sidebar-w: 62px; }

            .logo-name, .nav-label, .sidebar-item-text { display: none !important; }
            .sidebar-logo { justify-content: center; padding: 0; }
            .nav-group { padding: 0 .5rem; }
            .sidebar-item { justify-content: center; padding: .65rem; gap: 0; }

            .user-name, .user-role, .user-chevron { display: none; }
            .user-btn { padding: .375rem; }
        }

        /* Tooltips pour mode icônes seulement */
        @media (min-width: 641px) and (max-width: 960px) {
            .sidebar-item { position: relative; }
            .sidebar-item[data-tip]:hover::after {
                content: attr(data-tip);
                position: absolute; left: calc(100% + 10px); top: 50%;
                transform: translateY(-50%);
                background: var(--c-surface-2); color: var(--c-text);
                border: 1px solid var(--c-border);
                font-size: .72rem; font-weight: 500;
                padding: .3rem .65rem; border-radius: 6px;
                white-space: nowrap; pointer-events: none;
                box-shadow: 0 4px 16px rgba(0,0,0,.4); z-index: 100;
            }
            .sidebar-item[data-tip]:hover::before {
                content: '';
                position: absolute; left: calc(100% + 6px); top: 50%;
                transform: translateY(-50%);
                border: 4px solid transparent;
                border-right-color: var(--c-border);
                pointer-events: none; z-index: 100;
            }
        }

        /* ── Mobile (≤ 640px) : drawer ── */
        @media (max-width: 640px) {
            :root { --sidebar-w: 260px; }

            .logo-name, .nav-label, .sidebar-item-text { display: unset !important; }
            .sidebar-logo { justify-content: flex-start; padding: 0 1.25rem; }
            .nav-group { padding: 0 .75rem; }
            .sidebar-item { justify-content: flex-start; padding: .575rem .75rem; gap: .625rem; }

            /* Drawer masqué par défaut */
            .sidebar { transform: translateX(-100%); box-shadow: none; }
            .sidebar.open { transform: translateX(0); box-shadow: 8px 0 40px rgba(0,0,0,.5); }

            .sidebar-close { display: flex; }
            .sa-overlay { display: block; }

            .sa-header { left: 0; }
            .main-wrap { margin-left: 0; }
            .menu-btn { display: flex; }

            .user-name, .user-role, .user-chevron { display: none; }
            .user-btn { padding: .375rem; }

            .main-content { padding: 1rem; }
        }

        /* ── Extra small (≤ 380px) ── */
        @media (max-width: 380px) {
            .sa-header { padding: 0 .875rem; }
            .main-content { padding: .75rem; }
        }

        /* Tables scrollables sur mobile */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Grilles adaptatives */
        .grid-responsive-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        @media (max-width: 640px) {
            .grid-responsive-2 { grid-template-columns: 1fr; }
        }

        .grid-responsive-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        @media (max-width: 900px)  { .grid-responsive-3 { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 540px)  { .grid-responsive-3 { grid-template-columns: 1fr; } }

        .grid-responsive-5 {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
        }
        @media (max-width: 1100px) { .grid-responsive-5 { grid-template-columns: repeat(3,1fr); } }
        @media (max-width: 700px)  { .grid-responsive-5 { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 400px)  { .grid-responsive-5 { grid-template-columns: 1fr; } }

        /* Sidebar z-index correction mobile */
        @media (max-width: 640px) {
            .sidebar { z-index: 50; }
            .sa-overlay { z-index: 45; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- OVERLAY --}}
    <div class="sa-overlay" id="sa-overlay" onclick="closeSidebar()"></div>

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside class="sidebar" id="sa-sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">SF</div>
            <span class="logo-name">SyntriForge Edu</span>
            <button class="sidebar-close" onclick="closeSidebar()" aria-label="Fermer">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="sidebar-scroll">
            <nav class="sidebar-nav">
                <div class="nav-group">
                    <p class="nav-label">Principal</p>

                    <a href="{{ route('superadmin.dashboard') }}"
                       class="sidebar-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"
                       data-tip="Tableau de bord">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="sidebar-item-text">Tableau de bord</span>
                    </a>

                    <a href="{{ route('superadmin.users') }}"
                       class="sidebar-item {{ request()->routeIs('superadmin.users') ? 'active' : '' }}"
                       data-tip="Directeurs">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="sidebar-item-text">Directeurs</span>
                    </a>

                    <a href="{{ route('superadmin.institutions') }}"
                       class="sidebar-item {{ request()->routeIs('superadmin.institutions') ? 'active' : '' }}"
                       data-tip="Institutions">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span class="sidebar-item-text">Institutions</span>
                    </a>

                    <a href="{{ route('superadmin.library') }}"
                       class="sidebar-item {{ request()->routeIs('superadmin.library*') ? 'active' : '' }}"
                       data-tip="Bibliothèque globale">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span class="sidebar-item-text">Bibliothèque</span>
                    </a>
                </div>

                <div class="nav-group">
                    <p class="nav-label">Gestion</p>

                    <a href="#" class="sidebar-item" data-tip="Supervision">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span class="sidebar-item-text">Supervision</span>
                    </a>

                    <a href="#" class="sidebar-item" data-tip="Paramètres">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="sidebar-item-text">Paramètres</span>
                    </a>
                </div>
            </nav>
        </div>
    </aside>

    {{-- ══════════ HEADER ══════════ --}}
    <header class="sa-header">
        <div class="header-left">
            <button class="menu-btn" onclick="openSidebar()" aria-label="Menu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            {{-- Breadcrumb optionnel --}}
            <span style="font-size:.8rem;color:var(--c-muted);display:none;" id="page-title-mobile">@yield('title', 'Super Admin')</span>
        </div>

        <div class="header-right">
            <button class="icon-btn" title="Notifications">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="notif-dot">3</span>
            </button>

            <div class="user-menu">
                <button class="user-btn" onclick="toggleDropdown(event)" aria-label="Menu utilisateur">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'SA') }}&background=00d4ff&color=0a0c10" alt="Avatar">
                    <div class="text-left" id="user-info-block" style="display:none;">
                        <div class="user-name">{{ Auth::user()->name ?? 'Super Admin' }}</div>
                        <div class="user-role">Super Administrateur</div>
                    </div>
                    <svg class="user-chevron" id="user-chevron-icon" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div class="dropdown-menu" id="userDropdown">
                    <div class="dropdown-head">
                        <div class="dropdown-head-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="dropdown-head-email">{{ Auth::user()->email ?? '' }}</div>
                        {{-- Badge Super Admin clair --}}
                        <div style="margin-top:.4rem;">
                            <span style="font-size:.6rem;background:rgba(0,212,255,.1);color:var(--c-accent);border:1px solid rgba(0,212,255,.2);padding:.1rem .4rem;border-radius:4px;font-weight:700;">
                                SUPER ADMIN · Sans institution
                            </span>
                        </div>
                    </div>
                    <a href="#" class="dropdown-item">Mon profil</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- ══════════ MAIN ══════════ --}}
    <div class="main-wrap">
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <script>
        const sidebar  = document.getElementById('sa-sidebar');
        const overlay  = document.getElementById('sa-overlay');
        const dropdown = document.getElementById('userDropdown');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('visible');
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('visible');
            setTimeout(() => { overlay.style.display = 'none'; }, 300);
            document.body.style.overflow = '';
        }

        function toggleDropdown(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        }

        document.addEventListener('click', () => dropdown.classList.remove('show'));

        sidebar.querySelectorAll('.sidebar-item').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 640) closeSidebar();
            });
        });

        function applyUserInfo() {
            const show = window.innerWidth > 640;
            document.getElementById('user-info-block').style.display  = show ? 'block' : 'none';
            document.getElementById('user-chevron-icon').style.display = show ? 'block' : 'none';
            document.getElementById('page-title-mobile').style.display = show ? 'none' : 'block';
        }
        applyUserInfo();

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { closeSidebar(); dropdown.classList.remove('show'); }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 640) {
                overlay.classList.remove('visible');
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
            applyUserInfo();
        });
    </script>

    @stack('scripts')
</body>
</html>