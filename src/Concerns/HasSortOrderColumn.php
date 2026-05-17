<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasSortOrderColumn {
    protected function getSortOrderColumn(): string {
        return 'sort_order';
    }

    protected function getSortOrderColumnDefault(): int {
        return 0;
    }

    /**
     * @param  Builder<Model>  $builder
     */
    #[Scope]
    protected function orderBySortOrder(Builder $builder): void {
        $sortOrderColumn = $this->getSortOrderColumn();
        $builder->orderBy($sortOrderColumn);
    }

    /**
     * @param  Builder<Model>  $builder
     */
    #[Scope]
    protected function orderBySortOrderDesc(Builder $builder): void {
        $sortOrderColumn = $this->getSortOrderColumn();
        $builder->orderBy($sortOrderColumn, 'desc');
    }

    public function initializeHasSortOrderColumn(): void {
        $sortOrderColumn = $this->getSortOrderColumn();
        $this->mergeFillable([$sortOrderColumn]);
        $this->mergeCasts([$sortOrderColumn => 'integer']);
    }

    public function getSortOrder(): int {
        $sortOrderColumn = $this->getSortOrderColumn();

        return (int) $this->{$sortOrderColumn};
    }

    public function setSortOrder(int $order): void {
        $sortOrderColumn = $this->getSortOrderColumn();
        $this->{$sortOrderColumn} = $order;
        $this->save();
    }

    public function moveBefore(self $item): void {
        $sortOrderColumn = $this->getSortOrderColumn();
        if ($this->{$sortOrderColumn} > $item->{$sortOrderColumn}) {
            static::query()
                ->where($sortOrderColumn, '>=', $item->{$sortOrderColumn})
                ->where($sortOrderColumn, '<', $this->{$sortOrderColumn})
                ->increment($sortOrderColumn);

            $this->{$sortOrderColumn} = $item->{$sortOrderColumn};
            $this->save();
        } else {
            static::query()
                ->where($sortOrderColumn, '>', $this->{$sortOrderColumn})
                ->where($sortOrderColumn, '<=', $item->{$sortOrderColumn})
                ->decrement($sortOrderColumn);

            $this->{$sortOrderColumn} = $item->{$sortOrderColumn};
            $this->save();
        }
    }

    public function moveAfter(self $item): void {
        $sortOrderColumn = $this->getSortOrderColumn();
        if ($this->{$sortOrderColumn} > $item->{$sortOrderColumn}) {
            static::query()
                ->where($sortOrderColumn, '>', $item->{$sortOrderColumn})
                ->where($sortOrderColumn, '<', $this->{$sortOrderColumn})
                ->increment($sortOrderColumn);

            $this->{$sortOrderColumn} = $item->{$sortOrderColumn} + 1;
            $this->save();
        } else {
            static::query()
                ->where($sortOrderColumn, '>', $this->{$sortOrderColumn})
                ->where($sortOrderColumn, '<=', $item->{$sortOrderColumn})
                ->decrement($sortOrderColumn);

            $this->{$sortOrderColumn} = $item->{$sortOrderColumn};
            $this->save();
        }
    }
}
