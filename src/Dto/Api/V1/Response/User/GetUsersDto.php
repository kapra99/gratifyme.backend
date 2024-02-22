<?php

namespace App\Dto\Api\V1\Response\User;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] class GetUsersDto extends ResponseDto
{

    /**
     * @var User[]
     */
    #[Groups(['BASE'])]
    protected $users;

    /**
     * Get the value of items.
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set the value of items.
     *
     * @param User[] $users
     *
     * @return self
     */
    public function setUsers(array $users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get the value of count.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Set the value of count.
     *
     * @return self
     */
    public function setCount(int $count)
    {
        $this->count = $count;

        return $this;
    }
}