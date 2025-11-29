<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Campaign permissions
            'create-campaigns',
            'view-campaigns',
            'edit-own-campaigns',
            'edit-all-campaigns',
            'delete-campaigns',
            'approve-campaigns',
            'feature-campaigns',
            
            // Donation permissions
            'make-donations',
            'view-own-donations',
            'view-all-donations',
            'refund-donations',
            
            // User management permissions
            'manage-users',
            'view-user-reports',
            
            // Admin permissions
            'manage-platform-settings',
            'view-analytics',
            'manage-roles',
            'system-administration',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Employee role - regular employees
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->givePermissionTo([
            'create-campaigns',
            'view-campaigns',
            'edit-own-campaigns',
            'make-donations',
            'view-own-donations',
        ]);

        // Campaign Manager role - can approve and manage campaigns
        $campaignManager = Role::firstOrCreate(['name' => 'campaign_manager']);
        $campaignManager->givePermissionTo([
            'create-campaigns',
            'view-campaigns',
            'edit-own-campaigns',
            'edit-all-campaigns',
            'approve-campaigns',
            'feature-campaigns',
            'make-donations',
            'view-own-donations',
            'view-all-donations',
            'view-user-reports',
        ]);

        // Admin role - full access
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $this->command->info('Roles and permissions have been created successfully!');
        $this->command->info('Created roles: employee, campaign_manager, admin');
    }
}
