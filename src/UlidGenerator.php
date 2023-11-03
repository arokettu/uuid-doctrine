<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\SequenceFactory;
use Arokettu\Uuid\Sequences\UlidSequence;
use Arokettu\Uuid\Uuid;

/**
 * @psalm-api
 */
final class UlidGenerator extends AbstractGenerator
{
    private static UlidSequence $sequence;

    public function generateUuid(): Uuid
    {
        self::$sequence ??= SequenceFactory::ulid();
        return self::$sequence->next();
    }
}
