<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Création du User
        $user = User::firstOrCreate(
            ['email' => 'admin@syntriforg.edu'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'), // mot de passe
            ]
        );

        // Création du SuperAdmin lié
        SuperAdmin::firstOrCreate(
            ['email' => 'admin@syntriforg.edu'],
            [
                'user_id' => $user->id,
                'name' => 'Super Admin',
                'password' => $user->password, // même hash que le User
                'matricule' => '000000',
            ]
        );
    }
}