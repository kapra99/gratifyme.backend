<?php

namespace App\Dto\Api\V1\Response\User;

use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

class GetUserDto extends ResponseDto
{
    /**
     * @var User
     */
    #[Groups(['BASE'])]
    protected $user;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }


}