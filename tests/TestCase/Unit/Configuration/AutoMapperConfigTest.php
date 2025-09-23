<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Configuration;

use MapperBundle\Configuration\AutoMapperConfig;
use PHPUnit\Framework\TestCase;

class AutoMapperConfigTest extends TestCase
{
    public function testUsePreLoadDefaultIsFalse(): void
    {
        $config = new AutoMapperConfig();

        $this->assertFalse($config->usePreLoad(), 'Expected default usePreLoad to be false');
    }

    public function testSetUsePreLoadReturnsSelfAndSetsValue(): void
    {
        $config = new AutoMapperConfig();

        $result = $config->setUsePreLoad(true);

        $this->assertTrue($config->usePreLoad(), 'Expected usePreLoad to be true after setting');
        $this->assertSame($config, $result, 'Expected setUsePreLoad to return same instance');
    }
}
