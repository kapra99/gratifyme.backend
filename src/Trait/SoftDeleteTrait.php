<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait SoftDeleteTrait
{
    #[Groups(['BASE'])]
    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected ?bool $isDeleted = false;

    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}