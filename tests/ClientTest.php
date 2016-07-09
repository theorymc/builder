<?php

namespace Theory\Builder\Test;

use PHPUnit_Framework_TestCase;
use Theory\Builder\Client;

class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function commandsExecuted()
    {
        $builder = new Client(
            $address = "127.0.0.1", $port = 25575, $password = "hello", $timeout = 3
        );

        $builder->exec(
            $command = "/say hello world"
        );

        $path = realpath(__DIR__ . "/server/logs/latest.log");

        foreach (file($path) as $line) {
            if (stristr($line, "[Rcon] hello world")) {
                return;
            }
        }

        $this->fail("Command not executed");
    }
}
