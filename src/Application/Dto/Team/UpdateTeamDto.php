<?php

namespace App\Application\Dto\Team;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateTeamDto extends BaseDto
{
    #[Assert\Type('string')]
    // unique
    public string $name;

}
