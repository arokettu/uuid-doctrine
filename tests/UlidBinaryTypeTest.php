<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine\Tests;

use Arokettu\Uuid\Doctrine\UlidBinaryType;
use Arokettu\Uuid\Ulid;
use Arokettu\Uuid\UuidParser;
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

class UlidBinaryTypeTest extends TestCase
{
    public function testName(): void
    {
        $type = new UlidBinaryType();

        self::assertEquals($type::NAME, $type->getName());
    }

    public function testRequireComment(): void
    {
        $type = new UlidBinaryType();
        $platform = new SqlitePlatform();

        self::assertTrue($type->requiresSQLCommentHint($platform));
    }

    public function testCreation(): void
    {
        $type = new UlidBinaryType();

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
        $type = new UlidBinaryType();
        $platform = new SqlitePlatform();

        $uuid = '01H53P0ZMJJ9T3KE0595T5BXTV';
        $uuidBin = hex2bin('018947607e92927439b805497455f75b');

        self::assertNull($type->convertToPHPValue(null, $platform));

        $uuidObj = $type->convertToPHPValue($uuidBin, $platform);
        self::assertInstanceOf(Ulid::class, $uuidObj);
        self::assertEquals($uuid, $uuidObj->toString());
    }

    public function testDbToPHPWrongType(): void
    {
        $type = new UlidBinaryType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert database value to 'arokettu_ulid_blob' " .
            "as an error was triggered by the unserialization: " .
            "'Not a valid UUID or ULID representation'"
        );
        $type->convertToPHPValue(123, $platform);
    }

    public function testDbToPHPWrongFormat(): void
    {
        $type = new UlidBinaryType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert database value to 'arokettu_ulid_blob' " .
            "as an error was triggered by the unserialization: " .
            "'Not a valid UUID or ULID representation'"
        );
        $type->convertToPHPValue('U1H53P0ZMJJ9T3KE0595T5BXTV', $platform);
    }

    public function testPHPToDb(): void
    {
        $type = new UlidBinaryType();
        $platform = new SqlitePlatform();

        $uuid = '01H53P0ZMJJ9T3KE0595T5BXTV';
        $uuidBin = hex2bin('018947607e92927439b805497455f75b');
        $uuidObj = UuidParser::fromBase32($uuid);
        $stringable = new class () {
            public function __toString(): string
            {
                return '01H53P0ZMJJ9T3KE0595T5BXTV';
            }
        };

        self::assertEquals($uuidBin, $type->convertToDatabaseValue($uuid, $platform));
        self::assertEquals($uuidBin, $type->convertToDatabaseValue($uuidObj, $platform));
        self::assertEquals($uuidBin, $type->convertToDatabaseValue($stringable, $platform));

        self::assertNull($type->convertToDatabaseValue(null, $platform));
    }

    public function testPHPToDbWrongType(): void
    {
        $type = new UlidBinaryType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert PHP value 123 to type arokettu_ulid_blob. " .
            "Expected one of the following types: null, string, Arokettu\Uuid\Uuid"
        );
        $type->convertToDatabaseValue(123, $platform);
    }

    public function testPHPToDbWrongFormat(): void
    {
        $type = new UlidBinaryType();
        $platform = new SqlitePlatform();

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Could not convert PHP type 'string' to 'arokettu_ulid_blob', " .
            "as an 'Not a valid UUID or ULID representation' error was triggered by the serialization"
        );
        $type->convertToDatabaseValue('U1H53P0ZMJJ9T3KE0595T5BXTV', $platform);
    }
}
