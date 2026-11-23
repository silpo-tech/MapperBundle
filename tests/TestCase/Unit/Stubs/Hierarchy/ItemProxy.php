<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Stubs\Hierarchy;

use Doctrine\Persistence\Proxy;

class ItemProxy extends Item implements Proxy
{
    public function __load(): void
    {
    }

    public function __isInitialized(): bool
    {
        return true;
    }
}
