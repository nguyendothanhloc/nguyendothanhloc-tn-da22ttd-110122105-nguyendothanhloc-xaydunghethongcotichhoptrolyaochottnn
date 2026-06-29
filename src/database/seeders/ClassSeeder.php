<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::where('is_active', true)->get();
        $teachers = Teacher::all();

        if ($courses->isEmpty() || $teachers->isEmpty()) {
            $this->command->warn('No courses or teachers found. Please seed courses and teachers first.');
            return;
        }

        // Create 3 sample classes
        $classes = [
            [
                'course_id' => $courses->first()->id,
                'teacher_id' => $teachers->first()->id,
                'name' => 'Lớp ' . $courses->first()->name . ' - Sáng T2-T4-T6',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(7)->addWeeks($courses->first()->duration_weeks),
                'max_capacity' => 20,
                'current_enrollment' => 0,
                'status' => 'upcoming',
            ],
            [
                'course_id' => $courses->first()->id,
                'teacher_id' => $teachers->first()->id,
                'name' => 'Lớp ' . $courses->first()->name . ' - Chiều T3-T5-T7',
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(14)->addWeeks($courses->first()->duration_weeks),
                'max_capacity' => 25,
                'current_enrollment' => 0,
                'status' => 'upcoming',
            ],
        ];

        foreach ($classes as $classData) {
            ClassModel::create($classData);
        }

        $this->command->info('Classes seeded successfully!');
    }
}
