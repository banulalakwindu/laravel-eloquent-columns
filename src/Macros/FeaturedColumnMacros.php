<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Macros;

use Illuminate\Database\Schema\Blueprint;

final class FeaturedColumnMacros
{
    public static function register(): void
    {
        Blueprint::macro('featuredColumn', function (string $columnName = 'is_featured', bool $defaultValue = false): void {
            $this->boolean($columnName)->default($defaultValue);
        });
    }
}
