<?php

namespace App\Services;

use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\ClassSection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Safely record bulk attendance using a database transaction.
     */
    public function recordBulkAttendance(ClassSection $classSection, string $date, array $studentData): void
    {
        DB::transaction(function () use ($classSection, $date, $studentData) {
            
            $session = AttendanceSession::firstOrCreate(
                [
                    'class_section_id' => $classSection->id,
                    'date' => Carbon::parse($date)->format('Y-m-d'),
                ],
                [
                    'status' => 'closed'
                ]
            );

            foreach ($studentData as $data) {
                AttendanceRecord::updateOrCreate(
                    [
                        'attendance_session_id' => $session->id,
                        'student_id' => $data['student_id'],
                    ],
                    [
                        'status' => $data['status'],
                        'remarks' => $data['remarks'] ?? null,
                    ]
                );
            }
        });
    }
}