<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AuditorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'auditor')->first();

        if ($role) {
            User::updateOrCreate(
                ['email' => 'auditor@aset-imc.local'],
                [
                    'name' => 'Auditor User',
                    'password' => Hash::make('password123'),
                    'role_id' => $role->id,
                    'employee_id' => 'AUD001',
                    'department' => 'Internal Audit',
                    'is_active' => true,
                ]
            );
        }
    }
}
