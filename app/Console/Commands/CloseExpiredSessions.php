<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:close-expired')]
#[Description('Close expired attendance sessions and mark unenrolled students as absent')]
class CloseExpiredSessions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sessions = AttendanceSession::where('status', 'open')->get();

        $now = now();
        $closedCount = 0;

        foreach ($sessions as $session) {
            if (! $session->end_time || ! $session->date) {
                continue;
            }

            $endDateTime = Carbon::parse($session->date->format('Y-m-d').' '.$session->end_time);

            if ($now->greaterThan($endDateTime)) {
                $session->update(['status' => 'closed']);
                $closedCount++;

                $enrolledStudentIds = Enrollment::where('class_section_id', $session->class_section_id)
                    ->where('status', 'active')
                    ->pluck('student_id');

                $recordedStudentIds = AttendanceRecord::where('attendance_session_id', $session->id)
                    ->pluck('student_id');

                $absentStudentIds = $enrolledStudentIds->diff($recordedStudentIds);

                $recordsToInsert = [];
                foreach ($absentStudentIds as $studentId) {
                    $recordsToInsert[] = [
                        'attendance_session_id' => $session->id,
                        'student_id' => $studentId,
                        'status' => 'absent',
                        'remarks' => 'Auto-marked absent due to session expiration',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (! empty($recordsToInsert)) {
                    AttendanceRecord::insert($recordsToInsert);
                }
            }
        }

        $this->info("Closed {$closedCount} expired attendance sessions.");
    }
}
