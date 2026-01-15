<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            ['code' => 'DCvET', 'name' => 'Diploma in Civil Engineering Technology'],
            ['code' => 'DCPET', 'name' => 'Diploma in Computer Engineering Technology'],
            ['code' => 'DIT', 'name' => 'Diploma in Information Technology'],
            ['code' => 'DEET', 'name' => 'Diploma in Electrical Engineering Technology'],
            ['code' => 'DECET', 'name' => 'Diploma in Electronics and Communications Eng. Tech.'],
            ['code' => 'DICT', 'name' => 'Diploma in Information and Communication Technology'],
            ['code' => 'DMET', 'name' => 'Diploma in Mechanical Engineering Technology'],
            ['code' => 'DOMT', 'name' => 'Diploma in Office Management Technology'],
            ['code' => 'DRET', 'name' => 'Diploma in Railway Engineering Technology'],
        ];

        foreach ($courses as $course) {
            \DB::table('courses')->updateOrInsert(
                ['code' => $course['code']],
                ['name' => $course['name'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
