@extends('staff.master')

@section('title', 'Académique')
@section('page-title', 'Gestion académique')
@section('page-sub', 'Classes · Niveaux · Filières · Matières')

@section('content')

{{-- ===== STATS ===== --}}
<div class="stat-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-val">{{ $stats['classes'] }}</div>
        <div class="stat-lbl">Classes</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['sections'] }}</div>
        <div class="stat-lbl">Niveaux</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['filieres'] }}</div>
        <div class="stat-lbl">Filières</div>
    </div>
    <div class="stat-card">
        <div class="stat-val">{{ $stats['matieres'] }}</div>
        <div class="stat-lbl">Matières</div>
    </div>
</div>

{{-- ===== CLASSES ===== --}}
<div class="s-card" id="classes">
    <div class="s-card-hd">
        <h3>Classes</h3>
    </div>

    <div class="s-card-body">

        {{-- Form --}}
        <form method="POST" action="{{ route('staff.classes.store') }}" style="margin-bottom:1rem">
            @csrf
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem">
                <input class="inp" name="name" placeholder="Nom classe">
                <input class="inp" name="code" placeholder="Code">

                <select name="niveau_id" class="inp">
                    <option value="">Niveau</option>
                    @foreach($sections as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>

                <select name="filiere_id" class="inp">
                    <option value="">Filière</option>
                    @foreach($filieres as $f)
                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-gold" style="margin-top:1rem">Créer</button>
        </form>

        {{-- Table --}}
        <table class="s-tbl">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Niveau</th>
                    <th>Filière</th>
                    <th>Élèves</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($classes as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->niveau->name ?? '-' }}</td>
                    <td>{{ $c->filiere->name ?? '-' }}</td>
                    <td>{{ $c->apprenants_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('staff.classes.destroy', $c) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-err btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

{{-- ===== NIVEAUX ===== --}}
<div class="s-card" id="niveaux" style="margin-top:1.5rem">
    <div class="s-card-hd">
        <h3>Niveaux</h3>
    </div>

    <div class="s-card-body">
        <form method="POST" action="{{ route('staff.niveaux.store') }}">
            @csrf
            <div style="display:flex;gap:1rem">
                <input class="inp" name="name" placeholder="Nom niveau">
                <select class="inp" name="cycle">
                    <option value="">Cycle</option>
                    <option value="primaire">Primaire</option>
                    <option value="secondaire">Secondaire</option>
                </select>
                <button class="btn btn-gold">Créer</button>
            </div>
        </form>

        <table class="s-tbl" style="margin-top:1rem">
            @foreach($sections as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td>{{ $s->cycle }}</td>
                <td>
                    <form method="POST" action="{{ route('staff.niveaux.destroy',$s) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-err btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

{{-- ===== FILIÈRES ===== --}}
<div class="s-card" id="filieres" style="margin-top:1.5rem">
    <div class="s-card-hd">
        <h3>Filières</h3>
    </div>

    <div class="s-card-body">
        <form method="POST" action="{{ route('staff.filieres.store') }}">
            @csrf
            <div style="display:flex;gap:1rem">
                <input class="inp" name="name" placeholder="Nom filière">
                <button class="btn btn-gold">Créer</button>
            </div>
        </form>

        <table class="s-tbl" style="margin-top:1rem">
            @foreach($filieres as $f)
            <tr>
                <td>{{ $f->name }}</td>
                <td>
                    <form method="POST" action="{{ route('staff.filieres.destroy',$f) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-err btn-sm">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

@endsection