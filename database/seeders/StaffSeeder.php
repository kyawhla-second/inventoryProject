<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\User;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users
        $users = User::all();
        
        $staffData = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@company.com',
                'phone' => '+1-555-0101',
                'address' => '123 Main St, City, State 12345',
                'date_of_birth' => '1985-03-15',
                'hire_date' => '2020-01-15',
                'position' => 'Production Manager',
                'department' => 'Production',
                'base_salary' => 5000.00,
                'hourly_rate' => 25.00,
                'overtime_rate' => 37.50,
                'employment_type' => 'full_time',
                'status' => 'active',
                'emergency_contact_name' => 'Jane Smith',
                'emergency_contact_phone' => '+1-555-0102',
                'notes' => 'Experienced production manager with 10+ years in manufacturing.',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@company.com',
                'phone' => '+1-555-0201',
                'address' => '456 Oak Ave, City, State 12345',
                'date_of_birth' => '1990-07-22',
                'hire_date' => '2021-03-01',
                'position' => 'Quality Control Specialist',
                'department' => 'Quality Assurance',
                'base_salary' => 3500.00,
                'hourly_rate' => 18.00,
                'overtime_rate' => 27.00,
                'employment_type' => 'full_time',
                'status' => 'active',
                'emergency_contact_name' => 'Mike Johnson',
                'emergency_contact_phone' => '+1-555-0202',
                'notes' => 'Detail-oriented quality specialist with certification in ISO standards.',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'michael.brown@company.com',
                'phone' => '+1-555-0301',
                'address' => '789 Pine St, City, State 12345',
                'date_of_birth' => '1988-11-08',
                'hire_date' => '2019-06-15',
                'position' => 'Machine Operator',
                'department' => 'Production',
                'base_salary' => 2800.00,
                'hourly_rate' => 15.00,
                'overtime_rate' => 22.50,
                'employment_type' => 'full_time',
                'status' => 'active',
                'emergency_contact_name' => 'Lisa Brown',
                'emergency_contact_phone' => '+1-555-0302',
                'notes' => 'Skilled machine operator with expertise in multiple production lines.',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@company.com',
                'phone' => '+1-555-0401',
                'address' => '321 Elm St, City, State 12345',
                'date_of_birth' => '1992-05-12',
                'hire_date' => '2022-01-10',
                'position' => 'Inventory Clerk',
                'department' => 'Warehouse',
                'base_salary' => 2400.00,
                'hourly_rate' => 12.00,
                'overtime_rate' => 18.00,
                'employment_type' => 'part_time',
                'status' => 'active',
                'emergency_contact_name' => 'Robert Davis',
                'emergency_contact_phone' => '+1-555-0402',
                'notes' => 'Reliable part-time employee handling inventory management.',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.wilson@company.com',
                'phone' => '+1-555-0501',
                'address' => '654 Maple Ave, City, State 12345',
                'date_of_birth' => '1983-09-30',
                'hire_date' => '2018-04-20',
                'position' => 'Maintenance Technician',
                'department' => 'Maintenance',
                'base_salary' => 3200.00,
                'hourly_rate' => 16.50,
                'overtime_rate' => 24.75,
                'employment_type' => 'full_time',
                'status' => 'active',
                'emergency_contact_name' => 'Carol Wilson',
                'emergency_contact_phone' => '+1-555-0502',
                'notes' => 'Certified maintenance technician with electrical and mechanical skills.',
            ],
        ];

        foreach ($staffData as $index => $data) {
            // Link to existing user if available
            if ($users->count() > $index) {
                $data['user_id'] = $users[$index]->id;
            }
            
            // Set supervisor (first staff member supervises others)
            if ($index > 0) {
                $data['supervisor_id'] = 1; // John Smith as supervisor
            }
            
            Staff::create($data);
        }

        $this->command->info('Created ' . count($staffData) . ' staff members.');
    }
}