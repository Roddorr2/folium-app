<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $catalogerRole = Role::where('name', 'cataloger')->first();
        $librarianRole = Role::where('name', 'librarian')->first();
        $networkRole = Role::where('name', 'network_librarian')->first();
        $readerRole = Role::where('name', 'reader')->first();

        // 1. Admin
        User::updateOrCreate(
            ['email' => 'admin@folium.org'],
            [
                'name' => 'Directora de Red Bibliotecaria',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'branch_id' => 1,
                'dni' => '00100200'
            ]
        );

        // 2. Cataloger
        User::updateOrCreate(
            ['email' => 'cataloger@folium.org'],
            [
                'name' => 'Jefe de Catalogación WEMI',
                'password' => Hash::make('password'),
                'role_id' => $catalogerRole?->id,
                'branch_id' => 1,
                'dni' => '00200300'
            ]
        );

        // 3. Librarian (Lima)
        User::updateOrCreate(
            ['email' => 'librarian.lima@folium.org'],
            [
                'name' => 'Bibliotecario Sede Lima',
                'password' => Hash::make('password'),
                'role_id' => $librarianRole?->id,
                'branch_id' => 1,
                'dni' => '00300400'
            ]
        );

        // 4. Librarian (Cusco)
        User::updateOrCreate(
            ['email' => 'librarian.cusco@folium.org'],
            [
                'name' => 'Bibliotecaria Sede Cusco',
                'password' => Hash::make('password'),
                'role_id' => $librarianRole?->id,
                'branch_id' => 3,
                'dni' => '00400500'
            ]
        );

        // 5. Network Librarian (Multi-Branch ILL)
        User::updateOrCreate(
            ['email' => 'network.librarian@folium.org'],
            [
                'name' => 'Coordinador de Logística y Tránsito ILL',
                'password' => Hash::make('password'),
                'role_id' => $networkRole?->id,
                'branch_id' => 1,
                'dni' => '00500600'
            ]
        );

        // 6. Reader
        User::updateOrCreate(
            ['email' => 'reader@folium.org'],
            [
                'name' => 'Investigador Botánico Lector',
                'password' => Hash::make('password'),
                'role_id' => $readerRole?->id,
                'branch_id' => 1,
                'dni' => '00600700'
            ]
        );
    }
}
