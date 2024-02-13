<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['BASE'])]
    private string $id;
    #[Groups(['BASE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[Groups(['BASE'])]
    #[ORM\OneToMany(mappedBy: 'city', targetEntity: WorkPlace::class)]
    private Collection $workplace;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->workplace = new ArrayCollection();
    }

    public function getId(): ?int
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

    /**
     * @return Collection<int, WorkPlace>
     */
    public function getWorkPlace(): Collection
    {
        return $this->workplace;
    }

    public function addWorkPlace(WorkPlace $workplace): static
    {
        if (!$this->workplace->contains($workplace)) {
            $this->workplace->add($workplace);
            $workplace->setCity($this);
        }

        return $this;
    }

    public function removeWorkPlace(WorkPlace $workplace): static
    {
        if ($this->workplace->removeElement($workplace)) {
            // set the owning side to null (unless already changed)
            if ($workplace->getCity() === $this) {
                $workplace->setCity(null);
            }
        }

        return $this;
    }
}