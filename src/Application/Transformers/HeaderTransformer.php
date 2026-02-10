<?php

namespace App\Application\Transformers;

use App\Entity\Header;
use App\Entity\BaseEntity;

class HeaderTransformer extends BaseTransformer
{

  /**
   * @param Header|BaseEntity $entity
   * @return array
   */
  public function transform(Header|BaseEntity $entity): array
  {
    return [
      'logo' => [
        'text' => $entity->getLogoText(),
        'image' => $entity->getLogoImage(),
        'url' => $entity->getLogoUrl(),
        'tracking' => $this->hasInclude('logo_tracking')
          ? (new TrackingTransformer())->transform(
            $entity->getLogoTracking()
          )
          : null,
      ],
      'search' => [
        'placeholder' => $entity->getSearchPlaceholder(),
        'icon' => $entity->getSearchIcon(),
        'tracking' => $this->hasInclude('search_tracking')
          ? (new TrackingTransformer())->transform(
            $entity->getSearchTracking()
          )
          : null,
      ],
    ];
  }


}
