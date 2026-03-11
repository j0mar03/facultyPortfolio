<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Available filtering options (Years/Terms for this course)
        $availableYears = ClassOffering::whereHas('subject', function($query) use ($selectedCourse) {
            $query->where('course_id', $selectedCourse->id);
        })->distinct()->pluck('academic_year')->sortDesc();

        $selectedYear = $request->get('academic_year', $availableYears->first());

        $availableTerms = ClassOffering::whereHas('subject', function($query) use ($selectedCourse) {
            $query->where('course_id', $selectedCourse->id);
        })->where('academic_year', $selectedYear)
          ->distinct()->pluck('term')->sort();

        $selectedTerm = $request->get('term'); // Optional: filter by all terms in year

        // Get all subjects for this course (with filtered counts)
        $allSubjects = Subject::where('course_id', $selectedCourse->id)->get();
        $totalSubjectsCount = $allSubjects->count();
        $totalRequiredSubjectsCount = $allSubjects->filter->requiresPortfolio()->count();

        // Get all class offerings for this course (with filtering)
        $offeringQuery = ClassOffering::whereHas('subject', function($query) use ($selectedCourse) {
            $query->where('course_id', $selectedCourse->id);
        })->with(['subject', 'faculty', 'portfolio.items', 'portfolio.classOffering']);

        if ($selectedYear) {
            $offeringQuery->where('academic_year', $selectedYear);
        }
        if ($selectedTerm) {
            $offeringQuery->where('term', $selectedTerm);
        }

        $allClassOfferings = $offeringQuery->get();
        
        // Filter class offerings that require a portfolio
        $classOfferings = $allClassOfferings->filter(fn($o) => $o->subject->requiresPortfolio());
        $totalOfferings = $classOfferings->count();
        
        // Non-required offerings for separate reporting if needed
        $excludedOfferingsCount = $allClassOfferings->count() - $totalOfferings;

        // Portfolio statistics (only for required offerings)
        $portfoliosCreated = $classOfferings->filter(fn($o) => $o->portfolio)->count();
        $portfoliosSubmitted = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'submitted')->count();
        $portfoliosApproved = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'approved')->count();
        $portfoliosRejected = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'rejected')->count();
        $portfoliosDraft = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'draft')->count();
        $portfoliosPending = $totalOfferings - $portfoliosCreated;

        // Faculty statistics (only for required offerings)
        $facultyStats = [];
        $facultyOfferings = $classOfferings->groupBy('faculty_id');
        
        $totalFacultyCount = $facultyOfferings->keys()->filter(fn($id) => !empty($id))->count();
        $facultyWithPortfoliosCount = 0;

        foreach ($facultyOfferings as $facultyId => $offerings) {
            $faculty = $offerings->first()->faculty;
            if (!$faculty) continue;

            $totalDocs = 0;
            $completedDocs = 0;
            $portfolioCount = 0;
            $hasAnyPortfolio = false;
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
                    $hasAnyPortfolio = true;
                    $completion = $offering->portfolio->completionStats();
                    $completedDocs += $completion['completed'];
                    $totalDocs += $completion['total'];
                    $statuses[$offering->portfolio->status]++;
                } else {
                    $statuses['none']++;
                }
            }

            if ($hasAnyPortfolio) {
                $facultyWithPortfoliosCount++;
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
                'has_started' => $hasAnyPortfolio,
            ];
        }

        $facultyWithoutPortfoliosCount = $totalFacultyCount - $facultyWithPortfoliosCount;

        // Sort: Not started faculty first, then by completion percentage descending
        usort($facultyStats, function($a, $b) {
            if ($a['has_started'] !== $b['has_started']) {
                return $a['has_started'] <=> $b['has_started']; // False (0) before True (1)
            }
            return $b['completion_percentage'] <=> $a['completion_percentage'];
        });

        return view('chair.dashboard', compact(
            'managedCourses',
            'selectedCourse',
            'availableYears',
            'selectedYear',
            'availableTerms',
            'selectedTerm',
            'totalRequiredSubjectsCount',
            'totalSubjectsCount',
            'totalOfferings',
            'excludedOfferingsCount',
            'portfoliosCreated',
            'portfoliosSubmitted',
            'portfoliosApproved',
            'portfoliosRejected',
            'portfoliosDraft',
            'portfoliosPending',
            'facultyStats',
            'totalFacultyCount',
            'facultyWithPortfoliosCount',
            'facultyWithoutPortfoliosCount'
        ));
    }
}
