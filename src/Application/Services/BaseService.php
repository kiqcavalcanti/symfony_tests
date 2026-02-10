<?php

namespace App\Application\Services;

use App\Application\Dto\Common\BaseDto;
use App\Entity\BaseEntity;
use App\Entity\Traits\SoftDeletableTrait;
use App\Exceptions\EntityNotFoundException;
use App\Exceptions\UnprocessableEntityException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class BaseService
{
  protected EntityManagerInterface $em;
  protected EntityRepository $repository;

  public function __construct(EntityManagerInterface $em, string $entityClass)
  {
    $this->em = $em;
    $this->repository = $em->getRepository($entityClass);
  }

  abstract public function create(mixed $dto): BaseEntity;
  abstract public function update(mixed $dto): BaseEntity;

  abstract public function delete(mixed $dto): void;

  abstract public function show(mixed $dto): BaseEntity;

  abstract public function paginate(mixed $dto): array;

  public function baseCreate(BaseDto $dto): object
  {
    $entityClass = $this->repository->getClassName();

    /** @var BaseEntity $entity */
    $entity = $entityClass::fromDto($dto);

    $this->assertUnique($entity);

    $this->em->persist($entity);
    $this->em->flush();

    return $entity;
  }

  public function baseUpdate(BaseDto $dto): object
  {
    $entity = $this->findOrFail($dto->id);

    $entity->updateFromDto($dto);

    $this->em->flush();

    return $entity;
  }

  public function baseDelete(BaseDto $dto): void
  {
    $entity = $this->findOrFail($dto->id);

    $this->em->remove($entity);
    $this->em->flush();
  }

  public function baseInactivate(BaseDto $dto): object
  {
    $entity = $this->findOrFail($dto->id);

    if (!$this->supportsSoftDelete($entity)) {
      throw new UnprocessableEntityException([
        'message' => 'Entidade não suporta inativação',
      ]);
    }

    if (!$entity->isDeleted()) {
      $entity->softDelete();
      $this->em->flush();
    }

    return $entity;
  }

  public function baseReactivate(BaseDto $dto): object
  {
    $entity = $this->findOrFail($dto->id);

    if (!$this->supportsSoftDelete($entity)) {
      throw new UnprocessableEntityException([
        'message' => 'Entidade não suporta reativação',
      ]);
    }

    if ($entity->isDeleted()) {
      $entity->restore();
      $this->em->flush();
    }

    return $entity;
  }

  public function baseShow(BaseDto $dto): ?object
  {
    return $this->findOrFail($dto->id);
  }

  public function basePaginate(BaseDto $dto): array
  {
    $page = max($dto->page, 1);
    $limit = max($dto->limit, 1);

    $qb = $this->repository->createQueryBuilder('e');

    $this->applyFilters($qb, $dto->filter ?? []);
    $this->applyIncludes($qb, $dto->include ?? []);

    $total = (clone $qb)
      ->select('COUNT(e.id)')
      ->getQuery()
      ->getSingleScalarResult();

    $data = $qb
      ->setFirstResult(($page - 1) * $limit)
      ->setMaxResults($limit)
      ->getQuery()
      ->getResult();


    return [
      'data' => $data,
      'meta' => [
        'page' => $page,
        'limit' => $limit,
        'total' => (int) $total,
        'pages' => (int) ceil($total / $limit),
      ]
    ];
  }

  protected function applyIncludes(
    QueryBuilder $qb,
    array $includes
  ): void {}

  protected function applyFilters(
    QueryBuilder $qb,
    array $filters
  ): void {}

  protected function findOrFail(string $id): BaseEntity
  {
    /** @var BaseEntity $entity */
    $entity = $this->repository->find($id);

    if (!$entity) {
      throw new EntityNotFoundException([
        'message' => 'Registro não encontrado',
        'id' => $id,
      ]);
    }

    return $entity;
  }
  protected function assertUnique(BaseEntity $entity, ?string $ignoreId = null): void
  {
    $fields = $entity->getUniqueFields();

    if (empty($fields)) {
      return;
    }

    foreach ($fields as $field) {
      $value = $entity->{'get' . ucfirst($field)}();

      $qb = $this->em->getRepository($entity::class)
        ->createQueryBuilder('e')
        ->where("e.$field = :value")
        ->setParameter('value', $value);

      if ($ignoreId) {
        $qb->andWhere('e.id != :id')
          ->setParameter('id', $ignoreId);
      }

      $exists = $qb->getQuery()->getOneOrNullResult();

      if ($exists) {
        throw new UnprocessableEntityException([
            'errors' => [
              [
                'field' => $field,
                'message' => ucfirst($field) . ' já está em uso',
              ],
            ],
          ]
        );
      }
    }
  }

  protected function supportsSoftDelete(object $entity): bool
  {
    $traits = class_uses($entity, true);

    return isset($traits[SoftDeletableTrait::class]);
  }

}
