<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Macros;

use Illuminate\Database\Schema\Blueprint;

final class TimestampColumnsMacros {
    public static function register(): void {
        Blueprint::macro('timestampColumns', function (bool $withSoftDeletes = true): void {
            $this->timestamps();
            if ($withSoftDeletes) {
                $this->softDeletes();
            }
        });
    }
}
