<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class Add2025_2026Seeder extends Seeder
{
    /**
     * SAFELY add 2025-2026 academic year class offerings for all courses.
     * This uses firstOrCreate to ensure existing records are NOT modified.
     * Safe to run on production - will only create missing 2025-2026 entries.
     */
    public function run(): void
    {
        // Get all courses
        $courses = Course::whereIn('code', ['DEET', 'DMET', 'DIT', 'DOMT', 'DCPET', 'DECET'])->get();

        if ($courses->isEmpty()) {
            $this->command->warn('No courses found!');
            return;
        }

        $academicYear = '2025-2026';

        foreach ($courses as $course) {
            $subjects = Subject::where('course_id', $course->id)
                ->orderBy('year_level')
                ->orderBy('term')
                ->get();

            if ($subjects->isEmpty()) {
                $this->command->warn("No subjects found for {$course->code}");
                continue;
            }

            $this->command->info("Adding {$academicYear} class offerings for {$course->code} ({$subjects->count()} subjects)...");
            
            $created = 0;
            $skipped = 0;

            foreach ($subjects as $subject) {
                // Only create for term 1 and 2 (skip summer term 3)
                if ($subject->term <= 2) {
                    // Use firstOrCreate - will NOT modify if already exists
                    $offering = ClassOffering::firstOrCreate(
                        [
                            'subject_id' => $subject->id,
                            'academic_year' => $academicYear,
                            'term' => $subject->term,
                            'section' => '1',
                        ],
                        [
                            'faculty_id' => null, // No faculty assigned by default
                        ]
                    );

                    if ($offering->wasRecentlyCreated) {
                        $created++;
                    } else {
                        $skipped++;
                    }
                }
            }

            $this->command->info("{$course->code} {$academicYear}: Created {$created}, Skipped {$skipped} existing");
        }

        $this->command->info('Done! All 2025-2026 class offerings added (existing data preserved).');
    }
}
