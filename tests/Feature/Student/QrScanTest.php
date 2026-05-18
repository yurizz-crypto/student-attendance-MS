<?php

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Str;

// -----------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------

function makeQrStudent(): User
{
    return User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
}

function makeQrFaculty(): User
{
    return User::factory()->create(['role' => 'faculty', 'email_verified_at' => now()]);
}

function makeQrSession(User $faculty, User $student): array
{
    $semester = Semester::create(['name' => 'QR-Test Sem '.Str::random(4), 'faculty_id' => $faculty->id, 'is_active' => true]);
    $subject = Subject::firstOrCreate(['name' => 'QR Subject'], ['code' => 'QRS-'.rand(100, 999)]);
    $class = ClassSection::create([
        'subject_id' => $subject->id,
        'faculty_id' => $faculty->id,
        'semester_id' => $semester->id,
        'name' => 'QR Section',
        'schedule_details' => 'MWF 8am',
    ]);

    Enrollment::create(['class_section_id' => $class->id, 'student_id' => $student->id, 'status' => 'active']);

    $session = AttendanceSession::create([
        'class_section_id' => $class->id,
        'date' => today()->format('Y-m-d'),
        'status' => 'open',
        'attendance_code' => strtoupper(Str::random(6)),
        'start_time' => '08:00',
        'end_time' => '10:00',
        'qr_code_data' => null,
    ]);

    $qrData = implode('|', [$session->id, $class->id, today()->format('Y-m-d'), $faculty->id]);
    $session->update(['qr_code_data' => $qrData]);

    return ['session' => $session, 'class' => $class, 'qr_data' => $qrData];
}

// -----------------------------------------------------------------------
// Tests (all bypass OTP middleware for unit testing purposes)
// -----------------------------------------------------------------------

it('rejects QR scan when no qr_data is provided', function () {
    $student = makeQrStudent();

    $this->actingAs($student)
        ->withoutMiddleware(\App\Http\Middleware\EnsureOtpIsVerified::class)
        ->postJson(route('student.qr-scan.process'), [])
        ->assertStatus(400)
        ->assertJson(['success' => false]);
});

it('marks student present via qr_data key (qr-scan page format)', function () {
    $faculty = makeQrFaculty();
    $student = makeQrStudent();
    ['session' => $session, 'qr_data' => $qrData] = makeQrSession($faculty, $student);

    $this->actingAs($student)
        ->withoutMiddleware(\App\Http\Middleware\EnsureOtpIsVerified::class)
        ->postJson(route('student.qr-scan.process'), ['qr_data' => $qrData])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(
        AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('student_id', $student->id)
            ->where('status', 'present')
            ->exists()
    )->toBeTrue();
});

it('marks student present via qrData key (student dashboard format)', function () {
    $faculty = makeQrFaculty();
    $student = makeQrStudent();
    ['session' => $session, 'qr_data' => $qrData] = makeQrSession($faculty, $student);

    $this->actingAs($student)
        ->withoutMiddleware(\App\Http\Middleware\EnsureOtpIsVerified::class)
        ->postJson(route('student.qr-scan.process'), ['qrData' => $qrData])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(
        AttendanceRecord::where('attendance_session_id', $session->id)
            ->where('student_id', $student->id)
            ->where('status', 'present')
            ->exists()
    )->toBeTrue();
});

it('rejects marking present when session is closed', function () {
    $faculty = makeQrFaculty();
    $student = makeQrStudent();
    ['session' => $session, 'qr_data' => $qrData] = makeQrSession($faculty, $student);

    $session->update(['status' => 'closed']);

    $this->actingAs($student)
        ->withoutMiddleware(\App\Http\Middleware\EnsureOtpIsVerified::class)
        ->postJson(route('student.qr-scan.process'), ['qr_data' => $qrData])
        ->assertStatus(400)
        ->assertJson(['success' => false]);
});

it('rejects marking present when student is not enrolled', function () {
    $faculty = makeQrFaculty();
    $student = makeQrStudent();
    $unenrolledStudent = makeQrStudent();
    ['qr_data' => $qrData] = makeQrSession($faculty, $student);

    $this->actingAs($unenrolledStudent)
        ->withoutMiddleware(\App\Http\Middleware\EnsureOtpIsVerified::class)
        ->postJson(route('student.qr-scan.process'), ['qr_data' => $qrData])
        ->assertStatus(403)
        ->assertJson(['success' => false]);
});

it('rejects marking present twice for the same session', function () {
    $faculty = makeQrFaculty();
    $student = makeQrStudent();
    ['session' => $session, 'qr_data' => $qrData] = makeQrSession($faculty, $student);

    // First scan — should succeed
    $this->actingAs($student)
        ->withoutMiddleware(\App\Http\Middleware\EnsureOtpIsVerified::class)
        ->postJson(route('student.qr-scan.process'), ['qr_data' => $qrData])
        ->assertStatus(200)
        ->assertJson(['success' => true]);

    // Second scan — should fail
    $this->actingAs($student)
        ->withoutMiddleware(\App\Http\Middleware\EnsureOtpIsVerified::class)
        ->postJson(route('student.qr-scan.process'), ['qr_data' => $qrData])
        ->assertStatus(400)
        ->assertJson(['success' => false]);
});
