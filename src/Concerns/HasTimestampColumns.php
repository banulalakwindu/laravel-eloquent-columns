<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasTimestampColumns
{
    use SoftDeletes {
        SoftDeletes::bootSoftDeletes as parentBootSoftDeletes;
        SoftDeletes::initializeSoftDeletes as parentInitializeSoftDeletes;
    }

    protected static function bootHasTimestampColumns(): void
    {
        if (static::shouldUseSoftDeletes()) {
            static::parentBootSoftDeletes();
        }
    }

    protected static function shouldUseSoftDeletes(): bool
    {
        return true;
    }

    public function initializeHasTimestampColumns(): void
    {
        $casts = [
            $this->getCreatedAtColumn() => 'datetime',
            $this->getUpdatedAtColumn() => 'datetime',
        ];

        if ($this->shouldUseSoftDeletes()) {
            $casts[$this->getDeletedAtColumn()] = 'datetime';
            $this->parentInitializeSoftDeletes();
        }
    }

    public function getCreatedAtColumn(): string
    {
        return 'created_at';
    }

    public function getUpdatedAtColumn(): string
    {
        return 'updated_at';
    }

    public function getDeletedAtColumn(): string
    {
        return 'deleted_at';
    }

    public function initializeSoftDeletes(): void
    {
        if ($this->shouldUseSoftDeletes()) {
            $this->parentInitializeSoftDeletes();
        }
    }
}
