<?php

namespace App\Application\Dto\Common;

use Symfony\Component\Validator\Constraints as Assert;

class IdDto extends BaseDto
{
  #[Assert\NotBlank]
  #[Assert\Uuid]
  public string $id;
}

