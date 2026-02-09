<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Desk;
use App\Entity\BaseEntity;

class DeskTransformer extends BaseTransformer
{
  /**
   * @param Desk|array|BaseEntity $entity
   * @return array
   */
  public function transform(Desk|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
