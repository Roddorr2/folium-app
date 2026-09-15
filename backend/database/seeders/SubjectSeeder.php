<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        Subject::firstOrCreate(['id' => 1], ['name' => 'Fantasía']);
        Subject::firstOrCreate(['id' => 2], ['name' => 'Literatura Latinoamericana']);
        Subject::firstOrCreate(['id' => 3], ['name' => 'Ciencia Ficción / Distopía']);
        Subject::firstOrCreate(['id' => 4], ['name' => 'Botánica Neotropical']);
        Subject::firstOrCreate(['id' => 5], ['name' => 'Realismo Mágico']);
        Subject::firstOrCreate(['id' => 6], ['name' => 'Ensayos e Iconografía']);
        Subject::firstOrCreate(['id' => 7], ['name' => 'Flora Andina']);
        Subject::firstOrCreate(['id' => 8], ['name' => 'Literatura Clásica']);
        Subject::firstOrCreate(['id' => 9], ['name' => 'Biología Evolutiva']);
        Subject::firstOrCreate(['id' => 10], ['name' => 'Historia de la Ciencia']);
        Subject::firstOrCreate(['id' => 11], ['name' => 'Historiografía y Sociedad']);
        Subject::firstOrCreate(['id' => 12], ['name' => 'Pensamiento Crítico']);
    }
}
