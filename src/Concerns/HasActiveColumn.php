<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasActiveColumn {
    protected function getActiveColumnDefault(): bool {
        return true;
    }

    #[Scope]
    protected function active(Builder $builder): void {
        $activeColumn = $this->getActiveColumn();
        $builder->where($activeColumn, true);
    }

    protected function getActiveColumn(): string {
        return 'is_active';
    }

    public function initializeHasActiveColumn(): void {
        $activeColumn = $this->getActiveColumn();
        $this->mergeFillable([$activeColumn]);
        $this->mergeCasts([$activeColumn => 'boolean']);
    }

    public function isActive(): bool {
        $activeColumn = $this->getActiveColumn();

        return (bool) $this->{$activeColumn};
    }

    public function markActive(): void {
        $activeColumn = $this->getActiveColumn();
        $this->{$activeColumn} = true;
        $this->save();
    }

    public function markInactive(): void {
        $activeColumn = $this->getActiveColumn();
        $this->{$activeColumn} = false;
        $this->save();
    }
}
