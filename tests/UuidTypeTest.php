<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine\Tests;

use Arokettu\Uuid\Doctrine\UuidType;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV4;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;

class UuidTypeTest extends TestCase
{
    public function testName(): void
    {
        $type = new UuidType();

        self::assertEquals($type::NAME, $type->getName());
    }

    public function testRequireComment(): void
    {
        $type = new UuidType();
        $platform = new SqlitePlatform();

        self::assertTrue($type->requiresSQLCommentHint($platform));
    }

    public function testCreation(): void
    {
        $type = new UuidType();

        $sql = [
            [new SqlitePlatform(), 'CHAR(36)'],
            [new MySQLPlatform(), 'CHAR(36)'],
            [new MySQL80Platform(), 'CHAR(36)'],
            [new PostgreSQLPlatform(), 'UUID'],
            [new PostgreSQL94Platform(), 'UUID'],
            [new PostgreSQL100Platform(), 'UUID'],
            [new MariaDBPlatform(), 'CHAR(36)'],
            [new SQLServerPlatform(), 'UNIQUEIDENTIFIER'],
            [new OraclePlatform(), 'CHAR(36)'],
        ];

        $column = ['name' => 'test_test'];

        foreach ($sql as [$platform, $query]) {
            self::assertEquals($query, $type->getSQLDeclaration($column, $platform), $platform::class);
        }
    }

    public function testDbToPHP(): void
    {
        $type = new UuidType();
        $platform = new SqlitePlatform();

        $uuid = '03e52e6e-5f9b-4630-ad97-864e0a0661a3';

        self::assertNull($type->convertToPHPValue(null, $platform));

        $uuidObj = $type->convertToPHPValue($uuid, $platform);
        self::assertInstanceOf(UuidV4::class, $uuidObj);
        self::assertEquals($uuid, $uuidObj->toString());
    }

    public function testDbToPHPWrongType(): void
    {
        $type = new UuidType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert database value to 'arokettu_uuid' as an error was triggered by the unserialization: " .
            "'Not a valid UUID or ULID representation'"
        );
        $type->convertToPHPValue(123, $platform);
    }

    public function testDbToPHPWrongFormat(): void
    {
        $type = new UuidType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert database value to 'arokettu_uuid' as an error was triggered by the unserialization: " .
            "'Not a valid UUID or ULID representation'"
        );
        $type->convertToPHPValue('z3e52e6e-5f9b-4630-ad97-864e0a0661a3', $platform);
    }

    public function testPHPToDb(): void
    {
        $type = new UuidType();
        $platform = new SqlitePlatform();

        $uuid = '03e52e6e-5f9b-4630-ad97-864e0a0661a3';
        $uuidObj = UuidParser::fromRfc4122($uuid);
        $stringable = new class () {
            public function __toString(): string
            {
                return '03e52e6e-5f9b-4630-ad97-864e0a0661a3';
            }
        };

        self::assertEquals($uuid, $type->convertToDatabaseValue($uuid, $platform));
        self::assertEquals($uuid, $type->convertToDatabaseValue($uuidObj, $platform));
        self::assertEquals($uuid, $type->convertToDatabaseValue($stringable, $platform));

        self::assertNull($type->convertToDatabaseValue(null, $platform));
    }

    public function testPHPToDbWrongType(): void
    {
        $type = new UuidType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert PHP value 123 to type arokettu_uuid. " .
            "Expected one of the following types: null, string, Arokettu\Uuid\Uuid"
        );
        $type->convertToDatabaseValue(123, $platform);
    }

    public function testPHPToDbWrongFormat(): void
    {
        $type = new UuidType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert PHP type 'string' to 'arokettu_uuid', " .
            "as an 'Not a valid UUID or ULID representation' error was triggered by the serialization"
        );
        $type->convertToDatabaseValue('z3e52e6e-5f9b-4630-ad97-864e0a0661a3', $platform);
    }
}
