<?php

declare(strict_types=1);

use Arokettu\Uuid\Doctrine\UlidBinaryType;
use Arokettu\Uuid\Doctrine\UlidType;
use Arokettu\Uuid\Doctrine\UuidBinaryType;
use Arokettu\Uuid\Doctrine\UuidType;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/models/UuidTest1.php';

Type::addType(UuidType::NAME, UuidType::class);
Type::addType(UuidBinaryType::NAME, UuidBinaryType::class);
Type::addType(UlidType::NAME, UlidType::class);
Type::addType(UlidBinaryType::NAME, UlidBinaryType::class);

$eventManager = new EventManager();

$options = require __DIR__ . '/connection.php';
$db = DriverManager::getConnection($options, null, $eventManager);

$meta = new MappingDriverChain();
$meta->addDriver(new AttributeDriver([__DIR__ . '/models']), 'Arokettu\Uuid\Doctrine\Demo');

$ormConfig = new Configuration();
$ormConfig->setProxyDir(__DIR__ . '/tmp/proxy');
$ormConfig->setProxyNamespace('Proxy');
$ormConfig->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/models']));

$em = new EntityManager($db, $ormConfig, $eventManager);

return compact('db', 'em');
