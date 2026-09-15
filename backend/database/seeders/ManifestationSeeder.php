<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Manifestation;

class ManifestationSeeder extends Seeder
{
    public function run(): void
    {
        // Expression 1: Cien Años de Soledad
        Manifestation::firstOrCreate(['id' => 1], ['expression_id' => 1, 'isbn' => '978-03-074-7472-8', 'publisher' => 'Alfaguara', 'publication_year' => 2007, 'format' => 'Tapa Dura']);

        // Expression 2: Rayuela
        Manifestation::firstOrCreate(['id' => 2], ['expression_id' => 2, 'isbn' => '978-84-376-0457-2', 'publisher' => 'Cátedra', 'publication_year' => 1991, 'format' => 'Rústica']);

        // Expression 3: Ficciones
        Manifestation::firstOrCreate(['id' => 3], ['expression_id' => 3, 'isbn' => '978-84-206-3312-1', 'publisher' => 'Alianza Editorial', 'publication_year' => 1998, 'format' => 'Tapa Dura']);

        // Expression 4: La Casa de los Espíritus
        Manifestation::firstOrCreate(['id' => 4], ['expression_id' => 4, 'isbn' => '978-84-010-1060-6', 'publisher' => 'Plaza & Janés', 'publication_year' => 1995, 'format' => 'Bolsillo']);

        // Expression 5 & 6: 1984 (Español e Inglés)
        Manifestation::firstOrCreate(['id' => 5], ['expression_id' => 5, 'isbn' => '978-84-998-9094-4', 'publisher' => 'Debolsillo', 'publication_year' => 2013, 'format' => 'Bolsillo']);
        Manifestation::firstOrCreate(['id' => 6], ['expression_id' => 6, 'isbn' => '978-01-410-3614-4', 'publisher' => 'Penguin Classics', 'publication_year' => 2008, 'format' => 'Bolsillo']);

        // Expression 7: Don Quijote de la Mancha
        Manifestation::firstOrCreate(['id' => 7], ['expression_id' => 7, 'isbn' => '978-84-241-1684-2', 'publisher' => 'Real Academia Española', 'publication_year' => 2015, 'format' => 'Tapa Dura en Lino']);

        // Expression 8 & 9: El Señor de los Anillos
        Manifestation::firstOrCreate(['id' => 8], ['expression_id' => 8, 'isbn' => '978-84-450-7141-0', 'publisher' => 'Minotauro', 'publication_year' => 2002, 'format' => 'Tapa Dura en Lino']);
        Manifestation::firstOrCreate(['id' => 9], ['expression_id' => 9, 'isbn' => '978-00-075-2554-6', 'publisher' => 'HarperCollins UK', 'publication_year' => 2012, 'format' => 'Bolsillo']);

        // Expression 10: La Ciudad y los Perros
        Manifestation::firstOrCreate(['id' => 10], ['expression_id' => 10, 'isbn' => '978-84-204-7183-9', 'publisher' => 'Seix Barral', 'publication_year' => 2000, 'format' => 'Tapa Dura']);

        // Expression 11: Flora Huayaquilensis
        Manifestation::firstOrCreate(['id' => 11], ['expression_id' => 11, 'isbn' => '978-84-000-6922-3', 'publisher' => 'Real Jardín Botánico CSIC', 'publication_year' => 1989, 'format' => 'Gran Formato Ilustrado']);

        // Expression 12: El Origen de las Especies
        Manifestation::firstOrCreate(['id' => 12], ['expression_id' => 12, 'isbn' => '978-84-670-3418-9', 'publisher' => 'Espasa Calpe', 'publication_year' => 2009, 'format' => 'Tapa Dura']);

        // Expression 13: Cosmos
        Manifestation::firstOrCreate(['id' => 13], ['expression_id' => 13, 'isbn' => '978-84-080-5304-0', 'publisher' => 'Planeta', 'publication_year' => 2004, 'format' => 'Ilustrado Tapa Dura']);

        // Expression 14: Sapiens
        Manifestation::firstOrCreate(['id' => 14], ['expression_id' => 14, 'isbn' => '978-84-999-2622-3', 'publisher' => 'Debate', 'publication_year' => 2014, 'format' => 'Rústica']);

        // Expression 15: El Laberinto de la Soledad
        Manifestation::firstOrCreate(['id' => 15], ['expression_id' => 15, 'isbn' => '978-96-816-0302-1', 'publisher' => 'Fondo de Cultura Económica', 'publication_year' => 1992, 'format' => 'Rústica']);
    }
}
