<?php

namespace App\Entity;

use App\Repository\GoalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GoalRepository::class)]
class Goal
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['BASE'])]
    private string $id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?float $endGoalSum = null;

    #[ORM\Column(nullable: true)]
    private ?float $currentGoalSum = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $priority = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEndGoalSum(): ?float
    {
        return $this->endGoalSum;
    }

    public function setEndGoalSum(?float $endGoalSum): static
    {
        $this->endGoalSum = $endGoalSum;

        return $this;
    }

    public function getCurrentGoalSum(): ?float
    {
        return $this->currentGoalSum;
    }

    public function setCurrentGoalSum(?float $currentGoalSum): static
    {
        $this->currentGoalSum = $currentGoalSum;

        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function setStartDate(?string $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getuser(): ?User
    {
        return $this->user;
    }

    public function setuser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}