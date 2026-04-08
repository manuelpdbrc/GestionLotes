<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admins
            ['name' => 'Admin Principal', 'email' => 'admin@gestionlotes.com', 'role' => 'admin'],
            ['name' => 'Admin Soporte', 'email' => 'admin2@gestionlotes.com', 'role' => 'admin'],

            // Supervisores
            ['name' => 'Supervisor García', 'email' => 'supervisor1@gestionlotes.com', 'role' => 'supervisor'],
            ['name' => 'Supervisor López', 'email' => 'supervisor2@gestionlotes.com', 'role' => 'supervisor'],
            ['name' => 'Supervisor Martínez', 'email' => 'supervisor3@gestionlotes.com', 'role' => 'supervisor'],

            // Vendedores
            ['name' => 'Carlos Vendedor', 'email' => 'vendedor1@gestionlotes.com', 'role' => 'vendedor'],
            ['name' => 'María Vendedora', 'email' => 'vendedor2@gestionlotes.com', 'role' => 'vendedor'],
            ['name' => 'Juan Vendedor', 'email' => 'vendedor3@gestionlotes.com', 'role' => 'vendedor'],
            ['name' => 'Ana Vendedora', 'email' => 'vendedor4@gestionlotes.com', 'role' => 'vendedor'],
            ['name' => 'Pedro Vendedor', 'email' => 'vendedor5@gestionlotes.com', 'role' => 'vendedor'],

            // Control
            ['name' => 'Control Dirección', 'email' => 'control1@gestionlotes.com', 'role' => 'control'],
            ['name' => 'Control Finanzas', 'email' => 'control2@gestionlotes.com', 'role' => 'control'],
            ['name' => 'Control Marketing', 'email' => 'control3@gestionlotes.com', 'role' => 'control'],
            ['name' => 'Control Legal', 'email' => 'control4@gestionlotes.com', 'role' => 'control'],
            ['name' => 'Control Externo', 'email' => 'control5@gestionlotes.com', 'role' => 'control'],
        ];

        foreach ($users as $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'role' => $userData['role'],
                'email_verified_at' => now(),
            ]);
        }
    }
}
