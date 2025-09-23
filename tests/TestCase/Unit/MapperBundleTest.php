<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit;

use MapperBundle\DependencyInjection\CompilerPass\EntityPreLoaderCompilerPass;
use MapperBundle\MapperBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MapperBundleTest extends TestCase
{
    public function testBuild()
    {
        $containerBuilder = new ContainerBuilder();
        $bundle = new MapperBundle();
        $bundle->build($containerBuilder);
        $passes = $containerBuilder->getCompiler()->getPassConfig()->getBeforeOptimizationPasses();

        $this->assertContains(
            EntityPreLoaderCompilerPass::class,
            array_map(static fn (CompilerPassInterface $pass) => $pass::class, $passes),
        );
    }
}
