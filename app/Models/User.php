<?php

namespace App\Models;

use App\Traits\OptimisticLocking;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    'notification_preferences',
    'otp_code',
    'otp_expires_at',
    'last_activity',
    'lock_version',
])]

#[Hidden(['password', 'remember_token', 'otp_code'])]

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, OptimisticLocking, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'notification_preferences' => 'array',
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

    /**
     * Get the configured notification channels for a specific category.
     * Categories: 'system', 'warning', 'critical'
     */
    public function getNotificationChannels(string $category): array
    {
        // Default fallbacks if preference is not set
        $defaults = [
            'system' => ['database'],
            'warning' => ['database', 'mail'],
            'critical' => ['database', 'mail'],
        ];

        $prefs = $this->notification_preferences ?? [];

        if (! isset($prefs[$category])) {
            return $defaults[$category] ?? ['database'];
        }

        $channels = [];
        if (! empty($prefs[$category]['database'])) {
            $channels[] = 'database';
        }
        if (! empty($prefs[$category]['mail'])) {
            $channels[] = 'mail';
        }

        // Critical alerts should always at least notify in-app
        if ($category === 'critical' && empty($channels)) {
            return ['database'];
        }

        return empty($channels) ? ['database'] : $channels;
    }
}
