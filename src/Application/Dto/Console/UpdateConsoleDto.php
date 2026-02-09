<?php

namespace App\Application\Dto\Console;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateConsoleDto extends BaseDto
{
    #[Assert\Type('string')]
    // unique
    public string $name;

    #[Assert\Type('string')]
    public ?string $controle = null;

}
