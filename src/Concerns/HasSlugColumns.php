<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

trait HasSlugColumns
{
    use HasSlug;

    protected function getSlugSourceColumn(): string
    {
        return 'name';
    }

    protected function getSlugColumn(): string
    {
        return 'slug';
    }

    protected function shouldGenerateSlugsOnUpdate(): bool
    {
        return false;
    }

    public function initializeHasSlugColumns(): void
    {
        $mainColumn = $this->getSlugSourceColumn();
        $slugColumn = $this->getSlugColumn();

        $this->mergeFillable([$mainColumn, $slugColumn]);
    }

    public function getSlugOptions(): SlugOptions
    {
        $mainColumn = $this->getSlugSourceColumn();
        $slugColumn = $this->getSlugColumn();

        $slugOptions = SlugOptions::create()
            ->generateSlugsFrom($mainColumn)
            ->saveSlugsTo($slugColumn);

        if (! $this->shouldGenerateSlugsOnUpdate()) {
            $slugOptions->doNotGenerateSlugsOnUpdate();
        }

        if (method_exists($this, 'getDeletedAtColumn')) {
            $slugOptions->extraScope(
                function (Builder $builder): void {
                    $builder->whereNull($this->getDeletedAtColumn());
                }
            );
        }

        return $slugOptions;
    }

    public function getRouteKeyName(): string
    {
        return $this->getSlugColumn();
    }
}
