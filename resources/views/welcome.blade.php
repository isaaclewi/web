<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>SyntriForg Edu — Plateforme de Gestion Scolaire</title>
    <link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#7C3AED">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SyntriForg">
    <link rel="apple-touch-icon" href="/medias/logo-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,700;1,9..144,400;1,9..144,700&display=swap"
        rel="stylesheet">

    <style>
        /* ═══ VARIABLES ═══ */
        :root {
            --p: #7C3AED;
            --p-d: #5B21B6;
            --p-soft: #EDE9FE;
            --b: #2563EB;
            --b-soft: #DBEAFE;
            --ink: #0A0B14;
            --ink2: #1E1B4B;
            --muted: #64748B;
            --border: #E2E8F0;
            --white: #FFFFFF;
            --off: #F8FAFD;
            --green: #10B981;
            --amber: #F59E0B;
            --red: #EF4444;
            --grad: linear-gradient(135deg, #7C3AED 0%, #2563EB 100%);
            --grad-r: linear-gradient(135deg, #2563EB 0%, #7C3AED 100%);
            --sh-sm: 0 1px 4px rgba(10, 11, 20, .06);
            --sh-md: 0 4px 24px rgba(10, 11, 20, .09);
            --sh-lg: 0 16px 56px rgba(124, 58, 237, .18);
            --sh-xl: 0 32px 80px rgba(10, 11, 20, .18);
            --r: 14px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background: var(--white);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            cursor: none;
            -webkit-overflow-scrolling: touch;
        }

        img {
            display: block;
            max-width: 100%;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        ul {
            list-style: none;
        }

        /* ═══ CURSOR ═══ */
        #cur {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            pointer-events: none;
        }

        #cd {
            width: 10px;
            height: 10px;
            background: var(--p);
            border-radius: 50%;
            position: absolute;
            transform: translate(-50%, -50%);
            transition: transform .12s;
        }

        #cr {
            width: 40px;
            height: 40px;
            border: 1.5px solid rgba(124, 58, 237, .45);
            border-radius: 50%;
            position: absolute;
            transform: translate(-50%, -50%);
            transition: width .25s, height .25s, border-color .25s;
        }

        body.hov #cr {
            width: 65px;
            height: 65px;
            border-color: var(--p);
        }

        body.hov #cd {
            transform: translate(-50%, -50%) scale(0);
        }

        @media (hover:none) and (pointer:coarse) {
            body {
                cursor: auto
            }

            #cur {
                display: none
            }

            * {
                cursor: auto !important
            }
        }

        /* ═══ NOISE OVERLAY ═══ */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 9998;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='256' height='256'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='256' height='256' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
            opacity: .016;
        }

        /* ═══ REVEAL ═══ */
        .rv {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .75s ease, transform .75s ease;
        }

        .rv.in {
            opacity: 1;
            transform: translateY(0);
        }

        .d1 {
            transition-delay: .1s
        }

        .d2 {
            transition-delay: .2s
        }

        .d3 {
            transition-delay: .3s
        }

        .d4 {
            transition-delay: .4s
        }

        .d5 {
            transition-delay: .5s
        }

        .d6 {
            transition-delay: .6s
        }

        /* ═══ TOPBAR ═══ */
        .topbar {
            background: var(--ink);
            color: rgba(255, 255, 255, .65);
            font-size: .72rem;
            font-weight: 500;
            letter-spacing: .03em;
            text-align: center;
            padding: .55rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
        }

        .topbar-badge {
            background: var(--grad);
            color: #fff;
            font-size: .6rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .18rem .6rem;
            border-radius: 100px;
            flex-shrink: 0;
        }

        /* ═══ HEADER ═══ */
        #header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 0 max(2.5rem, env(safe-area-inset-right)) 0 max(2.5rem, env(safe-area-inset-left));
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background .4s, border .4s, height .4s;
            transform: translateZ(0);
        }

        #header.solid {
            height: 56px;
            background: rgba(10, 11, 20, .92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            box-shadow: var(--sh-sm);
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-shrink: 0;
        }

        .logo-mark {
            width: 32px;
            height: 32px;
            background: var(--grad);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            overflow: hidden;
        }

        .logo-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .logo-text {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: -.02em;
            color: #fff;
        }

        .logo-text span {
            color: #A78BFA;
        }

        #nav-menu {
            display: flex;
            align-items: center;
            gap: .05rem;
        }

        #nav-menu li a {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .04em;
            color: rgba(255, 255, 255, .55);
            padding: .45rem .8rem;
            border-radius: 8px;
            transition: color .2s, background .2s;
        }

        #nav-menu li a:hover {
            color: #fff;
            background: rgba(255, 255, 255, .06);
        }

        .nav-cta {
            background: var(--grad) !important;
            color: #fff !important;
            border-radius: 100px !important;
            padding: .45rem 1.3rem !important;
            box-shadow: 0 4px 16px rgba(124, 58, 237, .35) !important;
            transition: transform .15s, box-shadow .2s !important;
        }

        .nav-cta:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 24px rgba(124, 58, 237, .5) !important;
        }

        .burger {
            display: none;
            flex-direction: column;
            gap: 5px;
            padding: .5rem;
            border: none;
            background: none;
            cursor: pointer;
        }

        .burger span {
            width: 22px;
            height: 1.5px;
            background: #fff;
            border-radius: 2px;
            transition: .3s;
        }

        .burger.active span:nth-child(1) {
            transform: translateY(6.5px) rotate(45deg);
        }

        .burger.active span:nth-child(2) {
            opacity: 0;
        }

        .burger.active span:nth-child(3) {
            transform: translateY(-6.5px) rotate(-45deg);
        }

        /* ═══ HERO ═══ */
        #hero {
            min-height: 100vh;
            min-height: 100dvh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
            overflow: hidden;
        }

        /* Left — dark panel */
        .hero-left {
            background: #0A0B14;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 9rem 5rem 5rem;
            z-index: 2;
        }

        .hero-left::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(124, 58, 237, .07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124, 58, 237, .07) 1px, transparent 1px);
            background-size: 52px 52px;
            mask-image: radial-gradient(ellipse at 40% 60%, black 20%, transparent 75%);
            -webkit-mask-image: radial-gradient(ellipse at 40% 60%, black 20%, transparent 75%);
        }

        .hero-left::after {
            content: '';
            position: absolute;
            top: 5%;
            left: -25%;
            width: 90%;
            height: 90%;
            background: radial-gradient(ellipse, rgba(124, 58, 237, .16) 0%, transparent 65%);
            pointer-events: none;
        }

        .hero-left-line {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, transparent, #7C3AED 35%, #2563EB 65%, transparent);
            z-index: 3;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            background: rgba(124, 58, 237, .12);
            border: 1px solid rgba(124, 58, 237, .28);
            border-radius: 100px;
            padding: .3rem .9rem .3rem .45rem;
            margin-bottom: 2rem;
            width: fit-content;
            opacity: 0;
            animation: fu .7s .3s forwards;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7C3AED, #2563EB);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(124, 58, 237, .4)
            }

            50% {
                box-shadow: 0 0 0 7px rgba(124, 58, 237, 0)
            }
        }

        .hero-badge span {
            font-size: .67rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .65);
        }

        h1.ht {
            font-family: 'Fraunces', serif;
            font-size: clamp(2.6rem, 4.2vw, 5rem);
            line-height: .93;
            letter-spacing: -.02em;
            color: #fff;
            opacity: 0;
            animation: fu .9s .5s forwards;
        }

        h1.ht em {
            font-style: italic;
            color: rgba(255, 255, 255, .45);
        }

        h1.ht .grad {
            background: linear-gradient(135deg, #A78BFA 0%, #60A5FA 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sep {
            width: 48px;
            height: 1.5px;
            background: linear-gradient(90deg, #7C3AED, #2563EB);
            margin: 2rem 0;
            opacity: 0;
            animation: fu .6s .75s forwards;
        }

        .hero-desc {
            font-size: .9rem;
            font-weight: 400;
            line-height: 1.85;
            color: rgba(255, 255, 255, .42);
            max-width: 420px;
            opacity: 0;
            animation: fu .8s .7s forwards;
        }

        .hero-cta {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            opacity: 0;
            animation: fu .8s .9s forwards;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .74rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .8rem 1.7rem;
            border-radius: 100px;
            border: none;
            transition: .25s;
            -webkit-tap-highlight-color: transparent;
            cursor: pointer;
        }

        .btn-grad {
            background: var(--grad);
            color: #fff;
            box-shadow: 0 6px 20px rgba(124, 58, 237, .4);
        }

        .btn-grad:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(124, 58, 237, .5);
        }

        .btn-out {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, .18);
            color: rgba(255, 255, 255, .65);
        }

        .btn-out:hover {
            border-color: rgba(255, 255, 255, .45);
            color: #fff;
            transform: translateY(-2px);
        }

        .hero-trust {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            margin-top: 2.5rem;
            opacity: 0;
            animation: fu .8s 1.1s forwards;
        }

        .trust-avatars {
            display: flex;
        }

        .trust-av {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .12);
            overflow: hidden;
            margin-left: -9px;
            background: rgba(124, 58, 237, .2);
        }

        .trust-av:first-child {
            margin-left: 0;
        }

        .trust-av img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .trust-count {
            font-size: .78rem;
            font-weight: 700;
            color: #fff;
            margin-left: .5rem;
        }

        .trust-label {
            font-size: .68rem;
            color: rgba(255, 255, 255, .28);
            letter-spacing: .04em;
        }

        /* Right — image + overlays */
        .hero-right {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .hero-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
            filter: brightness(.82) saturate(.9);
        }

        .hero-right::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            background: linear-gradient(90deg, #0A0B14 0%, rgba(10, 11, 20, .25) 40%, transparent 70%);
        }

        .hero-right::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 35%;
            z-index: 2;
            pointer-events: none;
            background: linear-gradient(to bottom, transparent, rgba(10, 11, 20, .5));
        }

        /* App window floating */
        .hero-app-wrap {
            position: absolute;
            bottom: 2rem;
            right: 1.5rem;
            z-index: 5;
            opacity: 0;
            animation: fu .9s 1s forwards;
            width: 260px;
        }

        .hero-app-win {
            background: rgba(10, 11, 20, .82);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .35);
        }

        .app-win-bar {
            background: rgba(255, 255, 255, .04);
            padding: .6rem .9rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
        }

        .awb-dots {
            display: flex;
            gap: 4px;
        }

        .awb-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .awb-dots span:nth-child(1) {
            background: #EF4444;
        }

        .awb-dots span:nth-child(2) {
            background: #F59E0B;
        }

        .awb-dots span:nth-child(3) {
            background: #10B981;
        }

        .awb-title {
            font-size: .6rem;
            color: rgba(255, 255, 255, .35);
            font-family: monospace;
            margin-left: .3rem;
        }

        .app-win-body {
            padding: .9rem;
            display: flex;
            flex-direction: column;
            gap: .55rem;
        }

        .aw-kpi-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: .45rem;
        }

        .aw-kpi {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .07);
            border-radius: 9px;
            padding: .55rem .5rem;
        }

        .aw-kpi-lbl {
            font-size: .52rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .3);
            margin-bottom: .2rem;
        }

        .aw-kpi-val {
            font-family: 'Fraunces', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            line-height: 1;
        }

        .aw-kpi-delta {
            font-size: .55rem;
            font-weight: 600;
            color: var(--green);
            margin-top: .15rem;
        }

        .aw-chart-wrap {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .07);
            border-radius: 9px;
            padding: .65rem .7rem;
        }

        .aw-chart-title {
            font-size: .58rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .4);
            margin-bottom: .5rem;
        }

        .aw-bars {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 38px;
        }

        .aw-bar {
            flex: 1;
            border-radius: 3px 3px 0 0;
            background: linear-gradient(to top, #7C3AED, #A78BFA);
            opacity: .6;
        }

        .aw-bar.hi {
            opacity: 1;
        }

        .aw-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .07);
            border-radius: 9px;
            padding: .55rem .7rem;
        }

        .aw-row-left {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .aw-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse 2s infinite;
        }

        .aw-row-text {
            font-size: .62rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .55);
        }

        .aw-row-badge {
            font-size: .55rem;
            font-weight: 700;
            background: rgba(16, 185, 129, .2);
            color: var(--green);
            padding: .12rem .45rem;
            border-radius: 100px;
        }

        /* Floating stat cards */
        .hero-float-stack {
            position: absolute;
            top: 4rem;
            right: 1.5rem;
            z-index: 5;
            display: flex;
            flex-direction: column;
            gap: .65rem;
            opacity: 0;
            animation: fu .9s .8s forwards;
        }

        .hero-float {
            background: rgba(10, 11, 20, .78);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 13px;
            padding: .65rem .95rem;
            display: flex;
            align-items: center;
            gap: .7rem;
            min-width: 175px;
        }

        .hf-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
        }

        .hf-p {
            background: rgba(124, 58, 237, .2);
        }

        .hf-g {
            background: rgba(16, 185, 129, .2);
        }

        .hf-b {
            background: rgba(37, 99, 235, .2);
        }

        .hf-label {
            font-size: .56rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .3);
        }

        .hf-val {
            font-size: .8rem;
            font-weight: 700;
            color: #fff;
        }

        /* img label */
        .hero-img-label {
            position: absolute;
            top: 5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            text-align: center;
            opacity: 0;
            animation: fu .8s .8s forwards;
        }

        .hero-img-label-num {
            font-family: 'Fraunces', serif;
            font-size: 3.5rem;
            line-height: 1;
            background: linear-gradient(135deg, rgba(124, 58, 237, .45), rgba(37, 99, 235, .45));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-img-label-text {
            font-size: .6rem;
            font-weight: 600;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .2);
        }

        @keyframes fu {
            from {
                opacity: 0;
                transform: translateY(22px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* ═══ TICKER ═══ */
        .ticker {
            overflow: hidden;
            background: #0D0E1A;
            padding: .65rem 0;
            border-top: 1px solid rgba(255, 255, 255, .04);
        }

        .ticker-inner {
            display: flex;
            animation: tick 26s linear infinite;
            white-space: nowrap;
        }

        .ticker:hover .ticker-inner {
            animation-play-state: paused;
        }

        @keyframes tick {
            from {
                transform: translateX(0)
            }

            to {
                transform: translateX(-50%)
            }
        }

        .tick-item {
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .32);
            padding: 0 2rem;
            display: inline-flex;
            align-items: center;
            gap: 1.5rem;
            flex-shrink: 0;
        }

        .tick-dot {
            font-size: .45rem;
            background: var(--grad);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ═══ STATS BAND ═══ */
        .stats-band {
            background: #0D0E1A;
            border-top: 1px solid rgba(255, 255, 255, .04);
            position: relative;
            overflow: hidden;
        }

        .stats-band::before {
            content: '';
            position: absolute;
            top: -60%;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(124, 58, 237, .07) 0%, transparent 65%);
            pointer-events: none;
        }

        .stats-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 4rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .stat-item {
            padding: 3rem 2rem;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: .4rem;
            transition: background .3s;
        }

        .stat-item+.stat-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, .08), transparent);
        }

        .stat-item:hover {
            background: rgba(124, 58, 237, .04);
        }

        .stat-accent {
            width: 28px;
            height: 2px;
            background: var(--grad);
            border-radius: 2px;
            margin-bottom: .4rem;
        }

        .stat-num {
            font-family: 'Fraunces', serif;
            font-size: 3.2rem;
            line-height: 1;
            color: #fff;
            letter-spacing: -.02em;
        }

        .stat-num.g {
            background: linear-gradient(135deg, #A78BFA, #60A5FA);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-new {
            font-size: .58rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            background: rgba(124, 58, 237, .2);
            color: #A78BFA;
            padding: .15rem .55rem;
            border-radius: 100px;
            width: fit-content;
        }

        .stat-label {
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .28);
        }

        .stat-sub {
            font-size: .72rem;
            color: rgba(255, 255, 255, .15);
            font-weight: 300;
        }

        /* ═══ SECTIONS ═══ */
        .sec {
            padding: 7rem 4rem;
        }

        .sec-alt {
            background: var(--off);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .stag {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .stag.p {
            color: var(--p);
        }

        .stag.p::before {
            content: '';
            width: 20px;
            height: 1.5px;
            background: var(--p);
        }

        .stag.b {
            color: var(--b);
        }

        .stag.b::before {
            content: '';
            width: 20px;
            height: 1.5px;
            background: var(--b);
        }

        h2.st {
            font-family: 'Fraunces', serif;
            font-size: clamp(2rem, 3.2vw, 3.4rem);
            font-weight: 700;
            line-height: 1.05;
            letter-spacing: -.02em;
        }

        h2.st em {
            font-style: italic;
        }

        h2.st .grad {
            background: var(--grad);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sub {
            font-size: .9rem;
            font-weight: 400;
            color: var(--muted);
            line-height: 1.8;
            margin-top: .8rem;
            max-width: 560px;
        }

        /* ═══ FEATURE CARDS ═══ */
        .feat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .fc {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            transition: .3s;
            position: relative;
        }

        .fc::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--grad);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .35s;
            z-index: 1;
        }

        .fc:hover {
            border-color: transparent;
            box-shadow: var(--sh-lg);
            transform: translateY(-6px);
        }

        .fc:hover::before {
            transform: scaleX(1);
        }

        .fc-img {
            height: 190px;
            overflow: hidden;
            position: relative;
        }

        .fc-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .6s;
        }

        .fc:hover .fc-img img {
            transform: scale(1.06);
        }

        .fc-img::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(10, 11, 20, .12), transparent 55%);
        }

        .fc-body {
            padding: 1.4rem;
        }

        .fc-tag {
            display: inline-block;
            font-size: .6rem;
            font-weight: 700;
            letter-spacing: .15em;
            text-transform: uppercase;
            background: var(--p-soft);
            color: var(--p);
            padding: .22rem .65rem;
            border-radius: 100px;
            margin-bottom: .7rem;
        }

        .fc-body h3 {
            font-size: .92rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: .45rem;
        }

        .fc-body p {
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.65;
        }

        .fc-link {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--p);
            margin-top: .9rem;
            transition: gap .2s;
        }

        .fc:hover .fc-link {
            gap: .7rem;
        }

        /* ═══ PROCESS ═══ */
        #process {
            background: var(--ink);
            position: relative;
            overflow: hidden;
            padding: 7rem 4rem;
        }

        #process::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 60vw;
            height: 140%;
            background: radial-gradient(ellipse, rgba(124, 58, 237, .07) 0%, transparent 60%);
            pointer-events: none;
        }

        .proc-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .proc-header h2.st {
            color: #fff;
        }

        .proc-header .sub {
            color: rgba(255, 255, 255, .38);
            margin: 0 auto;
        }

        .proc-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }

        .proc-grid::before {
            content: '';
            position: absolute;
            top: 35px;
            left: 12%;
            right: 12%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .08) 20%, rgba(255, 255, 255, .08) 80%, transparent);
        }

        .ps {
            text-align: center;
            position: relative;
        }

        .ps-dot {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 2;
            transition: .35s;
            border: 2px solid rgba(255, 255, 255, .08);
            background: rgba(255, 255, 255, .04);
        }

        .ps:hover .ps-dot {
            background: var(--grad);
            border-color: transparent;
            transform: scale(1.1);
            box-shadow: 0 12px 32px rgba(124, 58, 237, .4);
        }

        .ps-num {
            font-family: 'Fraunces', serif;
            font-size: .88rem;
            font-style: italic;
            background: var(--grad);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: .5rem;
        }

        .ps h4 {
            font-size: .9rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .5rem;
        }

        .ps p {
            font-size: .78rem;
            color: rgba(255, 255, 255, .38);
            line-height: 1.65;
        }

        /* ═══ SCHOOLS ═══ */
        #schools {
            background: var(--off);
        }

        .schools-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .sc {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: .3s;
        }

        .sc:hover {
            box-shadow: var(--sh-lg);
            transform: translateY(-6px);
        }

        .sc-img {
            height: 170px;
            overflow: hidden;
            position: relative;
        }

        .sc-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .5s;
            filter: brightness(.85) saturate(.9);
        }

        .sc:hover .sc-img img {
            transform: scale(1.06);
            filter: brightness(.9) saturate(1.1);
        }

        .sc-logo {
            position: absolute;
            bottom: -24px;
            left: 50%;
            transform: translateX(-50%);
            width: 48px;
            height: 48px;
            background: #fff;
            border-radius: 13px;
            box-shadow: var(--sh-md);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sc-logo span {
            font-size: .85rem;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sc-body {
            padding: 2rem 1.4rem 1.4rem;
            text-align: center;
        }

        .sc-name {
            font-size: .9rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: .3rem;
        }

        .sc-type {
            font-size: .72rem;
            color: var(--muted);
            margin-bottom: .75rem;
        }

        .sc-pill {
            display: inline-block;
            font-size: .6rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            background: var(--p-soft);
            color: var(--p);
            padding: .22rem .75rem;
            border-radius: 100px;
        }

        /* ═══ ENRICH / PLATFORM ═══ */
        #enrich {
            background: var(--white);
        }

        .enrich-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .ec {
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            transition: .3s;
            position: relative;
            overflow: hidden;
        }

        .ec::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--grad);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .35s;
        }

        .ec:hover {
            box-shadow: var(--sh-lg);
            transform: translateY(-5px);
            border-color: transparent;
        }

        .ec:hover::before {
            transform: scaleX(1);
        }

        .ec-icon {
            font-size: 2.2rem;
            margin-bottom: 1.1rem;
            display: block;
        }

        .ec-num {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-family: 'Fraunces', serif;
            font-size: 2rem;
            color: rgba(124, 58, 237, .06);
        }

        .ec h4 {
            font-size: .92rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: .55rem;
        }

        .ec p {
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.7;
        }

        /* ═══ CTA ═══ */
        #cta {
            padding: 7rem 4rem;
            background: var(--off);
        }

        .cta-box {
            background: var(--grad);
            border-radius: 28px;
            padding: 5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            max-width: 1200px;
            margin: 0 auto;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -30%;
            left: -10%;
            width: 60%;
            height: 160%;
            background: radial-gradient(ellipse, rgba(255, 255, 255, .1) 0%, transparent 60%);
            pointer-events: none;
        }

        .cta-box::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -5%;
            width: 50%;
            height: 130%;
            background: radial-gradient(ellipse, rgba(255, 255, 255, .07) 0%, transparent 60%);
            pointer-events: none;
        }

        .cta-box h2.st {
            color: #fff;
            font-size: clamp(2.2rem, 4vw, 4rem);
        }

        .cta-box p {
            font-size: .95rem;
            color: rgba(255, 255, 255, .62);
            max-width: 500px;
            margin: 1rem auto 2.5rem;
            line-height: 1.75;
        }

        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .btn-white {
            background: #fff;
            color: var(--p);
            font-size: .74rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .85rem 1.8rem;
            border-radius: 100px;
            border: none;
            transition: .25s;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-white:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 40px rgba(0, 0, 0, .2);
        }

        .btn-out-white {
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, .4);
            color: #fff;
            font-size: .74rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .85rem 1.8rem;
            border-radius: 100px;
            transition: .25s;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-out-white:hover {
            border-color: rgba(255, 255, 255, .85);
            background: rgba(255, 255, 255, .1);
        }

        .cta-perks {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 2rem;
            position: relative;
            z-index: 2;
        }

        .cta-perk {
            font-size: .68rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .52);
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .cta-perk::before {
            content: '✓';
            color: rgba(255, 255, 255, .85);
        }

        /* ═══ FOOTER ═══ */
        footer {
            background: var(--ink);
            padding: 5rem 4rem max(2.5rem, env(safe-area-inset-bottom)) 4rem;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 4rem;
            padding-bottom: 4rem;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            max-width: 1200px;
            margin: 0 auto;
        }

        .fb-logo {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: .8rem;
        }

        .fb-logo-mark {
            width: 32px;
            height: 32px;
            background: var(--grad);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 1rem;
        }

        .fb-logo-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fb-logo-text {
            font-size: 1.1rem;
            font-weight: 800;
            color: #fff;
        }

        .fb-logo-text span {
            color: #A78BFA;
        }

        .fb-desc {
            font-size: .82rem;
            color: rgba(255, 255, 255, .28);
            line-height: 1.75;
            max-width: 250px;
            margin-bottom: 1.5rem;
        }

        .footer-col h4 {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .22);
            margin-bottom: 1.2rem;
        }

        .footer-col ul {
            display: flex;
            flex-direction: column;
            gap: .65rem;
        }

        .footer-col a {
            font-size: .82rem;
            font-weight: 300;
            color: rgba(255, 255, 255, .38);
            transition: color .2s;
        }

        .footer-col a:hover {
            color: #fff;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 2rem;
            font-size: .75rem;
            color: rgba(255, 255, 255, .2);
            max-width: 1200px;
            margin: 0 auto;
        }

        .socials {
            display: flex;
            gap: .7rem;
        }

        .soc {
            width: 34px;
            height: 34px;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .78rem;
            color: rgba(255, 255, 255, .3);
            transition: .25s;
        }

        .soc:hover {
            border-color: var(--p);
            color: #fff;
            background: var(--p);
        }

        /* ═══ PWA FAB ═══ */
        #pwa-fab {
            position: fixed;
            bottom: max(1.5rem, env(safe-area-inset-bottom));
            right: 1.5rem;
            z-index: 9990;
            display: none;
            align-items: center;
            gap: .6rem;
            background: var(--grad);
            color: #fff;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .75rem 1.4rem;
            border-radius: 100px;
            border: none;
            box-shadow: 0 8px 28px rgba(124, 58, 237, .45);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: transform .2s, box-shadow .2s;
        }

        #pwa-fab.visible {
            display: flex;
        }

        #pwa-fab:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 36px rgba(124, 58, 237, .55);
        }

        #pwa-fab:active {
            transform: scale(.97);
        }

        /* ═══ PWA MODAL ═══ */
        #pwa-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9995;
            background: rgba(0, 0, 0, .55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            align-items: flex-end;
            justify-content: center;
            padding: 1rem;
        }

        #pwa-modal-overlay.open {
            display: flex;
        }

        #pwa-modal {
            background: #fff;
            border-radius: 24px 24px 0 0;
            padding: 2rem 1.8rem max(2rem, env(safe-area-inset-bottom));
            width: 100%;
            max-width: 480px;
        }

        #pwa-modal h3 {
            font-size: 1rem;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: .75rem;
        }

        #pwa-modal p {
            font-size: .85rem;
            color: var(--muted);
            line-height: 1.7;
        }

        #pwa-modal .chip {
            display: inline-block;
            background: var(--p-soft);
            color: var(--p);
            border-radius: 6px;
            padding: .05rem .45rem;
            font-weight: 700;
            font-size: .8rem;
        }

        #pwa-modal-close {
            display: block;
            width: 100%;
            margin-top: 1.5rem;
            background: var(--grad);
            color: #fff;
            font-weight: 700;
            font-size: .78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .85rem;
            border-radius: 100px;
            border: none;
            cursor: pointer;
        }

        /* ═══ RESPONSIVE ═══ */
        @media (max-width:1100px) {
            #header {
                padding: 0 max(1.75rem, env(safe-area-inset-right)) 0 max(1.75rem, env(safe-area-inset-left));
            }

            .sec,
            #process,
            #cta,
            footer {
                padding-left: 2rem;
                padding-right: 2rem;
            }

            .stats-inner {
                padding: 0 2rem;
                grid-template-columns: 1fr 1fr;
            }

            #hero {
                grid-template-columns: 1fr;
            }

            .hero-right {
                display: none;
            }

            .hero-left {
                padding: 7rem 2rem 4rem;
            }

            .feat-grid,
            .schools-grid,
            .enrich-grid {
                grid-template-columns: 1fr 1fr;
            }

            .proc-grid {
                grid-template-columns: 1fr 1fr;
            }

            .proc-grid::before {
                display: none;
            }

            .footer-top {
                grid-template-columns: 1fr 1fr;
                gap: 2.5rem;
            }

            .cta-box {
                padding: 3.5rem 2rem;
            }
        }

        @media (max-width:768px) {
            #nav-menu {
                display: none;
                flex-direction: column;
                align-items: flex-start;
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                background: #0A0B14;
                padding: 1.5rem 1.75rem max(2rem, env(safe-area-inset-bottom));
                border-bottom: 1px solid rgba(255, 255, 255, .08);
                box-shadow: var(--sh-lg);
                gap: .2rem;
            }

            #nav-menu.active {
                display: flex;
            }

            #nav-menu li {
                width: 100%;
            }

            #nav-menu li a {
                display: block;
                padding: .65rem .5rem;
                color: rgba(255, 255, 255, .6);
            }

            .burger {
                display: flex;
            }

            .feat-grid,
            .enrich-grid {
                grid-template-columns: 1fr;
            }

            .schools-grid {
                grid-template-columns: 1fr 1fr;
            }

            .proc-grid {
                grid-template-columns: 1fr;
            }

            .footer-top {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: .5rem;
                text-align: center;
            }

            h1.ht {
                font-size: clamp(1.9rem, 7vw, 2.8rem);
            }

            .stat-num {
                font-size: 2.4rem;
            }

            .stat-item {
                padding: 2rem 1.2rem;
            }

            .hero-left {
                padding: 6rem 1.5rem 3rem;
            }
        }

        @media (max-width:390px) {
            .hero-cta {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .schools-grid {
                grid-template-columns: 1fr;
            }

            .cta-perks {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
        }

        /* ═══════════════════════════════════════════════════
   CORRECTIF HERO RESPONSIVE — SyntriForg Edu
   Remplace / surcharge la section @media du fichier principal
   Colle ce bloc à la FIN du <style> existant (avant
    </style>)
    ═══════════════════════════════════════════════════ */

    /* ── TABLETTE (≤ 1100px) : Hero garde ses 2 colonnes ── */
    @media (max-width:1100px){
    #hero {
    grid-template-columns: 1fr 1fr; /* 2 colonnes conservées */
    min-height: 100vh;
    }
    .hero-right {
    display: block !important; /* image toujours visible */
    }
    .hero-left {
    padding: 7rem 2rem 4rem;
    }
    /* Réduire les overlays flottants pour éviter le débordement */
    .hero-float-stack {
    top: 5rem;
    right: .75rem;
    }
    .hero-float {
    min-width: 140px;
    padding: .5rem .75rem;
    }
    .hero-app-wrap {
    width: 200px;
    right: .75rem;
    bottom: 1.5rem;
    }
    }

    /* ── MOBILE (≤ 768px) : Hero 2 colonnes, textes adaptés ── */
    @media (max-width:768px){
    #hero {
    grid-template-columns: 1fr 1fr; /* 2 colonnes maintenues */
    min-height: 100dvh;
    }
    .hero-right {
    display: block !important; /* image visible */
    }
    h1.ht {
    font-size: clamp(1.1rem, 4.5vw, 2rem);
    }
    .hero-left {
    padding: 5.5rem 1rem 2.5rem;
    }
    .hero-desc {
    font-size: .75rem;
    max-width: 100%;
    }
    .hero-badge span {
    font-size: .58rem;
    }
    .btn {
    padding: .65rem 1.1rem;
    font-size: .65rem;
    }
    .hero-trust {
    margin-top: 1.5rem;
    }
    .trust-count {
    font-size: .68rem;
    }
    .hero-sep {
    margin: 1.2rem 0;
    }
    .hero-cta {
    gap: .5rem;
    margin-top: 1.2rem;
    }
    /* Cards flottantes : cachées sur petit écran pour ne pas surcharger */
    .hero-float-stack {
    display: none;
    }
    .hero-app-wrap {
    width: 160px;
    right: .4rem;
    bottom: 1rem;
    }
    .aw-kpi-val {
    font-size: .9rem;
    }
    }

    /* ── TRÈS PETIT MOBILE (≤ 480px) : encore plus compact ── */
    @media (max-width:480px){
    #hero {
    grid-template-columns: 1fr 1fr;
    }
    .hero-right {
    display: block !important;
    }
    h1.ht {
    font-size: clamp(.95rem, 5vw, 1.6rem);
    }
    .hero-left {
    padding: 5rem .85rem 2rem;
    }
    /* Masquer la description sur très petit écran pour ne pas écraser */
    .hero-desc {
    display: none;
    }
    .hero-app-wrap {
    width: 135px;
    }
    .hero-img-label-num {
    font-size: 2.2rem;
    }
    }
    </style>
</head>

<body>

    <div id="cur">
        <div id="cd"></div>
        <div id="cr"></div>
    </div>

    <!-- TOPBAR -->
    <div class="topbar">
        <span class="topbar-badge">Lancement 2026</span>
        SyntriForg Edu est en phase de lancement — Rejoignez nos premiers partenaires et bénéficiez de 3 mois offerts
    </div>

    <!-- HEADER -->
    <header id="header">
        <div class="logo-wrap">
            <div class="logo-mark"><img src="/medias/Syntriforg[1].png" alt="SyntriForge"
                    onerror="this.style.display='none';this.parentElement.innerHTML='🎓'"></div>
            <div class="logo-text">SyntriForg <span>Edu</span></div>
        </div>
        <button class="burger" id="burger" aria-label="Menu"><span></span><span></span><span></span></button>
        <ul id="nav-menu">
            <li><a href="#fonctionnalites">Fonctionnalités</a></li>
            <li><a href="#schools">Partenaires</a></li>
            <li><a href="#process">Processus</a></li>
            <li><a href="#enrich">Plateforme</a></li>
            <li><a href="#cta">Contact</a></li>
            <li><a href="/about">À propos</a></li>
            <li><a href="/mission">Notre mission</a></li>
            <li><a href="/institutions">Établissements</a></li>
            <li><a href="/login" class="nav-cta">Connexion</a></li>
        </ul>
    </header>

    <!-- ═══ HERO ═══ -->
    <section id="hero">
        <!-- LEFT DARK PANEL -->
        <div class="hero-left">
            <div class="hero-left-line"></div>

            <div class="hero-badge">
                <div class="badge-dot"></div>
                <span>Nouvelle plateforme académique</span>
            </div>

            <h1 class="ht">
                Transformez<br>
                votre <span class="grad">écosystème</span><br>
                <em>éducatif</em>
            </h1>

            <div class="hero-sep"></div>

            <p class="hero-desc">Une solution complète pour gérer vos établissements scolaires, faciliter la
                collaboration et améliorer les performances académiques à grande échelle.</p>

            <div class="hero-cta">
                <a href="https://syntriforg.ct.ws/?i=1" class="btn btn-grad">Demander une démo →</a>
                <a href="#fonctionnalites" class="btn btn-out">Voir les fonctionnalités</a>
            </div>

            <div class="hero-trust">
                <div class="trust-avatars">
                    <div class="trust-av"><img src="https://i.pravatar.cc/100?img=1" alt=""></div>
                    <div class="trust-av"><img src="https://i.pravatar.cc/100?img=5" alt=""></div>
                    <div class="trust-av"><img src="https://i.pravatar.cc/100?img=9" alt=""></div>
                    <div class="trust-av"><img src="https://i.pravatar.cc/100?img=12" alt=""></div>
                </div>
                <div>
                    <div class="trust-count">250+ établissements visés</div>
                    <div class="trust-label">comme objectif de croissance</div>
                </div>
            </div>
        </div>

        <!-- RIGHT IMAGE PANEL -->
        <div class="hero-right">
            <img src="/medias/télécharger (8).jfif" alt="Étudiante SyntriForg Edu">

            <!-- Label image -->
            <div class="hero-img-label">
                <div class="hero-img-label-num">50K</div>
                <div class="hero-img-label-text">étudiants à atteindre</div>
            </div>

            <!-- Floating stat cards top right -->
            <div class="hero-float-stack">
                <div class="hero-float">
                    <div class="hf-icon hf-p">📊</div>
                    <div>
                        <div class="hf-label">Analytics</div>
                        <div class="hf-val">Temps réel</div>
                    </div>
                </div>
                <div class="hero-float">
                    <div class="hf-icon hf-g">✅</div>
                    <div>
                        <div class="hf-label">Satisfaction</div>
                        <div class="hf-val">95%</div>
                    </div>
                </div>
                <div class="hero-float">
                    <div class="hf-icon hf-b">🎓</div>
                    <div>
                        <div class="hf-label">Support</div>
                        <div class="hf-val">24 / 7</div>
                    </div>
                </div>
            </div>

            <!-- App window floating bottom right -->
            <div class="hero-app-wrap">
                <div class="hero-app-win">
                    <div class="app-win-bar">
                        <div class="awb-dots"><span></span><span></span><span></span></div>
                        <div class="awb-title">app.syntriforg.edu</div>
                    </div>
                    <div class="app-win-body">
                        <!-- KPIs -->
                        <div class="aw-kpi-row">
                            <div class="aw-kpi">
                                <div class="aw-kpi-lbl">Élèves</div>
                                <div class="aw-kpi-val">247</div>
                                <div class="aw-kpi-delta">↑ +12</div>
                            </div>
                            <div class="aw-kpi">
                                <div class="aw-kpi-lbl">Présence</div>
                                <div class="aw-kpi-val">91%</div>
                                <div class="aw-kpi-delta">↑ +3%</div>
                            </div>
                            <div class="aw-kpi">
                                <div class="aw-kpi-lbl">Frais</div>
                                <div class="aw-kpi-val">78%</div>
                                <div class="aw-kpi-delta" style="color:var(--amber)">→ stable</div>
                            </div>
                        </div>
                        <!-- Mini chart -->
                        <div class="aw-chart-wrap">
                            <div class="aw-chart-title">Inscriptions — 6 mois</div>
                            <div class="aw-bars">
                                <div class="aw-bar" style="height:42%"></div>
                                <div class="aw-bar" style="height:54%"></div>
                                <div class="aw-bar" style="height:63%"></div>
                                <div class="aw-bar" style="height:72%"></div>
                                <div class="aw-bar" style="height:82%"></div>
                                <div class="aw-bar hi" style="height:100%"></div>
                            </div>
                        </div>
                        <!-- Status row -->
                        <div class="aw-row">
                            <div class="aw-row-left">
                                <div class="aw-dot"></div>
                                <div class="aw-row-text">Terminale A — Actif</div>
                            </div>
                            <div class="aw-row-badge">38 élèves</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ TICKER ═══ -->
    <div class="ticker">
        <div class="ticker-inner">
            <span class="tick-item">Formation de données <span class="tick-dot">◆</span></span>
            <span class="tick-item">Éducation qualitative <span class="tick-dot">◆</span></span>
            <span class="tick-item">Comptabilité scolaire <span class="tick-dot">◆</span></span>
            <span class="tick-item">Apprentissage collaboratif <span class="tick-dot">◆</span></span>
            <span class="tick-item">Management personnel <span class="tick-dot">◆</span></span>
            <span class="tick-item">250+ établissements visés <span class="tick-dot">◆</span></span>
            <span class="tick-item">Support 24/7 <span class="tick-dot">◆</span></span>
            <span class="tick-item">Notes & bulletins automatiques <span class="tick-dot">◆</span></span>
            <span class="tick-item">Formation de données <span class="tick-dot">◆</span></span>
            <span class="tick-item">Éducation qualitative <span class="tick-dot">◆</span></span>
            <span class="tick-item">Comptabilité scolaire <span class="tick-dot">◆</span></span>
            <span class="tick-item">Apprentissage collaboratif <span class="tick-dot">◆</span></span>
            <span class="tick-item">Management personnel <span class="tick-dot">◆</span></span>
            <span class="tick-item">250+ établissements visés <span class="tick-dot">◆</span></span>
            <span class="tick-item">Support 24/7 <span class="tick-dot">◆</span></span>
            <span class="tick-item">Notes & bulletins automatiques <span class="tick-dot">◆</span></span>
        </div>
    </div>

    <!-- ═══ STATS ═══ -->
    <div class="stats-band">
        <div class="stats-inner">
            <div class="stat-item rv">
                <div class="stat-accent"></div>
                <div class="stat-num g" data-count="250" data-suffix="+">0+</div>
                <div class="stat-new">Objectif</div>
                <div class="stat-label">Établissements</div>
                <div class="stat-sub">visés sur 3 ans</div>
            </div>
            <div class="stat-item rv d1">
                <div class="stat-accent"></div>
                <div class="stat-num g">50K+</div>
                <div class="stat-new">Cible</div>
                <div class="stat-label">Étudiants</div>
                <div class="stat-sub">à atteindre</div>
            </div>
            <div class="stat-item rv d2">
                <div class="stat-accent"></div>
                <div class="stat-num g" data-count="95" data-suffix="%">0%</div>
                <div class="stat-new">Enquête</div>
                <div class="stat-label">Satisfaction</div>
                <div class="stat-sub">retours utilisateurs</div>
            </div>
            <div class="stat-item rv d3">
                <div class="stat-accent"></div>
                <div class="stat-num" style="color:#fff">24/7</div>
                <div class="stat-label">Support client</div>
                <div class="stat-sub">assistance permanente</div>
            </div>
        </div>
    </div>

    <!-- ═══ FONCTIONNALITÉS ═══ -->
    <section class="sec" id="fonctionnalites">
        <div class="container">
            <div style="margin-bottom:4rem">
                <div class="stag p rv">Fonctionnalités</div>
                <h2 class="st rv d1">Améliorez vos <em><span class="grad">compétences</span></em></h2>
                <p class="sub rv d2">Des outils puissants pour révolutionner la gestion de vos établissements
                    scolaires.</p>
            </div>
            <div class="feat-grid">
                <div class="fc rv">
                    <div class="fc-img"><img
                            src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&q=80"
                            alt="Formation"></div>
                    <div class="fc-body"><span class="fc-tag">Data</span>
                        <h3>Formation de données</h3>
                        <p>Collectez et analysez les données académiques pour améliorer les performances de chaque
                            apprenant.</p><span class="fc-link">En savoir plus →</span>
                    </div>
                </div>
                <div class="fc rv d1">
                    <div class="fc-img"><img
                            src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&q=80"
                            alt="Éducation"></div>
                    <div class="fc-body"><span class="fc-tag">Pédagogie</span>
                        <h3>Éducation qualitative</h3>
                        <p>Des outils pédagogiques de pointe pour un enseignement d'excellence adapté aux besoins
                            actuels.</p><span class="fc-link">En savoir plus →</span>
                    </div>
                </div>
                <div class="fc rv d2">
                    <div class="fc-img"><img
                            src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600&q=80"
                            alt="Comptabilité"></div>
                    <div class="fc-body"><span class="fc-tag">Finance</span>
                        <h3>Comptabilité élégante</h3>
                        <p>Gestion financière simplifiée pour tous vos établissements. Facturation, budgets, rapports en
                            un clic.</p><span class="fc-link">En savoir plus →</span>
                    </div>
                </div>
                <div class="fc rv d3">
                    <div class="fc-img"><img
                            src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80"
                            alt="Collaboratif"></div>
                    <div class="fc-body"><span class="fc-tag">Collaboration</span>
                        <h3>Apprentissage collaboratif</h3>
                        <p>Favorisez la collaboration en temps réel entre enseignants et étudiants sur tous les
                            appareils.</p><span class="fc-link">En savoir plus →</span>
                    </div>
                </div>
                <div class="fc rv d4">
                    <div class="fc-img"><img
                            src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&q=80"
                            alt="Management"></div>
                    <div class="fc-body"><span class="fc-tag">RH</span>
                        <h3>Management personnel</h3>
                        <p>Gérez efficacement le personnel enseignant et administratif. Plannings, congés, évaluations
                            intégrés.</p><span class="fc-link">En savoir plus →</span>
                    </div>
                </div>
                <div class="fc rv d5">
                    <div class="fc-img"><img
                            src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600&q=80"
                            alt="Certifications"></div>
                    <div class="fc-body"><span class="fc-tag">Certifications</span>
                        <h3>Centre de certification</h3>
                        <p>Délivrez et gérez les certifications et diplômes en ligne avec signature numérique et
                            traçabilité.</p><span class="fc-link">En savoir plus →</span>
                    </div>
                </div>
            </div>
            <div style="text-align:center;margin-top:3rem" class="rv">
                <a href="#" class="btn btn-grad">Découvrir tous les modules →</a>
            </div>
        </div>
    </section>

    <!-- ═══ PROCESSUS ═══ -->
    <section id="process">
        <div class="proc-header rv">
            <div class="stag p" style="justify-content:center;color:rgba(255,255,255,.38)">Notre méthode</div>
            <h2 class="st rv d1" style="text-align:center;color:#fff">Comment ça <em
                    style="color:rgba(255,255,255,.45)">fonctionne</em></h2>
            <p class="sub rv d2" style="text-align:center;margin:0 auto;color:rgba(255,255,255,.35)">De votre
                inscription à la transformation complète de votre établissement.</p>
        </div>
        <div class="proc-grid">
            <div class="ps rv">
                <div class="ps-dot">📋</div>
                <div class="ps-num">01</div>
                <h4>Programmes</h4>
                <p>Nos programmes sont construits en collaboration avec les experts de l'industrie éducative.</p>
            </div>
            <div class="ps rv d1">
                <div class="ps-dot">📖</div>
                <div class="ps-num">02</div>
                <h4>Enseignement</h4>
                <p>Un accompagnement personnalisé avec des méthodes pédagogiques innovantes et adaptées.</p>
            </div>
            <div class="ps rv d2">
                <div class="ps-dot">📊</div>
                <div class="ps-num">03</div>
                <h4>Résultats</h4>
                <p>Suivez la progression et les résultats en temps réel avec nos analytics avancés.</p>
            </div>
            <div class="ps rv d3">
                <div class="ps-dot">🎓</div>
                <div class="ps-num">04</div>
                <h4>Expériences</h4>
                <p>Des opportunités d'apprentissage pratique et de développement professionnel continu.</p>
            </div>
        </div>
    </section>

    <!-- ═══ SCHOOLS ═══ -->
    <section class="sec sec-alt" id="schools">
        <div class="container">
            <div style="text-align:center;margin-bottom:4rem">
                <div class="stag p rv" style="justify-content:center">Partenaires académiques</div>
                <h2 class="st rv d1" style="text-align:center">Nos <em><span class="grad">partenaires</span></em>
                    académiques</h2>
                <p class="sub rv d2" style="text-align:center;margin:0 auto">Notre objectif : 250+ établissements à
                    travers toute la francophonie.</p>
            </div>
            <div class="schools-grid">
                <div class="sc rv">
                    <div class="sc-img">
                        <img src="https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?w=600&q=80"
                            alt="Lycée International">
                        <div class="sc-logo"><span>LI</span></div>
                    </div>
                    <div class="sc-body">
                        <div class="sc-name">Lycée International</div>
                        <div class="sc-type">Enseignement secondaire</div><span class="sc-pill">25 classes</span>
                    </div>
                </div>
                <div class="sc rv d1">
                    <div class="sc-img">
                        <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&q=80"
                            alt="École Les Lilas">
                        <div class="sc-logo"><span>LL</span></div>
                    </div>
                    <div class="sc-body">
                        <div class="sc-name">École Les Lilas</div>
                        <div class="sc-type">Enseignement primaire</div><span class="sc-pill">12 classes</span>
                    </div>
                </div>
                <div class="sc rv d2">
                    <div class="sc-img">
                        <img src="https://images.unsplash.com/photo-1562774053-701939374585?w=600&q=80"
                            alt="Université Techno">
                        <div class="sc-logo"><span>UT</span></div>
                    </div>
                    <div class="sc-body">
                        <div class="sc-name">Université Techno</div>
                        <div class="sc-type">Enseignement supérieur</div><span class="sc-pill">42 départements</span>
                    </div>
                </div>
                <div class="sc rv d3">
                    <div class="sc-img">
                        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&q=80"
                            alt="Conservatoire des Arts">
                        <div class="sc-logo"><span>CA</span></div>
                    </div>
                    <div class="sc-body">
                        <div class="sc-name">Conservatoire des Arts</div>
                        <div class="sc-type">Formation artistique</div><span class="sc-pill">18 ateliers</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ PLATEFORME / ENRICH ═══ -->
    <section class="sec" id="enrich">
        <div class="container">
            <div style="text-align:center;margin-bottom:4rem">
                <div class="stag b rv" style="justify-content:center">Enrichissez votre plateforme</div>
                <h2 class="st rv d1" style="text-align:center">Enrichissez votre <em><span
                            class="grad">plateforme</span></em></h2>
                <p class="sub rv d2" style="text-align:center;margin:0 auto">Découvrez comment SyntriForg Edu peut
                    améliorer l'expérience académique de vos établissements.</p>
            </div>
            <div class="enrich-grid">
                <div class="ec rv">
                    <div class="ec-num">01</div><span class="ec-icon">📚</span>
                    <h4>Bibliothèque Numérique</h4>
                    <p>Accédez à un vaste catalogue de ressources pédagogiques pour enrichir les cours et projets
                        étudiants avec du contenu de qualité.</p>
                </div>
                <div class="ec rv d1">
                    <div class="ec-num">02</div><span class="ec-icon">💡</span>
                    <h4>Outils Collaboratifs</h4>
                    <p>Favorisez la collaboration entre enseignants et étudiants grâce à des outils intégrés, intuitifs
                        et accessibles depuis tout appareil.</p>
                </div>
                <div class="ec rv d2">
                    <div class="ec-num">03</div><span class="ec-icon">📈</span>
                    <h4>Analytics & Performance</h4>
                    <p>Suivez les progrès académiques en temps réel et prenez des décisions éclairées pour améliorer
                        durablement les résultats.</p>
                </div>
                <div class="ec rv d3">
                    <div class="ec-num">04</div><span class="ec-icon">🛠️</span>
                    <h4>Modules Personnalisables</h4>
                    <p>Personnalisez la plateforme selon les besoins spécifiques de chaque établissement ou programme
                        éducatif.</p>
                </div>
                <div class="ec rv d4">
                    <div class="ec-num">05</div><span class="ec-icon">🔒</span>
                    <h4>Sécurité & Confidentialité</h4>
                    <p>Garantissez la protection des données des étudiants et enseignants avec des protocoles de
                        sécurité institutionnels avancés.</p>
                </div>
                <div class="ec rv d5">
                    <div class="ec-num">06</div><span class="ec-icon">🌐</span>
                    <h4>Accessibilité Globale</h4>
                    <p>Accédez à la plateforme depuis n'importe où, favorisant l'apprentissage hybride et la continuité
                        pédagogique.</p>
                </div>
            </div>
            <div style="text-align:center;margin-top:3rem" class="rv">
                <a href="#" class="btn btn-grad">Découvrir toutes les fonctionnalités →</a>
            </div>
        </div>
    </section>

    <!-- ═══ CTA ═══ -->
    <section id="cta">
        <div class="cta-box rv">
            <div class="stag p rv" style="justify-content:center;color:rgba(255,255,255,.55)">Prêt à commencer ?</div>
            <h2 class="st rv d1">Prêt à transformer votre<br><em>écosystème éducatif ?</em></h2>
            <p class="rv d2">Rejoignez les premiers établissements qui font confiance à SyntriForg Edu pour simplifier
                leur gestion académique et améliorer leurs performances dès le lancement.</p>
            <div class="cta-actions rv d3">
                <a href="https://syntriforg.ct.ws/?i=1" class="btn-white">Demander une démo</a>
                <a href="#" class="btn-out-white">Parler à un expert</a>
            </div>
            <div class="cta-perks rv d4">
                <div class="cta-perk">Démo gratuite 3 mois</div>
                <div class="cta-perk">Sans carte bancaire</div>
                <div class="cta-perk">Support 24/7</div>
            </div>
        </div>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer>
        <div class="footer-top">
            <div>
                <div class="fb-logo">
                    <div class="fb-logo-mark">
                        <img src="/medias/Syntriforg[1].png" alt="SyntriForge"
                            onerror="this.style.display='none';this.parentElement.innerHTML='🎓'">
                    </div>
                    <div class="fb-logo-text">SyntriForg <span>Edu</span></div>
                </div>
                <p class="fb-desc">La plateforme tout-en-un pour gérer vos établissements scolaires avec efficacité et
                    innovation. Lancée en 2026 pour la francophonie.</p>
                <div class="socials">
                    <a href="#" class="soc" aria-label="X">𝕏</a>
                    <a href="#" class="soc" aria-label="LinkedIn">in</a>
                    <a href="#" class="soc" aria-label="Facebook">f</a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Entreprise</h4>
                <ul>
                    <li><a href="/about">À propos</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Carrières</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Légal</h4>
                <ul>
                    <li><a href="#">Confidentialité</a></li>
                    <li><a href="#">CGU</a></li>
                    <li><a href="#">Cookies</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 SyntriForg Edu. Tous droits réservés.</p>
            <p>Connected Intelligence</p>
        </div>
    </footer>

    <!-- ═══ PWA FAB ═══ -->
    <button id="pwa-fab" aria-label="Installer l'application">⬇ Installer l'app</button>

    <!-- ═══ PWA MODAL ═══ -->
    <div id="pwa-modal-overlay">
        <div id="pwa-modal">
            <h3 id="pwa-modal-title">Installer SyntriForg Edu</h3>
            <p id="pwa-modal-body"></p>
            <button id="pwa-modal-close">Fermer</button>
        </div>
    </div>

    <!-- ═══════════════ SCRIPTS ═══════════════ -->
    <script>
        /* ── Custom cursor ── */
        if (window.matchMedia('(hover:hover) and (pointer:fine)').matches) {
            const cd = document.getElementById('cd'),
                cr = document.getElementById('cr');
            let mx = 0,
                my = 0,
                rx = 0,
                ry = 0;
            document.addEventListener('mousemove', e => {
                mx = e.clientX;
                my = e.clientY;
                cd.style.left = mx + 'px';
                cd.style.top = my + 'px';
            });
            (function loop() {
                rx += (mx - rx) * .1;
                ry += (my - ry) * .1;
                cr.style.left = rx + 'px';
                cr.style.top = ry + 'px';
                requestAnimationFrame(loop);
            })();
            document.querySelectorAll('a,button,.fc,.sc,.ec,.ps').forEach(el => {
                el.addEventListener('mouseenter', () => document.body.classList.add('hov'));
                el.addEventListener('mouseleave', () => document.body.classList.remove('hov'));
            });
        } else {
            const c = document.getElementById('cur');
            if (c) c.style.display = 'none';
        }

        /* ── Header scroll ── */
        window.addEventListener('scroll', () => {
            document.getElementById('header').classList.toggle('solid', scrollY > 60);
        }, {
            passive: true
        });

        /* ── Scroll Reveal ── */
        const obs = new IntersectionObserver(entries => {
            entries.forEach(x => {
                if (x.isIntersecting) {
                    x.target.classList.add('in');
                    obs.unobserve(x.target);
                }
            });
        }, {
            threshold: .1
        });
        document.querySelectorAll('.rv').forEach(el => obs.observe(el));

        /* ── Counters ── */
        function counter(el, target, dur = 1600) {
            const s = performance.now(),
                suf = el.dataset.suffix || '';
            (function t(now) {
                const p = Math.min((now - s) / dur, 1),
                    ease = 1 - Math.pow(1 - p, 4);
                el.textContent = Math.floor(ease * target) + suf;
                if (p < 1) requestAnimationFrame(t);
                else el.textContent = target + suf;
            })(s);
        }
        const cobs = new IntersectionObserver(entries => {
            entries.forEach(x => {
                if (x.isIntersecting) {
                    x.target.querySelectorAll('[data-count]').forEach(el => counter(el, parseInt(el.dataset
                        .count)));
                    cobs.unobserve(x.target);
                }
            });
        }, {
            threshold: .3
        });
        document.querySelectorAll('.stats-band').forEach(el => cobs.observe(el));

        /* ── Card tilt ── */
        if (window.matchMedia('(hover:hover)').matches) {
            document.querySelectorAll('.fc,.sc,.ec').forEach(c => {
                c.addEventListener('mousemove', e => {
                    const r = c.getBoundingClientRect();
                    const x = ((e.clientX - r.left) / r.width - .5) * 7;
                    const y = ((e.clientY - r.top) / r.height - .5) * -7;
                    c.style.transform =
                        `perspective(900px) rotateX(${y}deg) rotateY(${x}deg) translateY(-6px)`;
                });
                c.addEventListener('mouseleave', () => c.style.transform = '');
            });
        }

        /* ── Burger ── */
        const burger = document.getElementById('burger'),
            menu = document.getElementById('nav-menu');
        burger.addEventListener('click', () => {
            burger.classList.toggle('active');
            menu.classList.toggle('active');
        });
        document.querySelectorAll('#nav-menu a').forEach(a => a.addEventListener('click', () => {
            burger.classList.remove('active');
            menu.classList.remove('active');
        }));

        /* ── Smooth scroll ── */
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const t = document.querySelector(a.getAttribute('href'));
                if (t) {
                    e.preventDefault();
                    t.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        /* ══ PWA INSTALL ══ */
        (function() {
            const fab = document.getElementById('pwa-fab');
            const overlay = document.getElementById('pwa-modal-overlay');
            const modalTitle = document.getElementById('pwa-modal-title');
            const modalBody = document.getElementById('pwa-modal-body');
            const modalClose = document.getElementById('pwa-modal-close');

            const ua = navigator.userAgent.toLowerCase();
            const isIOS = /iphone|ipad|ipod/.test(ua);
            const isAndroid = /android/.test(ua);
            const isSamsung = /samsungbrowser/.test(ua);

            const isStandalone =
                window.navigator.standalone === true ||
                window.matchMedia('(display-mode: standalone)').matches ||
                window.matchMedia('(display-mode: fullscreen)').matches;
            if (isStandalone) return;

            let deferredPrompt = null,
                promptAvailable = false;

            window.addEventListener('beforeinstallprompt', function(e) {
                e.preventDefault();
                deferredPrompt = e;
                promptAvailable = true;
                fab.classList.add('visible');
            });

            if (isIOS) fab.classList.add('visible');

            window.addEventListener('appinstalled', function() {
                fab.classList.remove('visible');
                deferredPrompt = null;
            });

            fab.addEventListener('click', async function() {
                if (promptAvailable && deferredPrompt) {
                    try {
                        await deferredPrompt.prompt();
                        const {
                            outcome
                        } = await deferredPrompt.userChoice;
                        if (outcome === 'accepted') fab.classList.remove('visible');
                    } catch (err) {
                        showManualGuide();
                    }
                    deferredPrompt = null;
                    promptAvailable = false;
                    return;
                }
                showManualGuide();
            });

            function showManualGuide() {
                if (isIOS) {
                    modalTitle.textContent = 'Installer sur iPhone / iPad';
                    modalBody.innerHTML =
                        `Dans <strong>Safari</strong>, appuyez sur <span class="chip">⎙ Partager</span> en bas de l'écran, puis choisissez <span class="chip">Sur l'écran d'accueil</span>. L'app s'installe sans passer par l'App Store.`;
                } else if (isSamsung) {
                    modalTitle.textContent = 'Installer sur Samsung';
                    modalBody.innerHTML =
                        `Dans <strong>Samsung Internet</strong>, appuyez sur l'icône <span class="chip">⋮ Menu</span> en bas, puis <span class="chip">Ajouter page à</span> → <span class="chip">Écran d'accueil</span>.`;
                } else if (isAndroid) {
                    modalTitle.textContent = 'Installer sur Android';
                    modalBody.innerHTML =
                        `Dans <strong>Chrome</strong>, appuyez sur le menu <span class="chip">⋮</span> en haut à droite, puis choisissez <span class="chip">Installer l'application</span> ou <span class="chip">Ajouter à l'écran d'accueil</span>.<br><br><em style="font-size:.78rem;color:#94a3b8">Si l'option n'apparaît pas, assurez-vous de visiter le site via Chrome et que HTTPS est actif.</em>`;
                } else {
                    modalTitle.textContent = 'Installer SyntriForg Edu';
                    modalBody.innerHTML =
                        `Dans <strong>Chrome</strong> ou <strong>Edge</strong>, cliquez sur le menu <span class="chip">⋮</span> puis <span class="chip">Installer l'application</span>.`;
                }
                overlay.classList.add('open');
            }

            modalClose.addEventListener('click', () => overlay.classList.remove('open'));
            overlay.addEventListener('click', e => {
                if (e.target === overlay) overlay.classList.remove('open');
            });
        })();
    </script>

    <!-- Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js', {
                        scope: '/'
                    })
                    .then(function(reg) {
                        console.log('✅ SW enregistré — scope :', reg.scope);
                        reg.addEventListener('updatefound', function() {
                            const nw = reg.installing;
                            nw.addEventListener('statechange', function() {
                                if (nw.state === 'installed' && navigator.serviceWorker
                                    .controller)
                                    console.log('🔄 Nouveau SW disponible');
                            });
                        });
                    })
                    .catch(function(err) {
                        console.warn('❌ SW erreur :', err);
                    });
            });
        }
    </script>
</body>

</html>
