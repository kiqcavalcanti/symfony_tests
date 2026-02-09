<?php

namespace App\Application\Dto\Glove;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateGloveDto extends BaseDto
{
    #[Assert\Type('string')]
    // unique
    public string $name;

}
