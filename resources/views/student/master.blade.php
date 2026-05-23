<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Étudiant')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --ink: #0f172a;
            --ink-mid: #334155;
            --muted: #64748b;
            --border: #e2e8f0;
            --bg: #f1f5f9;
            --primary: #6366f1;
            --primary-light: #eef2ff;
            --primary-mid: #818cf8;
            --success: #10b981;
            --success-light: #ecfdf5;
            --amber: #f59e0b;
            --amber-light: #fffbeb;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --sky: #0ea5e9;
            --sky-light: #f0f9ff;
            --font: 'Sora', sans-serif;
            --mono: 'JetBrains Mono', monospace;
            --radius: .875rem;
            --sidebar-w: 256px;
            --header-h: 56px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--ink-mid);
            font-size: .875rem;
            overflow-x: hidden;
        }

        /* ─────────────────────────────────────────
           OVERLAY
        ───────────────────────────────────────── */
        .s-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            z-index: 39;
            opacity: 0;
            transition: opacity .3s ease;
        }

        .s-overlay.visible {
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
            background: white;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 40;
            transition: transform .3s cubic-bezier(.4, 0, .2, 1),
                width .3s cubic-bezier(.4, 0, .2, 1);
            will-change: transform;
            overflow: hidden;
        }

        /* ─── Logo ─── */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 0 1.25rem;
            height: var(--header-h);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
            overflow: hidden;
        }

        .logo-mark {
            width: 34px;
            height: 34px;
            border-radius: .5rem;
            background: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .logo-name {
            font-size: .875rem;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .logo-sub {
            font-size: .62rem;
            color: var(--muted);
        }

        .logo-text {
            flex: 1;
            min-width: 0;
        }

        /* Close button */
        .sidebar-close {
            display: none;
            margin-left: auto;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #f1f5f9;
            border: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .15s;
            color: var(--muted);
        }

        .sidebar-close:hover {
            background: var(--border);
            color: var(--ink);
        }

        .sidebar-close svg {
            width: 14px;
            height: 14px;
        }

        /* ─── Scrollable nav ─── */
        .sidebar-nav-wrap {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav-wrap::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar-nav-wrap::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 2px;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-section-label {
            font-size: .6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            padding: 0 1.125rem;
            margin-bottom: .375rem;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .625rem;
            padding: .5rem 1rem;
            margin: .1rem .5rem;
            border-radius: .5rem;
            color: var(--muted);
            font-size: .8125rem;
            font-weight: 500;
            text-decoration: none;
            transition: all .15s;
            white-space: nowrap;
            overflow: hidden;
            border-left: 2px solid transparent;
        }

        .nav-item:hover {
            background: #f8fafc;
            color: var(--ink);
        }

        .nav-item.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
            border-left-color: var(--primary);
        }

        .nav-item.active svg {
            color: var(--primary);
        }

        .nav-item svg {
            width: 1.125rem;
            height: 1.125rem;
            flex-shrink: 0;
            stroke-width: 1.75;
        }

        .nav-item-text {
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--primary);
            color: white;
            font-size: .6rem;
            font-weight: 700;
            padding: .1rem .4rem;
            border-radius: 99px;
            font-family: var(--mono);
            flex-shrink: 0;
        }

        /* ─── Footer student card ─── */
        .sidebar-footer {
            border-top: 1px solid var(--border);
            padding: .875rem 1.125rem;
            display: flex;
            align-items: center;
            gap: .625rem;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-footer img {
            width: 32px;
            height: 32px;
            border-radius: .5rem;
            object-fit: cover;
            flex-shrink: 0;
        }

        .sidebar-footer-name {
            font-size: .78rem;
            font-weight: 600;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-footer-class {
            font-size: .68rem;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-footer-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-footer-cog {
            margin-left: auto;
            flex-shrink: 0;
            color: var(--muted);
            transition: color .15s;
        }

        .sidebar-footer-cog:hover {
            color: var(--ink);
        }

        .sidebar-footer-cog svg {
            width: 14px;
            height: 14px;
        }

        /* ─────────────────────────────────────────
           HEADER / TOPBAR
        ───────────────────────────────────────── */
        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-w);
            height: var(--header-h);
            background: white;
            border-bottom: 1px solid var(--border);
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            gap: 1rem;
            transition: left .3s cubic-bezier(.4, 0, .2, 1);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex: 1;
            min-width: 0;
        }

        .topbar-title {
            font-size: .875rem;
            font-weight: 600;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-sub {
            font-size: .68rem;
            color: var(--muted);
            margin-top: .1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-shrink: 0;
        }

        .menu-btn {
            display: none;
            width: 34px;
            height: 34px;
            border-radius: .5rem;
            background: none;
            border: 1px solid var(--border);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .15s;
            color: var(--muted);
        }

        .menu-btn:hover {
            background: #f8fafc;
            color: var(--ink);
        }

        .menu-btn svg {
            width: 16px;
            height: 16px;
        }

        .icon-btn {
            position: relative;
            width: 34px;
            height: 34px;
            border-radius: .5rem;
            background: none;
            border: 1px solid var(--border);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            transition: all .15s;
            flex-shrink: 0;
        }

        .icon-btn:hover {
            background: #f8fafc;
            color: var(--ink);
        }

        .icon-btn svg {
            width: 1rem;
            height: 1rem;
        }

        .notif-dot {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 8px;
            height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid white;
        }

        /* User pill */
        .user-pill {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .3rem .65rem .3rem .3rem;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 99px;
            cursor: pointer;
            transition: all .15s;
        }

        .user-pill:hover {
            border-color: #cbd5e1;
        }

        .user-pill img {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-pill-name {
            font-size: .75rem;
            font-weight: 600;
            color: var(--ink);
            white-space: nowrap;
        }

        .user-pill-chevron {
            width: 11px;
            height: 11px;
            color: var(--muted);
        }

        /* Dropdown */
        .dropdown {
            position: absolute;
            top: calc(100% + .5rem);
            right: 0;
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
            min-width: 200px;
            display: none;
            z-index: 50;
            overflow: hidden;
        }

        .dropdown.open {
            display: block;
        }

        .dropdown-header {
            padding: .8rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .dropdown-header p {
            font-size: .8rem;
            font-weight: 600;
            color: var(--ink);
        }

        .dropdown-header span {
            font-size: .7rem;
            color: var(--muted);
        }

        .dropdown a,
        .dropdown button {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .6rem 1rem;
            font-size: .8rem;
            color: var(--ink-mid);
            text-decoration: none;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: background .12s;
        }

        .dropdown a:hover,
        .dropdown button:hover {
            background: #f8fafc;
        }

        .dropdown a svg,
        .dropdown button svg {
            width: .9rem;
            height: .9rem;
            color: var(--muted);
            flex-shrink: 0;
        }

        .dropdown hr {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: .25rem 0;
        }

        .dropdown .logout {
            color: var(--danger);
        }

        .dropdown .logout svg {
            color: var(--danger);
        }

        /* ─────────────────────────────────────────
           MAIN
        ───────────────────────────────────────── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .3s cubic-bezier(.4, 0, .2, 1);
        }

        .page {
            padding: 1.5rem;
            flex: 1;
        }

        /* ─────────────────────────────────────────
           SHARED COMPONENTS
        ───────────────────────────────────────── */
        .bc {
            display: flex;
            align-items: center;
            gap: .375rem;
            font-size: .75rem;
            color: var(--muted);
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }

        .bc a {
            color: var(--muted);
            text-decoration: none;
            transition: color .15s;
        }

        .bc a:hover {
            color: var(--ink);
        }

        .bc-sep {
            opacity: .4;
            font-size: .625rem;
        }

        .bc-cur {
            color: var(--ink);
            font-weight: 500;
        }

        .alert {
            border-radius: .625rem;
            padding: .875rem 1.125rem;
            font-size: .875rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }

        .alert-s {
            background: var(--success-light);
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-e {
            background: var(--danger-light);
            border: 1px solid #fca5a5;
            color: #991b1b;
            flex-direction: column;
        }

        .card {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .card+.card {
            margin-top: 1.25rem;
        }

        .card-header {
            padding: 1rem 1.375rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .card-title {
            font-size: .9rem;
            font-weight: 600;
            color: var(--ink);
        }

        .card-sub {
            font-size: .7rem;
            color: var(--muted);
            margin-top: .1rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        @media(max-width:900px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:420px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }

        .kpi {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            transition: box-shadow .2s;
        }

        .kpi:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .06);
        }

        .kpi-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .kpi-icon {
            width: 38px;
            height: 38px;
            border-radius: .625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: .875rem;
        }

        .kpi-icon svg {
            width: 1.1rem;
            height: 1.1rem;
        }

        .kpi-val {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -.04em;
            font-family: var(--mono);
            line-height: 1;
        }

        .kpi-lbl {
            font-size: .72rem;
            color: var(--muted);
            font-weight: 500;
            margin-top: .25rem;
        }

        .kpi-note {
            font-size: .7rem;
            font-weight: 600;
            margin-top: .5rem;
            display: flex;
            align-items: center;
            gap: .2rem;
        }

        .up {
            color: var(--success);
        }

        .down {
            color: var(--danger);
        }

        .flat {
            color: var(--amber);
        }

        .t-table {
            width: 100%;
            border-collapse: collapse;
        }

        .t-table th {
            background: #f8fafc;
            padding: .65rem 1.125rem;
            text-align: left;
            font-size: .67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .t-table td {
            padding: .875rem 1.125rem;
            border-bottom: 1px solid #f8fafc;
            font-size: .8125rem;
            color: var(--ink-mid);
            vertical-align: middle;
        }

        .t-table tr:last-child td {
            border-bottom: none;
        }

        .t-table tr:hover td {
            background: #fafbfc;
        }

        .badge {
            display: inline-block;
            padding: .2rem .6rem;
            border-radius: 99px;
            font-size: .68rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .b-indigo {
            background: var(--primary-light);
            color: var(--primary);
        }

        .b-green {
            background: var(--success-light);
            color: #065f46;
        }

        .b-amber {
            background: var(--amber-light);
            color: #92400e;
        }

        .b-red {
            background: var(--danger-light);
            color: #991b1b;
        }

        .b-sky {
            background: var(--sky-light);
            color: #075985;
        }

        .b-gray {
            background: #f3f4f6;
            color: #374151;
        }

        .b-purple {
            background: #ede9fe;
            color: #5b21b6;
        }

        .gp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 26px;
            padding: 0 6px;
            border-radius: .375rem;
            font-size: .78rem;
            font-weight: 700;
            font-family: var(--mono);
        }

        .gp-A {
            background: #d1fae5;
            color: #065f46;
        }

        .gp-B {
            background: #dbeafe;
            color: #1e40af;
        }

        .gp-C {
            background: #fef3c7;
            color: #92400e;
        }

        .gp-D {
            background: #fee2e2;
            color: #991b1b;
        }

        .f-label {
            font-size: .68rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            display: block;
            margin-bottom: .3rem;
        }

        .f-input {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: .5rem;
            padding: .575rem .875rem;
            font-size: .875rem;
            color: var(--ink-mid);
            width: 100%;
            transition: all .2s;
            font-family: var(--font);
        }

        .f-input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .08);
        }

        .f-input::placeholder {
            color: #94a3b8;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border-radius: .5rem;
            font-size: .8rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .2s;
            padding: .45rem 1rem;
            text-decoration: none;
            font-family: var(--font);
        }

        .btn-p {
            background: var(--primary);
            color: white;
        }

        .btn-p:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, .25);
            color: white;
        }

        .btn-o {
            background: white;
            color: var(--ink-mid);
            border: 1px solid var(--border);
        }

        .btn-o:hover {
            background: #f8fafc;
            color: var(--ink-mid);
        }

        .btn-sm {
            padding: .3rem .7rem;
            font-size: .75rem;
        }

        .btn-xs {
            padding: .2rem .5rem;
            font-size: .7rem;
        }

        .prog {
            background: #e2e8f0;
            height: 6px;
            border-radius: 99px;
            overflow: hidden;
        }

        .prog-fill {
            height: 100%;
            border-radius: 99px;
        }

        .empty {
            text-align: center;
            padding: 3.5rem 1rem;
        }

        .empty-icon {
            font-size: 2.5rem;
            margin-bottom: .75rem;
            opacity: .5;
        }

        .empty-text {
            font-size: .875rem;
            color: var(--muted);
        }

        .pager {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 1.125rem;
            border-top: 1px solid #f1f5f9;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .pager-info {
            font-size: .75rem;
            color: var(--muted);
        }

        .filter-bar {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: .875rem 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: .625rem;
            flex-wrap: wrap;
        }

        .filter-label {
            font-size: .67rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            flex-shrink: 0;
        }

        .filter-sep {
            width: 1px;
            height: 20px;
            background: var(--border);
            flex-shrink: 0;
        }

        .filter-spacer {
            flex: 1;
        }

        .f-input.sm {
            padding: .35rem .65rem;
            font-size: .8rem;
            width: auto;
        }

        .modal-bg {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            z-index: 900;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-bg.open {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: 1rem;
            padding: 1.75rem;
            width: 100%;
            max-width: 540px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .15);
        }

        .modal-lg {
            max-width: 760px;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #f1f5f9;
            border: none;
            cursor: pointer;
            color: var(--muted);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            transition: all .15s;
        }

        .modal-close:hover {
            background: var(--border);
            color: var(--ink);
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 1.25rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .info-item {
            padding: .875rem 1.375rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-item:nth-child(odd) {
            border-right: 1px solid #f1f5f9;
        }

        .info-item.full {
            grid-column: span 2;
            border-right: none;
        }

        .info-key {
            font-size: .65rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: .06em;
        }

        .info-val {
            font-size: .875rem;
            font-weight: 500;
            color: var(--ink);
            margin-top: .2rem;
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

            .logo-text,
            .nav-section-label,
            .nav-item-text,
            .nav-badge,
            .sidebar-footer-info,
            .sidebar-footer-cog {
                display: none !important;
            }

            .sidebar-logo {
                justify-content: center;
                padding: 0;
            }

            .sidebar-nav .nav-section {
                padding: 0 .375rem;
            }

            .nav-item {
                justify-content: center;
                padding: .55rem;
                margin: .1rem .375rem;
                gap: 0;
                border-left: none;
            }

            .nav-item.active {
                background: var(--primary-light);
                border-radius: .5rem;
            }

            .sidebar-footer {
                justify-content: center;
                padding: .625rem .375rem;
            }

            .topbar-sub {
                display: none;
            }

            .user-pill-name,
            .user-pill-chevron {
                display: none;
            }
        }

        /* Tooltips icon mode */
        @media (min-width: 641px) and (max-width: 960px) {
            .nav-item {
                position: relative;
            }

            .nav-item[data-tip]:hover::after {
                content: attr(data-tip);
                position: absolute;
                left: calc(100% + 10px);
                top: 50%;
                transform: translateY(-50%);
                background: var(--ink);
                color: white;
                font-size: .72rem;
                font-weight: 500;
                padding: .3rem .65rem;
                border-radius: 6px;
                white-space: nowrap;
                pointer-events: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
                z-index: 100;
            }

            .nav-item[data-tip]:hover::before {
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

        /* ── Mobile (≤640px) : drawer ── */
        @media (max-width: 640px) {
            :root {
                --sidebar-w: 256px;
            }

            /* Restore all text */
            .logo-text,
            .nav-section-label,
            .nav-item-text,
            .nav-badge,
            .sidebar-footer-info,
            .sidebar-footer-cog {
                display: unset !important;
            }

            .nav-badge {
                display: inline-block !important;
            }

            .sidebar-logo {
                justify-content: flex-start;
                padding: 0 1.25rem;
            }

            .sidebar-nav .nav-section {
                padding: 0;
            }

            .nav-item {
                justify-content: flex-start;
                padding: .5rem 1rem;
                margin: .1rem .5rem;
                gap: .625rem;
                border-left: 2px solid transparent;
            }

            .nav-item.active {
                border-left-color: var(--primary);
            }

            .sidebar-footer {
                justify-content: flex-start;
                padding: .875rem 1.125rem;
            }

            .sidebar-close {
                display: flex;
            }

            .s-overlay {
                display: block;
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

            /* Header full width */
            .topbar {
                left: 0;
            }

            .main-wrap {
                margin-left: 0;
            }

            .menu-btn {
                display: flex;
            }

            .topbar-sub {
                display: none;
            }

            .page {
                padding: 1rem;
            }
        }

        /* ── Extra small (≤380px) ── */
        @media (max-width: 380px) {
            .topbar {
                padding: 0 .875rem;
            }

            .page {
                padding: .75rem;
            }

            .kpi-grid {
                grid-template-columns: 1fr 1fr;
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
    <div class="s-overlay" id="s-overlay" onclick="closeSidebar()"></div>

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside class="sidebar" id="s-sidebar">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="logo-mark">
                @if (isset($institution) && $institution?->logo)
                    <img src="{{ asset('storage/' . $institution->logo) }}" alt=""
                        style="width:34px;height:34px;object-fit:cover;">
                @else
                    <svg style="width:16px;height:16px;" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                @endif
            </div>
            <div class="logo-text">
                <div class="logo-name">{{ Str::limit($institution->name ?? 'Mon École', 18) }}</div>
                <div class="logo-sub">Espace Étudiant</div>
            </div>
            <button class="sidebar-close" onclick="closeSidebar()" aria-label="Fermer">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Scrollable nav --}}
        <div class="sidebar-nav-wrap">
            <nav class="sidebar-nav">

                {{-- Principal --}}
                <div class="nav-section">
                    <div class="nav-section-label">Principal</div>
                    <a href="{{ route('student.dashboard') }}"
                        class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"
                        data-tip="Tableau de bord">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="nav-item-text">Tableau de bord</span>
                    </a>
                </div>

                {{-- Scolarité --}}
                <div class="nav-section">
                    <div class="nav-section-label">Scolarité</div>

                    <a href="{{ route('student.notes') }}"
                        class="nav-item {{ request()->routeIs('student.notes') ? 'active' : '' }}" data-tip="Mes Notes">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="nav-item-text">Mes Notes</span>
                    </a>

                    <a href="{{ route('student.library') }}"
                        class="nav-item {{ request()->routeIs('student.library') ? 'active' : '' }}"
                        data-tip="Bibliothèque">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5..." />
                        </svg>
                        <span class="nav-item-text">Bibliothèque</span>
                    </a>

                    <a href="{{ route('student.enseignants') }}"
                        class="nav-item {{ request()->routeIs('student.enseignants') ? 'active' : '' }}"
                        data-tip="Mes Enseignants">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="nav-item-text">Mes Enseignants</span>
                    </a>

                    <a href="{{ route('student.bulletins') }}"
                        class="nav-item {{ request()->routeIs('student.bulletins') ? 'active' : '' }}"
                        data-tip="Mes Bulletins">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="nav-item-text">Mes Bulletins</span>
                        @if (isset($stats) && ($stats['bulletins'] ?? 0) > 0)
                            <span class="nav-badge">{{ $stats['bulletins'] }}</span>
                        @endif
                    </a>

                    <a href="{{ route('student.disciplinaire') }}"
                        class="nav-item {{ request()->routeIs('student.disciplinaire') ? 'active' : '' }}"
                        data-tip="Sanctions disciplinaires">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="nav-item-text">Sanctions disciplinaires</span>
                    </a>

                     <a href="{{ route('student.ai-coach') }}"
        class="nav-item {{ request()->routeIs('student.ai-coach') ? 'active' : '' }}"
        data-tip="Coach IA">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        <span class="nav-item-text">Coach IA ✨</span>
    </a>

                    <a href="{{ route('student.classes') }}"
                        class="nav-item {{ request()->routeIs('student.classes') ? 'active' : '' }}"
                        data-tip="Classe & Matières">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span class="nav-item-text">Classe & Matières</span>
                    </a>
                </div>

                {{-- Mon compte --}}
                <div class="nav-section">
                    <div class="nav-section-label">Mon compte</div>
                    <a href="{{ route('student.profil') }}"
                        class="nav-item {{ request()->routeIs('student.profil') ? 'active' : '' }}"
                        data-tip="Mon Profil">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="nav-item-text">Mon Profil</span>
                    </a>
                    <a href="{{ route('student.planning') }}"
                        class="nav-item {{ request()->routeIs('student.planning') ? 'active' : '' }}"
                        data-tip="Emploi du temps">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10m-11 8h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="nav-item-text">Planning</span>
                    </a>
                </div>

            </nav>
        </div>

        {{-- Footer student card --}}
        @if (isset($apprenant))
            <div class="sidebar-footer">
                <img src="https://i.pravatar.cc/40?u={{ $apprenant->id }}" alt="Avatar">
                <div class="sidebar-footer-info">
                    <div class="sidebar-footer-name">{{ $apprenant->prenom }} {{ $apprenant->nom }}</div>
                    <div class="sidebar-footer-class">{{ $apprenant->classe->name ?? '' }}</div>
                </div>
                <a href="{{ route('student.profil') }}" class="sidebar-footer-cog" title="Paramètres">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>

            </div>
        @endif

    </aside>

    {{-- ══════════ TOPBAR ══════════ --}}
    <header class="topbar">
        <div class="topbar-left">
            {{-- Hamburger --}}
            <button class="menu-btn" onclick="openSidebar()" aria-label="Menu">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div style="min-width:0;">
                <div class="topbar-title">@yield('page-title', 'Tableau de bord')</div>
                <div class="topbar-sub">@yield('page-sub', now()->locale('fr')->isoFormat('dddd D MMMM YYYY'))</div>
            </div>
        </div>

        <div class="topbar-actions">
            {{-- Notif --}}
            <button class="icon-btn" title="Notifications" aria-label="Notifications">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="notif-dot"></span>
            </button>

            {{-- User dropdown --}}
            <div style="position:relative;" id="userWrap">
                <button class="user-pill" onclick="toggleDropdown(event)" aria-label="Menu utilisateur">
                    <img src="https://i.pravatar.cc/40?u={{ $apprenant->id ?? 1 }}" alt="">
                    <span class="user-pill-name">{{ Auth::user()->name ?? 'Étudiant' }}</span>
                    <svg class="user-pill-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div class="dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <p>{{ Auth::user()->name ?? '—' }}</p>
                        <span>{{ Auth::user()->email ?? '—' }}</span>
                    </div>
                    <a href="{{ route('student.profil') }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Mon profil
                    </a>
                    <a href="{{ route('student.notes') }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10" />
                        </svg>
                        Mes notes
                    </a>

                    <hr>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout">
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
    <div class="main-wrap">
        <main class="page">
            @if (session('success'))
                <div class="alert alert-s">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-e">
                    <span>⚠️</span>
                    <ul style="margin:.25rem 0 0 1rem;padding:0;">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('s-sidebar');
        const overlay = document.getElementById('s-overlay');
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

        function toggleDropdown(e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        }

        document.addEventListener('click', () => dropdown.classList.remove('open'));

        // Close sidebar on nav link click (mobile)
        sidebar.querySelectorAll('.nav-item').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 640) closeSidebar();
            });
        });

        // Escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeSidebar();
                dropdown.classList.remove('open');
            }
        });

        // Restore scroll on resize
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
