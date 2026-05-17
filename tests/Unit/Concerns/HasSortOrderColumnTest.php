<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Concerns;

use Banulakwin\EloquentColumns\Concerns\HasSortOrderColumn;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class HasSortOrderColumnTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('sort_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('sort_models');
        parent::tearDown();
    }

    #[Test]
    public function it_orders_by_sort_order_ascending(): void
    {
        SortTestModel::create(['name' => 'third', 'sort_order' => 3]);
        SortTestModel::create(['name' => 'first', 'sort_order' => 1]);
        SortTestModel::create(['name' => 'second', 'sort_order' => 2]);

        $ordered = SortTestModel::orderBySortOrder()->get();

        $this->assertEquals(['first', 'second', 'third'], $ordered->pluck('name')->all());
    }

    #[Test]
    public function it_orders_by_sort_order_descending(): void
    {
        SortTestModel::create(['name' => 'first', 'sort_order' => 1]);
        SortTestModel::create(['name' => 'third', 'sort_order' => 3]);
        SortTestModel::create(['name' => 'second', 'sort_order' => 2]);

        $ordered = SortTestModel::orderBySortOrderDesc()->get();

        $this->assertEquals(['third', 'second', 'first'], $ordered->pluck('name')->all());
    }

    #[Test]
    public function it_gets_sort_order(): void
    {
        $model = SortTestModel::create(['name' => 'test', 'sort_order' => 5]);

        $this->assertEquals(5, $model->getSortOrder());
    }

    #[Test]
    public function it_sets_sort_order(): void
    {
        $model = SortTestModel::create(['name' => 'test', 'sort_order' => 1]);

        $model->setSortOrder(10);

        $this->assertEquals(10, $model->fresh()->getSortOrder());
    }

    #[Test]
    public function it_moves_item_before_another(): void
    {
        $a = SortTestModel::create(['name' => 'a', 'sort_order' => 1]);
        $b = SortTestModel::create(['name' => 'b', 'sort_order' => 2]);
        $c = SortTestModel::create(['name' => 'c', 'sort_order' => 3]);

        $c->moveBefore($a);

        $this->assertEquals(1, $c->fresh()->sort_order);
        $this->assertEquals(2, $a->fresh()->sort_order);
        $this->assertEquals(3, $b->fresh()->sort_order);
    }

    #[Test]
    public function it_moves_item_after_another(): void
    {
        $a = SortTestModel::create(['name' => 'a', 'sort_order' => 1]);
        $b = SortTestModel::create(['name' => 'b', 'sort_order' => 2]);
        $c = SortTestModel::create(['name' => 'c', 'sort_order' => 3]);

        $a->moveAfter($c);

        $this->assertEquals(3, $a->fresh()->sort_order);
        $this->assertEquals(1, $b->fresh()->sort_order);
        $this->assertEquals(2, $c->fresh()->sort_order);
    }

    #[Test]
    public function it_merges_fillable_and_casts(): void
    {
        $model = new SortTestModel;
        $this->assertContains('sort_order', $model->getFillable());
        $this->assertArrayHasKey('sort_order', $model->getCasts());
        $this->assertEquals('integer', $model->getCasts()['sort_order']);
    }
}

class SortTestModel extends Model
{
    use HasSortOrderColumn;

    protected $table = 'sort_models';

    protected $fillable = ['name', 'sort_order'];
}
