<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\_Shirt_;
use App\Entity\BaseEntity;

class ShirtTransformer extends BaseTransformer
{
  /**
   * @param _Shirt_|array|BaseEntity $entity
   * @return array
   */
  public function transform(_Shirt_|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
