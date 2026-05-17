<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit\Concerns;

use Banulakwin\EloquentColumns\Concerns\HasAuditColumns;
use Banulakwin\EloquentColumns\Tests\TestCase;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use LogicException;
use PHPUnit\Framework\Attributes\Test;

class HasAuditColumnsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('audit_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->timestamps();
            });
        }

        config(['eloquent-columns.user_model' => TestUser::class]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('audit_models');
        Auth::logout();
        parent::tearDown();
    }

    #[Test]
    public function it_sets_created_by_and_updated_by_on_create(): void
    {
        $user = TestUser::create(['email' => 'test@test.com']);
        Auth::login($user);

        $model = AuditTestModel::create(['name' => 'test']);

        $this->assertEquals($user->id, $model->created_by);
        $this->assertEquals($user->id, $model->updated_by);
    }

    #[Test]
    public function it_sets_updated_by_on_update(): void
    {
        $user1 = TestUser::create(['email' => 'user1@test.com']);
        $user2 = TestUser::create(['email' => 'user2@test.com']);

        Auth::login($user1);
        $model = AuditTestModel::create(['name' => 'test']);

        Auth::login($user2);
        $model->update(['name' => 'updated']);

        $this->assertEquals($user1->id, $model->fresh()->created_by);
        $this->assertEquals($user2->id, $model->fresh()->updated_by);
    }

    #[Test]
    public function it_sets_deleted_by_on_delete(): void
    {
        $user = TestUser::create(['email' => 'test@test.com']);
        Auth::login($user);

        $model = AuditTestModel::create(['name' => 'test']);
        $model->delete();

        $this->assertEquals($user->id, $model->deleted_by);
    }

    #[Test]
    public function it_does_not_set_audit_when_not_authenticated(): void
    {
        $model = AuditTestModel::create(['name' => 'test']);

        $this->assertNull($model->created_by);
        $this->assertNull($model->updated_by);
    }

    #[Test]
    public function it_merges_fillable(): void
    {
        $model = new AuditTestModel;
        $fillable = $model->getFillable();

        $this->assertContains('created_by', $fillable);
        $this->assertContains('updated_by', $fillable);
        $this->assertContains('deleted_by', $fillable);
    }

    #[Test]
    public function it_resolves_user_model_from_config(): void
    {
        $model = new AuditTestModel;
        $reflection = new \ReflectionClass($model);
        $method = $reflection->getMethod('resolveAuditUserModel');

        $this->assertEquals(TestUser::class, $method->invoke($model));
    }

    #[Test]
    public function it_throws_when_user_model_not_configured(): void
    {
        config(['eloquent-columns.user_model' => null]);
        config(['auth.providers.users.model' => null]);

        $model = new AuditTestModel;
        $reflection = new \ReflectionClass($model);
        $method = $reflection->getMethod('resolveAuditUserModel');

        $this->expectException(LogicException::class);
        $method->invoke($model);
    }

    #[Test]
    public function it_returns_creator_relationship(): void
    {
        $model = new AuditTestModel;
        $relation = $model->creator();

        $this->assertEquals(TestUser::class, get_class($relation->getRelated()));
        $this->assertEquals('created_by', $relation->getForeignKeyName());
    }

    #[Test]
    public function it_returns_updater_relationship(): void
    {
        $model = new AuditTestModel;
        $relation = $model->updater();

        $this->assertEquals(TestUser::class, get_class($relation->getRelated()));
        $this->assertEquals('updated_by', $relation->getForeignKeyName());
    }

    #[Test]
    public function it_returns_deleter_relationship(): void
    {
        $model = new AuditTestModel;
        $relation = $model->deleter();

        $this->assertEquals(TestUser::class, get_class($relation->getRelated()));
        $this->assertEquals('deleted_by', $relation->getForeignKeyName());
    }
}

class AuditTestModel extends Model
{
    use HasAuditColumns;

    protected $table = 'audit_models';

    protected $fillable = ['name', 'created_by', 'updated_by', 'deleted_by'];
}

class TestUser extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'users';

    protected $fillable = ['email'];

    public $timestamps = false;
}
