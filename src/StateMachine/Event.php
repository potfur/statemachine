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

use StateMachine\Exception\InvalidArgumentException;

/**
 * Describes state machine event
 *
 * @package StateMachine
 */
final class Event implements EventInterface
{
    /**
     * Event name
     *
     * @var string
     */
    private $name;

    /**
     * Target state
     *
     * @var string
     */
    private $targetState;

    /**
     * Error state
     *
     * @var string
     */
    private $errorState;

    /**
     * Commands collection
     *
     * @var CommandCollection
     */
    private $commands;

    /**
     * Timeout as fixed date or relative interval
     *
     * @var \DateInterval|\DateTime
     */
    private $timeout;

    /**
     * Constructor
     *
     * @param string                       $name        event name
     * @param string                       $targetState state where subject will go when all commands were executed successfully
     * @param string                       $errorState  state where subject will go when command execution failed
     * @param CommandCollection            $commands    collection of commands
     * @param null|\DateInterval|\DateTime $timeout     date or interval when event should timeout
     */
    public function __construct($name, $targetState = null, $errorState = null, CommandCollection $commands = null, $timeout = null)
    {
        $this->assertName($name);
        $this->assertTimeout($timeout);

        $this->name = $name;
        $this->targetState = $targetState;
        $this->errorState = $errorState;
        $this->commands = $commands;
        $this->timeout = $timeout;
    }

    /**
     * Assert if name is non empty string
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     */
    private function assertName($name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Invalid event name, can not be empty string');
        }
    }

    /**
     * Assert if timeout is \DateInterval, \DateTime or null
     *
     * @param null|\DateInterval|\DateTime $timeout
     *
     * @throws InvalidArgumentException
     */
    private function assertTimeout($timeout)
    {
        if ($timeout !== null && !$timeout instanceof \DateInterval && !$timeout instanceof \DateTime) {
            throw new InvalidArgumentException('Invalid timeout value, must be instance of \DateInterval for relative timeout, \DateTime for fixed date or null when without timeout');
        }
    }

    /**
     * Return event name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return name of success state
     * Subject will go into this state when all commands execute properly
     *
     * @return string
     */
    public function getTargetState()
    {
        return $this->targetState;
    }

    /**
     * Return error state name
     * State where subject will go when something goes wrong
     *
     * @return string
     */
    public function getErrorState()
    {
        return $this->errorState;
    }

    /**
     * Return true if event has timeout
     *
     * @return bool
     */
    public function hasTimeout()
    {
        return $this->timeout !== null;
    }

    /**
     * Return when event timeout
     *
     * @param \DateTime $now date will be used as reference for timeouts defined as intervals
     *
     * @return \DateTime
     */
    public function getTimeout(\DateTime $now)
    {
        if ($this->timeout instanceof \DateTime) {
            return $this->timeout;
        }

        if ($this->timeout instanceof \DateInterval) {
            return $now->add($this->timeout);
        }

        return $now;
    }

    /**
     * Triggers event and return next state name or null if there is no state change
     *
     * @param PayloadInterface $payload
     *
     * @return string
     */
    public function trigger(PayloadInterface $payload)
    {
        return $this->commands->execute($payload) ? $this->getTargetState() : $this->getErrorState();
    }

    /**
     * Return event string representation - its name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
