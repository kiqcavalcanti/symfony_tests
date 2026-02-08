<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
class User extends BaseEntity implements PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\Column(type: 'uuid', unique: true)]
  private Uuid $id;

  #[ORM\Column(length: 100)]
  private string $name;

  #[ORM\Column(length: 180, unique: true)]
  private string $email;

  #[ORM\Column]
  private string $password;

  public function __construct(
    string $name,
    string $email,
    string $hashedPassword
  )
  {
    $this->id = Uuid::v7();
    $this->name = $name;
    $this->email = $email;
    $this->password = $hashedPassword;
  }

  public function getId(): string
  {
    return $this->id->toRfc4122();
  }

  public function getUuid(): Uuid
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function changeName(string $name): void
  {
    $this->name = $name;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function changeEmail(string $email): void
  {
    $this->email = $email;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function changePassword(string $hashedPassword): void
  {
    $this->password = $hashedPassword;
  }

  public function getUniqueFields(): array
  {
    return ['email'];
  }
}
