<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Concerns;

use Banulakwin\EloquentColumns\Concerns\HasFeaturedColumn;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class HasFeaturedColumnTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('featured_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('featured_models');
        parent::tearDown();
    }

    #[Test]
    public function it_adds_featured_scope(): void
    {
        FeaturedTestModel::create(['name' => 'featured', 'is_featured' => true]);
        FeaturedTestModel::create(['name' => 'not featured', 'is_featured' => false]);

        $featured = FeaturedTestModel::featured()->get();
        $this->assertCount(1, $featured);
        $this->assertEquals('featured', $featured->first()->name);
    }

    #[Test]
    public function it_checks_is_featured(): void
    {
        $model = FeaturedTestModel::create(['name' => 'test', 'is_featured' => true]);
        $this->assertTrue($model->isFeatured());
    }

    #[Test]
    public function it_marks_featured(): void
    {
        $model = FeaturedTestModel::create(['name' => 'test', 'is_featured' => false]);
        $this->assertFalse($model->isFeatured());

        $model->markFeatured();
        $this->assertTrue($model->fresh()->isFeatured());
    }

    #[Test]
    public function it_marks_unfeatured(): void
    {
        $model = FeaturedTestModel::create(['name' => 'test', 'is_featured' => true]);
        $this->assertTrue($model->isFeatured());

        $model->markUnfeatured();
        $this->assertFalse($model->fresh()->isFeatured());
    }

    #[Test]
    public function it_merges_fillable_and_casts(): void
    {
        $model = new FeaturedTestModel;
        $this->assertContains('is_featured', $model->getFillable());
        $this->assertArrayHasKey('is_featured', $model->getCasts());
        $this->assertEquals('boolean', $model->getCasts()['is_featured']);
    }
}

class FeaturedTestModel extends Model
{
    use HasFeaturedColumn;

    protected $table = 'featured_models';

    protected $fillable = ['name', 'is_featured'];
}
