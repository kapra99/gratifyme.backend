<?php

namespace App\Dto\Api\V1\Response;

use Symfony\Component\Serializer\Annotation\Groups;

class ResponseDto
{
    /**
     * @var string[]
     */
    #[Groups(['BASE'])]
    protected $messages = [];

    /**
     * @var ServerDto
     */
    #[Groups(['BASE'])]
    protected $server;

    #[Groups(['DEBUG'])]
    protected $trace;

    public function __construct()
    {
        $this->server = new ServerDto();
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     *
     * @return self
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Get the value of server.
     *
     * @return ServerDto
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set the value of server.
     *
     * @return self
     */
    public function setServer(ServerDto $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get the value of trace.
     *
     * @return array
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * Set the value of trace.
     *
     * @return self
     */
    public function setTrace(array $trace)
    {
        $this->trace = $trace;

        return $this;
    }
}
