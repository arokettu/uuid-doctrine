<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidParser;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @psalm-api
 */
final class UuidType extends AbstractType
{
    public const NAME = 'arokettu_uuid';

    protected function uuidToDbString(Uuid $uuid): string
    {
        return $uuid->toRfc4122();
    }

    protected function dbStringToUuid(string $uuid): Uuid
    {
        return UuidParser::fromRfc4122($uuid);
    }

    protected function externalStringToUuid(string $uuid): Uuid
    {
        return UuidParser::fromString($uuid);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }
}
