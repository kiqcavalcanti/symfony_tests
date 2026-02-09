<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Laptop;
use App\Entity\BaseEntity;

class LaptopTransformer extends BaseTransformer
{
  /**
   * @param Laptop|array|BaseEntity $entity
   * @return array
   */
  public function transform(Laptop|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
