<?php

namespace App\Application\Dto\Table;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTableDto extends BaseDto
{
    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    // unique
    public string $name;

}
