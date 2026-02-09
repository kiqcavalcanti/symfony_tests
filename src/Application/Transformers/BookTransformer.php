<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Book;
use App\Entity\BaseEntity;

class BookTransformer extends BaseTransformer
{
  /**
   * @param Book|array|BaseEntity $entity
   * @return array
   */
  public function transform(Book|BaseEntity|array $entity): array
  {
    if (is_array($entity)) {
      return $entity;
    }

    return $entity->toArray();
  }
}
