<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine\Tests;

use Arokettu\Clock\RoundingClock;
use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Doctrine\UlidGenerator;
use Arokettu\Uuid\Doctrine\UuidV4Generator;
use Arokettu\Uuid\Doctrine\UuidV7Generator;
use Arokettu\Uuid\Ulid;
use Arokettu\Uuid\UuidV4;
use Arokettu\Uuid\UuidV7;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class GeneratorsTest extends TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('doctrine/orm does not support doctrine/dbal 4 yet');
    }

    protected function getEM(): EntityManager
    {
        // not really used but must be passed
        return self::createMock(EntityManager::class);
    }

    public function testGenerateUuidV4(): void
    {
        $gen = new UuidV4Generator();

        self::assertInstanceOf(UuidV4::class, $uuid1 = $gen->generate($this->getEM(), new \stdClass()));
        self::assertInstanceOf(UuidV4::class, $uuid2 = $gen->generateId($this->getEM(), new \stdClass()));

        self::assertNotEquals($uuid1, $uuid2); // not guaranteed but that should be the point of the UUID
    }

    public function testGenerateUuidV7(): void
    {
        $gen = new UuidV7Generator();
        // UUIDv7 and ULID have millisecond precision
        $clock = new RoundingClock(new SystemClock(), RoundingClock::ROUND_MILLISECONDS);

        $dtBefore = $clock->now();
        self::assertInstanceOf(UuidV7::class, $uuid1 = $gen->generate($this->getEM(), new \stdClass()));
        self::assertInstanceOf(UuidV7::class, $uuid2 = $gen->generateId($this->getEM(), new \stdClass()));
        $dtAfter = $clock->now();

        self::assertNotEquals($uuid1, $uuid2);
        self::assertGreaterThanOrEqual($dtBefore, $uuid1->getDateTime());
        self::assertGreaterThanOrEqual($dtBefore, $uuid2->getDateTime());
        self::assertLessThanOrEqual($dtAfter, $uuid1->getDateTime());
        self::assertLessThanOrEqual($dtAfter, $uuid2->getDateTime());
    }

    public function testGenerateUlid(): void
    {
        $gen = new UlidGenerator();
        // UUIDv7 and ULID have millisecond precision
        $clock = new RoundingClock(new SystemClock(), RoundingClock::ROUND_MILLISECONDS);

        $dtBefore = $clock->now();
        self::assertInstanceOf(Ulid::class, $uuid1 = $gen->generate($this->getEM(), new \stdClass()));
        self::assertInstanceOf(Ulid::class, $uuid2 = $gen->generateId($this->getEM(), new \stdClass()));
        $dtAfter = $clock->now();

        self::assertNotEquals($uuid1, $uuid2);
        self::assertGreaterThanOrEqual($dtBefore, $uuid1->getDateTime());
        self::assertGreaterThanOrEqual($dtBefore, $uuid2->getDateTime());
        self::assertLessThanOrEqual($dtAfter, $uuid1->getDateTime());
        self::assertLessThanOrEqual($dtAfter, $uuid2->getDateTime());
    }
}
