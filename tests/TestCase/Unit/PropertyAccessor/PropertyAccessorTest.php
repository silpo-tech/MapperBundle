<?php

declare(strict_types=1);

namespace Tests\MapperBundle\TestCase\Unit\PropertyAccessor;

use MapperBundle\PropertyAccessor\PropertyAccessor;
use PHPUnit\Framework\TestCase;

class PropertyAccessorTest extends TestCase
{
    private PropertyAccessor $accessor;

    protected function setUp(): void
    {
        $this->accessor = new PropertyAccessor();
    }

    public function testSetPropertyWithSetter(): void
    {
        $object = new class {
            private string $name = '';

            public function setName(string $name): void
            {
                $this->name = $name;
            }

            public function getName(): string
            {
                return $this->name;
            }
        };

        $this->accessor->setProperty($object, 'name', 'John');

        $this->assertSame('John', $object->getName());
    }

    public function testSetPropertyDirectly(): void
    {
        $object = new class {
            public string $name = '';
        };

        $this->accessor->setProperty($object, 'name', 'Doe');

        $this->assertSame('Doe', $object->name);
    }

    public function testSetPropertyFallbackToParent(): void
    {
        $object = new class {
            private string $name = '';
        };

        $this->expectNotToPerformAssertions();

        try {
            $this->accessor->setProperty($object, 'name', 'John');
        } catch (\Throwable $e) {
            $this->fail("Unexpected exception thrown: " . $e->getMessage());
        }
    }
}