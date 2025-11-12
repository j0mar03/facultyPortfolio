<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use ZipArchive;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(in_array(Auth::user()->role, ['admin', 'auditor']), 403);

        $query = Portfolio::with(['user', 'classOffering.subject.course', 'items']);

        // Apply filters
        if ($request->filled('course_id')) {
            $query->whereHas('classOffering.subject.course', function ($q) use ($request) {
                $q->where('id', $request->course_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_year')) {
            $query->whereHas('classOffering', function ($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        if ($request->filled('faculty_id')) {
            $query->where('user_id', $request->faculty_id);
        }

        $portfolios = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $courses = Course::orderBy('code')->get();
        $faculty = User::where('role', 'faculty')->orderBy('name')->get();
        $academicYears = Portfolio::join('class_offerings', 'portfolios.class_offering_id', '=', 'class_offerings.id')
            ->distinct()
            ->pluck('class_offerings.academic_year')
            ->sort()
            ->values();

        // Calculate statistics
        $stats = [
            'total' => $portfolios->total(),
            'draft' => Portfolio::where('status', 'draft')->count(),
            'submitted' => Portfolio::where('status', 'submitted')->count(),
            'approved' => Portfolio::where('status', 'approved')->count(),
            'rejected' => Portfolio::where('status', 'rejected')->count(),
        ];

        return view('admin.reports.index', compact('portfolios', 'courses', 'faculty', 'academicYears', 'stats'));
    }

    public function export(Portfolio $portfolio)
    {
        abort_unless(in_array(Auth::user()->role, ['admin', 'auditor', 'chair']), 403);

        $portfolio->load(['user', 'classOffering.subject.course', 'items']);

        // Create temporary directory
        $tempDir = storage_path('app/temp/export_' . $portfolio->id . '_' . time());
        @mkdir($tempDir, 0755, true);

        // Copy all files to temp directory
        foreach ($portfolio->items as $item) {
            if (Storage::disk('local')->exists($item->file_path)) {
                $sourcePath = Storage::disk('local')->path($item->file_path);
                $fileName = ($item->metadata_json['original_name'] ?? basename($item->file_path));
                $destPath = $tempDir . '/' . $item->type . '_' . $fileName;
                @copy($sourcePath, $destPath);
            }
        }

        // Create README with portfolio info
        $readme = "Portfolio Export\n";
        $readme .= "================\n\n";
        $readme .= "Faculty: {$portfolio->user->name}\n";
        $readme .= "Email: {$portfolio->user->email}\n";
        $readme .= "Course: {$portfolio->classOffering->subject->course->code}\n";
        $readme .= "Subject: {$portfolio->classOffering->subject->code} - {$portfolio->classOffering->subject->title}\n";
        $readme .= "Academic Year: {$portfolio->classOffering->academic_year}\n";
        $readme .= "Term: {$portfolio->classOffering->term}\n";
        $readme .= "Section: {$portfolio->classOffering->section}\n";
        $readme .= "Status: {$portfolio->status}\n";
        $readme .= "Submitted: " . ($portfolio->submitted_at ? $portfolio->submitted_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
        $readme .= "Approved: " . ($portfolio->approved_at ? $portfolio->approved_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
        file_put_contents($tempDir . '/README.txt', $readme);

        // Create ZIP
        $zipFileName = "portfolio_{$portfolio->id}_{$portfolio->user->name}_{$portfolio->classOffering->subject->code}.zip";
        $zipPath = storage_path('app/temp/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
        }

        // Clean up temp directory
        $this->deleteDirectory($tempDir);

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}
