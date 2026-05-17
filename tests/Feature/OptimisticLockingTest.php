<?php

use App\Exceptions\StaleRecordException;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Faculty\MyClasses;
use App\Models\ClassSection;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Livewire\Livewire;

// -----------------------------------------------------------------------
// OptimisticLocking Trait Unit Tests
// -----------------------------------------------------------------------

it('does not throw when lock version matches', function () {
    $user = User::factory()->create(['lock_version' => 0]);

    // Should not throw
    expect(fn () => $user->checkLockVersion(0))->not->toThrow(StaleRecordException::class);
});

it('throws StaleRecordException when lock version is stale', function () {
    $user = User::factory()->create(['lock_version' => 0]);

    // Simulate concurrent update by bumping the version in the DB
    User::where('id', $user->id)->update(['lock_version' => 1]);

    // Our in-memory $user still thinks version is 0, but DB has 1
    expect(fn () => $user->checkLockVersion(0))->toThrow(StaleRecordException::class);
});

it('increments lock_version after successful update', function () {
    $user = User::factory()->create(['lock_version' => 0]);
    $user->incrementLockVersion();

    expect(User::find($user->id)->lock_version)->toBe(1);
});

// -----------------------------------------------------------------------
// Optimistic Locking — UserManagement Component
// -----------------------------------------------------------------------

it('updates successfully when lock version is correct', function () {
    $admin = User::factory()->create(['role' => 'admin', 'lock_version' => 0]);
    $target = User::factory()->create(['role' => 'student', 'lock_version' => 0]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('openEditModal', $target->id)
        ->set('firstName', 'UpdatedName')
        ->call('update')
        ->assertHasNoErrors();

    expect(User::find($target->id)->first_name)->toBe('UpdatedName');
    expect(User::find($target->id)->lock_version)->toBe(1);
});

it('rejects update when lock version is stale (concurrent edit)', function () {
    $admin = User::factory()->create(['role' => 'admin', 'lock_version' => 0]);
    $target = User::factory()->create(['role' => 'student', 'lock_version' => 0]);

    // Simulate concurrent change — DB version bumped to 1
    User::where('id', $target->id)->update(['lock_version' => 1]);

    Livewire::actingAs($admin)
        ->test(UserManagement::class)
        ->call('openEditModal', $target->id) // captures version 1 from DB
        ->tap(function ($component) {
            // Force stale version to simulate two tabs
            $component->set('lockVersion', 0);
        })
        ->set('firstName', 'ShouldNotUpdate')
        ->call('update');

    // Name should NOT have changed
    expect(User::find($target->id)->first_name)->not->toBe('ShouldNotUpdate');
    // lockConflict flag should be true
    $component = Livewire::actingAs($admin)
        ->test(UserManagement::class);
    $component->set('lockVersion', 0)
        ->call('openEditModal', $target->id)
        ->tap(fn ($c) => $c->set('lockVersion', 0))
        ->set('firstName', 'AnotherAttempt')
        ->call('update');
    expect($component->get('lockConflict'))->toBeTrue();
});

// -----------------------------------------------------------------------
// ClassSection OptimisticLocking
// -----------------------------------------------------------------------

it('increments class section lock version after update', function () {
    $faculty = User::factory()->create(['role' => 'faculty']);
    $semester = Semester::create(['name' => 'S1', 'faculty_id' => $faculty->id, 'is_active' => true]);
    $subject = Subject::create(['name' => 'Physics', 'code' => 'PHY-001']);
    $class = ClassSection::create([
        'subject_id' => $subject->id,
        'faculty_id' => $faculty->id,
        'semester_id' => $semester->id,
        'name' => 'Original Name',
        'schedule_details' => 'MWF',
        'lock_version' => 0,
    ]);

    Livewire::actingAs($faculty)
        ->test(MyClasses::class)
        ->call('openEditModal', $class->id)
        ->set('className', 'Updated Name')
        ->call('update')
        ->assertHasNoErrors();

    expect(ClassSection::find($class->id)->name)->toBe('Updated Name');
    expect(ClassSection::find($class->id)->lock_version)->toBe(1);
});
