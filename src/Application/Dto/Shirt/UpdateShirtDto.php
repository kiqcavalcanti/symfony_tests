<?php

namespace App\Application\Dto\Shirt;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateShirtDto extends BaseDto
{
    #[Assert\Type('string')]
    // unique
    public string $name;

}
