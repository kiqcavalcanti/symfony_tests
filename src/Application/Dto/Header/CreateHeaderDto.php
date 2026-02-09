<?php

namespace App\Application\Dto\Header;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateHeaderDto extends BaseDto
{
    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    public string $logo_text;

    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    public string $logo_image;

    #[Assert\NotBlank(message: 'Este campo é obrigatório')]
    #[Assert\Type('string')]
    public string $logo_url;

    #[Assert\Type('integer')]
    public ?int $tracking_id = null;

    #[Assert\Type('string')]
    public ?string $search_placeholder = null;

    #[Assert\Type('string')]
    public ?string $search_icon = null;

    #[Assert\Type('integer')]
    public ?int $search_tracking_id = null;

}
