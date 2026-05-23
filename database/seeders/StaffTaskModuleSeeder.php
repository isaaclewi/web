<?php

namespace Database\Seeders;

use App\Models\StaffTaskModule;
use Illuminate\Database\Seeder;

class StaffTaskModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [

            [
                'key' => 'apprenants',
                'label' => 'Gestion des apprenants',
                'description' => 'Consulter, inscrire et mettre à jour les fiches des élèves.',
                'icone' => 'users',
                'roles_autorises' => ['surveillant', 'directeur', 'admin'],
                'route_prefix' => 'staff.apprenants',
                'ordre' => 1,
            ],

            [
                'key' => 'parents',
                'label' => 'Gestion des parents d\'élèves',
                'description' => 'Gérer les contacts et communications avec les parents.',
                'icone' => 'clock',
                'roles_autorises' => ['surveillant', 'directeur', 'admin'],
                'route_prefix' => 'staff.parents',
                'ordre' => 2,
            ],

            [
                'key' => 'paiements',
                'label' => 'Gestion des paiements',
                'description' => 'Saisir les paiements de scolarité et générer les reçus.',
                'icone' => 'money',
                'roles_autorises' => ['comptable', 'directeur', 'admin'],
                'route_prefix' => 'staff.paiements',
                'ordre' => 3,
            ],

            [
                'key' => 'inscriptions',
                'label' => 'Inscriptions',
                'description' => 'Traiter les dossiers d\'inscription.',
                'icone' => 'pencil',
                'roles_autorises' => ['admin', 'secretaire'],
                'route_prefix' => 'staff.inscriptions',
                'ordre' => 4,
            ],

            [
                'key' => 'planning',
                'label' => 'Planning & emplois du temps',
                'description' => 'Gérer les emplois du temps.',
                'icone' => 'calendar',
                'roles_autorises' => ['surveillant', 'directeur', 'admin'],
                'route_prefix' => 'staff.planning',
                'ordre' => 5,
            ],

            [
                'key' => 'disciplinaire',
                'label' => 'Discipline',
                'description' => 'Suivi des incidents disciplinaires.',
                'icone' => 'shield',
                'roles_autorises' => ['surveillant', 'directeur', 'admin'],
                'route_prefix' => 'staff.disciplinaire',
                'ordre' => 6,
            ],

            [
                'key' => 'rapports',
                'label' => 'Rapports & statistiques',
                'description' => 'Tableaux de bord et rapports.',
                'icone' => 'chart',
                'roles_autorises' => ['directeur', 'admin'],
                'route_prefix' => 'staff.rapports',
                'ordre' => 7,
            ],

            [
                'key' => 'bibliotheque',
                'label' => 'Bibliothèque',
                'description' => 'Gestion du fonds documentaire.',
                'icone' => 'book',
                'roles_autorises' => ['surveillant', 'directeur', 'admin'],
                'route_prefix' => 'staff.bibliotheque',
                'ordre' => 8,
            ],

            [
                'key' => 'enseignants',
                'label' => 'Gestion des enseignants',
                'description' => 'Gestion des enseignants.',
                'icone' => 'users',
                'roles_autorises' => ['directeur', 'admin'],
                'route_prefix' => 'staff.enseignants',
                'ordre' => 9,
            ],

            [
                'key' => 'notes',
                'label' => 'Notes et bulletins',
                'description' => 'Gestion des notes et bulletins.',
                'icone' => 'file',
                'roles_autorises' => ['directeur', 'admin', 'secretaire'],
                'route_prefix' => 'staff.notes',
                'ordre' => 10,
            ],

            [
                'key' => 'transferts',
                'label' => 'Transferts interécoles',
                'description' => 'Gestion des transferts.',
                'icone' => 'exchange',
                'roles_autorises' => ['directeur', 'admin'],
                'route_prefix' => 'staff.transferts',
                'ordre' => 11,
            ],

                [
                    'key' => 'academic',
                    'label' => 'Gestion académique',
                    'description' => 'Gérer les classes, matières et programmes.',
                    'icone' => 'graduation',
                    'roles_autorises' => ['directeur', 'admin'],
                    'route_prefix' => 'staff.academic',
                    'ordre' => 12,
                ],
        ];

        foreach ($modules as $m) {
            StaffTaskModule::updateOrCreate(
                ['key' => $m['key']],
                $m
            );
        }
    }
}