<?php

namespace App\Livewire\Faculty;

use App\Models\ClassOffering;
use App\Models\Reminder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $selectedAY = '';
    public $selectedTerm = '';

    public function dismissReminder($id)
    {
        $reminder = Reminder::where('recipient_id', Auth::id())->find($id);
        if ($reminder) {
            $reminder->update(['read_at' => now()]);
        }
    }

    public function mount()
    {
        // Default to the latest academic year and term assigned to this faculty
        $latest = ClassOffering::where('faculty_id', Auth::id())
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->first();

        if ($latest) {
            $this->selectedAY = $latest->academic_year;
            $this->selectedTerm = (string)$latest->term;
        }
    }

    public function render(): View
    {
        // Get unique AYs and Terms for the dropdowns
        $allOffers = ClassOffering::where('faculty_id', Auth::id())
            ->select('academic_year', 'term')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->get();

        $availableYears = $allOffers->pluck('academic_year')->unique()->values();
        
        // Filter terms based on selected year, but show all if year is empty
        $termQuery = $allOffers;
        if ($this->selectedAY) {
            $termQuery = $allOffers->where('academic_year', $this->selectedAY);
        }
        $availableTerms = $termQuery->pluck('term')->unique()->sort()->values();

        $query = ClassOffering::with(['subject.course', 'portfolio'])
            ->where('faculty_id', Auth::id());

        if ($this->selectedAY) {
            $query->where('academic_year', $this->selectedAY);
        }

        if ($this->selectedTerm !== '') {
            $query->where('term', $this->selectedTerm);
        }

        $offerings = $query->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->orderBy('section')
            ->get();

        $unreadReminder = Reminder::with('classOffering.subject')
            ->where('recipient_id', Auth::id())
            ->whereNull('read_at')
            ->latest()
            ->first();

        return view('livewire.faculty.dashboard', [
            'offerings' => $offerings,
            'availableYears' => $availableYears,
            'availableTerms' => $availableTerms,
            'unreadReminder' => $unreadReminder,
        ]);
    }
}


