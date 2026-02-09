<?php

namespace App\Controller;

use App\Application\Dto\Desk\CreateDeskDto;
use App\Application\Dto\Desk\UpdateDeskDto;
use App\Application\Dto\Common\BasePaginateDto;
use App\Application\Dto\Common\IdDto;
use App\Application\Services\DeskService;
use App\Application\Transformers\DeskTransformer;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/desks')]
class DeskController extends BaseController
{
  public function __construct(
    DeskService $service,
    ValidatorInterface $validator,
    DeskTransformer $transformer,
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
    return parent::baseCreate($request, CreateDeskDto::class);
  }

  #[Route('/{id}', methods: ['PUT'])]
  public function update(string $id, Request $request): JsonResponse
  {
    return parent::baseUpdate($id, $request, UpdateDeskDto::class);
  }


  #[Route('/{id}', methods: ['DELETE'])]
  public function delete(string $id, Request $request): JsonResponse
  {
    return parent::baseDelete($id, $request, IdDto::class);
  }
}
