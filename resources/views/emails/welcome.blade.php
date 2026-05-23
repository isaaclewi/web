<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenue</title>
<style>
  body { margin:0; padding:0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background:#f4f6f9; color:#1e293b; }
  .wrapper { max-width:600px; margin:0 auto; padding:32px 16px; }
  .card { background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.08); }
  /* Header */
  .header { background:linear-gradient(135deg,#1f2937 0%,#374151 100%); padding:36px 40px; text-align:center; }
  .header .logo-ring { width:64px; height:64px; border-radius:50%; background:rgba(255,255,255,.15); display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px; }
  .header h1 { margin:0; color:#fff; font-size:22px; font-weight:700; letter-spacing:-.3px; }
  .header p  { margin:6px 0 0; color:rgba(255,255,255,.65); font-size:13px; }
  /* Body */
  .body { padding:36px 40px; }
  .greeting { font-size:17px; font-weight:600; color:#1e293b; margin:0 0 12px; }
  .intro    { font-size:14px; color:#475569; line-height:1.7; margin:0 0 28px; }
  /* Credentials box */
  .creds { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:28px; }
  .creds-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#94a3b8; margin:0 0 18px; }
  .cred-row { display:flex; align-items:center; gap:14px; padding:10px 0; border-bottom:1px solid #f1f5f9; }
  .cred-row:last-child { border-bottom:none; padding-bottom:0; }
  .cred-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .ci-slate  { background:#f1f5f9; }
  .ci-green  { background:#dcfce7; }
  .ci-amber  { background:#fef3c7; }
  .ci-indigo { background:#ede9fe; }
  .cred-icon svg { width:18px; height:18px; }
  .cred-label { font-size:11px; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.05em; }
  .cred-value { font-size:14px; font-weight:700; color:#1e293b; font-family: 'Courier New', monospace; }
  .role-badge { display:inline-block; background:#e0f2fe; color:#0369a1; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
  /* CTA */
  .cta-wrap { text-align:center; margin-bottom:28px; }
  .cta-btn { display:inline-block; background:#1f2937; color:#fff !important; text-decoration:none; padding:13px 32px; border-radius:10px; font-size:14px; font-weight:600; letter-spacing:.02em; }
  /* Warning */
  .warning { background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:14px 18px; display:flex; gap:12px; align-items:flex-start; margin-bottom:28px; }
  .warning svg { flex-shrink:0; margin-top:2px; }
  .warning p { margin:0; font-size:13px; color:#9a3412; line-height:1.6; }
  /* Footer */
  .footer { background:#f8fafc; padding:20px 40px; text-align:center; border-top:1px solid #e2e8f0; }
  .footer p { margin:0; font-size:12px; color:#94a3b8; line-height:1.7; }
  @media(max-width:480px){
    .body, .header, .footer { padding-left:20px; padding-right:20px; }
    .creds { padding:18px 16px; }
  }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">

    {{-- ── HEADER ── --}}
    <div class="header">
      <div class="logo-ring">
        <svg width="30" height="30" fill="none" stroke="white" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0
               01.665 6.479A11.952 11.952 0 0012 20.055a11.952
               11.952 0 00-6.824-2.998 12.078 12.078 0
               01.665-6.479L12 14z"/>
        </svg>
      </div>
      <h1>{{ $payload['institution'] }}</h1>
      <p>Plateforme de gestion scolaire</p>
    </div>

    {{-- ── BODY ── --}}
    <div class="body">

      <p class="greeting">Bonjour {{ $payload['prenom'] }} {{ $payload['nom'] }},</p>
      <p class="intro">
        Votre compte a été créé avec succès sur la plateforme de <strong>{{ $payload['institution'] }}</strong>.
        Vous trouverez ci-dessous vos identifiants de connexion. Conservez-les précieusement et
        changez votre mot de passe dès votre première connexion.
      </p>

      {{-- ── CREDENTIALS ── --}}
      <div class="creds">
        <div class="creds-title">Vos identifiants</div>

        {{-- Matricule --}}
        @if(!empty($payload['matricule']))
        <div class="cred-row">
          <div class="cred-icon ci-indigo">
            <svg fill="none" stroke="#7c3aed" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
            </svg>
          </div>
          <div>
            <div class="cred-label">Matricule</div>
            <div class="cred-value">{{ $payload['matricule'] }}</div>
          </div>
        </div>
        @endif

        {{-- Rôle --}}
        <div class="cred-row">
          <div class="cred-icon ci-slate">
            <svg fill="none" stroke="#475569" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
          <div>
            <div class="cred-label">Rôle</div>
            <div><span class="role-badge">{{ ucfirst($payload['role']) }}</span></div>
          </div>
        </div>

        {{-- Email --}}
        <div class="cred-row">
          <div class="cred-icon ci-green">
            <svg fill="none" stroke="#16a34a" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0
                   002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <div class="cred-label">Adresse e-mail (identifiant)</div>
            <div class="cred-value">{{ $payload['email'] }}</div>
          </div>
        </div>

        {{-- Mot de passe --}}
        <div class="cred-row">
          <div class="cred-icon ci-amber">
            <svg fill="none" stroke="#d97706" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4
                   a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
          </div>
          <div>
            <div class="cred-label">Mot de passe temporaire</div>
            <div class="cred-value">{{ $payload['password_raw'] }}</div>
          </div>
        </div>
      </div>

      {{-- ── CTA ── --}}
      <div class="cta-wrap">
        <a href="{{ $payload['app_url'] }}" class="cta-btn">
          Accéder à ma plateforme →
        </a>
      </div>

      {{-- ── WARNING ── --}}
      <div class="warning">
        <svg width="18" height="18" fill="none" stroke="#ea580c" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94
               a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <p>
          <strong>Important :</strong> Ce mot de passe est temporaire. Veuillez le modifier
          immédiatement après votre première connexion pour sécuriser votre compte.
          Ne partagez jamais vos identifiants.
        </p>
      </div>

    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
      <p>
        Cet e-mail a été envoyé automatiquement par la plateforme <strong>{{ $payload['institution'] }}</strong>.<br>
        Si vous n'êtes pas concerné(e), veuillez ignorer ce message.<br>
        © {{ date('Y') }} {{ $payload['institution'] }} — Tous droits réservés.
      </p>
    </div>

  </div>
</div>
</body>
</html>