<?php

namespace App\Application\Dto\Hat;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateHatDto extends BaseDto
{
    #[Assert\Type('string')]
    public string $color;

    #[Assert\Type('string')]
    public string $yes;

}
