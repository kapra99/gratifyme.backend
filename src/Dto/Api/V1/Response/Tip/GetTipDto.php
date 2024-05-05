<?php

namespace App\Dto\Api\V1\Response\Tip;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\Tip;
use Symfony\Component\Serializer\Annotation\Groups;

class GetTipDto extends ResponseDto
{
    /**
     * @var Tip[]
     */
    #[Groups(['BASE'])]
    protected $tips;

    /**
     * Get the value of tips.
     *
     * @return Tip[]
     */
    public function getTips()
    {
        return $this->tips;
    }

    /**
     * Set the value of tips.
     *
     * @param Tip[] $tips
     *
     * @return self
     */
    public function setTips(array $tips)
    {
        $this->tips = $tips;

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