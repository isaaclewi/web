<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $institution = Institution::first();

        if (!$institution) {
            $this->command->error('Aucune institution trouvée.');
            return;
        }

        $specialites = ['Mathématiques', 'Informatique', 'Droit', 'Physique', 'Anglais'];
        $contrats = ['fonctionnaire', 'vacataire', 'prive'];

        for ($i = 1; $i <= 5; $i++) {

            $email = "teacher$i@example.com";

            // Création du compte utilisateur
            $user = User::create([
                'name' => "Teacher $i",
                'email' => $email,
                'password' => Hash::make('password'),
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'institution_id' => $institution->id,

                // Identité
                'matricule' => 'ENS' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nom' => "Nom$i",
                'prenom' => "Prenom$i",
                'sexe' => $i % 2 == 0 ? 'F' : 'M',

                // Contact
                'telephone' => '06' . rand(10000000, 99999999),
                'email' => $email,

                // Professionnel
                'specialite' => $specialites[array_rand($specialites)],
                'type_contrat' => $contrats[array_rand($contrats)],
                'date_recrutement' => now()->subYears(rand(1, 10)),
                'date_depart' => null,

                // Documents & Infos
                'photo' => null,
                'cv' => 'cv_teacher_' . $i . '.pdf',
                'diplome' => 'Master en ' . $specialites[array_rand($specialites)],
                'experience' => rand(2, 15) . ' ans d\'expérience',
                'competences' => 'Pédagogie, Gestion de classe',
                'langues' => 'Français, Anglais',
                'certifications' => 'Certification pédagogique',
                'publications' => null,
                'projets' => 'Projet éducatif ' . Str::random(5),
                'reseaux_sociaux' => 'linkedin.com/in/teacher' . $i,
                'autres_infos' => null,
                'notes' => null,
                'documents' => null,
                'observations' => null,
                'recommandations' => null,
                'avis' => null,
                'evaluation' => null,

                // Compétences détaillées
                'competences_pedagogiques' => 'Gestion de classe, Méthodologie active',
                'competences_techniques' => 'Outils numériques, LMS',
                'competences_relationnelles' => 'Communication, Leadership',
                'competences_organisationnelles' => 'Planification académique',
                'competences_gestion' => 'Gestion d’équipe',

                'status' => true,
            ]);
        }
    }
}