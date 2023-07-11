<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\UlidFactory;
use Arokettu\Uuid\UlidMonotonicSequence;
use Arokettu\Uuid\Uuid;

/**
 * @psalm-api
 */
final class UlidGenerator extends AbstractGenerator
{
    private static UlidMonotonicSequence $sequence;

    public function generateUuid(): Uuid
    {
        self::$sequence ??= UlidFactory::sequence();
        return self::$sequence->next();
    }
}
