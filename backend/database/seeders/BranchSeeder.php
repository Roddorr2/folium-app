<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::updateOrCreate(['id' => 1], ['name' => 'Sede Lima Central', 'city' => 'Lima', 'address' => 'Av. Javier Prado Este #102, San Isidro', 'phone' => '(01) 512-8000']);
        Branch::updateOrCreate(['id' => 2], ['name' => 'Sede Arequipa', 'city' => 'Arequipa', 'address' => 'Calle Santa Catalina #45, Cercado', 'phone' => '(054) 215-400']);
        Branch::updateOrCreate(['id' => 3], ['name' => 'Sede Cusco', 'city' => 'Cusco', 'address' => 'Av. El Sol #89, Centro Histórico', 'phone' => '(084) 234-900']);
        Branch::updateOrCreate(['id' => 4], ['name' => 'Sede Trujillo', 'city' => 'Trujillo', 'address' => 'Jr. Pizarro #210, Trujillo Central', 'phone' => '(044) 201-330']);
    }
}
