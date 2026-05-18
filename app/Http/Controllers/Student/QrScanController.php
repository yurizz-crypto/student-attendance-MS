<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Notifications\StudentScannedAttendanceNotification;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QrScanController extends Controller
{
    /**
     * Show the QR code scanning page.
     */
    public function show(): View
    {
        return view('student.qr-scan');
    }

    /**
     * Process the scanned QR code and mark student as present.
     */
    public function scan(Request $request): JsonResponse
    {
        // Support both 'qr_data' (qr-scan page) and 'qrData' (student dashboard)
        $qrData = $request->input('qr_data') ?? $request->input('qrData', '');

        if (empty($qrData)) {
            return response()->json([
                'success' => false,
                'message' => 'No QR code data provided.',
            ], 400);
        }

        try {
            // Parse QR data: session_id|class_id|date|faculty_id
            $parts = explode('|', $qrData);

            if (count($parts) !== 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format.',
                ], 400);
            }

            $sessionId = (int) $parts[0];
            $classId = (int) $parts[1];
            $date = $parts[2];
            $facultyId = (int) $parts[3];

            // Get the session
            $session = AttendanceSession::find($sessionId);

            if (! $session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance session not found.',
                ], 404);
            }

            // Verify the session data matches
            if (
                $session->class_section_id !== $classId ||
                $session->date->format('Y-m-d') !== $date ||
                $session->classSection->faculty_id !== $facultyId
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code data does not match the session.',
                ], 400);
            }

            // Check if session is still open
            if ($session->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'message' => 'This attendance session has been closed.',
                ], 400);
            }

            // Verify student is enrolled in this class
            $enrollment = Enrollment::where('class_section_id', $classId)
                ->where('student_id', Auth::id())
                ->exists();

            if (! $enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not enrolled in this class.',
                ], 403);
            }

            // Check if already marked present
            $existing = AttendanceRecord::where('attendance_session_id', $sessionId)
                ->where('student_id', Auth::id())
                ->first();

            if ($existing && $existing->status === 'present') {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already marked yourself present in this session.',
                ], 400);
            }

            // Create or update attendance record
            if ($existing) {
                $existing->update([
                    'status' => 'present',
                    'remarks' => 'Marked present via QR code',
                ]);
                $record = $existing;
            } else {
                $record = AttendanceRecord::create([
                    'attendance_session_id' => $sessionId,
                    'student_id' => Auth::id(),
                    'status' => 'present',
                    'remarks' => 'Marked present via QR code',
                ]);
            }

            // Notify faculty
            $faculty = $session->classSection->faculty;
            if ($faculty) {
                $faculty->notify(new StudentScannedAttendanceNotification(Auth::user(), $record));
            }

            // Log the attendance marking
            AuditService::log(
                'marked_present_via_qr',
                AttendanceSession::class,
                $sessionId,
                ['student_id' => Auth::id()],
                Auth::user()->first_name.' '.Auth::user()->last_name.' marked present via QR code'
            );

            return response()->json([
                'success' => true,
                'message' => 'You have been marked present!',
                'data' => [
                    'sessionId' => $sessionId,
                    'className' => $session->classSection->name,
                    'subject' => $session->classSection->subject->name,
                    'date' => $session->date->format('M d, Y'),
                    'time' => now()->format('H:i:s'),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: '.$e->getMessage(),
            ], 500);
        }
    }
}
