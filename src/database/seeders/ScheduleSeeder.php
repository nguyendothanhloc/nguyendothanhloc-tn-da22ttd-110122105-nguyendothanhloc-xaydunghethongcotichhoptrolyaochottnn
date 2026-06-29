<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all classes
        $classes = ClassModel::all();

        foreach ($classes as $class) {
            // Create 10 sessions for each class
            $startDate = Carbon::parse($class->start_date);
            $endDate = Carbon::parse($class->end_date);
            
            // Calculate number of weeks
            $weeks = $startDate->diffInWeeks($endDate);
            $sessionsPerWeek = 2; // 2 sessions per week
            $totalSessions = min($weeks * $sessionsPerWeek, 20); // Max 20 sessions

            for ($i = 1; $i <= $totalSessions; $i++) {
                // Calculate session date (every Monday and Wednesday)
                $daysToAdd = floor(($i - 1) / 2) * 7 + (($i - 1) % 2) * 2;
                $sessionDate = $startDate->copy()->addDays($daysToAdd);

                // Skip if session date is after end date
                if ($sessionDate->gt($endDate)) {
                    break;
                }

                Schedule::create([
                    'class_id' => $class->id,
                    'date' => $sessionDate,
                    'start_time' => '18:00:00',
                    'end_time' => '20:00:00',
                    'location' => 'Phòng ' . rand(101, 110),
                    'topic' => 'Buổi ' . $i,
                    'status' => $sessionDate->isPast() ? 'completed' : 'scheduled',
                ]);
            }
        }
    }
}
