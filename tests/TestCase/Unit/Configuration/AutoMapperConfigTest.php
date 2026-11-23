<?php

declare(strict_types=1);

namespace MapperBundle\Tests\TestCase\Unit\Configuration;

use AutoMapperPlus\Configuration\AutoMapperConfig as BaseAutoMapperConfig;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use AutoMapperPlus\Configuration\MappingInterface;
use MapperBundle\Configuration\AutoMapperConfig;
use MapperBundle\Tests\TestCase\Unit\Stubs\Configuration\ScanCountingAutoMapperConfig;
use MapperBundle\Tests\TestCase\Unit\Stubs\Hierarchy\AbstractItem;
use MapperBundle\Tests\TestCase\Unit\Stubs\Hierarchy\BaseDto;
use MapperBundle\Tests\TestCase\Unit\Stubs\Hierarchy\Item;
use MapperBundle\Tests\TestCase\Unit\Stubs\Hierarchy\ItemDto;
use MapperBundle\Tests\TestCase\Unit\Stubs\Hierarchy\ItemInterface;
use MapperBundle\Tests\TestCase\Unit\Stubs\Hierarchy\ItemProxy;
use MapperBundle\Tests\TestCase\Unit\Stubs\Hierarchy\OtherDto;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AutoMapperConfigTest extends TestCase
{
    public function testUsePreLoadDefaultIsFalse(): void
    {
        $config = new AutoMapperConfig();

        $this->assertFalse($config->usePreLoad(), 'Expected default usePreLoad to be false');
    }

    public function testSetUsePreLoadReturnsSelfAndSetsValue(): void
    {
        $config = new AutoMapperConfig();

        $result = $config->setUsePreLoad(true);

        $this->assertTrue($config->usePreLoad(), 'Expected usePreLoad to be true after setting');
        $this->assertSame($config, $result, 'Expected setUsePreLoad to return same instance');
    }

    #[DataProvider('providerLookupOk')]
    public function testGetMappingForReturnsWhatTheUncachedConfigReturns(
        array $data = [],
        array $conditions = [],
        array $expected = [],
    ): void {
        $config = new AutoMapperConfig();
        $uncached = new BaseAutoMapperConfig();
        $this->registerAll($config, $data['registrations']);
        $this->registerAll($uncached, $data['registrations']);

        foreach ($data['lookups'] as $index => [$source, $destination]) {
            $first = $config->getMappingFor($source, $destination);
            $second = $config->getMappingFor($source, $destination);

            $this->assertSame($expected[$index], $this->describe($first), "Lookup #$index: unexpected mapping");
            $this->assertSame($this->describe($uncached->getMappingFor($source, $destination)), $this->describe($first));
            $this->assertSame($first, $second, "Lookup #$index: the repeated lookup returned another instance");
        }
    }

    public static function providerLookupOk(): iterable
    {
        yield 'subclass gets the mapping of its parent' => [
            'data' => [
                'registrations' => [[AbstractItem::class, ItemDto::class]],
                'lookups' => [[Item::class, ItemDto::class]],
            ],
            'conditions' => [],
            'expected' => [[AbstractItem::class, ItemDto::class]],
        ];

        yield 'proxy gets the closest of several matching mappings' => [
            'data' => [
                'registrations' => [
                    [ItemInterface::class, ItemDto::class],
                    [AbstractItem::class, ItemDto::class],
                    [Item::class, ItemDto::class],
                    [AbstractItem::class, BaseDto::class],
                ],
                'lookups' => [[ItemProxy::class, ItemDto::class]],
            ],
            'conditions' => [],
            'expected' => [[Item::class, ItemDto::class]],
        ];

        yield 'same source with different destinations' => [
            'data' => [
                'registrations' => [
                    [ItemInterface::class, ItemDto::class],
                    [AbstractItem::class, ItemDto::class],
                    [Item::class, ItemDto::class],
                    [AbstractItem::class, BaseDto::class],
                ],
                'lookups' => [
                    [ItemProxy::class, ItemDto::class],
                    [ItemProxy::class, BaseDto::class],
                    [ItemProxy::class, OtherDto::class],
                ],
            ],
            'conditions' => [],
            'expected' => [
                [Item::class, ItemDto::class],
                [AbstractItem::class, BaseDto::class],
                null,
            ],
        ];

        yield 'equal distance, the first registered wins' => [
            'data' => [
                'registrations' => [
                    [AbstractItem::class, ItemDto::class],
                    [Item::class, BaseDto::class],
                ],
                'lookups' => [[ItemProxy::class, ItemDto::class]],
            ],
            'conditions' => [],
            'expected' => [[AbstractItem::class, ItemDto::class]],
        ];

        yield 'equal distance, the first registered wins, reverse order' => [
            'data' => [
                'registrations' => [
                    [Item::class, BaseDto::class],
                    [AbstractItem::class, ItemDto::class],
                ],
                'lookups' => [[ItemProxy::class, ItemDto::class]],
            ],
            'conditions' => [],
            'expected' => [[Item::class, BaseDto::class]],
        ];

        yield 'exact mapping beats its parent' => [
            'data' => [
                'registrations' => [
                    [AbstractItem::class, ItemDto::class],
                    [ItemProxy::class, ItemDto::class],
                ],
                'lookups' => [[ItemProxy::class, ItemDto::class]],
            ],
            'conditions' => [],
            'expected' => [[ItemProxy::class, ItemDto::class]],
        ];

        yield 'parent of the registered class has no mapping' => [
            'data' => [
                'registrations' => [[Item::class, ItemDto::class]],
                'lookups' => [[AbstractItem::class, ItemDto::class]],
            ],
            'conditions' => [],
            'expected' => [null],
        ];
    }

    #[DataProvider('providerScans')]
    public function testRepeatedLookupDoesNotScanMappingsAgain(
        array $data = [],
        array $conditions = [],
        array $expected = [],
    ): void {
        $config = new ScanCountingAutoMapperConfig();
        $this->registerAll($config, $data['registrations']);

        foreach ($data['lookups'] as [$source, $destination]) {
            $config->getMappingFor($source, $destination);
        }

        $this->assertSame($expected['scans'], $config->scanCount);
    }

    public static function providerScans(): iterable
    {
        yield 'proxy resolved through a parent mapping' => [
            'data' => [
                'registrations' => [[AbstractItem::class, ItemDto::class]],
                'lookups' => [
                    [ItemProxy::class, ItemDto::class],
                    [ItemProxy::class, ItemDto::class],
                    [ItemProxy::class, ItemDto::class],
                ],
            ],
            'conditions' => [],
            'expected' => ['scans' => 1],
        ];

        yield 'pair without a mapping' => [
            'data' => [
                'registrations' => [[Item::class, ItemDto::class]],
                'lookups' => [
                    [ItemProxy::class, OtherDto::class],
                    [ItemProxy::class, OtherDto::class],
                ],
            ],
            'conditions' => [],
            'expected' => ['scans' => 1],
        ];

        yield 'each pair is scanned once' => [
            'data' => [
                'registrations' => [
                    [AbstractItem::class, ItemDto::class],
                    [AbstractItem::class, BaseDto::class],
                ],
                'lookups' => [
                    [ItemProxy::class, ItemDto::class],
                    [ItemProxy::class, BaseDto::class],
                    [ItemProxy::class, ItemDto::class],
                    [ItemProxy::class, BaseDto::class],
                ],
            ],
            'conditions' => [],
            'expected' => ['scans' => 2],
        ];
    }

    #[DataProvider('providerRegisterMappingResets')]
    public function testRegisterMappingDropsResolvedMappings(
        array $data = [],
        array $conditions = [],
        array $expected = [],
    ): void {
        $config = new AutoMapperConfig();
        $this->registerAll($config, $data['registrations']);
        [$source, $destination] = $data['lookup'];

        $this->assertSame($expected['before'], $this->describe($config->getMappingFor($source, $destination)));

        $registered = $config->registerMapping(...$data['registerLater']);

        $after = $config->getMappingFor($source, $destination);
        $this->assertSame($expected['after'], $this->describe($after));
        $this->assertSame($registered, $after);
    }

    public static function providerRegisterMappingResets(): iterable
    {
        yield 'closer mapping registered' => [
            'data' => [
                'registrations' => [[AbstractItem::class, ItemDto::class]],
                'lookup' => [ItemProxy::class, ItemDto::class],
                'registerLater' => [Item::class, ItemDto::class],
            ],
            'conditions' => [],
            'expected' => [
                'before' => [AbstractItem::class, ItemDto::class],
                'after' => [Item::class, ItemDto::class],
            ],
        ];

        yield 'mapping registered for a pair that had none' => [
            'data' => [
                'registrations' => [],
                'lookup' => [ItemProxy::class, ItemDto::class],
                'registerLater' => [AbstractItem::class, ItemDto::class],
            ],
            'conditions' => [],
            'expected' => [
                'before' => null,
                'after' => [AbstractItem::class, ItemDto::class],
            ],
        ];

        yield 'mapping registered again under the same key' => [
            'data' => [
                'registrations' => [[Item::class, ItemDto::class]],
                'lookup' => [ItemProxy::class, ItemDto::class],
                'registerLater' => [Item::class, ItemDto::class],
            ],
            'conditions' => [],
            'expected' => [
                'before' => [Item::class, ItemDto::class],
                'after' => [Item::class, ItemDto::class],
            ],
        ];
    }

    public function testReverseMapIsTakenIntoAccountByTheNextLookup(): void
    {
        $config = new AutoMapperConfig();
        $mapping = $config->registerMapping(Item::class, ItemDto::class);

        $this->assertNull($config->getMappingFor(ItemDto::class, Item::class));

        $reverse = $mapping->reverseMap();

        $this->assertSame($reverse, $config->getMappingFor(ItemDto::class, Item::class));
    }

    #[DataProvider('providerOptionChanges')]
    public function testOptionChangeAfterLookupIsTakenIntoAccount(
        array $data = [],
        array $conditions = [],
        array $expected = [],
    ): void {
        $config = new AutoMapperConfig();
        $uncached = new BaseAutoMapperConfig();
        $this->registerAll($config, $data['registrations']);
        $this->registerAll($uncached, $data['registrations']);
        [$source, $destination] = $data['lookup'];

        $this->assertSame($expected['before'], $this->describe($config->getMappingFor($source, $destination)));

        $config->getOptions()->{$conditions['option']}();
        $uncached->getOptions()->{$conditions['option']}();

        $after = $this->describe($config->getMappingFor($source, $destination));
        $this->assertSame($expected['after'], $after);
        $this->assertSame($this->describe($uncached->getMappingFor($source, $destination)), $after);
    }

    public static function providerOptionChanges(): iterable
    {
        yield 'substitution disallowed' => [
            'data' => [
                'registrations' => [[AbstractItem::class, ItemDto::class]],
                'lookup' => [ItemProxy::class, ItemDto::class],
            ],
            'conditions' => ['option' => 'disallowSubstitution'],
            'expected' => [
                'before' => [AbstractItem::class, ItemDto::class],
                'after' => null,
            ],
        ];

        yield 'unregistered mappings created' => [
            'data' => [
                'registrations' => [[Item::class, ItemDto::class]],
                'lookup' => [AbstractItem::class, ItemDto::class],
            ],
            'conditions' => ['option' => 'createUnregisteredMappings'],
            'expected' => [
                'before' => null,
                'after' => [AbstractItem::class, ItemDto::class],
            ],
        ];
    }

    /**
     * @param list<array{string, string}> $registrations
     */
    private function registerAll(AutoMapperConfigInterface $config, array $registrations): void
    {
        foreach ($registrations as [$source, $destination]) {
            $config->registerMapping($source, $destination);
        }
    }

    /**
     * @return array{string, string}|null
     */
    private function describe(?MappingInterface $mapping): ?array
    {
        return null === $mapping ? null : [$mapping->getSourceClassName(), $mapping->getDestinationClassName()];
    }
}
