<?php

declare(strict_types=1);

namespace MapperBundle\Configuration;

use AutoMapperPlus\Configuration\AutoMapperConfig as BaseAutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;

class AutoMapperConfig extends BaseAutoMapperConfig implements AutoMapperConfigInterface
{
    private bool $usePreLoad = false;

    /**
     * The parent caches nothing and scans every mapping when the source is a subclass or a Doctrine proxy.
     *
     * @var array<string, MappingInterface|null>
     */
    private array $resolvedMappings = [];

    private int $resolvedOptionsState = -1;

    public function __construct(?callable $configurator = null)
    {
        parent::__construct($configurator);
    }

    #[\Override]
    public function getMappingFor(string $sourceClassName, string $destinationClassName): ?MappingInterface
    {
        $this->dropResolvedMappingsOnOptionsChange();

        $key = "$sourceClassName|$destinationClassName";
        if (array_key_exists($key, $this->resolvedMappings)) {
            return $this->resolvedMappings[$key];
        }

        $mapping = parent::getMappingFor($sourceClassName, $destinationClassName);
        $this->resolvedMappings[$key] = $mapping;

        return $mapping;
    }

    #[\Override]
    public function registerMapping(string $sourceClassName, string $destinationClassName): MappingInterface
    {
        $mapping = parent::registerMapping($sourceClassName, $destinationClassName);
        $this->resolvedMappings = [];

        return $mapping;
    }

    public function usePreLoad(): bool
    {
        return $this->usePreLoad;
    }

    public function setUsePreLoad(bool $usePreLoad): self
    {
        $this->usePreLoad = $usePreLoad;

        return $this;
    }

    // Options are mutable after construction and change what the parent resolves.
    private function dropResolvedMappingsOnOptionsChange(): void
    {
        $options = $this->getOptions();
        $state = ($options->shouldUseSubstitution() ? 1 : 0) | ($options->shouldCreateUnregisteredMappings() ? 2 : 0);

        if ($state !== $this->resolvedOptionsState) {
            $this->resolvedMappings = [];
            $this->resolvedOptionsState = $state;
        }
    }
}
