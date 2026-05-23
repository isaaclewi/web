@extends('teacher.master')

@section('content')
    @include('teacher.partials.css')

    {{-- ── BREADCRUMB ── --}}
    <nav class="bc">
        <a href="{{ route('teacher.dashboard') }}">Tableau de bord</a>
        <span class="bc-sep">›</span>
        <span class="bc-cur">Mon profil</span>
    </nav>

    @if (session('success'))
        <div class="alert alert-s">✅ {{ session('success') }}</div>
    @endif

    {{-- ── COVER + AVATAR ── --}}
    <div style="position:relative;margin-bottom:3.5rem;">
        <div
            style="height:180px;background:linear-gradient(135deg,#0f172a 0%,#134e4a 60%,#0d9488 100%);border-radius:1rem;overflow:hidden;position:relative;">
            <div
                style="position:absolute;inset:0;background-image:repeating-linear-gradient(45deg,rgba(255,255,255,.03) 0,rgba(255,255,255,.03) 1px,transparent 0,transparent 50%);background-size:20px 20px;">
            </div>
            <div
                style="position:absolute;bottom:1.25rem;left:9rem;color:rgba(255,255,255,.35);font-family:'Playfair Display',serif;font-size:1rem;font-style:italic;">
                "L'éducation est l'arme la plus puissante."
            </div>
        </div>
        <div style="position:absolute;bottom:-40px;left:2rem;z-index:2;">
        @php
        $teacherAvatarUrl = $teacher->photo
            ? \Illuminate\Support\Facades\Storage::disk('root_storage')->url($teacher->photo)
            : 'https://cdn-icons-png.flaticon.com/512/2995/2995620.png';
    @endphp
 
    <form method="POST" action="{{ route('teacher.profil.avatar') }}"
          enctype="multipart/form-data" id="teacherAvatarForm">
        @csrf
        <input type="file" id="teacherAvatarInput" name="avatar"
               accept=".jpg,.jpeg,.png,.webp" style="display:none;"
               onchange="this.closest('form').submit()">
    </form>
 
    <div style="position:relative;display:inline-block;">
        <img id="teacherAvatarPreview"
             src="{{ $teacherAvatarUrl }}"
             style="width:86px;height:86px;border-radius:1rem;border:4px solid white;
                    object-fit:cover;box-shadow:0 8px 24px rgba(0,0,0,.2);">
        <label for="teacherAvatarInput"
               style="position:absolute;bottom:2px;right:2px;width:28px;height:28px;
                      border-radius:50%;background:#0d9488;color:white;border:2px solid white;
                      display:flex;align-items:center;justify-content:center;cursor:pointer;
                      font-size:.8rem;box-shadow:0 2px 6px rgba(0,0,0,.2);"
               title="Changer la photo">📷</label>
    </div>
 
    {{-- Script aperçu --}}
    <script>
    document.getElementById('teacherAvatarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = evt => { document.getElementById('teacherAvatarPreview').src = evt.target.result; };
            reader.readAsDataURL(file);
        }
    });
    </script>
        </div>
    </div>

    {{-- ── NOM + BADGES ── --}}
    <div
        style="margin-bottom:1.5rem;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:var(--ink);">
                {{ $teacher->prenom }} {{ $teacher->nom }}</div>
            <div
                style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;margin-top:.35rem;font-size:.8125rem;color:var(--muted);">
                <span
                    style="background:var(--teal-light);color:var(--teal);border:1px solid rgba(13,148,136,.2);border-radius:.375rem;padding:.2rem .6rem;font-size:.7rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;font-family:var(--font-mono);">{{ $teacher->matricule ?? 'N/A' }}</span>
                <span>·</span>
                <span>{{ $subjects->pluck('name')->unique()->implode(', ') ?: 'Aucune matière' }}</span>
                <span>·</span>
                <span>{{ $institution->name ?? '' }}</span>
                <span>·</span>
                @if ($teacher->status === 'actif')
                    <span style="color:#10b981;font-weight:600;font-size:.75rem;">● En service</span>
                @else
                    <span style="color:#ef4444;font-weight:600;font-size:.75rem;">● {{ ucfirst($teacher->status) }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── MINI TABS ── --}}
    <div class="mini-tabs" style="max-width:420px;margin-bottom:1.5rem;">
        <button class="mini-tab active" onclick="switchPTab('info',this)">Informations</button>
        <button class="mini-tab" onclick="switchPTab('stats',this)">Statistiques</button>
        <button class="mini-tab" onclick="switchPTab('matieres',this)">Matières & Classes</button>
    </div>

    {{-- ── ONGLET : INFORMATIONS ── --}}
    <div id="ptab-info">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;" class="two-col">

            {{-- Infos perso --}}
            <div class="t-card">
                <div class="t-header">
                    <div class="t-title">Informations personnelles</div>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-key">Nom complet</div>
                        <div class="info-val">{{ $teacher->prenom }} {{ $teacher->nom }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Genre</div>
                        <div class="info-val">
                            {{ $teacher->sexe === 'F' ? '♀ Féminin' : ($teacher->sexe === 'M' ? '♂ Masculin' : '—') }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Spécialité</div>
                        <div class="info-val">{{ $teacher->specialite ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Type de contrat</div>
                        <div class="info-val">{{ $teacher->type_contrat ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Téléphone</div>
                        <div class="info-val">{{ $teacher->telephone ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Email</div>
                        <div class="info-val" style="font-size:.82rem;">{{ $teacher->email ?? $user->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Date de recrutement</div>
                        <div class="info-val">
                            {{ $teacher->date_recrutement ? \Carbon\Carbon::parse($teacher->date_recrutement)->locale('fr')->isoFormat('D MMMM YYYY') : '—' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Ancienneté</div>
                        <div class="info-val">
                            @if ($teacher->date_recrutement)
                                {{ \Carbon\Carbon::parse($teacher->date_recrutement)->locale('fr')->diffForHumans(null, true) }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Matricule</div>
                        <div class="info-val mono" style="font-size:.82rem;">{{ $teacher->matricule ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-key">Statut</div>
                        <div class="info-val">
                            @if ($teacher->status === 'actif')
                                <span class="badge b-green">Actif</span>
                            @else
                                <span class="badge b-gray">{{ ucfirst($teacher->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item full">
                        <div class="info-key">Établissement</div>
                        <div class="info-val">{{ $institution->name ?? '—' }}@if ($institution?->commune)
                                · {{ $institution->commune }}
                                @endif @if ($institution?->pays)
                                    · {{ $institution->pays }}
                                @endif
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                {{-- Compte utilisateur --}}
                <div class="t-card">
                    <div class="t-header">
                        <div class="t-title">Compte utilisateur</div>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-key">Nom d'utilisateur</div>
                            <div class="info-val">{{ $user->name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Email compte</div>
                            <div class="info-val" style="font-size:.82rem;">{{ $user->email }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Rôle</div>
                            <div class="info-val"><span class="badge b-purple">{{ $user->role ?? 'teacher' }}</span></div>
                        </div>
                        <div class="info-item">
                            <div class="info-key">Membre depuis</div>
                            <div class="info-val">{{ $user->created_at?->locale('fr')->isoFormat('D MMM YYYY') ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Évaluation --}}
                <div class="t-card">
                    <div class="t-header">
                        <div class="t-title">Résumé pédagogique</div>
                    </div>
                    <div class="t-body">
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.875rem;text-align:center;">
                            <div style="background:#f0fdfa;border-radius:.625rem;padding:1rem;">
                                <div class="mono" style="font-size:1.5rem;font-weight:800;color:var(--teal);">
                                    {{ $stats['average_grade'] }}</div>
                                <div style="font-size:.7rem;color:var(--muted);">Moy. générale</div>
                            </div>
                            <div style="background:#eef2ff;border-radius:.625rem;padding:1rem;">
                                <div class="mono" style="font-size:1.5rem;font-weight:800;color:#6366f1;">
                                    {{ $evaluations->count() }}</div>
                                <div style="font-size:.7rem;color:var(--muted);">Évaluations</div>
                            </div>
                            <div style="background:#ecfdf5;border-radius:.625rem;padding:1rem;">
                                <div class="mono" style="font-size:1.5rem;font-weight:800;color:#10b981;">
                                    {{ $stats['students'] }}</div>
                                <div style="font-size:.7rem;color:var(--muted);">Élèves</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ONGLET : STATISTIQUES ── --}}
    <div id="ptab-stats" style="display:none;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;" class="two-col">
            {{-- Progression --}}
            <div class="t-card">
                <div class="t-header">
                    <div class="t-title">Indicateurs d'activité</div>
                </div>
                <div class="t-body" style="display:flex;flex-direction:column;gap:1rem;">
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
                            <span style="font-size:.8rem;color:var(--muted);">Classes assignées</span>
                            <span class="mono"
                                style="font-size:.8rem;font-weight:700;color:var(--teal);">{{ $stats['classes'] }}</span>
                        </div>
                        <div class="prog">
                            <div class="prog-fill"
                                style="width:{{ min($stats['classes'] * 20, 100) }}%;background:var(--teal);"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
                            <span style="font-size:.8rem;color:var(--muted);">Matières enseignées</span>
                            <span class="mono"
                                style="font-size:.8rem;font-weight:700;color:#6366f1;">{{ $stats['subjects'] }}</span>
                        </div>
                        <div class="prog">
                            <div class="prog-fill"
                                style="width:{{ min($stats['subjects'] * 25, 100) }}%;background:#6366f1;"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
                            <span style="font-size:.8rem;color:var(--muted);">Élèves encadrés</span>
                            <span class="mono"
                                style="font-size:.8rem;font-weight:700;color:#f59e0b;">{{ $stats['students'] }}</span>
                        </div>
                        <div class="prog">
                            <div class="prog-fill"
                                style="width:{{ min($stats['students'] / 1.5, 100) }}%;background:#f59e0b;"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
                            <span style="font-size:.8rem;color:var(--muted);">Évaluations créées</span>
                            <span class="mono"
                                style="font-size:.8rem;font-weight:700;color:#10b981;">{{ $evaluations->count() }}</span>
                        </div>
                        <div class="prog">
                            <div class="prog-fill"
                                style="width:{{ min($evaluations->count() * 10, 100) }}%;background:#10b981;"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
                            <span style="font-size:.8rem;color:var(--muted);">Heures estimées / semaine</span>
                            <span class="mono"
                                style="font-size:.8rem;font-weight:700;color:#8b5cf6;">{{ $stats['hours'] }}h</span>
                        </div>
                        <div class="prog">
                            <div class="prog-fill"
                                style="width:{{ min($stats['hours'] * 3, 100) }}%;background:#8b5cf6;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Répartition par type --}}
            <div class="t-card">
                <div class="t-header">
                    <div class="t-title">Évaluations par type</div>
                </div>
                <div class="t-body"><canvas id="typeChart" height="200"></canvas></div>
            </div>
        </div>
    </div>

    {{-- ── ONGLET : MATIÈRES & CLASSES ── --}}
    <div id="ptab-matieres" style="display:none;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;" class="two-col">

            {{-- Matières --}}
            <div class="t-card">
                <div class="t-header">
                    <div class="t-title">Matières enseignées</div><span
                        class="badge b-teal">{{ $subjects->count() }}</span>
                </div>
                @if ($subjects->isEmpty())
                    <div class="empty">
                        <div class="empty-icon">📚</div>
                        <div class="empty-text">Aucune matière assignée</div>
                    </div>
                @else
                    <div class="t-scroll">
                        <table class="t-table">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Classe</th>
                                    <th>Coefficient</th>
                                    <th>Évaluations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjects as $sub)
                                    @php $subEvals = $evaluations->where('subject_id', $sub->id); @endphp
                                    <tr>
                                        <td style="font-weight:600;">{{ $sub->name }}</td>
                                        <td><span class="badge b-teal">{{ $sub->classe->name ?? '—' }}</span></td>
                                        <td class="mono">×{{ $sub->coefficient }}</td>
                                        <td class="mono">{{ $subEvals->count() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                {{-- Classes --}}
                <div class="t-card">
                    <div class="t-header">
                        <div class="t-title">Classes assignées</div><span
                            class="badge b-blue">{{ $classes->count() }}</span>
                    </div>
                    @if ($classes->isEmpty())
                        <div class="empty">
                            <div class="empty-icon">🏫</div>
                            <div class="empty-text">Aucune classe</div>
                        </div>
                    @else
                        <div class="t-scroll">
                            <table class="t-table compact">
                                <thead>
                                    <tr>
                                        <th>Classe</th>
                                        <th>Niveau</th>
                                        <th>Élèves</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classes as $cl)
                                        <tr>
                                            <td style="font-weight:600;">{{ $cl->name }}</td>
                                            <td>{{ $cl->niveau->name ?? '—' }}</td>
                                            <td class="mono">{{ $cl->apprenants->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Niveaux & filières --}}
                <div class="t-card">
                    <div class="t-header">
                        <div class="t-title">Niveaux & Filières</div>
                    </div>
                    <div class="t-body">
                        @if ($niveaux->isNotEmpty())
                            <div style="margin-bottom:.875rem;">
                                <div
                                    style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;">
                                    Niveaux</div>
                                <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                                    @foreach ($niveaux as $niv)
                                        <span class="badge b-blue">{{ $niv->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($filieres->isNotEmpty())
                            <div>
                                <div
                                    style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;">
                                    Filières</div>
                                <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                                    @foreach ($filieres as $fil)
                                        <span class="badge b-purple">{{ $fil->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($niveaux->isEmpty() && $filieres->isEmpty())
                            <p style="font-size:.8rem;color:var(--muted);">Aucun niveau ou filière assigné.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        function switchPTab(id, btn) {
            ['info', 'stats', 'matieres'].forEach(t => {
                document.getElementById('ptab-' + t).style.display = t === id ? '' : 'none';
            });
            document.querySelectorAll('.mini-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (id === 'stats') buildTypeChart();
        }

        let typeChartBuilt = false;

        function buildTypeChart() {
            if (typeChartBuilt) return;
            typeChartBuilt = true;
            const ctx = document.getElementById('typeChart');
            const evals = @json($evaluations->groupBy('type')->map->count());
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(evals),
                    datasets: [{
                        data: Object.values(evals),
                        backgroundColor: ['#fee2e2', '#dbeafe', '#fef3c7', '#ede9fe'],
                        borderColor: ['#ef4444', '#3b82f6', '#f59e0b', '#8b5cf6'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                },
                                boxWidth: 14,
                                color: '#6b7280'
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection
