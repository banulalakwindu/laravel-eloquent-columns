<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Macros;

use Banulakwin\EloquentColumns\Macros\ActiveColumnMacros;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class ActiveColumnMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ActiveColumnMacros::register();
    }

    #[Test]
    public function it_registers_active_column_macro(): void
    {
        $this->assertTrue(Blueprint::hasMacro('activeColumn'));
    }

    #[Test]
    public function it_creates_boolean_column_with_default_true(): void
    {
        Schema::create('test_active', function (Blueprint $table) {
            $table->id();
            $table->activeColumn();
        });

        $columns = Schema::getColumns('test_active');
        $activeCol = collect($columns)->firstWhere('name', 'is_active');

        $this->assertNotNull($activeCol);
        $this->assertContains($activeCol['type_name'], ['boolean', 'tinyint', 'tinyint(1)']);
        $this->assertContains($activeCol['default'], ['1', "'1'", 'true']);

        Schema::dropIfExists('test_active');
    }

    #[Test]
    public function it_accepts_custom_column_name(): void
    {
        Schema::create('test_active_custom', function (Blueprint $table) {
            $table->id();
            $table->activeColumn('custom_active');
        });

        $columns = Schema::getColumns('test_active_custom');
        $col = collect($columns)->firstWhere('name', 'custom_active');

        $this->assertNotNull($col);

        Schema::dropIfExists('test_active_custom');
    }

    #[Test]
    public function it_accepts_custom_default_value(): void
    {
        Schema::create('test_active_false', function (Blueprint $table) {
            $table->id();
            $table->activeColumn('is_active', false);
        });

        $columns = Schema::getColumns('test_active_false');
        $col = collect($columns)->firstWhere('name', 'is_active');

        $this->assertContains($col['default'], ['0', "'0'", 'false']);

        Schema::dropIfExists('test_active_false');
    }
}
