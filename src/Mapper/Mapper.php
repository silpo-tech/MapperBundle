<?php

namespace MapperBundle\Mapper;

use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\DataType;
use AutoMapperPlus\Exception\UnregisteredMappingException;
use AutoMapperPlus\MappingOperation\MappingOperationInterface;
use AutoMapperPlus\MappingOperation\Operation;
use MapperBundle\Configuration\AutoMapperConfig;
use MapperBundle\PreLoader\PreloaderInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;

/**
 * Class Mapper.
 */
class Mapper implements MapperInterface
{
    /**
     * @var AutoMapperInterface
     */
    private $autoMapper;

    /**
     * @var PropertyInfoExtractor
     */
    private $extractor;

    /**
     * @var PreloaderInterface
     */
    private $preLoader;

    public function __construct(
        AutoMapperInterface $autoMapper,
        PropertyInfoExtractor $extractor,
        PreloaderInterface $preLoader,
    ) {
        $this->autoMapper = $autoMapper;
        $this->extractor = $extractor;
        $this->preLoader = $preLoader;
    }

    /**
     * @param array|object        $source
     * @param array|object|string $destination
     *
     * @return array|mixed|object|null
     *
     * @throws UnregisteredMappingException
     */
    public function convert($source, $destination)
    {
        $this->autoConfiguration($source, $destination);
        if (is_object($destination)) {
            return $this->autoMapper->mapToObject($source, $destination);
        }

        return $this->autoMapper->map($source, $destination);
    }

    /**
     * @throws UnregisteredMappingException
     */
    public function convertToObject(array|object $source, string|object $destination): object
    {
        return $this->convert($source, $destination);
    }

    /**
     * @throws UnregisteredMappingException
     */
    public function convertToArray(object $source): array
    {
        return $this->convert($source, DataType::ARRAY);
    }

    public function convertCollection(iterable $sources, string $destination): iterable
    {
        if (empty($sources)) {
            return [];
        }

        $this->autoConfiguration(end($sources), $destination);

        if (
            $this->autoMapper->getConfiguration() instanceof AutoMapperConfig
            && true === $this->autoMapper->getConfiguration()->usePreLoad()
        ) {
            return $this->convertCollectionWithPreLoader($sources, $destination);
        }

        return $this->autoMapper->mapMultiple($sources, $destination);
    }

    public function convertCollectionWithPreLoader(iterable $sources, string $destination): iterable
    {
        if (empty($sources)) {
            return [];
        }

        $this->autoConfiguration(end($sources), $destination);

        $registeredMappingOperations = $this->getMappingProperties(
            true === is_array($sources) ? get_class($sources[0]) : $sources->getTypeClass()->name,
            $destination,
        );

        return $this->autoMapper->mapMultiple(
            $this->preLoader->preLoad($sources, $registeredMappingOperations),
            $destination
        );
    }

    /**
     * @param array|object        $source
     * @param array|object|string $destination
     */
    private function autoConfiguration($source, $destination): void
    {
        $destination = is_object($destination) ? get_class($destination) : $destination;
        if (
            !is_array($source)
            || $this->autoMapper->getConfiguration()->hasMappingFor('array', $destination)
        ) {
            return;
        }

        $this->createSchemaForMapping($destination);
    }

    private function createSchemaForMapping(string $destination): void
    {
        $config = $this->autoMapper->getConfiguration();
        if (null !== $config->getMappingFor(DataType::ARRAY, $destination)) {
            return;
        }
        $mapping = $config->registerMapping('array', $destination);
        $props = $this->extractor->getProperties($destination);
        foreach ($props as $property) {
            /** @var Type $propertyInfo */
            $types = $this->extractor->getTypes($destination, $property);
            if (!$types) {
                continue;
            }
            $propertyInfo = $types[0];
            $innerClass = false;
            if ($types = $propertyInfo->getCollectionValueTypes()) {
                $innerClass = $types[0]->getClassName();
                $this->createSchemaForMapping($innerClass);
                $mapping->forMember($property, Operation::mapTo($innerClass));
            } elseif (is_a($propertyInfo->getClassName(), \DateTimeInterface::class, true)) {
                $innerClass = $propertyInfo->getClassName();
                $mapping->forMember($property, $this->getDateTimeMappingOperation($property, $innerClass));
            } elseif ('object' === $propertyInfo->getBuiltinType()) {
                $innerClass = $propertyInfo->getClassName();
                $this->createSchemaForMapping($innerClass);
                $mapping->forMember($property, Operation::mapTo($innerClass, true));
            }
        }
    }

    /**
     * @return MappingOperationInterface[]
     */
    private function getMappingProperties(string $sourceClass, string $destinationClass): array
    {
        $mapping = $this->autoMapper->getConfiguration()->getMappingFor($sourceClass, $destinationClass);

        return $mapping->getRegisteredMappingOperations();
    }

    private function getDateTimeMappingOperation(string $property, string $destinationClass): callable
    {
        return function ($source) use ($destinationClass, $property) {
            if (null === $source[$property]) {
                return null;
            }

            return \DateTimeImmutable::class === $destinationClass
                ? new \DateTimeImmutable($source[$property])
                : new \DateTime($source[$property]);
        };
    }
}
