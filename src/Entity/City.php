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

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: WorkPlace::class)]
    private Collection $workPlaces;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->workPlaces = new ArrayCollection();
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

    /**
     * @return Collection<int, WorkPlace>
     */
    public function getWorkPlaces(): Collection
    {
        return $this->workPlaces;
    }

    public function addWorkPlace(WorkPlace $workPlace): static
    {
        if (!$this->workPlaces->contains($workPlace)) {
            $this->workPlaces->add($workPlace);
            $workPlace->setCity($this);
        }

        return $this;
    }

    public function removeWorkPlace(WorkPlace $workPlace): static
    {
        if ($this->workPlaces->removeElement($workPlace)) {
            // set the owning side to null (unless already changed)
            if ($workPlace->getCity() === $this) {
                $workPlace->setCity(null);
            }
        }

        return $this;
    }
}