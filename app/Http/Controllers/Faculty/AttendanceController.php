<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\ClassSection;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display the attendance form for a specific class.
     */
    public function show(ClassSection $classSection): View
    {
        // Security check: Only the assigned faculty can view this
        abort_if($classSection->faculty_id !== Auth::id(), 403, 'Unauthorized action.');

        // Eager load the enrolled students to prevent N+1 query issues
        $classSection->load('enrollments.student');

        return view('faculty.attendance', compact('classSection'));
    }

    /**
     * Store the attendance records.
     */
    public function store(
        StoreAttendanceRequest $request, 
        ClassSection $classSection, 
        AttendanceService $attendanceService
    ): RedirectResponse {
        
        abort_if($classSection->faculty_id !== Auth::id(), 403, 'Unauthorized action.');

        // Retrieve the data that passed our strict validation rules
        $validated = $request->validated();

        // Pass it to the service to handle the database heavy lifting
        $attendanceService->recordBulkAttendance(
            $classSection,
            $validated['attendance_date'],
            $validated['students']
        );

        return back()->with('status', 'Attendance recorded successfully!');
    }
}