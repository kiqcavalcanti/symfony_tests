<?php
namespace App\Application\Dto\Game;
use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;
class CreateGameDto extends BaseDto
{
    #[Assert\NotBlank(message: "Este campo é obrigatório")]
    #[Assert\Type("string")]
    public string $name;
}
