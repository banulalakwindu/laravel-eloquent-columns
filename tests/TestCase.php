<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests;

use Banulakwin\EloquentColumns\EloquentColumnsServiceProvider;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            EloquentColumnsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabase($app): void
    {
        parent::defineDatabase($app);

        Schema::create('users', function ($table) {
            $table->id();
            $table->string('email');
            $table->timestamps();
        });
    }
}
