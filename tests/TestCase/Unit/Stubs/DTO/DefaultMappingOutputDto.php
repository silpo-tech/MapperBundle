<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Stubs\DTO;

final class DefaultMappingOutputDto
{
    /**
     * @param DefaultMappingOutputDto[] $collection
     */
    public function __construct(
        public ?DefaultMappingOutputDto $object,
        public array $collection,
        public \DateTime $createdAt,
        public \DateTimeInterface $updatedAt,
    ) {
    }
}
