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
        $portfolios = Portfolio::with(['classOffering.subject.course'])
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

        $portfolio->load(['classOffering.subject.course', 'items']);
        return view('portfolio.show', compact('portfolio'));
    }

    public function submit(Portfolio $portfolio): RedirectResponse
    {
        abort_unless($portfolio->user_id === Auth::id(), 403);
        abort_if($portfolio->status !== 'draft', 403, 'Portfolio already submitted');

        // Check if all required items are uploaded
        $requiredTypes = config('portfolio.required_items');
        $uploadedTypes = $portfolio->items()->pluck('type')->unique()->toArray();
        $missingTypes = array_diff($requiredTypes, $uploadedTypes);

        if (!empty($missingTypes)) {
            $missingLabels = array_map(function ($type) {
                return config("portfolio.item_types.{$type}", $type);
            }, $missingTypes);

            return back()->withErrors([
                'submit' => 'Missing required documents: ' . implode(', ', $missingLabels)
            ]);
        }

        $portfolio->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return back()->with('status', 'Portfolio submitted successfully!');
    }
}


