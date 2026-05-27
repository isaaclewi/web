<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>À propos — SyntriForg Edu</title>
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
#cd{width:10px;height:10px;background:var(--purple);border-radius:50%;position:absolute;transform:translate(-50%,-50%);transition:transform .12s,background .2s}
#cr{width:40px;height:40px;border:1.5px solid rgba(124,58,237,.45);border-radius:50%;position:absolute;transform:translate(-50%,-50%);transition:width .25s,height .25s,border-color .25s}
body.hov #cr{width:65px;height:65px;border-color:var(--purple)}
body.hov #cd{transform:translate(-50%,-50%) scale(0)}
body::after{content:'';position:fixed;inset:0;z-index:9998;pointer-events:none;
background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='256' height='256'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='256' height='256' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
opacity:.018}

.rv{opacity:0;transform:translateY(28px);transition:opacity .75s ease,transform .75s ease}
.rv.in{opacity:1;transform:translateY(0)}
.d1{transition-delay:.1s}.d2{transition-delay:.2s}.d3{transition-delay:.3s}
.d4{transition-delay:.4s}.d5{transition-delay:.5s}.d6{transition-delay:.6s}

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

/* ── HERO ABOUT ── */
#hero-about{min-height:70vh;position:relative;display:flex;align-items:center;justify-content:center;padding:7rem 4rem 4.5rem;overflow:hidden;text-align:center}
.hero-about-bg{position:absolute;inset:0;background:linear-gradient(160deg,rgba(245,243,255,.95) 0%,rgba(239,246,255,.7) 60%,rgba(255,255,255,1) 100%)}
.hero-about-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(124,58,237,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(124,58,237,.04) 1px,transparent 1px);background-size:60px 60px;mask-image:radial-gradient(ellipse at 50% 40%,black 20%,transparent 70%)}
.hero-about-glow{position:absolute;top:10%;left:50%;transform:translateX(-50%);width:600px;height:450px;background:radial-gradient(circle,rgba(124,58,237,.06) 0%,transparent 70%);pointer-events:none}
.hero-about-inner{position:relative;z-index:2;max-width:760px}
.hero-badge{display:inline-flex;align-items:center;gap:.55rem;background:linear-gradient(90deg,rgba(124,58,237,.1),rgba(37,99,235,.1));border:1px solid rgba(124,58,237,.2);border-radius:100px;padding:.3rem .85rem .3rem .45rem;margin-bottom:1.5rem}
.badge-dot{width:7px;height:7px;border-radius:50%;background:var(--grad);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(124,58,237,.4)}50%{box-shadow:0 0 0 6px rgba(124,58,237,0)}}
.hero-badge span{font-family:'Syne',sans-serif;font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
h1.ht{font-family:'Instrument Serif',serif;font-size:clamp(2.2rem,4vw,4.4rem);line-height:.95;letter-spacing:-.02em;color:var(--ink)}
h1.ht em{font-style:italic}
h1.ht .grad{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.hero-desc{font-size:.95rem;font-weight:300;line-height:1.8;color:var(--muted);max-width:520px;margin:1.5rem auto 0}
@keyframes fu{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

/* ── SECTION HELPERS ── */
.sec{padding:7rem 4rem}
.sec-alt{background:var(--offwhite)}
.container{max-width:1200px;margin:0 auto}
.stag{display:inline-flex;align-items:center;gap:.5rem;font-family:'Syne',sans-serif;font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;margin-bottom:1rem}
.stag.p{color:var(--purple)}.stag.p::before{content:'';width:24px;height:1.5px;background:var(--purple)}
.stag.b{color:var(--blue)}.stag.b::before{content:'';width:24px;height:1.5px;background:var(--blue)}
h2.st{font-family:'Instrument Serif',serif;font-size:clamp(2rem,3.2vw,3.4rem);font-weight:400;line-height:1.05;letter-spacing:-.02em}
h2.st em{font-style:italic}
h2.st .grad{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.sub{font-size:.92rem;font-weight:300;color:var(--muted);line-height:1.75;margin-top:.8rem;max-width:560px}
.btn{display:inline-flex;align-items:center;gap:.55rem;font-family:'Syne',sans-serif;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:.8rem 1.6rem;border-radius:100px;border:none;cursor:none;transition:.25s}
.btn-grad{background:var(--grad);color:#fff}
.btn-grad:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(124,58,237,.45)}
.btn-out{background:transparent;border:1.5px solid rgba(124,58,237,.3);color:var(--purple)}
.btn-out:hover{border-color:var(--purple);background:var(--purple-l);transform:translateY(-2px)}

/* ── TICKER ── */
.ticker{overflow:hidden;background:var(--ink);padding:.65rem 0}
.ticker-inner{display:flex;gap:0;animation:tick 24s linear infinite;white-space:nowrap}
.ticker:hover .ticker-inner{animation-play-state:paused}
@keyframes tick{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.tick-item{font-family:'Syne',sans-serif;font-size:.7rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.35);padding:0 2rem;display:inline-flex;align-items:center;gap:1.5rem;flex-shrink:0}
.tick-item span{font-size:.5rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}

/* ── STORY SECTION ── */
.story-grid{display:grid;grid-template-columns:1fr 1fr;gap:6rem;align-items:center}
.story-img-wrap{position:relative}
.story-img{width:100%;aspect-ratio:4/5;border-radius:28px;overflow:hidden;position:relative;box-shadow:var(--shadow-xl)}
.story-img img{width:100%;height:100%;object-fit:cover;filter:brightness(.9) saturate(.95)}
.story-img::after{content:'';position:absolute;inset:0;background:linear-gradient(to top,rgba(10,11,20,.2),transparent 50%)}
.story-badge-wrap{position:absolute;bottom:-1.5rem;right:-1.5rem;background:#fff;border-radius:18px;padding:1.1rem 1.4rem;box-shadow:var(--shadow-lg);border:1px solid var(--divider)}
.sbw-val{font-family:'Instrument Serif',serif;font-size:2.2rem;line-height:1;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.sbw-lbl{font-size:.7rem;color:var(--muted);margin-top:.2rem;font-family:'Syne',sans-serif;font-weight:600;letter-spacing:.08em;text-transform:uppercase}
.story-text h2.st{margin-bottom:1.4rem}
.story-text p{font-size:.9rem;color:var(--muted);line-height:1.85;margin-bottom:1.1rem}
.story-text p strong{color:var(--ink);font-weight:500}

/* ── LINK SECTION ── */
#link-section{background:var(--ink);position:relative;overflow:hidden}
#link-section::before{content:'';position:absolute;top:-20%;left:-10%;width:60vw;height:140%;background:radial-gradient(ellipse,rgba(124,58,237,.08) 0%,transparent 60%);pointer-events:none}
.link-inner{display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center;position:relative;z-index:2;max-width:1200px;margin:0 auto}
.link-text .stag.p{color:rgba(255,255,255,.4)}
.link-text .stag.p::before{background:rgba(255,255,255,.25)}
.link-text h2.st{color:#fff}
.link-text .sub{color:rgba(255,255,255,.4);max-width:460px}
.link-list{margin-top:2rem;display:flex;flex-direction:column;gap:.9rem}
.link-item{display:flex;align-items:flex-start;gap:.9rem;padding:1.1rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:14px;transition:.3s}
.link-item:hover{background:rgba(255,255,255,.07);border-color:rgba(124,58,237,.3)}
.link-item-icon{width:40px;height:40px;border-radius:10px;background:rgba(124,58,237,.15);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
.link-item-title{font-family:'Syne',sans-serif;font-size:.84rem;font-weight:700;color:#fff;margin-bottom:.25rem}
.link-item-desc{font-size:.78rem;color:rgba(255,255,255,.35);line-height:1.6}
.link-visual{display:flex;flex-direction:column;gap:1rem}
.app-screen{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:18px;padding:1.4rem;overflow:hidden;position:relative}
.app-screen-hd{display:flex;align-items:center;gap:.5rem;margin-bottom:.9rem}
.app-dot{width:8px;height:8px;border-radius:50%}
.app-screen-title{font-family:'Syne',sans-serif;font-size:.68rem;font-weight:600;color:rgba(255,255,255,.25);letter-spacing:.1em;text-transform:uppercase;margin-left:.5rem}
.app-row{display:flex;align-items:center;justify-content:space-between;padding:.55rem .7rem;background:rgba(255,255,255,.03);border-radius:8px;margin-bottom:.35rem}
.app-row:last-child{margin-bottom:0}
.app-row-name{font-size:.78rem;color:rgba(255,255,255,.55);font-family:'Syne',sans-serif;font-weight:600}
.app-row-pill{font-size:.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:.18rem .55rem;border-radius:100px;font-family:'Syne',sans-serif}
.pill-g{background:rgba(16,185,129,.15);color:#34d399}
.pill-a{background:rgba(245,158,11,.15);color:#fbbf24}
.pill-b{background:rgba(37,99,235,.15);color:#60a5fa}

/* ── VALEURS ── */
.values-header{text-align:center;margin-bottom:4rem}
.values-header .sub{margin:0 auto}
.values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem}
.vc{border:1px solid var(--divider);border-radius:22px;padding:2rem;cursor:none;transition:.3s;position:relative;overflow:hidden}
.vc::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--grad);transform:scaleX(0);transform-origin:left;transition:transform .35s}
.vc:hover{box-shadow:var(--shadow-lg);transform:translateY(-5px);border-color:transparent}
.vc:hover::before{transform:scaleX(1)}
.vc-icon{font-size:2rem;margin-bottom:1.1rem;display:block}
.vc-num{position:absolute;top:1.5rem;right:1.5rem;font-family:'Instrument Serif',serif;font-size:2rem;color:rgba(124,58,237,.06)}
.vc h4{font-family:'Syne',sans-serif;font-size:.92rem;font-weight:700;color:var(--ink);margin-bottom:.55rem}
.vc p{font-size:.82rem;color:var(--muted);line-height:1.7}

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
  #hero-about{padding:6rem 2rem 4rem}
  .story-grid,.link-inner{grid-template-columns:1fr;gap:3rem}
  .story-img-wrap{max-width:480px;margin:0 auto}
  .values-grid{grid-template-columns:1fr 1fr}
  .footer-top{grid-template-columns:1fr 1fr;gap:2.5rem}
}
@media(max-width:768px){
   #nav-menu{display:none;flex-direction:column;align-items:flex-start;position:fixed;top:56px;left:0;right:0;background:#0A0B14;padding:1.5rem 1.75rem max(2rem,env(safe-area-inset-bottom));border-bottom:1px solid rgba(255,255,255,.08);box-shadow:0 16px 56px rgba(124,58,237,.15);gap:.2rem}
  #nav-menu.active{display:flex}
  #nav-menu li{width:100%}
  #nav-menu li a{display:block;padding:.65rem .5rem;color:rgba(255,255,255,.6)}
  .burger{display:flex}
  .values-grid{grid-template-columns:1fr}
  .footer-top{grid-template-columns:1fr}
  .footer-bottom{flex-direction:column;gap:.5rem;text-align:center}
  .cta-box{padding:3.5rem 2rem}
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
    <li><a href="/mission">Mission</a></li>
    <li><a href="/about" style="color:var(--purple)">À propos</a></li>
    <a href="{{ route('institutions.index') }}" class="active">Établissements</a>
    <li><a href="/login" class="nav-cta">Connexion</a></li>
  </ul>
</header>

<!-- ── HERO ── -->
<section id="hero-about">
  <div class="hero-about-bg"></div>
  <div class="hero-about-grid"></div>
  <div class="hero-about-glow"></div>
  <div class="hero-about-inner">
    <div class="hero-badge" style="opacity:0;animation:fu .7s .3s forwards"><div class="badge-dot"></div><span>Notre histoire</span></div>
    <h1 class="ht" style="opacity:0;animation:fu .9s .5s forwards">
      Une <em>vision</em><br>
      au service de<br>
      l'<span class="grad">éducation</span>
    </h1>
    <p class="hero-desc" style="opacity:0;animation:fu .8s .7s forwards">SyntriForg Edu est né d'un constat simple : les établissements scolaires méritent des outils modernes, connectés et centrés sur l'humain.</p>
  </div>
</section>

<!-- ── TICKER ── -->
<div class="ticker">
  <div class="ticker-inner">
    <span class="tick-item">Notre histoire <span>◆</span></span>
    <span class="tick-item">Fondé en 2026 <span>◆</span></span>
    <span class="tick-item">250+ écoles partenaires, comme objectif <span>◆</span></span>
    <span class="tick-item">Multi-rôles intégrés <span>◆</span></span>
    <span class="tick-item">Suivi parent-élève <span>◆</span></span>
    <span class="tick-item">Transferts inter-écoles <span>◆</span></span>
    <span class="tick-item">Notre histoire <span>◆</span></span>
    <span class="tick-item">Fondé en 2026 <span>◆</span></span>
    <span class="tick-item">250+ écoles partenaires, comme objectif <span>◆</span></span>
    <span class="tick-item">Multi-rôles intégrés <span>◆</span></span>
    <span class="tick-item">Suivi parent-élève <span>◆</span></span>
    <span class="tick-item">Transferts inter-écoles <span>◆</span></span>
  </div>
</div>

<!-- ── NOTRE HISTOIRE ── -->
<section class="sec">
  <div class="container">
    <div class="story-grid">
      <div class="story-img-wrap rv">
        <div class="story-img">
          <img src="/medias/télécharger (8).jfif" alt="Étudiants en classe">
        </div>
        <div class="story-badge-wrap">
          <div class="sbw-val">2026</div>
          <div class="sbw-lbl">Année de création</div>
        </div>
      </div>
      <div class="story-text">
        <div class="stag p rv">Notre histoire</div>
        <h2 class="st rv d1">Nés d'un besoin <em><span class="grad">concret</span></em></h2>
        <div class="rv d2">
          <p>SyntriForge Edu est né de l'observation directe des réalités du terrain éducatif africain et francophone. <strong>Les directeurs d'école jonglaient avec des dizaines de fichiers Excel</strong>, les parents ne savaient jamais où en était leur enfant, et les enseignants saisissaient les mêmes notes trois fois dans des registres différents.</p>
          <p>Nous avons réuni une équipe de développeurs passionnés par l'éducation pour construire une plateforme qui répond à ces problèmes <strong>une bonne fois pour toutes</strong>. Le résultat : un système multi-rôles complet, du superadmin à l'élève, en passant par les directeurs, le staff administratif, les enseignants et les parents.</p>
          <p>Aujourd'hui, SyntriForg Edu sera déployé dans <strong>plus de 250 établissements</strong> — primaires, secondaires, lycées et centres de formation — avec une satisfaction client de 95%.</p>
        </div>
        <div class="rv d3" style="margin-top:2rem;display:flex;gap:1rem;flex-wrap:wrap">
          <a href="/mission" class="btn btn-grad">Lire notre mission →</a>
          <a href="/#cta" class="btn btn-out">Demander une démo</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── LIEN AVEC LA PLATEFORME LARAVEL ── -->
<section class="sec" id="link-section">
  <div class="link-inner">
    <div class="link-text">
      <div class="stag p rv">Plateforme intégrée</div>
      <h2 class="st rv d1">SyntriForg Edu est <em>directement lié</em> à votre espace de gestion</h2>
      <p class="sub rv d2">Ce site vitrine est la porte d'entrée vers la plateforme académique complète. Une fois connecté, chaque utilisateur accède à un espace dédié selon son rôle.</p>
      <div class="link-list">
        <div class="link-item rv d2">
          <div class="link-item-icon">🏫</div>
          <div><div class="link-item-title">Espace Directeur / Admin</div><div class="link-item-desc">Gestion complète de l'établissement : apprenants, enseignants, staff, finances, académique, bulletins et rapports.</div></div>
        </div>
        <div class="link-item rv d3">
          <div class="link-item-icon">👩‍🏫</div>
          <div><div class="link-item-title">Espace Enseignant</div><div class="link-item-desc">Saisie des notes, création d'évaluations, suivi des classes et consultation de l'emploi du temps en temps réel.</div></div>
        </div>
        <div class="link-item rv d4">
          <div class="link-item-icon">👨‍👩‍👧</div>
          <div><div class="link-item-title">Espace Parent</div><div class="link-item-desc">Suivi des résultats, bulletins publiés, finances et incidents disciplinaires de chaque enfant inscrit.</div></div>
        </div>
        <div class="link-item rv d5">
          <div class="link-item-icon">🔄</div>
          <div><div class="link-item-title">Transferts inter-établissements</div><div class="link-item-desc">Une école peut demander le dossier complet d'un élève à une autre école partenaire de façon sécurisée et traçable.</div></div>
        </div>
      </div>
    </div>
    <div class="link-visual rv d3">
      <div class="app-screen">
        <div class="app-screen-hd">
          <div class="app-dot" style="background:#ef4444"></div>
          <div class="app-dot" style="background:#f59e0b;margin-left:4px"></div>
          <div class="app-dot" style="background:#10b981;margin-left:4px"></div>
          <div class="app-screen-title">Espace directeur</div>
        </div>
        <div class="app-row"><span class="app-row-name">Mathieu Nzinga</span><span class="app-row-pill pill-g">Actif</span></div>
        <div class="app-row"><span class="app-row-name">Amina Diallo</span><span class="app-row-pill pill-g">Actif</span></div>
        <div class="app-row"><span class="app-row-name">Cédric Mboukou</span><span class="app-row-pill pill-a">En attente</span></div>
        <div class="app-row"><span class="app-row-name">Stella Kouassi</span><span class="app-row-pill pill-b">Transféré</span></div>
      </div>
      <div class="app-screen">
        <div class="app-screen-hd">
          <div class="app-dot" style="background:#ef4444"></div>
          <div class="app-dot" style="background:#f59e0b;margin-left:4px"></div>
          <div class="app-dot" style="background:#10b981;margin-left:4px"></div>
          <div class="app-screen-title">Bulletins — Trimestre 1</div>
        </div>
        <div class="app-row"><span class="app-row-name">3e B — 28 élèves</span><span class="app-row-pill pill-g">Publiés</span></div>
        <div class="app-row"><span class="app-row-name">4e A — 30 élèves</span><span class="app-row-pill pill-a">Calculés</span></div>
        <div class="app-row"><span class="app-row-name">Terminale — 25 élèves</span><span class="app-row-pill pill-a">En cours</span></div>
      </div>
      <div style="display:flex;gap:.75rem">
        <div style="flex:1;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:1.1rem;text-align:center">
          <div style="font-family:'Instrument Serif',serif;font-size:1.8rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent">95%</div>
          <div style="font-size:.68rem;color:rgba(255,255,255,.3);font-family:'Syne',sans-serif;font-weight:600;letter-spacing:.08em;text-transform:uppercase;margin-top:.3rem">Satisfaction</div>
        </div>
        <div style="flex:1;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:1.1rem;text-align:center">
          <div style="font-family:'Instrument Serif',serif;font-size:1.8rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent">250+</div>
          <div style="font-size:.68rem;color:rgba(255,255,255,.3);font-family:'Syne',sans-serif;font-weight:600;letter-spacing:.08em;text-transform:uppercase;margin-top:.3rem">Écoles</div>
        </div>
        <div style="flex:1;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:1.1rem;text-align:center">
          <div style="font-family:'Instrument Serif',serif;font-size:1.8rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent">6</div>
          <div style="font-size:.68rem;color:rgba(255,255,255,.3);font-family:'Syne',sans-serif;font-weight:600;letter-spacing:.08em;text-transform:uppercase;margin-top:.3rem">Rôles</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── NOS VALEURS ── -->
<section class="sec sec-alt">
  <div class="container">
    <div class="values-header">
      <div class="stag p rv" style="justify-content:center">Ce qui nous guide</div>
      <h2 class="st rv d1" style="text-align:center">Nos <em><span class="grad">valeurs fondatrices</span></em></h2>
      <p class="sub rv d2" style="text-align:center">Chaque décision produit, chaque ligne de code, chaque fonctionnalité est guidée par ces principes.</p>
    </div>
    <div class="values-grid">
      <div class="vc rv"><div class="vc-num">01</div><span class="vc-icon">🎯</span><h4>Centré sur l'humain</h4><p>La technologie doit s'adapter aux personnes, pas l'inverse. Chaque interface est pensée pour être utilisable par un directeur d'école ou un parent sans formation technique.</p></div>
      <div class="vc rv d1"><div class="vc-num">02</div><span class="vc-icon">🔒</span><h4>Sécurité & confidentialité</h4><p>Les données des enfants et des familles sont sacrées. Isolation complète par établissement, traçabilité de chaque action, accès strictement contrôlé par rôle.</p></div>
      <div class="vc rv d2"><div class="vc-num">03</div><span class="vc-icon">🌍</span><h4>Accessibilité universelle</h4><p>Conçu pour fonctionner sur des connexions lentes, des appareils modestes, dans des environnements variés — des grandes villes aux zones rurales.</p></div>
      <div class="vc rv d3"><div class="vc-num">04</div><span class="vc-icon">🤝</span><h4>Collaboration inter-écoles</h4><p>Les établissements ne sont pas des silos. La communication sécurisée des dossiers élèves entre écoles partenaires est au cœur de notre philosophie.</p></div>
      <div class="vc rv d4"><div class="vc-num">05</div><span class="vc-icon">📊</span><h4>Décisions éclairées</h4><p>Des analytics clairs et actionnables permettent aux directeurs de prendre de meilleures décisions pour leurs élèves, leur staff et leur budget.</p></div>
      <div class="vc rv d5"><div class="vc-num">06</div><span class="vc-icon">⚡</span><h4>Amélioration continue</h4><p>La plateforme évolue avec ses utilisateurs. Chaque retour terrain est une opportunité d'améliorer l'expérience de milliers d'acteurs éducatifs.</p></div>
    </div>
  </div>
</section>

<!-- ── CTA ── -->
<section id="cta">
  <div class="cta-box rv">
    <h2 class="st rv d1">Rejoignez la<br><em>communauté SyntriForg</em></h2>
    <p class="rv d2">Votre établissement mérite une gestion moderne. Découvrez la plateforme en 3 mois, sans engagement.</p>
    <div class="cta-actions rv d3">
      <a href="https://syntriforg.ct.ws/?i=1" class="btn-white">Demander une démo</a>
      <a href="/mission" class="btn-out-white">Notre mission</a>
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
document.querySelectorAll('a,button,.vc,.link-item').forEach(el=>{
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
