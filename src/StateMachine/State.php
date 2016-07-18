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

use StateMachine\Collection\Events;
use StateMachine\Exception\InvalidArgumentException;
use StateMachine\Payload\Payload;

/**
 * State machine state representation
 *
 * @package StateMachine
 */
final class State
{
    /**
     * State name
     *
     * @var string
     */
    private $name;

    /**
     * State events
     *
     * @var Events
     */
    private $events;

    /**
     * Additional attributes
     *
     * @var Attributes
     */
    private $attributes;

    /**
     * @param string  $name       state name
     * @param Event[] $events     list of events in state
     * @param array   $attributes additional attributes
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name, array $events = [], array $attributes = [])
    {
        if (empty($name)) {
            throw InvalidArgumentException::emptyStateName();
        }

        $this->name = $name;
        $this->events = new Events($events);
        $this->attributes = new Attributes($attributes);
    }

    /**
     * Return state name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Return event collection
     *
     * @return Event[]
     */
    public function events(): array
    {
        return $this->events->all();
    }

    /**
     * Return true if event exists in collection
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasEvent($name): bool
    {
        return $this->events->has($name);
    }

    /**
     * Return event with given name
     *
     * @param string $name
     *
     * @return Event
     */
    public function event($name): Event
    {
        return $this->events->get($name);
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
     * Triggers event with given name and payload
     * Returns name of next state or null if no change
     *
     * @param string  $name
     * @param Payload $payload
     *
     * @return null|string
     */
    public function triggerEvent($name, Payload $payload)
    {
        return $this->event($name)->trigger($payload);
    }

    /**
     * Return state string representation - its name
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name();
    }
}
