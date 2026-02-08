<?php

namespace App\Application\Transformers;

use App\Entity\BaseEntity;
use ReflectionClass;
use ReflectionProperty;

class BaseTransformer
{
  /**
   * @param BaseEntity|BaseEntity[] $data
   * @return array
   */
  public function transform(BaseEntity|array $data): array
  {
    if (is_array($data)) {
      return array_map(fn($item) => $this->transformEntity($item), $data);
    }

    return $this->transformEntity($data);
  }

  /**
   * Transforma uma única entidade
   */
  protected function transformEntity(BaseEntity $entity): array
  {
    $calledClass = get_called_class();
    if ($calledClass !== self::class &&
      (new \ReflectionMethod($calledClass, 'transformEntity'))->getDeclaringClass()->getName() === $calledClass) {
      return $calledClass::transformEntity($entity);
    }

    return $this->genericTransform($entity);
  }

  protected function genericTransform(BaseEntity $entity): array
  {
    $reflection = new ReflectionClass($entity);
    $props = $reflection->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC);

    $data = [];
    foreach ($props as $prop) {
      $prop->setAccessible(true);
      $data[$prop->getName()] = $prop->getValue($entity);
    }

    return $data;
  }
}
