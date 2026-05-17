<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Macros;

use Banulakwin\EloquentColumns\Macros\FeaturedColumnMacros;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class FeaturedColumnMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        FeaturedColumnMacros::register();
    }

    #[Test]
    public function it_registers_featured_column_macro(): void
    {
        $this->assertTrue(Blueprint::hasMacro('featuredColumn'));
    }

    #[Test]
    public function it_creates_boolean_column_with_default_false(): void
    {
        Schema::create('test_featured', function (Blueprint $table) {
            $table->id();
            $table->featuredColumn();
        });

        $columns = Schema::getColumns('test_featured');
        $col = collect($columns)->firstWhere('name', 'is_featured');

        $this->assertNotNull($col);
        $this->assertContains($col['type_name'], ['boolean', 'tinyint', 'tinyint(1)']);
        $this->assertContains($col['default'], ['0', "'0'", 'false']);

        Schema::dropIfExists('test_featured');
    }

    #[Test]
    public function it_accepts_custom_column_name(): void
    {
        Schema::create('test_featured_custom', function (Blueprint $table) {
            $table->id();
            $table->featuredColumn('custom_featured');
        });

        $columns = Schema::getColumns('test_featured_custom');
        $col = collect($columns)->firstWhere('name', 'custom_featured');

        $this->assertNotNull($col);

        Schema::dropIfExists('test_featured_custom');
    }

    #[Test]
    public function it_accepts_custom_default_value(): void
    {
        Schema::create('test_featured_true', function (Blueprint $table) {
            $table->id();
            $table->featuredColumn('is_featured', true);
        });

        $columns = Schema::getColumns('test_featured_true');
        $col = collect($columns)->firstWhere('name', 'is_featured');

        $this->assertContains($col['default'], ['1', "'1'", 'true']);

        Schema::dropIfExists('test_featured_true');
    }
}
