<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Auth::user()->role === 'chair', 403);

        $chair = Auth::user();

        // Get all courses this chair manages
        $managedCourses = $chair->managedCourses;

        // For backward compatibility
        if ($managedCourses->isEmpty() && $chair->course_id) {
            $managedCourses = \App\Models\Course::where('id', $chair->course_id)->get();
        }

        abort_unless($managedCourses->isNotEmpty(), 403, 'No course assigned to this chair');

        // Get selected course or default to first
        $selectedCourseId = $request->get('course_id', $managedCourses->first()->id);
        $selectedCourse = $managedCourses->firstWhere('id', $selectedCourseId);

        abort_unless($selectedCourse, 403, 'Invalid course selection');

        // Get all subjects for this course
        $totalSubjects = Subject::where('course_id', $selectedCourse->id)->count();

        // Get all class offerings for this course
        $classOfferings = ClassOffering::whereHas('subject', function($query) use ($selectedCourse) {
            $query->where('course_id', $selectedCourse->id);
        })->with(['subject', 'faculty', 'portfolio.items'])->get();

        $totalOfferings = $classOfferings->count();

        // Portfolio statistics
        $portfoliosCreated = $classOfferings->filter(fn($o) => $o->portfolio)->count();
        $portfoliosSubmitted = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'submitted')->count();
        $portfoliosApproved = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'approved')->count();
        $portfoliosRejected = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'rejected')->count();
        $portfoliosDraft = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'draft')->count();
        $portfoliosPending = $totalOfferings - $portfoliosCreated;

        // Document completion statistics per faculty
        $requiredDocuments = config('portfolio.required_items');
        $totalRequiredDocs = count($requiredDocuments);

        $facultyStats = [];
        $facultyOfferings = $classOfferings->groupBy('faculty_id');

        foreach ($facultyOfferings as $facultyId => $offerings) {
            $faculty = $offerings->first()->faculty;
            if (!$faculty) continue;

            $totalDocs = 0;
            $completedDocs = 0;
            $portfolioCount = 0;
            $statuses = [
                'draft' => 0,
                'submitted' => 0,
                'approved' => 0,
                'rejected' => 0,
                'none' => 0,
            ];

            foreach ($offerings as $offering) {
                if ($offering->portfolio) {
                    $portfolioCount++;
                    $uploadedTypes = $offering->portfolio->items->pluck('type')->unique()->count();
                    $completedDocs += $uploadedTypes;
                    $totalDocs += $totalRequiredDocs;
                    $statuses[$offering->portfolio->status]++;
                } else {
                    $statuses['none']++;
                }
            }

            $percentage = $totalDocs > 0 ? ($completedDocs / $totalDocs) * 100 : 0;

            $facultyStats[] = [
                'faculty' => $faculty,
                'offerings_count' => $offerings->count(),
                'portfolio_count' => $portfolioCount,
                'documents_completed' => $completedDocs,
                'documents_total' => $totalDocs,
                'completion_percentage' => $percentage,
                'statuses' => $statuses,
            ];
        }

        // Sort by completion percentage descending
        usort($facultyStats, fn($a, $b) => $b['completion_percentage'] <=> $a['completion_percentage']);

        return view('chair.dashboard', compact(
            'managedCourses',
            'selectedCourse',
            'totalSubjects',
            'totalOfferings',
            'portfoliosCreated',
            'portfoliosSubmitted',
            'portfoliosApproved',
            'portfoliosRejected',
            'portfoliosDraft',
            'portfoliosPending',
            'facultyStats',
            'totalRequiredDocs'
        ));
    }
}
