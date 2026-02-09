<?php

namespace App\Controller;

use App\Application\Dto\Header\CreateHeaderDto;
use App\Application\Dto\Header\UpdateHeaderDto;
use App\Application\Dto\Common\BasePaginateDto;
use App\Application\Dto\Common\IdDto;
use App\Application\Services\HeaderService;
use App\Application\Transformers\HeaderTransformer;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/headers')]
class HeaderController extends BaseController
{
  public function __construct(
    HeaderService $service,
    ValidatorInterface $validator,
    HeaderTransformer $transformer,
  ) {
    parent::__construct($validator, $service, $transformer);
  }

  #[Route('', methods: ['GET'])]
  public function index(Request $request): JsonResponse
  {
    return parent::basePaginate($request, BasePaginateDto::class);
  }

  #[Route('/{id}', methods: ['GET'])]
  public function show(string $id, Request $request): JsonResponse
  {
    return parent::baseShow($id, $request, IdDto::class);
  }

  #[Route('', methods: ['POST'])]
  public function create(Request $request): JsonResponse
  {
    return parent::baseCreate($request, CreateHeaderDto::class);
  }

  #[Route('/{id}', methods: ['PUT'])]
  public function update(string $id, Request $request): JsonResponse
  {
    return parent::baseUpdate($id, $request, UpdateHeaderDto::class);
  }


  #[Route('/{id}', methods: ['DELETE'])]
  public function delete(string $id, Request $request): JsonResponse
  {
    return parent::baseDelete($id, $request, IdDto::class);
  }

  #[Route('/{id}/reactivate', methods: ['POST'])]
  public function reactivate(string $id, Request $request): JsonResponse
  {
    return parent::baseReactivate($id, $request, IdDto::class);
  }

  #[Route('/{id}/inactivate', methods: ['POST'])]
  public function inactivate(string $id, Request $request): JsonResponse
  {
    return parent::baseInactivate($id, $request, IdDto::class);
  }

}
