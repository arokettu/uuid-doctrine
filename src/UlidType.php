<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\UlidParser;
use Arokettu\Uuid\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @psalm-api
 */
final class UlidType extends AbstractType
{
    public const NAME = 'arokettu_ulid';

    protected function uuidToDbString(Uuid $uuid): string
    {
        return $uuid->toBase32();
    }

    protected function dbStringToUuid(string $uuid): Uuid
    {
        return UlidParser::fromBase32($uuid);
    }

    protected function externalStringToUuid(string $uuid): Uuid
    {
        return UlidParser::fromString($uuid);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 26;
        $column['fixed'] = true;
        return $platform->getStringTypeDeclarationSQL($column);
    }
}
