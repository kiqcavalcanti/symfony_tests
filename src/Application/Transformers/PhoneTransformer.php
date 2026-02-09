<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Phone;
use App\Entity\BaseEntity;

class PhoneTransformer extends BaseTransformer
{
  /**
   * @param Phone|array|BaseEntity $entity
   * @return array
   */
  public function transform(Phone|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
