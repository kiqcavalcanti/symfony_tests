<?php

namespace App\Entity;

use App\Repository\TrackingRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEntity;
use App\Entity\Traits\SoftDeletableTrait;
use App\Entity\Traits\TimestampableTrait;

#[ORM\Entity(repositoryClass: TrackingRepository::class)]
class Tracking extends BaseEntity {
    use TimestampableTrait;
    use SoftDeletableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

  #[ORM\Column(length: 255)]
  private ?string $label = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

  public function getAction(): ?string
  {
    return $this->action;
  }

  public function setAction(string $action): static
  {
    $this->action = $action;

    return $this;
  }
}
