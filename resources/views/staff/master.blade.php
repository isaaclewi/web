<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Staff') — {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════
         | TOKENS
         ═══════════════════════════════════════════ */
        :root {
            --night: #080c14; --night-2: #0d1220; --night-3: #131928; --night-4: #1c2436;
            --steel: #2a3347; --mist: #8896b0; --pale: #c4cedf; --snow: #f0f4fa; --white: #ffffff;
            --gold: #f59e0b; --gold-d: #d97706; --gold-l: #fef3c7; --gold-dim: rgba(245,158,11,.12);
            --ok: #10b981; --ok-l: #d1fae5; --warn: #f59e0b; --warn-l: #fef3c7;
            --err: #ef4444; --err-l: #fee2e2; --info: #3b82f6; --info-l: #dbeafe;
            --bg: #f5f7fb; --brd: #e4e8f0; --brd-d: #d0d7e4;
            --sb-w: 268px; --sb-w-c: 70px; --hh: 64px;
            --ease: cubic-bezier(.4,0,.2,1);
        }

        /* ── Base ── */
        *, *::before, *::after { font-family: 'DM Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); color: var(--night); overflow-x: hidden; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, .syne { font-family: 'Syne', sans-serif; }

        /* ── Overlay mobile ── */
        .ov {
            display: none;
            position: fixed; inset: 0;
            background: rgba(8,12,20,.65);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            z-index: 49;
            opacity: 0;
            transition: opacity .3s var(--ease);
            /* ✅ FIX: pointer-events actif même en opacity 0 bloque les clics — on corrige */
            pointer-events: none;
        }
        .ov.on { opacity: 1; pointer-events: auto; }

        /* ═══════════════════════════════════════════
         | SIDEBAR
         ═══════════════════════════════════════════ */
        .sb {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sb-w);
            background: var(--night);
            z-index: 50;
            display: flex; flex-direction: column;
            overflow: hidden;
            transition: transform .35s var(--ease), width .3s var(--ease);
            border-right: 1px solid rgba(255,255,255,.04);
        }
        .sb::before {
            content: ''; position: absolute; top: 0; left: 0; width: 3px; height: 100%;
            background: linear-gradient(to bottom, var(--gold) 0%, transparent 60%);
            opacity: .7; pointer-events: none;
        }

        /* ── Logo ── */
        .sb-logo {
            display: flex; align-items: center; gap: .875rem;
            padding: 1.25rem 1.125rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.055);
            flex-shrink: 0;
        }
        .sb-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-d) 100%);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; box-shadow: 0 4px 14px rgba(245,158,11,.35);
        }
        .sb-icon svg { width: 20px; height: 20px; color: var(--night); }
        .sb-brand { min-width: 0; flex: 1; }
        .sb-brand-name { font-family: 'Syne', sans-serif; font-weight: 700; font-size: .9rem; color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -.01em; }
        .sb-brand-tag { font-size: .62rem; font-weight: 500; color: var(--gold); text-transform: uppercase; letter-spacing: .1em; margin-top: .1rem; }
        .sb-close {
            /* ✅ FIX: toujours en display:flex mais caché par défaut via visibility */
            display: flex; visibility: hidden;
            width: 36px; height: 36px;
            border-radius: 8px; background: rgba(255,255,255,.08); border: none;
            color: var(--mist); align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0; transition: all .15s;
            /* ✅ FIX: zone de tap minimum 44px recommandée par Apple HIG */
            min-width: 36px; min-height: 36px;
        }
        .sb-close:hover { background: rgba(255,255,255,.13); color: var(--white); }
        .sb-close svg { width: 14px; height: 14px; }

        /* ── Staff card ── */
        .sb-staff {
            display: flex; align-items: center; gap: .75rem;
            padding: .875rem 1.125rem;
            border-bottom: 1px solid rgba(255,255,255,.055);
            flex-shrink: 0;
        }
        .sb-avatar {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--night-4); border: 1.5px solid var(--steel);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-weight: 700; color: var(--gold);
            font-size: .82rem; flex-shrink: 0;
        }
        .sb-staff-name { font-size: .82rem; font-weight: 600; color: var(--pale); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sb-staff-role { font-size: .64rem; color: var(--mist); margin-top: .1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* ── Navigation ── */
        .sb-nav { padding: .875rem .75rem; flex: 1; overflow-y: auto; overflow-x: hidden; -webkit-overflow-scrolling: touch; }
        .sb-nav::-webkit-scrollbar { width: 0; }
        .sb-sec { margin-bottom: 1.5rem; }
        .sb-sec-lbl { font-size: .58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .14em; color: rgba(255,255,255,.2); padding: 0 .625rem; margin-bottom: .5rem; white-space: nowrap; }

        .sb-lnk {
            display: flex; align-items: center; gap: .75rem;
            padding: .625rem .75rem;           /* ✅ FIX: padding vertical augmenté pour zone de tap */
            border-radius: 9px;
            color: var(--mist);
            font-size: .83rem; font-weight: 400;
            text-decoration: none;
            transition: all .18s var(--ease);
            margin-bottom: 3px;               /* ✅ FIX: espacement entre items */
            position: relative; white-space: nowrap;
            /* ✅ FIX: hauteur minimum tap target */
            min-height: 44px;
        }
        .sb-lnk:hover { background: rgba(255,255,255,.055); color: var(--pale); }
        .sb-lnk:active { background: rgba(255,255,255,.08); transform: scale(.99); }
        .sb-lnk.on { background: var(--gold-dim); color: var(--gold); font-weight: 500; }
        .sb-lnk.on::after { content: ''; position: absolute; right: .75rem; width: 5px; height: 5px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 6px var(--gold); }

        .sb-lnk-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: rgba(255,255,255,.05);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background .18s;
        }
        .sb-lnk:hover .sb-lnk-icon { background: rgba(255,255,255,.09); }
        .sb-lnk.on .sb-lnk-icon { background: rgba(245,158,11,.15); }
        .sb-lnk-icon svg { width: 15px; height: 15px; }

        /* ── Footer / logout ── */
        .sb-foot { padding: .875rem .75rem 1rem; border-top: 1px solid rgba(255,255,255,.055); flex-shrink: 0; }
        .sb-logout {
            display: flex; align-items: center; gap: .75rem; width: 100%;
            background: none; border: 1px solid rgba(255,255,255,.07);
            border-radius: 9px; padding: .625rem .75rem;   /* ✅ FIX: zone de tap plus grande */
            color: var(--mist); font-size: .83rem; font-weight: 400;
            cursor: pointer; font-family: inherit; transition: all .18s; text-align: left;
            min-height: 44px;                               /* ✅ FIX: tap target */
        }
        .sb-logout:hover { background: rgba(239,68,68,.1); border-color: rgba(239,68,68,.25); color: #fca5a5; }
        .sb-logout-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,.05); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .sb-logout:hover .sb-logout-icon { background: rgba(239,68,68,.15); }
        .sb-logout-icon svg { width: 15px; height: 15px; }

        /* ═══════════════════════════════════════════
         | HEADER
         ═══════════════════════════════════════════ */
        .hd {
            position: fixed; top: 0; right: 0; left: var(--sb-w); height: var(--hh);
            background: rgba(245,247,251,.9); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--brd); z-index: 30;
            display: flex; align-items: center; padding: 0 1.75rem;
            justify-content: space-between; gap: 1rem;
            transition: left .35s var(--ease);
        }
        .hd-left { display: flex; align-items: center; gap: .875rem; min-width: 0; flex: 1; }

        .hd-mbtn {
            /* ✅ FIX: taille minimale 44x44 pour bonne tap area */
            display: none;
            width: 44px; height: 44px;
            border-radius: 10px; border: 1px solid var(--brd);
            background: var(--white);
            align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0; transition: all .15s;
            /* ✅ FIX: pas de propagation du clic vers le contenu derrière */
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        .hd-mbtn:hover { background: var(--bg); border-color: var(--brd-d); }
        .hd-mbtn:active { background: var(--gold-dim); border-color: var(--gold); transform: scale(.96); }
        .hd-mbtn svg { width: 18px; height: 18px; color: #6b7280; }

        .hd-sep { width: 1px; height: 22px; background: var(--brd-d); flex-shrink: 0; display: none; }
        .hd-title-wrap { min-width: 0; }
        .hd-title { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--night); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -.02em; }
        .hd-breadcrumb { display: flex; align-items: center; gap: .35rem; font-size: .7rem; color: var(--mist); margin-top: .1rem; }
        .hd-breadcrumb span { white-space: nowrap; }
        .hd-breadcrumb .sep { color: var(--brd-d); }
        .hd-right { display: flex; align-items: center; gap: .625rem; flex-shrink: 0; }
        .hd-date { display: flex; align-items: center; gap: .4rem; background: var(--white); border: 1px solid var(--brd); border-radius: 8px; padding: .35rem .75rem; font-size: .72rem; font-weight: 500; color: #6b7280; }
        .hd-date svg { width: 12px; height: 12px; color: var(--gold); }
        .hd-av {
            width: 38px; height: 38px; border-radius: 9px;
            background: var(--night); border: 2px solid var(--steel);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-weight: 700; color: var(--gold);
            font-size: .75rem; cursor: pointer; transition: border-color .15s;
            text-decoration: none;
        }
        .hd-av:hover { border-color: var(--gold); }

        /* ═══════════════════════════════════════════
         | MAIN CONTENT
         ═══════════════════════════════════════════ */
        .s-main { margin-left: var(--sb-w); padding-top: var(--hh); min-height: 100vh; transition: margin-left .35s var(--ease); }
        .s-body { padding: 1.75rem; }

        /* ═══════════════════════════════════════════
         | FLASH NOTIFICATIONS
         ═══════════════════════════════════════════ */
        .s-fl {
            position: fixed; top: 1.25rem; right: 1.25rem; z-index: 9999;
            display: flex; align-items: flex-start; gap: .75rem;
            padding: .875rem 1.125rem; border-radius: 12px;
            font-size: .83rem; font-weight: 500; max-width: 380px;
            box-shadow: 0 8px 32px rgba(0,0,0,.12), 0 0 0 1px rgba(0,0,0,.04);
            animation: flIn .35s var(--ease) both;
        }
        .s-fl-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .s-fl-icon svg { width: 14px; height: 14px; }
        .s-fl.ok { background: var(--white); color: #065f46; border-left: 3px solid var(--ok); }
        .s-fl.ok .s-fl-icon { background: var(--ok-l); color: var(--ok); }
        .s-fl.ko { background: var(--white); color: #7f1d1d; border-left: 3px solid var(--err); }
        .s-fl.ko .s-fl-icon { background: var(--err-l); color: var(--err); }
        .s-fl-close { margin-left: auto; width: 24px; height: 24px; border-radius: 5px; background: none; border: none; color: var(--mist); cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .15s; }
        .s-fl-close:hover { background: var(--bg); }
        .s-fl-close svg { width: 10px; height: 10px; }
        @keyframes flIn { from { transform: translateX(20px); opacity: 0; } to { transform: none; opacity: 1; } }

        /* ═══════════════════════════════════════════
         | SHARED DESIGN SYSTEM
         ═══════════════════════════════════════════ */
        .s-card { background: var(--white); border: 1px solid var(--brd); border-radius: 14px; overflow: hidden; }
        .s-card-hd { padding: .875rem 1.375rem; border-bottom: 1px solid var(--brd); display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
        .s-card-hd h3 { font-family: 'Syne', sans-serif; font-size: .9rem; font-weight: 700; letter-spacing: -.01em; }
        .s-card-body { padding: 1.375rem; }

        .stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; }
        .stat-card { background: var(--white); border: 1px solid var(--brd); border-radius: 14px; padding: 1.25rem; position: relative; overflow: hidden; transition: box-shadow .2s, border-color .2s; }
        .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); border-color: var(--brd-d); }
        .stat-card::after { content: ''; position: absolute; top: 0; right: 0; width: 60px; height: 60px; border-radius: 0 14px 0 60px; background: var(--bg); }
        .stat-val { font-family: 'Syne', sans-serif; font-size: 1.875rem; font-weight: 800; color: var(--night); line-height: 1; letter-spacing: -.04em; }
        .stat-lbl { font-size: .73rem; color: var(--mist); margin-top: .4rem; font-weight: 400; }
        .stat-icon { position: absolute; top: 1rem; right: 1rem; width: 34px; height: 34px; border-radius: 9px; background: var(--bg); display: flex; align-items: center; justify-content: center; z-index: 1; }
        .stat-icon svg { width: 16px; height: 16px; }

        .s-tbl { width: 100%; border-collapse: collapse; }
        .s-tbl thead th { padding: .625rem 1.125rem; text-align: left; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--mist); border-bottom: 1px solid var(--brd); background: #fafbfd; }
        .s-tbl tbody td { padding: .85rem 1.125rem; border-bottom: 1px solid #f3f5f9; font-size: .85rem; color: #374151; vertical-align: middle; }
        .s-tbl tbody tr:last-child td { border-bottom: none; }
        .s-tbl tbody tr { transition: background .12s; }
        .s-tbl tbody tr:hover td { background: #fafbfd; }

        .bdg { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .65rem; border-radius: 20px; font-size: .69rem; font-weight: 600; white-space: nowrap; }
        .bdg::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: .7; }
        .bdg-g { background: var(--ok-l); color: #065f46; }
        .bdg-a { background: var(--warn-l); color: #92400e; }
        .bdg-r { background: var(--err-l); color: #7f1d1d; }
        .bdg-b { background: var(--info-l); color: #1e3a8a; }
        .bdg-n { background: #f1f5f9; color: #64748b; }

        .btn {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .5rem 1.125rem; border-radius: 9px;
            font-size: .82rem; font-weight: 600; cursor: pointer; border: none;
            transition: all .18s var(--ease); text-decoration: none;
            font-family: inherit; letter-spacing: -.01em;
            /* ✅ FIX: tap targets */
            min-height: 40px;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        .btn svg { width: 15px; height: 15px; }
        .btn-dk { background: var(--night); color: var(--white); }
        .btn-dk:hover { background: var(--night-3); box-shadow: 0 4px 12px rgba(8,12,20,.25); }
        .btn-gold { background: var(--gold); color: var(--night); }
        .btn-gold:hover { background: var(--gold-d); box-shadow: 0 4px 14px rgba(245,158,11,.35); }
        .btn-ok { background: var(--ok); color: var(--white); }
        .btn-ok:hover { background: #059669; }
        .btn-err { background: var(--err); color: var(--white); }
        .btn-err:hover { background: #dc2626; }
        .btn-ot { background: var(--white); color: #6b7280; border: 1px solid var(--brd); }
        .btn-ot:hover { background: var(--bg); border-color: var(--brd-d); color: var(--night); }
        .btn-sm { padding: .35rem .75rem; font-size: .76rem; border-radius: 7px; min-height: 36px; }

        .inp { width: 100%; border: 1.5px solid var(--brd); border-radius: 9px; padding: .55rem .875rem; font-size: .84rem; font-family: inherit; color: var(--night); background: var(--white); transition: border-color .18s, box-shadow .18s; outline: none; }
        .inp:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(245,158,11,.12); }
        .inp::placeholder { color: var(--mist); }
        .lbl { font-size: .76rem; font-weight: 600; color: #6b7280; display: block; margin-bottom: .35rem; }
        select.inp { cursor: pointer; }
        textarea.inp { resize: vertical; min-height: 90px; }

        .s-section-title { font-family: 'Syne', sans-serif; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--mist); margin-bottom: .875rem; }
        .s-empty { text-align: center; padding: 3.5rem 2rem; }
        .s-empty-icon { width: 56px; height: 56px; border-radius: 14px; background: var(--bg); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; }
        .s-empty-icon svg { width: 24px; height: 24px; color: var(--mist); }
        .s-empty h4 { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--night); margin-bottom: .4rem; }
        .s-empty p { font-size: .84rem; color: var(--mist); line-height: 1.6; }

        /* ═══════════════════════════════════════════
         | RESPONSIVE TABLET (collapsed sidebar)
         ═══════════════════════════════════════════ */
        @media (min-width: 769px) and (max-width: 1024px) {
            :root { --sb-w: var(--sb-w-c); }
            .sb-brand, .sb-staff-name, .sb-staff-role, .sb-sec-lbl, .sb-lnk span, .sb-logout span { display: none !important; }
            .sb-logo { justify-content: center; padding: 1.1rem .625rem; }
            .sb-staff { justify-content: center; padding: .75rem .5rem; }
            .sb-nav { padding: .75rem .5rem; }
            .sb-lnk { justify-content: center; padding: .625rem; min-height: 44px; }
            .sb-lnk-icon { width: 36px; height: 36px; }
            .sb-lnk-icon svg { width: 17px; height: 17px; }
            .sb-lnk.on::after { display: none; }
            .sb-foot { padding: .625rem .5rem; }
            .sb-logout { padding: .625rem; justify-content: center; }
            .sb-logout-icon { width: 36px; height: 36px; }
            .hd-date { display: none; }

            /* Tooltips sidebar collapsed */
            .sb-lnk { position: relative; }
            .sb-lnk[data-tip]:hover::after {
                content: attr(data-tip);
                position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
                background: var(--night); color: var(--white); font-size: .75rem; font-weight: 600;
                padding: .35rem .7rem; border-radius: 7px; white-space: nowrap; pointer-events: none;
                box-shadow: 0 4px 16px rgba(0,0,0,.2); z-index: 100; border: 1px solid rgba(255,255,255,.08);
            }
            .sb-lnk[data-tip]:hover::before {
                content: ''; position: absolute; left: calc(100% + 8px); top: 50%; transform: translateY(-50%);
                border: 5px solid transparent; border-right-color: var(--night); pointer-events: none; z-index: 100;
            }
            .sb-logout { position: relative; }
            .sb-logout[data-tip]:hover::after {
                content: attr(data-tip);
                position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
                background: var(--night); color: var(--white); font-size: .75rem; font-weight: 600;
                padding: .35rem .7rem; border-radius: 7px; white-space: nowrap; pointer-events: none;
                box-shadow: 0 4px 16px rgba(0,0,0,.2); z-index: 100; border: 1px solid rgba(255,255,255,.08);
            }
            .sb-logout[data-tip]:hover::before {
                content: ''; position: absolute; left: calc(100% + 8px); top: 50%; transform: translateY(-50%);
                border: 5px solid transparent; border-right-color: var(--night); pointer-events: none; z-index: 100;
            }
        }

        /* ═══════════════════════════════════════════
         | RESPONSIVE MOBILE (sidebar off-canvas)
         ═══════════════════════════════════════════ */
        @media (max-width: 768px) {
            /* ✅ FIX: sidebar mobile complètement hors écran par défaut */
            .sb {
                transform: translateX(-100%);
                box-shadow: none;
                width: 280px; /* Légèrement plus large pour meilleur confort */
            }
            .sb.open {
                transform: translateX(0);
                box-shadow: 8px 0 48px rgba(0,0,0,.35);
            }
            /* ✅ FIX: overlay visible uniquement sur mobile */
            .ov { display: block; }
            /* ✅ FIX: header et main prennent toute la largeur */
            .hd { left: 0 !important; }
            .s-main { margin-left: 0 !important; }
            /* ✅ FIX: bouton burger visible */
            .hd-mbtn { display: flex; }
            .hd-sep { display: block; }
            /* ✅ FIX: bouton fermer sidebar visible */
            .sb-close { visibility: visible; }
            /* Textes sidebar toujours visibles sur mobile */
            .sb-brand, .sb-staff-name, .sb-staff-role, .sb-sec-lbl, .sb-lnk span, .sb-logout span { display: unset !important; }
            .sb-logo { justify-content: flex-start; padding: 1.25rem 1.125rem 1rem; }
            .sb-staff { justify-content: flex-start; padding: .875rem 1.125rem; }
            .sb-nav { padding: .875rem .75rem; }
            .sb-lnk { justify-content: flex-start; padding: .75rem .875rem; }
            .sb-lnk-icon { width: 34px; height: 34px; }
            .sb-lnk-icon svg { width: 16px; height: 16px; }
            .sb-lnk.on::after { display: block; }
            .sb-foot { padding: .875rem .75rem 1rem; }
            .sb-logout { padding: .75rem .875rem; justify-content: flex-start; }
            .sb-logout-icon { width: 34px; height: 34px; }
            .s-body { padding: 1rem; }
            .hd-date { display: flex; }
        }

        /* ══ Desktop toggle sidebar ══ */
        body.sb-collapsed { --sb-w: var(--sb-w-c); }
        body.sb-collapsed .sb { width: var(--sb-w-c); }
        body.sb-collapsed .hd { left: var(--sb-w-c); }
        body.sb-collapsed .s-main { margin-left: var(--sb-w-c); }
        body.sb-collapsed .sb-brand, body.sb-collapsed .sb-staff-name, body.sb-collapsed .sb-staff-role,
        body.sb-collapsed .sb-sec-lbl, body.sb-collapsed .sb-lnk span, body.sb-collapsed .sb-logout span { display: none !important; }
        body.sb-collapsed .sb-logo, body.sb-collapsed .sb-staff, body.sb-collapsed .sb-lnk, body.sb-collapsed .sb-logout { justify-content: center; }

        /* ══ Corrections mobiles pour les tableaux ══ */
        @media (max-width: 640px) {
            .s-tbl { font-size: .78rem; }
            .s-tbl thead th { padding: .5rem .75rem; font-size: .62rem; }
            .s-tbl tbody td { padding: .75rem .75rem; }
        }
    </style>
    @stack('styles')
</head>

<body>

{{-- Flash notifications --}}
@if(session('success'))
<div class="s-fl ok" id="fl-ok">
    <div class="s-fl-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
    <span style="flex:1;line-height:1.5">{{ session('success') }}</span>
    <button class="s-fl-close" onclick="this.closest('.s-fl').remove()"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
</div>
@endif
@if(session('error'))
<div class="s-fl ko" id="fl-ko">
    <div class="s-fl-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></div>
    <span style="flex:1;line-height:1.5">{{ session('error') }}</span>
    <button class="s-fl-close" onclick="this.closest('.s-fl').remove()"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
</div>
@endif

{{-- Overlay mobile --}}
{{-- ✅ FIX: onclick sur l'overlay ferme la sidebar --}}
<div class="ov" id="s-ov"></div>

@php
    $sU  = Auth::user();
    $sS  = $sU?->staff;
    $sI  = $sU?->institution;
    $ini = $sS
        ? strtoupper(mb_substr($sS->prenom,0,1).mb_substr($sS->nom,0,1))
        : strtoupper(mb_substr($sU?->name ?? 'S',0,2));
    $rol = $sU?->roles->pluck('name')->first() ?? 'staff';
    $mks = [];
    if ($sS) {
        $mks = isset($modulesActifs)
            ? $modulesActifs->pluck('key')->toArray()
            : $sS->modulesActifs()->pluck('key')->toArray();
    }
@endphp

{{-- ═══════ SIDEBAR ═══════ --}}
<aside class="sb" id="s-sb">
    {{-- Logo --}}
    <div class="sb-logo">
        <div class="sb-icon">
            @if($sI?->logo)
                <img src="{{ asset('storage/'.$sI->logo) }}" style="width:38px;height:38px;object-fit:cover;border-radius:10px" alt="">
            @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
            @endif
        </div>
        <div class="sb-brand">
            <div class="sb-brand-name">{{ Str::limit($sI->name ?? 'École', 18) }}</div>
            <div class="sb-brand-tag">Espace staff</div>
        </div>
        {{-- ✅ FIX: bouton fermer accessible et fonctionnel --}}
        <button class="sb-close" id="sb-close-btn" aria-label="Fermer le menu" type="button">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Carte staff --}}
    <div class="sb-staff">
        <div class="sb-avatar">{{ $ini }}</div>
        <div style="min-width:0;flex:1">
            <div class="sb-staff-name">{{ $sS ? $sS->prenom.' '.$sS->nom : $sU?->name }}</div>
            <div class="sb-staff-role">{{ ucfirst($rol) }} · {{ $sS?->poste ?? ($sS?->administrativeUnit?->name ?? '—') }}</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sb-nav">
        <div class="sb-sec">
            <div class="sb-sec-lbl">Principal</div>
            <a href="{{ route('staff.dashboard') }}" class="sb-lnk {{ request()->routeIs('staff.dashboard') ? 'on' : '' }}" data-tip="Tableau de bord">
                <span class="sb-lnk-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></span>
                <span>Tableau de bord</span>
            </a>
        </div>

        @if(count($mks) > 0)
        <div class="sb-sec">
            <div class="sb-sec-lbl">Mes tâches</div>

            @if(in_array('apprenants', $mks))
            <a href="{{ route('staff.apprenants') }}" class="sb-lnk {{ request()->routeIs('staff.apprenants*') ? 'on' : '' }}" data-tip="Apprenants">
                <span class="sb-lnk-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                <span>Apprenants</span>
            </a>
            @endif

            @if(in_array('finances', $mks))
            <a href="{{ route('staff.finances') }}" class="sb-lnk {{ request()->routeIs('staff.finances*') ? 'on' : '' }}" data-tip="Finances">
                <span class="sb-lnk-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                <span>Finances</span>
            </a>
            @endif

            @if(in_array('disciplinaire', $mks))
            <a href="{{ route('staff.disciplinaire') }}" class="sb-lnk {{ request()->routeIs('staff.disciplinaire*') ? 'on' : '' }}" data-tip="Disciplinaire">
                <span class="sb-lnk-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                <span>Disciplinaire</span>
            </a>
            @endif

            @if(in_array('planning', $mks))
            <a href="{{ route('staff.planning') }}" class="sb-lnk {{ request()->routeIs('staff.planning*') ? 'on' : '' }}" data-tip="Planning">
                <span class="sb-lnk-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                <span>Planning</span>
            </a>
            @endif

            @if(in_array('bibliotheque', $mks))
            <a href="{{ route('staff.library') }}" class="sb-lnk {{ request()->routeIs('staff.library*') ? 'on' : '' }}" data-tip="Bibliothèque">
                <span class="sb-lnk-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></span>
                <span>Bibliothèque</span>
            </a>
            @endif

            @if(in_array('rapports', $mks))
            <a href="{{ route('staff.rapports') }}" class="sb-lnk {{ request()->routeIs('staff.rapports*') ? 'on' : '' }}" data-tip="Rapports">
                <span class="sb-lnk-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></span>
                <span>Rapports</span>
            </a>
            @endif
        </div>
        @endif

        <div class="sb-sec">
            <div class="sb-sec-lbl">Mon compte</div>
            <a href="{{ route('staff.profil') }}" class="sb-lnk {{ request()->routeIs('staff.profil*') ? 'on' : '' }}" data-tip="Mon profil">
                <span class="sb-lnk-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                <span>Mon profil</span>
            </a>
        </div>
    </nav>

    {{-- Déconnexion --}}
    <div class="sb-foot">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-logout" data-tip="Déconnexion">
                <span class="sb-logout-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></span>
                <span>Déconnexion</span>
            </button>
        </form>
    </div>
</aside>

{{-- ═══════ HEADER ═══════ --}}
<header class="hd">
    <div class="hd-left">
        {{-- ✅ FIX: bouton burger avec id et type="button" explicite --}}
        <button class="hd-mbtn" id="sb-toggle-btn" type="button" aria-label="Ouvrir le menu">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="hd-sep"></div>
        <div class="hd-title-wrap">
            <div class="hd-title">@yield('page-title', 'Tableau de bord')</div>
            <div class="hd-breadcrumb">
                <span>{{ $sI->name ?? 'École' }}</span>
                <span class="sep">·</span>
                <span>@yield('page-sub', 'Espace staff')</span>
            </div>
        </div>
    </div>
    <div class="hd-right">
        <div class="hd-date">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ now()->locale('fr')->isoFormat('ddd D MMM YYYY') }}
        </div>
        <a href="{{ route('staff.profil') }}" class="hd-av" title="Mon profil">{{ $ini }}</a>
    </div>
</header>

{{-- ═══════ CONTENU ═══════ --}}
<main class="s-main">
    <div class="s-body">
        @yield('content')
    </div>
</main>

<script>
/* ═══════════════════════════════════════════════════════════
 | SIDEBAR LOGIC — VERSION CORRIGÉE
 |
 | BUGS corrigés :
 |   1. openSb() utilisait window.innerWidth <= 768 mais le CSS
 |      bascule à max-width:768px → à exactement 768px le burger
 |      ne fonctionnait pas. Fix : utiliser < 769 (ou matchMedia).
 |
 |   2. L'overlay .ov avait pointer-events actifs même fermé
 |      (opacity:0 ne désactive pas les clics). Fix : pointer-events:none
 |      par défaut, auto seulement quand .on.
 |
 |   3. Le click sur l'overlay n'appelait pas closeSb() car
 |      onclick était inline et parfois ignoré sur mobile Safari.
 |      Fix : addEventListener séparé.
 |
 |   4. Sur desktop en mode collapsed, le clic sur le burger
 |      ajoutait .open à la sidebar (comportement mobile) au lieu
 |      de toggler sb-collapsed. Fix : distinguer isMobile().
 ═══════════════════════════════════════════════════════════ */

const sbEl      = document.getElementById('s-sb');
const ovEl      = document.getElementById('s-ov');
const toggleBtn = document.getElementById('sb-toggle-btn');
const closeBtn  = document.getElementById('sb-close-btn');

// ✅ FIX 1 : détection mobile fiable
function isMobile() {
    return window.matchMedia('(max-width: 768px)').matches;
}

function openSbMobile() {
    sbEl.classList.add('open');
    ovEl.classList.add('on');
    document.body.style.overflow = 'hidden'; // empêche le scroll du fond
}

function closeSb() {
    sbEl.classList.remove('open');
    ovEl.classList.remove('on');
    document.body.style.overflow = '';
}

function toggleDesktop() {
    document.body.classList.toggle('sb-collapsed');
}

// ✅ FIX 2 : le bouton burger gère les deux contextes
toggleBtn.addEventListener('click', function(e) {
    e.stopPropagation(); // évite que le clic remonte et déclenche autre chose
    if (isMobile()) {
        openSbMobile();
    } else {
        toggleDesktop();
    }
});

// ✅ FIX 3 : bouton fermer (croix dans la sidebar)
if (closeBtn) {
    closeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        closeSb();
    });
}

// ✅ FIX 4 : overlay cliquable avec addEventListener (plus fiable que onclick inline)
ovEl.addEventListener('click', function() {
    closeSb();
});

// Fermer avec Échap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && sbEl.classList.contains('open')) {
        closeSb();
    }
});

// Fermer auto au redimensionnement vers desktop
window.addEventListener('resize', function() {
    if (!isMobile() && sbEl.classList.contains('open')) {
        closeSb();
    }
});

// ✅ FIX 5 : sur mobile, les liens de la sidebar ferment la sidebar avant navigation
// (évite que le scroll de la sidebar soit bloqué et rende les liens difficiles à cliquer)
if (isMobile() || true) { // on l'applique toujours
    sbEl.querySelectorAll('a.sb-lnk').forEach(function(link) {
        link.addEventListener('click', function() {
            // Ferme la sidebar sans preventDefault (laisse la navigation se faire)
            if (isMobile()) {
                closeSb();
            }
        });
    });
}

// Auto dismiss flash notifications
setTimeout(function() {
    document.querySelectorAll('.s-fl').forEach(function(el) {
        el.style.transition = 'opacity .4s ease, transform .4s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        setTimeout(function() { el.remove(); }, 400);
    });
}, 5000);
</script>

@stack('scripts')
</body>
</html>