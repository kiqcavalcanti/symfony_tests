<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class EntityNotFoundException extends HttpException
{
  public array $payload;

  public function __construct(array $payload, int $statusCode = 404)
  {
    parent::__construct($statusCode);
    $this->payload = $payload;
  }
}

