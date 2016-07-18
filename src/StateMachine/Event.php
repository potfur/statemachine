<?php

declare(strict_types = 1);

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine;

use StateMachine\Exception\InvalidArgumentException;
use StateMachine\Payload;

/**
 * Describes state machine event
 *
 * @package StateMachine
 */
final class Event
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
     * Command executed when event is triggered
     *
     * @var callable
     */
    private $command;

    /**
     * Additional attributes
     *
     * @var Attributes
     */
    private $attributes;

    /**
     * Constructor
     *
     * @param string   $name        event name
     * @param string   $targetState state where subject will go when all commands were executed successfully
     * @param string   $errorState  state where subject will go when command execution failed
     * @param callable $command     collection of commands
     * @param array    $attributes  additional attributes.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $name,
        $targetState = null,
        $errorState = null,
        callable $command = null,
        array $attributes = []
    ) {
        if (empty($name)) {
            throw InvalidArgumentException::emptyEventName();
        }

        $this->name = $name;
        $this->targetState = $targetState;
        $this->errorState = $errorState;
        $this->command = $command;
        $this->attributes = new Attributes($attributes);
    }

    /**
     * Return event name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Return name of success state
     * Subject will go into this state when all commands execute properly
     *
     * @return null|string
     */
    public function targetState()
    {
        return $this->targetState;
    }

    /**
     * Return error state name
     * State where subject will go when something goes wrong
     *
     * @return null|string
     */
    public function errorState()
    {
        return $this->errorState;
    }

    /**
     * Return attributes container
     *
     * @return Attributes
     */
    public function attributes(): Attributes
    {
        return $this->attributes;
    }

    /**
     * Triggers event and return next state name or null if there is no state change
     *
     * @param Payload $payload
     *
     * @return null|string
     */
    public function trigger(Payload $payload)
    {
        if (!$this->command) {
            return $this->targetState();
        }

        return call_user_func($this->command, $payload) ? $this->targetState() : $this->errorState();
    }

    /**
     * Return event string representation - its name
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name();
    }
}
