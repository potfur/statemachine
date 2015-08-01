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
     * Timeout
     *
     * @var Timeout|null
     */
    private $timeout;

    /**
     * Additional comment
     *
     * @var string
     */
    private $attributes;

    /**
     * Constructor
     *
     * @param string            $name        event name
     * @param string            $targetState state where subject will go when all commands were executed successfully
     * @param string            $errorState  state where subject will go when command execution failed
     * @param CommandCollection $commands    collection of commands
     * @param Timeout|null      $timeout     date or interval when event should timeout
     * @param array             $attributes  additional attributes, like comment etc.
     */
    public function __construct($name, $targetState = null, $errorState = null, CommandCollection $commands = null, Timeout $timeout = null, array $attributes = [])
    {
        $this->assertName($name);

        $this->name = $name;
        $this->targetState = $targetState;
        $this->errorState = $errorState;
        $this->commands = $commands;
        $this->timeout = $timeout;
        $this->attributes = new AttributeCollection($attributes);
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
     * Return list of transition types with target states
     *
     * @return array
     */
    public function getStates()
    {
        return [
            'target' => $this->targetState,
            'error' => $this->errorState
        ];
    }

    /**
     * Return attributes container

     *
*@return AttributeCollectionInterface
     */
    public function getAttributes()
    {
        return $this->attributes;
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
     * Return timeout value
     *
     * @return Timeout|null
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Return when event timeout
     *
     * @param \DateTime $now date will be used as reference for timeouts defined as intervals
     *
     * @return \DateTime
     */
    public function timeoutAt(\DateTime $now)
    {
        return $this->timeout->timeoutAt($now);
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
