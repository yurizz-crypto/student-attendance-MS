<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'ip_address',
        'user_agent',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'changes' => AsCollection::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a human-readable description of the model type.
     */
    public function getModelDisplayName(): string
    {
        return str(class_basename($this->model_type))->plural()->toString();
    }

    /**
     * Get the action label.
     */
    public function getActionLabel(): string
    {
        return match ($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'viewed' => 'Viewed',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'exported' => 'Exported',
            'imported' => 'Imported',
            'excuse_submitted' => 'Excuse Submitted',
            'excuse_processed' => 'Excuse Processed',
            default => str_replace('_', ' ', Str::title($this->action)),
        };
    }

    /**
     * Get the action color for UI.
     */
    public function getActionColor(): string
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'error',
            'viewed' => 'warning',
            'login' => 'success',
            'logout' => 'warning',
            'excuse_submitted' => 'info',
            'excuse_processed' => 'success',
            default => 'gray',
        };
    }
}
