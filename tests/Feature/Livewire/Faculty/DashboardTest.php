<?php

namespace Tests\Feature\Livewire\Faculty;

use App\Livewire\Faculty\Dashboard;
use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_filters_offerings_by_academic_year_and_term()
    {
        $user = User::factory()->create(['role' => 'faculty']);
        $course = Course::create(['code' => 'TEST', 'name' => 'Test Course']);
        $subject = Subject::create([
            'course_id' => $course->id,
            'code' => 'S1',
            'title' => 'Subject 1',
            'year_level' => 1,
            'term' => 1,
        ]);

        // Create offerings for different years/terms
        ClassOffering::create([
            'subject_id' => $subject->id,
            'faculty_id' => $user->id,
            'academic_year' => '2024-2025',
            'term' => 1,
            'section' => 'SEC-OLD',
        ]);

        ClassOffering::create([
            'subject_id' => $subject->id,
            'faculty_id' => $user->id,
            'academic_year' => '2025-2026',
            'term' => 2,
            'section' => 'SEC-NEW',
        ]);

        $this->actingAs($user);

        // Initially should show the latest (2025-2026, Term 2)
        Livewire::test(Dashboard::class)
            ->assertSee('SEC-NEW')
            ->assertDontSee('SEC-OLD')
            ->set('selectedAY', '2024-2025')
            ->set('selectedTerm', '1')
            ->assertSee('SEC-OLD')
            ->assertDontSee('SEC-NEW');
    }
}
