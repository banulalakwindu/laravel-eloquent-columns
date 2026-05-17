<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Macros;

use Illuminate\Database\Schema\Blueprint;

final class SlugColumnMacros
{
    public static function register(): void
    {
        Blueprint::macro('slugColumns', function (string $mainColumn = 'name', string $slugColumn = 'slug'): void {
            $this->string($mainColumn);
            $this->string($slugColumn);
        });
    }
}
