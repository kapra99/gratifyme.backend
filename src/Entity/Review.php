<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['BASE'])]
    private string $id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(nullable: true)]
    private ?bool $subreview = null;

    #[ORM\ManyToOne(inversedBy: 'review')]
    private ?User $evaluatedUser = null;

//    #[ORM\ManyToOne(inversedBy: 'review')]
//    private ?User $user = null;
    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function isSubreview(): ?bool
    {
        return $this->subreview;
    }

    public function setSubreview(?bool $subreview): static
    {
        $this->subreview = $subreview;

        return $this;
    }

    public function getEvaluatedUser(): ?User
    {
        return $this->evaluatedUser;
    }

    public function setEvaluatedUser(?User $evaluatedUser): static
    {
        $this->evaluatedUser = $evaluatedUser;

        return $this;
    }

//    public function getUser(): ?User
//    {
//        return $this->user;
//    }
//
//    public function setUser(?User $user): static
//    {
//        $this->user = $user;
//
//        return $this;
//    }
}
