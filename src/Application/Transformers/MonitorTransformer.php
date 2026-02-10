<?php

namespace App\Application\Transformers;

use League\Fractal\TransformerAbstract;
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
