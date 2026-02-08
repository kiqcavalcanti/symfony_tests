<?php

namespace App\Entity;

use App\Application\Dto\Common\BaseDto;

abstract class BaseEntity
{
  public static function fromDto(object $dto): self
  {
    return static::fromArray(get_object_vars($dto));
  }

  /**
   * Cria a entidade a partir de um array
   */
  public static function fromArray(array $data): self
  {
    $reflection = new \ReflectionClass(get_called_class());
    $constructor = $reflection->getConstructor();

    // Normaliza todas as chaves para camelCase
    $normalizedData = [];
    foreach ($data as $key => $value) {
      $normalizedKey = self::toCamelCase($key);
      $normalizedData[$normalizedKey] = $value;
    }

    // Prepara argumentos do construtor
    $args = [];
    if ($constructor) {
      foreach ($constructor->getParameters() as $param) {
        $name = $param->getName();
        if (!array_key_exists($name, $normalizedData)) {
          if ($param->isOptional()) {
            $args[] = $param->getDefaultValue();
          } else {
            throw new \InvalidArgumentException(
              "Missing parameter $name for " . static::class
            );
          }
        } else {
          $args[] = $normalizedData[$name];
        }
      }
    }

    $entity = $reflection->newInstanceArgs($args);

    // Preenche campos restantes usando changeX
    foreach ($normalizedData as $key => $value) {
      $method = 'set' . ucfirst($key);
      if (method_exists($entity, $method)) {
        $entity->$method($value);
      }
    }

    return $entity;
  }

  public function updateFromArray(array $data): void
  {
    // Normaliza todas as chaves para camelCase
    $normalizedData = [];
    foreach ($data as $key => $value) {
      $normalizedKey = self::toCamelCase($key);
      $normalizedData[$normalizedKey] = $value;
    }

    foreach ($normalizedData as $key => $value) {
      $method = 'set' . ucfirst($key);

      if (!method_exists($this, $method)) {
        continue;
      }

      $this->$method($value);
    }
  }


  public function updateFromDto(BaseDto $dto): void
  {
    if (!method_exists($dto, 'toArray')) {
      throw new \LogicException('DTO must implement toArray()');
    }

    $this->updateFromArray($dto->toArray());
  }

  private static function toCamelCase(string $str): string
  {
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
  }

  /**
   * Retorna os campos únicos da entidade. Por padrão nenhum campo é único.
   */
  public function getUniqueFields(): array
  {
    return [];
  }

  public function toArray(): array
  {
    $result = [];
    $reflection = new \ReflectionClass($this);

    foreach ($reflection->getProperties() as $property) {
      $property->setAccessible(true);
      $fieldName = $property->getName();

      // Tenta getters com os prefixes convencionais: getX e isX (para booleanos)
      $getterCandidates = [
        'get' . ucfirst($fieldName),
        'is' . ucfirst($fieldName),
      ];

      $valueSet = false;
      foreach ($getterCandidates as $getter) {
        if (method_exists($this, $getter)) {
          $result[$fieldName] = $this->$getter();
          $valueSet = true;
          break;
        }
      }

      // Se não encontrou getter, tenta acessar a propriedade diretamente
      if (!$valueSet) {
        $result[$fieldName] = $property->getValue($this);
      }
    }

    return $result;
  }

}
