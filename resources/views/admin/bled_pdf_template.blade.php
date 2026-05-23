<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta['titre'] }} — {{ $meta['institution'] }} — {{ $meta['annee'] }}</title>
    <style>
        /* ══════════════════════════════════════════════════════════════
           BLED PDF TEMPLATE — Styles écran + impression
        ══════════════════════════════════════════════════════════════ */

        :root {
            --navy:  #0f172a;
            --slate: #1e293b;
            --steel: #334155;
            --muted: #64748b;
            --light: #f1f5f9;
            --white: #ffffff;
            --gold:  #d97706;
            --green: #059669;
            --blue:  #2563eb;
            --red:   #dc2626;
            --border:#e2e8f0;
        }

        * { box-sizing:border-box; margin:0; padding:0; }

        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            font-size: 11pt;
            color: var(--slate);
            background: #f8fafc;
            line-height: 1.45;
        }

        /* ── Page conteneur ── */
        .pdf-page {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,.12);
        }

        /* ═══════════════════════════════════════
           EN-TÊTE DOCUMENT
        ═══════════════════════════════════════ */
        .doc-header {
            background: var(--navy);
            color: #fff;
            padding: 20px 28px 16px;
        }
        .doc-header-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
        }
        .doc-logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .doc-logo {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border-radius: 6px;
            background: rgba(255,255,255,.1);
            padding: 4px;
        }
        .doc-logo-placeholder {
            width: 52px;
            height: 52px;
            border-radius: 6px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .doc-inst-name {
            font-size: 15pt;
            font-weight: 700;
            letter-spacing: -.02em;
        }
        .doc-inst-sub {
            font-size: 8.5pt;
            color: #94a3b8;
            margin-top: 2px;
        }
        .doc-qr-area { text-align: right; }
        .doc-ref {
            font-size: 7.5pt;
            color: #94a3b8;
            font-family: monospace;
        }

        /* Bandeau titre document */
        .doc-title-band {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 6px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .doc-title-main {
            font-size: 13pt;
            font-weight: 700;
            letter-spacing: -.01em;
        }
        .doc-title-annee {
            font-size: 10pt;
            background: var(--gold);
            color: #fff;
            padding: 3px 10px;
            border-radius: 99px;
            font-weight: 700;
        }

        /* ── Infos document ── */
        .doc-meta {
            display: flex;
            gap: 0;
            border-top: 1px solid rgba(255,255,255,.08);
            margin-top: 12px;
        }
        .doc-meta-item {
            flex: 1;
            padding: 8px 14px;
            border-right: 1px solid rgba(255,255,255,.08);
        }
        .doc-meta-item:last-child { border-right: none; }
        .doc-meta-label { font-size: 7pt; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 2px; }
        .doc-meta-val   { font-size: 8.5pt; color: #e2e8f0; font-weight: 500; }

        /* ── Filtres actifs ── */
        .doc-filtres {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 5px;
            padding: 8px 14px;
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }
        .doc-filtres-label { font-size: 7.5pt; color: #94a3b8; margin-right: 4px; }
        .doc-filtre-tag {
            background: rgba(255,255,255,.12);
            border-radius: 99px;
            padding: 2px 8px;
            font-size: 7.5pt;
            color: #e2e8f0;
        }
        .doc-filtre-tag strong { color: #fff; }

        /* ═══════════════════════════════════════
           CORPS DU DOCUMENT
        ═══════════════════════════════════════ */
        .doc-body { padding: 20px 28px; }

        /* ── Bloc statistiques ── */
        .stats-row {
            display: grid;
            gap: 10px;
            margin-bottom: 20px;
        }
        .stats-row.cols-2 { grid-template-columns: repeat(2,1fr); }
        .stats-row.cols-3 { grid-template-columns: repeat(3,1fr); }
        .stats-row.cols-4 { grid-template-columns: repeat(4,1fr); }
        .stat-box {
            background: var(--light);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px 14px;
        }
        .stat-box-label { font-size: 7.5pt; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 3px; }
        .stat-box-val   { font-size: 16pt; font-weight: 700; color: var(--navy); line-height: 1.1; }

        /* ── Tableau principal ── */
        .section-title {
            font-size: 11pt;
            font-weight: 700;
            color: var(--navy);
            padding: 10px 0 8px;
            border-bottom: 2px solid var(--navy);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .section-count {
            font-size: 8pt;
            background: var(--navy);
            color: #fff;
            padding: 2px 8px;
            border-radius: 99px;
            font-weight: 600;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 20px;
        }
        table.data-table thead tr {
            background: var(--navy);
            color: #fff;
        }
        table.data-table thead th {
            padding: 7px 10px;
            font-weight: 600;
            text-align: left;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }
        table.data-table tbody tr:nth-child(even) { background: #f8fafc; }
        table.data-table tbody tr:hover { background: #f1f5f9; }
        table.data-table tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--slate);
            vertical-align: middle;
        }
        table.data-table tbody tr:last-child td { border-bottom: none; }

        /* Badges dans tableau */
        .tbl-badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 99px;
            font-size: 7pt;
            font-weight: 600;
        }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-red   { background: #fee2e2; color: #991b1b; }
        .badge-gold  { background: #fef3c7; color: #92400e; }
        .badge-slate { background: #f1f5f9; color: #334155; }
        .badge-blue  { background: #dbeafe; color: #1e40af; }

        /* Matricule */
        .code { font-family: monospace; font-size: 7.5pt; background: #f8fafc; padding: 1px 4px; border-radius: 3px; }

        /* Valeur importante */
        .val-red  { color: var(--red); font-weight: 700; }
        .val-green{ color: var(--green); font-weight: 600; }

        /* ── Section complet (multi-catégories) ── */
        .section-block {
            margin-bottom: 28px;
            page-break-inside: avoid;
        }
        .section-block-title {
            font-size: 10pt;
            font-weight: 700;
            color: var(--white);
            background: var(--steel);
            padding: 7px 12px;
            border-radius: 5px 5px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .section-block-count { font-size: 7.5pt; background: rgba(255,255,255,.2); padding: 2px 7px; border-radius: 99px; }

        /* ── Détail bulletin apprenant ── */
        .bulletin-card {
            border: 1px solid var(--border);
            border-radius: 6px;
            margin-bottom: 14px;
            overflow: hidden;
        }
        .bulletin-card-hd {
            background: var(--light);
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }
        .bulletin-card-hd h4 { font-size: 9pt; font-weight: 700; }
        .bulletin-moy { font-size: 14pt; font-weight: 700; color: var(--navy); }
        .bulletin-mention { font-size: 8pt; color: var(--muted); }

        /* ── Vide ── */
        .empty-state {
            text-align: center;
            padding: 30px;
            color: var(--muted);
            font-size: 9pt;
            border: 1px dashed var(--border);
            border-radius: 6px;
        }

        /* ── Pied de page ── */
        .doc-footer {
            border-top: 2px solid var(--navy);
            padding: 10px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 7.5pt;
            color: var(--muted);
            background: #fff;
        }
        .doc-footer strong { color: var(--navy); }
        .doc-footer-logo { font-weight: 700; color: var(--navy); font-size: 8pt; }

        /* ── Barre d'impression (visible uniquement à l'écran) ── */
        .print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--navy);
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
            font-size: 13px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .print-bar span { color: #94a3b8; font-size: 12px; }
        .print-btn {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--gold); color: #fff;
            border: none; border-radius: 6px;
            padding: 7px 16px; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none;
        }
        .print-btn:hover { background: #b45309; }
        .close-btn {
            background: rgba(255,255,255,.12); color: #cbd5e1;
            border: none; border-radius: 6px;
            padding: 7px 14px; font-size: 12px;
            cursor: pointer; text-decoration: none;
        }
        body { padding-top: 52px; } /* décalage pour la barre */

        /* ═══════════════════════════════════════
           CSS IMPRESSION (@media print)
        ═══════════════════════════════════════ */
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { background: #fff !important; padding-top: 0; font-size: 9pt; }
            .print-bar { display: none !important; }
            .pdf-page { box-shadow: none; max-width: 100%; }
            .doc-body { padding: 10px 15px; }

            table.data-table { font-size: 7.5pt; }
            table.data-table thead th { padding: 5px 7px; font-size: 6.5pt; }
            table.data-table tbody td { padding: 4px 7px; }

            .section-block { page-break-inside: avoid; }

            /* Saut de page entre sections complet */
            .page-break { page-break-before: always; }

            /* En-tête répétée à chaque page */
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }

            /* Masquer les éléments non-imprimables */
            .no-print { display: none !important; }

            /* Réduire les marges */
            @page {
                margin: 12mm 10mm 15mm 10mm;
                size: A4 portrait;
            }
            @page :first { margin-top: 5mm; }
        }
    </style>
</head>
<body>

    {{-- ── Barre d'impression (masquée à l'impression) ── --}}
    <div class="print-bar no-print">
        <div>
            <strong>{{ $meta['titre'] }}</strong>
            <span> — {{ $meta['institution'] }} — Année {{ $meta['annee'] }}</span>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <button class="print-btn" onclick="window.print()">
                🖨️ Imprimer / Enregistrer en PDF
            </button>
            <button class="close-btn" onclick="window.close()">✕ Fermer</button>
        </div>
    </div>

    <div class="pdf-page">

        {{-- ════════════════════════════════════
             EN-TÊTE
        ════════════════════════════════════ --}}
        <div class="doc-header">
            <div class="doc-header-top">
                <div class="doc-logo-area">
                    @if(!empty($meta['logo']))
                        <img src="{{ Storage::url($meta['logo']) }}" alt="Logo" class="doc-logo">
                    @else
                        <div class="doc-logo-placeholder">🏫</div>
                    @endif
                    <div>
                        <div class="doc-inst-name">{{ $meta['institution'] }}</div>
                        @if(!empty($meta['adresse']))
                            <div class="doc-inst-sub">{{ $meta['adresse'] }}</div>
                        @endif
                        @if(!empty($meta['telephone']) || !empty($meta['email']))
                            <div class="doc-inst-sub">
                                @if(!empty($meta['telephone'])) 📞 {{ $meta['telephone'] }} @endif
                                @if(!empty($meta['email'])) · ✉️ {{ $meta['email'] }} @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="doc-qr-area">
                    <div style="font-size:7pt;color:#64748b;margin-bottom:3px;">BLED — Bureau de Liaison<br>et d'Enregistrement des Données</div>
                    <div class="doc-ref">Réf. BLED-{{ strtoupper(substr($categorie,0,3)) }}-{{ date('Ymd') }}</div>
                </div>
            </div>

            <div class="doc-title-band">
                <div class="doc-title-main">{{ $meta['titre'] }}</div>
                <div class="doc-title-annee">{{ $meta['annee'] }}</div>
            </div>

            <div class="doc-meta">
                <div class="doc-meta-item">
                    <div class="doc-meta-label">Édité le</div>
                    <div class="doc-meta-val">{{ $meta['date_edition'] }}</div>
                </div>
                <div class="doc-meta-item">
                    <div class="doc-meta-label">Édité par</div>
                    <div class="doc-meta-val">{{ $meta['edite_par'] }}</div>
                </div>
                <div class="doc-meta-item">
                    <div class="doc-meta-label">Catégorie</div>
                    <div class="doc-meta-val">{{ ucfirst($categorie) }}</div>
                </div>
                <div class="doc-meta-item">
                    <div class="doc-meta-label">Année académique</div>
                    <div class="doc-meta-val">{{ $meta['annee'] }}</div>
                </div>
            </div>

            @if(!empty($meta['filtres']))
                <div class="doc-filtres">
                    <span class="doc-filtres-label">Filtres :</span>
                    @foreach($meta['filtres'] as $key => $val)
                        <span class="doc-filtre-tag"><strong>{{ $key }}</strong> : {{ $val }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ════════════════════════════════════
             CORPS
        ════════════════════════════════════ --}}
        <div class="doc-body">

            @if($categorie === 'complet' && isset($data['sections']))
                {{-- ══ MODE COMPLET : toutes les sections ══ --}}
                @foreach($data['sections'] as $secKey => $secData)
                    @php
                        $secTitres = [
                            'apprenants'    => '🎓 Apprenants',
                            'enseignants'   => '👨‍🏫 Enseignants',
                            'bulletins'     => '📋 Bulletins',
                            'finances'      => '💰 Finances',
                            'disciplinaire' => '⚠️ Suivi Disciplinaire',
                            'classes'       => '🏫 Classes',
                            'planning'      => '📅 Planning / EDT',
                            'staff'         => '👥 Staff Administratif',
                        ];
                    @endphp

                    @if(!empty($secData['rows']) && (is_countable($secData['rows']) ? count($secData['rows']) : 0) > 0)
                        <div class="section-block {{ !$loop->first ? 'page-break' : '' }}">
                            <div class="section-block-title">
                                {{ $secTitres[$secKey] ?? ucfirst($secKey) }}
                                <span class="section-block-count">{{ is_countable($secData['rows']) ? count($secData['rows']) : 0 }} entrées</span>
                            </div>
                            @include('admin.bled_pdf_table', ['sectionData' => $secData])
                        </div>
                    @endif
                @endforeach

            @else
                {{-- ══ MODE SIMPLE : une catégorie ══ --}}

                {{-- Stats --}}
                @if(!empty($data['stats']))
                    @php $statCount = count($data['stats']); @endphp
                    <div class="stats-row {{ $statCount <= 2 ? 'cols-2' : ($statCount === 3 ? 'cols-3' : 'cols-4') }}">
                        @foreach($data['stats'] as $label => $val)
                            <div class="stat-box">
                                <div class="stat-box-label">{{ $label }}</div>
                                <div class="stat-box-val">{{ $val }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Titre section --}}
                <div class="section-title">
                    <span>Données — {{ $meta['titre'] }}</span>
                    @if(!empty($data['total']))
                        <span class="section-count">{{ $data['total'] }} enregistrement(s)</span>
                    @endif
                </div>

                {{-- Tableau principal --}}
                @if(!empty($data['rows']) && (is_countable($data['rows']) ? count($data['rows']) : 0) > 0)
                    @include('admin.bled_pdf_table', ['sectionData' => $data])
                @else
                    <div class="empty-state">
                        <div style="font-size:24px;margin-bottom:8px;">📭</div>
                        Aucune donnée à afficher pour les filtres sélectionnés.
                    </div>
                @endif

                {{-- Détail bulletin apprenant individuel --}}
                @if($categorie === 'bulletins' && !empty($data['detail_par_apprenant']))
                    <div class="section-title" style="margin-top:20px;">
                        <span>Détail par période</span>
                    </div>
                    @foreach($data['detail_par_apprenant'] as $detail)
                        <div class="bulletin-card">
                            <div class="bulletin-card-hd">
                                <h4>{{ $detail['periode'] ?? '—' }}</h4>
                                <div style="text-align:right;">
                                    <div class="bulletin-moy">{{ $detail['moyenne'] ?? '—' }}/20</div>
                                    <div class="bulletin-mention">
                                        Rang {{ $detail['rang'] ?? '—' }}/{{ $detail['effectif'] ?? '—' }} —
                                        {{ $detail['mention'] ?? '—' }} —
                                        {{ $detail['admis'] ? '✓ Admis' : '✗ Non admis' }}
                                    </div>
                                </div>
                            </div>
                            @if(!empty($detail['notes']) && $detail['notes']->count())
                                <table class="data-table" style="margin-bottom:0;">
                                    <thead>
                                        <tr>
                                            <th>Matière</th>
                                            <th>Coefficient</th>
                                            <th>Note</th>
                                            <th>Note / Coef</th>
                                            <th>Appréciation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detail['notes'] as $note)
                                            <tr>
                                                <td>{{ optional($note->subject)->name ?? '—' }}</td>
                                                <td>{{ optional($note->subject)->coefficient ?? '—' }}</td>
                                                <td style="font-weight:700;">{{ $note->score ?? '—' }}/20</td>
                                                <td>{{ isset($note->score, $note->subject->coefficient) ? number_format($note->score * $note->subject->coefficient, 2) : '—' }}</td>
                                                <td>{{ $note->appreciation ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div style="padding:10px 14px;font-size:8pt;color:#64748b;">Notes non disponibles pour cette période.</div>
                            @endif
                        </div>
                    @endforeach
                @endif

            @endif

        </div>

        {{-- ════════════════════════════════════
             PIED DE PAGE
        ════════════════════════════════════ --}}
        <div class="doc-footer">
            <div>
                <strong>{{ $meta['institution'] }}</strong> — BLED Archive —
                Année académique <strong>{{ $meta['annee'] }}</strong>
            </div>
            <div style="text-align:center;color:#94a3b8;">
                Document généré le {{ $meta['date_edition'] }} par {{ $meta['edite_par'] }}
            </div>
            <div class="doc-footer-logo">BLED™</div>
        </div>

    </div>

    <script>
        // Auto-print si paramètre dans l'URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            window.onload = () => setTimeout(() => window.print(), 500);
        }
    </script>
</body>
</html>
