<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine\Tests;

use Arokettu\Uuid\Doctrine\UlidType;
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

final class UlidTypeTest extends TestCase
{
    public function testBindingType(): void
    {
        $type = new UlidType();

        self::assertEquals(ParameterType::STRING, $type->getBindingType());
    }

    public function testCreation(): void
    {
        $type = new UlidType();

        $sql = [
            [new SQLitePlatform(), 'CHAR(26)'],
            [new MySQLPlatform(), 'CHAR(26)'],
            [new MySQL80Platform(), 'CHAR(26)'],
            [new PostgreSQLPlatform(), 'CHAR(26)'],
            [new MariaDBPlatform(), 'CHAR(26)'],
            [new SQLServerPlatform(), 'NCHAR(26)'],
            [new OraclePlatform(), 'CHAR(26)'],
        ];

        $column = ['name' => 'test_test'];

        foreach ($sql as [$platform, $query]) {
            self::assertEquals($query, $type->getSQLDeclaration($column, $platform), $platform::class);
        }
    }

    public function testDbToPHP(): void
    {
        $type = new UlidType();
        $platform = new SQLitePlatform();

        $uuid = '01H53P0ZMJJ9T3KE0595T5BXTV';

        self::assertNull($type->convertToPHPValue(null, $platform));

        $uuidObj = $type->convertToPHPValue($uuid, $platform);
        self::assertInstanceOf(Ulid::class, $uuidObj);
        self::assertEquals($uuid, $uuidObj->toString());
    }

    public function testDbToPHPWrongType(): void
    {
        $type = new UlidType();
        $platform = new SQLitePlatform();

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "arokettu_ulid" as an error was triggered by the unserialization: ' .
            'Not a valid UUID or ULID representation',
        );
        $type->convertToPHPValue(123, $platform);
    }

    public function testDbToPHPWrongFormat(): void
    {
        $type = new UlidType();
        $platform = new SQLitePlatform();

        $this->expectException(ValueNotConvertible::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "arokettu_ulid" as an error was triggered by the unserialization: ' .
            'Not a valid UUID or ULID representation',
        );
        $type->convertToPHPValue('U1H53P0ZMJJ9T3KE0595T5BXTV', $platform);
    }

    public function testPHPToDb(): void
    {
        $type = new UlidType();
        $platform = new SQLitePlatform();

        $uuid = '01H53P0ZMJJ9T3KE0595T5BXTV';
        $uuidObj = UuidParser::fromBase32($uuid);
        $stringable = new class () {
            public function __toString(): string
            {
                return '01H53P0ZMJJ9T3KE0595T5BXTV';
            }
        };

        self::assertEquals($uuid, $type->convertToDatabaseValue($uuid, $platform));
        self::assertEquals($uuid, $type->convertToDatabaseValue($uuidObj, $platform));
        self::assertEquals($uuid, $type->convertToDatabaseValue($stringable, $platform));

        self::assertNull($type->convertToDatabaseValue(null, $platform));
    }

    public function testPHPToDbWrongType(): void
    {
        $type = new UlidType();
        $platform = new SQLitePlatform();

        $this->expectException(InvalidType::class);
        $this->expectExceptionMessage(
            'Could not convert PHP value 123 to type arokettu_ulid. ' .
            'Expected one of the following types: null, string, Arokettu\Uuid\Uuid.',
        );
        $type->convertToDatabaseValue(123, $platform);
    }

    public function testPHPToDbWrongFormat(): void
    {
        $type = new UlidType();
        $platform = new SQLitePlatform();

        $this->expectException(SerializationFailed::class);
        $this->expectExceptionMessage(
            'Could not convert PHP type "string" to "arokettu_ulid". ' .
            'An error was triggered by the serialization: Not a valid UUID or ULID representation',
        );
        $type->convertToDatabaseValue('U1H53P0ZMJJ9T3KE0595T5BXTV', $platform);
    }
}
