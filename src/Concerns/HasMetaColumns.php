<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Concerns;

trait HasMetaColumns
{
    protected function getMetaTitleColumn(): string
    {
        return 'meta_title';
    }

    protected function getMetaDescriptionColumn(): string
    {
        return 'meta_description';
    }

    protected function getMetaKeywordsColumn(): string
    {
        return 'meta_keywords';
    }

    protected function getMetaImageColumn(): string
    {
        return 'meta_image';
    }

    public function initializeHasMetaColumns(): void
    {
        $this->mergeFillable([
            $this->getMetaTitleColumn(),
            $this->getMetaDescriptionColumn(),
            $this->getMetaKeywordsColumn(),
            $this->getMetaImageColumn(),
        ]);

        $this->mergeCasts([
            $this->getMetaKeywordsColumn() => 'array',
        ]);
    }
}
