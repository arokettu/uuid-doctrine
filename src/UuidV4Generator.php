<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidFactory;

final class UuidV4Generator extends AbstractGenerator
{
    public function generateUuid(): Uuid
    {
        return UuidFactory::v4();
    }
}
