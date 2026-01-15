<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\AssetCategory;
use App\Models\Location;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Roles
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'description' => 'Super Administrator',
        ]);

        $adminRole = Role::create([
            'name' => 'admin_aset',
            'description' => 'Administrator Aset',
        ]);

        $approverRole = Role::create([
            'name' => 'approver',
            'description' => 'Approver / Manager',
        ]);

        $staffRole = Role::create([
            'name' => 'staff',
            'description' => 'Staf / User Umum',
        ]);

        $auditorRole = Role::create([
            'name' => 'auditor',
            'description' => 'Auditor',
        ]);

        // Create Permissions
        $permissions = [
            // Asset permissions
            'asset.view',
            'asset.create',
            'asset.edit',
            'asset.delete',
            'asset.import',
            // Loan permissions
            'loan.view',
            'loan.request',
            'loan.approve',
            'loan.reject',
            // Maintenance permissions
            'maintenance.view',
            'maintenance.create',
            'maintenance.approve',
            // Transfer permissions
            'transfer.view',
            'transfer.request',
            'transfer.approve',
            // Disposal permissions
            'disposal.view',
            'disposal.request',
            'disposal.approve',
            // Report permissions
            'report.view',
            'report.export',
            // User & Role permissions
            'user.manage',
            'role.manage',
            'permission.manage',
            // Audit permissions
            'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to Super Admin (all)
        $superAdminRole->permissions()->attach(Permission::all());

        // Assign permissions to Admin
        $adminPerms = Permission::whereIn('name', [
            'asset.view',
            'asset.create',
            'asset.edit',
            'asset.delete',
            'asset.import',
            'loan.view',
            'loan.approve',
            'maintenance.view',
            'maintenance.create',
            'maintenance.approve',
            'transfer.view',
            'transfer.approve',
            'disposal.view',
            'disposal.approve',
            'report.view',
            'report.export',
            'audit.view',
        ])->pluck('id');
        $adminRole->permissions()->attach($adminPerms);

        // Assign permissions to Approver
        $approverPerms = Permission::whereIn('name', [
            'asset.view',
            'loan.view',
            'loan.approve',
            'maintenance.view',
            'maintenance.approve',
            'transfer.view',
            'transfer.approve',
            'disposal.view',
            'disposal.approve',
            'report.view',
        ])->pluck('id');
        $approverRole->permissions()->attach($approverPerms);

        // Assign permissions to Staff
        $staffPerms = Permission::whereIn('name', [
            'asset.view',
            'loan.request',
            'maintenance.create',
        ])->pluck('id');
        $staffRole->permissions()->attach($staffPerms);

        // Assign permissions to Auditor
        $auditorPerms = Permission::whereIn('name', [
            'asset.view',
            'loan.view',
            'maintenance.view',
            'transfer.view',
            'disposal.view',
            'audit.view',
        ])->pluck('id');
        $auditorRole->permissions()->attach($auditorPerms);

        // Create Super Admin User
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@aset-imc.local',
            'password' => bcrypt('password123'),
            'role_id' => $superAdminRole->id,
            'employee_id' => 'SA001',
            'department' => 'IT',
            'is_active' => true,
        ]);

        // Create Asset Categories
        $categories = [
            ['name' => 'IT Equipment', 'code' => 'IT', 'depreciation_years' => 3],
            ['name' => 'Furniture', 'code' => 'FUR', 'depreciation_years' => 5],
            ['name' => 'Kendaraan', 'code' => 'KND', 'depreciation_years' => 8],
            ['name' => 'Mesin', 'code' => 'MSN', 'depreciation_years' => 10],
            ['name' => 'Bangunan', 'code' => 'BGN', 'depreciation_years' => 20],
        ];

        foreach ($categories as $cat) {
            AssetCategory::create($cat);
        }

        // Create Locations
        $buildingA = Location::create([
            'name' => 'Building A',
            'level' => 'building',
            'address' => 'Jalan Sudirman No. 1',
        ]);

        Location::create([
            'name' => 'Floor 1',
            'parent_id' => $buildingA->id,
            'level' => 'floor',
            'floor' => '1',
        ]);

        Location::create([
            'name' => 'Floor 2',
            'parent_id' => $buildingA->id,
            'level' => 'floor',
            'floor' => '2',
        ]);

        $buildingB = Location::create([
            'name' => 'Building B',
            'level' => 'building',
            'address' => 'Jalan Gatot Subroto No. 2',
        ]);

        Location::create([
            'name' => 'Floor 1',
            'parent_id' => $buildingB->id,
            'level' => 'floor',
            'floor' => '1',
        ]);

        // Create Sample Assets
        $itCategory = AssetCategory::where('code', 'IT')->first();
        $furnitureCategory = AssetCategory::where('code', 'FUR')->first();
        $floor1A = Location::where('parent_id', $buildingA->id)->where('floor', '1')->first();
        $floor2A = Location::where('parent_id', $buildingA->id)->where('floor', '2')->first();

        \App\Models\Asset::create([
            'asset_code' => 'IT-2024-001',
            'name' => 'Laptop Dell Latitude 5420',
            'category_id' => $itCategory->id,
            'location_id' => $floor1A->id,
            'description' => 'Laptop untuk staff IT',
            'acquisition_price' => 15000000,
            'book_value' => 15000000,
            'condition' => 'good',
            'status' => 'active',
            'acquisition_date' => now()->subMonths(2),
            'warranty_until' => now()->addYears(2),
            'serial_number' => 'DL5420-2024-001',
        ]);

        \App\Models\Asset::create([
            'asset_code' => 'IT-2024-002',
            'name' => 'Monitor LG 27 inch',
            'category_id' => $itCategory->id,
            'location_id' => $floor1A->id,
            'description' => 'Monitor untuk workstation',
            'acquisition_price' => 3500000,
            'book_value' => 3500000,
            'condition' => 'good',
            'status' => 'active',
            'acquisition_date' => now()->subMonths(1),
            'warranty_until' => now()->addYears(1),
            'serial_number' => 'LG27-2024-002',
        ]);

        \App\Models\Asset::create([
            'asset_code' => 'FUR-2024-001',
            'name' => 'Meja Kerja Kayu Jati',
            'category_id' => $furnitureCategory->id,
            'location_id' => $floor2A->id,
            'description' => 'Meja kerja untuk ruang meeting',
            'acquisition_price' => 5000000,
            'book_value' => 5000000,
            'condition' => 'acceptable',
            'status' => 'active',
            'acquisition_date' => now()->subMonths(6),
            'serial_number' => 'MK-2024-001',
        ]);

        \App\Models\Asset::create([
            'asset_code' => 'IT-2023-015',
            'name' => 'Printer HP LaserJet',
            'category_id' => $itCategory->id,
            'location_id' => $floor1A->id,
            'description' => 'Printer untuk keperluan kantor',
            'acquisition_price' => 4500000,
            'book_value' => 3000000,
            'condition' => 'poor',
            'status' => 'maintenance',
            'acquisition_date' => now()->subYear(),
            'warranty_until' => now()->subMonths(2),
            'serial_number' => 'HP-LJ-2023-015',
        ]);

        $this->call([
            AdminUserSeeder::class,
            ApproverUserSeeder::class,
            StaffUserSeeder::class,
            AuditorUserSeeder::class,
        ]);
    }
}
