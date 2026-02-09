<?php

namespace App\Application\Dto\Laptop;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateLaptopDto extends BaseDto
{
    #[Assert\Type('string')]
    // unique
    public string $brand;

    #[Assert\Type('string')]
    public string $no;

}
