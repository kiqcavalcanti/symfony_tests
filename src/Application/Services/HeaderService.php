<?php

namespace App\Application\Services;

use App\Application\Services\BaseService;
use App\Entity\Header;
use App\Entity\BaseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class HeaderService extends BaseService
{
  public function __construct(EntityManagerInterface $em)
  {
    parent::__construct($em, Header::class);
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

  protected function applyIncludes(
    QueryBuilder $qb,
    array $includes
  ): void {

    if (in_array('logo_tracking', $includes, true)) {
      $qb
        ->leftJoin('e.tracking', 'logoTracking')
        ->addSelect('logoTracking');
    }

    if (in_array('search_tracking', $includes, true)) {
      $qb
        ->leftJoin('e.searchTracking', 'searchTracking')
        ->addSelect('searchTracking');
    }
  }

  protected function applyFilters(QueryBuilder $qb, array $filters): void
  {
      if (isset($filters['logo_text'])) {
        $qb
          ->andWhere('e.logo_text LIKE :logoText')
          ->setParameter('logoText', '%' . $filters['logo_text'] . '%');
      }
  }

}
