<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\__TEMPLATE_ENTITY__;
use App\Entity\BaseEntity;

class TemplateTransformer extends BaseTransformer
{
  /**
   * @param __TEMPLATE_ENTITY__|array|BaseEntity $entity
   * @return array
   */
  public function transform(__TEMPLATE_ENTITY__|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
