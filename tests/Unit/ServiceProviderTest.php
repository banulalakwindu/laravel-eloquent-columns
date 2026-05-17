<?php

declare(strict_types=1);

namespace Banulakwin\EloquentColumns\Tests\Unit;

use Banulakwin\EloquentColumns\EloquentColumnsServiceProvider;
use Banulakwin\EloquentColumns\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_config(): void
    {
        $this->assertNotNull(config('eloquent-columns'));
        $this->assertTrue(config('eloquent-columns.register_macros'));
    }

    #[Test]
    public function it_is_discoverable(): void
    {
        $this->assertTrue(
            app()->providerIsLoaded(EloquentColumnsServiceProvider::class)
        );
    }
}
