<?php

declare(strict_types=1);

namespace Camoo\Hosting\Factory;

use Camoo\Hosting\Entity\EntityInterface;

interface EntityFactoryInterface
{
    public function getEntityClass(string $entity): EntityInterface;
}
