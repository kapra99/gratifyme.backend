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
    #[ORM\Column(nullable: true)]
    private ?string $mainreviewId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reviewerId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $evaluatedUser = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?string
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

    public function getreviewerId(): ?string
    {
        return $this->reviewerId;
    }

    public function setreviewerId(?string $reviewerId): static
    {
        $this->reviewerId = $reviewerId;

        return $this;
    }

    public function getEvaluatedUser(): ?string
    {
        return $this->evaluatedUser;
    }

    public function setEvaluatedUser(?string $evaluatedUser): static
    {
        $this->evaluatedUser = $evaluatedUser;

        return $this;
    }
}
