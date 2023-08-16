<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidParser;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @psalm-api
 */
final class UuidBinaryType extends AbstractType
{
    public const NAME = 'arokettu_uuid_blob';

    protected function uuidToDbString(Uuid $uuid): string
    {
        return $uuid->toBytes();
    }

    protected function dbStringToUuid(string $uuid): Uuid
    {
        return UuidParser::fromBytes($uuid);
    }

    protected function externalStringToUuid(string $uuid): Uuid
    {
        return UuidParser::fromString($uuid);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 16;
        $column['fixed'] = true;
        return $platform->getBinaryTypeDeclarationSQL($column);
    }

    public function getBindingType(): ParameterType
    {
        return ParameterType::BINARY;
    }
}
