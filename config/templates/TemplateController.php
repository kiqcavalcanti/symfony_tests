<?php

namespace App\Controller;

use App\Application\Dto\__TEMPLATE_NAME__\Create__TEMPLATE_NAME__Dto;
use App\Application\Dto\__TEMPLATE_NAME__\Update__TEMPLATE_NAME__Dto;
use App\Application\Dto\Common\BasePaginateDto;
use App\Application\Dto\Common\IdDto;
use App\Application\Services\__TEMPLATE_NAME__Service;
use App\Application\Transformers\__TEMPLATE_NAME__Transformer;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/__TEMPLATE_NAME_IN_PLURAL__')]
class __TEMPLATE_NAME__Controller extends BaseController
{
  public function __construct(
    __TEMPLATE_NAME__Service $service,
    ValidatorInterface $validator,
    __TEMPLATE_NAME__Transformer $transformer,
  ) {
    parent::__construct($validator, $service, $transformer);
  }

  protected function getAllowedIncludes(): array
  {
    return [];
  }

  protected function getDefaultIncludes(): array
  {
    return [];
  }

  protected function getAvailableFilters(): array
  {
    return [];
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
    return parent::baseCreate($request, Create__TEMPLATE_NAME__Dto::class);
  }

  #[Route('/{id}', methods: ['PUT'])]
  public function update(string $id, Request $request): JsonResponse
  {
    return parent::baseUpdate($id, $request, Update__TEMPLATE_NAME__Dto::class);
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
