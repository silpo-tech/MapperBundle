# Rest Bundle for Symfony Framework #

[![CI](https://github.com/silpo-tech/MapperBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/silpo-tech/MapperBundle/actions)
[![codecov](https://codecov.io/gh/silpo-tech/MapperBundle/graph/badge.svg)](https://codecov.io/gh/silpo-tech/MapperBundle)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Installation

The suggested installation method's via [composer](https://getcomposer.org/):

```sh
composer require silpo-tech/mapper-bundle
```

## Setup

Register bundle in bundles.php file.
```php
<?php

return [
    MapperBundle\MapperBundle::class => ['all' => true],
    AutoMapperPlus\AutoMapperPlusBundle\AutoMapperPlusBundle::class  => ['all' => true]
];
```

Configure mappings
```yaml
  # this config only applies to the services created by this file
  _instanceof:
    AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface:
      tags: ['automapper_plus.configurator']

  # and add to override implementation of AutoMapperConfig
  automapper_plus.configuration:
    class: MapperBundle\Configuration\AutoMapperConfig
```

## Tests ##

```shell
composer test:run
```