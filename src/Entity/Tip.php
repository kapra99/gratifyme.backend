<?php

namespace App\Entity;

use App\Repository\TipRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TipRepository::class)]
class Tip
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['BASE'])]
    private string $id;

    #[ORM\Column]
    private ?float $tipAmount = null;

    #[ORM\Column(length: 255)]
    private ?string $tipDate = null;

    #[ORM\ManyToOne(inversedBy: 'tips')]
    private ?User $user = null;
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    #[Groups(["BASE"])]
    public function getId(): ?string
    {
        return $this->id;
    }
    #[Groups(["BASE"])]
    public function getTipAmount(): ?float
    {
        return $this->tipAmount;
    }

    public function setTipAmount(float $tipAmount): static
    {
        $this->tipAmount = $tipAmount;

        return $this;
    }
    #[Groups(["BASE"])]
    public function getTipDate(): ?string
    {
        return $this->tipDate;
    }

    public function setTipDate(string $tipDate): static
    {
        $this->tipDate = $tipDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
