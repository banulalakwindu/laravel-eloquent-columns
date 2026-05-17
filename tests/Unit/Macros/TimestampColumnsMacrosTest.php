<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Macros;

use Banulakwin\EloquentColumns\Macros\TimestampColumnsMacros;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class TimestampColumnsMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TimestampColumnsMacros::register();
    }

    #[Test]
    public function it_registers_timestamp_columns_macro(): void
    {
        $this->assertTrue(Blueprint::hasMacro('timestampColumns'));
    }

    #[Test]
    public function it_creates_timestamps_and_soft_deletes_by_default(): void
    {
        Schema::create('test_timestamps', function (Blueprint $table) {
            $table->id();
            $table->timestampColumns();
        });

        $columns = Schema::getColumns('test_timestamps');
        $names = collect($columns)->pluck('name')->all();

        $this->assertContains('created_at', $names);
        $this->assertContains('updated_at', $names);
        $this->assertContains('deleted_at', $names);

        Schema::dropIfExists('test_timestamps');
    }

    #[Test]
    public function it_creates_only_timestamps_when_soft_deletes_disabled(): void
    {
        Schema::create('test_timestamps_no_soft', function (Blueprint $table) {
            $table->id();
            $table->timestampColumns(false);
        });

        $columns = Schema::getColumns('test_timestamps_no_soft');
        $names = collect($columns)->pluck('name')->all();

        $this->assertContains('created_at', $names);
        $this->assertContains('updated_at', $names);
        $this->assertNotContains('deleted_at', $names);

        Schema::dropIfExists('test_timestamps_no_soft');
    }
}
