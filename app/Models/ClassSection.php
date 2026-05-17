<?php

namespace App\Models;

use App\Traits\OptimisticLocking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSection extends Model
{
    use OptimisticLocking, SoftDeletes;

    protected $fillable = [
        'subject_id',
        'faculty_id',
        'semester_id',
        'name',
        'schedule_details',
        'lock_version',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function getIsActiveAttribute(): bool
    {
        if (! $this->semester_id) {
            return true;
        }

        return $this->semester ? $this->semester->is_active : true;
    }
}
