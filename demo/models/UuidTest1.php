<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine\Demo;

use Arokettu\Uuid\Doctrine\UlidBinaryType;
use Arokettu\Uuid\Doctrine\UlidType;
use Arokettu\Uuid\Doctrine\UuidBinaryType;
use Arokettu\Uuid\Doctrine\UuidType;
use Arokettu\Uuid\Doctrine\UuidV4Generator;
use Arokettu\Uuid\Ulid;
use Arokettu\Uuid\Uuid;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'uuid_test1')]
class UuidTest1
{
    #[Column(type: UuidBinaryType::NAME)]
    #[Id, GeneratedValue(strategy: 'CUSTOM'), CustomIdGenerator(UuidV4Generator::class)]
    public Uuid $id;

    #[Column(type: UuidType::NAME)]
    public Uuid $uuidString;

    #[Column(type: UuidBinaryType::NAME)]
    public Uuid $uuidBin;

    #[Column(type: UlidType::NAME)]
    public Ulid $ulidString;

    #[Column(type: UlidBinaryType::NAME)]
    public Ulid $ulidBin;
}
