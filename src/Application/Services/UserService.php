<?php

namespace App\Application\Services;

use App\Application\Dto\User\CreateUserDto;
use App\Entity\BaseEntity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService extends BaseService
{
  public function __construct(EntityManagerInterface $em, private UserPasswordHasherInterface $passwordHasher)
  {
    parent::__construct($em, User::class);
  }

  /**
   * @param CreateUserDto $dto
   * @return User
   */
  public function create($dto): User
  {
    /** @var User $user */
    $user = User::fromArray([
      'name' => $dto->name,
      'hashed_password' => 'temp',
      'email' => $dto->email,
    ]);

    $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
    $user->changePassword($hashedPassword);

    $this->assertUnique($user);

    $this->em->persist($user);
    $this->em->flush();

    return $user;
  }

  public function update($dto): BaseEntity
  {
    return parent::baseUpdate($dto);
  }

  public function delete(mixed $dto): void
  {
    parent::baseDelete($dto);
  }

  public function show(mixed $dto): BaseEntity
  {
    return parent::baseShow($dto);
  }

  public function paginate(mixed $dto): array
  {
    return parent::basePaginate($dto);
  }
}
