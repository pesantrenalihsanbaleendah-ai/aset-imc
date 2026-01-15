<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'admin_aset')->first();

        if ($role) {
            User::updateOrCreate(
                ['email' => 'admin@aset-imc.local'],
                [
                    'name' => 'Admin Aset',
                    'password' => Hash::make('password123'),
                    'role_id' => $role->id,
                    'employee_id' => 'ADM001',
                    'department' => 'Asset Management',
                    'is_active' => true,
                ]
            );
        }
    }
}
