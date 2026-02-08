<?php

namespace App\Application\Transformers;

use App\Entity\BaseEntity;
use App\Entity\User;

class UserTransformer extends BaseTransformer
{
  /**
   * @param User|array|BaseEntity $user
   * @return array
   */
  public function transform(User|BaseEntity|array $user): array
  {
    return [
      'id' => $user->getId(),
      'email' => $user->getEmail(),
      'name' => $user->getName(),
    ];
  }
}
