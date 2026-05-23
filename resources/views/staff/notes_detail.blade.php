@extends('staff.master')

@section('title', 'Détail du bulletin')

@section('page-title', 'Détail du bulletin')
@section('page-sub', 'Informations complètes')

@section('content')

    <div class="s-card" style="margin-bottom:1.25rem">
        <div class="s-card-hd">
            <h3>Informations générales</h3>
            <a href="{{ route('staff.bulletins') }}" class="btn btn-ot btn-sm">← Retour</a>
        </div>

        <div class="s-card-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem">
            <div>
                <strong>Apprenant</strong><br>
                {{ $bulletin->apprenant?->prenom }} {{ $bulletin->apprenant?->nom }}
            </div>

            <div>
                <strong>Classe</strong><br>
                {{ $bulletin->classe?->name ?? '—' }}
            </div>

            <div>
                <strong>Période</strong><br>
                {{ $bulletin->periode }}
            </div>

            <div>
                <strong>Moyenne</strong><br>
                {{ number_format($bulletin->moyenne_generale, $config->decimales) }} / {{ $config->note_max }}
            </div>

            <div>
                <strong>Rang</strong><br>
                {{ $bulletin->rang ?? '—' }}
            </div>

            <div>
                <strong>Mention</strong><br>
                {{ $bulletin->mention ?? '—' }}
            </div>
        </div>
    </div>


    {{-- DÉTAIL MATIÈRES --}}
    <div class="s-card" style="margin-bottom:1.25rem">
        <div class="s-card-hd">
            <h3>Détail complet des matières</h3>
        </div>

        <div class="s-card-body">

            @forelse($matieres as $m)
                <div style="border:1px solid var(--brd);border-radius:10px;margin-bottom:1rem;overflow:hidden">

                    {{-- HEADER MATIÈRE --}}
                    <div
                        style="padding:.75rem 1rem;background:var(--bg);display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <strong>{{ $m['matiere'] }}</strong>
                            <div style="font-size:.7rem;color:var(--mist)">
                                👨‍🏫 {{ $m['teacher'] }} · Coef {{ $m['coef'] }}
                            </div>
                        </div>

                        <div style="font-weight:700">
                            {{ $m['moyenne'] ?? '—' }}/20
                        </div>
                    </div>

                    {{-- TABLE NOTES --}}
                    <table class="s-tbl">
                        <thead>
                            <tr>
                                <th>Évaluation</th>
                                <th>Type</th>
                                <th>Enseignant</th> {{-- 🔥 AJOUT --}}
                                <th>Note</th>
                                <th>/20</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            @foreach ($m['notes'] as $n)
                                <tr>
                                    <td>{{ $n['evaluation'] }}</td>
                                    <td><span class="bdg bdg-b">{{ $n['type'] }}</span></td>
                                    <td>
                                        {{ $m['teacher'] }}
                                    </td>
                                    <td>{{ $n['score'] }} / {{ $n['max'] }}</td>
                                    <td>
                                        {{ round(($n['score'] / $n['max']) * 20, 1) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            @empty
                <div class="s-empty">
                    <h4>Aucune note trouvée</h4>
                </div>
            @endforelse

        </div>
    </div>


    {{-- APPRÉCIATIONS --}}
    <div class="s-card">
        <div class="s-card-hd">
            <h3>Appréciations</h3>
        </div>

        <div class="s-card-body">
            <form method="POST" action="{{ route('staff.bulletins.appreciation.update', $bulletin) }}">
                @csrf

                <div class="fg-group" style="margin-bottom:1rem">
                    <label class="lbl">Conseil de classe</label>
                    <textarea class="inp" name="appreciation_conseil" rows="3">{{ $bulletin->appreciation_conseil }}</textarea>
                </div>

                <div class="fg-group" style="margin-bottom:1rem">
                    <label class="lbl">Directeur</label>
                    <textarea class="inp" name="appreciation_directeur" rows="3">{{ $bulletin->appreciation_directeur }}</textarea>
                </div>

                <button class="btn btn-gold">💾 Enregistrer</button>
            </form>
        </div>
    </div>

@endsection
