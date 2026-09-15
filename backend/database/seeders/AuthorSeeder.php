<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        Author::firstOrCreate(['id' => 1], ['name' => 'J.R.R. Tolkien']);
        Author::firstOrCreate(['id' => 2], ['name' => 'Gabriel García Márquez']);
        Author::firstOrCreate(['id' => 3], ['name' => 'George Orwell']);
        Author::firstOrCreate(['id' => 4], ['name' => 'Alexander von Humboldt']);
        Author::firstOrCreate(['id' => 5], ['name' => 'Jorge Luis Borges']);
        Author::firstOrCreate(['id' => 6], ['name' => 'Isabel Allende']);
        Author::firstOrCreate(['id' => 7], ['name' => 'Mario Vargas Llosa']);
        Author::firstOrCreate(['id' => 8], ['name' => 'Juan Tafalla & Hipólito Ruiz']);
        Author::firstOrCreate(['id' => 9], ['name' => 'Julio Cortázar']);
        Author::firstOrCreate(['id' => 10], ['name' => 'Miguel de Cervantes']);
        Author::firstOrCreate(['id' => 11], ['name' => 'Charles Darwin']);
        Author::firstOrCreate(['id' => 12], ['name' => 'Carl Sagan']);
        Author::firstOrCreate(['id' => 13], ['name' => 'Yuval Noah Harari']);
        Author::firstOrCreate(['id' => 14], ['name' => 'Octavio Paz']);
    }
}
