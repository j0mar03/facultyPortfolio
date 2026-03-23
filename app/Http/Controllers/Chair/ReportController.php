<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\Course;
use App\Models\ClassOffering;
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

        // For backward compatibility
        if ($managedCourses->isEmpty() && $user->course_id) {
            $managedCourses = Course::where('id', $user->course_id)->get();
        }

        abort_unless($managedCourses->isNotEmpty(), 403, 'No course assigned to this chair');

        // Get selected course
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

        $selectedTerm = $request->get('term');

        // Base query for portfolios in this course and year/term
        $baseQuery = Portfolio::with([
                'user',
                'classOffering.subject.course',
                'classOffering.faculty',
                'items',
                'reviews.reviewer'
            ])
            ->whereHas('classOffering.subject', function ($query) use ($selectedCourse) {
                $query->where('course_id', $selectedCourse->id);
            })
            ->whereHas('classOffering', function ($query) use ($selectedYear, $selectedTerm) {
                $query->where('academic_year', $selectedYear);
                if ($selectedTerm) {
                    $query->where('term', $selectedTerm);
                }
            });

        // 1. Approved Portfolios (Finalized)
        $approvedPortfolios = (clone $baseQuery)->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(15, ['*'], 'approved_page');

        // 2. In Progress Portfolios (Monitoring Mode)
        $inProgressPortfolios = (clone $baseQuery)->whereIn('status', ['draft', 'rejected', 'submitted'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15, ['*'], 'progress_page');

        // Statistics
        $totalApproved = (clone $baseQuery)->where('status', 'approved')->count();
        $totalInProgress = (clone $baseQuery)->whereIn('status', ['draft', 'rejected', 'submitted'])->count();

        return view('chair.reports.index', compact(
            'approvedPortfolios',
            'inProgressPortfolios',
            'totalApproved',
            'totalInProgress',
            'managedCourses',
            'selectedCourse',
            'availableYears',
            'selectedYear',
            'availableTerms',
            'selectedTerm'
        ));
    }

    public function compliance(Request $request): View
    {
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin', 'auditor']), 403);

        $user = Auth::user();
        $managedCourses = $user->managedCourses;
        if ($managedCourses->isEmpty() && $user->course_id) {
            $managedCourses = Course::where('id', $user->course_id)->get();
        }
        abort_unless($managedCourses->isNotEmpty(), 403, 'No course assigned');

        $selectedCourseId = $request->get('course_id', $managedCourses->first()->id);
        $selectedCourse = $managedCourses->firstWhere('id', $selectedCourseId);
        abort_unless($selectedCourse, 403);

        $availableYears = ClassOffering::whereHas('subject', function($query) use ($selectedCourse) {
            $query->where('course_id', $selectedCourse->id);
        })->distinct()->pluck('academic_year')->sortDesc();

        $selectedYear = $request->get('academic_year', $availableYears->first());

        $availableTerms = ClassOffering::whereHas('subject', function($query) use ($selectedCourse) {
            $query->where('course_id', $selectedCourse->id);
        })->where('academic_year', $selectedYear)
          ->distinct()->pluck('term')->sort();

        $selectedTerm = $request->get('term', $availableTerms->first());

        // Get subjects that REQUIRE portfolios for this course and term
        $subjectsQuery = \App\Models\Subject::where('course_id', $selectedCourse->id);
        if ($selectedTerm) {
            $subjectsQuery->where('term', $selectedTerm);
        }

        $allSubjects = $subjectsQuery->orderBy('year_level')->orderBy('code')->get();
        
        // Filter subjects based on requiresPortfolio() method
        $subjects = $allSubjects->filter->requiresPortfolio();

        $subjects->load(['classOfferings' => function ($query) use ($selectedYear, $selectedTerm) {
            $query->where('academic_year', $selectedYear);
            if ($selectedTerm) {
                $query->where('term', $selectedTerm);
            }
            $query->with(['faculty', 'portfolio.items']);
        }]);

        // Calculate Compliance Summary Stats (Before grouping)
        $stats = [
            'approved' => 0,
            'in_progress' => 0,
            'not_started' => 0,
            'total' => $subjects->count()
        ];

        foreach ($subjects as $subject) {
            $hasApproved = false;
            $hasInProgress = false;
            
            foreach ($subject->classOfferings as $offering) {
                if ($offering->portfolio) {
                    if ($offering->portfolio->status === 'approved') {
                        $hasApproved = true;
                    } else {
                        $hasInProgress = true;
                    }
                }
            }

            if ($hasApproved) {
                $stats['approved']++;
            } elseif ($hasInProgress) {
                $stats['in_progress']++;
            } else {
                $stats['not_started']++;
            }
        }

        // Now group subjects for the view
        $groupedSubjects = $subjects->groupBy(function ($subject) {
            return "Year {$subject->year_level} - Term {$subject->term}";
        });

        $requiredItems = config('portfolio.required_items', []);
        $itemTypes = config('portfolio.item_types', []);

        return view('chair.reports.compliance', [
            'subjects' => $groupedSubjects,
            'managedCourses' => $managedCourses,
            'selectedCourse' => $selectedCourse,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'availableTerms' => $availableTerms,
            'selectedTerm' => $selectedTerm,
            'requiredItems' => $requiredItems,
            'itemTypes' => $itemTypes,
            'stats' => $stats
        ]);
    }

    public function activity(Request $request): View
    {
        // Only chairs and admins can access
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin']), 403);

        $user = Auth::user();

        // Get all courses this chair manages
        $managedCourses = $user->managedCourses;
        if ($managedCourses->isEmpty() && $user->course_id) {
            $managedCourses = Course::where('id', $user->course_id)->get();
        }

        abort_unless($managedCourses->isNotEmpty(), 403, 'No course assigned');

        // Get selected course for filtering (optional)
        $selectedCourseId = $request->get('course_id');
        
        // Get available periods for filtering
        $availableYears = ClassOffering::distinct()->pluck('academic_year')->sortDesc();
        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y') . '-' . (date('Y') + 1)]);
        }
        $selectedYear = $request->get('academic_year', $availableYears->first());
        
        $availableTerms = ClassOffering::where('academic_year', $selectedYear)
            ->distinct()->pluck('term')->sort();
        if ($availableTerms->isEmpty()) {
            $availableTerms = collect([1]);
        }
        $selectedTerm = $request->get('term', $availableTerms->first());

        // Use Teaching Load assignments as the source of truth
        // Find all faculty assigned to subjects within the chair's managed courses for this period
        $managedCourseIds = $managedCourses->pluck('id');
        
        $assignedFacultyIds = ClassOffering::where('academic_year', $selectedYear)
            ->where('term', $selectedTerm)
            ->whereHas('subject', function($q) use ($managedCourseIds, $selectedCourseId) {
                if ($selectedCourseId) {
                    $q->where('course_id', $selectedCourseId);
                } else {
                    $q->whereIn('course_id', $managedCourseIds);
                }
            })
            ->whereNotNull('faculty_id')
            ->distinct()
            ->pluck('faculty_id');

        $allFaculty = User::whereIn('id', $assignedFacultyIds)
            ->with(['course', 'classOfferings' => function($q) use ($selectedYear, $selectedTerm, $managedCourseIds, $selectedCourseId) {
                $q->where('academic_year', $selectedYear)
                  ->where('term', $selectedTerm)
                  ->whereHas('subject', function($sq) use ($managedCourseIds, $selectedCourseId) {
                      if ($selectedCourseId) {
                          $sq->where('course_id', $selectedCourseId);
                      } else {
                          $sq->whereIn('course_id', $managedCourseIds);
                      }
                  })
                  ->with(['subject', 'portfolio.items']);
            }])->get();

        $facultyActivity = [];

        foreach ($allFaculty as $faculty) {
            // Only consider offerings that require a portfolio
            $requiredOfferings = $faculty->classOfferings->filter(fn($o) => $o->subject->requiresPortfolio());
            
            // Skip faculty who have no required subjects for THIS specific period
            if ($requiredOfferings->isEmpty()) continue;

            $totalDocs = 0;
            $completedDocs = 0;
            $approvedCount = 0;
            $submittedCount = 0;
            $draftCount = 0;
            $rejectedCount = 0;
            $lastUpdate = null;
            $latestPortfolio = null;

            foreach ($requiredOfferings as $offering) {
                if ($offering->portfolio) {
                    $completion = $offering->portfolio->completionStats();
                    $completedDocs += $completion['completed'];
                    $totalDocs += $completion['total'];
                    
                    if ($offering->portfolio->status === 'approved') $approvedCount++;
                    elseif ($offering->portfolio->status === 'submitted') $submittedCount++;
                    elseif ($offering->portfolio->status === 'rejected') $rejectedCount++;
                    else $draftCount++;

                    if (!$lastUpdate || $offering->portfolio->updated_at > $lastUpdate) {
                        $lastUpdate = $offering->portfolio->updated_at;
                        $latestPortfolio = $offering->portfolio;
                    }
                }
            }

            $percentage = $totalDocs > 0 ? ($completedDocs / $totalDocs) * 100 : 0;
            $portfoliosCreated = $requiredOfferings->filter(fn($o) => $o->portfolio)->count();
            $status = ($approvedCount == $requiredOfferings->count() && $requiredOfferings->count() > 0) ? 'Complete' : 'In Progress';

            $facultyActivity[] = [
                'faculty' => $faculty,
                'total_subjects' => $requiredOfferings->count(),
                'portfolios_created' => $portfoliosCreated,
                'approved_count' => $approvedCount,
                'submitted_count' => $submittedCount,
                'draft_count' => $draftCount,
                'rejected_count' => $rejectedCount,
                'completed_docs' => $completedDocs,
                'total_docs' => $totalDocs,
                'percentage' => $percentage,
                'last_update' => $lastUpdate,
                'latest_portfolio' => $latestPortfolio,
                'status' => $status
            ];
        }

        // Handle Sorting
        $sortBy = $request->get('sort_by', 'last_activity');
        usort($facultyActivity, function($a, $b) use ($sortBy) {
            switch ($sortBy) {
                case 'portfolios':
                    return $b['portfolios_created'] <=> $a['portfolios_created'];
                case 'completion':
                    return $b['percentage'] <=> $a['percentage'];
                case 'name':
                    return strcasecmp($a['faculty']->name, $b['faculty']->name);
                case 'last_activity':
                default:
                    return $b['last_update'] <=> $a['last_update'];
            }
        });

        return view('chair.reports.activity', [
            'facultyActivity' => $facultyActivity,
            'managedCourses' => $managedCourses,
            'selectedCourseId' => $selectedCourseId,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'availableTerms' => $availableTerms,
            'selectedTerm' => $selectedTerm,
            'sortBy' => $sortBy,
        ]);
    }

    public function downloadAll(Request $request)
    {
        // Only chairs and admins can download
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin']), 403);

        $user = Auth::user();

        // Get all courses this chair manages
        $managedCourses = $user->managedCourses;

        if ($managedCourses->isEmpty() && $user->course_id) {
            $managedCourses = Course::where('id', $user->course_id)->get();
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

            // Add instructional material if it exists (only if it's a file, not a Google Drive link)
            if ($offering->instructional_material && !filter_var($offering->instructional_material, FILTER_VALIDATE_URL)) {
                if (Storage::disk('local')->exists($offering->instructional_material)) {
                    $filePath = Storage::disk('local')->path($offering->instructional_material);
                    $zip->addFile($filePath, $folderName . '/instructional_material/' . basename($offering->instructional_material));
                }
            } elseif ($offering->instructional_material && filter_var($offering->instructional_material, FILTER_VALIDATE_URL)) {
                // Add Google Drive link to a text file
                $zip->addFromString($folderName . '/instructional_material/google_drive_link.txt', 'Google Drive Link: ' . $offering->instructional_material);
            }

            // Add syllabus if it exists (only if it's a file, not a Google Drive link)
            if ($offering->syllabus && !filter_var($offering->syllabus, FILTER_VALIDATE_URL)) {
                if (Storage::disk('local')->exists($offering->syllabus)) {
                    $filePath = Storage::disk('local')->path($offering->syllabus);
                    $zip->addFile($filePath, $folderName . '/syllabus/' . basename($offering->syllabus));
                }
            } elseif ($offering->syllabus && filter_var($offering->syllabus, FILTER_VALIDATE_URL)) {
                // Add Google Drive link to a text file
                $zip->addFromString($folderName . '/syllabus/google_drive_link.txt', 'Google Drive Link: ' . $offering->syllabus);
            }
        }

        $zip->close();

        // Download and then delete the temp file
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
