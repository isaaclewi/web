@extends('staff.master')

@section('title', 'Dossier disciplinaire')

@section('page-title', 'Dossier apprenant')
@section('page-sub', $apprenant->prenom.' '.$apprenant->nom)

@section('content')

<div class="s-card">
<div class="s-card-body">

<h3>Statistiques</h3>
<ul>
<li>Total : {{ $statsApprenant['total'] }}</li>
<li>Graves : {{ $statsApprenant['graves'] }}</li>
<li>Ouverts : {{ $statsApprenant['ouverts'] }}</li>
<li>Notifiés : {{ $statsApprenant['notifies'] }}</li>
</ul>

</div>
</div>

<div class="s-card" style="margin-top:1rem">
<div class="s-card-body">

<table style="width:100%">
<thead>
<tr>
<th>Date</th>
<th>Type</th>
<th>Gravité</th>
<th>Sanction</th>
<th>Statut</th>
</tr>
</thead>

<tbody>
@foreach($incidents as $i)
<tr>
<td>{{ $i->date_incident->format('d/m/Y') }}</td>
<td>{{ $typeLabels[$i->type] }}</td>
<td>{{ $graviteLabels[$i->gravite] }}</td>
<td>{{ $sanctionLabels[$i->sanction] }}</td>
<td>{{ $i->statut }}</td>
</tr>
@endforeach
</tbody>
</table>

</div>
</div>

@endsection