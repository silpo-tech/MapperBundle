<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Stubs\Entity;

use MapperBundle\Tests\TestCase\Unit\Stubs\FakePersistentCollection;

class SourceEntity
{
    public int $id = 123;
    public FakePersistentCollection $related;
}
