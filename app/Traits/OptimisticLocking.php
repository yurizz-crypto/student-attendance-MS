<?php

namespace App\Traits;

use App\Exceptions\StaleRecordException;

/**
 * Optimistic Locking trait.
 *
 * Add `lock_version` (unsignedInteger, default 0) to the model's table.
 * Include `lock_version` in $fillable.
 *
 * Usage in Livewire component:
 *   1. Capture $model->lock_version into a component property when opening the edit modal.
 *   2. Call $model->checkLockVersion($capturedVersion) before saving.
 *   3. After a successful save, increment the version.
 */
trait OptimisticLocking
{
    /**
     * Check if the record is still at the expected version.
     * Throws StaleRecordException if not.
     */
    public function checkLockVersion(int $expectedVersion): void
    {
        // Refresh to get the latest version from DB
        $current = $this->fresh()?->lock_version ?? 0;

        if ($current !== $expectedVersion) {
            throw new StaleRecordException;
        }
    }

    /**
     * Increment the lock version after a successful update.
     */
    public function incrementLockVersion(): void
    {
        $this->increment('lock_version');
    }
}
