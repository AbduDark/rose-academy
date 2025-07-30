<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Subscription;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create an admin user
        User::updateOrCreate([
            'email' => 'admin@roseacademy.com'
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password123'),
            'gender' => 'male',
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create some student users
        User::factory()->count(5)->create();

        // Create some courses
        $courses = [
            ['title' => 'Chemistry 101', 'description' => 'Introductory Chemistry', 'price' => 100.00],
            ['title' => 'Physics Basics', 'description' => 'Fundamentals of Physics', 'price' => 120.00],
            ['title' => 'Mathematics Advanced', 'description' => 'Advanced Math Course', 'price' => 150.00],
        ];

        foreach ($courses as $data) {
            Course::updateOrCreate(
                ['title' => $data['title']],
                ['description' => $data['description'], 'price' => $data['price']]
            );
        }

        // Subscribe first student to first course
        $student = User::where('role', 'student')->first();
        $firstCourse = Course::first();

        if ($student && $firstCourse) {
            Subscription::updateOrCreate(
                ['user_id' => $student->id, 'course_id' => $firstCourse->id],
                ['start_date' => now(), 'end_date' => now()->addMonth(), 'status' => 'active']
            );
        }
    }
}
