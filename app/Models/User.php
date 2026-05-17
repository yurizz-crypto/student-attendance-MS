<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'first_name',
    'middle_name',
    'last_name',
    'identity_id',
    'email',
    'password',
    'role',
    'device_fingerprint',
    'status',
    'avatar_path',
    'permissions',
    'otp_code',
    'otp_expires_at',
    'last_activity',
])]

#[Hidden(['password', 'remember_token', 'otp_code'])]

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'otp_expires_at' => 'datetime',
            'last_activity' => 'datetime',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        $permissions = $this->permissions ?? [];

        return in_array($permission, $permissions);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    /* * Faculty Relationships
     */
    public function classesTaught(): HasMany
    {
        return $this->hasMany(ClassSection::class, 'faculty_id');
    }

    /* * Student Relationships
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
