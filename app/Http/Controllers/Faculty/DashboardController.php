<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Get all class offerings for this faculty
        $classOfferings = ClassOffering::where('faculty_id', $user->id)
            ->with(['subject.course', 'portfolio.items'])
            ->get();

        // Calculate statistics
        $totalOfferings = $classOfferings->count();
        $portfoliosCreated = $classOfferings->filter(fn($o) => $o->portfolio)->count();
        $portfoliosSubmitted = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'submitted')->count();
        $portfoliosApproved = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'approved')->count();
        $portfoliosRejected = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'rejected')->count();
        $portfoliosDraft = $classOfferings->filter(fn($o) => $o->portfolio && $o->portfolio->status === 'draft')->count();

        // Document completion statistics
        $requiredDocuments = config('portfolio.required_items');
        $totalRequiredDocs = count($requiredDocuments);

        $documentStats = [];
        foreach ($classOfferings as $offering) {
            if ($offering->portfolio) {
                $uploadedTypes = $offering->portfolio->items->pluck('type')->unique();
                $completedDocs = $uploadedTypes->count();
                $percentage = $totalRequiredDocs > 0 ? ($completedDocs / $totalRequiredDocs) * 100 : 0;

                $documentStats[] = [
                    'offering' => $offering,
                    'completed' => $completedDocs,
                    'total' => $totalRequiredDocs,
                    'percentage' => $percentage,
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
