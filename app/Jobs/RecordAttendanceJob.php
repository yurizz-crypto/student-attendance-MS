<?php

namespace App\Jobs;

use App\Models\ClassSection;
use App\Services\AttendanceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordAttendanceJob implements ShouldQueue
{
    use Queueable;

    protected $classSectionId;

    protected $date;

    protected $studentData;

    /**
     * Create a new job instance.
     */
    public function __construct(int $classSectionId, string $date, array $studentData)
    {
        $this->classSectionId = $classSectionId;
        $this->date = $date;
        $this->studentData = $studentData;
    }

    /**
     * Execute the job.
     */
    public function handle(AttendanceService $attendanceService): void
    {
        $classSection = ClassSection::findOrFail($this->classSectionId);

        $attendanceService->recordBulkAttendance(
            $classSection,
            $this->date,
            $this->studentData
        );
    }
}
