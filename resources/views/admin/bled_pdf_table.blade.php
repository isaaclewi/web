{{--
    BLED PDF — Composant tableau réutilisable
    resources/views/admin/bled_pdf_table.blade.php
    Usage : @include('admin.bled_pdf_table', ['sectionData' => $data])
--}}

@php
    $rows     = $sectionData['rows'] ?? collect();
    $colonnes = $sectionData['colonnes'] ?? [];
    $mapper   = $sectionData['mapper'] ?? null;

    // Badges spéciaux selon la valeur
    function pdfBadgeClass(string $val): string {
        $v = mb_strtolower(trim($val));
        if (in_array($v, ['actif', 'admis', 'oui', '✓ admis', '✓ payé', 'payé'])) return 'badge-green';
        if (in_array($v, ['inactif', 'non admis', '✗ non admis', '✗ impayé', 'impayé'])) return 'badge-red';
        if (in_array($v, ['partiel', '△ partiel', 'masculin', 'cdi'])) return 'badge-gold';
        if (str_contains($v, 'cdd') || str_contains($v, 'vacataire') || str_contains($v, 'bénévole')) return 'badge-slate';
        return '';
    }

    // Colonnes qui reçoivent un badge (index 0-based)
    $badgeCols = []; // sera rempli dynamiquement selon le contexte
    $rightCols = []; // colonnes à aligner à droite (montants, %)
    $codeCols  = []; // colonnes en monospace (matricule)

    // Détection automatique des colonnes spéciales
    foreach ($colonnes as $idx => $colName) {
        $lower = mb_strtolower($colName);
        if (str_contains($lower, 'statut') || str_contains($lower, 'admis') || str_contains($lower, 'publié') || str_contains($lower, 'notifi')) {
            $badgeCols[] = $idx;
        }
        if (str_contains($lower, 'fcfa') || str_contains($lower, 'montant') || str_contains($lower, 'dû') || str_contains($lower, 'payé') || str_contains($lower, 'reste') || str_contains($lower, 'moy')) {
            $rightCols[] = $idx;
        }
        if (str_contains($lower, 'matricule') || str_contains($lower, 'code') || str_contains($lower, 'réf')) {
            $codeCols[] = $idx;
        }
    }
@endphp

@if(is_countable($rows) && count($rows) > 0 && $mapper)
<table class="data-table">
    <thead>
        <tr>
            <th style="width:28px;">#</th>
            @foreach($colonnes as $col)
                <th>{{ $col }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $row)
            @php $cells = $mapper($row); @endphp
            <tr>
                <td style="color:#94a3b8;font-size:7pt;text-align:right;">{{ $index + 1 }}</td>
                @foreach($cells as $colIdx => $cell)
                    @php
                        $isRight  = in_array($colIdx, $rightCols);
                        $isCode   = in_array($colIdx, $codeCols);
                        $isBadge  = in_array($colIdx, $badgeCols);
                        $badgeClass = $isBadge ? pdfBadgeClass((string)$cell) : '';

                        // Détection valeur négative / reste impayé pour coloriser
                        $isNegative = $isRight && is_numeric(str_replace([' ', ','], ['', '.'], $cell)) && str_replace([' ', ','], ['', '.'], $cell) > 0
                            && in_array(mb_strtolower($colonnes[$colIdx] ?? ''), ['reste (fcfa)', 'reste']);

                        // Montants à zéro en vert, > 0 en rouge (pour "reste")
                        $colName = mb_strtolower($colonnes[$colIdx] ?? '');
                        $isReste = str_contains($colName, 'reste');
                    @endphp
                    <td
                        style="{{ $isRight ? 'text-align:right;' : '' }}{{ $isNegative || $isReste ? ($cell !== '0' && $cell !== '0 ' && $cell > 0 ? 'color:#dc2626;font-weight:600;' : '') : '' }}"
                    >
                        @if($isCode)
                            <span class="code">{{ $cell }}</span>
                        @elseif($isBadge && $badgeClass)
                            <span class="tbl-badge {{ $badgeClass }}">{{ $cell }}</span>
                        @else
                            {{ $cell }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#f1f5f9;">
            <td colspan="{{ count($colonnes) + 1 }}" style="padding:6px 10px;font-size:7.5pt;color:#64748b;text-align:right;">
                Total : <strong>{{ is_countable($rows) ? count($rows) : '—' }}</strong> enregistrement(s)
            </td>
        </tr>
    </tfoot>
</table>
@else
    <div class="empty-state">
        <div style="font-size:22px;margin-bottom:6px;">📭</div>
        Aucune donnée disponible pour cette section avec les filtres appliqués.
    </div>
@endif
