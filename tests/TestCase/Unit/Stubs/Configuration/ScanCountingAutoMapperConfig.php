<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Stubs\Configuration;

use AutoMapperPlus\Configuration\MappingInterface;
use MapperBundle\Configuration\AutoMapperConfig;

class ScanCountingAutoMapperConfig extends AutoMapperConfig
{
    public int $scanCount = 0;

    protected function getMostSpecificCandidate(
        array $candidates,
        string $sourceClassName,
        string $destinationClassName,
    ): ?MappingInterface {
        ++$this->scanCount;

        return parent::getMostSpecificCandidate($candidates, $sourceClassName, $destinationClassName);
    }
}
