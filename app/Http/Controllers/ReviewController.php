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

        $portfolios = Portfolio::with(['user', 'classOffering.subject.course', 'reviews.reviewer'])
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'asc')
            ->paginate(20);

        return view('chair.review-queue', compact('portfolios'));
    }

    public function show(Portfolio $portfolio): View
    {
        // Only chairs and admins can review
        abort_unless(in_array(Auth::user()->role, ['chair', 'admin', 'auditor']), 403);

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
