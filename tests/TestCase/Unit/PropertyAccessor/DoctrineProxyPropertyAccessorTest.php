<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\PropertyAccessor;

use Doctrine\Persistence\Proxy;
use MapperBundle\PropertyAccessor\DoctrineProxyPropertyAccessor;
use PHPUnit\Framework\TestCase;

class DoctrineProxyPropertyAccessorTest extends TestCase
{
    private DoctrineProxyPropertyAccessor $accessor;

    protected function setUp(): void
    {
        $this->accessor = new DoctrineProxyPropertyAccessor();
    }

    public function testGetPropertyWithProxy(): void
    {
        $proxy = $this->createMock(Proxy::class);
        $proxy->expects($this->once())->method('__load');
        $proxy->name = 'John';

        $result = $this->accessor->getProperty($proxy, 'name');

        $this->assertSame('John', $result);
    }

    public function testGetPropertyWithoutProxy(): void
    {
        $object = new class {
            public string $name = 'Doe';
        };

        $result = $this->accessor->getProperty($object, 'name');

        $this->assertSame('Doe', $result);
    }
}
