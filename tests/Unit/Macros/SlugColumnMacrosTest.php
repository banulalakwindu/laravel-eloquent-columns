<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Macros;

use Banulakwin\EloquentColumns\Macros\SlugColumnMacros;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class SlugColumnMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        SlugColumnMacros::register();
    }

    #[Test]
    public function it_registers_slug_columns_macro(): void
    {
        $this->assertTrue(Blueprint::hasMacro('slugColumns'));
    }

    #[Test]
    public function it_creates_name_and_slug_columns(): void
    {
        Schema::create('test_slug', function (Blueprint $table) {
            $table->id();
            $table->slugColumns();
        });

        $columns = Schema::getColumns('test_slug');
        $names = collect($columns)->pluck('name')->all();

        $this->assertContains('name', $names);
        $this->assertContains('slug', $names);

        Schema::dropIfExists('test_slug');
    }

    #[Test]
    public function it_accepts_custom_column_names(): void
    {
        Schema::create('test_slug_custom', function (Blueprint $table) {
            $table->id();
            $table->slugColumns('title', 'url_slug');
        });

        $columns = Schema::getColumns('test_slug_custom');
        $names = collect($columns)->pluck('name')->all();

        $this->assertContains('title', $names);
        $this->assertContains('url_slug', $names);

        Schema::dropIfExists('test_slug_custom');
    }
}
