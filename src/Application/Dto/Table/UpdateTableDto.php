<?php

namespace App\Application\Dto\Table;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateTableDto extends BaseDto
{
    #[Assert\Type('string')]
    // unique
    public string $name;

}
