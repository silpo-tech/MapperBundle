<?php

declare(strict_types=1);

namespace MapperBundle\PreLoader;

interface PreloaderInterface
{
    public function preLoad($sourceCollection, array $registeredMappingOperations): array;
}
