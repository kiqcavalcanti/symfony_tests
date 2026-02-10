<?php

namespace App\Application\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entity\_Hat_;
use App\Entity\BaseEntity;

class HatTransformer extends BaseTransformer
{
  /**
   * @param _Hat_|array|BaseEntity $entity
   * @return array
   */
  public function transform(_Hat_|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
