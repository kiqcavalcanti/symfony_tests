<?php

namespace App\Application\Dto\User;

use App\Application\Dto\Common\BaseDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDto extends BaseDto
{
  #[Assert\NotBlank]
  #[Assert\Length(min: 2, max: 100)]
  public string $name;

  #[Assert\NotBlank]
  #[Assert\Email(message: 'Email inválido')]
  public string $email;

  #[Assert\NotBlank]
  #[Assert\Length(
    min: 8,
    minMessage: 'A senha deve ter pelo menos {{ limit }} caracteres'
  )]
  #[Assert\Regex(
    pattern: '/[A-Z]/',
    message: 'A senha deve conter ao menos uma letra maiúscula'
  )]
  #[Assert\Regex(
    pattern: '/[\W]/',
    message: 'A senha deve conter ao menos um símbolo'
  )]
  public string $password;
}

