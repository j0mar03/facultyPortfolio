<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        // Only chairs and admins can access review queue
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin']), 403);

        $user = Auth::user();
        
        // Build query for portfolios
        $query = Portfolio::with(['user', 'classOffering.subject.course', 'reviews.reviewer'])
            ->where('status', 'submitted');

        // If user is a chair (not admin), filter by their managed courses
        if ($user->role === 'chair') {
            $managedCourseIds = $user->managedCourses->pluck('id');
            
            // Backward compatibility: if no managed courses, use old course_id
            if ($managedCourseIds->isEmpty() && $user->course_id) {
                $managedCourseIds = collect([$user->course_id]);
            }
            
            // Filter portfolios to only show those from managed courses
            $query->whereHas('classOffering.subject', function ($q) use ($managedCourseIds) {
                $q->whereIn('course_id', $managedCourseIds);
            });
        }
        
        $portfolios = $query->orderBy('submitted_at', 'asc')->paginate(20);

        return view('chair.review-queue', compact('portfolios'));
    }

    public function show(Portfolio $portfolio): View
    {
        // Only chairs and admins can review
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin', 'auditor']), 403);

        $user = Auth::user();
        
        // If user is a chair (not admin/auditor), verify they manage this portfolio's course
        if ($user->role === 'chair') {
            $managedCourseIds = $user->managedCourses->pluck('id');
            
            // Backward compatibility
            if ($managedCourseIds->isEmpty() && $user->course_id) {
                $managedCourseIds = collect([$user->course_id]);
            }
            
            $portfolio->load('classOffering.subject');
            $portfolioCourseId = $portfolio->classOffering->subject->course_id;
            
            abort_unless(
                $managedCourseIds->contains($portfolioCourseId),
                403,
                'You do not have permission to review this portfolio.'
            );
        }

        $portfolio->load([
            'user',
            'classOffering.subject.course',
            'items',
            'reviews.reviewer'
        ]);

        return view('chair.review-portfolio', compact('portfolio'));
    }

    public function decision(Request $request, Portfolio $portfolio): RedirectResponse
    {
        // Only chairs can make decisions
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin']), 403);
        abort_if($portfolio->status !== 'submitted', 403, 'Portfolio is not pending review');

        $user = Auth::user();
        
        // If user is a chair (not admin), verify they manage this portfolio's course
        if ($user->role === 'chair') {
            $managedCourseIds = $user->managedCourses->pluck('id');
            
            // Backward compatibility
            if ($managedCourseIds->isEmpty() && $user->course_id) {
                $managedCourseIds = collect([$user->course_id]);
            }
            
            $portfolio->load('classOffering.subject');
            $portfolioCourseId = $portfolio->classOffering->subject->course_id;
            
            abort_unless(
                $managedCourseIds->contains($portfolioCourseId),
                403,
                'You do not have permission to make decisions on this portfolio.'
            );
        }

        $data = $request->validate([
            'decision' => ['required', 'in:approved,rejected,changes_requested'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Create review record
        Review::create([
            'portfolio_id' => $portfolio->id,
            'reviewer_id' => Auth::id(),
            'decision' => $data['decision'],
            'remarks' => $data['remarks'],
        ]);

        // Update portfolio status
        $newStatus = $data['decision'] === 'approved' ? 'approved' : 'rejected';
        $updateData = ['status' => $newStatus];

        if ($data['decision'] === 'approved') {
            $updateData['approved_at'] = now();
        }

        $portfolio->update($updateData);

        // TODO: Send notification to faculty

        $message = $data['decision'] === 'approved'
            ? 'Portfolio approved successfully!'
            : 'Portfolio rejected with remarks.';

        return redirect()
            ->route('reviews.index')
            ->with('status', $message);
    }
}
