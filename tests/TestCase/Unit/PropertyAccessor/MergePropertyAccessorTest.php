<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\PropertyAccessor;

use MapperBundle\PropertyAccessor\MergePropertyAccessor;
use PHPUnit\Framework\TestCase;

class MergePropertyAccessorTest extends TestCase
{
    private MergePropertyAccessor $accessor;

    protected function setUp(): void
    {
        $this->accessor = new MergePropertyAccessor();
    }

    public function testSetPropertySkipsNullValue(): void
    {
        $object = new class {
            public string $name = 'Initial';
        };

        $this->accessor->setProperty($object, 'name', null);

        $this->assertSame('Initial', $object->name);
    }

    public function testSetPropertySetsNonNullValue(): void
    {
        $object = new class {
            public string $name = 'Initial';
        };

        $this->accessor->setProperty($object, 'name', 'Updated');

        $this->assertSame('Updated', $object->name);
    }
}
