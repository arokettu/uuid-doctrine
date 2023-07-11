<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\UlidFactory;
use Arokettu\Uuid\Uuid;

/**
 * @psalm-api
 */
final class UlidGenerator extends AbstractGenerator
{
    public function generateUuid(): Uuid
    {
        return UlidFactory::ulid();
    }
}
