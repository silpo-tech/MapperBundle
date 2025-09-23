<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\PreLoader;

use MapperBundle\PreLoader\ODMPreLoader;
use PHPUnit\Framework\TestCase;

class ODMPreLoaderTest extends TestCase
{
    private ODMPreLoader $preLoader;

    protected function setUp(): void
    {
        $this->preLoader = new ODMPreLoader();
    }

    public function testPreLoadThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->preLoader->preLoad([], []);
    }
}
