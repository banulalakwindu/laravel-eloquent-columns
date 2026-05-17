<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns;

use Banulakwin\EloquentColumns\Macros\ActiveColumnMacros;
use Banulakwin\EloquentColumns\Macros\AuditColumnsMacros;
use Banulakwin\EloquentColumns\Macros\FeaturedColumnMacros;
use Banulakwin\EloquentColumns\Macros\MetaColumnMacros;
use Banulakwin\EloquentColumns\Macros\SlugColumnMacros;
use Banulakwin\EloquentColumns\Macros\SortOrderColumnMacros;
use Banulakwin\EloquentColumns\Macros\TimestampColumnsMacros;
use Illuminate\Support\ServiceProvider;

final class EloquentColumnsServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->mergeConfigFrom(__DIR__ . '/../config/eloquent-columns.php', 'eloquent-columns');
    }

    public function boot(): void {
        $this->publishes([
            __DIR__ . '/../config/eloquent-columns.php' => config_path('eloquent-columns.php'),
        ], 'eloquent-columns-config');

        if ( ! (bool) config('eloquent-columns.register_macros', true)) {
            return;
        }

        ActiveColumnMacros::register();
        AuditColumnsMacros::register();
        FeaturedColumnMacros::register();
        MetaColumnMacros::register();
        SlugColumnMacros::register();
        SortOrderColumnMacros::register();
        TimestampColumnsMacros::register();
    }
}
