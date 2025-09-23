<?php

declare(strict_types=1);

namespace MapperBundle\PropertyAccessor;

use AutoMapperPlus\PropertyAccessor\PropertyAccessor;
use Doctrine\Persistence\Proxy;

class DoctrineProxyPropertyAccessor extends PropertyAccessor
{
    public function getProperty($object, string $propertyName)
    {
        if ($object instanceof Proxy) {
            $object->__load();
        }

        return parent::getProperty($object, $propertyName);
    }
}
