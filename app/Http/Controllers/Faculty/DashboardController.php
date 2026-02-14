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
        $classOfferings = ClassOffering::where('faculty_id', $user->id)
            ->with(['subject.course', 'portfolio.items', 'portfolio.classOffering'])
            ->get();

        // Calculate statistics
        $totalOfferings = $classOfferings->count();
        $portfoliosCreated = $classOfferings->filter(fn($o) => $o->portfolio)->count();
        $portfoliosSubmitted = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'submitted')->count();
        $portfoliosApproved = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'approved')->count();
        $portfoliosRejected = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'rejected')->count();
        $portfoliosDraft = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'draft')->count();

        $totalRequiredDocs = count(config('portfolio.required_items'));

        $documentStats = [];
        foreach ($classOfferings as $offering) {
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

        // Average completion percentage
        $avgCompletion = 0;
        if (count($documentStats) > 0) {
            $avgCompletion = collect($documentStats)->avg('percentage');
        }

        return view('faculty.dashboard', compact(
            'totalOfferings',
            'portfoliosCreated',
            'portfoliosSubmitted',
            'portfoliosApproved',
            'portfoliosRejected',
            'portfoliosDraft',
            'documentStats',
            'avgCompletion',
            'totalRequiredDocs'
        ));
    }
}
