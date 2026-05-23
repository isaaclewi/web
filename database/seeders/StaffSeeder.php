<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\User;
use App\Models\Institution;
use App\Models\AdministrativeUnit;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $institution = Institution::first();
        if (!$institution) {
            $this->command->error('Aucune institution trouvée.');
            return;
        }

        $units = AdministrativeUnit::where('institution_id', $institution->id)
            ->get()
            ->keyBy('type');

        $staffs = [
            [
                'poste' => 'Directeur',
                'unit_type' => 'direction',
                'matricule' => 'ADM-001',
                'nom' => 'NGOMA',
                'prenom' => 'Paul',
                'telephone' => '+242060111111',
                'email' => 'directeur@syntriforg.edu',
            ],
            [
                'poste' => 'Secrétaire',
                'unit_type' => 'scolarite',
                'matricule' => 'ADM-002',
                'nom' => 'MABIALA',
                'prenom' => 'Anne',
                'telephone' => '+242060222222',
                'email' => 'scolarite@syntriforg.edu',
            ],
            [
                'poste' => 'Comptable',
                'unit_type' => 'finances',
                'matricule' => 'ADM-003',
                'nom' => 'KIMIA',
                'prenom' => 'Jean',
                'telephone' => '+242060333333',
                'email' => 'finance@syntriforg.edu',
            ],
            [
                'poste' => 'Censeur',
                'unit_type' => 'discipline',
                'matricule' => 'ADM-004',
                'nom' => 'OKOMBI',
                'prenom' => 'Lucie',
                'telephone' => '+242060444444',
                'email' => 'discipline@syntriforg.edu',
            ],
        ];

        foreach ($staffs as $data) {

            if (!isset($units[$data['unit_type']])) continue;

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ User
            |--------------------------------------------------------------------------
            */
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nom'] . ' ' . $data['prenom'],
                    'password' => Hash::make('password123'),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ Staff
            |--------------------------------------------------------------------------
            */
            Staff::firstOrCreate(
                ['matricule' => $data['matricule']],
                [
                    'user_id' => $user->id,
                    'institution_id' => $institution->id,
                    'administrative_unit_id' => $units[$data['unit_type']]->id,

                    'poste' => $data['poste'],
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'telephone' => $data['telephone'],
                    'email' => $data['email'],
                    'adresse' => 'Brazzaville',
                    'date_naissance' => now()->subYears(rand(30, 55)),
                    'lieu_naissance' => 'Congo',
                    'genre' => rand(0, 1) ? 'M' : 'F',

                    'photo' => null,
                    'cv' => 'cv_' . Str::slug($data['nom']) . '.pdf',
                    'diplome' => 'Licence / Master',
                    'experience' => rand(5, 20) . ' ans d’expérience',
                    'competences' => 'Gestion administrative, Organisation',
                    'langues' => 'Français',
                    'certifications' => 'Certificat administratif',
                    'references' => 'Référence interne',

                    'notes' => null,
                    'date_embauche' => now()->subYears(rand(1, 10)),
                    'date_depart' => null,
                    'motivation' => 'Contribuer au développement de l’institution',
                    'hobbies' => 'Lecture, Sport',
                    'projets' => 'Modernisation administrative',
                    'realisations' => 'Digitalisation dossiers',
                    'publications' => null,
                    'distinctions' => null,

                    'status' => true,
                ]
            );
        }
    }
}