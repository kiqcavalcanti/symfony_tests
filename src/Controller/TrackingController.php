<?php

namespace App\Controller;

use App\Application\Dto\Tracking\CreateTrackingDto;
use App\Application\Dto\Tracking\UpdateTrackingDto;
use App\Application\Dto\Common\BasePaginateDto;
use App\Application\Dto\Common\IdDto;
use App\Application\Services\TrackingService;
use App\Application\Transformers\TrackingTransformer;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/trackings')]
class TrackingController extends BaseController
{
  public function __construct(
    TrackingService $service,
    ValidatorInterface $validator,
    TrackingTransformer $transformer,
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
    return parent::baseCreate($request, CreateTrackingDto::class);
  }

  #[Route('/{id}', methods: ['PUT'])]
  public function update(string $id, Request $request): JsonResponse
  {
    return parent::baseUpdate($id, $request, UpdateTrackingDto::class);
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
