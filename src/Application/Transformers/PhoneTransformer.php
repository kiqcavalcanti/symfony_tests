<?php

namespace App\Application\Transformers;

use League\Fractal\TransformerAbstract;
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
