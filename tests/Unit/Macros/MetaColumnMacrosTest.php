<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Macros;

use Banulakwin\EloquentColumns\Macros\MetaColumnMacros;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class MetaColumnMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        MetaColumnMacros::register();
    }

    #[Test]
    public function it_registers_meta_columns_macro(): void
    {
        $this->assertTrue(Blueprint::hasMacro('metaColumns'));
    }

    #[Test]
    public function it_creates_four_meta_columns(): void
    {
        Schema::create('test_meta', function (Blueprint $table) {
            $table->id();
            $table->metaColumns();
        });

        $columns = Schema::getColumns('test_meta');
        $names = collect($columns)->pluck('name')->all();

        $this->assertContains('meta_title', $names);
        $this->assertContains('meta_description', $names);
        $this->assertContains('meta_keywords', $names);
        $this->assertContains('meta_image', $names);

        foreach (['meta_title', 'meta_description', 'meta_keywords', 'meta_image'] as $colName) {
            $col = collect($columns)->firstWhere('name', $colName);
            $this->assertTrue((bool) $col['nullable']);
        }

        Schema::dropIfExists('test_meta');
    }

    #[Test]
    public function it_accepts_custom_column_names(): void
    {
        Schema::create('test_meta_custom', function (Blueprint $table) {
            $table->id();
            $table->metaColumns('og_title', 'og_desc', 'tags', 'cover');
        });

        $columns = Schema::getColumns('test_meta_custom');
        $names = collect($columns)->pluck('name')->all();

        $this->assertEquals(['id', 'og_title', 'og_desc', 'tags', 'cover'], $names);

        Schema::dropIfExists('test_meta_custom');
    }
}
