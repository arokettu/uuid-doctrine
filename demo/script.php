<?php

declare(strict_types=1);

use Arokettu\Uuid\Doctrine\Demo\UuidTest1;
use Arokettu\Uuid\UlidFactory;
use Arokettu\Uuid\UuidFactory;

['db' => $db, 'em' => $em] = require __DIR__ . '/db.php';

$test1 = new UuidTest1();

$test1->uuidString = UuidFactory::v4();
$test1->uuidBin = UuidFactory::v7();
$test1->ulidString = UlidFactory::ulid();
$test1->ulidBin = UlidFactory::ulid();

var_dump($test1->uuidString->toString());
var_dump($test1->uuidBin->toString());
var_dump($test1->ulidString->toString());
var_dump($test1->ulidBin->toString());

$em->persist($test1);
$em->flush();

$uuid = $test1->id;

$test2 = $em->find(UuidTest1::class, $uuid);

var_dump($test2->id->toString());
var_dump($test2->uuidString->toString());
var_dump($test2->uuidBin->toString());
var_dump($test2->ulidString->toString());
var_dump($test2->ulidBin->toString());
