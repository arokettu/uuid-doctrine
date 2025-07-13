<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine\Tests;

use Arokettu\Uuid\Doctrine\UlidBinaryType;
use Arokettu\Uuid\Ulid;
use Arokettu\Uuid\UuidParser;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\SerializationFailed;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use PHPUnit\Framework\TestCase;

final class UlidBinaryTypeTest extends TestCase
{
    public function testBindingType(): void
    {
        $type = new UlidBinaryType();

        self::assertEquals(ParameterType::BINARY, $type->getBindingType());
    }

    public function testCreation(): void
    {
        $type = new UlidBinaryType();

        $sql = [
            [new SQLitePlatform(), 'BLOB'],
            [new MySQLPlatform(), 'BINARY(16)'],
            [new MySQL80Platform(), 'BINARY(16)'],
            [new PostgreSQLPlatform(), 'BYTEA'],
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
        $platform = new SQLitePlatform();

        $ulid = '01H53P0ZMJJ9T3KE0595T5BXTV';
        $ulidBin = hex2bin('018947607e92927439b805497455f75b');
        $ulidStream = fopen('php://temp', 'r+');
        fwrite($ulidStream, $ulidBin);
        rewind($ulidStream);

        self::assertNull($type->convertToPHPValue(null, $platform));

        $ulidObj = $type->convertToPHPValue($ulidBin, $platform);
        self::assertInstanceOf(Ulid::class, $ulidObj);
        self::assertEquals($ulid, $ulidObj->toString());

        $ulidObj = $type->convertToPHPValue($ulidStream, $platform);
        self::assertInstanceOf(Ulid::class, $ulidObj);
        self::assertEquals($ulid, $ulidObj->toString());
    }

    public function testDbToPHPWrongType(): void
    {
        $type = new UlidBinaryType();
        $platform = new SQLitePlatform();

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "arokettu_ulid_blob" ' .
            'as an error was triggered by the unserialization: ' .
            'Not a valid UUID or ULID representation',
        );
        $type->convertToPHPValue(123, $platform);
    }

    public function testDbToPHPWrongLength(): void
    {
        $type = new UlidBinaryType();
        $platform = new SQLitePlatform();

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "arokettu_ulid_blob" ' .
            'as an error was triggered by the unserialization: ' .
            'Not a valid UUID or ULID representation',
        );
        $type->convertToPHPValue('123456789012345', $platform);
    }

    public function testDbToPHPWrongLengthStreamTooShort(): void
    {
        $type = new UlidBinaryType();
        $platform = new SQLitePlatform();

        $ulidStream = fopen('php://temp', 'r+');
        fwrite($ulidStream, '123456789012');
        rewind($ulidStream);

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "arokettu_ulid_blob" ' .
            'as an error was triggered by the unserialization: ' .
            'Not a valid UUID or ULID representation',
        );
        $type->convertToPHPValue($ulidStream, $platform);
    }

    public function testDbToPHPWrongLengthStreamTooLong(): void
    {
        $type = new UlidBinaryType();
        $platform = new SQLitePlatform();

        $ulidStream = fopen('php://temp', 'r+');
        fwrite($ulidStream, '1234567890123456789');
        rewind($ulidStream);

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "arokettu_ulid_blob" ' .
            'as an error was triggered by the unserialization: ' .
            'Not a valid UUID or ULID representation',
        );
        $type->convertToPHPValue($ulidStream, $platform);
    }

    public function testPHPToDb(): void
    {
        $type = new UlidBinaryType();
        $platform = new SQLitePlatform();

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
        $platform = new SQLitePlatform();

        $this->expectException(InvalidType::class);
        $this->expectExceptionMessage(
            'Could not convert PHP value 123 to type arokettu_ulid_blob. ' .
            'Expected one of the following types: null, string, Arokettu\Uuid\Uuid.',
        );
        $type->convertToDatabaseValue(123, $platform);
    }

    public function testPHPToDbWrongFormat(): void
    {
        $type = new UlidBinaryType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "string" to "arokettu_ulid_blob". ' .
            'An error was triggered by the serialization: Not a valid UUID or ULID representation',
        );
        $type->convertToDatabaseValue('U1H53P0ZMJJ9T3KE0595T5BXTV', $platform);
    }
}
