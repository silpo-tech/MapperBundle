<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Integration;

use MapperBundle\Mapper\MapperInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class KernelTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::bootKernel();
    }

    public function testMapperServiceIsAvailable(): void
    {
        $container = $this->getContainer();

        $this->assertTrue($container->has(MapperInterface::class));
        $mapper = $container->get(MapperInterface::class);
        $this->assertInstanceOf(MapperInterface::class, $mapper);
    }
}
