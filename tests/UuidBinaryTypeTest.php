<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine\Tests;

use Arokettu\Uuid\Doctrine\UuidBinaryType;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV4;
use Doctrine\DBAL\ParameterType;
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

class UuidBinaryTypeTest extends TestCase
{
    public function testName(): void
    {
        $type = new UuidBinaryType();

        self::assertEquals($type::NAME, $type->getName());
    }

    public function testRequireComment(): void
    {
        $type = new UuidBinaryType();
        $platform = new SqlitePlatform();

        self::assertTrue($type->requiresSQLCommentHint($platform));
    }

    public function testBindingType(): void
    {
        $type = new UuidBinaryType();

        self::assertEquals(ParameterType::BINARY, $type->getBindingType());
    }

    public function testCreation(): void
    {
        $type = new UuidBinaryType();

        $sql = [
            [new SqlitePlatform(), 'BLOB'],
            [new MySQLPlatform(), 'BINARY(16)'],
            [new MySQL80Platform(), 'BINARY(16)'],
            [new PostgreSQLPlatform(), 'BYTEA'],
            [new PostgreSQL94Platform(), 'BYTEA'],
            [new PostgreSQL100Platform(), 'BYTEA'],
            [new MariaDBPlatform(), 'BINARY(16)'],
            [new SQLServerPlatform(), 'BINARY(16)'],
            [new OraclePlatform(), 'RAW(16)'],
        ];

        $column = ['name' => 'test_test'];

        foreach ($sql as [$platform, $query]) {
            self::assertEquals($query, $type->getSQLDeclaration($column, $platform), $platform::class);
        }
    }

    public function testDbToPHP(): void
    {
        $type = new UuidBinaryType();
        $platform = new SqlitePlatform();

        $uuid = '03e52e6e-5f9b-4630-ad97-864e0a0661a3';
        $uuidBin = hex2bin('03e52e6e5f9b4630ad97864e0a0661a3');

        self::assertNull($type->convertToPHPValue(null, $platform));

        $uuidObj = $type->convertToPHPValue($uuidBin, $platform);
        self::assertInstanceOf(UuidV4::class, $uuidObj);
        self::assertEquals($uuid, $uuidObj->toString());
    }

    public function testDbToPHPWrongType(): void
    {
        $type = new UuidBinaryType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert database value to 'arokettu_uuid_blob' " .
            "as an error was triggered by the unserialization: " .
            "'Not a valid UUID or ULID representation'"
        );
        $type->convertToPHPValue(123, $platform);
    }

    public function testDbToPHPWrongFormat(): void
    {
        $type = new UuidBinaryType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert database value to 'arokettu_uuid_blob' " .
            "as an error was triggered by the unserialization: " .
            "'Not a valid UUID or ULID representation'"
        );
        $type->convertToPHPValue('z3e52e6e-5f9b-4630-ad97-864e0a0661a3', $platform);
    }

    public function testPHPToDb(): void
    {
        $type = new UuidBinaryType();
        $platform = new SqlitePlatform();

        $uuid = '03e52e6e-5f9b-4630-ad97-864e0a0661a3';
        $uuidBin = hex2bin('03e52e6e5f9b4630ad97864e0a0661a3');
        $uuidObj = UuidParser::fromRfc4122($uuid);
        $stringable = new class () {
            public function __toString(): string
            {
                return '03e52e6e-5f9b-4630-ad97-864e0a0661a3';
            }
        };

        self::assertEquals($uuidBin, $type->convertToDatabaseValue($uuid, $platform));
        self::assertEquals($uuidBin, $type->convertToDatabaseValue($uuidObj, $platform));
        self::assertEquals($uuidBin, $type->convertToDatabaseValue($stringable, $platform));

        self::assertNull($type->convertToDatabaseValue(null, $platform));
    }

    public function testPHPToDbWrongType(): void
    {
        $type = new UuidBinaryType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert PHP value 123 to type arokettu_uuid_blob. " .
            "Expected one of the following types: null, string, Arokettu\Uuid\Uuid"
        );
        $type->convertToDatabaseValue(123, $platform);
    }

    public function testPHPToDbWrongFormat(): void
    {
        $type = new UuidBinaryType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert PHP type 'string' to 'arokettu_uuid_blob', " .
            "as an 'Not a valid UUID or ULID representation' error was triggered by the serialization"
        );
        $type->convertToDatabaseValue('z3e52e6e-5f9b-4630-ad97-864e0a0661a3', $platform);
    }
}