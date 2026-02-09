<?php

namespace App\Application\Dto\Desk;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateDeskDto extends BaseDto
{
    #[Assert\Type('string')]
    // unique
    public string $name;

    #[Assert\Type('string')]
    public string $cpu;

}
