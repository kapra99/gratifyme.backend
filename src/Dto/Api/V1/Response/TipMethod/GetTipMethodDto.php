<?php

namespace App\Dto\Api\V1\Response\TipMethod;

use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\TipMethod;
use Symfony\Component\Serializer\Annotation\Groups;

class GetTipMethodDto extends ResponseDto
{
    /**
     * @var TipMethod[]
     */
    #[Groups(['BASE'])]
    protected $tipMethods;

    /**
     * Get the value of tipmethods.
     *
     * @return TipMethod[]
     */
    public function getTipMethods()
    {
        return $this->tipMethods;
    }

    /**
     * Set the value of tipmethods.
     *
     * @param TipMethod[] $tipMethods
     *
     * @return self
     */
    public function setTipMethods(array $tipMethods)
    {
        $this->tipMethods = $tipMethods;

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