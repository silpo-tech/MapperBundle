<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Stubs\Entity;

class TargetEntity
{
    public SourceEntity $owner;

    public function __construct(SourceEntity $owner)
    {
        $this->owner = $owner;
    }
}
