<?php

namespace App\Dto\Api\V1\Response\WorkPlace;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\WorkPlace;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] class GetWorkPlaceDto extends ResponseDto
{
    /**
     * @var WorkPlace
     */
    #[Groups(['BASE'])]
    protected $workPlace;

    /**
     * Get the value of workPlace.
     *
     * @return WorkPlace
     */
    public function getWorkPlace()
    {
        return $this->workPlace;
    }

    /**
     * Set the value of workPlace.
     *
     * @param WorkPlace $workPlace
     *
     * @return self
     */
    public function setWorkPlace(WorkPlace $workPlace)
    {
        $this->workPlace = $workPlace;

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