<?php

namespace App\Application\Transformers;

use App\Utils\CaseConverter;
use League\Fractal\TransformerAbstract;

abstract class BaseTransformer extends TransformerAbstract
{
  public array $includes = [];

  public function setIncludes(array $includes): self
  {
    $this->includes = array_map([CaseConverter::class, 'toSnakeCase'], $includes);
    return $this;
  }

  protected function hasInclude(string $include): bool
  {
    return in_array(CaseConverter::toSnakeCase($include), $this->includes, true);
  }
}
