<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Tracking;
use App\Entity\BaseEntity;

class TrackingTransformer extends BaseTransformer
{
  /**
   * @param Tracking|array|BaseEntity $entity
   * @return array
   */
  public function transform(Tracking|BaseEntity|array $entity): array
  {
    return [
      'data' => $entity->toArray()
    ];
  }
}
