<?php

namespace Database\Seeders;

use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DEETDMET2025_2026Seeder extends Seeder
{
    /**
     * Add 2024-2025 and 2025-2026 academic year class offerings for DEET, DMET, DIT, and DOMT courses.
     * This matches the structure of DCPET and DECET which already have both years.
     */
    public function run(): void
    {
        // Get all courses that need both academic years
        $deet = Course::where('code', 'DEET')->first();
        $dmet = Course::where('code', 'DMET')->first();
        $dit = Course::where('code', 'DIT')->first();
        $domt = Course::where('code', 'DOMT')->first();

        if (!$deet) {
            $this->command->warn('DEET course not found!');
        }

        if (!$dmet) {
            $this->command->warn('DMET course not found!');
        }

        if (!$dit) {
            $this->command->warn('DIT course not found!');
        }

        if (!$domt) {
            $this->command->warn('DOMT course not found!');
        }

        if (!$deet && !$dmet && !$dit && !$domt) {
            return;
        }

        $academicYears = ['2024-2025', '2025-2026'];

        // Process DEET subjects
        if ($deet) {
            $deetSubjects = Subject::where('course_id', $deet->id)
                ->orderBy('year_level')
                ->orderBy('term')
                ->get();

            foreach ($academicYears as $academicYear) {
                $this->command->info("Creating {$academicYear} class offerings for DEET ({$deetSubjects->count()} subjects)...");

                foreach ($deetSubjects as $subject) {
                    // Create a class offering for each subject in term 1 and 2 (skip term 3/summer unless needed)
                    if ($subject->term <= 2) {
                        ClassOffering::updateOrCreate(
                            [
                                'subject_id' => $subject->id,
                                'academic_year' => $academicYear,
                                'term' => $subject->term,
                                'section' => '1', // Default section
                            ],
                            [
                                'faculty_id' => null, // No faculty assigned by default
                            ]
                        );
                    }
                }

                $this->command->info("DEET {$academicYear} class offerings created successfully!");
            }
        }

        // Process DMET subjects
        if ($dmet) {
            $dmetSubjects = Subject::where('course_id', $dmet->id)
                ->orderBy('year_level')
                ->orderBy('term')
                ->get();

            foreach ($academicYears as $academicYear) {
                $this->command->info("Creating {$academicYear} class offerings for DMET ({$dmetSubjects->count()} subjects)...");

                foreach ($dmetSubjects as $subject) {
                    // Create a class offering for each subject in term 1 and 2 (skip term 3/summer unless needed)
                    if ($subject->term <= 2) {
                        ClassOffering::updateOrCreate(
                            [
                                'subject_id' => $subject->id,
                                'academic_year' => $academicYear,
                                'term' => $subject->term,
                                'section' => '1', // Default section
                            ],
                            [
                                'faculty_id' => null, // No faculty assigned by default
                            ]
                        );
                    }
                }

                $this->command->info("DMET {$academicYear} class offerings created successfully!");
            }
        }

        // Process DIT subjects
        if ($dit) {
            $ditSubjects = Subject::where('course_id', $dit->id)
                ->orderBy('year_level')
                ->orderBy('term')
                ->get();

            foreach ($academicYears as $academicYear) {
                $this->command->info("Creating {$academicYear} class offerings for DIT ({$ditSubjects->count()} subjects)...");

                foreach ($ditSubjects as $subject) {
                    // Create a class offering for each subject in term 1 and 2 (skip term 3/summer unless needed)
                    if ($subject->term <= 2) {
                        ClassOffering::updateOrCreate(
                            [
                                'subject_id' => $subject->id,
                                'academic_year' => $academicYear,
                                'term' => $subject->term,
                                'section' => '1', // Default section
                            ],
                            [
                                'faculty_id' => null, // No faculty assigned by default
                            ]
                        );
                    }
                }

                $this->command->info("DIT {$academicYear} class offerings created successfully!");
            }
        }

        // Process DOMT subjects
        if ($domt) {
            $domtSubjects = Subject::where('course_id', $domt->id)
                ->orderBy('year_level')
                ->orderBy('term')
                ->get();

            foreach ($academicYears as $academicYear) {
                $this->command->info("Creating {$academicYear} class offerings for DOMT ({$domtSubjects->count()} subjects)...");

                foreach ($domtSubjects as $subject) {
                    // Create a class offering for each subject in term 1 and 2 (skip term 3/summer unless needed)
                    if ($subject->term <= 2) {
                        ClassOffering::updateOrCreate(
                            [
                                'subject_id' => $subject->id,
                                'academic_year' => $academicYear,
                                'term' => $subject->term,
                                'section' => '1', // Default section
                            ],
                            [
                                'faculty_id' => null, // No faculty assigned by default
                            ]
                        );
                    }
                }

                $this->command->info("DOMT {$academicYear} class offerings created successfully!");
            }
        }

        $this->command->info('All class offerings created for DEET, DMET, DIT, and DOMT (2024-2025 and 2025-2026)!');
    }
}
