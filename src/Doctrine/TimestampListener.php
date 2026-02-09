<?php

namespace App\Doctrine;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class TimestampListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$this->hasTimestampTrait($entity)) {
            return;
        }

        $this->setTimestamps($entity, true);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$this->hasTimestampTrait($entity)) {
            return;
        }

        $this->setTimestamps($entity, false);
    }

    private function setTimestamps(object $entity, bool $isNew): void
    {
        $reflection = new \ReflectionClass($entity);
        $now = new \DateTimeImmutable();

        // Para nova entidade, define createdAt e updatedAt
        if ($isNew && $reflection->hasProperty('createdAt')) {
            $property = $reflection->getProperty('createdAt');
            $property->setAccessible(true);
            $property->setValue($entity, $now);
        }

        // Sempre atualiza updatedAt
        if ($reflection->hasProperty('updatedAt')) {
            $property = $reflection->getProperty('updatedAt');
            $property->setAccessible(true);
            $property->setValue($entity, $now);
        }
    }

    private function hasTimestampTrait(object $entity): bool
    {
        $traits = class_uses($entity, true);
        return isset($traits[TimestampableTrait::class]);
    }
}

