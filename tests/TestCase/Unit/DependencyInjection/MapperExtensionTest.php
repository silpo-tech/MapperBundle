<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\DependencyInjection;

use MapperBundle\DependencyInjection\MapperExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MapperExtensionTest extends TestCase
{
    public function testServicesYamlIsLoaded(): void
    {
        $container = new ContainerBuilder();

        $extension = new MapperExtension();
        $extension->load([], $container);

        $this->assertTrue($container->has('mapper.preloader'));
        $this->assertSame(
            'MapperBundle\PreLoader\NullPreLoader',
            $container->getDefinition('mapper.preloader')->getClass()
        );
    }
}
