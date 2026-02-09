<?php

namespace App\Application\Services;

use App\Application\Services\BaseService;
use App\Entity\_Glove_;
use App\Entity\BaseEntity;
use Doctrine\ORM\EntityManagerInterface;

class GloveService extends BaseService
{
  public function __construct(EntityManagerInterface $em)
  {
    parent::__construct($em, _Glove_::class);
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
}
