<?php

declare(strict_types=1);

namespace MapperBundle\PreLoader;

readonly class NullPreLoader implements PreloaderInterface
{
    public function preLoad($sourceCollection, array $registeredMappingOperations): array
    {
        return $sourceCollection;
    }
}
