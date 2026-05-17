<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Concerns;

use Banulakwin\EloquentColumns\Concerns\HasMetaColumns;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class HasMetaColumnsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('meta_models', function (Blueprint $table) {
            $table->id();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->text('meta_image')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('meta_models');
        parent::tearDown();
    }

    #[Test]
    public function it_merges_all_meta_columns_into_fillable(): void
    {
        $model = new MetaTestModel;
        $fillable = $model->getFillable();

        $this->assertContains('meta_title', $fillable);
        $this->assertContains('meta_description', $fillable);
        $this->assertContains('meta_keywords', $fillable);
        $this->assertContains('meta_image', $fillable);
    }

    #[Test]
    public function it_casts_meta_keywords_to_array(): void
    {
        $model = new MetaTestModel;
        $casts = $model->getCasts();

        $this->assertArrayHasKey('meta_keywords', $casts);
        $this->assertEquals('array', $casts['meta_keywords']);
    }

    #[Test]
    public function it_stores_and_retrieves_meta_keywords_as_array(): void
    {
        $model = MetaTestModel::create([
            'meta_title' => 'Test',
            'meta_keywords' => ['laravel', 'php', 'eloquent'],
        ]);

        $fresh = $model->fresh();
        $this->assertEquals(['laravel', 'php', 'eloquent'], $fresh->meta_keywords);
    }
}

class MetaTestModel extends Model
{
    use HasMetaColumns;

    protected $table = 'meta_models';

    protected $fillable = ['meta_title', 'meta_description', 'meta_keywords', 'meta_image'];
}
