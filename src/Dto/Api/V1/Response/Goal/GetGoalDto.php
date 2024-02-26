<?php

namespace App\Dto\Api\V1\Response\Goal;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\Goal;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] class GetGoalDto extends ResponseDto
{
    /**
     * @var Goal[]
     */
    #[Groups(['BASE'])]
    protected $goals;

    /**
     * Get the value of items.
     *
     * @return Goal[]
     */
    public function getItems()
    {
        return $this->goals;
    }

    /**
     * Set the value of items.
     *
     * @param Goal[] $goal
     *
     * @return self
     */
    public function setGoals(array $goals)
    {
        $this->goals = $goals;

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