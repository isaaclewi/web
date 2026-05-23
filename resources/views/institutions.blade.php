<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nos Établissements — SyntriForg Edu</title>
<link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
:root {
  --purple:  #7C3AED;
  --purple-d:#5B21B6;
  --purple-l:#F5F3FF;
  --blue:    #2563EB;
  --blue-l:  #EFF6FF;
  --ink:     #0A0B14;
  --ink2:    #1E293B;
  --muted:   #64748B;
  --divider: #E2E8F0;
  --white:   #FFFFFF;
  --offwhite:#F8FAFD;
  --grad:    linear-gradient(135deg,#7C3AED 0%,#2563EB 100%);
  --shadow-sm: 0 1px 4px rgba(10,11,20,.06);
  --shadow-md: 0 4px 24px rgba(10,11,20,.09);
  --shadow-lg: 0 16px 56px rgba(124,58,237,.15);
  --shadow-xl: 0 32px 80px rgba(10,11,20,.15);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--white);-webkit-font-smoothing:antialiased;overflow-x:hidden;cursor:none}
a{color:inherit;text-decoration:none}

/* CURSOR */
#cur{position:fixed;top:0;left:0;z-index:9999;pointer-events:none}
#cd{width:10px;height:10px;background:var(--purple);border-radius:50%;position:absolute;transform:translate(-50%,-50%);transition:transform .12s,background .2s}
#cr{width:40px;height:40px;border:1.5px solid rgba(124,58,237,.45);border-radius:50%;position:absolute;transform:translate(-50%,-50%);transition:width .25s,height .25s,border-color .25s}
body.hov #cr{width:65px;height:65px;border-color:var(--purple)}
body.hov #cd{transform:translate(-50%,-50%) scale(0)}

/* NOISE */
body::after{content:'';position:fixed;inset:0;z-index:9998;pointer-events:none;
background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='256' height='256'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='256' height='256' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
opacity:.018}

/* HEADER */
#header{position:fixed;top:0;left:0;right:0;z-index:1000;padding:0 2.5rem;height:62px;display:flex;align-items:center;justify-content:space-between;gap:1rem;background:rgba(255,255,255,.97);backdrop-filter:blur(18px);border-bottom:1px solid var(--divider);box-shadow:var(--shadow-sm)}
.logo-wrap{display:flex;align-items:center;gap:.55rem;flex-shrink:0;cursor:none}
.logo-mark{width:32px;height:32px;background:var(--grad);border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden}
.logo-mark img{width:100%;height:100%;object-fit:cover}
.logo-text{font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;letter-spacing:-.01em;color:var(--ink)}
.logo-text span{color:var(--purple)}

/* Desktop nav */
.header-nav{display:flex;align-items:center;gap:.25rem}
.header-nav a{font-family:'Syne',sans-serif;font-size:.67rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);padding:.4rem .7rem;border-radius:7px;transition:color .2s,background .2s;cursor:none;white-space:nowrap}
.header-nav a:hover,.header-nav a.active{color:var(--purple);background:var(--purple-l)}
.header-cta{background:var(--grad)!important;color:#fff!important;border-radius:100px!important;padding:.4rem 1.1rem!important;transition:transform .2s,box-shadow .2s!important}
.header-cta:hover{transform:translateY(-2px)!important;box-shadow:0 8px 24px rgba(124,58,237,.4)!important}

/* Burger button */
.burger{display:none;flex-direction:column;justify-content:center;gap:5px;width:38px;height:38px;padding:.5rem;cursor:none;border:1px solid var(--divider);border-radius:8px;background:transparent;flex-shrink:0}
.burger span{display:block;width:18px;height:1.5px;background:var(--ink2);border-radius:2px;transition:.3s ease;transform-origin:center}
.burger.open span:nth-child(1){transform:translateY(6.5px) rotate(45deg)}
.burger.open span:nth-child(2){opacity:0;transform:scaleX(0)}
.burger.open span:nth-child(3){transform:translateY(-6.5px) rotate(-45deg)}

/* Mobile drawer */
.mobile-nav{position:fixed;top:62px;left:0;right:0;background:rgba(255,255,255,.99);backdrop-filter:blur(20px);border-bottom:1px solid var(--divider);box-shadow:0 12px 32px rgba(10,11,20,.1);z-index:998;padding:1rem 1.5rem 1.5rem;flex-direction:column;gap:.2rem;display:none}
.mobile-nav.open{display:flex}
.mobile-nav a{font-family:'Syne',sans-serif;font-size:.78rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--ink2);padding:.7rem .9rem;border-radius:10px;transition:color .18s,background .18s;display:block}
.mobile-nav a:hover,.mobile-nav a.active{color:var(--purple);background:var(--purple-l)}
.mobile-nav .m-cta{background:var(--grad);color:#fff!important;border-radius:100px;text-align:center;margin-top:.6rem;padding:.75rem 1.25rem}

/* PAGE HERO */
.page-hero{padding:9rem 4rem 5rem;background:linear-gradient(160deg,rgba(245,243,255,.9) 0%,rgba(239,246,255,.6) 60%,rgba(255,255,255,.9) 100%);position:relative;overflow:hidden;text-align:center}
.page-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(124,58,237,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(124,58,237,.04) 1px,transparent 1px);background-size:60px 60px;mask-image:radial-gradient(ellipse at 50% 50%,black 20%,transparent 70%)}
.page-hero-glow{position:absolute;top:30%;left:50%;transform:translateX(-50%);width:600px;height:300px;background:radial-gradient(ellipse,rgba(124,58,237,.08) 0%,transparent 70%);pointer-events:none}
.stag{display:inline-flex;align-items:center;gap:.5rem;font-family:'Syne',sans-serif;font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;margin-bottom:1rem;color:var(--purple);position:relative;z-index:1}
.stag::before{content:'';width:24px;height:1.5px;background:var(--purple)}
h1.page-title{font-family:'Instrument Serif',serif;font-size:clamp(2.4rem,5vw,4.2rem);line-height:.95;letter-spacing:-.02em;color:var(--ink);position:relative;z-index:1}
h1.page-title em{font-style:italic}
h1.page-title .grad{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.page-sub{font-size:.92rem;font-weight:300;line-height:1.8;color:var(--muted);max-width:520px;margin:.8rem auto 0;position:relative;z-index:1}

/* SEARCH */
.search-wrap{max-width:600px;margin:2.5rem auto 0;position:relative;z-index:2}
.search-inner{position:relative}
.search-icon{position:absolute;left:1.2rem;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none}
.search-icon svg{width:17px;height:17px}
.search-input{width:100%;background:#fff;border:1.5px solid var(--divider);border-radius:100px;padding:.875rem 1.25rem .875rem 3.1rem;font-family:'DM Sans',sans-serif;font-size:.9rem;color:var(--ink);transition:all .25s;box-shadow:var(--shadow-md);cursor:text}
.search-input:focus{outline:none;border-color:rgba(124,58,237,.4);box-shadow:0 0 0 4px rgba(124,58,237,.08),var(--shadow-md)}
.search-input::placeholder{color:#94a3b8}
.search-count{font-family:'Syne',sans-serif;font-size:.7rem;font-weight:600;color:var(--muted);text-align:center;margin-top:.7rem;letter-spacing:.06em;text-transform:uppercase}
.search-count strong{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}

/* MAIN SECTION */
.institutions-section{padding:4rem 4rem 7rem;background:var(--offwhite)}
.container{max-width:1320px;margin:0 auto}

/* CATEGORY BLOCK */
.cat-block{margin-bottom:4rem}
.cat-block:last-child{margin-bottom:0}
.cat-header{display:flex;align-items:center;gap:.85rem;margin-bottom:1.75rem}
.cat-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
.cat-icon.uni  {background:linear-gradient(135deg,rgba(124,58,237,.12),rgba(37,99,235,.12))}
.cat-icon.sec  {background:linear-gradient(135deg,rgba(5,150,105,.12),rgba(16,185,129,.12))}
.cat-icon.pri  {background:linear-gradient(135deg,rgba(245,158,11,.12),rgba(239,68,68,.12))}
.cat-icon.form {background:linear-gradient(135deg,rgba(14,165,233,.12),rgba(99,102,241,.12))}
.cat-icon.other{background:#f1f5f9}
.cat-title{font-family:'Instrument Serif',serif;font-size:1.45rem;color:var(--ink);line-height:1}
.cat-title em{font-style:italic}
.cat-line{flex:1;height:1px;background:var(--divider)}
.cat-count{font-family:'Syne',sans-serif;font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);background:#fff;border:1px solid var(--divider);border-radius:100px;padding:.22rem .7rem;flex-shrink:0}

/* CARD GRID */
.inst-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.2rem}

/* CARD */
.inst-card{background:var(--white);border:1px solid var(--divider);border-radius:22px;overflow:hidden;cursor:none;transition:all .3s cubic-bezier(.4,0,.2,1);position:relative;display:flex;flex-direction:column;align-items:center;padding:1.85rem 1.25rem 1.4rem;text-align:center}
.inst-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--grad);transform:scaleX(0);transform-origin:left;transition:transform .35s}
.inst-card:hover{border-color:transparent;box-shadow:var(--shadow-lg);transform:translateY(-7px)}
.inst-card:hover::before{transform:scaleX(1)}

/* LOGO */
.inst-logo-wrap{width:78px;height:78px;border-radius:20px;overflow:hidden;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;flex-shrink:0;box-shadow:var(--shadow-md);background:#fff;position:relative}
.inst-logo-img{width:100%;height:100%;object-fit:cover;display:block;position:absolute;inset:0}
.inst-logo-initial{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:#fff;background:var(--grad)}

.inst-name{font-family:'Syne',sans-serif;font-size:.86rem;font-weight:700;color:var(--ink);margin-bottom:.22rem;line-height:1.3}
.inst-type-badge{display:inline-block;font-family:'Syne',sans-serif;font-size:.56rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.16rem .55rem;border-radius:100px;background:var(--purple-l);color:var(--purple);margin-bottom:.5rem}
.inst-location{display:flex;align-items:center;gap:.28rem;justify-content:center;font-size:.68rem;color:var(--muted)}
.inst-location svg{width:10px;height:10px;flex-shrink:0;color:var(--purple)}
.inst-pills{display:flex;flex-wrap:wrap;gap:.28rem;justify-content:center;margin-top:.65rem}
.inst-pill{font-family:'Syne',sans-serif;font-size:.54rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:.16rem .5rem;border-radius:100px;background:var(--purple-l);color:var(--purple)}
.inst-pill.blue {background:var(--blue-l);color:var(--blue)}
.inst-pill.green{background:#ecfdf5;color:#059669}

/* EMPTY */
.global-empty{text-align:center;padding:5rem 2rem}
.global-empty .ei{font-size:3.5rem;display:block;margin-bottom:1rem;opacity:.35}
.global-empty .et{font-family:'Instrument Serif',serif;font-size:1.8rem;color:var(--ink);margin-bottom:.5rem}
.global-empty .es{font-size:.88rem;color:var(--muted)}

/* MODAL */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(10,11,20,.62);z-index:2000;align-items:center;justify-content:center;padding:1.25rem;backdrop-filter:blur(6px)}
.modal-bg.open{display:flex}
.modal{background:#fff;border-radius:28px;width:100%;max-width:700px;max-height:92vh;overflow-y:auto;position:relative;box-shadow:var(--shadow-xl);animation:popIn .35s cubic-bezier(.34,1.56,.64,1)}
@keyframes popIn{from{opacity:0;transform:scale(.92) translateY(18px)}to{opacity:1;transform:none}}
.modal::-webkit-scrollbar{width:4px}
.modal::-webkit-scrollbar-thumb{background:var(--divider);border-radius:2px}

.modal-hero{padding:2.2rem 2.2rem 1.4rem;background:linear-gradient(135deg,rgba(245,243,255,.8),rgba(239,246,255,.6));border-bottom:1px solid var(--divider);display:flex;align-items:flex-start;gap:1.4rem;position:relative}
.modal-close-btn{position:absolute;top:1.1rem;right:1.1rem;width:32px;height:32px;border-radius:50%;background:#f1f5f9;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);transition:all .18s;font-size:.85rem}
.modal-close-btn:hover{background:var(--divider);color:var(--ink)}
.modal-logo-lg{width:68px;height:68px;border-radius:17px;overflow:hidden;flex-shrink:0;box-shadow:var(--shadow-md);background:#fff;position:relative}
.modal-logo-lg img{width:100%;height:100%;object-fit:cover;display:block;position:absolute;inset:0}
.modal-logo-init{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:1.35rem;font-weight:800;color:#fff;background:var(--grad)}
.modal-title{font-family:'Instrument Serif',serif;font-size:1.5rem;line-height:1.1;color:var(--ink);margin-bottom:.18rem}
.modal-type-badge{display:inline-block;font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;background:var(--grad);color:#fff;padding:.18rem .65rem;border-radius:100px;margin-bottom:.45rem}
.modal-loc{font-size:.76rem;color:var(--muted);display:flex;align-items:center;gap:.32rem;flex-wrap:wrap}
.modal-loc svg{width:12px;height:12px;color:var(--purple);flex-shrink:0}

.modal-stats{display:grid;grid-template-columns:repeat(5,1fr);border-bottom:1px solid var(--divider)}
.mstat{padding:.85rem .4rem;text-align:center;border-right:1px solid var(--divider)}
.mstat:last-child{border-right:none}
.mstat-num{font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.mstat-label{font-size:.58rem;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-top:.08rem}

.modal-body{padding:1.55rem 2.2rem 1.9rem}
.modal-sec{font-family:'Syne',sans-serif;font-size:.6rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--purple);margin-bottom:.75rem;display:flex;align-items:center;gap:.5rem}
.modal-sec::after{content:'';flex:1;height:1px;background:var(--divider)}

.minfo-grid{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1.35rem}
.minfo{background:var(--offwhite);border:1px solid var(--divider);border-radius:10px;padding:.65rem .85rem}
.minfo-key{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)}
.minfo-val{font-size:.82rem;font-weight:500;color:var(--ink);margin-top:.1rem;word-break:break-word}
.minfo-val a{color:var(--purple);text-decoration:underline;text-underline-offset:3px}
.minfo.full{grid-column:span 2}

.tags-wrap{display:flex;flex-wrap:wrap;gap:.38rem;margin-bottom:1.35rem}
.tag{font-family:'Syne',sans-serif;font-size:.64rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;padding:.24rem .68rem;border-radius:100px;border:1px solid var(--divider);color:var(--ink2);background:#fff}

.classes-list{display:flex;flex-direction:column;gap:.4rem;max-height:230px;overflow-y:auto;padding-right:3px;margin-bottom:1.35rem}
.classes-list::-webkit-scrollbar{width:3px}
.classes-list::-webkit-scrollbar-thumb{background:var(--divider);border-radius:2px}
.class-row{display:flex;align-items:center;justify-content:space-between;background:var(--offwhite);border:1px solid var(--divider);border-radius:9px;padding:.5rem .85rem;gap:.65rem}
.class-row-name{font-size:.78rem;font-weight:600;color:var(--ink);min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.class-row-meta{display:flex;gap:.3rem;flex-shrink:0;align-items:center;flex-wrap:wrap}
.cmp{font-family:'Syne',sans-serif;font-size:.54rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:.14rem .48rem;border-radius:100px}
.cmp-p{background:var(--purple-l);color:var(--purple)}
.cmp-b{background:var(--blue-l);color:var(--blue)}
.cmp-g{background:#ecfdf5;color:#059669}
.no-data{font-size:.78rem;color:var(--muted);text-align:center;padding:1rem}

.modal-website-btn{display:inline-flex;align-items:center;gap:.48rem;font-family:'Syne',sans-serif;font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;background:var(--grad);color:#fff;padding:.62rem 1.3rem;border-radius:100px;border:none;cursor:pointer;transition:.25s;text-decoration:none;margin-top:.2rem}
.modal-website-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(124,58,237,.4)}
.modal-website-btn svg{width:12px;height:12px}

.spinner{width:34px;height:34px;border:3px solid var(--divider);border-top-color:var(--purple);border-radius:50%;animation:spin .7s linear infinite;margin:3rem auto}
@keyframes spin{to{transform:rotate(360deg)}}

/* REVEAL */
.rv{opacity:0;transform:translateY(20px);transition:opacity .62s ease,transform .62s ease}
.rv.in{opacity:1;transform:translateY(0)}
.d1{transition-delay:.07s}.d2{transition-delay:.14s}.d3{transition-delay:.21s}
.d4{transition-delay:.28s}.d5{transition-delay:.35s}.d6{transition-delay:.42s}

/* RESPONSIVE */
@media(max-width:960px){
  .header-nav{display:none}
  .burger{display:flex}
  .institutions-section{padding:3rem 2rem 5rem}
  .page-hero{padding:8.5rem 2rem 4rem}
}
@media(max-width:640px){
  #header{padding:0 1.1rem;height:56px}
  .mobile-nav{top:56px}
  .page-hero{padding:7.5rem 1.25rem 3.5rem}
  .institutions-section{padding:2.5rem 1.25rem 4rem}
  .inst-grid{grid-template-columns:repeat(auto-fill,minmax(155px,1fr));gap:.9rem}
  .inst-card{padding:1.5rem .9rem 1.2rem}
  .inst-logo-wrap{width:66px;height:66px}
  .modal-hero{flex-direction:column;align-items:center;text-align:center;padding:2rem 1.4rem 1.2rem}
  .modal-loc{justify-content:center}
  .modal-stats{grid-template-columns:repeat(3,1fr)}
  .mstat:nth-child(4){border-right:1px solid var(--divider)}
  .modal-body{padding:1.3rem 1.4rem 1.7rem}
  .minfo-grid{grid-template-columns:1fr}
  .minfo.full{grid-column:span 1}
  body{cursor:auto}
  #cur{display:none}
}
@media(max-width:400px){
  .inst-grid{grid-template-columns:1fr 1fr}
}
</style>
</head>
<body>

<div id="cur"><div id="cd"></div><div id="cr"></div></div>

<!-- HEADER -->
<header id="header">
  <a href="{{ route('home') }}" class="logo-wrap">
    <div class="logo-mark"><img src="/medias/Syntriforg[1].png" alt="SyntriForge"></div>
    <div class="logo-text">SyntriForg <span>Edu</span></div>
  </a>

  <nav class="header-nav">
    <a href="{{ route('home') }}">Accueil</a>
    <a href="{{ route('home') }}#fonctionnalites">Fonctionnalités</a>
    <a href="{{ route('institutions.index') }}" class="active">Établissements</a>
    <a href="{{ route('home') }}#cta">Contact</a>
    <a href="{{ route('login') }}" class="header-cta">Connexion</a>
  </nav>

  <button class="burger" id="burgerBtn" aria-label="Ouvrir le menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
</header>

<!-- Mobile nav -->
<nav class="mobile-nav" id="mobileNav" aria-label="Navigation mobile">
  <a href="{{ route('home') }}">Accueil</a>
  <a href="{{ route('home') }}#fonctionnalites">Fonctionnalités</a>
  <a href="{{ route('institutions.index') }}" class="active">Établissements</a>
  <a href="{{ route('home') }}#cta">Contact</a>
  <a href="{{ route('login') }}" class="m-cta">Connexion</a>
</nav>

<!-- PAGE HERO -->
<section class="page-hero">
  <div class="page-hero-glow"></div>
  <div class="stag rv">Réseau académique</div>
  <h1 class="page-title rv d1">Nos <em><span class="grad">établissements</span></em><br>partenaires</h1>
  <p class="page-sub rv d2">Découvrez les institutions qui font confiance à SyntriForg Edu pour transformer leur gestion académique.</p>

  <div class="search-wrap rv d3">
    <div class="search-inner">
      <span class="search-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
        </svg>
      </span>
      <input type="text" id="searchInput" class="search-input" placeholder="Rechercher par nom, pays, ville, type…" autocomplete="off">
    </div>
    <p class="search-count" id="searchCount">
      <strong id="countNum">{{ $institutions->count() }}</strong>
      établissement{{ $institutions->count() > 1 ? 's' : '' }} au total
    </p>
  </div>
</section>

<!-- GRID PAR CATÉGORIE -->
<section class="institutions-section">
  <div class="container" id="mainContainer">

    @php
      $catConfig = [
        'universite'   => ['label' => 'Universités & Enseignement Supérieur', 'icon' => '🎓', 'cls' => 'uni'],
        'lycee'        => ['label' => 'Lycées',                                'icon' => '🏫', 'cls' => 'sec'],
        'college'      => ['label' => 'Collèges',                             'icon' => '📚', 'cls' => 'sec'],
        'secondaire'   => ['label' => 'Enseignement Secondaire',              'icon' => '📖', 'cls' => 'sec'],
        'primaire'     => ['label' => 'Enseignement Primaire',                'icon' => '✏️', 'cls' => 'pri'],
        'maternelle'   => ['label' => 'Maternelle & Préscolaire',             'icon' => '🌱', 'cls' => 'pri'],
        'formation'    => ['label' => 'Centres de Formation',                 'icon' => '🛠️', 'cls' => 'form'],
        'professionnel'=> ['label' => 'Enseignement Professionnel',           'icon' => '⚙️', 'cls' => 'form'],
        'institut'     => ['label' => 'Instituts Spécialisés',                'icon' => '🔬', 'cls' => 'form'],
        'autre'        => ['label' => 'Autres Établissements',                'icon' => '🏛️', 'cls' => 'other'],
      ];

      $grouped = $institutions->groupBy(function($inst) {
        $raw = strtolower(trim($inst->type ?? ''));
        if(str_contains($raw,'univ')||str_contains($raw,'sup')||str_contains($raw,'érieur')) return 'universite';
        if(str_contains($raw,'lyc'))                                                          return 'lycee';
        if(str_contains($raw,'coll'))                                                         return 'college';
        if(str_contains($raw,'sec'))                                                          return 'secondaire';
        if(str_contains($raw,'prim')||str_contains($raw,'élém'))                             return 'primaire';
        if(str_contains($raw,'mat')||str_contains($raw,'presc'))                             return 'maternelle';
        if(str_contains($raw,'form')||str_contains($raw,'centre'))                           return 'formation';
        if(str_contains($raw,'prof'))                                                         return 'professionnel';
        if(str_contains($raw,'inst'))                                                         return 'institut';
        return 'autre';
      });

      $sortedGroups = collect(array_keys($catConfig))
        ->filter(fn($k) => $grouped->has($k))
        ->mapWithKeys(fn($k) => [$k => $grouped[$k]]);
    @endphp

    @if($institutions->isEmpty())
      <div class="global-empty">
        <span class="ei">🏫</span>
        <div class="et">Aucun établissement enregistré</div>
        <div class="es">Les établissements partenaires apparaîtront ici dès leur inscription.</div>
      </div>
    @else
      @foreach($sortedGroups as $catKey => $group)
        @php $cfg = $catConfig[$catKey]; @endphp
        <div class="cat-block rv" data-cat="{{ $catKey }}">

          <div class="cat-header">
            <div class="cat-icon {{ $cfg['cls'] }}">{{ $cfg['icon'] }}</div>
            <h2 class="cat-title">{{ $cfg['label'] }}</h2>
            <div class="cat-line"></div>
            <div class="cat-count">{{ $group->count() }} établissement{{ $group->count() > 1 ? 's' : '' }}</div>
          </div>

          <div class="inst-grid">
            @foreach($group as $idx => $inst)
              @php
                $initials = strtoupper(
                  mb_substr($inst->name, 0, 1) .
                  mb_substr(preg_split('/\s+/', trim($inst->name))[1] ?? $inst->name, 0, 1)
                );
                $loc = collect([$inst->commune, $inst->pays])->filter()->join(', ');
                $delay = 'd'.min($idx + 1, 6);
                /* ── URL du logo ──
                   root_storage : root = base_path('storage'), url = APP_URL.'/storage'
                   mais les fichiers sont stockés sous logos/institutions/xxx
                   donc l'URL publique = asset($inst->logo)  (= APP_URL + '/' + logo)
                   Si ton hébergeur expose /storage/ directement, utilise asset('storage/'.$inst->logo)
                   Ici on utilise asset($inst->logo) conforme à la config root_storage.
                */
                $logoUrl = $inst->logo ? asset($inst->logo) : null;
              @endphp

              <div class="inst-card rv {{ $delay }}"
                   data-id="{{ $inst->id }}"
                   data-search="{{ strtolower($inst->name.' '.($inst->pays??'').' '.($inst->commune??'').' '.($inst->type??'')) }}"
                   onclick="openModal({{ $inst->id }})">

                <!-- LOGO (réel ou initiales) -->
                <div class="inst-logo-wrap">
                  @if($logoUrl)
                    {{-- Image réelle : si elle charge → on la garde, sinon fallback initiales --}}
                    <img class="inst-logo-img"
                         src="{{ $logoUrl }}"
                         alt="{{ $inst->name }}"
                         onload="this.nextElementSibling.style.display='none'"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="inst-logo-initial">{{ $initials }}</div>
                  @else
                    <div class="inst-logo-initial">{{ $initials }}</div>
                  @endif
                </div>

                <div class="inst-name">{{ $inst->name }}</div>

                @if($inst->type)
                  <div class="inst-type-badge">{{ ucfirst($inst->type) }}</div>
                @endif

                @if($loc)
                  <div class="inst-location">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $loc }}
                  </div>
                @endif

                <div class="inst-pills">
                  @if($inst->apprenants_count)
                    <span class="inst-pill">{{ $inst->apprenants_count }} élève{{ $inst->apprenants_count > 1 ? 's' : '' }}</span>
                  @endif
                  @if($inst->niveaux_count)
                    <span class="inst-pill blue">{{ $inst->niveaux_count }} niveau{{ $inst->niveaux_count > 1 ? 'x' : '' }}</span>
                  @endif
                  @if($inst->academic_year)
                    <span class="inst-pill green">{{ $inst->academic_year }}</span>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    @endif

    <!-- Message vide dynamique (recherche live) -->
    <div id="noResultsMsg" style="display:none" class="global-empty">
      <span class="ei">🔍</span>
      <div class="et">Aucun résultat</div>
      <div class="es" id="noResultsSub">Aucun établissement ne correspond à votre recherche.</div>
    </div>

  </div>
</section>

<!-- MODAL -->
<div class="modal-bg" id="modalBg" onclick="handleBgClick(event)">
  <div class="modal" id="modalBox">
    <div id="modalContent">
      <div style="padding:3rem"><div class="spinner"></div></div>
    </div>
  </div>
</div>

<script>
/* ── CURSOR ── */
const cd=document.getElementById('cd'),cr=document.getElementById('cr');
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cd.style.left=mx+'px';cd.style.top=my+'px'});
(function loop(){rx+=(mx-rx)*.1;ry+=(my-ry)*.1;cr.style.left=rx+'px';cr.style.top=ry+'px';requestAnimationFrame(loop)})();
function bindHover(root=document){
  root.querySelectorAll('a,button,.inst-card').forEach(el=>{
    el.addEventListener('mouseenter',()=>document.body.classList.add('hov'));
    el.addEventListener('mouseleave',()=>document.body.classList.remove('hov'));
  });
}
bindHover();

/* ── SCROLL REVEAL ── */
const revObs=new IntersectionObserver(entries=>{
  entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');revObs.unobserve(e.target)}});
},{threshold:.07});
document.querySelectorAll('.rv').forEach(el=>revObs.observe(el));

/* ── BURGER / MOBILE NAV ── */
const burgerBtn=document.getElementById('burgerBtn');
const mobileNav=document.getElementById('mobileNav');
burgerBtn.addEventListener('click',()=>{
  const isOpen=mobileNav.classList.toggle('open');
  burgerBtn.classList.toggle('open',isOpen);
  burgerBtn.setAttribute('aria-expanded',isOpen);
  document.body.style.overflow=isOpen?'hidden':'';
});
// Fermer au clic sur un lien mobile
mobileNav.querySelectorAll('a').forEach(a=>{
  a.addEventListener('click',()=>{
    mobileNav.classList.remove('open');
    burgerBtn.classList.remove('open');
    burgerBtn.setAttribute('aria-expanded','false');
    document.body.style.overflow='';
  });
});
// Fermer au scroll
window.addEventListener('scroll',()=>{
  if(mobileNav.classList.contains('open')){
    mobileNav.classList.remove('open');
    burgerBtn.classList.remove('open');
    burgerBtn.setAttribute('aria-expanded','false');
    document.body.style.overflow='';
  }
},{passive:true});
// Fermer à Escape
document.addEventListener('keydown',e=>{
  if(e.key==='Escape'){
    mobileNav.classList.remove('open');
    burgerBtn.classList.remove('open');
    burgerBtn.setAttribute('aria-expanded','false');
    document.body.style.overflow='';
    closeModal();
  }
});

/* ── SEARCH LIVE ── */
const searchInput=document.getElementById('searchInput');
const catBlocks=[...document.querySelectorAll('.cat-block')];
const countNumEl=document.getElementById('countNum');
const noResultsMsg=document.getElementById('noResultsMsg');
const noResultsSub=document.getElementById('noResultsSub');

searchInput.addEventListener('input',()=>{
  const q=searchInput.value.toLowerCase().trim();
  let total=0;
  catBlocks.forEach(block=>{
    const cards=[...block.querySelectorAll('.inst-card')];
    let vis=0;
    cards.forEach(c=>{
      const match=!q||c.dataset.search.includes(q);
      c.style.display=match?'':'none';
      if(match)vis++;
    });
    block.style.display=vis>0?'':'none';
    total+=vis;
  });
  countNumEl.textContent=total;
  document.getElementById('searchCount').innerHTML=
    `<strong id="countNum">${total}</strong> établissement${total>1?'s':''} trouvé${total>1?'s':''}`;
  noResultsMsg.style.display=total===0&&q?'block':'none';
  if(q)noResultsSub.textContent=`Aucun établissement ne correspond à « ${searchInput.value} ».`;
});

/* ── MODAL ── */
async function openModal(id){
  const bg=document.getElementById('modalBg');
  bg.classList.add('open');
  document.body.style.overflow='hidden';
  document.getElementById('modalContent').innerHTML='<div style="padding:3rem"><div class="spinner"></div></div>';
  try{
    const r=await fetch(`/institutions/${id}/data`);
    if(!r.ok)throw new Error();
    renderModal(await r.json());
  }catch{
    document.getElementById('modalContent').innerHTML=
      '<div style="padding:3rem;text-align:center;color:#64748b;font-size:.875rem">Impossible de charger les données.</div>';
  }
}

function renderModal(d){
  const inst=d.institution,stats=d.stats,classes=d.classes;

  // ── Logo dans le modal ──
  const initials=(inst.name.charAt(0)+(inst.name.trim().split(/\s+/)[1]?.charAt(0)||inst.name.charAt(1)||'E')).toUpperCase();
  const logoUrl=inst.logo?assetUrl(inst.logo):null;
  const logoHtml=logoUrl
    ?`<img src="${logoUrl}" alt="${esc(inst.name)}"
        style="width:100%;height:100%;object-fit:cover;display:block;position:absolute;inset:0"
        onload="this.nextElementSibling.style.display='none'"
        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
       <div class="modal-logo-init" style="">${initials}</div>`
    :`<div class="modal-logo-init">${initials}</div>`;

  const location=[inst.commune,inst.departement,inst.pays].filter(Boolean).join(', ');

  const statsHtml=`<div class="modal-stats">
    <div class="mstat"><div class="mstat-num">${stats.apprenants}</div><div class="mstat-label">Élèves</div></div>
    <div class="mstat"><div class="mstat-num">${stats.teachers}</div><div class="mstat-label">Profs</div></div>
    <div class="mstat"><div class="mstat-num">${stats.classes}</div><div class="mstat-label">Classes</div></div>
    <div class="mstat"><div class="mstat-num">${stats.niveaux}</div><div class="mstat-label">Niveaux</div></div>
    <div class="mstat"><div class="mstat-num">${stats.filieres}</div><div class="mstat-label">Filières</div></div>
  </div>`;

  const rows=[
    ['Année académique',inst.academic_year||'—'],
    ['Statut juridique',inst.statut_juridique||'—'],
    ['Date de création',inst.date_creation||'—'],
    ['Devise',inst.devise||'—'],
    inst.email?['E-mail',`<a href="mailto:${esc(inst.email)}">${esc(inst.email)}</a>`]:null,
    inst.telephone?['Téléphone',esc(inst.telephone)]:null,
    inst.adresse?['Adresse',esc(inst.adresse),true]:null,
  ].filter(Boolean);
  const infoHtml=rows.map(([k,v,full])=>
    `<div class="minfo${full?' full':''}"><div class="minfo-key">${k}</div><div class="minfo-val">${v}</div></div>`
  ).join('');

  const niveauxHtml=inst.niveaux?.length
    ?inst.niveaux.map(n=>`<span class="tag">${esc(n.name)}</span>`).join('')
    :'<span class="no-data" style="padding:0">Aucun niveau configuré</span>';

  const filieresHtml=inst.filieres?.length
    ?inst.filieres.map(f=>`<span class="tag">${esc(f.name)}</span>`).join('')
    :'<span class="no-data" style="padding:0">Aucune filière configurée</span>';

  const classesHtml=classes.length
    ?classes.map(c=>`
      <div class="class-row">
        <div class="class-row-name">${esc(c.name)}</div>
        <div class="class-row-meta">
          ${c.niveau?`<span class="cmp cmp-p">${esc(c.niveau.name)}</span>`:''}
          ${c.filiere?`<span class="cmp cmp-b">${esc(c.filiere.name)}</span>`:''}
          <span class="cmp cmp-g">${c.apprenants_count} élève${c.apprenants_count>1?'s':''}</span>
        </div>
      </div>`).join('')
    :'<div class="no-data">Aucune classe enregistrée</div>';

  const websiteHtml=inst.site_web
    ?`<div style="margin-top:1.35rem">
        <a href="${esc(inst.site_web)}" target="_blank" rel="noopener" class="modal-website-btn">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
          </svg>
          Visiter le site web
        </a>
      </div>` :'';

  document.getElementById('modalContent').innerHTML=`
    <button class="modal-close-btn" onclick="closeModal()">✕</button>
    <div class="modal-hero">
      <div class="modal-logo-lg" style="position:relative">${logoHtml}</div>
      <div style="min-width:0;flex:1">
        <div class="modal-type-badge">${esc(inst.type||'Établissement')}</div>
        <div class="modal-title">${esc(inst.name)}</div>
        ${location?`<div class="modal-loc">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>${esc(location)}</div>`:''}
      </div>
    </div>
    ${statsHtml}
    <div class="modal-body">
      <div class="modal-sec">Informations générales</div>
      <div class="minfo-grid">${infoHtml}</div>
      <div class="modal-sec">Niveaux</div>
      <div class="tags-wrap">${niveauxHtml}</div>
      <div class="modal-sec">Filières</div>
      <div class="tags-wrap">${filieresHtml}</div>
      <div class="modal-sec">Classes (${classes.length})</div>
      <div class="classes-list">${classesHtml}</div>
      ${websiteHtml}
    </div>`;

  bindHover(document.getElementById('modalContent'));
}

function closeModal(){
  document.getElementById('modalBg').classList.remove('open');
  document.body.style.overflow='';
}
function handleBgClick(e){if(e.target===document.getElementById('modalBg'))closeModal();}

/* ── HELPERS ── */
function esc(s){const d=document.createElement('div');d.textContent=s??'';return d.innerHTML;}
function assetUrl(path){
  if(!path)return null;
  if(path.startsWith('http'))return path;
  /* root_storage disk : url = APP_URL + '/storage'
     fichier stocké ex: 'logos/institutions/abc.jpg'
     → URL publique = APP_URL + '/logos/institutions/abc.jpg'
     → asset($inst->logo) côté PHP donne APP_URL + '/' + path
     Donc en JS on fait simplement : */
  return window.location.origin+'/'+path;
}
</script>
</body>
</html>