<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Monitor;
use App\Entity\BaseEntity;

class MonitorTransformer extends BaseTransformer
{
  /**
   * @param Monitor|array|BaseEntity $entity
   * @return array
   */
  public function transform(Monitor|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
