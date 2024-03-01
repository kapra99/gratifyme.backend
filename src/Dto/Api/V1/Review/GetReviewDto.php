<?php

namespace App\Dto\Api\V1\Review;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\Review;
use Symfony\Component\Serializer\Annotation\Groups;

#[AllowDynamicProperties] class GetReviewDto extends ResponseDto
{
    /**
     * @var Review[]
     */
    #[Groups(['BASE'])]
    protected $reviews;

    /**
     * Get the value of reviews.
     *
     * @return Review[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set the value of reviews.
     *
     * @param Review[] $reviews
     *
     * @return self
     */
    public function setReviews(array $reviews)
    {
        $this->reviews = $reviews;

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