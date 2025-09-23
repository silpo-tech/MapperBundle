<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\PreLoader;

use MapperBundle\PreLoader\NullPreLoader;
use PHPUnit\Framework\TestCase;

class NullPreLoaderTest extends TestCase
{
    private NullPreLoader $preLoader;

    protected function setUp(): void
    {
        $this->preLoader = new NullPreLoader();
    }

    public function testPreLoadReturnsSameCollection(): void
    {
        $sourceCollection = [1, 2, 3, 4];
        $registeredMappingOperations = [];

        $result = $this->preLoader->preLoad($sourceCollection, $registeredMappingOperations);

        $this->assertSame($sourceCollection, $result);
    }
}
