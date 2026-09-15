<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Work;

class WorkSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------------
        // 80% Clásicos Universales y Literatura Fundamental (Obra Literaria)
        // -------------------------------------------------------------

        // 101. Cien Años de Soledad
        $cienAnos = Work::firstOrCreate(
            ['id' => 101],
            [
                'title' => 'Cien Años de Soledad',
                'abstract' => 'Historia de la saga familiar Buendía a lo largo de siete generaciones en el emblemático e inolvidable pueblo de Macondo, obra cumbre del realismo mágico.',
                'original_language' => 'Español',
                'dewey' => '863.64',
                'nature' => 'Obra Literaria'
            ]
        );
        $cienAnos->authors()->sync([2]);
        $cienAnos->subjects()->sync([2, 5]);

        // 102. Rayuela
        $rayuela = Work::firstOrCreate(
            ['id' => 102],
            [
                'title' => 'Rayuela',
                'abstract' => 'Contranovela fundamental de la literatura hispanoamericana que propone una lectura discontinua y plural entre París y Buenos Aires a través de Horacio Oliveira.',
                'original_language' => 'Español',
                'dewey' => '863.64',
                'nature' => 'Obra Literaria'
            ]
        );
        $rayuela->authors()->sync([9]);
        $rayuela->subjects()->sync([2]);

        // 103. Ficciones
        $ficciones = Work::firstOrCreate(
            ['id' => 103],
            [
                'title' => 'Ficciones',
                'abstract' => 'Obra maestra del relato breve que explora bibliotecas infinitas, mapas del imperio, laberintos de espejos, juegos de lógica y paradojas del tiempo.',
                'original_language' => 'Español',
                'dewey' => '864.6',
                'nature' => 'Obra Literaria'
            ]
        );
        $ficciones->authors()->sync([5]);
        $ficciones->subjects()->sync([2, 6]);

        // 104. La Casa de los Espíritus
        $casaEspiritus = Work::firstOrCreate(
            ['id' => 104],
            [
                'title' => 'La Casa de los Espíritus',
                'abstract' => 'Crónica familiar e itinerario de cuatro generaciones de la familia Trueba, entrelazando pasiones personales, espíritus y las transformaciones sociopolíticas sudamericanas.',
                'original_language' => 'Español',
                'dewey' => '863.64',
                'nature' => 'Obra Literaria'
            ]
        );
        $casaEspiritus->authors()->sync([6]);
        $casaEspiritus->subjects()->sync([2, 5]);

        // 105. 1984
        $ow1984 = Work::firstOrCreate(
            ['id' => 105],
            [
                'title' => '1984',
                'abstract' => 'Novela distópica sobre un régimen totalitario de vigilancia masiva, manipulación de la verdad histórica y la figura ominosa del Gran Hermano.',
                'original_language' => 'Inglés',
                'dewey' => '823.912',
                'nature' => 'Obra Literaria'
            ]
        );
        $ow1984->authors()->sync([3]);
        $ow1984->subjects()->sync([3]);

        // 106. Don Quijote de la Mancha
        $quijote = Work::firstOrCreate(
            ['id' => 106],
            [
                'title' => 'Don Quijote de la Mancha',
                'abstract' => 'La cumbre de la literatura castellana y primera novela moderna, que narra los desvaríos caballerescos y diálogos entre el hidalgo Don Quijote y Sancho Panza.',
                'original_language' => 'Español',
                'dewey' => '863.3',
                'nature' => 'Obra Literaria'
            ]
        );
        $quijote->authors()->sync([10]);
        $quijote->subjects()->sync([8]);

        // 107. El Señor de los Anillos
        $lotr = Work::firstOrCreate(
            ['id' => 107],
            [
                'title' => 'El Señor de los Anillos',
                'abstract' => 'La epopeya fantástica clásica que sigue la travesía de Frodo Bolsón para destruir el Anillo Único en la Tierra Media.',
                'original_language' => 'Inglés',
                'dewey' => '823.914',
                'nature' => 'Obra Literaria'
            ]
        );
        $lotr->authors()->sync([1]);
        $lotr->subjects()->sync([1]);

        // 108. La Ciudad y los Perros
        $ciudadPerros = Work::firstOrCreate(
            ['id' => 108],
            [
                'title' => 'La Ciudad y los Perros',
                'abstract' => 'Novela emblemática ambientada en el Colegio Militar Leoncio Prado que explora la disciplina autoritaria, la lealtad y la juventud urbana.',
                'original_language' => 'Español',
                'dewey' => '863.64',
                'nature' => 'Obra Literaria'
            ]
        );
        $ciudadPerros->authors()->sync([7]);
        $ciudadPerros->subjects()->sync([2]);

        // -------------------------------------------------------------
        // 10% Tratados Científicos e Historia Natural (Tratado Científico)
        // -------------------------------------------------------------

        // 109. Flora Huayaquilensis
        $huaya = Work::firstOrCreate(
            ['id' => 109],
            [
                'title' => 'Flora Huayaquilensis',
                'abstract' => 'Monumental estudio botánico de la flora neotropical y farmacopea virreinal de la Real Expedición a los Reinos del Perú y Chile, registrado en láminas e ilustraciones.',
                'original_language' => 'Latín',
                'dewey' => '580.0',
                'nature' => 'Tratado Científico'
            ]
        );
        $huaya->authors()->sync([4, 8]);
        $huaya->subjects()->sync([4, 7]);

        // 110. El Origen de las Especies
        $origen = Work::firstOrCreate(
            ['id' => 110],
            [
                'title' => 'El Origen de las Especies',
                'abstract' => 'Tratado fundacional de la biología moderna que presenta la teoría de la evolución mediante la selección natural y la diversificación de las formas vivas.',
                'original_language' => 'Inglés',
                'dewey' => '576.8',
                'nature' => 'Tratado Científico'
            ]
        );
        $origen->authors()->sync([11]);
        $origen->subjects()->sync([9, 10]);

        // 111. Cosmos
        $cosmos = Work::firstOrCreate(
            ['id' => 111],
            [
                'title' => 'Cosmos',
                'abstract' => 'Obra maestra de divulgación científica que explora la evolución del universo, la historia de la astronomía y el lugar de la humanidad en el cosmos.',
                'original_language' => 'Inglés',
                'dewey' => '520.0',
                'nature' => 'Tratado Científico'
            ]
        );
        $cosmos->authors()->sync([12]);
        $cosmos->subjects()->sync([10]);

        // -------------------------------------------------------------
        // 10% Ensayo, Historia y Pensamiento Crítico (Ensayo)
        // -------------------------------------------------------------

        // 112. Sapiens: De animales a dioses
        $sapiens = Work::firstOrCreate(
            ['id' => 112],
            [
                'title' => 'Sapiens: De animales a dioses',
                'abstract' => 'Ensayo de historia universal y antropología que recorre la evolución del Homo sapiens desde la revolución cognitiva hasta las tecnologías del siglo XXI.',
                'original_language' => 'Hebreo',
                'dewey' => '909.00',
                'nature' => 'Ensayo'
            ]
        );
        $sapiens->authors()->sync([13]);
        $sapiens->subjects()->sync([11, 12]);

        // 113. El Laberinto de la Soledad
        $laberinto = Work::firstOrCreate(
            ['id' => 113],
            [
                'title' => 'El Laberinto de la Soledad',
                'abstract' => 'Ensayo fundamental sobre la identidad, el mito, la historia y la condición colectiva mexicana y latinoamericana frente a la modernidad.',
                'original_language' => 'Español',
                'dewey' => '864.6',
                'nature' => 'Ensayo'
            ]
        );
        $laberinto->authors()->sync([14]);
        $laberinto->subjects()->sync([6, 12]);
    }
}
