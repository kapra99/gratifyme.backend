<?php

namespace App\Dto\Api\V1\Response\WorkPlace;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\WorkPlace;
use Symfony\Component\Serializer\Attribute\Groups;

#[AllowDynamicProperties] class WorkPlaceDto extends ResponseDto
{
    /**
     * @var WorkPlace[]
     */
    #[Groups(['BASE'])]
    protected $items;

    /**
     * Get the value of items.
     *
     * @return WorkPlace[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set the value of items.
     *
     * @param WorkPlace[] $items
     *
     * @return self
     */
    public function setItems(array $items)
    {
        $this->items = $items;

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