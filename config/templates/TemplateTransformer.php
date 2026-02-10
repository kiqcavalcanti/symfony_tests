<?php

namespace App\Application\Transformers;

use App\Entity\__TEMPLATE_ENTITY__;
use App\Entity\BaseEntity;

class TemplateTransformer extends BaseTransformer
{
  /**
   * @param __TEMPLATE_ENTITY__|BaseEntity $entity
   * @return array
   */
  public function transform(__TEMPLATE_ENTITY__|BaseEntity $entity): array
  {
    return $entity->toArray();
  }
}
