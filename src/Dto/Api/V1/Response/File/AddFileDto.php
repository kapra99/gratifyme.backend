<?php

namespace App\Dto\Api\V1\Response\File;

use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\File;
use Symfony\Component\Serializer\Annotation\Groups;

class AddFileDto extends ResponseDto
{
    /**
     * @var File|null
     */
    #[Groups(['BASE'])]
    protected $details;

    /**
     * Get the value of details.
     */
    public function getDetails(): ?File
    {
        return $this->details;
    }

    /**
     * Set the value of details.
     *
     * @return self
     */
    public function setDetails(?File $details)
    {
        $this->details = $details;

        return $this;
    }
}
