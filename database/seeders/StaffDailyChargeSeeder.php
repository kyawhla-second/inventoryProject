<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaffDailyCharge;
use App\Models\User;
use Carbon\Carbon;

class StaffDailyChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffUsers = User::where('role', '!=', 'admin')->get();
        
        if ($staffUsers->isEmpty()) {
            $this->command->info('No staff users found. Creating sample staff users...');
            
            // Create sample staff users
            $manager = User::create([
                'name' => 'John Manager',
                'email' => 'manager@example.com',
                'password' => bcrypt('password'),
                'role' => 'manager',
            ]);
            
            $staff = User::create([
                'name' => 'Jane Staff',
                'email' => 'staff@example.com',
                'password' => bcrypt('password'),
                'role' => 'staff',
            ]);
            
            $staffUsers = collect([$manager, $staff]);
        }

        // Create charges for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip weekends for some variety
            if ($date->isWeekend() && rand(0, 2) == 0) {
                continue;
            }

            foreach ($staffUsers as $user) {
                // Create charges for about 80% of days
                if (rand(0, 4) == 0) {
                    continue;
                }

                $dailyRate = $user->role == 'manager' ? rand(120, 180) : rand(80, 120);
                $hoursWorked = rand(7, 9) + (rand(0, 1) * 0.5); // 7-9.5 hours
                $overtimeHours = rand(0, 3) + (rand(0, 1) * 0.5); // 0-3.5 hours
                $overtimeRate = ($dailyRate / 8) * 1.5; // 1.5x regular hourly rate

                $charge = new StaffDailyCharge([
                    'user_id' => $user->id,
                    'charge_date' => $date->format('Y-m-d'),
                    'daily_rate' => $dailyRate,
                    'hours_worked' => $hoursWorked,
                    'overtime_hours' => $overtimeHours,
                    'overtime_rate' => $overtimeRate,
                    'notes' => rand(0, 3) == 0 ? 'Sample work notes for ' . $date->format('M d') : null,
                    'status' => ['pending', 'approved', 'paid'][rand(0, 2)],
                ]);

                $charge->calculateTotalCharge();
                $charge->save();
            }
        }

        $this->command->info('Staff daily charges seeded successfully!');
    }
}