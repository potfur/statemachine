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

use StateMachine\Collection\States;
use StateMachine\Exception\InvalidArgumentException;
use StateMachine\Exception\InvalidStateException;
use StateMachine\Payload\Payload;

/**
 * State machine process
 *
 * @package StateMachine
 */
final class Process implements ProcessInterface
{
    /**
     * Process/schema name
     *
     * @var string
     */
    private $name;

    /**
     * Initial state
     *
     * @var string
     */
    private $initialState;

    /**
     * Schema states
     *
     * @var States|State[]
     */
    private $states = [];

    /**
     * @param string  $name         process/schema name
     * @param string  $initialState initial state for entities starting process
     * @param State[] $states
     *
     * @throws InvalidArgumentException|InvalidStateException
     */
    public function __construct($name, $initialState, array $states)
    {
        if (empty($name)) {
            throw InvalidArgumentException::emptyProcessName();
        }

        $this->name = $name;
        $this->initialState = $initialState;

        $this->states = new States($states);

        if (!$this->states->has($this->initialState)) {
            throw InvalidStateException::missingInitialState($this->name, $this->initialState);
        }
    }

    /**
     * Return process name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Return initial state
     * All entities without state will have this one
     *
     * @return State
     */
    public function initialState(): State
    {
        return $this->state($this->initialState);
    }

    /**
     * Return state from collection by its name
     *
     * @param string $name
     *
     * @return State
     */
    public function state($name): State
    {
        return $this->states->get($name);
    }

    /**
     * Return all states
     *
     * @return State[]
     */
    public function states(): array
    {
        return $this->states->all();
    }

    /**
     * Trigger event for payload
     * Return next state name
     *
     * @param string  $event
     * @param Payload $payload
     *
     * @return string
     */
    public function triggerEvent($event, Payload $payload): string
    {
        return $this->state($payload->state())->triggerEvent($event, $payload);
    }
}
