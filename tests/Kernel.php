<?php

declare(strict_types=1);

namespace MapperBundle\Tests;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperPlusBundle;
use MapperBundle\MapperBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    use MicroKernelTrait;

    public function getConfigDir(): string
    {
        return $this->getProjectDir() . '/src/Resources/config';
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/MapperBundle/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/MapperBundle/logs';
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new AutoMapperPlusBundle(),
            new MapperBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('kernel.secret', 'test');
            $container->loadFromExtension('framework', [
                'test' => true,
                'secret' => 'test',
            ]);
        });
    }

    public function shutdown(): void
    {
        parent::shutdown();

        $cacheDirectory = $this->getCacheDir();
        $logDirectory = $this->getLogDir();

        $filesystem = new Filesystem();

        if ($filesystem->exists($cacheDirectory)) {
            $filesystem->remove($cacheDirectory);
        }

        if ($filesystem->exists($logDirectory)) {
            $filesystem->remove($logDirectory);
        }
    }
}

