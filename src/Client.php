<?php

namespace Theory\Builder;

use xPaw\SourceQuery\Exception\InvalidArgumentException;
use xPaw\SourceQuery\Exception\TimeoutException;
use xPaw\SourceQuery\SourceQuery;

class Client
{
    /**
     * @var SourceQuery
     */
    private $query;

    /**
     * @var int
     */
    private $engine = SourceQuery::SOURCE;

    /**
     * @param string $address
     * @param int $port
     * @param string $password
     * @param int $timeout
     */
    public function __construct($address, $port, $password, $timeout = 1)
    {
        $this->query = $this->newQuery($address, $port, $password, $timeout);
    }

    /**
     * Creates a new SourceQuery instance.
     *
     * @param string $address
     * @param int $port
     * @param string $password
     * @param int $timeout
     *
     * @return SourceQuery
     *
     * @throws InvalidArgumentException
     * @throws TimeoutException
     */
    private function newQuery($address, $port, $password, $timeout = 1)
    {
        $query = new SourceQuery();
        $query->Connect($address, $port, $timeout, $this->engine);
        $query->SetRconPassword($password);

        return $query;
    }

    /**
     * Runs a command on the server.
     *
     * @param string $command
     *
     * @return bool|string
     */
    public function exec($command)
    {
        return $this->query->Rcon($command);
    }
}
