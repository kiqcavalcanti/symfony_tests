<?php

namespace App\Application\Dto\Common;

abstract class BaseDto
{
  public function toArray(): array
  {
    return get_object_vars($this);
  }
}
