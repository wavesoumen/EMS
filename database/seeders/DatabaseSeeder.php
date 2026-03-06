<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\TimeBank;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default company
        $company = Company::create([
            'name' => 'MUKTI',
            'address' => 'MUKTI Head Office',
            'settings' => [
                'timezone' => 'Asia/Kolkata',
                'work_hours' => 8,
            ],
        ]);

        // Create default roles
        $adminRole = Role::create([
            'name' => 'Admin',
            'level' => 1,
            'company_id' => $company->id,
            'permissions' => [
                'manage_company', 'manage_roles', 'manage_users',
                'approve_clockin', 'view_reports', 'view_all_users',
                'manage_attendance',
            ],
        ]);

        $hrRole = Role::create([
            'name' => 'HR',
            'level' => 2,
            'company_id' => $company->id,
            'permissions' => [
                'manage_users', 'approve_clockin', 'view_reports', 'view_all_users',
            ],
        ]);

        $managerRole = Role::create([
            'name' => 'Manager',
            'level' => 3,
            'company_id' => $company->id,
            'permissions' => [
                'approve_clockin', 'view_reports',
            ],
        ]);

        $employeeRole = Role::create([
            'name' => 'Employee',
            'level' => 4,
            'company_id' => $company->id,
            'permissions' => [],
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'MUKTI Admin',
            'mobile' => '9999999999',
            'role_id' => $adminRole->id,
            'company_id' => $company->id,
            'supervisor_id' => null,
            'is_active' => true,
        ]);

        TimeBank::create(['user_id' => $admin->id, 'total_minutes' => 0]);

        // Create sample HR
        $hr = User::create([
            'name' => 'HR Manager',
            'mobile' => '8888888888',
            'role_id' => $hrRole->id,
            'company_id' => $company->id,
            'supervisor_id' => $admin->id,
            'is_active' => true,
        ]);

        TimeBank::create(['user_id' => $hr->id, 'total_minutes' => 0]);

        // Create sample Manager
        $manager = User::create([
            'name' => 'Team Manager',
            'mobile' => '7777777777',
            'role_id' => $managerRole->id,
            'company_id' => $company->id,
            'supervisor_id' => $hr->id,
            'is_active' => true,
        ]);

        TimeBank::create(['user_id' => $manager->id, 'total_minutes' => 0]);

        // Create sample Employee
        $employee = User::create([
            'name' => 'John Employee',
            'mobile' => '6666666666',
            'role_id' => $employeeRole->id,
            'company_id' => $company->id,
            'supervisor_id' => $manager->id,
            'is_active' => true,
        ]);

        TimeBank::create(['user_id' => $employee->id, 'total_minutes' => 0]);
    }
}
