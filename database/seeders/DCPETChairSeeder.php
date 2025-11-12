<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DCPETChairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dcpet = Course::where('code', 'DCPET')->first();

        if (!$dcpet) {
            $this->command->error('DCPET course not found!');
            return;
        }

        $user = User::updateOrCreate(
            ['email' => 'chair.dcpet@pup.edu.ph'],
            [
                'name' => 'Dr. Maria Santos',
                'password' => Hash::make('password'),
                'role' => 'chair',
                'course_id' => $dcpet->id,
            ]
        );

        $this->command->info('DCPET Chair created successfully!');
        $this->command->info('Email: ' . $user->email);
        $this->command->info('Password: password');
    }
}
