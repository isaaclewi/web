<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InstitutionSeeder::class,
            AdministrativeUnitSeeder::class,
            NiveauSeeder::class,
            FiliereSeeder::class,
            ApprenantSeeder::class,
            StaffSeeder::class,
            TeacherSeeder::class,
            SuperAdminSeeder::class,
            ParentSeeder::class,
            StaffTaskModuleSeeder::class,
        ]);
    }
}
