<?php

namespace App\Application\Transformers;

use App\Application\Transformers\BaseTransformer;
use App\Entity\Header;
use App\Entity\BaseEntity;

class HeaderTransformer extends BaseTransformer
{
  /**
   * @param Header|array|BaseEntity $entity
   * @return array
   */
  public function transform(Header|BaseEntity|array $entity): array
  {
    return [
      'data' => [
        'logo' => [
          'text' => $entity->getLogoText(),
          'image' => $entity->getLogoImage(),
          'url' => $entity->getLogoUrl(),
          'tracking_id' => $entity->getTrackingId(),
        ],
        'search' => [
          'placeholder' => $entity->getSearchPlaceholder(),
          'icon' => $entity->getSearchIcon(),
          'tracking_id' => $entity->getSearchTrackingId(),
        ]
      ]
    ];
  }
}
