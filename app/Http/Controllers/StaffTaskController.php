<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffTaskAssignment;
use App\Models\StaffTaskModule;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffTaskController extends Controller
{
    private function getInstitution(): Institution
    {
        $institution = Auth::user()?->institution;
        if (! $institution) abort(403, 'Aucun établissement lié à votre compte.');
        return $institution;
    }

    /**
     * Page de gestion des tâches du staff — vue admin
     */
    public function index(Request $request)
    {
        $institution = $this->getInstitution();
        $instId      = $institution->id;

        $staffMembers = Staff::where('institution_id', $instId)
            ->with(['user.roles', 'administrativeUnit',
                    'taskAssignments' => fn($q) => $q->with('module')->where('institution_id', $instId)])
            ->orderBy('nom')
            ->get();

        $modules = StaffTaskModule::where('actif', true)->orderBy('ordre')->get();

        // Filtres
        $roleFilter = $request->get('role', '');
        if ($roleFilter) {
            $staffMembers = $staffMembers->filter(function ($s) use ($roleFilter) {
                return $s->user?->roles->pluck('name')->contains($roleFilter) ||
                       $s->poste === $roleFilter;
            });
        }

        $stats = [
            'total_staff'   => $staffMembers->count(),
            'avec_taches'   => $staffMembers->filter(fn($s) => $s->taskAssignments->where('actif', true)->count() > 0)->count(),
            'sans_taches'   => $staffMembers->filter(fn($s) => $s->taskAssignments->where('actif', true)->count() === 0)->count(),
            'total_modules' => $modules->count(),
        ];

        return view('admin.staff_tasks', compact(
            'institution', 'staffMembers', 'modules', 'stats', 'roleFilter'
        ));
    }

    /**
     * Fiche d'un membre du staff avec ses tâches
     */
    public function show(Staff $staff)
    {
        $institution = $this->getInstitution();
        if ((int) $staff->institution_id !== $institution->id) abort(403);

        $staff->load(['user.roles', 'administrativeUnit',
                      'taskAssignments.module', 'taskAssignments.assigne']);

        $role = $staff->user?->roles->pluck('name')->first() ?? 'staff';
        $modulesDisponibles = StaffTaskModule::where('actif', true)->get();
        $modulesAssignesIds = $staff->taskAssignments->pluck('module_id')->toArray();

        return view('admin.staff_task_detail', compact(
            'institution', 'staff', 'modulesDisponibles', 'modulesAssignesIds'
        ));
    }

    /**
     * Assigner un module à un membre du staff
     */
    public function assign(Request $request, Staff $staff)
    {
        $institution = $this->getInstitution();
        if ((int) $staff->institution_id !== $institution->id) abort(403);

        $data = $request->validate([
            'module_id' => 'required|exists:staff_task_modules,id',
            'notes'     => 'nullable|string|max:500',
        ]);

        $module = StaffTaskModule::findOrFail($data['module_id']);

        // Vérifier compatibilité rôle
        $role = $staff->user?->roles->pluck('name')->first() ?? '';
        if (! empty($module->roles_autorises) && ! in_array($role, $module->roles_autorises)) {
            // On laisse passer quand même — le directeur a la main
        }

        StaffTaskAssignment::updateOrCreate(
            ['staff_id' => $staff->id, 'module_id' => $data['module_id']],
            [
                'institution_id' => $institution->id,
                'actif'          => true,
                'notes'          => $data['notes'] ?? null,
                'assigne_par'    => Auth::id(),
                'assigne_at'     => now(),
                'desactive_at'   => null,
            ]
        );

        return redirect()->back()->with('success',
            "Module « {$module->label} » assigné à {$staff->prenom} {$staff->nom}."
        );
    }

    /**
     * Activer / désactiver une assignation
     */
    public function toggle(Request $request, StaffTaskAssignment $assignment)
    {
        $institution = $this->getInstitution();
        if ((int) $assignment->institution_id !== $institution->id) abort(403);

        $assignment->update([
            'actif'        => ! $assignment->actif,
            'desactive_at' => $assignment->actif ? now() : null,
        ]);

        $etat = $assignment->actif ? 'activée' : 'désactivée';
        return redirect()->back()->with('success', "Tâche {$etat}.");
    }

    /**
     * Retirer complètement un module d'un membre
     */
    public function remove(StaffTaskAssignment $assignment)
    {
        $institution = $this->getInstitution();
        if ((int) $assignment->institution_id !== $institution->id) abort(403);

        $label = $assignment->module?->label ?? 'Tâche';
        $assignment->delete();

        return redirect()->back()->with('success', "« {$label} » retiré.");
    }

    /**
     * Mettre à jour les notes/instructions d'une assignation
     */
    public function updateNotes(Request $request, StaffTaskAssignment $assignment)
    {
        $institution = $this->getInstitution();
        if ((int) $assignment->institution_id !== $institution->id) abort(403);

        $data = $request->validate(['notes' => 'nullable|string|max:500']);
        $assignment->update($data);

        return redirect()->back()->with('success', 'Instructions mises à jour.');
    }

    /**
     * Assignation en masse — attribuer un ensemble de modules à un staff
     */
    public function bulkAssign(Request $request, Staff $staff)
    {
        $institution = $this->getInstitution();
        if ((int) $staff->institution_id !== $institution->id) abort(403);

        $data = $request->validate([
            'modules'   => 'nullable|array',
            'modules.*' => 'exists:staff_task_modules,id',
        ]);

        $moduleIds = $data['modules'] ?? [];

        // Désactiver les modules non cochés
        StaffTaskAssignment::where('staff_id', $staff->id)
            ->where('institution_id', $institution->id)
            ->whereNotIn('module_id', $moduleIds)
            ->update(['actif' => false, 'desactive_at' => now()]);

        // Activer / créer les modules cochés
        foreach ($moduleIds as $moduleId) {
            StaffTaskAssignment::updateOrCreate(
                ['staff_id' => $staff->id, 'module_id' => $moduleId],
                [
                    'institution_id' => $institution->id,
                    'actif'          => true,
                    'assigne_par'    => Auth::id(),
                    'assigne_at'     => now(),
                    'desactive_at'   => null,
                ]
            );
        }

        return redirect()->back()->with('success',
            "Tâches de {$staff->prenom} {$staff->nom} mises à jour (".count($moduleIds)." module(s) actif(s))."
        );
    }
}