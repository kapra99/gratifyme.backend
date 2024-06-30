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
    #[ORM\ManyToOne(inversedBy: 'review')]
    private ?User $evaluatedUser = null;

    #[Groups(['BASE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author_firstName = null;
    #[Groups(['BASE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author_lastName = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[Groups(["BASE"])]
    private ?File $avatar = null;

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

    public function getAuthorFirstName(): ?string
    {
        return $this->author_firstName;
    }

    public function getAuthorLastName(): ?string
    {
        return $this->author_lastName;
    }

    public function setAuthorFirstName(?string $author_firstName): void
    {
        $this->author_firstName = $author_firstName;
    }


    public function setAuthorLastName(?string $author_lastName): void
    {
        $this->author_lastName = $author_lastName;
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

    public function getEvaluatedUser(): ?User
    {
        return $this->evaluatedUser;
    }

    public function setEvaluatedUser(?User $evaluatedUser): static
    {
        $this->evaluatedUser = $evaluatedUser;

        return $this;
    }
    public function getAvatar(): ?File
    {
        return $this->avatar;
    }

    public function setAvatar(?File $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }
}
