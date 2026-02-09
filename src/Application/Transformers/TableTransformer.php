<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Table;
use App\Entity\BaseEntity;

class TableTransformer extends BaseTransformer
{
  /**
   * @param Table|array|BaseEntity $entity
   * @return array
   */
  public function transform(Table|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
