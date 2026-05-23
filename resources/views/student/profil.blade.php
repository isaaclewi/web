@extends('student.master')
@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')
@section('page-sub', 'Informations personnelles, parents et situation financière')

@section('content')
    <style>
        /* ═══════════════════════════════════════════════════════
       PROFIL ÉTUDIANT — Responsive CSS
       Mobile-first : 320px → 480px → 640px → 900px → 1100px
    ═══════════════════════════════════════════════════════ */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        /* ── BREADCRUMB ── */
        .bc {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .35rem;
            font-size: .8rem;
            color: var(--muted);
            margin-bottom: 1.25rem;
        }

        .bc a {
            color: var(--muted);
            text-decoration: none;
        }

        .bc a:hover {
            color: var(--ink);
        }

        .bc-sep {
            color: #cbd5e1;
        }

        .bc-cur {
            color: var(--ink);
            font-weight: 500;
        }

        /* ── HERO BANNER ── */
        .prof-banner {
            position: relative;
            margin-bottom: 3.5rem;
        }

        .prof-banner-bg {
            height: 140px;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
        }

        @media (min-width: 640px) {
            .prof-banner-bg {
                height: 160px;
            }
        }

        .prof-avatar-wrap {
            position: absolute;
            bottom: -40px;
            left: 1rem;
        }

        @media (min-width: 480px) {
            .prof-avatar-wrap {
                left: 1.5rem;
            }
        }

        @media (min-width: 640px) {
            .prof-avatar-wrap {
                left: 2rem;
            }
        }

        .prof-avatar-wrap img {
            width: 76px;
            height: 76px;
            border-radius: 1rem;
            border: 4px solid white;
            object-fit: cover;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .15);
            display: block;
        }

        @media (min-width: 480px) {
            .prof-avatar-wrap img {
                width: 88px;
                height: 88px;
            }
        }

        @media (min-width: 640px) {
            .prof-avatar-wrap img {
                width: 90px;
                height: 90px;
            }
        }

        /* ── NOM + BADGES ROW ── */
        .prof-head {
            display: flex;
            flex-direction: column;
            gap: .875rem;
            margin-bottom: 1.25rem;
        }

        @media (min-width: 640px) {
            .prof-head {
                flex-direction: row;
                align-items: flex-start;
                justify-content: space-between;
                flex-wrap: wrap;
                margin-bottom: 1.5rem;
            }
        }

        .prof-head-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--ink);
        }

        @media (min-width: 480px) {
            .prof-head-name {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 640px) {
            .prof-head-name {
                font-size: 1.6rem;
            }
        }

        .prof-head-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .4rem .5rem;
            margin-top: .35rem;
            font-size: .78rem;
            color: var(--muted);
        }

        @media (min-width: 480px) {
            .prof-head-meta {
                font-size: .8rem;
            }
        }

        /* Masquer les séparateurs · sur très petits écrans pour éviter les sauts */
        .prof-head-meta .sep {
            display: none;
        }

        @media (min-width: 480px) {
            .prof-head-meta .sep {
                display: inline;
            }
        }

        /* ── KPI MOY/RANG ── */
        .prof-kpi-row {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .prof-kpi-box {
            text-align: center;
            border-radius: .875rem;
            padding: .75rem 1rem;
            min-width: 80px;
        }

        @media (min-width: 480px) {
            .prof-kpi-box {
                padding: .875rem 1.25rem;
                min-width: 90px;
            }
        }

        .prof-kpi-box .mono {
            font-size: 1.3rem;
            font-weight: 800;
        }

        @media (min-width: 480px) {
            .prof-kpi-box .mono {
                font-size: 1.5rem;
            }
        }

        /* ── TABS ── */
        .ptab-bar {
            display: flex;
            gap: .25rem;
            background: #f1f5f9;
            border-radius: .5rem;
            padding: .25rem;
            margin-bottom: 1.25rem;
            /* pleine largeur sur mobile, max 480px sur desktop */
            width: 100%;
        }

        @media (min-width: 480px) {
            .ptab-bar {
                max-width: 480px;
            }
        }

        .ptab {
            flex: 1;
            padding: .45rem .5rem;
            border-radius: .375rem;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: none;
            color: var(--muted);
            transition: all .2s;
            white-space: nowrap;
        }

        @media (min-width: 380px) {
            .ptab {
                font-size: .8rem;
                padding: .45rem .75rem;
            }
        }

        .ptab.active {
            background: white;
            color: var(--ink);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }

        /* ── GRILLES 2 COLONNES → 1 sur mobile ── */
        .two-col {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }

        @media (min-width: 760px) {
            .two-col {
                grid-template-columns: 1fr 1fr !important;
                gap: 1.25rem !important;
            }
        }

        /* ── FINANCES KPI STRIP ── */
        .fin-kpi-strip {
            display: grid;
            grid-template-columns: 1fr;
            gap: .75rem;
            margin-bottom: 1.25rem;
        }

        @media (min-width: 480px) {
            .fin-kpi-strip {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* ── CARD ── */
        .card {
            border-radius: .875rem;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .5rem;
            padding: .875rem 1rem;
        }

        @media (min-width: 480px) {
            .card-header {
                padding: 1rem 1.25rem;
            }
        }

        /* ── INFO GRID ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            padding: .875rem 1rem;
            gap: .75rem;
        }

        @media (min-width: 380px) {
            .info-grid {
                grid-template-columns: 1fr 1fr;
                gap: .875rem 1rem;
            }
        }

        @media (min-width: 480px) {
            .info-grid {
                padding: 1rem 1.25rem;
            }
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: .2rem;
        }

        .info-item.full {
            grid-column: 1 / -1;
        }

        .info-key {
            font-size: .7rem;
            color: var(--muted);
            font-weight: 500;
        }

        .info-val {
            font-size: .85rem;
            color: var(--ink);
            font-weight: 500;
            line-height: 1.4;
        }

        /* ── TABLEAU FINANCES ── */
        .t-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .t-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 640px;
            /* scroll horizontal en dessous */
        }

        .t-table thead th {
            padding: .55rem .875rem;
            background: #f8fafc;
            text-align: left;
            font-size: .67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .t-table td {
            padding: .65rem .875rem;
            border-bottom: 1px solid #f8fafc;
            font-size: .8rem;
            color: var(--ink);
            vertical-align: middle;
        }

        .t-table tr:last-child td {
            border-bottom: none;
        }

        .t-table tr:hover td {
            background: #f8fafc;
        }

        /* ── BARRE DE PROGRESSION ── */
        .prog-wrap {
            padding: 1rem 1.25rem;
            border-top: 1px solid #f1f5f9;
        }

        .prog-head {
            display: flex;
            justify-content: space-between;
            margin-bottom: .375rem;
            font-size: .78rem;
            color: var(--muted);
            flex-wrap: wrap;
            gap: .25rem;
        }

        .prog {
            height: 10px;
            background: #f1f5f9;
            border-radius: 99px;
            overflow: hidden;
        }

        .prog-fill {
            height: 100%;
            border-radius: 99px;
            transition: width .4s;
        }

        /* ── PARENT CARD HEADER ── */
        .parent-card-head {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: .875rem;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }

        .parent-avatar {
            width: 50px;
            height: 50px;
            background: var(--primary-light);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        @media (min-width: 480px) {
            .parent-avatar {
                width: 56px;
                height: 56px;
                font-size: 1.5rem;
            }
        }

        .parent-badge {
            margin-left: auto;
        }

        /* ── EMPTY STATE ── */
        .empty {
            padding: 2.5rem 1rem;
            text-align: center;
        }

        .empty-icon {
            font-size: 2.5rem;
            margin-bottom: .75rem;
        }

        .empty-text {
            font-size: .85rem;
            color: var(--muted);
        }

        /* ── GRADE PILL ── */
        .gp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            font-weight: 800;
            border-radius: .5rem;
            padding: .2rem .6rem;
            font-family: var(--mono);
        }

        .gp-A {
            background: #dcfce7;
            color: #15803d;
        }

        .gp-B {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .gp-C {
            background: #fef3c7;
            color: #b45309;
        }

        .gp-D {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* ── BADGES ── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: .18rem .52rem;
            border-radius: 20px;
            font-size: .67rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .b-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .b-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .b-amber {
            background: #fef3c7;
            color: #d97706;
        }

        .b-indigo {
            background: #e0e7ff;
            color: #4338ca;
        }

        .b-purple {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .b-gray {
            background: #f1f5f9;
            color: #64748b;
        }

        /* ── Mono ── */
        .mono {
            font-family: var(--mono, 'Courier New', monospace);
        }

        /* ── Ajustements tablette ── */
        @media (max-width: 900px) {

            /* La sidebar "Scolarité + Compte" passe sous "Infos perso" sur mobile */
            .prof-info-right {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* ── Très petits écrans ── */
        @media (max-width: 360px) {
            .ptab {
                font-size: .68rem;
                padding: .4rem .3rem;
            }

            .prof-head-name {
                font-size: 1.1rem;
            }

            .kpi-val,
            .prof-kpi-box .mono {
                font-size: 1.1rem;
            }
        }
    </style>
    <nav class="bc">
        <a href="{{ route('student.dashboard') }}">Tableau de bord</a>
        <span class="bc-sep">›</span><span class="bc-cur">Mon Profil</span>
    </nav>

    {{-- HERO PROFIL --}}
    <div style="position:relative;margin-bottom:3.5rem;">
        <div
            style="height:160px;background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0f172a 100%);border-radius:1rem;overflow:hidden;position:relative;">
            <div
                style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.03)1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03)1px,transparent 1px);background-size:36px 36px;">
            </div>
            <div
                style="position:absolute;top:20px;right:40px;width:120px;height:120px;border-radius:50%;background:rgba(99,102,241,.08);">
            </div>
        </div>
        <div style="position:absolute;bottom:-40px;left:2rem;">
            <div style="position:relative;display:inline-block;">
              @php
        $studentAvatarUrl = $apprenant->photo
            ? \Illuminate\Support\Facades\Storage::disk('root_storage')->url($apprenant->photo)
            : null;
    @endphp
 
    <form method="POST" action="{{ route('student.profil.avatar') }}"
          enctype="multipart/form-data" id="studentAvatarForm">
        @csrf
        <input type="file" id="studentAvatarInput" name="avatar"
               accept=".jpg,.jpeg,.png,.webp" style="display:none;"
               onchange="this.closest('form').submit()">
    </form>
 
    <div style="position:relative;display:inline-block;">
        @if($studentAvatarUrl)
            <img id="studentAvatarPreview" src="{{ $studentAvatarUrl }}"
                 style="width:90px;height:90px;border-radius:1rem;border:4px solid white;
                        object-fit:cover;box-shadow:0 8px 24px rgba(0,0,0,.15);">
        @else
            <div id="studentAvatarPreview"
                 style="width:90px;height:90px;border-radius:1rem;border:4px solid white;
                        display:flex;align-items:center;justify-content:center;
                        background:#e2e8f0;box-shadow:0 8px 24px rgba(0,0,0,.15);">
                <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M22 10l-10-5L2 10l10 5 10-5z"></path>
                    <path d="M6 12v5c0 1 3 3 6 3s6-2 6-3v-5"></path>
                </svg>
            </div>
        @endif
        <label for="studentAvatarInput"
               style="position:absolute;bottom:2px;right:2px;width:28px;height:28px;
                      border-radius:50%;background:#1f2937;color:white;border:2px solid white;
                      display:flex;align-items:center;justify-content:center;cursor:pointer;
                      font-size:.8rem;" title="Changer la photo">📷</label>
    </div>
 
    <script>
    document.getElementById('studentAvatarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const preview = document.getElementById('studentAvatarPreview');
                if (preview.tagName === 'IMG') {
                    preview.src = evt.target.result;
                } else {
                    // Remplacer le div SVG par une image
                    const img = document.createElement('img');
                    img.id = 'studentAvatarPreview';
                    img.src = evt.target.result;
                    img.style.cssText = preview.style.cssText;
                    preview.parentNode.replaceChild(img, preview);
                }
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
            </div>
        </div>
    </div>

    {{-- NOM + BADGES --}}
    <div
        style="margin-bottom:1.5rem;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="font-size:1.6rem;font-weight:700;color:var(--ink);">{{ $apprenant->prenom }} {{ $apprenant->nom }}
            </div>
            <div
                style="display:flex;align-items:center;gap:.625rem;flex-wrap:wrap;margin-top:.375rem;font-size:.8rem;color:var(--muted);">
                <span
                    style="background:var(--primary-light);color:var(--primary);border-radius:.375rem;padding:.2rem .6rem;font-size:.7rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;font-family:var(--mono);">{{ $apprenant->matricule }}</span>
                <span>·</span>
                <span>{{ $classe->name ?? 'Aucune classe' }}</span>
                @if ($apprenant->niveau)
                    <span>·</span><span class="badge b-indigo">{{ $apprenant->niveau->name }}</span>
                @endif
                @if ($apprenant->filiere)
                    <span class="badge b-purple">{{ $apprenant->filiere->name }}</span>
                @endif
                <span>·</span>
                @if ($apprenant->status === 'actif')
                    <span style="color:var(--success);font-weight:600;font-size:.75rem;">● Actif</span>
                @else
                    <span style="color:var(--danger);font-weight:600;font-size:.75rem;">●
                        {{ ucfirst($apprenant->status) }}</span>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:.5rem;">
            @if ($moyenneGenerale)
                <div
                    style="text-align:center;background:var(--primary-light);border-radius:.875rem;padding:.875rem 1.25rem;">
                    <div class="mono" style="font-size:1.5rem;font-weight:800;color:var(--primary);">
                        {{ $moyenneGenerale }}</div>
                    <div style="font-size:.7rem;color:var(--muted);">Moy. générale</div>
                </div>
            @endif
            @if ($rang['rang'] !== '—')
                <div
                    style="text-align:center;background:#f8fafc;border:1px solid var(--border);border-radius:.875rem;padding:.875rem 1.25rem;">
                    <div class="mono" style="font-size:1.5rem;font-weight:800;color:var(--ink);">{{ $rang['rang'] }}<span
                            style="font-size:.9rem;font-weight:400;color:var(--muted);">/{{ $rang['total'] }}</span></div>
                    <div style="font-size:.7rem;color:var(--muted);">Rang classe</div>
                </div>
            @endif
        </div>
    </div>

    {{-- MINI TABS --}}
    <div
        style="display:flex;gap:.25rem;background:#f1f5f9;border-radius:.5rem;padding:.25rem;margin-bottom:1.5rem;max-width:480px;">
        <button class="ptab active" onclick="switchPTab('info',this)"
            style="flex:1;padding:.45rem .75rem;border-radius:.375rem;font-size:.8rem;font-weight:600;cursor:pointer;border:none;background:white;color:var(--ink);box-shadow:0 1px 4px rgba(0,0,0,.08);transition:all .2s;">Informations</button>
        <button class="ptab" onclick="switchPTab('parents',this)"
            style="flex:1;padding:.45rem .75rem;border-radius:.375rem;font-size:.8rem;font-weight:500;cursor:pointer;border:none;background:none;color:var(--muted);transition:all .2s;">Parents
            / Tuteurs</button>
        <button class="ptab" onclick="switchPTab('finances',this)"
            style="flex:1;padding:.45rem .75rem;border-radius:.375rem;font-size:.8rem;font-weight:500;cursor:pointer;border:none;background:none;color:var(--muted);transition:all .2s;">Finances</button>
    </div>

    {{-- ── TAB INFO ── --}}
    <div id="ptab-info">
        <div class="two-col">

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Informations personnelles</div>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-key">Nom complet</div>
                        <div class="info-val">{{ $apprenant->prenom }} {{ $apprenant->nom }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Genre</div>
                        <div class="info-val">{{ $apprenant->sexe === 'F' ? '♀ Féminin' : '♂ Masculin' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Date de naissance</div>
                        <div class="info-val">
                            {{ $apprenant->date_naissance ? \Carbon\Carbon::parse($apprenant->date_naissance)->format('d/m/Y') : '—' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Matricule</div>
                        <div class="info-val mono" style="font-size:.82rem;">{{ $apprenant->matricule }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Statut</div>
                        <div class="info-val"><span
                                class="badge {{ $apprenant->status === 'actif' ? 'b-green' : 'b-gray' }}">{{ ucfirst($apprenant->status) }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Année académique</div>
                        <div class="info-val">{{ $apprenant->annee_academique ?? '—' }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-key">Établissement</div>
                        <div class="info-val">{{ $institution->name ?? '—' }}@if ($institution?->commune)
                                · {{ $institution->commune }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Scolarité</div>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-key">Classe</div>
                            <div class="info-val"><span class="badge b-indigo">{{ $classe->name ?? '—' }}</span></div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Niveau</div>
                            <div class="info-val">{{ $apprenant->niveau->name ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Filière</div>
                            <div class="info-val">{{ $apprenant->filiere->name ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Matières</div>
                            <div class="info-val">{{ $subjects->count() }}</div>
                        </div>
                        <div class="info-item full">
                            <div class="info-key">Résultats</div>
                            <div class="info-val" style="display:flex;gap:.625rem;margin-top:.35rem;">
                                @if ($moyenneGenerale)
                                    <span
                                        class="gp {{ $moyenneGenerale >= 14 ? 'gp-A' : ($moyenneGenerale >= 10 ? 'gp-B' : ($moyenneGenerale >= 8 ? 'gp-C' : 'gp-D')) }}">{{ $moyenneGenerale }}</span>
                                    <span style="font-size:.8rem;color:var(--muted);">Rang
                                        {{ $rang['rang'] }}/{{ $rang['total'] }}</span>
                                @else
                                    <span style="color:var(--muted);font-size:.85rem;">Pas encore de résultats</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Compte utilisateur</div>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-key">Identifiant</div>
                            <div class="info-val">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Email</div>
                            <div class="info-val" style="font-size:.8rem;">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Membre depuis</div>
                            <div class="info-val">
                                {{ Auth::user()->created_at?->locale('fr')->isoFormat('D MMM YYYY') ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Rôle</div>
                            <div class="info-val"><span
                                    class="badge b-purple">{{ Auth::user()->role ?? 'apprenant' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB PARENTS ── --}}
    <div id="ptab-parents" style="display:none;">
        @if ($parents->isEmpty())
            <div class="card">
                <div class="empty">
                    <div class="empty-icon">👨‍👩‍👦</div>
                    <div class="empty-text">Aucun parent ou tuteur enregistré</div>
                </div>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1.25rem;" class="two-col">
                @foreach ($parents as $parent)
                    <div class="card">
                        <div
                            style="padding:1.5rem;display:flex;align-items:center;gap:1rem;border-bottom:1px solid #f1f5f9;">
                            <div
                                style="width:56px;height:56px;background:var(--primary-light);border-radius:1rem;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">
                                {{ $parent->sexe === 'F' ? '👩' : '👨' }}
                            </div>
                            <div>
                                <div style="font-weight:700;color:var(--ink);">{{ $parent->prenom }} {{ $parent->nom }}
                                </div>
                                <div style="font-size:.72rem;color:var(--muted);margin-top:.2rem;">
                                    {{ ucfirst($parent->pivot->lien ?? 'Tuteur') }}
                                </div>
                            </div>
                            @if ($parent->status === 'actif')
                                <span class="badge b-green" style="margin-left:auto;">Actif</span>
                            @endif
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-key">Téléphone</div>
                                <div class="info-val">{{ $parent->telephone ?? '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-key">Email</div>
                                <div class="info-val" style="font-size:.8rem;">{{ $parent->email ?? '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-key">Profession</div>
                                <div class="info-val">{{ $parent->profession ?? '—' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-key">Lien</div>
                                <div class="info-val"><span
                                        class="badge b-indigo">{{ ucfirst($parent->pivot->lien ?? 'Tuteur') }}</span>
                                </div>
                            </div>
                            @if ($parent->adresse)
                                <div class="info-item full">
                                    <div class="info-key">Adresse</div>
                                    <div class="info-val">{{ $parent->adresse }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── TAB FINANCES ── --}}
    <div id="ptab-finances" style="display:none;">

        {{-- Récap --}}
        <div class="fin-kpi-strip">
            <div class="kpi" style="padding:1rem;">
                <div class="kpi-accent" style="background:linear-gradient(90deg,var(--primary),var(--primary-mid));">
                </div>
                <div class="kpi-val" style="font-size:1.4rem;">{{ number_format($totalDu, 0, ',', ' ') }}</div>
                <div class="kpi-lbl">Total dû ({{ $institution->devise ?? 'FCFA' }})</div>
            </div>
            <div class="kpi" style="padding:1rem;">
                <div class="kpi-accent" style="background:linear-gradient(90deg,var(--success),#34d399);"></div>
                <div class="kpi-val" style="font-size:1.4rem;color:var(--success);">
                    {{ number_format($totalPaye, 0, ',', ' ') }}</div>
                <div class="kpi-lbl">Total payé</div>
            </div>
            <div class="kpi" style="padding:1rem;">
                <div class="kpi-accent"
                    style="background:linear-gradient(90deg,{{ $totalReste > 0 ? 'var(--danger)' : 'var(--success)' }},{{ $totalReste > 0 ? '#f87171' : '#34d399' }});">
                </div>
                <div class="kpi-val"
                    style="font-size:1.4rem;color:{{ $totalReste > 0 ? 'var(--danger)' : 'var(--success)' }};">
                    {{ number_format($totalReste, 0, ',', ' ') }}</div>
                <div class="kpi-lbl">Reste à payer</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Historique des paiements</div>
                @if ($totalReste > 0)
                    <span class="badge b-red">{{ number_format($totalReste, 0, ',', ' ') }}
                        {{ $institution->devise ?? 'FCFA' }} restant</span>
                @else
                    <span class="badge b-green">Tout est à jour ✓</span>
                @endif
            </div>

            @if ($finances->isEmpty())
                <div class="empty">
                    <div class="empty-icon">💰</div>
                    <div class="empty-text">Aucun enregistrement financier</div>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <div class="t-table-wrap">
                    <table class="t-table">
                        <thead>
                            <tr>
                                <th>Période</th>
                                <th>Mois</th>
                                <th>Montant dû</th>
                                <th>Payé</th>
                                <th>Reste</th>
                                <th>Mode</th>
                                <th>Réf.</th>
                                <th>Date paiement</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($finances as $fin)
                                <tr>
                                    <td class="mono" style="font-size:.78rem;">{{ $fin->annee_academique ?? '—' }}</td>
                                    <td>{{ $fin->mois_label ?? ($fin->mois ? \Carbon\Carbon::create()->month($fin->mois)->locale('fr')->isoFormat('MMMM') : '—') }}
                                    </td>
                                    <td class="mono" style="font-weight:600;">
                                        {{ number_format($fin->montant_du, 0, ',', ' ') }}</td>
                                    <td class="mono" style="color:var(--success);font-weight:600;">
                                        {{ number_format($fin->montant_paye, 0, ',', ' ') }}</td>
                                    <td class="mono"
                                        style="color:{{ $fin->montant_reste > 0 ? 'var(--danger)' : 'var(--success)' }};font-weight:700;">
                                        {{ number_format($fin->montant_reste, 0, ',', ' ') }}</td>
                                    <td>{{ $fin->mode_paiement ?? '—' }}</td>
                                    <td class="mono" style="font-size:.72rem;color:var(--muted);">
                                        {{ $fin->reference ?? '—' }}</td>
                                    <td style="font-size:.78rem;">
                                        {{ $fin->date_paiement ? $fin->date_paiement->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        @if ($fin->statut === 'paye')
                                            <span class="badge b-green">Payé</span>
                                        @elseif($fin->statut === 'partiel')
                                            <span class="badge b-amber">Partiel</span>
                                        @else
                                            <span class="badge b-red">Impayé</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

                {{-- Barre de progression globale --}}
                @if ($totalDu > 0)
                    <div style="padding:1.25rem;border-top:1px solid #f1f5f9;">
                        @php $pctPay = round($totalPaye / $totalDu * 100); @endphp
                        <div
                            style="display:flex;justify-content:space-between;margin-bottom:.375rem;font-size:.78rem;color:var(--muted);">
                            <span>Progression globale des paiements</span>
                            <span class="mono"
                                style="font-weight:700;color:{{ $pctPay >= 100 ? 'var(--success)' : ($pctPay >= 50 ? 'var(--primary)' : 'var(--danger)') }};">{{ $pctPay }}%</span>
                        </div>
                        <div class="prog" style="height:10px;">
                            <div class="prog-fill"
                                style="width:{{ $pctPay }}%;background:{{ $pctPay >= 100 ? 'var(--success)' : ($pctPay >= 50 ? 'var(--primary)' : 'var(--danger)') }};">
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function switchPTab(id, btn) {
                ['info', 'parents', 'finances'].forEach(t => {
                    document.getElementById('ptab-' + t).style.display = t === id ? '' : 'none';
                });
                document.querySelectorAll('.ptab').forEach(b => {
                    b.style.background = 'none';
                    b.style.color = 'var(--muted)';
                    b.style.fontWeight = '500';
                    b.style.boxShadow = 'none';
                });
                btn.style.background = 'white';
                btn.style.color = 'var(--ink)';
                btn.style.fontWeight = '600';
                btn.style.boxShadow = '0 1px 4px rgba(0,0,0,.08)';
            }
        </script>
    @endpush
@endsection
