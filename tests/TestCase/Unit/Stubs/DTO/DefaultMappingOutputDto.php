<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Stubs\DTO;

use DateTime;
use DateTimeInterface;

final class DefaultMappingOutputDto
{
    /**
     * @param DefaultMappingOutputDto[] $collection
     */
    public function __construct(
        public DefaultMappingOutputDto|null $object,
        public array $collection,
        public DateTime $createdAt,
        public DateTimeInterface $updatedAt,
    ) {
    }
}
