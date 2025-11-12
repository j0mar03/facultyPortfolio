<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $courses = Course::withCount('subjects')->orderBy('code')->paginate(20);

        return view('admin.courses.index', compact('courses'));
    }
}
