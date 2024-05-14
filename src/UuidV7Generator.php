<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\SequenceFactory;
use Arokettu\Uuid\Sequences\UuidV7Sequence;
use Arokettu\Uuid\Sequences\UuidV7ShortSequence;
use Arokettu\Uuid\Uuid;

/**
 * @psalm-api
 */
final class UuidV7Generator extends AbstractGenerator
{
    private static UuidV7Sequence|UuidV7ShortSequence $sequence;

    public function generateUuid(): Uuid
    {
        self::$sequence ??= SequenceFactory::v7();
        return self::$sequence->next();
    }
}
