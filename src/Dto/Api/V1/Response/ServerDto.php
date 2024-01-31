<?php

namespace App\Dto\Api\V1\Response;

use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

class ServerDto
{
    /**
     * @var int
     */
    #[Groups(['BASE'])]
    protected $httpCode;

    /**
     * @var string
     */
    #[Groups(['BASE'])]
    protected $date;

    public function __construct()
    {
        $this->date =  (new DateTime())->format("c");
    }

    /**
     * Get the value of date
     *
     * @return  string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @param  string  $date
     *
     * @return  self
     */
    public function setDate(string $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of httpCode
     *
     * @return  int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Set the value of httpCode
     *
     * @param  int  $httpCode
     *
     * @return  self
     */
    public function setHttpCode(int $httpCode)
    {
        $this->httpCode = $httpCode;

        return $this;
    }
}
