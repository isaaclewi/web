@extends('staff.master')

@section('title', 'Rapports')

@section('content')

{{-- ═════════ HEADER SECTION ═════════ --}}
<div class="s-card" style="margin-bottom:1.5rem">
    <div class="s-card-hd">
        <h3>Rapports institutionnels</h3>
        <span class="bdg bdg-b">Année : {{ $annee }}</span>
    </div>
    <div class="s-card-body">
        <p style="color:var(--mist);font-size:.85rem;line-height:1.6">
            Vue globale des performances académiques, financières et administratives de l’institution.
        </p>
    </div>
</div>

{{-- ═════════ STATS GÉNÉRALES ═════════ --}}
<div class="stat-grid" style="margin-bottom:1.5rem">

    <div class="stat-card">
        <div class="stat-val">{{ $totalApprenants }}</div>
        <div class="stat-lbl">Apprenants</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">{{ $actifsApprenants }}</div>
        <div class="stat-lbl">Actifs</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">{{ $garcons }} / {{ $filles }}</div>
        <div class="stat-lbl">Garçons / Filles</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">{{ $tauxAffectation }}%</div>
        <div class="stat-lbl">Affectation classes</div>
    </div>

</div>

{{-- ═════════ RÉPARTITION APPRENANTS ═════════ --}}
<div class="s-card" style="margin-bottom:1.5rem">
    <div class="s-card-hd">
        <h3>Répartition des apprenants</h3>
    </div>

    <div class="s-card-body">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

            {{-- Par classe --}}
            <div>
                <div class="s-section-title">Par classe</div>
                <table class="s-tbl">
                    <thead>
                        <tr>
                            <th>Classe</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apprenantsByClasse as $c)
                        <tr>
                            <td>{{ $c->classe }}</td>
                            <td><span class="bdg bdg-b">{{ $c->total }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Par niveau --}}
            <div>
                <div class="s-section-title">Par niveau</div>
                <table class="s-tbl">
                    <thead>
                        <tr>
                            <th>Niveau</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apprenantsByNiveau as $n)
                        <tr>
                            <td>{{ $n->niveau }}</td>
                            <td><span class="bdg bdg-g">{{ $n->total }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

{{-- ═════════ ENSEIGNANTS ═════════ --}}
<div class="stat-grid" style="margin-bottom:1.5rem">

    <div class="stat-card">
        <div class="stat-val">{{ $totalTeachers }}</div>
        <div class="stat-lbl">Enseignants</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">{{ $actifsTeachers }}</div>
        <div class="stat-lbl">Actifs</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">
          {{ $teachersHommes }} / {{ $teachersFemmes }}
        </div>
        <div class="stat-lbl">Hommes / Femmes</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">{{ $teachersByContrat->count() }}</div>
        <div class="stat-lbl">Types contrats</div>
    </div>

</div>

{{-- ═════════ FINANCES ═════════ --}}
<div class="s-card" style="margin-bottom:1.5rem">
    <div class="s-card-hd">
        <h3>Situation financière</h3>
    </div>

    <div class="s-card-body">

        <div class="stat-grid" style="margin-bottom:1.5rem">

            <div class="stat-card">
                <div class="stat-val">{{ number_format($finStats->total_du ?? 0) }}</div>
                <div class="stat-lbl">Total dû</div>
            </div>

            <div class="stat-card">
                <div class="stat-val">{{ number_format($finStats->total_paye ?? 0) }}</div>
                <div class="stat-lbl">Total payé</div>
            </div>

            <div class="stat-card">
                <div class="stat-val">{{ number_format($finStats->total_reste ?? 0) }}</div>
                <div class="stat-lbl">Reste</div>
            </div>

            <div class="stat-card">
                <div class="stat-val">{{ $finStats->nb_impayes ?? 0 }}</div>
                <div class="stat-lbl">Impayés</div>
            </div>

        </div>

        <div>
            <div class="s-section-title">Top débiteurs</div>

            <table class="s-tbl">
                <thead>
                    <tr>
                        <th>Apprenant</th>
                        <th>Classe</th>
                        <th>Dette</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topDebiteurs as $d)
                    <tr>
                        <td>{{ $d->nom }} {{ $d->prenom }}</td>
                        <td>{{ $d->classe?->name ?? '—' }}</td>
                        <td><span class="bdg bdg-r">{{ number_format($d->total_reste) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

    </div>
</div>

{{-- ═════════ STAFF + PARENTS ═════════ --}}
<div class="stat-grid">

    <div class="stat-card">
        <div class="stat-val">{{ $totalStaff }}</div>
        <div class="stat-lbl">Staff total</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">{{ $actifsStaff }}</div>
        <div class="stat-lbl">Staff actifs</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">{{ $totalParents }}</div>
        <div class="stat-lbl">Parents</div>
    </div>

    <div class="stat-card">
        <div class="stat-val">{{ $tauxCouvertureParents }}%</div>
        <div class="stat-lbl">Couverture parents</div>
    </div>

</div>

@endsection
