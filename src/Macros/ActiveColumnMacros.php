<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Macros;

use Illuminate\Database\Schema\Blueprint;

final class ActiveColumnMacros
{
    public static function register(): void
    {
        Blueprint::macro('activeColumn', function (string $columnName = 'is_active', bool $defaultValue = true): void {
            $this->boolean($columnName)->default($defaultValue);
        });
    }
}
