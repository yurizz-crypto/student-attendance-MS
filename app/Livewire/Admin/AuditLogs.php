<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $filterAction = '';
    public $filterUser = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterAction()
    {
        $this->resetPage();
    }

    public function updatedFilterUser()
    {
        $this->resetPage();
    }

    public function sort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getAuditLogs()
    {
        return AuditLog::with('user')
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->where('description', 'like', "%{$this->search}%")
                        ->orWhere('model_type', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($q) {
                            $q->where('first_name', 'like', "%{$this->search}%")
                                ->orWhere('last_name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterAction, function ($query) {
                return $query->where('action', $this->filterAction);
            })
            ->when($this->filterUser, function ($query) {
                return $query->where('user_id', $this->filterUser);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.audit-logs', [
            'logs' => $this->getAuditLogs(),
            'users' => \App\Models\User::orderBy('first_name')->get(),
            'actions' => ['created', 'updated', 'deleted', 'viewed', 'login', 'logout'],
        ]);
    }
}
