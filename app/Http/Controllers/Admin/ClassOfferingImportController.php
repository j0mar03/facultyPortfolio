<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClassOfferingImportController extends Controller
{
    public function showForm()
    {
        return view('admin.import.class-offerings');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $csv = array_map('str_getcsv', file($path));
        $header = array_shift($csv); // Remove header row

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($csv as $lineNumber => $row) {
                $line = $lineNumber + 2; // +2 because we removed header and arrays are 0-indexed

                if (count($row) < 7) {
                    $errors[] = "Line {$line}: Not enough columns";
                    continue;
                }

                list($courseCode, $subjectCode, $subjectTitle, $yearLevel, $term, $academicYear, $section, $facultyEmail) = $row;

                // Find or create course
                $course = Course::where('code', trim($courseCode))->first();
                if (!$course) {
                    $errors[] = "Line {$line}: Course '{$courseCode}' not found";
                    continue;
                }

                // Find or create subject
                $subject = Subject::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'code' => trim($subjectCode),
                        'year_level' => (int)$yearLevel,
                        'term' => (int)$term,
                    ],
                    [
                        'title' => trim($subjectTitle),
                    ]
                );

                // Find faculty by email
                $faculty = null;
                if (!empty(trim($facultyEmail))) {
                    $faculty = User::where('email', trim($facultyEmail))->first();
                    if (!$faculty) {
                        $errors[] = "Line {$line}: Faculty email '{$facultyEmail}' not found";
                    }
                }

                // Create or update class offering
                ClassOffering::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'academic_year' => trim($academicYear),
                        'term' => (int)$term,
                        'section' => trim($section),
                    ],
                    [
                        'faculty_id' => $faculty?->id,
                    ]
                );

                $imported++;
            }

            DB::commit();

            return redirect()->back()->with([
                'success' => "Successfully imported {$imported} class offerings" . (count($errors) > 0 ? " with " . count($errors) . " errors" : ""),
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['import' => 'Import failed: ' . $e->getMessage()]);
        }
    }
}
