<?php

namespace App\Application\Dto\Tracking;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateTrackingDto extends BaseDto
{
    #[Assert\Type('string')]
    public string $category;

    #[Assert\Type('string')]
    public string $action;

    #[Assert\Type('string')]
    public string $label;

}
