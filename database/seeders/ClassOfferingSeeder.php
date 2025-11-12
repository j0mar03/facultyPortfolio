<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassOfferingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get faculty users
        $faculty1 = User::where('email', 'faculty1@example.com')->first();

        if (!$faculty1) {
            $this->command->warn('Faculty user not found. Run UserSeeder first.');
            return;
        }

        // Get current academic year
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $academicYear = "$currentYear-$nextYear";

        // Get some DICT subjects
        $dictSubjects = Subject::whereHas('course', function ($query) {
            $query->where('code', 'DICT');
        })->get();

        // Create class offerings for faculty1 (DICT subjects)
        if ($dictSubjects->isNotEmpty()) {
            // Assign Year 1, Term 1 subjects
            $cs101 = $dictSubjects->where('code', 'CS101')->first();
            if ($cs101) {
                ClassOffering::updateOrCreate(
                    [
                        'subject_id' => $cs101->id,
                        'academic_year' => $academicYear,
                        'term' => 1,
                        'section' => '1A',
                    ],
                    [
                        'faculty_id' => $faculty1->id,
                    ]
                );

                ClassOffering::updateOrCreate(
                    [
                        'subject_id' => $cs101->id,
                        'academic_year' => $academicYear,
                        'term' => 1,
                        'section' => '1B',
                    ],
                    [
                        'faculty_id' => $faculty1->id,
                    ]
                );
            }

            $cs102 = $dictSubjects->where('code', 'CS102')->first();
            if ($cs102) {
                ClassOffering::updateOrCreate(
                    [
                        'subject_id' => $cs102->id,
                        'academic_year' => $academicYear,
                        'term' => 1,
                        'section' => '1A',
                    ],
                    [
                        'faculty_id' => $faculty1->id,
                    ]
                );
            }

            // Assign Year 2, Term 1 subjects
            $cs201 = $dictSubjects->where('code', 'CS201')->first();
            if ($cs201) {
                ClassOffering::updateOrCreate(
                    [
                        'subject_id' => $cs201->id,
                        'academic_year' => $academicYear,
                        'term' => 1,
                        'section' => '2A',
                    ],
                    [
                        'faculty_id' => $faculty1->id,
                    ]
                );
            }

            $cs202 = $dictSubjects->where('code', 'CS202')->first();
            if ($cs202) {
                ClassOffering::updateOrCreate(
                    [
                        'subject_id' => $cs202->id,
                        'academic_year' => $academicYear,
                        'term' => 1,
                        'section' => '2A',
                    ],
                    [
                        'faculty_id' => $faculty1->id,
                    ]
                );
            }
        }

        // Get some DCPET subjects for variety
        $dcpetSubjects = Subject::whereHas('course', function ($query) {
            $query->where('code', 'DCPET');
        })->get();

        if ($dcpetSubjects->isNotEmpty()) {
            $cmpe102 = $dcpetSubjects->where('code', 'CMPE 102')->first();
            if ($cmpe102) {
                ClassOffering::updateOrCreate(
                    [
                        'subject_id' => $cmpe102->id,
                        'academic_year' => $academicYear,
                        'term' => 1,
                        'section' => '1A',
                    ],
                    [
                        'faculty_id' => $faculty1->id,
                    ]
                );
            }
        }

        $this->command->info('Class offerings created successfully!');
    }
}
