<?php

namespace Database\Seeders;

use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Test Faculty Member
        $faculty = User::create([
            'first_name' => 'Jane',
            'middle_name' => 'A.',
            'last_name' => 'Smith',
            'identity_id' => 'FAC-1001',
            'email' => 'faculty@pcyc.edu',
            'password' => Hash::make('password'),
            'role' => 'faculty',
        ]);

        // 2. Create Test Students
        $students = [];
        for ($i = 1; $i <= 5; $i++) {
            $students[] = User::create([
                'first_name' => 'Test',
                'middle_name' => '',
                'last_name' => 'Student '.$i,
                'identity_id' => 'STU-200'.$i,
                'email' => "student{$i}@pcyc.edu",
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);
        }

        // 3. Create a Subject
        $subject = Subject::create([
            'code' => 'IT101',
            'name' => 'Introduction to Computing',
            'description' => 'Fundamentals of information technology and computer systems.',
        ]);

        // 4. Create a Class Section assigned to the Faculty
        $classSection = ClassSection::create([
            'subject_id' => $subject->id,
            'faculty_id' => $faculty->id,
            'name' => 'Block A',
            'schedule_details' => 'Mon/Wed 9:00 AM - 10:30 AM',
        ]);

        // 5. Enroll the Students in the Class Section
        foreach ($students as $student) {
            Enrollment::create([
                'class_section_id' => $classSection->id,
                'student_id' => $student->id,
                'status' => 'enrolled',
            ]);
        }
    }
}
