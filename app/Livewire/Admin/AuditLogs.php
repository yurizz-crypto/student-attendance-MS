<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\User;
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

    public function exportCsv()
    {
        $logs = AuditLog::with('user')
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
            ->get();

        $filename = 'audit-logs-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID', 'Date', 'User', 'Role', 'Action', 'Module/Type', 'Description', 'IP Address', 'Status',
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->full_name : 'System',
                    $log->user ? $log->user->role : 'N/A',
                    $log->action,
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->description,
                    $log->ip_address,
                    $log->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.admin.audit-logs', [
            'logs' => $this->getAuditLogs(),
            'users' => User::orderBy('first_name')->get(),
            'actions' => ['created', 'updated', 'deleted', 'viewed', 'login', 'logout', 'excuse_submitted', 'excuse_processed'],
        ]);
    }
}
