<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use LogicException;

/**
 * Tracks created_by, updated_by, deleted_by from the authenticated user.
 *
 * Relationships use {@see config('eloquent-columns.user_model')} or
 * {@see config('auth.providers.users.model')}.
 */
trait HasAuditColumns
{
    protected static function bootHasAuditColumns(): void
    {
        static::creating(function (self $model): void {
            if (Auth::check()) {
                $createdByColumn = $model->getCreatedByColumn();
                $model->{$createdByColumn} = Auth::id();

                $updatedByColumn = $model->getUpdatedByColumn();
                $model->{$updatedByColumn} = Auth::id();
            }
        });

        static::updating(function (self $model): void {
            if (Auth::check()) {
                $updatedByColumn = $model->getUpdatedByColumn();
                $model->{$updatedByColumn} = Auth::id();
            }
        });

        static::deleting(function (self $model): void {
            if (Auth::check()) {
                $deletedByColumn = $model->getDeletedByColumn();
                $model->{$deletedByColumn} = Auth::id();
                $model->save();
            }
        });
    }

    protected function getCreatedByColumn(): string
    {
        return 'created_by';
    }

    protected function getUpdatedByColumn(): string
    {
        return 'updated_by';
    }

    protected function getDeletedByColumn(): string
    {
        return 'deleted_by';
    }

    /**
     * @return class-string<Model>
     */
    protected function resolveAuditUserModel(): string
    {
        $configured = config('eloquent-columns.user_model');
        if (is_string($configured) && $configured !== '' && class_exists($configured)) {
            return $configured;
        }

        $authModel = config('auth.providers.users.model');
        if (is_string($authModel) && $authModel !== '' && class_exists($authModel)) {
            return $authModel;
        }

        throw new LogicException(
            'HasAuditColumns requires eloquent-columns.user_model or auth.providers.users.model to be a valid Eloquent model class.',
        );
    }

    public function initializeHasAuditColumns(): void
    {
        $this->mergeFillable([
            $this->getCreatedByColumn(),
            $this->getUpdatedByColumn(),
            $this->getDeletedByColumn(),
        ]);
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo($this->resolveAuditUserModel(), $this->getCreatedByColumn());
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo($this->resolveAuditUserModel(), $this->getUpdatedByColumn());
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo($this->resolveAuditUserModel(), $this->getDeletedByColumn());
    }
}
