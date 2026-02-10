<?php

namespace App\Application\Dto\Common;

use Symfony\Component\Validator\Constraints as Assert;

class BasePaginateDto extends BaseDto
{
  #[Assert\Positive]
  public int $page = 1;

  #[Assert\Positive]
  #[Assert\LessThanOrEqual(100)]
  public int $limit = 10;


  #[Assert\All([
    new Assert\Type('string'),
    new Assert\Regex('/^[a-zA-Z0-9_.]+$/')
  ])]
  public array $include = [];

  #[Assert\All([
    new Assert\Type('string'),
    new Assert\Regex('/^[a-zA-Z0-9_.]+$/')
  ])]
  public array $filter = [];
}
