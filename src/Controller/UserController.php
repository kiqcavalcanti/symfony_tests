<?php

namespace App\Controller;

use App\Application\Dto\Common\BasePaginateDto;
use App\Application\Dto\Common\IdDto;
use App\Application\Dto\User\CreateUserDto;
use App\Application\Dto\User\UpdateUserDto;
use App\Application\Services\UserService;
use App\Application\Transformers\UserTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/users')]
class UserController extends BaseController
{
  public function __construct(
    UserService $service,
    ValidatorInterface $validator,
    UserTransformer $transformer,
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
    return parent::baseCreate($request, CreateUserDto::class);
  }

  #[Route('/{id}', methods: ['PUT'])]
  public function update(string $id, Request $request): JsonResponse
  {
    return parent::baseUpdate($id, $request, UpdateUserDto::class);
  }


  #[Route('/{id}', methods: ['DELETE'])]
  public function delete(string $id, Request $request): JsonResponse
  {
    return parent::baseDelete($id, $request, IdDto::class);
  }
}
