<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'staff')->first();

        if ($role) {
            User::updateOrCreate(
                ['email' => 'staff@aset-imc.local'],
                [
                    'name' => 'Staff User',
                    'password' => Hash::make('password123'),
                    'role_id' => $role->id,
                    'employee_id' => 'STF001',
                    'department' => 'Operations',
                    'is_active' => true,
                ]
            );
        }
    }
}
