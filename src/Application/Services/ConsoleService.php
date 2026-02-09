<?php

namespace App\Application\Services;

use App\Application\Services\BaseService;
use App\Entity\Console;
use App\Entity\BaseEntity;
use Doctrine\ORM\EntityManagerInterface;

class ConsoleService extends BaseService
{
  public function __construct(EntityManagerInterface $em)
  {
    parent::__construct($em, Console::class);
  }


  public function create($dto): BaseEntity
  {
    return parent::baseCreate($dto);
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

  public function inactivate(mixed $dto): BaseEntity
  {
    return parent::baseInactivate($dto);
  }

  public function reactivate(mixed $dto): BaseEntity
  {
    return parent::baseReactivate($dto);
  }

}
