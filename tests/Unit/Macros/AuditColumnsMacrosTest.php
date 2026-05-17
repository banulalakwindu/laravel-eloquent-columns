<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Macros;

use Banulakwin\EloquentColumns\Macros\AuditColumnsMacros;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class AuditColumnsMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        AuditColumnsMacros::register();
    }

    #[Test]
    public function it_registers_audit_columns_macro(): void
    {
        $this->assertTrue(Blueprint::hasMacro('auditColumns'));
    }

    #[Test]
    public function it_creates_foreign_ulid_columns_by_default(): void
    {
        Schema::create('test_audit_ulid', function (Blueprint $table) {
            $table->id();
            $table->auditColumns();
        });

        $columns = Schema::getColumns('test_audit_ulid');
        $names = collect($columns)->pluck('name')->all();

        $this->assertContains('created_by', $names);
        $this->assertContains('updated_by', $names);
        $this->assertContains('deleted_by', $names);

        foreach (['created_by', 'updated_by', 'deleted_by'] as $colName) {
            $col = collect($columns)->firstWhere('name', $colName);
            $this->assertTrue((bool) $col['nullable']);
        }

        Schema::dropIfExists('test_audit_ulid');
    }

    #[Test]
    public function it_creates_foreign_id_columns_when_use_ulid_is_false(): void
    {
        Schema::create('test_audit_id', function (Blueprint $table) {
            $table->id();
            $table->auditColumns('created_by', 'updated_by', 'deleted_by', false);
        });

        $columns = Schema::getColumns('test_audit_id');

        foreach (['created_by', 'updated_by', 'deleted_by'] as $colName) {
            $col = collect($columns)->firstWhere('name', $colName);
            $this->assertContains($col['type_name'], ['bigint', 'integer', 'unsigned big integer']);
            $this->assertTrue((bool) $col['nullable']);
        }

        Schema::dropIfExists('test_audit_id');
    }

    #[Test]
    public function it_accepts_custom_column_names(): void
    {
        Schema::create('test_audit_custom', function (Blueprint $table) {
            $table->id();
            $table->auditColumns('creator_id', 'updater_id', 'deleter_id');
        });

        $columns = Schema::getColumns('test_audit_custom');
        $names = collect($columns)->pluck('name')->all();

        $this->assertContains('creator_id', $names);
        $this->assertContains('updater_id', $names);
        $this->assertContains('deleter_id', $names);

        Schema::dropIfExists('test_audit_custom');
    }
}
