<?php

namespace App\Entity;

use App\Repository\DeskRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEntity;
use App\Entity\Traits\TimestampableTrait;

#[ORM\Entity(repositoryClass: DeskRepository::class)]
class Desk extends BaseEntity {
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $cpu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCpu(): ?string
    {
        return $this->cpu;
    }

    public function setCpu(string $cpu): static
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getUniqueFields(): array
    {
        return ['name'];
    }
}
