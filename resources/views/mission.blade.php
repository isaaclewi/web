<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notre Mission — SyntriForg Edu</title>
<link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
:root{
  --purple:#7C3AED;--purple-d:#5B21B6;--purple-l:#F5F3FF;
  --blue:#2563EB;--blue-l:#EFF6FF;
  --ink:#0A0B14;--ink2:#1E293B;--muted:#64748B;
  --divider:#E2E8F0;--white:#FFFFFF;--offwhite:#F8FAFD;
  --grad:linear-gradient(135deg,#7C3AED 0%,#2563EB 100%);
  --shadow-sm:0 1px 4px rgba(10,11,20,.06);
  --shadow-md:0 4px 24px rgba(10,11,20,.09);
  --shadow-lg:0 16px 56px rgba(124,58,237,.15);
  --shadow-xl:0 32px 80px rgba(10,11,20,.15);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--white);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden;cursor:none}
img{display:block;max-width:100%}
a{color:inherit;text-decoration:none}
ul{list-style:none}

#cur{position:fixed;top:0;left:0;z-index:9999;pointer-events:none}
#cd{width:10px;height:10px;background:var(--purple);border-radius:50%;position:absolute;transform:translate(-50%,-50%);transition:transform .12s}
#cr{width:40px;height:40px;border:1.5px solid rgba(124,58,237,.45);border-radius:50%;position:absolute;transform:translate(-50%,-50%);transition:width .25s,height .25s,border-color .25s}
body.hov #cr{width:65px;height:65px;border-color:var(--purple)}
body.hov #cd{transform:translate(-50%,-50%) scale(0)}
body::after{content:'';position:fixed;inset:0;z-index:9998;pointer-events:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='256' height='256'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='256' height='256' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");opacity:.018}
.rv{opacity:0;transform:translateY(28px);transition:opacity .75s ease,transform .75s ease}
.rv.in{opacity:1;transform:translateY(0)}
.d1{transition-delay:.1s}.d2{transition-delay:.2s}.d3{transition-delay:.3s}.d4{transition-delay:.4s}.d5{transition-delay:.5s}
@keyframes fu{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(124,58,237,.4)}50%{box-shadow:0 0 0 6px rgba(124,58,237,0)}}

/* ── HEADER ── */
/* ── HEADER ── */
#header{position:fixed;top:0;left:0;right:0;z-index:1000;padding:0 2.5rem;height:62px;display:flex;align-items:center;justify-content:space-between;transition:background .4s,border .4s,height .4s}
#header.solid{height:56px;background:rgba(10,11,20,.92);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.06);box-shadow:0 1px 4px rgba(10,11,20,.06)}
.logo-wrap{display:flex;align-items:center;gap:.55rem;flex-shrink:0}
.logo-mark{width:32px;height:32px;background:var(--grad);border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden}
.logo-mark img{width:100%;height:100%;object-fit:cover}
.logo-text{font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;letter-spacing:-.01em;color:#fff}
.logo-text span{color:#A78BFA}
#nav-menu{display:flex;align-items:center;gap:.05rem}
#nav-menu li a{font-family:'Syne',sans-serif;font-size:.68rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.55);padding:.45rem .75rem;border-radius:8px;transition:color .2s,background .2s;cursor:none}
#nav-menu li a:hover{color:#fff;background:rgba(255,255,255,.06)}
.nav-cta{background:linear-gradient(135deg,#7C3AED 0%,#2563EB 100%)!important;color:#fff!important;border-radius:100px!important;padding:.45rem 1.2rem!important;box-shadow:0 4px 16px rgba(124,58,237,.35)!important;transition:transform .15s,box-shadow .2s!important}
.nav-cta:hover{transform:translateY(-2px)!important;box-shadow:0 8px 24px rgba(124,58,237,.5)!important}
.burger{display:none;flex-direction:column;gap:5px;cursor:none;padding:.5rem;border:none;background:none}
.burger span{width:22px;height:1.5px;background:#fff;border-radius:2px;transition:.3s}
.burger.active span:nth-child(1){transform:translateY(6.5px) rotate(45deg)}
.burger.active span:nth-child(2){opacity:0}
.burger.active span:nth-child(3){transform:translateY(-6.5px) rotate(-45deg)}
.burger.active span:nth-child(1){transform:translateY(6.5px) rotate(45deg)}
.burger.active span:nth-child(2){opacity:0}
.burger.active span:nth-child(3){transform:translateY(-6.5px) rotate(-45deg)}

/* ── HERO MISSION ── */
#hero-mission{min-height:88vh;position:relative;display:grid;grid-template-columns:1fr 1fr;align-items:center;padding:7rem 4rem 4.5rem;gap:4rem;overflow:hidden}
.hm-bg{position:absolute;inset:0;background:var(--ink)}
.hm-bg::before{content:'';position:absolute;top:-20%;right:-10%;width:70%;height:140%;background:radial-gradient(ellipse,rgba(124,58,237,.12) 0%,transparent 60%);pointer-events:none}
.hm-bg::after{content:'';position:absolute;bottom:-10%;left:-5%;width:50%;height:100%;background:radial-gradient(ellipse,rgba(37,99,235,.08) 0%,transparent 60%);pointer-events:none}
.hm-left{position:relative;z-index:2}
.hero-badge{display:inline-flex;align-items:center;gap:.55rem;background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.25);border-radius:100px;padding:.3rem .85rem .3rem .45rem;margin-bottom:1.5rem;opacity:0;animation:fu .7s .3s forwards}
.badge-dot{width:7px;height:7px;border-radius:50%;background:var(--grad);animation:pulse 2s infinite}
.hero-badge span{font-family:'Syne',sans-serif;font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
h1.ht{font-family:'Instrument Serif',serif;font-size:clamp(2.2rem,4vw,4.4rem);line-height:.95;letter-spacing:-.02em;color:#fff;opacity:0;animation:fu .9s .5s forwards}
h1.ht em{font-style:italic}
h1.ht .grad{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.hero-desc{font-size:.92rem;font-weight:300;line-height:1.85;color:rgba(255,255,255,.5);max-width:460px;margin-top:1.4rem;opacity:0;animation:fu .8s .7s forwards}
.hm-right{position:relative;z-index:2;opacity:0;animation:fu 1s .65s forwards;display:flex;flex-direction:column;gap:1.1rem}
.mission-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:18px;padding:1.5rem;transition:.3s}
.mission-card:hover{background:rgba(255,255,255,.07);border-color:rgba(124,58,237,.3)}
.mc-icon{font-size:1.35rem;margin-bottom:.75rem;display:block}
.mc-title{font-family:'Syne',sans-serif;font-size:.88rem;font-weight:700;color:#fff;margin-bottom:.4rem}
.mc-desc{font-size:.8rem;color:rgba(255,255,255,.4);line-height:1.7}
.mc-tag{display:inline-block;font-family:'Syne',sans-serif;font-size:.58rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;background:rgba(124,58,237,.2);color:#a78bfa;padding:.18rem .6rem;border-radius:100px;margin-top:.65rem}

/* ── TICKER ── */
.ticker{overflow:hidden;padding:.65rem 0}
.ticker.light{background:var(--offwhite)}
.ticker-inner{display:flex;gap:0;animation:tick 28s linear infinite;white-space:nowrap}
.ticker:hover .ticker-inner{animation-play-state:paused}
@keyframes tick{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.tick-item{font-family:'Syne',sans-serif;font-size:.7rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);padding:0 2rem;display:inline-flex;align-items:center;gap:1.5rem;flex-shrink:0}
.tick-item span{font-size:.5rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}

/* ── SECTIONS ── */
.sec{padding:7rem 4rem}
.sec-dark{background:var(--ink);position:relative;overflow:hidden}
.sec-dark::before{content:'';position:absolute;top:-20%;left:-10%;width:60vw;height:140%;background:radial-gradient(ellipse,rgba(124,58,237,.07) 0%,transparent 60%);pointer-events:none}
.sec-alt{background:var(--offwhite)}
.container{max-width:1200px;margin:0 auto}
.stag{display:inline-flex;align-items:center;gap:.5rem;font-family:'Syne',sans-serif;font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;margin-bottom:1rem}
.stag.p{color:var(--purple)}.stag.p::before{content:'';width:24px;height:1.5px;background:var(--purple)}
.stag.w{color:rgba(255,255,255,.4)}.stag.w::before{content:'';width:24px;height:1.5px;background:rgba(255,255,255,.25)}
h2.st{font-family:'Instrument Serif',serif;font-size:clamp(2rem,3.2vw,3.4rem);font-weight:400;line-height:1.05;letter-spacing:-.02em}
h2.st em{font-style:italic}
h2.st .grad{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.sub{font-size:.92rem;font-weight:300;color:var(--muted);line-height:1.75;margin-top:.8rem;max-width:560px}
.sub.light{color:rgba(255,255,255,.4)}
.btn{display:inline-flex;align-items:center;gap:.55rem;font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:.8rem 1.6rem;border-radius:100px;border:none;cursor:none;transition:.25s}
.btn-grad{background:var(--grad);color:#fff}
.btn-grad:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(124,58,237,.45)}
.btn-out{background:transparent;border:1.5px solid rgba(124,58,237,.3);color:var(--purple)}
.btn-out:hover{border-color:var(--purple);background:var(--purple-l)}

/* ── OBJECTIFS PILIERS ── */
.pillars-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem}
.pillar{padding:2.2rem;border-radius:22px;cursor:none;transition:.3s;position:relative;overflow:hidden}
.pillar.p1{background:var(--purple-l);border:1px solid rgba(124,58,237,.15)}
.pillar.p2{background:var(--blue-l);border:1px solid rgba(37,99,235,.15)}
.pillar.p3{background:#ECFDF5;border:1px solid rgba(16,185,129,.15)}
.pillar.p4{background:#FFF7ED;border:1px solid rgba(245,158,11,.15)}
.pillar.p5{background:#FFF1F2;border:1px solid rgba(239,68,68,.12)}
.pillar.p6{background:#F1F5F9;border:1px solid rgba(100,116,139,.15)}
.pillar:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg)}
.pillar-n{font-family:'Instrument Serif',serif;font-size:3rem;line-height:1;margin-bottom:.9rem;opacity:.2;color:var(--purple)}
.pillar-icon{font-size:1.85rem;margin-bottom:.9rem;display:block}
.pillar h4{font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;color:var(--ink);margin-bottom:.65rem}
.pillar p{font-size:.83rem;color:var(--muted);line-height:1.75}

/* ── PROCESSUS SUIVI ── */
.suivi-wrap{max-width:820px;margin:0 auto}
.suivi-item{display:grid;grid-template-columns:72px 1fr;gap:2rem;position:relative;margin-bottom:3rem}
.suivi-item:last-child{margin-bottom:0}
.suivi-item::before{content:'';position:absolute;left:35px;top:72px;bottom:-3rem;width:1px;background:linear-gradient(to bottom,rgba(124,58,237,.3),transparent)}
.suivi-item:last-child::before{display:none}
.suivi-num{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Instrument Serif',serif;font-size:1.6rem;font-style:italic;background:var(--grad);color:#fff;flex-shrink:0;position:relative;z-index:2;box-shadow:0 8px 24px rgba(124,58,237,.3)}
.suivi-content{padding-top:1.1rem}
.suivi-content h4{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;color:#fff;margin-bottom:.55rem}
.suivi-content p{font-size:.83rem;color:rgba(255,255,255,.45);line-height:1.75}
.suivi-tags{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.8rem}
.suivi-tag{font-family:'Syne',sans-serif;font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.22rem .65rem;border-radius:100px;background:rgba(255,255,255,.07);color:rgba(255,255,255,.45);border:1px solid rgba(255,255,255,.08)}

/* ── IMPACT STATS ── */
.impact-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem}
.ig{text-align:center;padding:2.2rem 1.5rem;background:var(--white);border:1px solid var(--divider);border-radius:20px;cursor:none;transition:.3s}
.ig:hover{box-shadow:var(--shadow-lg);transform:translateY(-5px)}
.ig-num{font-family:'Instrument Serif',serif;font-size:2.6rem;line-height:1;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:.5rem}
.ig-label{font-family:'Syne',sans-serif;font-size:.7rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)}
.ig-desc{font-size:.75rem;color:var(--muted);margin-top:.5rem;line-height:1.5}

/* ── QUOTE ── */
.quote-wrap{max-width:900px;margin:0 auto;text-align:center}
.quote-mark{font-family:'Instrument Serif',serif;font-size:5rem;line-height:.5;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:1rem}
.quote-text{font-family:'Instrument Serif',serif;font-size:clamp(1.35rem,2.2vw,2rem);line-height:1.5;color:var(--ink);font-style:italic;letter-spacing:-.01em}
.quote-author{margin-top:2rem;display:inline-flex;align-items:center;gap:.875rem}
.qa-av{width:44px;height:44px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;overflow:hidden}
.qa-av img{width:100%;height:100%;object-fit:cover}
.qa-name{font-family:'Syne',sans-serif;font-size:.84rem;font-weight:700;color:var(--ink);text-align:left}
.qa-role{font-size:.72rem;color:var(--muted)}

/* ── CTA ── */
#cta{padding:7rem 4rem;background:var(--offwhite)}
.cta-box{background:var(--grad);border-radius:32px;padding:5rem;text-align:center;position:relative;overflow:hidden;max-width:1200px;margin:0 auto}
.cta-box::before{content:'';position:absolute;top:-30%;left:-10%;width:60%;height:160%;background:radial-gradient(ellipse,rgba(255,255,255,.1) 0%,transparent 60%);pointer-events:none}
.cta-box h2.st{color:#fff;font-size:clamp(2.2rem,4vw,4rem)}
.cta-box p{font-size:.95rem;color:rgba(255,255,255,.65);max-width:500px;margin:1rem auto 2.5rem;line-height:1.75}
.cta-actions{display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;position:relative;z-index:2}
.btn-white{background:#fff;color:var(--purple);font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:.8rem 1.8rem;border-radius:100px;cursor:none;transition:.25s}
.btn-white:hover{transform:translateY(-3px);box-shadow:0 14px 40px rgba(0,0,0,.2)}
.btn-out-white{background:transparent;border:1.5px solid rgba(255,255,255,.4);color:#fff;font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:.8rem 1.8rem;border-radius:100px;cursor:none;transition:.25s}
.btn-out-white:hover{border-color:rgba(255,255,255,.8);background:rgba(255,255,255,.1)}
.cta-perks{display:flex;justify-content:center;gap:2rem;flex-wrap:wrap;margin-top:2rem;position:relative;z-index:2}
.cta-perk{font-family:'Syne',sans-serif;font-size:.68rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.55);display:flex;align-items:center;gap:.4rem}
.cta-perk::before{content:'✓';color:rgba(255,255,255,.8)}

/* ── FOOTER ── */
footer{background:var(--ink);padding:5rem 4rem 2.5rem}
.footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:4rem;padding-bottom:4rem;border-bottom:1px solid rgba(255,255,255,.06);max-width:1200px;margin:0 auto}
.fb-logo{display:flex;align-items:center;gap:.55rem;margin-bottom:.8rem}
.fb-logo-mark{width:32px;height:32px;background:var(--grad);border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden}
.fb-logo-mark img{width:100%;height:100%;object-fit:cover}
.fb-logo-text{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;color:#fff}
.fb-logo-text span{color:var(--purple)}
.fb-desc{font-size:.82rem;color:rgba(255,255,255,.3);line-height:1.75;max-width:250px;margin-bottom:1.5rem}
.footer-col h4{font-family:'Syne',sans-serif;font-size:.62rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.22);margin-bottom:1.2rem}
.footer-col ul{display:flex;flex-direction:column;gap:.65rem}
.footer-col a{font-size:.82rem;font-weight:300;color:rgba(255,255,255,.4);transition:color .2s}
.footer-col a:hover{color:#fff}
.footer-bottom{display:flex;justify-content:space-between;align-items:center;padding-top:2rem;font-size:.75rem;color:rgba(255,255,255,.2);max-width:1200px;margin:0 auto}
.socials{display:flex;gap:.7rem}
.soc{width:34px;height:34px;border:1px solid rgba(255,255,255,.08);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.78rem;color:rgba(255,255,255,.3);transition:.25s;cursor:none}
.soc:hover{border-color:var(--purple);color:#fff;background:var(--purple)}

@media(max-width:1100px){
  #header{padding:0 1.75rem}
  .sec,#cta,footer{padding-left:2rem;padding-right:2rem}
  #hero-mission{grid-template-columns:1fr;padding:6rem 2rem 4rem;gap:3rem}
  .hm-right{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
  .pillars-grid{grid-template-columns:1fr 1fr}
  .impact-grid{grid-template-columns:1fr 1fr}
  .footer-top{grid-template-columns:1fr 1fr;gap:2.5rem}
}
@media(max-width:768px){
 #nav-menu{display:none;flex-direction:column;align-items:flex-start;position:fixed;top:56px;left:0;right:0;background:#0A0B14;padding:1.5rem 1.75rem max(2rem,env(safe-area-inset-bottom));border-bottom:1px solid rgba(255,255,255,.08);box-shadow:0 16px 56px rgba(124,58,237,.15);gap:.2rem}
  #nav-menu.active{display:flex}
  #nav-menu li{width:100%}
  #nav-menu li a{display:block;padding:.65rem .5rem;color:rgba(255,255,255,.6)}
  .burger{display:flex}
  .pillars-grid{grid-template-columns:1fr}
  .impact-grid{grid-template-columns:1fr 1fr}
  .hm-right{grid-template-columns:1fr}
  .footer-top{grid-template-columns:1fr}
  .footer-bottom{flex-direction:column;gap:.5rem;text-align:center}
  .cta-box{padding:3.5rem 2rem}
  .suivi-item{grid-template-columns:56px 1fr;gap:1.25rem}
  .suivi-num{width:56px;height:56px;font-size:1.3rem}
  .suivi-item::before{left:27px}
  body{cursor:auto}
  #cur{display:none}
  h1.ht{font-size:clamp(1.8rem,7vw,2.8rem)}
}
</style>
</head>
<body>

<div id="cur"><div id="cd"></div><div id="cr"></div></div>

<header id="header">
  <div class="logo-wrap">
    <div class="logo-mark"><img src="/medias/Syntriforg[1].png" alt="SyntriForge"></div>
    <div class="logo-text">SyntriForg <span>Edu</span></div>
  </div>
  <button class="burger" id="burger"><span></span><span></span><span></span></button>
  <ul id="nav-menu">
    <li><a href="/">Accueil</a></li>
    <li><a href="/#fonctionnalites">Fonctionnalités</a></li>
    <li><a href="/#schools">Partenaire</a></li>
    <li><a href="/mission" style="color:var(--purple)">Mission</a></li>
    <li><a href="/about">À propos</a></li>
    <a href="{{ route('institutions.index') }}" class="active">Établissements</a>
    <li><a href="/login" class="nav-cta">Connexion</a></li>
  </ul>
</header>

<!-- ── HERO ── -->
<section id="hero-mission">
  <div class="hm-bg"></div>
  <div class="hm-left">
    <div class="hero-badge"><div class="badge-dot"></div><span>Notre raison d'être</span></div>
    <h1 class="ht">
      Connecter<br>
      chaque <em>acteur</em><br>
      de l'<span class="grad">éducation</span>
    </h1>
    <p class="hero-desc">SyntriForg Edu a une mission précise : faire en sorte qu'aucun enfant ne tombe dans les failles du système éducatif, faute de suivi, de communication ou d'accès à ses dossiers.</p>
    <div style="display:flex;gap:.9rem;flex-wrap:wrap;margin-top:1.8rem;opacity:0;animation:fu .8s .9s forwards">
      <a href="#objectifs" class="btn btn-grad">Nos objectifs →</a>
      <a href="/about" class="btn" style="background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.12)">Notre histoire</a>
    </div>
  </div>
  <div class="hm-right">
    <div class="mission-card">
      <span class="mc-icon">🎯</span>
      <div class="mc-title">Suivi continu de chaque élève</div>
      <div class="mc-desc">De l'inscription au diplôme, chaque étape du parcours est tracée, accessible et communicable entre parties prenantes.</div>
      <span class="mc-tag">Pilier 1</span>
    </div>
    <div class="mission-card">
      <span class="mc-icon">👨‍👩‍👧</span>
      <div class="mc-title">Parents toujours informés</div>
      <div class="mc-desc">Notes, bulletins, absences, finances — les parents accèdent en temps réel à tout ce qui concerne leurs enfants.</div>
      <span class="mc-tag">Pilier 2</span>
    </div>
    <div class="mission-card">
      <span class="mc-icon">🔄</span>
      <div class="mc-title">Dossiers inter-écoles fluidifiés</div>
      <div class="mc-desc">Un élève qui change d'école ne repart pas de zéro. Son dossier complet peut être transmis de façon sécurisée entre établissements partenaires.</div>
      <span class="mc-tag">Pilier 3</span>
    </div>
  </div>
</section>

<!-- ── TICKER ── -->
<div class="ticker light">
  <div class="ticker-inner">
    <span class="tick-item">Suivi parents <span>◆</span></span>
    <span class="tick-item">Notes en temps réel <span>◆</span></span>
    <span class="tick-item">Bulletins publiés <span>◆</span></span>
    <span class="tick-item">Transferts sécurisés <span>◆</span></span>
    <span class="tick-item">Multi-établissements <span>◆</span></span>
    <span class="tick-item">6 rôles intégrés <span>◆</span></span>
    <span class="tick-item">Analytics académiques <span>◆</span></span>
    <span class="tick-item">Suivi parents <span>◆</span></span>
    <span class="tick-item">Notes en temps réel <span>◆</span></span>
    <span class="tick-item">Bulletins publiés <span>◆</span></span>
    <span class="tick-item">Transferts sécurisés <span>◆</span></span>
    <span class="tick-item">Multi-établissements <span>◆</span></span>
    <span class="tick-item">6 rôles intégrés <span>◆</span></span>
    <span class="tick-item">Analytics académiques <span>◆</span></span>
  </div>
</div>

<!-- ── OBJECTIFS ── -->
<section class="sec" id="objectifs">
  <div class="container">
    <div style="text-align:center;margin-bottom:4rem">
      <div class="stag p rv" style="justify-content:center">Nos 6 objectifs fondateurs</div>
      <h2 class="st rv d1" style="text-align:center">Ce que nous <em><span class="grad">voulons changer</span></em></h2>
      <p class="sub rv d2" style="text-align:center;margin:0 auto">Six problèmes concrets. Six solutions intégrées dans une seule plateforme.</p>
    </div>
    <div class="pillars-grid">
      <div class="pillar p1 rv">
        <div class="pillar-n">01</div>
        <span class="pillar-icon">👁️</span>
        <h4>Visibilité totale pour les parents</h4>
        <p>Les parents ne devraient jamais découvrir les résultats de leur enfant lors des conseils de classe. Avec SyntriForge Edu, ils ont accès en permanence aux notes, bulletins, absences et paiements.</p>
      </div>
      <div class="pillar p2 rv d1">
        <div class="pillar-n">02</div>
        <span class="pillar-icon">📚</span>
        <h4>Continuité du dossier élève</h4>
        <p>Quand un élève change d'école, son historique académique, disciplinaire et financier doit pouvoir le suivre. Notre système de transfert inter-établissements rend cela possible, sécurisé et traçable.</p>
      </div>
      <div class="pillar p3 rv d2">
        <div class="pillar-n">03</div>
        <span class="pillar-icon">🏫</span>
        <h4>Allègement administratif</h4>
        <p>Les directeurs passent trop de temps sur des tâches administratives répétitives. SyntriForge Edu automatise la génération des bulletins, les calculs de moyennes, les suivis financiers et bien plus.</p>
      </div>
      <div class="pillar p4 rv d3">
        <div class="pillar-n">04</div>
        <span class="pillar-icon">✏️</span>
        <h4>Saisie de notes simplifiée</h4>
        <p>Les enseignants peuvent saisir leurs notes directement depuis la plateforme. Admin et staff peuvent également noter les apprenants. Les bulletins se calculent automatiquement selon la configuration de l'école.</p>
      </div>
      <div class="pillar p5 rv d4">
        <div class="pillar-n">05</div>
        <span class="pillar-icon">🤝</span>
        <h4>Collaboration multi-acteurs</h4>
        <p>Directeur, staff administratif, enseignants, parents, élèves — chacun a son espace, ses droits, ses outils. La plateforme casse les silos sans sacrifier la sécurité des données.</p>
      </div>
      <div class="pillar p6 rv d5">
        <div class="pillar-n">06</div>
        <span class="pillar-icon">📈</span>
        <h4>Décisions basées sur les données</h4>
        <p>Des rapports clairs, des analytics en temps réel et des indicateurs financiers permettent aux directeurs de prendre de meilleures décisions pour leurs établissements.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── SUIVI STEP BY STEP ── -->
<section class="sec sec-dark">
  <div class="container">
    <div style="text-align:center;margin-bottom:5rem">
      <div class="stag w rv" style="justify-content:center">Le parcours de l'élève</div>
      <h2 class="st rv d1" style="text-align:center;color:#fff">Comment nous <em><span class="grad">accompagnons</span></em><br>chaque élève</h2>
      <p class="sub light rv d2" style="text-align:center;margin:0 auto">De l'inscription jusqu'à la remise des diplômes, aucune étape n'échappe à la plateforme.</p>
    </div>
    <div class="suivi-wrap">
      <div class="suivi-item rv">
        <div class="suivi-num">1</div>
        <div class="suivi-content">
          <h4>Inscription & création du dossier</h4>
          <p>L'élève est créé dans la plateforme avec son matricule unique, affecté à une classe, une filière, un niveau. Son dossier est immédiatement accessible à tous les acteurs autorisés de l'établissement.</p>
          <div class="suivi-tags"><span class="suivi-tag">Directeur</span><span class="suivi-tag">Staff</span><span class="suivi-tag">Admin</span></div>
        </div>
      </div>
      <div class="suivi-item rv d1">
        <div class="suivi-num">2</div>
        <div class="suivi-content">
          <h4>Suivi pédagogique en continu</h4>
          <p>Les enseignants saisissent leurs évaluations et notes. L'admin et le staff peuvent également noter les apprenants. Les bulletins sont calculés automatiquement selon la configuration pédagogique de l'école.</p>
          <div class="suivi-tags"><span class="suivi-tag">Enseignants</span><span class="suivi-tag">Staff</span><span class="suivi-tag">Admin</span></div>
        </div>
      </div>
      <div class="suivi-item rv d2">
        <div class="suivi-num">3</div>
        <div class="suivi-content">
          <h4>Publication & accès parent en temps réel</h4>
          <p>Une fois publiés, les bulletins sont instantanément accessibles aux parents depuis leur espace. Ils consultent aussi les paiements dus, les absences et les incidents disciplinaires de leurs enfants.</p>
          <div class="suivi-tags"><span class="suivi-tag">Parents</span><span class="suivi-tag">Élèves</span></div>
        </div>
      </div>
      <div class="suivi-item rv d3">
        <div class="suivi-num">4</div>
        <div class="suivi-content">
          <h4>Transfert inter-établissements</h4>
          <p>Si l'élève change d'école, l'établissement d'accueil fait une demande de consultation du dossier. L'école source approuve et ouvre un accès sécurisé à durée limitée. Les notes, bulletins, dossier disciplinaire et historique financier sont transmissibles.</p>
          <div class="suivi-tags"><span class="suivi-tag">Inter-écoles</span><span class="suivi-tag">Sécurisé</span><span class="suivi-tag">Traçable</span></div>
        </div>
      </div>
      <div class="suivi-item rv d4">
        <div class="suivi-num">5</div>
        <div class="suivi-content">
          <h4>Diplôme & clôture du dossier</h4>
          <p>Le directeur génère les rapports finaux, valide les résultats et archive le dossier complet de l'élève. Toute l'histoire académique est conservée et consultable.</p>
          <div class="suivi-tags"><span class="suivi-tag">Directeur</span><span class="suivi-tag">Archives</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── IMPACT ── -->
<section class="sec sec-alt">
  <div class="container">
    <div style="text-align:center;margin-bottom:4rem">
      <div class="stag p rv" style="justify-content:center">Notre impact</div>
      <h2 class="st rv d1" style="text-align:center">Les <em><span class="grad">chiffres</span></em> qui parlent</h2>
    </div>
    <div class="impact-grid">
      <div class="ig rv">
        <div class="ig-num">250+</div>
        <div class="ig-label">But Établissements</div>
        <div class="ig-desc">De la maternelle à l'université, dans toute la francophonie</div>
      </div>
      <div class="ig rv d1">
        <div class="ig-num">50K+</div>
        <div class="ig-label">But Élèves suivis</div>
        <div class="ig-desc">Chaque dossier élève, mis à jour en temps réel par l'équipe pédagogique</div>
      </div>
      <div class="ig rv d2">
        <div class="ig-num">95%</div>
        <div class="ig-label">Satisfaction</div>
        <div class="ig-desc">Directeurs, staff et enseignants recommandent la plateforme</div>
      </div>
      <div class="ig rv d3">
        <div class="ig-num">6</div>
        <div class="ig-label">Rôles intégrés</div>
        <div class="ig-desc">Superadmin, Directeur, Staff, Enseignant, Parent, Élève — tous connectés</div>
      </div>
    </div>
  </div>
</section>

<!-- ── CITATION ── -->
<section class="sec">
  <div class="container">
    <div class="quote-wrap rv">
      <span class="quote-mark">"</span>
      <p class="quote-text">L'éducation n'est pas seulement ce qui se passe entre les quatre murs d'une salle de classe. C'est un écosystème où chaque acteur — parent, enseignant, directeur, élève — doit pouvoir communiquer et agir en temps réel.</p>
      <div class="quote-author">
        <div class="qa-av"><img src="/medias/Syntriforg[1].png" alt="SyntriForge"></div>
        <div>
          <div class="qa-name">Équipe SyntriForg Edu</div>
          <div class="qa-role">Fondateurs de la plateforme</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ── -->
<section id="cta">
  <div class="cta-box rv">
    <h2 class="st rv d1">Rejoignez notre<br><em>mission éducative</em></h2>
    <p class="rv d2">Chaque école qui rejoint SyntriForge Edu améliore concrètement la vie de ses élèves, de ses enseignants et des familles.</p>
    <div class="cta-actions rv d3">
      <a href="https://syntriforg.ct.ws/?i=1" class="btn-white">Demander une démo</a>
      <a href="/about" class="btn-out-white">Notre histoire</a>
    </div>
    <div class="cta-perks rv d4">
      <div class="cta-perk">Démo gratuite 3 mois</div>
      <div class="cta-perk">Sans carte bancaire</div>
      <div class="cta-perk">Support 24/7</div>
    </div>
  </div>
</section>

<footer>
  <div class="footer-top">
    <div>
      <div class="fb-logo"><div class="fb-logo-mark"><img src="/medias/Syntriforg[1].png" alt="SyntriForge"></div><div class="fb-logo-text">SyntriForge <span>Edu</span></div></div>
      <p class="fb-desc">La plateforme tout-en-un pour gérer vos établissements scolaires avec efficacité et innovation.</p>
      <div class="socials"><a href="#" class="soc">𝕏</a><a href="#" class="soc">in</a><a href="#" class="soc">f</a></div>
    </div>
    <div class="footer-col"><h4>Produit</h4><ul><li><a href="/#fonctionnalites">Fonctionnalités</a></li><li><a href="#">Tarifs</a></li><li><a href="#">Documentation</a></li></ul></div>
    <div class="footer-col"><h4>Entreprise</h4><ul><li><a href="/about">À propos</a></li><li><a href="/mission">Mission</a></li><li><a href="#">Contact</a></li></ul></div>
    <div class="footer-col"><h4>Légal</h4><ul><li><a href="#">Confidentialité</a></li><li><a href="#">CGU</a></li><li><a href="#">Support</a></li></ul></div>
  </div>
  <div class="footer-bottom"><p>© 2026 SyntriForg Edu. Tous droits réservés.</p><p>Connected Intelligence</p></div>
</footer>

<script>
const cd=document.getElementById('cd'),cr=document.getElementById('cr');
let mx=0,my=0,rx=0,ry=0;
document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cd.style.left=mx+'px';cd.style.top=my+'px'});
(function loop(){rx+=(mx-rx)*.1;ry+=(my-ry)*.1;cr.style.left=rx+'px';cr.style.top=ry+'px';requestAnimationFrame(loop)})();
document.querySelectorAll('a,button,.pillar,.ig,.mission-card').forEach(el=>{
  el.addEventListener('mouseenter',()=>document.body.classList.add('hov'));
  el.addEventListener('mouseleave',()=>document.body.classList.remove('hov'));
});
window.addEventListener('scroll',()=>document.getElementById('header').classList.toggle('solid',scrollY>60));
const obs=new IntersectionObserver(e=>{e.forEach(x=>{if(x.isIntersecting){x.target.classList.add('in');obs.unobserve(x.target)}})},{threshold:.1});
document.querySelectorAll('.rv').forEach(el=>obs.observe(el));
const burger=document.getElementById('burger'),menu=document.getElementById('nav-menu');
burger.addEventListener('click',()=>{burger.classList.toggle('active');menu.classList.toggle('active')});
document.querySelectorAll('#nav-menu a').forEach(a=>a.addEventListener('click',()=>{burger.classList.remove('active');menu.classList.remove('active')}));
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click',e=>{const t=document.querySelector(a.getAttribute('href'));if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'})}});
});
</script>
</body>
</html>
