<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class ApproverUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'approver')->first();

        if ($role) {
            User::updateOrCreate(
                ['email' => 'approver@aset-imc.local'],
                [
                    'name' => 'Approver Manager',
                    'password' => Hash::make('password123'),
                    'role_id' => $role->id,
                    'employee_id' => 'APR001',
                    'department' => 'Management',
                    'is_active' => true,
                ]
            );
        }
    }
}
