<?php

namespace App\Application\Dto\Tracking;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTrackingDto extends BaseDto
{
    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    public string $category;

    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    public string $action;

    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    public string $label;

}
