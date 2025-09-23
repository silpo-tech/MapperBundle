<?php

declare(strict_types=1);

namespace MapperBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntityPreLoaderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (true === $container->hasDefinition('doctrine')) {
            $container
                ->findDefinition('mapper.preloader')
                ->setClass('MapperBundle\PreLoader\ORMPreLoader')
            ;

            return;
        }

        if (true === $container->hasDefinition('doctrine_mongodb')) {
            $container
                ->findDefinition('mapper.preloader')
                ->setClass('MapperBundle\PreLoader\ODMPreLoader')
            ;
        }
    }
}
