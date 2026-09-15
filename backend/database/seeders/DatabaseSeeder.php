<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            LanguageSeeder::class,
            BranchSeeder::class,
            AuthorSeeder::class,
            SubjectSeeder::class,
            WorkSeeder::class,
            ExpressionSeeder::class,
            ManifestationSeeder::class,
            ItemSeeder::class,
            UserSeeder::class,
        ]);
    }
}
