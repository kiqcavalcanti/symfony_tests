<?php

namespace App\Application\Dto\Book;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateBookDto extends BaseDto
{
    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    public string $title;

    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    public string $yes;

}
