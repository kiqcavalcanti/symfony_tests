<?php
namespace App\Application\Dto\Game;
use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;
class UpdateGameDto extends BaseDto
{
    #[Assert\Type("string")]
    public ?string $name = null;
}
