<?php

namespace App\Application\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entity\Tracking;
use App\Entity\BaseEntity;

class TrackingTransformer extends BaseTransformer
{
  /**
   * @param Tracking|BaseEntity $entity
   * @return array
   */
  public function transform(Tracking|BaseEntity $entity): array
  {
    return [
      $entity->toArray()
    ];
  }
}
