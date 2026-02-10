<?php

namespace App\Application\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entity\Team;
use App\Entity\BaseEntity;

class TeamTransformer extends BaseTransformer
{
  /**
   * @param Team|array|BaseEntity $entity
   * @return array
   */
  public function transform(Team|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
