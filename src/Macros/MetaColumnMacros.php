<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Macros;

use Illuminate\Database\Schema\Blueprint;

final class MetaColumnMacros
{
    public static function register(): void
    {
        Blueprint::macro('metaColumns', function (
            string $titleColumn = 'meta_title',
            string $descriptionColumn = 'meta_description',
            string $keywordsColumn = 'meta_keywords',
            string $imageColumn = 'meta_image'
        ): void {
            $this->string($titleColumn)->nullable();
            $this->text($descriptionColumn)->nullable();
            $this->json($keywordsColumn)->nullable();
            $this->text($imageColumn)->nullable();
        });
    }
}
