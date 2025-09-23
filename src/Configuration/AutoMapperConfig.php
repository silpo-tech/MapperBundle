<?php

declare(strict_types=1);

namespace MapperBundle\Configuration;

use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\AutoMapperConfig as BaseAutoMapperConfig;

class AutoMapperConfig extends BaseAutoMapperConfig implements AutoMapperConfigInterface
{
    private bool $usePreLoad = false;

    public function __construct(callable|null $configurator = null)
    {
        parent::__construct($configurator);
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
}
