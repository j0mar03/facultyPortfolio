<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use ZipArchive;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        // Only chairs and admins can access
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin']), 403);

        $user = Auth::user();

        // Get all courses this chair manages
        $managedCourses = $user->managedCourses;

        // For backward compatibility, if no managed courses, use the old course_id
        if ($managedCourses->isEmpty() && $user->course_id) {
            $managedCourses = \App\Models\Course::where('id', $user->course_id)->get();
        }

        abort_unless($managedCourses->isNotEmpty(), 403, 'No course assigned to this chair');

        // Get selected course or default to first managed course
        $selectedCourseId = $request->get('course_id', $managedCourses->first()->id);
        $selectedCourse = $managedCourses->firstWhere('id', $selectedCourseId);

        abort_unless($selectedCourse, 403, 'Invalid course selection');

        // Get all available academic years from approved portfolios
        $availableYears = Portfolio::whereHas('classOffering.subject', function ($query) use ($selectedCourse) {
                $query->where('course_id', $selectedCourse->id);
            })
            ->where('status', 'approved')
            ->join('class_offerings', 'portfolios.class_offering_id', '=', 'class_offerings.id')
            ->distinct()
            ->pluck('class_offerings.academic_year')
            ->sort()
            ->values();

        // If no years exist, add current year as default
        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y') . '-' . (date('Y') + 1)]);
        }

        // Select year: use request value if provided and valid; otherwise pick the latest available
        $requestedYear = $request->get('academic_year');
        if ($requestedYear && $availableYears->contains($requestedYear)) {
            $selectedYear = $requestedYear;
        } else {
            // fallback to latest available year
            $selectedYear = $availableYears->last();
        }

        // Get all approved portfolios for the selected course and academic year
        $portfolios = Portfolio::with([
                'user',
                'classOffering.subject.course',
                'classOffering.faculty',
                'items',
                'reviews.reviewer'
            ])
            ->whereHas('classOffering.subject', function ($query) use ($selectedCourse) {
                $query->where('course_id', $selectedCourse->id);
            })
            ->whereHas('classOffering', function ($query) use ($selectedYear) {
                $query->where('academic_year', $selectedYear);
            })
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(20);

        return view('chair.reports.index', compact(
            'portfolios',
            'managedCourses',
            'selectedCourse',
            'availableYears',
            'selectedYear'
        ));
    }

    public function downloadAll(Request $request)
    {
        // Only chairs and admins can download
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin']), 403);

        $user = Auth::user();

        // Get all courses this chair manages
        $managedCourses = $user->managedCourses;

        if ($managedCourses->isEmpty() && $user->course_id) {
            $managedCourses = \App\Models\Course::where('id', $user->course_id)->get();
        }

        abort_unless($managedCourses->isNotEmpty(), 403, 'No course assigned to this chair');

        // Get selected course or default to first managed course
        $selectedCourseId = $request->get('course_id', $managedCourses->first()->id);
        $selectedCourse = $managedCourses->firstWhere('id', $selectedCourseId);

        abort_unless($selectedCourse, 403, 'Invalid course selection');

        // Get selected academic year or default to current
        $selectedYear = $request->get('academic_year', date('Y') . '-' . (date('Y') + 1));

        // Get all approved portfolios for the selected course and academic year
        $portfolios = Portfolio::with([
                'user',
                'classOffering.subject',
                'items'
            ])
            ->whereHas('classOffering.subject', function ($query) use ($selectedCourse) {
                $query->where('course_id', $selectedCourse->id);
            })
            ->whereHas('classOffering', function ($query) use ($selectedYear) {
                $query->where('academic_year', $selectedYear);
            })
            ->where('status', 'approved')
            ->get();

        if ($portfolios->isEmpty()) {
            return back()->with('error', 'No approved portfolios found to download.');
        }

        // Create a temporary ZIP file
        $zipFileName = 'approved_portfolios_' . $selectedCourse->code . '_' . $selectedYear . '_' . date('YmdHis') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Failed to create ZIP file.');
        }

        // Add portfolios to ZIP
        foreach ($portfolios as $portfolio) {
            $facultyName = $portfolio->user->name;
            $subjectCode = $portfolio->classOffering->subject->code;
            $folderName = "{$facultyName}_{$subjectCode}";

            // Add all portfolio items
            foreach ($portfolio->items as $item) {
                if (Storage::disk('local')->exists($item->file_path)) {
                    $filePath = Storage::disk('local')->path($item->file_path);
                    $metadata = $item->metadata_json ?? [];
                    $fileName = $metadata['original_name'] ?? basename($item->file_path);

                    // Add file to ZIP with proper folder structure
                    $zip->addFile($filePath, $folderName . '/' . $item->type . '/' . $fileName);
                }
            }

            // Add class offering documents if they exist
            $offering = $portfolio->classOffering;

            if ($offering->assignment_document && Storage::disk('local')->exists($offering->assignment_document)) {
                $filePath = Storage::disk('local')->path($offering->assignment_document);
                $zip->addFile($filePath, $folderName . '/teaching_load/' . basename($offering->assignment_document));
            }

            if ($offering->instructional_material && Storage::disk('local')->exists($offering->instructional_material)) {
                $filePath = Storage::disk('local')->path($offering->instructional_material);
                $zip->addFile($filePath, $folderName . '/instructional_material/' . basename($offering->instructional_material));
            }

            if ($offering->syllabus && Storage::disk('local')->exists($offering->syllabus)) {
                $filePath = Storage::disk('local')->path($offering->syllabus);
                $zip->addFile($filePath, $folderName . '/syllabus/' . basename($offering->syllabus));
            }
        }

        $zip->close();

        // Download and then delete the temp file
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
