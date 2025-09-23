<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Stubs;

use MapperBundle\Tests\TestCase\Unit\Stubs\Entity\SourceEntity;
use MapperBundle\Tests\TestCase\Unit\Stubs\Entity\TargetEntity;

class FakePersistentCollectionWithNullMappedBy extends FakePersistentCollection
{
    public function getMapping(): array
    {
        return [
            'sourceEntity' => SourceEntity::class,
            'fieldName' => 'related',
            'mappedBy' => null,
            'targetEntity' => TargetEntity::class,
        ];
    }
}
