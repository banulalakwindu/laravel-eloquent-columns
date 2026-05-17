<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Concerns;

use Banulakwin\EloquentColumns\Concerns\HasSlugColumns;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Sluggable\SlugOptions;

class HasSlugColumnsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('slug_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('slug_models');
        parent::tearDown();
    }

    #[Test]
    public function it_generates_slug_from_name(): void
    {
        $model = SlugTestModel::create(['name' => 'Hello World']);

        $this->assertEquals('hello-world', $model->slug);
    }

    #[Test]
    public function it_does_not_regenerate_slug_on_update(): void
    {
        $model = SlugTestModel::create(['name' => 'Hello World']);
        $originalSlug = $model->slug;

        $model->update(['name' => 'New Name']);

        $this->assertEquals($originalSlug, $model->fresh()->slug);
    }

    #[Test]
    public function it_uses_slug_as_route_key(): void
    {
        $model = SlugTestModel::create(['name' => 'Test Post']);

        $this->assertEquals('slug', $model->getRouteKeyName());
    }

    #[Test]
    public function it_returns_correct_slug_options(): void
    {
        $model = new SlugTestModel;
        $options = $model->getSlugOptions();

        $this->assertInstanceOf(SlugOptions::class, $options);
    }

    #[Test]
    public function it_merges_name_and_slug_into_fillable(): void
    {
        $model = new SlugTestModel;
        $fillable = $model->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('slug', $fillable);
    }
}

class SlugTestModel extends Model
{
    use HasSlugColumns;

    protected $table = 'slug_models';

    protected $fillable = ['name', 'slug'];
}
