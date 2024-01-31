<?php

namespace App\Interface;

interface SoftDeleteInterface
{
    public function getIsDeleted(): bool;
    public function setIsDeleted(bool $isDeleted): self;
}