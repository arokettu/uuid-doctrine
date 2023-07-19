<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

use function Arokettu\IsResource\try_get_resource_type;

abstract class AbstractType extends Type
{
    public const NAME = '';

    abstract protected function uuidToDbString(Uuid $uuid, AbstractPlatform $platform): string;
    abstract protected function dbStringToUuid(string $uuid, AbstractPlatform $platform): Uuid;
    abstract protected function externalStringToUuid(string $uuid): Uuid;

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Uuid
    {
        if ($value === null || $value instanceof Uuid) {
            return $value;
        }

        if (try_get_resource_type($value) === 'stream') {
            // Read 17 bytes. If the  steam is longer than 16 bytes, crash, no need to read it whole
            $value = stream_get_contents($value, 17);
        }

        try {
            return $this->dbStringToUuid((string)$value, $platform);
        } catch (\TypeError | \UnexpectedValueException) {
            throw ConversionException::conversionFailedUnserialization(
                static::NAME,
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
            } catch (\TypeError | \UnexpectedValueException $e) {
                throw ConversionException::conversionFailedSerialization(
                    $value,
                    static::NAME,
                    'Not a valid UUID or ULID representation',
                    $e
                );
            }
        }

        if ($value instanceof Uuid) {
            return $this->uuidToDbString($value, $platform);
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
