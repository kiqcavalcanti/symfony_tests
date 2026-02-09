<?php

namespace App\Application\Dto\Router;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateRouterDto extends BaseDto
{
    #[Assert\Type('string')]
    // unique
    public string $name;

    #[Assert\Type('string')]
    public string $model;

    #[Assert\Type('string')]
    public string $status;

}
