<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Create test admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@acme.com',
            'employee_id' => 'EMP001',
            'department' => 'IT',
            'position' => 'System Administrator',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create test campaign manager
        $campaignManager = User::factory()->create([
            'name' => 'Campaign Manager',
            'email' => 'campaigns@acme.com',
            'employee_id' => 'EMP002',
            'department' => 'CSR',
            'position' => 'Campaign Manager',
            'is_active' => true,
        ]);
        $campaignManager->assignRole('campaign_manager');

        // Create test employee
        $employee = User::factory()->create([
            'name' => 'John Employee',
            'email' => 'john@acme.com',
            'employee_id' => 'EMP003',
            'department' => 'Finance',
            'position' => 'Financial Analyst',
            'is_active' => true,
        ]);
        $employee->assignRole('employee');

        // Seed sample campaigns
        $this->call([
            CampaignSeeder::class,
        ]);
    }
}
