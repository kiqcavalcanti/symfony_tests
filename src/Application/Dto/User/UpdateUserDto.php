<?php

namespace App\Application\Dto\User;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserDto extends BaseDto
{
  #[Assert\NotBlank(message: 'O id é obrigatório')]
  #[Assert\Uuid(message: 'O id deve ser um UUID válido')]
  public string $id;

  #[Assert\NotBlank]
  #[Assert\Length(min: 2, max: 100)]
  public string $name;

  #[Assert\NotBlank]
  #[Assert\Email(message: 'Email inválido')]
  public string $email;
}

