<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

use function Arokettu\IsResource\try_get_resource_type;

abstract class AbstractType extends Type
{
    public const NAME = '';

    abstract protected function uuidToDbString(Uuid $uuid): string;
    abstract protected function dbStringToUuid(string $uuid): Uuid;
    abstract protected function externalStringToUuid(string $uuid): Uuid;

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Uuid|null
    {
        if ($value === null || $value instanceof Uuid) {
            return $value;
        }

        if (try_get_resource_type($value) === 'stream') {
            // Read 17 bytes. If the  steam is longer than 16 bytes, crash, no need to read it whole
            $value = stream_get_contents($value, 17);
        }

        try {
            return $this->dbStringToUuid((string)$value);
        } catch (\TypeError | \UnexpectedValueException | \DomainException) {
            throw ValueNotConvertible::new(
                $value,
                static::NAME,
                'Not a valid UUID or ULID representation',
            );
        }
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string|null
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Uuid) {
            return $this->uuidToDbString($value);
        }

        if (\is_string($value) || $value instanceof \Stringable) {
            try {
                $value = $this->externalStringToUuid((string)$value);
                return $this->uuidToDbString($value);
            } catch (\TypeError | \UnexpectedValueException | \DomainException $e) {
                throw SerializationFailed::new(
                    $value,
                    static::NAME,
                    'Not a valid UUID or ULID representation',
                    $e,
                );
            }
        }

        throw InvalidType::new($value, static::NAME, ['null', 'string', Uuid::class]);
    }
}
