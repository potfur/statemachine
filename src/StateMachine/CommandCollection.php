<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine;

/**
 * Collection class containing event commands
 *
 * @package StateMachine
 */
final class CommandCollection implements \Countable
{
    private $commands = [];
    private $status = [];

    /**
     * Constructor
     * Builds command collection from passed array
     *
     * @param callable[] $commands
     *
     * @throws \Exception
     */
    public function __construct(array $commands = [])
    {
        foreach ($commands as $command) {
            $this->add($command);
        }

        $this->resetStatus();
    }

    /**
     * Add command to collection
     *
     * @param callable $command
     */
    public function add(callable $command)
    {
        $this->commands[] = $command;
    }

    /**
     * Reset status
     * Fills status array with null (not executed) values for each command
     */
    public function resetStatus()
    {
        $this->status = array_fill(0, count($this->commands), null);
    }

    /**
     * Execute all commands in collection and returns true if everything went ok.
     * Stops when any command returns false and returns false.
     *
     * @param PayloadInterface $payload
     *
     * @return bool
     */
    public function execute(PayloadInterface $payload)
    {
        $this->resetStatus();

        foreach ($this->commands as $i => $command) {
            $result = call_user_func($command, $payload);
            $this->status[$i] = $result;

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return array of all commands with execution status:
     *  - true - for successfully executed
     *  - false - for failed execution
     *  - null - for not executed
     *
     * @return array
     */
    public function getExecutionStatus()
    {
        return $this->status;
    }

    /**
     * Count elements of an object
     *
     * @return int
     */
    public function count()
    {
        return count($this->commands);
    }
}
