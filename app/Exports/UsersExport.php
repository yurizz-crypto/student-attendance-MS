<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /** @param array<string, mixed> $filters */
    public function __construct(protected array $filters) {}

    public function title(): string
    {
        return 'Users';
    }

    public function query()
    {
        return User::query()
            ->when($this->filters['showTrashed'] ?? false, fn ($q) => $q->onlyTrashed())
            ->when($this->filters['searchTerm'] ?? '', function ($q) {
                $s = $this->filters['searchTerm'];

                return $q->where(function ($inner) use ($s) {
                    $inner->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%")
                        ->orWhere('identity_id', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%");
                });
            })
            ->when($this->filters['filterRole'] ?? '', fn ($q) => $q->where('role', $this->filters['filterRole']))
            ->when($this->filters['filterStatus'] ?? '', fn ($q) => $q->where('status', $this->filters['filterStatus']))
            ->when($this->filters['dateFrom'] ?? '', fn ($q) => $q->whereDate('created_at', '>=', $this->filters['dateFrom']))
            ->when($this->filters['dateTo'] ?? '', fn ($q) => $q->whereDate('created_at', '<=', $this->filters['dateTo']))
            ->when(! empty($this->filters['selectedIds']), fn ($q) => $q->whereIn('id', $this->filters['selectedIds']))
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return ['ID', 'First Name', 'Middle Name', 'Last Name', 'Identity ID', 'Email', 'Role', 'Status', 'Created At'];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->first_name,
            $user->middle_name ?? '',
            $user->last_name,
            $user->identity_id,
            $user->email,
            ucfirst($user->role),
            ucfirst($user->status ?? ''),
            $user->created_at?->format('Y-m-d H:i:s') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
