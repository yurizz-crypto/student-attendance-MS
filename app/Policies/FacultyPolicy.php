<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\ClassSection;
use App\Models\User;

class FacultyPolicy
{
    /**
     * Determine if the user can view any classes.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'faculty';
    }

    /**
     * Determine if the user can view a class.
     */
    public function view(User $user, ClassSection $classSection): bool
    {
        return $user->role === 'faculty' && $user->id === $classSection->faculty_id;
    }

    /**
     * Determine if the user can create a class.
     */
    public function create(User $user): bool
    {
        return $user->role === 'faculty';
    }

    /**
     * Determine if the user can update a class.
     */
    public function update(User $user, ClassSection $classSection): bool
    {
        return $user->role === 'faculty' && $user->id === $classSection->faculty_id;
    }

    /**
     * Determine if the user can delete a class.
     */
    public function delete(User $user, ClassSection $classSection): bool
    {
        return $user->role === 'faculty' && $user->id === $classSection->faculty_id;
    }

    /**
     * Determine if the user can manage students in a class.
     */
    public function manageStudents(User $user, ClassSection $classSection): bool
    {
        return $user->role === 'faculty' && $user->id === $classSection->faculty_id;
    }

    /**
     * Determine if the user can view attendance sessions.
     */
    public function viewAttendance(User $user, ClassSection $classSection): bool
    {
        return $user->role === 'faculty' && $user->id === $classSection->faculty_id;
    }

    /**
     * Determine if the user can create attendance sessions.
     */
    public function createAttendance(User $user, ClassSection $classSection): bool
    {
        return $user->role === 'faculty' && $user->id === $classSection->faculty_id;
    }

    /**
     * Determine if the user can record attendance.
     */
    public function recordAttendance(User $user, AttendanceSession $session): bool
    {
        return $user->role === 'faculty' &&
            $user->id === $session->classSection->faculty_id;
    }

    /**
     * Determine if the user can view QR codes.
     */
    public function viewQrCode(User $user, AttendanceSession $session): bool
    {
        return $user->role === 'faculty' &&
            $user->id === $session->classSection->faculty_id;
    }
}
