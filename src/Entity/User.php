<?php

namespace App\Entity;

use App\Interface\SoftDeleteInterface;
use App\Repository\UserRepository;
use App\Trait\SoftDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Index(fields: ['isDeleted'])]
#[ORM\Table(name: '`user`')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, SoftDeleteInterface
{
    use SoftDeleteTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(["BASE"])]
    private string $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["BASE"])]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["BASE"])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["BASE"])]
    private ?string $surName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["BASE"])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable:true)]
    #[Groups(["BASE"])]
    private ?string $nickName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["BASE"])]
    private ?string $dateOfBirth = null;

    #[ORM\Column(nullable: true)]
    private ?int $rating = null;

    #[ORM\ManyToOne(inversedBy: 'user')]
    #[MaxDepth(1)]
    private ?WorkPlace $workPlace = null;

    #[ORM\ManyToOne(inversedBy: 'user')]
    #[MaxDepth(1)]
    private ?WorkingPosition $workingPosition = null;

    #[ORM\OneToMany(mappedBy: 'evaluatedUser', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TipMethod::class)]
    private Collection $tipMethod;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?File $avatar = null;


    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->reviews = new ArrayCollection();
        $this->tipMethod = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSurName(): ?string
    {
        return $this->surName;
    }

    public function setSurName(?string $surName): static
    {
        $this->surName = $surName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(string $nickName): static
    {
        $this->nickName = $nickName;

        return $this;
    }

    public function getDateOfBirth(): ?string
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?string $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getWorkPlace(): ?WorkPlace
    {
        return $this->workPlace;
    }
    #[Groups(["BASE"])]
    public function getWorkPlaceId(): ?string
    {
        if ($this->workPlace !== null) {
            return $this->workPlace->getId();
        }

        return null;
    }

    public function setWorkPlace(?WorkPlace $workPlace): static
    {
        $this->workPlace = $workPlace;

        return $this;
    }
    #[Groups(["BASE"])]
    public function getWorkingPosition(): ?WorkingPosition
    {
        return $this->workingPosition;
    }
    #[Groups(["BASE"])]
    public function getWorkingPositionId (): ?string
    {
        if($this->workingPosition !== null){
            return $this->workingPosition->getId();
        }
        return null;
    }

    public function setWorkingPosition(?WorkingPosition $workingPosition): static
    {
        $this->workingPosition = $workingPosition;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setEvaluatedUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getEvaluatedUser() === $this) {
                $review->setEvaluatedUser(null);
            }
        }

        return $this;
    }
    #[Groups(["BASE"])]
    /**
     * @return Collection<int, TipMethod>
     */
    public function getTipMethod(): Collection
    {
        return $this->tipMethod;
    }

    public function addTipMethod(TipMethod $tipMethod): static
    {
        if (!$this->tipMethod->contains($tipMethod)) {
            $this->tipMethod->add($tipMethod);
            $tipMethod->setUser($this);
        }

        return $this;
    }

    public function removeTipMethod(TipMethod $tipMethod): static
    {
        if ($this->tipMethod->removeElement($tipMethod)) {
            // set the owning side to null (unless already changed)
            if ($tipMethod->getUser() === $this) {
                $tipMethod->setUser(null);
            }
        }

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