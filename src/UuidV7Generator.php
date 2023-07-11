<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidV7MonotonicSequence;

/**
 * @psalm-api
 */
final class UuidV7Generator extends AbstractGenerator
{
    private static UuidV7MonotonicSequence $sequence;

    public function generateUuid(): Uuid
    {
        self::$sequence ??= UuidFactory::v7Sequence();
        return self::$sequence->next();
    }
}
