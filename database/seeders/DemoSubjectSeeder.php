<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get courses
        $dict = Course::where('code', 'DICT')->first();
        $dcpet = Course::where('code', 'DCPET')->first();
        $deet = Course::where('code', 'DEET')->first();

        // DICT Subjects
        if ($dict) {
            $dictSubjects = [
                // Year 1
                ['code' => 'CS101', 'title' => 'Introduction to Programming', 'year_level' => 1, 'term' => 1],
                ['code' => 'CS102', 'title' => 'Computer Fundamentals', 'year_level' => 1, 'term' => 1],
                ['code' => 'MATH101', 'title' => 'College Algebra', 'year_level' => 1, 'term' => 1],
                ['code' => 'CS103', 'title' => 'Data Structures and Algorithms', 'year_level' => 1, 'term' => 2],
                ['code' => 'CS104', 'title' => 'Web Development Fundamentals', 'year_level' => 1, 'term' => 2],

                // Year 2
                ['code' => 'CS201', 'title' => 'Database Management Systems', 'year_level' => 2, 'term' => 1],
                ['code' => 'CS202', 'title' => 'Object-Oriented Programming', 'year_level' => 2, 'term' => 1],
                ['code' => 'CS203', 'title' => 'Network Fundamentals', 'year_level' => 2, 'term' => 2],
                ['code' => 'CS204', 'title' => 'Software Engineering', 'year_level' => 2, 'term' => 2],

                // Year 3
                ['code' => 'CS301', 'title' => 'System Administration', 'year_level' => 3, 'term' => 1],
                ['code' => 'CS302', 'title' => 'Mobile Application Development', 'year_level' => 3, 'term' => 1],
                ['code' => 'CS303', 'title' => 'Capstone Project', 'year_level' => 3, 'term' => 2],
            ];

            foreach ($dictSubjects as $subject) {
                Subject::updateOrCreate(
                    [
                        'course_id' => $dict->id,
                        'code' => $subject['code'],
                        'year_level' => $subject['year_level'],
                        'term' => $subject['term'],
                    ],
                    [
                        'title' => $subject['title'],
                    ]
                );
            }
        }

        // DCPET Subjects
        if ($dcpet) {
            $dcpetSubjects = [
                // Year 1
                ['code' => 'CE101', 'title' => 'Digital Logic Design', 'year_level' => 1, 'term' => 1],
                ['code' => 'CE102', 'title' => 'Computer Programming I', 'year_level' => 1, 'term' => 1],
                ['code' => 'MATH101', 'title' => 'Engineering Mathematics I', 'year_level' => 1, 'term' => 1],
                ['code' => 'CE103', 'title' => 'Circuit Analysis', 'year_level' => 1, 'term' => 2],

                // Year 2
                ['code' => 'CE201', 'title' => 'Microprocessor Systems', 'year_level' => 2, 'term' => 1],
                ['code' => 'CE202', 'title' => 'Embedded Systems', 'year_level' => 2, 'term' => 2],
            ];

            foreach ($dcpetSubjects as $subject) {
                Subject::updateOrCreate(
                    [
                        'course_id' => $dcpet->id,
                        'code' => $subject['code'],
                        'year_level' => $subject['year_level'],
                        'term' => $subject['term'],
                    ],
                    [
                        'title' => $subject['title'],
                    ]
                );
            }
        }

        // DEET Subjects
        if ($deet) {
            $deetSubjects = [
                // Year 1
                ['code' => 'EE101', 'title' => 'Basic Electronics', 'year_level' => 1, 'term' => 1],
                ['code' => 'EE102', 'title' => 'Electrical Circuits I', 'year_level' => 1, 'term' => 1],
                ['code' => 'EE103', 'title' => 'Power Systems Fundamentals', 'year_level' => 1, 'term' => 2],

                // Year 2
                ['code' => 'EE201', 'title' => 'Industrial Electronics', 'year_level' => 2, 'term' => 1],
                ['code' => 'EE202', 'title' => 'Electrical Machines', 'year_level' => 2, 'term' => 2],
            ];

            foreach ($deetSubjects as $subject) {
                Subject::updateOrCreate(
                    [
                        'course_id' => $deet->id,
                        'code' => $subject['code'],
                        'year_level' => $subject['year_level'],
                        'term' => $subject['term'],
                    ],
                    [
                        'title' => $subject['title'],
                    ]
                );
            }
        }
    }
}
