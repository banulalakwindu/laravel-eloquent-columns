<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasFeaturedColumn
{
    protected function getFeaturedColumn(): string
    {
        return 'is_featured';
    }

    protected function getFeaturedColumnDefault(): bool
    {
        return false;
    }

    /**
     * @param  Builder<Model>  $builder
     */
    #[Scope]
    protected function featured(Builder $builder): void
    {
        $featuredColumn = $this->getFeaturedColumn();
        $builder->where($featuredColumn, true);
    }

    public function initializeHasFeaturedColumn(): void
    {
        $featuredColumn = $this->getFeaturedColumn();
        $this->mergeFillable([$featuredColumn]);
        $this->mergeCasts([$featuredColumn => 'boolean']);
    }

    public function isFeatured(): bool
    {
        $featuredColumn = $this->getFeaturedColumn();

        return (bool) $this->{$featuredColumn};
    }

    public function markFeatured(): void
    {
        $featuredColumn = $this->getFeaturedColumn();
        $this->{$featuredColumn} = true;
        $this->save();
    }

    public function markUnfeatured(): void
    {
        $featuredColumn = $this->getFeaturedColumn();
        $this->{$featuredColumn} = false;
        $this->save();
    }
}
