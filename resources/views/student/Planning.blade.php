@extends('student.master')

@section('title', 'Mon Planning')

@section('page-title', 'Emploi du temps')
@section('page-sub', $classe->name . ' • ' . $annee)

@section('content')

{{-- ── EMPLOI DU TEMPS ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">📅 Emploi du temps</div>
            <div class="card-sub">Organisation hebdomadaire</div>
        </div>
    </div>

    <div class="card-body" style="overflow-x:auto;">
        <table class="t-table">
            <thead>
                <tr>
                    <th>Jour</th>
                    <th>Heure</th>
                    <th>Matière</th>
                    <th>Enseignant</th>
                    <th>Salle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grille as $jour => $cours)
                    @forelse($cours as $c)
                        <tr>
                            <td>{{ ucfirst($jour) }}</td>
                            <td>{{ $c->heure_debut }} - {{ $c->heure_fin }}</td>
                            <td>
                                {{ $c->subject->name ?? '—' }}
                                <span class="badge b-indigo">{{ $c->type }}</span>
                            </td>
                            <td>{{ $c->teacher->nom ?? '—' }}</td>
                            <td>{{ $c->salle ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td>{{ ucfirst($jour) }}</td>
                            <td colspan="4">Aucun cours</td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            <div class="empty-text">Aucun emploi du temps disponible</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── PROGRAMMES DE PAIEMENT ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">💰 Échéances de paiement</div>
            <div class="card-sub">Frais scolaires</div>
        </div>
    </div>

    <div class="card-body">
        <table class="t-table">
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Montant</th>
                    <th>Échéance</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programmes as $p)
                    <tr>
                        <td>{{ $p->libelle }}</td>
                        <td>{{ number_format($p->montant, 0, ',', ' ') }} {{ $p->devise }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->date_echeance)->format('d M Y') }}</td>
                        <td>
                            <span class="badge b-amber">
                                {{ ucfirst($p->type_frais) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">
                            <div class="empty-text">Aucune échéance</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── SÉANCES DE LA SEMAINE ── --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">🧑‍🏫 Séances de la semaine</div>
            <div class="card-sub">Cours planifiés et réalisés</div>
        </div>
    </div>

    <div class="card-body">
        <table class="t-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Matière</th>
                    <th>Enseignant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($seances as $s)
                    <tr>
                        <td>{{ $s->date_seance->format('d M Y') }}</td>
                        <td>{{ $s->heure_debut }} - {{ $s->heure_fin }}</td>
                        <td>{{ $s->subject->name ?? '—' }}</td>
                        <td>{{ $s->teacher->nom ?? '—' }}</td>
                        <td>
                            <span class="badge b-indigo">
                                {{ $s->statut_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">
                            <div class="empty-text">Aucune séance cette semaine</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection