<?php

namespace App\Entity;

use App\Repository\TipMethodRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TipMethodRepository::class)]
class TipMethod
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['BASE'])]
    private string $id;
    #[Groups(['BASE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;
    #[Groups(['BASE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tipMethodUrl = null;
    #[Groups(['BASE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tipMethodStaticUrl = null;
    #[Groups(['BASE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $qrCodeImgPath = null;
    #[ORM\ManyToOne(inversedBy: 'tipMethod')]
    private ?User $user = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    #[Groups(["tip-method"])]
    public function getId(): ?string
    {
        return $this->id;
    }
    #[Groups(["tip-method"])]
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }
    #[Groups(["tip-method"])]
    public function getTipMethodUrl(): ?string
    {
        return $this->tipMethodUrl;
    }

    public function setTipMethodUrl(?string $tipMethodUrl): static
    {
        $this->tipMethodUrl = $tipMethodUrl;

        return $this;
    }
    #[Groups(["tip-method"])]
    public function getTipMethodStaticUrl(): ?string
    {
        return $this->tipMethodStaticUrl;
    }

    public function setTipMethodStaticUrl(?string $tipMethodStaticUrl): static
    {
        $this->tipMethodStaticUrl = $tipMethodStaticUrl;

        return $this;
    }
    #[Groups(["tip-method"])]
    public function getQrCodeImgPath(): ?string
    {
        return $this->qrCodeImgPath;
    }

    public function setQrCodeImgPath(?string $qrCodeImgPath): static
    {
        $this->qrCodeImgPath = $qrCodeImgPath;

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