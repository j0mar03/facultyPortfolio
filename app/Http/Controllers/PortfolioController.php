<?php

namespace App\Http\Controllers;

use App\Models\ClassOffering;
use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $portfolios = Portfolio::with(['classOffering.subject.course', 'items'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('portfolio.index', compact('portfolios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'class_offering_id' => ['required', 'exists:class_offerings,id'],
        ]);

        $offering = ClassOffering::with('portfolio')->findOrFail($data['class_offering_id']);

        abort_unless($offering->faculty_id === Auth::id(), 403);

        $portfolio = $offering->portfolio;
        if (!$portfolio) {
            $portfolio = Portfolio::create([
                'user_id' => Auth::id(),
                'class_offering_id' => $offering->id,
                'status' => 'draft',
            ]);
        }

        return redirect()->route('portfolios.show', $portfolio)->with('status', 'Portfolio ready.');
    }

    public function show(Portfolio $portfolio): View
    {
        abort_unless($portfolio->user_id === Auth::id(), 403);

        $portfolio->load(['classOffering.subject.course', 'items.facultyDocument']);
        return view('portfolio.show', compact('portfolio'));
    }

    public function submit(Portfolio $portfolio): RedirectResponse
    {
        abort_unless($portfolio->user_id === Auth::id(), 403);
        abort_if(!in_array($portfolio->status, ['draft', 'rejected']), 403, 'Portfolio already submitted or approved');

        $portfolio->load(['items', 'classOffering']);
        $completion = $portfolio->completionStats();
        $missingTypes = $completion['missing_types'];

        if (!empty($missingTypes)) {
            $itemTypes = config('portfolio.item_types');
            $missingLabels = array_map(function ($type) use ($itemTypes) {
                return $itemTypes[$type] ?? $type;
            }, $missingTypes);

            return back()->withErrors([
                'submit' => 'Missing required documents: ' . implode(', ', $missingLabels) . '. Please upload all required documents before submitting.'
            ]);
        }

        $portfolio->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $message = $portfolio->wasChanged('status') && $portfolio->getOriginal('status') === 'rejected'
            ? 'Portfolio resubmitted successfully!'
            : 'Portfolio submitted successfully!';

        return back()->with('status', $message);
    }
}

