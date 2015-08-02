<?php

namespace StateMachine;

/**
 * State machine state interface
 *
 * @package StateMachine
 */
interface StateInterface
{
    /**
     * Return state name
     *
     * @return string
     */
    public function getName();

    /**
     * Return flag collection
     *
     * @return Flag[]
     */
    public function getFlags();

    /**
     * Return true if flag with given name exist
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasFlag($name);

    /**
     * Return flag with given name
     *
     * @param string $name
     *
     * @return Flag
     */
    public function getFlag($name);

    /**
     * Return event collection
     *
     * @return EventInterface[]
     */
    public function getEvents();

    /**
     * Return true if event exists in collection
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasEvent($name);

    /**
     * Return event with given name
     *
     * @param string $name
     *
     * @return EventInterface
     */
    public function getEvent($name);

    /**
     * Return attributes container
     *
     * @return AttributeCollectionInterface
     */
    public function getAttributes();

    /**
     * Triggers event with given name and payload
     * Returns name of next state or null if no change
     *
     * @param string           $name
     * @param PayloadInterface $payload
     *
     * @return string
     */
    public function triggerEvent($name, PayloadInterface $payload);

    /**
     * Return state string representation - its name
     *
     * @return string
     */
    public function __toString();
}
