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
    #[Groups(['BASE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;
    #[Groups(['BASE'])]
    #[ORM\Column(nullable: true)]
    private ?float $rating = null;
    #[Groups(['BASE'])]
    #[ORM\Column(nullable: true)]
    private ?bool $subreview = null;
    #[ORM\ManyToOne(inversedBy: 'review')]
    private ?User $evaluatedUser = null;

    #[ORM\ManyToOne(inversedBy: 'reviewsAuthor')]
    private ?User $author = null;

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

    #[Groups(["review"])]
    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    #[Groups(["review"])]
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
    #[Groups(['BASE'])]
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
