<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Doctrine;

use Arokettu\Uuid\Uuid;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

abstract class AbstractGenerator extends AbstractIdGenerator
{
    abstract protected function generateUuid(): Uuid;

    public function generateId(EntityManagerInterface $em, mixed $entity): Uuid
    {
        return $this->generateUuid();
    }

    public function generate(EntityManager $em, mixed $entity): Uuid
    {
        return $this->generateUuid();
    }
}
