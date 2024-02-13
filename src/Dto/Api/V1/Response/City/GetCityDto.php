<?php

namespace App\Dto\Api\V1\Response\City;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\City;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] class GetCityDto extends ResponseDto
{

    /**
     * @var City[]
     */
    #[Groups(['BASE'])]
    protected $cities;
    protected $city;

    /**
     * Get the value of items.
     *
     * @return City[]
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Set the value of items.
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
    public function setCity(City $city){

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