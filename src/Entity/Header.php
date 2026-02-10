<?php

namespace App\Entity;

use App\Repository\HeaderRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEntity;
use App\Entity\Traits\TimestampableTrait;

#[ORM\Entity(repositoryClass: HeaderRepository::class)]
class Header extends BaseEntity
{
  use TimestampableTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $logo_text = null;

  #[ORM\Column(length: 255)]
  private ?string $logo_image = null;

  #[ORM\Column(length: 255)]
  private ?string $logo_url = null;

  #[ORM\Column(nullable: true)]
  private ?int $tracking_id = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $search_placeholder = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $search_icon = null;

  #[ORM\Column(nullable: true)]
  private ?int $search_tracking_id = null;


  #[ORM\ManyToOne(targetEntity: Tracking::class)]
  #[ORM\JoinColumn(name: 'tracking_id', referencedColumnName: 'id', nullable: true)]
  private ?Tracking $tracking = null;

  #[ORM\ManyToOne(targetEntity: Tracking::class)]
  #[ORM\JoinColumn(name: 'search_tracking_id', referencedColumnName: 'id', nullable: true)]
  private ?Tracking $searchTracking = null;


  public function getId(): ?int
  {
    return $this->id;
  }

  public function getLogoText(): ?string
  {
    return $this->logo_text;
  }

  public function setLogoText(string $logo_text): static
  {
    $this->logo_text = $logo_text;

    return $this;
  }

  public function getLogoImage(): ?string
  {
    return $this->logo_image;
  }

  public function setLogoImage(string $logo_image): static
  {
    $this->logo_image = $logo_image;

    return $this;
  }

  public function getLogoUrl(): ?string
  {
    return $this->logo_url;
  }

  public function setLogoUrl(string $logo_url): static
  {
    $this->logo_url = $logo_url;

    return $this;
  }

  public function getTrackingId(): ?int
  {
    return $this->tracking_id;
  }

  public function setTrackingId(?int $tracking_id): static
  {
    $this->tracking_id = $tracking_id;

    return $this;
  }

  public function getSearchPlaceholder(): ?string
  {
    return $this->search_placeholder;
  }

  public function setSearchPlaceholder(?string $search_placeholder): static
  {
    $this->search_placeholder = $search_placeholder;

    return $this;
  }

  public function getSearchIcon(): ?string
  {
    return $this->search_icon;
  }

  public function setSearchIcon(?string $search_icon): static
  {
    $this->search_icon = $search_icon;

    return $this;
  }

  public function getSearchTrackingId(): ?int
  {
    return $this->search_tracking_id;
  }

  public function setSearchTrackingId(?int $search_tracking_id): static
  {
    $this->search_tracking_id = $search_tracking_id;

    return $this;
  }

  public function getLogoTracking(): ?Tracking
  {
    return $this->tracking;
  }

  public function getSearchTracking(): ?Tracking
  {
    return $this->searchTracking;
  }


}
