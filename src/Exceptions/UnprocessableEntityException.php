<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnprocessableEntityException extends HttpException
{
  public array $payload;

  public function __construct(array $payload, int $statusCode = 422)
  {
    parent::__construct($statusCode);
    $this->payload = $payload;
  }
}

