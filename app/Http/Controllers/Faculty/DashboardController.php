<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Get all class offerings for this faculty
        $allClassOfferings = ClassOffering::where('faculty_id', $user->id)
            ->with(['subject.course', 'portfolio.items', 'portfolio.classOffering'])
            ->get();

        // Filter for subjects that REQUIRE portfolios for dashboard summary
        $requiredOfferings = $allClassOfferings->filter(fn($o) => $o->subject->requiresPortfolio());
        $totalOfferings = $requiredOfferings->count();

        // Calculate statistics based on REQUIRED subjects
        $portfoliosCreated = $requiredOfferings->filter(fn($o) => $o->portfolio)->count();
        $portfoliosSubmitted = $requiredOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'submitted')->count();
        $portfoliosApproved = $requiredOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'approved')->count();
        $portfoliosRejected = $requiredOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'rejected')->count();
        $portfoliosDraft = $requiredOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'draft')->count();

        $documentStats = [];
        foreach ($requiredOfferings as $offering) {
            if ($offering->portfolio) {
                $completion = $offering->portfolio->completionStats();
                $documentStats[] = [
                    'offering' => $offering,
                    'completed' => $completion['completed'],
                    'total' => $completion['total'],
                    'percentage' => $completion['percentage'],
                ];
            }
        }

        $avgCompletion = count($documentStats) > 0 ? collect($documentStats)->avg('percentage') : 0;

        return view('faculty.dashboard', compact(
            'totalOfferings',
            'portfoliosCreated',
            'portfoliosSubmitted',
            'portfoliosApproved',
            'portfoliosRejected',
            'portfoliosDraft',
            'documentStats',
            'avgCompletion'
        ));
    }

    public function compliance(): View
    {
        $user = Auth::user();

        // Get all class offerings for this faculty
        $allClassOfferings = ClassOffering::where('faculty_id', $user->id)
            ->with(['subject.course', 'portfolio.items', 'portfolio.classOffering'])
            ->get();

        // Filter for subjects that REQUIRE portfolios
        $requiredOfferings = $allClassOfferings->filter(fn($o) => $o->subject->requiresPortfolio());
        $nonRequiredCount = $allClassOfferings->count() - $requiredOfferings->count();

        $requiredItems = config('portfolio.required_items');
        $itemTypes = config('portfolio.item_types');

        // Overall compliance calculation for the header
        $totalDocs = 0;
        $completedDocs = 0;
        foreach ($requiredOfferings as $offering) {
            $stats = $offering->portfolio ? $offering->portfolio->completionStats() : ['completed' => 0, 'total' => count($requiredItems)];
            $completedDocs += $stats['completed'];
            $totalDocs += $stats['total'];
        }
        $overallCompliance = $totalDocs > 0 ? ($completedDocs / $totalDocs) * 100 : 0;

        return view('faculty.compliance', compact(
            'requiredOfferings',
            'nonRequiredCount',
            'requiredItems',
            'itemTypes',
            'overallCompliance'
        ));
    }
}
