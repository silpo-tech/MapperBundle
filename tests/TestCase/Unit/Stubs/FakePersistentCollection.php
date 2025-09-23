<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Stubs;

use MapperBundle\Tests\TestCase\Unit\Stubs\Entity\SourceEntity;
use MapperBundle\Tests\TestCase\Unit\Stubs\Entity\TargetEntity;

class FakePersistentCollection implements \IteratorAggregate
{
    public array $values = [];
    public bool $wasInitialized = false;
    public bool $snapshotTaken = false;
    private SourceEntity $owner;

    public function __construct(SourceEntity $owner)
    {
        $this->owner = $owner;
    }

    public function getOwner(): object
    {
        return $this->owner;
    }

    public function isInitialized(): bool
    {
        return false;
    }

    public function getMapping(): array
    {
        return [
            'sourceEntity' => SourceEntity::class,
            'fieldName' => 'related',
            'mappedBy' => 'owner',
            'targetEntity' => TargetEntity::class,
        ];
    }

    public function add($value): void
    {
        $this->values[] = $value;
    }

    public function setInitialized(bool $val): void
    {
        $this->wasInitialized = $val;
    }

    public function takeSnapshot(): void
    {
        $this->snapshotTaken = true;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator([]);
    }
}
