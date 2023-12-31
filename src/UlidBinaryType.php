<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\UlidParser;
use Arokettu\Uuid\Uuid;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @psalm-api
 */
final class UlidBinaryType extends AbstractType
{
    public const NAME = 'arokettu_ulid_blob';

    protected function uuidToDbString(Uuid $uuid): string
    {
        return $uuid->toBytes();
    }

    protected function dbStringToUuid(string $uuid): Uuid
    {
        return UlidParser::fromBytes($uuid);
    }

    protected function externalStringToUuid(string $uuid): Uuid
    {
        return UlidParser::fromString($uuid);
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
