<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\ClassOffering;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Auth::user()->role === 'chair', 403);

        $chair = Auth::user();

        // Get all courses this chair manages
        $managedCourses = $chair->managedCourses;

        // For backward compatibility, if no managed courses, use the old course_id
        if ($managedCourses->isEmpty() && $chair->course_id) {
            $managedCourses = \App\Models\Course::where('id', $chair->course_id)->get();
        }

        abort_unless($managedCourses->isNotEmpty(), 403, 'No course assigned to this chair');

        // Get selected course or default to first managed course
        $selectedCourseId = $request->get('course_id', $managedCourses->first()->id);
        $selectedCourse = $managedCourses->firstWhere('id', $selectedCourseId);

        abort_unless($selectedCourse, 403, 'Invalid course selection');

        // Get selected academic year or default to current
        $selectedYear = $request->get('academic_year', date('Y') . '-' . (date('Y') + 1));

        // Get all available academic years from class offerings
        $availableYears = ClassOffering::whereHas('subject', function ($query) use ($selectedCourse) {
                $query->where('course_id', $selectedCourse->id);
            })
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->values();

        // If no years exist, add current year as default
        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y') . '-' . (date('Y') + 1)]);
        }

        $subjects = Subject::where('course_id', $selectedCourse->id)
            ->with(['classOfferings' => function ($query) use ($selectedYear) {
                $query->where('academic_year', $selectedYear)
                      ->with(['faculty', 'portfolio.items']);
            }])
            ->orderBy('year_level')
            ->orderBy('term')
            ->orderBy('code')
            ->get()
            ->groupBy(function ($subject) {
                return "Year {$subject->year_level} - Term {$subject->term}";
            });

        return view('chair.subjects.index', compact('subjects', 'chair', 'managedCourses', 'selectedCourse', 'availableYears', 'selectedYear'));
    }

    public function show(Subject $subject): View
    {
        $user = Auth::user();
        abort_unless($user->role === 'chair', 403);

        // Check if chair manages this course (support many-to-many relationship)
        $managesCourse = $user->managedCourses->contains($subject->course_id);
        if (!$managesCourse && $user->course_id) {
            // Fallback to old single course_id field
            $managesCourse = $user->course_id === $subject->course_id;
        }
        abort_unless($managesCourse, 403, 'You do not manage this course');

        $subject->load('classOfferings.faculty');

        // Get faculty and chairs who can be assigned (chairs can also teach)
        $availableFaculty = User::whereIn('role', ['faculty', 'chair'])->orderBy('name')->get();

        return view('chair.subjects.show', compact('subject', 'availableFaculty'));
    }

    public function assignFaculty(Request $request, Subject $subject): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->role === 'chair', 403);

        // Check if chair manages this course (support many-to-many relationship)
        $managesCourse = $user->managedCourses->contains($subject->course_id);
        if (!$managesCourse && $user->course_id) {
            // Fallback to old single course_id field
            $managesCourse = $user->course_id === $subject->course_id;
        }
        abort_unless($managesCourse, 403, 'You do not manage this course');

        $data = $request->validate([
            'faculty_id' => ['required', 'exists:users,id'],
            'academic_year' => ['required', 'string'],
            'term' => ['required', 'integer', 'min:1', 'max:3'],
            'section' => ['required', 'string'],
            'assignment_document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB max
        ]);

        // Verify faculty or chair role (chairs can also teach)
        $faculty = User::findOrFail($data['faculty_id']);
        abort_unless(in_array($faculty->role, ['faculty', 'chair']), 403, 'Selected user must be a faculty member or chair');

        // Find or create the class offering
        $classOffering = ClassOffering::firstOrNew([
            'subject_id' => $subject->id,
            'academic_year' => $data['academic_year'],
            'term' => $data['term'],
            'section' => $data['section'],
        ]);

        // Update faculty assignment
        $classOffering->faculty_id = $data['faculty_id'];

        // Handle file upload
        if ($request->hasFile('assignment_document')) {
            // Delete old document if exists
            if ($classOffering->assignment_document && \Storage::disk('local')->exists($classOffering->assignment_document)) {
                \Storage::disk('local')->delete($classOffering->assignment_document);
            }

            $file = $request->file('assignment_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $assignmentDocPath = $file->storeAs(
                'assignments/' . $subject->id,
                $fileName,
                'local'
            );
            $classOffering->assignment_document = $assignmentDocPath;
        }

        $classOffering->save();

        return back()->with('status', 'Faculty assigned successfully!');
    }

    public function removeAssignment(ClassOffering $classOffering): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->role === 'chair', 403);

        // Check if chair manages this course (support many-to-many relationship)
        $managesCourse = $user->managedCourses->contains($classOffering->subject->course_id);
        if (!$managesCourse && $user->course_id) {
            // Fallback to old single course_id field
            $managesCourse = $user->course_id === $classOffering->subject->course_id;
        }
        abort_unless($managesCourse, 403, 'You do not manage this course');

        $classOffering->delete();

        return back()->with('status', 'Class offering removed successfully!');
    }

    public function downloadAssignment(ClassOffering $classOffering)
    {
        $user = Auth::user();

        // Check if chair manages this course (support many-to-many relationship)
        $managesCourse = false;
        if ($user->role === 'chair') {
            $managesCourse = $user->managedCourses->contains($classOffering->subject->course_id);
            if (!$managesCourse && $user->course_id) {
                // Fallback to old single course_id field
                $managesCourse = $user->course_id === $classOffering->subject->course_id;
            }
        }

        // Allow chair (of the same course), admin, or the assigned faculty to download
        abort_unless(
            $user->role === 'admin' ||
            ($user->role === 'chair' && $managesCourse) ||
            ($user->role === 'faculty' && $classOffering->faculty_id === $user->id),
            403
        );

        abort_unless($classOffering->assignment_document, 404, 'No assignment document found');

        // Use Storage facade to get the correct path (includes 'private' directory for local disk)
        $path = \Storage::disk('local')->path($classOffering->assignment_document);
        abort_unless(file_exists($path), 404, 'File not found');

        return response()->download($path);
    }

    public function uploadAssignment(Request $request, ClassOffering $classOffering): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->role === 'chair', 403);

        // Check if chair manages this course (support many-to-many relationship)
        $managesCourse = $user->managedCourses->contains($classOffering->subject->course_id);
        if (!$managesCourse && $user->course_id) {
            // Fallback to old single course_id field
            $managesCourse = $user->course_id === $classOffering->subject->course_id;
        }
        abort_unless($managesCourse, 403, 'You do not manage this course');

        $data = $request->validate([
            'assignment_document' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB max
        ]);

        // Delete old document if exists
        if ($classOffering->assignment_document && \Storage::disk('local')->exists($classOffering->assignment_document)) {
            \Storage::disk('local')->delete($classOffering->assignment_document);
        }

        // Handle file upload
        $file = $request->file('assignment_document');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $assignmentDocPath = $file->storeAs(
            'assignments/' . $classOffering->subject_id,
            $fileName,
            'local'
        );

        $classOffering->update([
            'assignment_document' => $assignmentDocPath,
        ]);

        return back()->with('status', 'Teaching load document uploaded successfully!');
    }

    public function uploadDocument(Request $request, ClassOffering $classOffering, string $type): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->role === 'chair', 403);

        // Check if chair manages this course (support many-to-many relationship)
        $managesCourse = $user->managedCourses->contains($classOffering->subject->course_id);
        if (!$managesCourse && $user->course_id) {
            // Fallback to old single course_id field
            $managesCourse = $user->course_id === $classOffering->subject->course_id;
        }
        abort_unless($managesCourse, 403, 'You do not manage this course');

        // Validate type parameter
        $allowedTypes = ['im', 'syllabus'];
        abort_unless(in_array($type, $allowedTypes), 400, 'Invalid document type');

        try {
            $data = $request->validate([
                'document' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'], // 5MB max
            ]);

            // Map type to database column
            $columnName = $type === 'im' ? 'instructional_material' : 'syllabus';
            $folderName = $type === 'im' ? 'instructional_materials' : 'syllabi';

            // Delete old document if exists
            if ($classOffering->$columnName && \Storage::disk('local')->exists($classOffering->$columnName)) {
                \Storage::disk('local')->delete($classOffering->$columnName);
            }

            // Handle file upload
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $documentPath = $file->storeAs(
                $folderName . '/' . $classOffering->subject_id,
                $fileName,
                'local'
            );

            if (!$documentPath) {
                return back()->withErrors(['document' => 'Failed to store the file. Please try again.']);
            }

            $classOffering->update([
                $columnName => $documentPath,
            ]);

            $documentLabel = $type === 'im' ? 'Instructional Material' : 'Syllabus';
            return back()->with('status', "{$documentLabel} uploaded successfully!");
        } catch (\Exception $e) {
            \Log::error('Document upload failed', [
                'error' => $e->getMessage(),
                'offering_id' => $classOffering->id,
                'type' => $type
            ]);
            return back()->withErrors(['document' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    public function downloadDocument(ClassOffering $classOffering, string $type)
    {
        $user = Auth::user();

        // Check if chair manages this course (support many-to-many relationship)
        $managesCourse = false;
        if ($user->role === 'chair') {
            $managesCourse = $user->managedCourses->contains($classOffering->subject->course_id);
            if (!$managesCourse && $user->course_id) {
                // Fallback to old single course_id field
                $managesCourse = $user->course_id === $classOffering->subject->course_id;
            }
        }

        // Allow chair (of the same course), admin, or the assigned faculty to download
        abort_unless(
            $user->role === 'admin' ||
            ($user->role === 'chair' && $managesCourse) ||
            ($user->role === 'faculty' && $classOffering->faculty_id === $user->id),
            403
        );

        // Validate type parameter
        $allowedTypes = ['im', 'syllabus'];
        abort_unless(in_array($type, $allowedTypes), 400, 'Invalid document type');

        // Map type to database column
        $columnName = $type === 'im' ? 'instructional_material' : 'syllabus';

        abort_unless($classOffering->$columnName, 404, 'No document found');

        // Use Storage facade to get the correct path
        $path = \Storage::disk('local')->path($classOffering->$columnName);
        abort_unless(file_exists($path), 404, 'File not found');

        return response()->download($path);
    }
}
