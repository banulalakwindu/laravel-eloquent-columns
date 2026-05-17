<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Macros;

use Banulakwin\EloquentColumns\Macros\SortOrderColumnMacros;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class SortOrderColumnMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        SortOrderColumnMacros::register();
    }

    #[Test]
    public function it_registers_sort_order_column_macro(): void
    {
        $this->assertTrue(Blueprint::hasMacro('sortOrderColumn'));
    }

    #[Test]
    public function it_creates_integer_column_with_default_zero(): void
    {
        Schema::create('test_sort', function (Blueprint $table) {
            $table->id();
            $table->sortOrderColumn();
        });

        $columns = Schema::getColumns('test_sort');
        $col = collect($columns)->firstWhere('name', 'sort_order');

        $this->assertNotNull($col);
        $this->assertEquals('integer', $col['type_name']);
        $this->assertContains($col['default'], ['0', "'0'"]);

        Schema::dropIfExists('test_sort');
    }

    #[Test]
    public function it_accepts_custom_column_name(): void
    {
        Schema::create('test_sort_custom', function (Blueprint $table) {
            $table->id();
            $table->sortOrderColumn('position');
        });

        $columns = Schema::getColumns('test_sort_custom');
        $col = collect($columns)->firstWhere('name', 'position');

        $this->assertNotNull($col);

        Schema::dropIfExists('test_sort_custom');
    }

    #[Test]
    public function it_accepts_custom_default_value(): void
    {
        Schema::create('test_sort_default', function (Blueprint $table) {
            $table->id();
            $table->sortOrderColumn('sort_order', 100);
        });

        $columns = Schema::getColumns('test_sort_default');
        $col = collect($columns)->firstWhere('name', 'sort_order');

        $this->assertContains($col['default'], ['100', "'100'"]);

        Schema::dropIfExists('test_sort_default');
    }
}
