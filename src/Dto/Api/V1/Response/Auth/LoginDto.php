<?php

namespace App\Dto\Api\V1\Response\Auth;

use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\UserToken;
use Symfony\Component\Serializer\Annotation\Groups;

class LoginDto extends ResponseDto
{
    /**
     * @var UserToken
     */
    #[Groups(['BASE'])]
    protected $details;

    /**
     * @return UserToken
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return self
     */
    public function setDetails(UserToken $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function setToken(string $jwtToken)
    {
        $this->details->setToken($jwtToken);

        return $this;
    }
}
