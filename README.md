# Doctrine Support for `arokettu/uuid`

[![Packagist](https://img.shields.io/packagist/v/arokettu/uuid-doctrine.svg?style=flat-square)](https://packagist.org/packages/arokettu/uuid-doctrine)
[![PHP](https://img.shields.io/packagist/php-v/arokettu/uuid-doctrine.svg?style=flat-square)](https://packagist.org/packages/arokettu/uuid-doctrine)
[![License](https://img.shields.io/packagist/l/arokettu/uuid-doctrine.svg?style=flat-square)](LICENSE.md)
[![Gitlab pipeline status](https://img.shields.io/gitlab/pipeline/sandfox/uuid-doctrine/master.svg?style=flat-square)](https://gitlab.com/sandfox/uuid-doctrine/-/pipelines)
[![Codecov](https://img.shields.io/codecov/c/gl/sandfox/uuid-doctrine?style=flat-square)](https://codecov.io/gl/sandfox/uuid-doctrine/)

[``arokettu/uuid``](https://sandfox.dev/php/uuid.html) row classes and ID generators for Doctrine.

## Usage

```php
<?php

use Arokettu\Uuid\Doctrine\{UuidType,UuidV4Generator};
use Arokettu\Uuid\Uuid;
use Doctrine\ORM\Mapping\{Column,CustomIdGenerator,Entity,GeneratedValue,Id,Table};

#[Entity, Table(name: 'uuid_object')]
class UuidObject
{
    #[Column(type: UuidType::NAME)]
    #[Id, GeneratedValue(strategy: 'CUSTOM'), CustomIdGenerator(UuidV4Generator::class)]
    public Uuid $id;

    #[Column(type: UuidType::NAME)]
    public Uuid $uuidString;
}
```

## Installation

```bash
composer require arokettu/uuid-doctrine
```

## Documentation

Read full documentation for the base library here: <https://sandfox.dev/php/uuid.html>

Also on Read the Docs: <https://arokettu-uuid.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/php-uuid/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [MIT License](LICENSE.md).
