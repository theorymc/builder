<?php

namespace Theory\Builder\Test;

use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_TestSuite;

class Listener implements PHPUnit_Framework_TestListener
{
    /**
     * @var string[]
     */
    private $pid = [];

    /**
     * Number of microseconds to wait between each check.
     *
     * @var int
     */
    private $delay = 250000;

    /**
     * Total number of checks to perform before timing out.
     *
     * @var int
     */
    private $limit = 200;

    /**
     * @var bool
     */
    private $run = false;

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // TODO: nothing
    }

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_Test $test
     * @param PHPUnit_Framework_AssertionFailedError $e
     * @param float $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        // TODO: nothing
    }

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // TODO: nothing
    }

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // TODO: nothing
    }

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        // TODO: nothing
    }

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->run) {
            return;
        }

        $this->run = true;

        $this->startServer();
        $this->waitForServer();
    }

    /**
     * Starts the Minecraft server and stores the PID of each related process.
     */
    private function startServer()
    {
        if (!$this->pid) {
            $hash = spl_object_hash($this);
            $path = realpath(__DIR__ . "/server");

            $this->log("Removing old logs...");
            $this->exec("rm {$path}/logs/*.gz");
            $this->exec("rm {$path}/logs/*.log");

            sleep(1);

            $this->log("Starting server...");
            $this->exec("cd {$path}; java -Xmx1024M -Xms1024M -jar {$path}/server.1.10.jar --nogui hash={$hash}");

            sleep(1);

            $this->log("Getting PID...");
            $output = $this->exec("ps -o pid,command | grep {$hash}", $silent = false, $background = false);

            if (count($output) > 0) {
                foreach ($output as $line) {
                    $parts = explode(" ", $line);
                    $this->pid[] = $parts[0];
                }
            }
        }
    }

    /**
     * Logs a message.
     *
     * @param string $message
     */
    private function log($message)
    {
        print "{$message}\n";
    }

    /**
     * Runs a command silently and in the background.
     *
     * @param string $command
     *
     * @param bool $silent
     * @param bool $background
     *
     * @return array|string
     */
    private function exec($command, $silent = true, $background = true)
    {
        if ($silent) {
            $command .= " > /dev/null 2> /dev/null";
        }

        if ($background) {
            $command .= " &";
        }

        exec($command, $output);

        return $output;
    }

    /**
     * Wait for the server to start.
     *
     * @throws Exception
     */
    private function waitForServer()
    {
        $tick = 0;

        $this->log("Waiting for server to start...");

        while ($tick < $this->limit) {
            usleep($this->delay);

            $path = realpath(__DIR__ . "/server/logs/latest.log");

            foreach (file($path) as $line) {
                if (stristr($line, "[Server thread/INFO]: Done")) {
                    $this->log("Running tests...");
                    return;
                }
            }
        }

        throw new Exception("Server did not start in allowed time");
    }

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->stopServer();
    }

    /**
     * Stops each process related to the Minecraft server.
     */
    public function stopServer()
    {
        foreach ($this->pid as $pid) {
            $this->exec("kill -9 {$pid}");
        }
    }

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        // TODO: nothing
    }

    /**
     * @inheritdoc
     *
     * @param PHPUnit_Framework_Test $test
     * @param float $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        // TODO: nothing
    }
}
