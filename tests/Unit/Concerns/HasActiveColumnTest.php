<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Concerns;

use Banulakwin\EloquentColumns\Concerns\HasActiveColumn;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class HasActiveColumnTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('active_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('active_models');
        parent::tearDown();
    }

    #[Test]
    public function it_adds_active_scope(): void
    {
        ActiveTestModel::create(['name' => 'active']);
        ActiveTestModel::create(['name' => 'inactive', 'is_active' => false]);

        $active = ActiveTestModel::active()->get();
        $this->assertCount(1, $active);
        $this->assertEquals('active', $active->first()->name);
    }

    #[Test]
    public function it_checks_is_active(): void
    {
        $model = ActiveTestModel::create(['name' => 'test', 'is_active' => true]);
        $this->assertTrue($model->isActive());
    }

    #[Test]
    public function it_marks_active(): void
    {
        $model = ActiveTestModel::create(['name' => 'test', 'is_active' => false]);
        $this->assertFalse($model->isActive());

        $model->markActive();
        $this->assertTrue($model->fresh()->isActive());
    }

    #[Test]
    public function it_marks_inactive(): void
    {
        $model = ActiveTestModel::create(['name' => 'test', 'is_active' => true]);
        $this->assertTrue($model->isActive());

        $model->markInactive();
        $this->assertFalse($model->fresh()->isActive());
    }

    #[Test]
    public function it_merges_fillable_and_casts(): void
    {
        $model = new ActiveTestModel;
        $this->assertContains('is_active', $model->getFillable());
        $this->assertArrayHasKey('is_active', $model->getCasts());
        $this->assertEquals('boolean', $model->getCasts()['is_active']);
    }
}

class ActiveTestModel extends Model
{
    use HasActiveColumn;

    protected $table = 'active_models';

    protected $fillable = ['name', 'is_active'];
}
