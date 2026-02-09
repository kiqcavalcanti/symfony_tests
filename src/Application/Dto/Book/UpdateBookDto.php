<?php

namespace App\Application\Dto\Book;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateBookDto extends BaseDto
{
    #[Assert\Type('string')]
    public string $title;

    #[Assert\Type('string')]
    public string $yes;

}
