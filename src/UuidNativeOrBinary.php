<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidParser;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;

final class UuidNativeOrBinary extends AbstractType
{
    protected function uuidToDbString(Uuid $uuid, AbstractPlatform $platform): string
    {
        if ($this->hasNativeGuid($platform)) {
            return $uuid->toString();
        } else {
            return $uuid->toBytes();
        }
    }

    protected function dbStringToUuid(string $uuid, AbstractPlatform $platform): Uuid
    {
        if ($this->hasNativeGuid($platform)) {
            return UuidParser::fromRfc4122($uuid);
        } else {
            return UuidParser::fromBytes($uuid);
        }
    }

    protected function externalStringToUuid(string $uuid): Uuid
    {
        return UuidParser::fromString($uuid);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if ($this->hasNativeGuid($platform)) {
            return $platform->getGuidTypeDeclarationSQL($column);
        } else {
            $column['length'] = 16;
            $column['fixed'] = true;
            return $platform->getBinaryTypeDeclarationSQL($column);
        }
    }

    private function hasNativeGuid(AbstractPlatform $platform): bool
    {
        return
            $platform instanceof PostgreSQLPlatform ||
            $platform instanceof SQLServerPlatform;
    }
}
