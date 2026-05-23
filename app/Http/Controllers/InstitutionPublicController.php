<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Classe;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Apprenant;
use App\Models\Teacher;
use Illuminate\Http\Request;

class InstitutionPublicController extends Controller
{
    /**
     * Page publique listant toutes les institutions actives
     */
    public function index(Request $request)
    {
        $query = Institution::where('status', 1)
            ->withCount([
                'apprenants',
                'niveaux',
                'filieres',
            ]);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($s) use ($q) {
                $s->where('name', 'like', "%{$q}%")
                  ->orWhere('pays', 'like', "%{$q}%")
                  ->orWhere('commune', 'like', "%{$q}%")
                  ->orWhere('type', 'like', "%{$q}%");
            });
        }

        $institutions = $query->orderBy('name')->get();

        return view('institutions', compact('institutions'));
    }

    /**
     * Données JSON d'une institution pour le pop-up
     */
    public function show(Institution $institution)
    {
        if (! $institution->status) {
            abort(404);
        }

        $institution->load(['niveaux', 'filieres']);

        $classes = Classe::where('institution_id', $institution->id)
            ->with(['niveau:id,name', 'filiere:id,name'])
            ->withCount('apprenants')
            ->orderBy('name')
            ->get();

        $stats = [
            'apprenants' => Apprenant::where('institution_id', $institution->id)->count(),
            'teachers'   => Teacher::where('institution_id', $institution->id)->count(),
            'classes'    => $classes->count(),
            'niveaux'    => $institution->niveaux->count(),
            'filieres'   => $institution->filieres->count(),
        ];

        return response()->json([
            'institution' => $institution,
            'classes'     => $classes,
            'stats'       => $stats,
        ]);
    }
}
