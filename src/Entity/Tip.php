<?php

namespace App\Entity;

use App\Repository\TipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipRepository::class)]
class Tip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $tipAmount = null;

    #[ORM\Column(length: 255)]
    private ?string $tipDate = null;

    #[ORM\ManyToOne(inversedBy: 'tips')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipAmount(): ?float
    {
        return $this->tipAmount;
    }

    public function setTipAmount(float $tipAmount): static
    {
        $this->tipAmount = $tipAmount;

        return $this;
    }

    public function getTipDate(): ?string
    {
        return $this->tipDate;
    }

    public function setTipDate(string $tipDate): static
    {
        $this->tipDate = $tipDate;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
