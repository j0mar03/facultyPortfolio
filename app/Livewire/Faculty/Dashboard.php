<?php

namespace App\Livewire\Faculty;

use App\Models\ClassOffering;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $offerings = ClassOffering::with(['subject.course', 'portfolio'])
            ->where('faculty_id', Auth::id())
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->orderBy('section')
            ->get();

        return view('livewire.faculty.dashboard', [
            'offerings' => $offerings,
        ]);
    }
}


