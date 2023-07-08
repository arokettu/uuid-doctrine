<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

abstract class AbstractType extends Type
{
    public const NAME = '';

    abstract protected function uuidToDbString(Uuid $uuid): string;
    abstract protected function dbStringToUuid(string $uuid): Uuid;
    abstract protected function externalStringToUuid(string $uuid): Uuid;

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Uuid
    {
        if ($value === null || $value instanceof Uuid) {
            return $value;
        }

        try {
            return $this->dbStringToUuid((string)$value);
        } catch (\ValueError | \UnexpectedValueException) {
            throw ConversionException::conversionFailedUnserialization(
                $value,
                'Not a valid UUID or ULID representation'
            );
        }
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (\is_string($value) || $value instanceof \Stringable) {
            try {
                $value = $this->externalStringToUuid((string)$value);
            } catch (\UnexpectedValueException $e) {
                throw ConversionException::conversionFailedSerialization(
                    $value,
                    static::NAME,
                    'Not a valid UUID or ULID representation',
                    $e
                );
            }
        }

        if ($value instanceof Uuid) {
            return $this->uuidToDbString($value);
        }

        throw ConversionException::conversionFailedInvalidType($value, static::NAME, ['null', 'string', Uuid::class]);
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
