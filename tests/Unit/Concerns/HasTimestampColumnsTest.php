<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Concerns;

use Banulakwin\EloquentColumns\Concerns\HasTimestampColumns;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class HasTimestampColumnsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('timestamp_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('timestamp_models');
        parent::tearDown();
    }

    #[Test]
    public function it_casts_timestamps_to_datetime(): void
    {
        $model = TimestampTestModel::create(['name' => 'test']);

        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
        $this->assertInstanceOf(Carbon::class, $model->created_at);
        $this->assertInstanceOf(Carbon::class, $model->updated_at);
    }

    #[Test]
    public function it_casts_deleted_at_when_soft_deletes_enabled(): void
    {
        $model = TimestampTestModel::create(['name' => 'test']);
        $casts = $model->getCasts();

        $this->assertArrayHasKey('deleted_at', $casts);
        $this->assertEquals('datetime', $casts['deleted_at']);
    }

    #[Test]
    public function it_returns_correct_column_names(): void
    {
        $model = new TimestampTestModel;

        $this->assertEquals('created_at', $model->getCreatedAtColumn());
        $this->assertEquals('updated_at', $model->getUpdatedAtColumn());
        $this->assertEquals('deleted_at', $model->getDeletedAtColumn());
    }

    #[Test]
    public function it_soft_deletes_model(): void
    {
        $model = TimestampTestModel::create(['name' => 'test']);
        $model->delete();

        $this->assertNotNull($model->deleted_at);
        $this->assertNull(TimestampTestModel::find($model->id));
        $this->assertNotNull(TimestampTestModel::withTrashed()->find($model->id));
    }

    #[Test]
    public function it_can_restore_soft_deleted_model(): void
    {
        $model = TimestampTestModel::create(['name' => 'test']);
        $model->delete();

        $model->restore();

        $this->assertNotNull(TimestampTestModel::find($model->id));
    }

    #[Test]
    public function it_can_disable_soft_deletes(): void
    {
        $model = new NoSoftDeletesTestModel;

        $casts = $model->getCasts();
        $this->assertArrayNotHasKey('deleted_at', $casts);
    }
}

class TimestampTestModel extends Model
{
    use HasTimestampColumns;

    protected $table = 'timestamp_models';

    protected $fillable = ['name'];
}

class NoSoftDeletesTestModel extends Model
{
    use HasTimestampColumns;

    protected $table = 'timestamp_models';

    protected $fillable = ['name'];

    protected static function shouldUseSoftDeletes(): bool
    {
        return false;
    }
}
