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
     * @return State[]
     */
    public function getStates();

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

    /**
     * Return true if payloads state has timeout event
     *
     * @param PayloadInterface $payload
     *
     * @return bool
     */
    public function hasTimeout(PayloadInterface $payload);

    /**
     * Return timeout object for payloads state timeout event
     *
     * @param PayloadInterface $payload
     * @param \DateTime        $now date will be used as reference for timeouts defined as intervals
     *
     * @return PayloadTimeout
     */
    public function getTimeout(PayloadInterface $payload, \DateTime $now);
}
