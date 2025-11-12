<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        abort_unless(in_array(Auth::user()->role, ['admin', 'auditor']), 403);

        $stats = [
            'total_users' => User::count(),
            'total_faculty' => User::where('role', 'faculty')->count(),
            'total_chairs' => User::where('role', 'chair')->count(),
            'total_courses' => Course::count(),
            'total_portfolios' => Portfolio::count(),
            'portfolios_draft' => Portfolio::where('status', 'draft')->count(),
            'portfolios_submitted' => Portfolio::where('status', 'submitted')->count(),
            'portfolios_approved' => Portfolio::where('status', 'approved')->count(),
            'portfolios_rejected' => Portfolio::where('status', 'rejected')->count(),
        ];

        $recentPortfolios = Portfolio::with(['user', 'classOffering.subject.course'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentPortfolios'));
    }
}
