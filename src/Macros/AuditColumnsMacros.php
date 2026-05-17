<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Macros;

use Illuminate\Database\Schema\Blueprint;

/**
 * Adds nullable audit foreign keys. Use {@see $useUlid} false for bigint {@see foreignId} columns.
 */
final class AuditColumnsMacros
{
    public static function register(): void
    {
        Blueprint::macro(
            'auditColumns',
            function (
                string $createdByColumn = 'created_by',
                string $updatedByColumn = 'updated_by',
                string $deletedByColumn = 'deleted_by',
                bool $useUlid = true,
            ): void {
                if ($useUlid) {
                    $this->foreignUlid($createdByColumn)->nullable();
                    $this->foreignUlid($updatedByColumn)->nullable();
                    $this->foreignUlid($deletedByColumn)->nullable();

                    return;
                }

                $this->foreignId($createdByColumn)->nullable();
                $this->foreignId($updatedByColumn)->nullable();
                $this->foreignId($deletedByColumn)->nullable();
            },
        );
    }
}
