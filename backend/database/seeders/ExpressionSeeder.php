<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expression;

class ExpressionSeeder extends Seeder
{
    public function run(): void
    {
        // 101. Cien Años de Soledad
        Expression::firstOrCreate(['id' => 1], ['work_id' => 101, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 1967, 'description' => 'Edición conmemorativa RAE y Asociación de Academias.']);

        // 102. Rayuela
        Expression::firstOrCreate(['id' => 2], ['work_id' => 102, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 1963, 'description' => 'Edición crítica en castellano con esquema de lectura alternativo.']);

        // 103. Ficciones
        Expression::firstOrCreate(['id' => 3], ['work_id' => 103, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 1944, 'description' => 'Texto revisado por el autor para Editorial Sur.']);

        // 104. La Casa de los Espíritus
        Expression::firstOrCreate(['id' => 4], ['work_id' => 104, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 1982, 'description' => 'Edición original castellana.']);

        // 105. 1984
        Expression::firstOrCreate(['id' => 5], ['work_id' => 105, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 2013, 'description' => 'Traducción al español por Miguel Martínez-Lage.']);
        Expression::firstOrCreate(['id' => 6], ['work_id' => 105, 'language_id' => 2, 'type' => 'Original Text', 'revision_year' => 1949, 'description' => 'Original English edition text.']);

        // 106. Don Quijote de la Mancha
        Expression::firstOrCreate(['id' => 7], ['work_id' => 106, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 1605, 'description' => 'Edición anotada por Francisco Rico para el Instituto Cervantes.']);

        // 107. El Señor de los Anillos
        Expression::firstOrCreate(['id' => 8], ['work_id' => 107, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 2002, 'description' => 'Traducción oficial al español por Luis Domènech.']);
        Expression::firstOrCreate(['id' => 9], ['work_id' => 107, 'language_id' => 2, 'type' => 'Original Text', 'revision_year' => 1954, 'description' => 'Original English edition text.']);

        // 108. La Ciudad y los Perros
        Expression::firstOrCreate(['id' => 10], ['work_id' => 108, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 1963, 'description' => 'Texto ganador del Premio Biblioteca Breve.']);

        // 109. Flora Huayaquilensis
        Expression::firstOrCreate(['id' => 11], ['work_id' => 109, 'language_id' => 1, 'type' => 'Facsímil Botánico', 'revision_year' => 1989, 'description' => 'Edición crítica en español de las láminas neotropicales.']);

        // 110. El Origen de las Especies
        Expression::firstOrCreate(['id' => 12], ['work_id' => 110, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 2009, 'description' => 'Traducción anotada conmemorativa del bicentenario.']);

        // 111. Cosmos
        Expression::firstOrCreate(['id' => 13], ['work_id' => 111, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 1980, 'description' => 'Edición ilustrada de divulgación.']);

        // 112. Sapiens: De animales a dioses
        Expression::firstOrCreate(['id' => 14], ['work_id' => 112, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 2014, 'description' => 'Traducción castellana de Joandomènec Ros.']);

        // 113. El Laberinto de la Soledad
        Expression::firstOrCreate(['id' => 15], ['work_id' => 113, 'language_id' => 1, 'type' => 'Texto Impreso', 'revision_year' => 1950, 'description' => 'Edición revisada y ampliada por el autor en 1959.']);
    }
}
