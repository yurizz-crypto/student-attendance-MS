<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                    'status' => 'closed',
                ]
            );

            $now = Carbon::now();
            $recordsData = array_map(function ($data) use ($session, $now) {
                return [
                    'attendance_session_id' => $session->id,
                    'student_id' => $data['student_id'],
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $studentData);

            // Chunk the data to handle "millions of data" safely without hitting binding limits
            foreach (array_chunk($recordsData, 1000) as $chunk) {
                AttendanceRecord::upsert(
                    $chunk,
                    ['attendance_session_id', 'student_id'],
                    ['status', 'remarks', 'updated_at']
                );
            }
        });
    }
}
