<?php

namespace App\Controller;

use App\Application\Dto\Common\BasePaginateDto;
use App\Application\Services\BaseService;
use App\Application\Transformers\BaseTransformer;
use App\Exceptions\UnprocessableEntityException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseController extends AbstractController
{
  public function __construct(
    protected ValidatorInterface $validator,
    protected BaseService        $service,
    protected BaseTransformer    $baseTransformer
  )
  {
  }


  protected function baseCreate(Request $request, string $dtoClass): JsonResponse
  {
    return $this->handleRequest(function() use ($request, $dtoClass) {
      $dto = $this->dtoFromRequest($request, $dtoClass);
      $entity = $this->service->create($dto);

      return new JsonResponse($this->baseTransformer->transform($entity), 201);
    });
  }

  protected function baseUpdate(string $id, Request $request, string $dtoClass): JsonResponse
  {
    return $this->handleRequest(function() use ($request, $dtoClass, $id) {
      $dto = $this->dtoFromRequest($request, $dtoClass, $id);
      $entity = $this->service->update($dto);

      return new JsonResponse($this->baseTransformer->transform($entity), 200);
    });
  }

  protected function baseShow(string $id, Request $request, string $dtoClass): JsonResponse
  {
    return $this->handleRequest(function () use ($request, $dtoClass, $id) {
      $dto = $this->dtoFromRequest($request, $dtoClass, $id);

      $entity = $this->service->show($dto);

      return new JsonResponse(
        $this->baseTransformer->transform($entity),
        200
      );
    });
  }

  protected function baseDelete(string $id, Request $request, string $dtoClass): JsonResponse
  {
    return $this->handleRequest(function () use ($request, $dtoClass, $id) {
      $dto = $this->dtoFromRequest($request, $dtoClass, $id);

      $this->service->delete($dto);

      return new JsonResponse(null, 204);
    });
  }

  protected function basePaginate(
    Request $request,
    string $paginateDtoClass
  ): JsonResponse {
    return $this->handleRequest(function () use ($request, $paginateDtoClass) {
      /** @var BasePaginateDto $dto */
      $dto = new $paginateDtoClass();

      $dto->page  = max(1, (int) $request->query->get('page', 1));
      $dto->limit = min(100, max(1, (int) $request->query->get('limit', 10)));

      foreach ($request->query->all() as $key => $value) {
        if (property_exists($dto, $key)) {
          $dto->$key = $value;
        }
      }

      $this->validateDto($dto);

      $result = $this->service->paginate($dto);

      return new JsonResponse([
        'data' => array_map(
          fn ($entity) => $this->baseTransformer->transform($entity),
          $result['data']
        ),
        'meta' => $result['meta'],
      ]);
    });
  }


  protected function dtoFromRequest(Request $request, string $dtoClass, $id = null): object
  {
    $data = json_decode($request->getContent(), true) ?? [];

    $dto = new $dtoClass();

    if ($id !== null && property_exists($dto, 'id')) {
      $dto->id = $id;
    }

    foreach ($data as $key => $value) {
      if (property_exists($dto, $key)) {
        $dto->$key = $value;
      }
    }

    $this->validateDto($dto);

    return $dto;
  }

  protected function validateDto(object $dto): void
  {
    $errors = $this->validator->validate($dto);

    if (count($errors) > 0) {
      throw new UnprocessableEntityException([
        'errors' => array_map(
          fn ($e) => [
            'field' => $e->getPropertyPath(),
            'message' => $e->getMessage(),
          ],
          iterator_to_array($errors)
        )
      ]);
    }
  }

  protected function handleRequest(callable $callback): JsonResponse
  {
    try {
      return $callback();
    } catch (\Throwable $e) {
       return $this->handleThrowable($e);
    }
  }

  protected function handleThrowable(\Throwable $e): JsonResponse
  {
    $status = 500;
    $payload = ['message' => 'Erro interno do servidor'];

    if ($e instanceof HttpException) {
      $status = $e->getStatusCode();

      if (property_exists($e, 'payload')) {
        $payload = $e->payload;
      } else {
        $payload = ['message' => $e->getMessage()];
      }
    }

    if ($status === 500 && $_ENV['APP_ENV'] !== 'prod') {
      $payload['exception'] = get_class($e);
      $payload['file'] = $e->getFile();
      $payload['line'] = $e->getLine();
      $payload['trace'] = $e->getTrace();
    }

    return new JsonResponse($payload, $status);
  }

}
