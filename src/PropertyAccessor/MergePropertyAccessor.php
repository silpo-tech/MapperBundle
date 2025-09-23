<?php

namespace MapperBundle\PropertyAccessor;

class MergePropertyAccessor extends PropertyAccessor
{
    /**
     * @inheritdoc
     */
    public function setProperty($object, string $propertyName, $value): void
    {
        if (null !== $value) {
            parent::setProperty($object, $propertyName, $value);
        }
    }
}
