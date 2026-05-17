<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Macros;

use Illuminate\Database\Schema\Blueprint;

final class SortOrderColumnMacros
{
    public static function register(): void
    {
        Blueprint::macro('sortOrderColumn', function (string $columnName = 'sort_order', int $defaultValue = 0): void {
            $this->integer($columnName)->default($defaultValue);
        });
    }
}
