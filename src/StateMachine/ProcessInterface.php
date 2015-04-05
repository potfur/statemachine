<?php

namespace StateMachine;

/**
 * State machine process interface
 *
 * @package StateMachine
 */
interface ProcessInterface
{
    /**
     * Return process name
     *
     * @return string
     */
    public function getName();

    /**
     * Return entity class
     * Fully qualified subject class for which process is defined
     *
     * @return string
     */
    public function getSubjectClass();

    /**
     * Return initial state
     * All entities without state will have this one
     *
     * @return string
     */
    public function getInitialStateName();

    /**
     * Return all states
     *
     * @return StateInterface[]
     */
    public function getStates();

    /**
     * Return true if event exists in collection
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasState($name);

    /**
     * Return event with given name
     *
     * @param string $name
     *
     * @return StateInterface
     */
    public function getState($name);

    /**
     * Trigger event for payload
     * Return array with all transitional state names
     *
     * @param string           $event
     * @param PayloadInterface $payload
     *
     * @return array
     */
    public function triggerEvent($event, PayloadInterface $payload);
}
