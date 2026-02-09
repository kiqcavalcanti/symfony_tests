<?php

namespace App\Application\Dto\Team;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTeamDto extends BaseDto
{
    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    // unique
    public string $name;

}
