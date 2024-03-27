<?php

namespace App\Dto\Api\V1\Response\User;

use App\Dto\Api\V1\Response\ResponseDto;
use Symfony\Component\Serializer\Annotation\Groups;

class UploadAvatarDto extends ResponseDto
{
    /**
     * @var String|null
     */
    #[Groups(['BASE'])]
    protected $avatarPath;

    /**
     * Get the value of details.
     */
    public function getDetails(): ?String
    {
        return $this->avatarPath;
    }

    /**
     * Set the value of details.
     *
     * @return self
     */
    public function setDetails(?String $avatarPath)
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

}