<?php

declare(strict_types=1);

namespace MapperBundle\PreLoader;

readonly class ODMPreLoader implements PreloaderInterface
{
    // doesn't support yet
    public function preLoad($sourceCollection, array $registeredMappingOperations): array
    {
        throw new \RuntimeException('ODM preloader doesn\'t support yet');
    }
}
