<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create teacher accounts
        $teachers = [
            [
                'name' => 'Nguyễn Văn Giáo',
                'email' => 'teacher1@teacher.com',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'phone' => '0901234567',
                'is_active' => true,
                'specialization' => 'Tiếng Anh giao tiếp',
                'qualifications' => 'Cử nhân Ngôn ngữ Anh, TESOL Certificate',
                'bio' => '5 năm kinh nghiệm giảng dạy tiếng Anh giao tiếp',
            ],
            [
                'name' => 'Trần Thị Lan',
                'email' => 'teacher2@teacher.com',
                'password' => Hash::make('teacher123'),
                'role' => 'teacher',
                'phone' => '0902345678',
                'is_active' => true,
                'specialization' => 'Tiếng Anh học thuật',
                'qualifications' => 'Thạc sĩ Ngôn ngữ học, IELTS 8.0',
                'bio' => '7 năm kinh nghiệm giảng dạy IELTS và tiếng Anh học thuật',
            ],
        ];

        foreach ($teachers as $teacherData) {
            // Extract teacher-specific fields
            $specialization = $teacherData['specialization'];
            $qualifications = $teacherData['qualifications'];
            $bio = $teacherData['bio'];
            
            unset($teacherData['specialization'], $teacherData['qualifications'], $teacherData['bio']);
            
            // Create user
            $user = User::create($teacherData);
            
            // Create teacher profile
            Teacher::create([
                'user_id' => $user->id,
                'specialization' => $specialization,
                'qualifications' => $qualifications,
                'bio' => $bio,
            ]);
        }

        $this->command->info('Teachers seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('  teacher1@teacher.com / teacher123');
        $this->command->info('  teacher2@teacher.com / teacher123');
    }
}
