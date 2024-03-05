<?php

namespace App\Dto\Api\V1\Response\WorkPlace;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\WorkPlace;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] class GetWorkPlacesDto extends ResponseDto
{
    /**
     * @var WorkPlace
     */
    #[Groups(['BASE'])]
    protected $workPlaces;

    /**
     * Get the value of workPlaces.
     *
     * @return WorkPlace
     */
    public function getWorkPlaces()
    {
        return $this->workPlaces;
    }

    /**
     * Set the value of workPlaces.
     *
     * @param WorkPlace $workPlaces
     *
     * @return self
     */
    public function setWorkPlaces(array $workPlaces)
    {
        $this->workPlaces = $workPlaces;

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