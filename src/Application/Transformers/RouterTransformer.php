<?php

namespace App\Application\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entity\Router;
use App\Entity\BaseEntity;

class RouterTransformer extends BaseTransformer
{
  /**
   * @param Router|array|BaseEntity $entity
   * @return array
   */
  public function transform(Router|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
