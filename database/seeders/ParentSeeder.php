<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SchoolParent as ParentModel;
use Illuminate\Support\Facades\Hash;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {

            // Création du compte utilisateur
            $user = User::create([
                'name' => "Parent $i",
                'email' => "parent$i@example.com",
                'password' => Hash::make('password123'),
                'institution_id' => 1 // adapte si nécessaire

            ]);

            // Création du profil Parent
            ParentModel::create([
                'user_id' => $user->id,
                'nom' => "Nom$i",
                'prenom' => "Prenom$i",
                'sexe' => $i % 2 === 0 ? 'F' : 'M',
                'telephone' => '06 00 00 00 0' . $i,
                'email' => "parent$i@example.com",
                'matricule' => "MAT000$i",
                'profession' => 'Commerçant',
                'adresse' => 'Avenue de la Paix',
                'ville' => 'Brazzaville',
                'pays' => 'Congo',
                'code_postal' => '0000',
                'photo' => null,
                'cin' => 'CIN000' . $i,
                'nationalite' => 'Congolaise',
                'nombre_enfants' => rand(1, 4),
                'status' => true,
            ]);
        }
    }
}