<?php

namespace App\Dto\Api\V1\Response\City;

use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\City;
use Symfony\Component\Serializer\Annotation\Groups;

class GetCityDto extends ResponseDto
{

    /**
     * @var City[]
     */
    #[Groups(['BASE'])]
    protected $cities;

    /**
     * Get the value of cities.
     *
     * @return City[]
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Set the value of cities.
     *
     * @param City[] $cities
     *
     * @return self
     */
    public function setCities(array $cities)
    {
        $this->cities = $cities;

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