<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $users = User::orderBy('role')->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $courses = Course::orderBy('code')->get();

        return view('admin.users.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,chair,faculty,auditor'],
            'managed_course_ids' => ['array'],
            'managed_course_ids.*' => ['integer', 'exists:courses,id'],
        ]);

        $data['password'] = Hash::make($data['password']);

        /** @var \App\Models\User $user */
        $user = User::create($data);

        // If chair, attach managed courses (and set primary course_id for backward compatibility)
        if ($user->role === 'chair') {
            $courseIds = $data['managed_course_ids'] ?? [];
            if (!empty($courseIds)) {
                $user->managedCourses()->sync($courseIds);
                // Set primary course_id as the first managed course (for older code paths)
                $user->course_id = $courseIds[0];
                $user->save();
            }
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully!');
    }

    public function edit(User $user): View
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $courses = Course::orderBy('code')->get();
        $managedCourseIds = $user->managedCourses()->pluck('courses.id')->toArray();

        return view('admin.users.edit', compact('user', 'courses', 'managedCourseIds'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,chair,faculty,auditor'],
            'password' => ['nullable', 'string', 'min:8'],
            'managed_course_ids' => ['array'],
            'managed_course_ids.*' => ['integer', 'exists:courses,id'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Update managed courses if user is chair
        if ($user->role === 'chair') {
            $courseIds = $data['managed_course_ids'] ?? [];
            $user->managedCourses()->sync($courseIds);

            // Keep single course_id in sync for backward compatibility
            if (!empty($courseIds)) {
                $user->course_id = $courseIds[0];
            } else {
                $user->course_id = null;
            }
            $user->save();
        } else {
            // Non-chairs should not have managed courses
            $user->managedCourses()->detach();
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated successfully!');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(Auth::user()->role === 'admin', 403);
        abort_if($user->id === Auth::id(), 403, 'Cannot delete your own account');

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted successfully!');
    }
}
