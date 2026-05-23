<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apprenant;
use App\Models\Institution;
use App\Models\Niveau;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApprenantSeeder extends Seeder
{
    public function run(): void
    {
        // Institution 1 uniquement
        $institution = Institution::find(1);

        if (!$institution) {
            $this->command->error("Institution 1 introuvable.");
            return;
        }

        // Niveau secondaire uniquement
        $niveau = Niveau::where('cycle', 'secondaire')->first();

        if (!$niveau) {
            $this->command->error("Niveau secondaire introuvable.");
            return;
        }

        for ($i = 1; $i <= 200; $i++) {

            $nom = 'Eleve' . $i;
            $prenom = 'Test' . $i;
            $email = "eleve{$i}@example.com";

            // Création User
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "$prenom $nom",
                    'password' => Hash::make('password'),
                ]
            );

            // Création Apprenant
            Apprenant::firstOrCreate(
                ['matricule' => 'ELV-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $user->id,
                    'institution_id' => $institution->id,
                    'niveau_id' => $niveau->id,
                    'filiere_id' => null,
                    'nom' => strtoupper($nom),
                    'prenom' => $prenom,
                    'email' => $email,
                    'date_naissance' => '2008-01-01', // même tranche
                    'lieu_naissance' => 'Brazzaville',
                    'contact' => '06 000 00 ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'adresse' => 'Talangaï',
                    'sexe' => $i % 2 == 0 ? 'M' : 'F',
                    'annee_academique' => '2025-2026',
                    'status' => true,
                ]
            );
        }

        $this->command->info("✅ 200 élèves générés avec succès pour l'institution 1.");
    }
}