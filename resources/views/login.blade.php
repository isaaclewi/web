<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion | SyntriForge Edu</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="icon" type="image/x-icon" href="/medias/Syntriforg[1].png">
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
:root {
  --purple:   #7C3AED;
  --blue:     #2563EB;
  --grad:     linear-gradient(135deg,#7C3AED 0%,#2563EB 100%);
  --ink:      #0A0B14;
  --ink2:     #1E293B;
  --muted:    #64748B;
  --divider:  #E2E8F0;
  --white:    #FFFFFF;
  --offwhite: #F8FAFD;
  --purple-l: #F5F3FF;
  --shadow-sm:  0 1px 4px rgba(10,11,20,.06);
  --shadow-md:  0 8px 32px rgba(10,11,20,.10);
  --shadow-lg:  0 24px 64px rgba(124,58,237,.18);
  --shadow-xl:  0 40px 80px rgba(10,11,20,.18);
  --r: 18px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{height:100%}
body{
  font-family:'DM Sans',sans-serif;
  color:var(--ink);
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:1.5rem;
  overflow:hidden;
  position:relative;
  background:var(--offwhite);
  cursor:none;
}

/* ── CURSOR ── */
#cur{position:fixed;top:0;left:0;z-index:9999;pointer-events:none}
#cd{width:8px;height:8px;background:var(--purple);border-radius:50%;position:absolute;transform:translate(-50%,-50%);transition:transform .12s}
#cr{width:36px;height:36px;border:1.5px solid rgba(124,58,237,.4);border-radius:50%;position:absolute;transform:translate(-50%,-50%);transition:width .25s,height .25s,border-color .25s}
body.hov #cr{width:55px;height:55px;border-color:var(--purple)}
body.hov #cd{transform:translate(-50%,-50%) scale(0)}

/* ── BG SPLIT ── */
.bg-left{
  position:fixed;top:0;left:0;bottom:0;width:50%;
  background:var(--ink);
  z-index:0;
}
.bg-left::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse at 30% 40%,rgba(124,58,237,.18) 0%,transparent 60%);
}
.bg-left::after{
  content:'';position:absolute;inset:0;
  background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);
  background-size:50px 50px;
}
.bg-right{
  position:fixed;top:0;right:0;bottom:0;width:50%;
  background:var(--offwhite);
  z-index:0;
}

/* ── NOISE ── */
.bg-noise{
  position:fixed;inset:0;z-index:1;pointer-events:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='256' height='256'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='256' height='256' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");
  opacity:.02;
}

/* ── LAYOUT WRAPPER ── */
.page-wrap{
  position:relative;z-index:2;
  width:100%;max-width:1100px;
  display:grid;grid-template-columns:1fr 1fr;
  min-height:600px;
  border-radius:28px;
  overflow:hidden;
  box-shadow:var(--shadow-xl);
}

/* ── LEFT PANEL ── */
.panel-left{
  background:var(--ink);
  padding:3.5rem;
  display:flex;flex-direction:column;justify-content:space-between;
  position:relative;overflow:hidden;
}
.panel-left::before{
  content:'';position:absolute;bottom:-100px;right:-80px;
  width:350px;height:350px;border-radius:50%;
  background:radial-gradient(circle,rgba(124,58,237,.18) 0%,transparent 70%);
}
.panel-left::after{
  content:'';position:absolute;top:-60px;left:-60px;
  width:250px;height:250px;border-radius:50%;
  background:radial-gradient(circle,rgba(37,99,235,.12) 0%,transparent 70%);
}
.pl-logo{display:flex;align-items:center;gap:.7rem;position:relative;z-index:2}
.pl-logo-mark{
  width:42px;height:42px;border-radius:12px;
  background:var(--grad);
  display:flex;align-items:center;justify-content:center;
  font-family:'Syne',sans-serif;font-size:.95rem;font-weight:800;color:#fff;
  box-shadow:0 6px 20px rgba(124,58,237,.4);
}
.pl-logo-text{font-family:'Syne',sans-serif;font-size:1.2rem;font-weight:800;color:#fff}
.pl-logo-text span{color:var(--purple)}
.pl-body{position:relative;z-index:2;flex:1;display:flex;flex-direction:column;justify-content:center;padding:2rem 0}
.pl-tag{display:inline-flex;align-items:center;gap:.5rem;font-family:'Syne',sans-serif;font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.3);margin-bottom:1.2rem}
.pl-tag::before{content:'';width:20px;height:1.5px;background:rgba(255,255,255,.2)}
.pl-title{font-family:'Instrument Serif',serif;font-size:clamp(2rem,3vw,3rem);line-height:1.05;color:#fff;letter-spacing:-.02em;margin-bottom:1.2rem}
.pl-title em{font-style:italic;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.pl-desc{font-size:.9rem;font-weight:300;color:rgba(255,255,255,.4);line-height:1.8;max-width:320px}
.pl-stats{display:flex;gap:2rem;position:relative;z-index:2;margin-top:auto}
.pl-stat-num{font-family:'Instrument Serif',serif;font-size:2rem;background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1}
.pl-stat-lbl{font-size:.7rem;color:rgba(255,255,255,.25);margin-top:.2rem;letter-spacing:.06em}
.pl-visual{position:relative;z-index:2;margin-top:2rem}
.pl-card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:1.2rem 1.4rem;display:flex;align-items:center;gap:.9rem;margin-top:.8rem;transition:.3s;cursor:none}
.pl-card:hover{background:rgba(255,255,255,.08);transform:translateX(4px)}
.pl-card-icon{width:38px;height:38px;border-radius:10px;background:var(--grad);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
.pl-card-title{font-family:'Syne',sans-serif;font-size:.82rem;font-weight:700;color:#fff}
.pl-card-sub{font-size:.72rem;color:rgba(255,255,255,.35);margin-top:.1rem}

/* ── RIGHT PANEL ── */
.panel-right{
  background:var(--white);
  padding:3.5rem;
  display:flex;flex-direction:column;justify-content:center;
  position:relative;
}
.pr-back{
  position:absolute;top:2rem;right:2rem;
  font-family:'Syne',sans-serif;font-size:.7rem;font-weight:700;
  letter-spacing:.1em;text-transform:uppercase;
  color:var(--muted);
  display:flex;align-items:center;gap:.4rem;
  cursor:none;transition:color .2s;
}
.pr-back:hover{color:var(--purple)}
.pr-inner{max-width:360px;margin:0 auto;width:100%}
.pr-heading{margin-bottom:2rem}
.pr-heading h1{font-family:'Instrument Serif',serif;font-size:2.2rem;line-height:1.1;color:var(--ink);letter-spacing:-.02em}
.pr-heading h1 em{font-style:italic;color:var(--purple)}
.pr-heading p{font-size:.9rem;color:var(--muted);margin-top:.5rem;font-weight:300}
.pr-divider{height:1px;background:var(--divider);margin:1.5rem 0}

/* ── FORM ── */
.form{display:flex;flex-direction:column;gap:1.1rem}
.field{display:flex;flex-direction:column;gap:.45rem}
.field label{
  font-family:'Syne',sans-serif;
  font-size:.65rem;font-weight:700;
  letter-spacing:.18em;text-transform:uppercase;
  color:var(--muted);
}
.input-wrap{position:relative}
.input-icon{
  position:absolute;left:1rem;top:50%;transform:translateY(-50%);
  color:var(--muted);width:16px;height:16px;
  pointer-events:none;transition:color .2s;flex-shrink:0;
}
.field input{
  width:100%;
  background:var(--offwhite);
  border:1.5px solid var(--divider);
  border-radius:12px;
  padding:.85rem 1rem .85rem 2.8rem;
  font-family:'DM Sans',sans-serif;
  font-size:.9rem;color:var(--ink);
  transition:border-color .2s,box-shadow .2s,background .2s;
  outline:none;
}
.field input::placeholder{color:rgba(100,116,139,.5)}
.field input:focus{
  background:#fff;
  border-color:rgba(124,58,237,.4);
  box-shadow:0 0 0 4px rgba(124,58,237,.08);
}
.input-wrap:focus-within .input-icon{color:var(--purple)}

/* ── SUBMIT ── */
.btn-submit{
  margin-top:.5rem;
  width:100%;padding:.95rem 1rem;
  background:var(--grad);
  border:none;border-radius:100px;
  color:#fff;
  font-family:'Syne',sans-serif;
  font-size:.78rem;font-weight:700;
  letter-spacing:.1em;text-transform:uppercase;
  cursor:none;
  transition:transform .2s,box-shadow .2s;
  box-shadow:0 6px 24px rgba(124,58,237,.35);
}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(124,58,237,.5)}
.btn-submit:active{transform:translateY(0)}

/* ── BOTTOM ── */
.pr-footer{text-align:center;margin-top:1.5rem;font-size:.85rem;color:var(--muted)}
.pr-footer a{color:var(--purple);font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;transition:opacity .15s}
.pr-footer a:hover{opacity:.7}
.pr-copyright{text-align:center;margin-top:1rem;font-size:.72rem;color:rgba(100,116,139,.4);letter-spacing:.05em}

/* ── ERROR BLADE COMPATIBLE ── */
.alert-error{
  background:rgba(239,68,68,.06);
  border:1px solid rgba(239,68,68,.2);
  border-radius:12px;
  padding:.85rem 1rem;
  font-size:.82rem;
  color:#B91C1C;
  margin-bottom:.5rem;
  display:flex;align-items:center;gap:.5rem;
}
.alert-error::before{content:'⚠';font-size:.9rem}

/* ── ANIMATION ── */
.panel-right{animation:slideIn .6s cubic-bezier(.22,1,.36,1) both}
.panel-left{animation:slideInL .6s cubic-bezier(.22,1,.36,1) both}
@keyframes slideIn{from{opacity:0;transform:translateX(24px)}to{opacity:1;transform:none}}
@keyframes slideInL{from{opacity:0;transform:translateX(-24px)}to{opacity:1;transform:none}}

/* ── RESPONSIVE ── */
@media(max-width:768px){
  body{padding:1rem;cursor:auto;background:var(--white)}
  #cur{display:none}
  .bg-left,.bg-right{display:none}
  .page-wrap{grid-template-columns:1fr;box-shadow:var(--shadow-md);border-radius:22px}
  .panel-left{display:none}
  .panel-right{padding:2.5rem 2rem}
  .pr-inner{max-width:100%}
}
</style>
</head>
<body>

<div id="cur"><div id="cd"></div><div id="cr"></div></div>
<div class="bg-left"></div>
<div class="bg-right"></div>
<div class="bg-noise"></div>

<div class="page-wrap">

  <!-- ── PANNEAU GAUCHE ── -->
  <div class="panel-left">
    <div class="pl-logo">
      <div class="pl-logo-mark"><img src="/medias/Syntriforg[1].png" alt="SyntriForge" onerror="this.style.display='none';this.parentElement.innerHTML='🎓'"></div>
      <div class="pl-logo-text">SyntriForge <span>Edu</span></div>
    </div>

    <div class="pl-body">
      <div class="pl-tag">Plateforme académique</div>
      <h2 class="pl-title">
        Votre espace<br>
        <em>éducatif</em><br>
        vous attend.
      </h2>
      <p class="pl-desc">Accédez à tous vos outils pédagogiques, vos données académiques et votre réseau scolaire en un seul endroit.</p>

      <div class="pl-visual">
        <div class="pl-card">
          <div class="pl-card-icon">📊</div>
          <div><div class="pl-card-title">Analytics en temps réel</div><div class="pl-card-sub">Suivez les performances de vos élèves</div></div>
        </div>
        <div class="pl-card">
          <div class="pl-card-icon">🎓</div>
          <div><div class="pl-card-title">Gestion des certifications</div><div class="pl-card-sub">Diplômes & attestations numériques</div></div>
        </div>
        <div class="pl-card">
          <div class="pl-card-icon">🔐</div>
          <div><div class="pl-card-title">Sécurité institutionnelle</div><div class="pl-card-sub">Données protégées 24/7</div></div>
        </div>
      </div>
    </div>

    <div class="pl-stats">
      <div><div class="pl-stat-num">250+</div><div class="pl-stat-lbl">Établissements</div></div>
      <div><div class="pl-stat-num">50K+</div><div class="pl-stat-lbl">Étudiants actifs</div></div>
      <div><div class="pl-stat-num">95%</div><div class="pl-stat-lbl">Satisfaction</div></div>
    </div>
  </div>

  <!-- ── PANNEAU DROIT (FORMULAIRE) ── -->
  <div class="panel-right">
    <a href="/" class="pr-back">← Accueil</a>
    <div class="pr-inner">

      <div class="pr-heading">
        <h1>Bon<br>retour <em>parmi nous</em></h1>
        <p>Accédez à votre espace personnel</p>
      </div>

      <div class="pr-divider"></div>

      {{-- Blade : erreurs de validation --}}
      @if ($errors->any())
        @foreach ($errors->all() as $error)
          <div class="alert-error">{{ $error }}</div>
        @endforeach
      @endif

      {{-- Blade : message de session --}}
      @if (session('status'))
        <div class="alert-error" style="background:rgba(22,163,74,.06);border-color:rgba(22,163,74,.2);color:#15803D">
          ✓ {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="form">
        @csrf

        <!-- Email -->
        <div class="field">
          <label for="email">Adresse email</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="4" width="20" height="16" rx="3"/>
              <path d="M2 8l10 6 10-6"/>
            </svg>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="vous@exemple.com" required autocomplete="email">
          </div>
        </div>

        <!-- Matricule -->
        <div class="field">
          <label for="matricule">Numéro matricule</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="5" y="2" width="14" height="20" rx="2"/>
              <path d="M9 7h6M9 11h6M9 15h4"/>
            </svg>
            <input type="text" id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Ex: MAT-20240001" required autocomplete="username">
          </div>
        </div>

        <!-- Mot de passe -->
        <div class="field">
          <label for="password">Mot de passe</label>
          <div class="input-wrap">
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
          </div>
        </div>

        <button type="submit" class="btn-submit">Se connecter →</button>
      </form>


      <p class="pr-copyright">© 2026 SyntriForge Edu. Tous droits réservés.</p>

    </div>
  </div>

</div>

<script>
// ── CURSOR ──
const cd=document.getElementById('cd'),cr=document.getElementById('cr');
let mx=0,my=0,rx=0,ry=0;
if(cd&&cr){
  document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;cd.style.left=mx+'px';cd.style.top=my+'px'});
  (function loop(){rx+=(mx-rx)*.1;ry+=(my-ry)*.1;cr.style.left=rx+'px';cr.style.top=ry+'px';requestAnimationFrame(loop)})();
  document.querySelectorAll('a,button,input,.pl-card').forEach(el=>{
    el.addEventListener('mouseenter',()=>document.body.classList.add('hov'));
    el.addEventListener('mouseleave',()=>document.body.classList.remove('hov'));
  });
}

// ── INPUT INTERACTION ──
document.querySelectorAll('.field input').forEach(input=>{
  // Shake on invalid
  input.addEventListener('invalid',()=>{
    input.style.animation='none';
    requestAnimationFrame(()=>{
      input.style.animation='shake .4s ease';
    });
  });
});

// ── SUBMIT BUTTON LOADING ──
document.querySelector('.form').addEventListener('submit',function(){
  const btn=this.querySelector('.btn-submit');
  btn.textContent='Connexion en cours…';
  btn.style.opacity='.8';
});

// ── PARALLAX BG ──
document.addEventListener('mousemove',e=>{
  const cx=window.innerWidth/2,cy=window.innerHeight/2;
  const dx=(e.clientX-cx)/cx,dy=(e.clientY-cy)/cy;
  const left=document.querySelector('.bg-left');
  const cards=document.querySelectorAll('.pl-card');
  if(left)left.style.backgroundPosition=`${50+dx*3}% ${50+dy*3}%`;
  cards.forEach((c,i)=>{c.style.transform=`translateX(${dx*(i+1)*3}px)`});
});

// ── SHAKE ANIMATION ──
const style=document.createElement('style');
style.textContent='@keyframes shake{0%,100%{transform:none}20%,60%{transform:translateX(-4px)}40%,80%{transform:translateX(4px)}}';
document.head.appendChild(style);
</script>
</body>
</html>
