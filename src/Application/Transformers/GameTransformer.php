<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Game;
use App\Entity\BaseEntity;

class GameTransformer extends BaseTransformer
{
  /**
   * @param Game|array|BaseEntity $entity
   * @return array
   */
  public function transform(Game|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
