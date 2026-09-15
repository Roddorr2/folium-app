<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrador General',
                'description' => 'Acceso completo a la plataforma, configuración global y gestión de usuarios.'
            ]
        );

        Role::updateOrCreate(
            ['name' => 'cataloger'],
            [
                'display_name' => 'Catalogador WEMI',
                'description' => 'Gestión técnica de obras, expresiones, manifestaciones e iconografía bibliotecaria.'
            ]
        );

        Role::updateOrCreate(
            ['name' => 'librarian'],
            [
                'display_name' => 'Bibliotecario de Sede',
                'description' => 'Gestión física de préstamos locales, devoluciones e inventarios de sucursal.'
            ]
        );

        Role::updateOrCreate(
            ['name' => 'network_librarian'],
            [
                'display_name' => 'Bibliotecario de Red Multi-Sede',
                'description' => 'Supervisión de préstamos intersede (ILL), transferencias y auditoría de incidencias en tránsito.'
            ]
        );

        Role::updateOrCreate(
            ['name' => 'reader'],
            [
                'display_name' => 'Lector Registrado',
                'description' => 'Usuario registrado OPAC con permisos de consulta, reserva de ejemplares y solicitud de traslados.'
            ]
        );
    }
}
