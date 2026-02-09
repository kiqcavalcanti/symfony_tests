<?php

namespace App\Entity;

use App\Repository\ConsoleRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEntity;
use App\Entity\Traits\TimestampableTrait;

#[ORM\Entity(repositoryClass: ConsoleRepository::class)]
class Console extends BaseEntity {
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $controle = null;

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

    public function getControle(): ?string
    {
        return $this->controle;
    }

    public function setControle(?string $controle): static
    {
        $this->controle = $controle;

        return $this;
    }

    public function getUniqueFields(): array
    {
        return ['name'];
    }
}
