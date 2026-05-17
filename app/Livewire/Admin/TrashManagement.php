<?php

namespace App\Livewire\Admin;

use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Models\User;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithPagination;

class TrashManagement extends Component
{
    use WithPagination;

    public string $activeTab = 'users';

    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function getTrashedUsers()
    {
        return User::onlyTrashed()->latest('deleted_at')->paginate(10, pageName: 'usersPage');
    }

    public function getTrashedClasses()
    {
        return ClassSection::onlyTrashed()->with('subject', 'semester', 'faculty')->latest('deleted_at')->paginate(10, pageName: 'classesPage');
    }

    public function getTrashedSessions()
    {
        return AttendanceSession::onlyTrashed()->with('classSection.subject')->latest('deleted_at')->paginate(10, pageName: 'sessionsPage');
    }

    public function restoreUser(int $id): void
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        AuditService::logRestored($user);
        $this->dispatch('swal:success', title: 'User Restored', message: "{$user->first_name} {$user->last_name} has been restored.");
    }

    public function forceDeleteUser(int $id): void
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $name = $user->first_name.' '.$user->last_name;
        AuditService::logForceDeleted($user);
        $user->forceDelete();
        $this->dispatch('swal:success', title: 'Permanently Deleted', message: "User {$name} has been permanently deleted.");
    }

    public function restoreClass(int $id): void
    {
        $class = ClassSection::onlyTrashed()->findOrFail($id);
        $class->restore();
        AuditService::logRestored($class);
        $this->dispatch('swal:success', title: 'Class Restored', message: "Class \"{$class->name}\" has been restored.");
    }

    public function forceDeleteClass(int $id): void
    {
        $class = ClassSection::onlyTrashed()->findOrFail($id);
        $name = $class->name;
        AuditService::logForceDeleted($class);
        $class->forceDelete();
        $this->dispatch('swal:success', title: 'Permanently Deleted', message: "Class \"{$name}\" has been permanently deleted.");
    }

    public function restoreSession(int $id): void
    {
        $session = AttendanceSession::onlyTrashed()->findOrFail($id);
        $session->restore();
        AuditService::logRestored($session);
        $this->dispatch('swal:success', title: 'Session Restored', message: 'Attendance session has been restored.');
    }

    public function forceDeleteSession(int $id): void
    {
        $session = AttendanceSession::onlyTrashed()->findOrFail($id);
        AuditService::logForceDeleted($session);
        $session->forceDelete();
        $this->dispatch('swal:success', title: 'Permanently Deleted', message: 'Session has been permanently deleted.');
    }

    public function render()
    {
        return view('livewire.admin.trash-management', [
            'trashedUsers' => $this->getTrashedUsers(),
            'trashedClasses' => $this->getTrashedClasses(),
            'trashedSessions' => $this->getTrashedSessions(),
        ]);
    }
}
