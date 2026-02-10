<?php

namespace App\Application\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entity\_Glove_;
use App\Entity\BaseEntity;

class GloveTransformer extends BaseTransformer
{
  /**
   * @param _Glove_|array|BaseEntity $entity
   * @return array
   */
  public function transform(_Glove_|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
