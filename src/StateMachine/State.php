<?php

namespace StateMachine;

use StateMachine\Exception\InvalidArgumentException;

/**
 * State machine state representation
 *
 * @package StateMachine
 */
final class State implements StateInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Event[]
     */
    private $events;

    /**
     * @var Flag[]
     */
    private $flags;

    /**
     * @param string  $name
     * @param Event[] $events
     * @param Flag[]  $flags
     */
    public function __construct($name, array $events = [], array $flags = [])
    {
        $this->assertName($name);

        $this->name = $name;
        $this->events = new GenericCollection($events, '\StateMachine\Event');
        $this->flags = new GenericCollection($flags, '\StateMachine\Flag');
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
            throw new InvalidArgumentException('Invalid state name, can not be empty string');
        }
    }

    /**
     * Return state name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return flag collection
     *
     * @return Flag[]
     */
    public function getFlags()
    {
        return $this->flags->all();
    }

    /**
     * Return true if flag with given name exist
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasFlag($name)
    {
        return $this->flags->has($name);
    }

    /**
     * Return flag with given name
     *
     * @param string $name
     *
     * @return Flag
     */
    public function getFlag($name)
    {
        return $this->flags->get($name);
    }

    /**
     * Return event collection
     *
     * @return Event[]
     */
    public function getEvents()
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
    public function hasEvent($name)
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
    public function getEvent($name)
    {
        return $this->events->get($name);
    }

    /**
     * Triggers event with given name and payload
     * Returns name of next state or null if no change
     *
     * @param string           $name
     * @param PayloadInterface $payload
     *
     * @return string
     */
    public function triggerEvent($name, PayloadInterface $payload)
    {
        return $this->getEvent($name)->trigger($payload);
    }

    /**
     * Return state string representation - its name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
