<?php

namespace App\Dto\Api\V1\Response\WorkingPosition;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\WorkingPosition;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] class GetWorkingPositionDto extends ResponseDto
{
    /**
     * @var WorkingPosition[]
     */
    #[Groups(['BASE'])]
    protected $workingPositions;

    /**
     * Get the value of workingPositions.
     *
     * @return WorkingPosition[]
     */
    public function getWorkingPositions()
    {
        return $this->workingPositions;
    }

    /**
     * Set the value of workingPositions.
     *
     * @param WorkingPosition[] $workingPositions
     *
     * @return self
     */
    public function setWorkingPosition(array $workingPositions)
    {
        $this->workingPositions = $workingPositions;

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