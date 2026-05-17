<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'filters',
        'is_favorite',
        'schedule_frequency',
        'email_to',
        'last_run_at',
        'next_run_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'is_favorite' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
