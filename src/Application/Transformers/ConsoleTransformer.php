<?php

namespace App\Application\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entity\Console;
use App\Entity\BaseEntity;

class ConsoleTransformer extends BaseTransformer
{
  /**
   * @param Console|array|BaseEntity $entity
   * @return array
   */
  public function transform(Console|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
