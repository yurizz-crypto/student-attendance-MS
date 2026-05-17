<?php

use App\Livewire\Admin\UserManagement;
use App\Livewire\Faculty\MyClasses;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Livewire\Livewire;

// -----------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------
function makeAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

function makeFaculty(): User
{
    return User::factory()->create(['role' => 'faculty']);
}

function makeStudent(): User
{
    return User::factory()->create(['role' => 'student']);
}

// -----------------------------------------------------------------------
// Soft Delete — Users
// -----------------------------------------------------------------------

it('soft deletes a user and keeps the record in the database', function () {
    $admin = makeAdmin();
    $student = makeStudent();

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('openDeleteModal', $student->id)
        ->call('destroy');

    // Not in default query (soft deleted)
    expect(User::find($student->id))->toBeNull();

    // Still in database
    expect(User::withTrashed()->find($student->id))->not->toBeNull();
    expect(User::withTrashed()->find($student->id)->deleted_at)->not->toBeNull();
});

it('lists only active users by default', function () {
    $admin = makeAdmin();
    $active = makeStudent();
    $deleted = makeStudent();
    $deleted->delete();

    $component = Livewire::actingAs($admin)->test(UserManagement::class);

    $users = $component->viewData('users');
    expect($users->pluck('id')->toArray())->toContain($active->id);
    expect($users->pluck('id')->toArray())->not->toContain($deleted->id);
});

it('shows trashed users when toggle is active', function () {
    $admin = makeAdmin();
    $deleted = makeStudent();
    $deleted->delete();

    $component = Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('toggleTrashed');

    $users = $component->viewData('users');
    expect($users->pluck('id')->toArray())->toContain($deleted->id);
});

it('restores a soft-deleted user', function () {
    $admin = makeAdmin();
    $student = makeStudent();
    $student->delete();

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('showTrashed', true)
        ->call('restoreUser', $student->id);

    expect(User::find($student->id))->not->toBeNull();
    expect(User::find($student->id)->deleted_at)->toBeNull();
});

it('permanently deletes a soft-deleted user', function () {
    $admin = makeAdmin();
    $student = makeStudent();
    $student->delete();

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->set('showTrashed', true)
        ->call('forceDeleteUser', $student->id);

    expect(User::withTrashed()->find($student->id))->toBeNull();
});

it('prevents deleting own account', function () {
    $admin = makeAdmin();

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('openDeleteModal', $admin->id)
        ->call('destroy');

    // Admin still exists
    expect(User::find($admin->id))->not->toBeNull();
});

// -----------------------------------------------------------------------
// Soft Delete — Class Sections
// -----------------------------------------------------------------------

it('soft deletes a class and keeps it in the database', function () {
    $faculty = makeFaculty();

    $semester = Semester::create(['name' => 'Test Sem', 'faculty_id' => $faculty->id, 'is_active' => true]);
    $subject = Subject::create(['name' => 'Math', 'code' => 'MTH-001']);
    $class = ClassSection::create([
        'subject_id' => $subject->id,
        'faculty_id' => $faculty->id,
        'semester_id' => $semester->id,
        'name' => 'Section A',
        'schedule_details' => 'MWF 8am',
    ]);

    Livewire::actingAs($faculty)
        ->test(MyClasses::class)
        ->call('openDeleteModal', $class->id)
        ->call('delete');

    expect(ClassSection::find($class->id))->toBeNull();
    expect(ClassSection::withTrashed()->find($class->id))->not->toBeNull();
});

// -----------------------------------------------------------------------
// Cascade Warning Counts
// -----------------------------------------------------------------------

it('shows cascade info with enrollment count when opening delete modal', function () {
    $admin = makeAdmin();
    $student = makeStudent();

    $faculty = makeFaculty();
    $semester = Semester::create(['name' => 'Sem1', 'faculty_id' => $faculty->id, 'is_active' => true]);
    $subject = Subject::create(['name' => 'CS101', 'code' => 'CS-101']);
    $class = ClassSection::create([
        'subject_id' => $subject->id,
        'faculty_id' => $faculty->id,
        'semester_id' => $semester->id,
        'name' => 'CS101-A',
        'schedule_details' => 'TTh 9am',
    ]);

    Enrollment::create(['class_section_id' => $class->id, 'student_id' => $student->id, 'status' => 'active']);

    $component = Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('openDeleteModal', $student->id);

    expect($component->get('cascadeInfo')['enrollments'])->toBe(1);
});
