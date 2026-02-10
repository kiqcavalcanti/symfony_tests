<?php

namespace App\Utils;

class CaseConverter
{

  public static function toSnakeCase(string $input): string
  {
    return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
  }


  public static function toCamelCase(string $input): string
  {
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
  }

}
