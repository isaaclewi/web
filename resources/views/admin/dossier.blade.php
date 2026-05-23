@extends('admin.master')

@section('title', 'Dossier — ' . $apprenant->prenom . ' ' . $apprenant->nom)

@push('styles')
<style>
    :root { --ink:#111827; --ink-mid:#374151; --muted:#6b7280; --border:#e5e7eb; --radius:.75rem; --accent:#1f2937; }

    /* ── Layout ── */
    .dos-layout { display:grid; grid-template-columns:280px 1fr; gap:1.25rem; align-items:start; }
    @media(max-width:960px) { .dos-layout { grid-template-columns:1fr; } }

    /* ── Cards ── */
    .dos-card { background:white; border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; margin-bottom:1.25rem; }
    .dos-card-head { padding:.875rem 1.375rem; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:.5rem; }
    .dos-card-icon { width:30px; height:30px; border-radius:.375rem; display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }
    .dos-card-title { font-size:.875rem; font-weight:700; color:var(--ink); }
    .dos-card-body  { padding:1.125rem 1.375rem; }

    /* ── Identité ── */
    .id-avatar { width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,#1f2937,#374151); display:flex; align-items:center; justify-content:center; font-size:1.6rem; color:white; font-weight:700; flex-shrink:0; }
    .id-name  { font-size:1rem; font-weight:700; color:var(--ink); }
    .id-meta  { font-size:.75rem; color:var(--muted); margin-top:.2rem; }
    .id-badge { display:inline-flex; align-items:center; gap:.3rem; background:#dbeafe; color:#1e40af; font-size:.68rem; font-weight:700; padding:.2rem .6rem; border-radius:99px; margin-top:.375rem; }

    /* Info grid */
    .info-grid { display:grid; grid-template-columns:1fr 1fr; }
    .info-item { padding:.75rem 1.375rem; border-bottom:1px solid #f9fafb; }
    .info-item:nth-child(odd) { border-right:1px solid #f9fafb; }
    .info-item.full { grid-column:span 2; border-right:none; }
    .info-key { font-size:.62rem; text-transform:uppercase; font-weight:700; color:#9ca3af; letter-spacing:.06em; }
    .info-val { font-size:.8125rem; font-weight:500; color:var(--ink); margin-top:.15rem; }

    /* ── Notes table ── */
    .t-sm { width:100%; border-collapse:collapse; font-size:.8rem; }
    .t-sm th { background:#f9fafb; padding:.55rem .875rem; text-align:left; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); border-bottom:1px solid var(--border); }
    .t-sm td { padding:.65rem .875rem; border-bottom:1px solid #f9fafb; color:var(--ink-mid); }
    .t-sm tr:last-child td { border-bottom:none; }

    /* Score badge */
    .score { display:inline-flex; align-items:center; justify-content:center; min-width:38px; height:24px; padding:0 6px; border-radius:.375rem; font-size:.78rem; font-weight:700; font-family:monospace; }
    .score-A { background:#d1fae5; color:#065f46; }
    .score-B { background:#dbeafe; color:#1e40af; }
    .score-C { background:#fef3c7; color:#92400e; }
    .score-D { background:#fee2e2; color:#991b1b; }

    /* ── Discipline ── */
    .incident-row { display:flex; gap:.875rem; padding:.875rem 0; border-bottom:1px solid #f9fafb; align-items:flex-start; }
    .incident-row:last-child { border-bottom:none; padding-bottom:0; }
    .gravite-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; margin-top:.3rem; }
    .gravite-1 { background:#f59e0b; }
    .gravite-2 { background:#f97316; }
    .gravite-3 { background:#ef4444; }
    .incident-type { font-size:.8rem; font-weight:600; color:var(--ink); }
    .incident-meta { font-size:.7rem; color:var(--muted); margin-top:.2rem; }
    .badge-sanction { background:#fef2f2; color:#991b1b; font-size:.65rem; padding:.15rem .45rem; border-radius:4px; display:inline-block; margin-top:.25rem; }

    /* ── Finances ── */
    .fin-summary { display:grid; grid-template-columns:repeat(3,1fr); gap:.625rem; margin-bottom:1rem; }
    .fin-chip { background:#f9fafb; border:1px solid var(--border); border-radius:.5rem; padding:.75rem; text-align:center; }
    .fin-val  { font-size:1.1rem; font-weight:700; }
    .fin-lbl  { font-size:.63rem; color:var(--muted); margin-top:.1rem; text-transform:uppercase; letter-spacing:.04em; }

    /* ── Timeline classes ── */
    .timeline-item { display:flex; gap:.75rem; padding:.75rem 0; border-bottom:1px solid #f9fafb; align-items:flex-start; }
    .timeline-item:last-child { border-bottom:none; }
    .timeline-dot { width:32px; height:32px; border-radius:50%; background:#f3f4f6; border:2px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:.8rem; flex-shrink:0; margin-top:.1rem; }
    .timeline-dot.active { background:#1f2937; border-color:#1f2937; color:white; }
    .timeline-info-label { font-size:.8rem; font-weight:600; color:var(--ink); }
    .timeline-info-meta  { font-size:.7rem; color:var(--muted); margin-top:.1rem; }

    /* ── Sidebar identité ── */
    .sidebar-id-row { display:flex; align-items:flex-start; gap:.625rem; padding:.65rem 0; border-bottom:1px solid #f9fafb; }
    .sidebar-id-row:last-child { border-bottom:none; padding-bottom:0; }
    .sidebar-id-icon { width:26px; height:26px; border-radius:.375rem; background:#f3f4f6; display:flex; align-items:center; justify-content:center; font-size:.75rem; flex-shrink:0; margin-top:.05rem; }
    .sidebar-id-key  { font-size:.62rem; text-transform:uppercase; font-weight:700; color:#9ca3af; letter-spacing:.05em; }
    .sidebar-id-val  { font-size:.8rem; font-weight:500; color:var(--ink); margin-top:.05rem; }

    /* ── Alerts ── */
    .alert-warn { background:#fffbeb; border:1px solid #fde68a; color:#92400e; border-radius:.5rem; padding:.875rem 1rem; font-size:.8rem; margin-bottom:1rem; }
    .alert-info { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; border-radius:.5rem; padding:.875rem 1rem; font-size:.8rem; margin-bottom:1rem; }

    /* ── Boutons ── */
    .btn-back { display:inline-flex; align-items:center; gap:.375rem; padding:.45rem .875rem; background:#f3f4f6; border:1px solid var(--border); border-radius:.5rem; color:var(--muted); font-size:.8rem; font-weight:600; text-decoration:none; transition:all .15s; }
    .btn-back:hover { background:#e5e7eb; color:var(--ink); }

    /* ── Print ── */
    @media print {
        .btn-back, .no-print { display:none !important; }
        body { background:white !important; }
        .dos-layout { grid-template-columns:1fr !important; }
    }
</style>
@endpush

@section('content')

{{-- En-tête --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <a href="{{ route('admin.transfer.index') }}" class="btn-back">
            ← Retour
        </a>
        <div>
            <h1 style="font-size:1.125rem;font-weight:700;color:#111827;">
                📂 Dossier de {{ $apprenant->prenom }} {{ $apprenant->nom }}
            </h1>
            <p style="font-size:.72rem;color:#6b7280;margin-top:.1rem;">
                Demande #{{ $transfer->id }} · Consulté le {{ now()->format('d/m/Y à H:i') }}
            </p>
        </div>
    </div>
    <div style="display:flex;gap:.5rem;" class="no-print">
        <button onclick="window.print()" class="btn-back">
            🖨 Imprimer
        </button>
    </div>
</div>

{{-- Alerte expiration --}}
@if($transfer->token_expires_at)
    <div class="alert-warn">
        ⏳ Ce dossier était accessible jusqu'au {{ $transfer->token_expires_at->format('d/m/Y à H:i') }}.
        Certaines données peuvent avoir évolué depuis la demande initiale.
    </div>
@endif

<div class="dos-layout">

    {{-- ════════════════════════════════════
         COLONNE GAUCHE — Identité + méta
    ════════════════════════════════════ --}}
    <div>

        {{-- Carte identité principale --}}
        <div class="dos-card">
            <div class="dos-card-body">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                    <div class="id-avatar">
                        {{ strtoupper(substr($apprenant->prenom, 0, 1)) }}{{ strtoupper(substr($apprenant->nom, 0, 1)) }}
                    </div>
                    <div>
                        <div class="id-name">{{ $apprenant->prenom }} {{ $apprenant->nom }}</div>
                        <div class="id-meta">{{ $apprenant->sexe === 'M' ? '👨 Masculin' : ($apprenant->sexe === 'F' ? '👩 Féminin' : '—') }}</div>
                        <div>
                            <span class="id-badge">📋 {{ $apprenant->matricule }}</span>
                        </div>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:0;">
                    <div class="sidebar-id-row">
                        <div class="sidebar-id-icon">🏫</div>
                        <div>
                            <div class="sidebar-id-key">Établissement d'origine</div>
                            <div class="sidebar-id-val">{{ $apprenant->institution?->name ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="sidebar-id-row">
                        <div class="sidebar-id-icon">📚</div>
                        <div>
                            <div class="sidebar-id-key">Dernière classe</div>
                            <div class="sidebar-id-val">{{ $apprenant->classe?->name ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="sidebar-id-row">
                        <div class="sidebar-id-icon">🎓</div>
                        <div>
                            <div class="sidebar-id-key">Niveau</div>
                            <div class="sidebar-id-val">{{ $apprenant->niveau?->name ?? '—' }}</div>
                        </div>
                    </div>
                    @if($apprenant->filiere)
                    <div class="sidebar-id-row">
                        <div class="sidebar-id-icon">🔖</div>
                        <div>
                            <div class="sidebar-id-key">Filière</div>
                            <div class="sidebar-id-val">{{ $apprenant->filiere->name }}</div>
                        </div>
                    </div>
                    @endif
                    <div class="sidebar-id-row">
                        <div class="sidebar-id-icon">📅</div>
                        <div>
                            <div class="sidebar-id-key">Date de naissance</div>
                            <div class="sidebar-id-val">{{ $apprenant->date_naissance ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="sidebar-id-row">
                        <div class="sidebar-id-icon">📆</div>
                        <div>
                            <div class="sidebar-id-key">Année académique</div>
                            <div class="sidebar-id-val">{{ $apprenant->annee_academique ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carte infos demande --}}
        <div class="dos-card">
            <div class="dos-card-head">
                <div class="dos-card-icon" style="background:#eff6ff;">📋</div>
                <span class="dos-card-title">Infos de la demande</span>
            </div>
            <div class="dos-card-body" style="font-size:.78rem;">
                <div class="sidebar-id-row">
                    <div class="sidebar-id-icon">🏢</div>
                    <div>
                        <div class="sidebar-id-key">École source</div>
                        <div class="sidebar-id-val">{{ $transfer->institutionSource?->name }}</div>
                    </div>
                </div>
                <div class="sidebar-id-row">
                    <div class="sidebar-id-icon">📅</div>
                    <div>
                        <div class="sidebar-id-key">Date de la demande</div>
                        <div class="sidebar-id-val">{{ $transfer->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
                <div class="sidebar-id-row">
                    <div class="sidebar-id-icon">💬</div>
                    <div>
                        <div class="sidebar-id-key">Motif</div>
                        <div class="sidebar-id-val">{{ $transfer->motif }}</div>
                    </div>
                </div>
                <div style="margin-top:.75rem;">
                    <div class="sidebar-id-key" style="margin-bottom:.375rem;">Données consultées</div>
                    <div style="display:flex;flex-wrap:wrap;gap:.25rem;">
                        @foreach($scope as $s)
                            <span style="background:#f3f4f6;color:#374151;font-size:.65rem;padding:.2rem .5rem;border-radius:4px;">{{ $s }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Résumé rapide si notes --}}
        @if(isset($dossier['moyenne_generale']))
        <div class="dos-card">
            <div class="dos-card-head">
                <div class="dos-card-icon" style="background:#d1fae5;">📊</div>
                <span class="dos-card-title">Résumé académique</span>
            </div>
            <div class="dos-card-body" style="text-align:center;">
                <div style="font-size:2.5rem;font-weight:700;color:#111827;font-family:monospace;">
                    {{ $dossier['moyenne_generale'] ?? '—' }}<span style="font-size:1rem;color:#9ca3af;">/20</span>
                </div>
                <div style="font-size:.72rem;color:#6b7280;margin-top:.25rem;">Moyenne générale pondérée</div>
                @if(isset($dossier['stats_discipline']))
                <div style="margin-top:.875rem;padding-top:.875rem;border-top:1px solid #f3f4f6;display:grid;grid-template-columns:1fr 1fr;gap:.5rem;text-align:center;">
                    <div>
                        <div style="font-size:1.25rem;font-weight:700;color:{{ $dossier['stats_discipline']['total'] > 0 ? '#f59e0b' : '#10b981' }};">
                            {{ $dossier['stats_discipline']['total'] }}
                        </div>
                        <div style="font-size:.65rem;color:#9ca3af;">Incidents</div>
                    </div>
                    <div>
                        <div style="font-size:1.25rem;font-weight:700;color:{{ $dossier['stats_discipline']['graves'] > 0 ? '#ef4444' : '#10b981' }};">
                            {{ $dossier['stats_discipline']['graves'] }}
                        </div>
                        <div style="font-size:.65rem;color:#9ca3af;">Graves</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- fin sidebar --}}

    {{-- ════════════════════════════════════
         COLONNE DROITE — Données
    ════════════════════════════════════ --}}
    <div>

        {{-- ── NOTES & MOYENNES ── --}}
        @if(in_array('notes', $scope) && isset($dossier['moyennes']) && $dossier['moyennes']->isNotEmpty())
        <div class="dos-card">
            <div class="dos-card-head">
                <div class="dos-card-icon" style="background:#eff6ff;">📊</div>
                <span class="dos-card-title">Moyennes par matière</span>
                <span style="font-size:.7rem;color:#9ca3af;margin-left:auto;">{{ $dossier['moyennes']->count() }} matière(s)</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="t-sm">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Coeff.</th>
                            <th>Moyenne</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Nb notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dossier['moyennes'] as $m)
                        @php
                            $avg = $m['moyenne'];
                            $cls = $avg === null ? '' : ($avg >= 14 ? 'score-A' : ($avg >= 10 ? 'score-B' : ($avg >= 8 ? 'score-C' : 'score-D')));
                        @endphp
                        <tr>
                            <td style="font-weight:600;">{{ $m['matiere'] }}</td>
                            <td>{{ $m['coefficient'] }}</td>
                            <td>
                                @if($avg !== null)
                                    <span class="score {{ $cls }}">{{ $avg }}</span>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td style="color:#9ca3af;">{{ $m['min'] ?? '—' }}</td>
                            <td style="color:#9ca3af;">{{ $m['max'] ?? '—' }}</td>
                            <td>{{ $m['nb_notes'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f9fafb;">
                            <td colspan="2" style="font-weight:700;color:#111827;padding:.75rem .875rem;">Moyenne générale</td>
                            <td colspan="4">
                                @if($dossier['moyenne_generale'] !== null)
                                    @php $mg = $dossier['moyenne_generale']; @endphp
                                    <span class="score {{ $mg >= 14 ? 'score-A' : ($mg >= 10 ? 'score-B' : ($mg >= 8 ? 'score-C' : 'score-D')) }}" style="font-size:.9rem;padding:.25rem .75rem;">
                                        {{ $mg }} / 20
                                    </span>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- ── DOSSIER DISCIPLINAIRE ── --}}
        @if(in_array('discipline', $scope) && isset($dossier['incidents']))
        <div class="dos-card">
            <div class="dos-card-head">
                <div class="dos-card-icon" style="background:#fef2f2;">⚠️</div>
                <span class="dos-card-title">Dossier disciplinaire</span>
                <span style="font-size:.7rem;margin-left:auto;">
                    <span style="background:{{ $dossier['stats_discipline']['total'] > 0 ? '#fef3c7' : '#d1fae5' }};color:{{ $dossier['stats_discipline']['total'] > 0 ? '#92400e' : '#065f46' }};padding:.2rem .6rem;border-radius:99px;font-weight:700;">
                        {{ $dossier['stats_discipline']['total'] }} incident(s)
                    </span>
                </span>
            </div>
            <div class="dos-card-body">
                @if($dossier['incidents']->isEmpty())
                    <div style="text-align:center;padding:1.5rem;color:#6b7280;font-size:.8rem;">
                        ✅ Aucun incident disciplinaire enregistré.
                    </div>
                @else
                    @foreach($dossier['incidents'] as $inc)
                    <div class="incident-row">
                        <div class="gravite-dot gravite-{{ $inc->gravite }}"></div>
                        <div style="flex:1;min-width:0;">
                            <div class="incident-type">
                                {{ \App\Models\SuiviDisciplinaire::typeLabels()[$inc->type] ?? $inc->type }}
                            </div>
                            <div class="incident-meta">
                                📅 {{ $inc->date_incident?->format('d/m/Y') }}
                                · {{ \App\Models\SuiviDisciplinaire::graviteLabels()[$inc->gravite] ?? '—' }}
                                @if($inc->recordedBy)
                                    · par {{ $inc->recordedBy->name }}
                                @endif
                            </div>
                            @if($inc->description)
                                <div style="font-size:.75rem;color:#374151;margin-top:.25rem;background:#f9fafb;padding:.375rem .5rem;border-radius:.25rem;">
                                    {{ Str::limit($inc->description, 120) }}
                                </div>
                            @endif
                            @if($inc->sanction && $inc->sanction !== 'aucune')
                                <span class="badge-sanction">
                                    🔒 {{ \App\Models\SuiviDisciplinaire::sanctionLabels()[$inc->sanction] ?? $inc->sanction }}
                                </span>
                            @endif
                        </div>
                        <div style="font-size:.65rem;color:#9ca3af;flex-shrink:0;">
                            {{ $inc->annee_academique ?? $inc->annee_civile ?? '' }}
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        {{-- ── FINANCES ── --}}
        @if(in_array('finances', $scope) && isset($dossier['finances']))
        <div class="dos-card">
            <div class="dos-card-head">
                <div class="dos-card-icon" style="background:#f0fdf4;">💰</div>
                <span class="dos-card-title">Situation financière</span>
            </div>
            <div class="dos-card-body">
                <div class="fin-summary">
                    <div class="fin-chip">
                        <div class="fin-val" style="color:#111827;">{{ number_format($dossier['finances_totaux']['total_du'], 0, ',', ' ') }}</div>
                        <div class="fin-lbl">Total dû</div>
                    </div>
                    <div class="fin-chip">
                        <div class="fin-val" style="color:#10b981;">{{ number_format($dossier['finances_totaux']['total_paye'], 0, ',', ' ') }}</div>
                        <div class="fin-lbl">Total payé</div>
                    </div>
                    <div class="fin-chip">
                        <div class="fin-val" style="color:{{ $dossier['finances_totaux']['total_reste'] > 0 ? '#ef4444' : '#10b981' }};">
                            {{ number_format($dossier['finances_totaux']['total_reste'], 0, ',', ' ') }}
                        </div>
                        <div class="fin-lbl">Reste dû</div>
                    </div>
                </div>
                @if($dossier['finances']->isNotEmpty())
                    <div style="overflow-x:auto;">
                        <table class="t-sm">
                            <thead><tr>
                                <th>Année</th><th>Mois</th><th>Dû</th><th>Payé</th><th>Reste</th><th>Statut</th>
                            </tr></thead>
                            <tbody>
                                @foreach($dossier['finances'] as $f)
                                <tr>
                                    <td>{{ $f->annee_academique }}</td>
                                    <td>{{ $f->mois_label }}</td>
                                    <td>{{ number_format($f->montant_du, 0) }}</td>
                                    <td style="color:#10b981;">{{ number_format($f->montant_paye, 0) }}</td>
                                    <td style="color:{{ $f->montant_reste > 0 ? '#ef4444' : '#10b981' }};">{{ number_format($f->montant_reste, 0) }}</td>
                                    <td>
                                        <span style="background:{{ $f->statut === 'paye' ? '#d1fae5' : ($f->statut === 'partiel' ? '#fef3c7' : '#fee2e2') }};color:{{ $f->statut === 'paye' ? '#065f46' : ($f->statut === 'partiel' ? '#92400e' : '#991b1b') }};font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:99px;">
                                            {{ ucfirst($f->statut) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="font-size:.8rem;color:#6b7280;text-align:center;padding:1rem;">Aucun enregistrement financier.</p>
                @endif
            </div>
        </div>
        @endif

        {{-- ── HISTORIQUE CLASSES ── --}}
        @if(in_array('classes', $scope) && isset($dossier['historique_classes']))
        <div class="dos-card">
            <div class="dos-card-head">
                <div class="dos-card-icon" style="background:#fef3c7;">📚</div>
                <span class="dos-card-title">Historique des classes</span>
            </div>
            <div class="dos-card-body">
                @if($dossier['historique_classes']->isEmpty())
                    <p style="font-size:.8rem;color:#6b7280;text-align:center;padding:1rem;">Aucun historique disponible.</p>
                @else
                    @foreach($dossier['historique_classes'] as $h)
                    <div class="timeline-item">
                        <div class="timeline-dot {{ $h->statut === 'actif' ? 'active' : '' }}">
                            {{ $h->statut === 'actif' ? '✓' : '📖' }}
                        </div>
                        <div>
                            <div class="timeline-info-label">{{ $h->classe }}</div>
                            <div class="timeline-info-meta">
                                {{ $h->niveau ?? '' }}{{ $h->filiere ? ' · '.$h->filiere : '' }}
                                · {{ $h->annee_academique }}
                            </div>
                            @if($h->date_inscription)
                            <div class="timeline-info-meta" style="margin-top:.1rem;">
                                Inscription : {{ \Carbon\Carbon::parse($h->date_inscription)->format('d/m/Y') }}
                            </div>
                            @endif
                            <span style="background:{{ $h->statut === 'actif' ? '#d1fae5' : '#f3f4f6' }};color:{{ $h->statut === 'actif' ? '#065f46' : '#374151' }};font-size:.65rem;padding:.15rem .45rem;border-radius:4px;margin-top:.25rem;display:inline-block;">
                                {{ ucfirst($h->statut) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        {{-- ── BULLETINS ── --}}
        @if(in_array('bulletins', $scope) && isset($dossier['bulletins']))
        <div class="dos-card">
            <div class="dos-card-head">
                <div class="dos-card-icon" style="background:#f0f9ff;">📄</div>
                <span class="dos-card-title">Bulletins scolaires</span>
                <span style="font-size:.7rem;color:#9ca3af;margin-left:auto;">{{ $dossier['bulletins']->count() }} bulletin(s)</span>
            </div>
            <div class="dos-card-body">
                @if($dossier['bulletins']->isEmpty())
                    <p style="font-size:.8rem;color:#6b7280;text-align:center;padding:1rem;">Aucun bulletin publié.</p>
                @else
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem;">
                        @foreach($dossier['bulletins'] as $rc)
                        <div style="background:#f9fafb;border:1px solid var(--border);border-radius:.625rem;padding:1rem;">
                            <div style="font-size:.78rem;font-weight:700;color:#111827;">{{ $rc->period ?? 'Bulletin '.$rc->id }}</div>
                            <div style="font-size:.7rem;color:#6b7280;margin-top:.2rem;">{{ $rc->classe?->name ?? '' }}</div>
                            @if($rc->average)
                            <div style="font-size:1.25rem;font-weight:700;color:#1f2937;margin-top:.5rem;font-family:monospace;">
                                {{ $rc->average }}<span style="font-size:.75rem;color:#9ca3af;">/20</span>
                            </div>
                            @endif
                            @if($rc->rank)
                            <div style="font-size:.68rem;color:#9ca3af;">Rang : {{ $rc->rank }}</div>
                            @endif
                            <div style="font-size:.65rem;color:#9ca3af;margin-top:.375rem;">{{ $rc->created_at?->format('d/m/Y') }}</div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- fin colonne droite --}}
</div>

@endsection
