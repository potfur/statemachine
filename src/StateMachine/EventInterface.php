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
 * Interface for events
 *
 * @package StateMachine
 */
interface EventInterface
{
    /**
     * Return event name
     *
     * @return string
     */
    public function getName();

    /**
     * Return name of success state
     * Subject will go into this state when all commands execute properly
     *
     * @return string
     */
    public function getTargetState();

    /**
     * Return error state name
     * State where subject will go when something goes wrong
     *
     * @return string
     */
    public function getErrorState();

    /**
     * Return comment
     *
     * @return string
     */
    public function getComment();

    /**
     * Return true if event has timeout
     *
     * @return bool
     */
    public function hasTimeout();

    /**
     * Return timeout value
     *
     * @return null|\DateTime|\DateInterval
     */
    public function getTimeout();

    /**
     * Return date when event timeout
     *
     * @param \DateTime $now date will be used as reference for timeouts defined as intervals
     *
     * @return \DateTime
     */
    public function timeoutAt(\DateTime $now);

    /**
     * Triggers event and return next state name or null if there is no state change
     *
     * @param PayloadInterface $payload
     *
     * @return string
     */
    public function trigger(PayloadInterface $payload);

    /**
     * Return event string representation - its name
     *
     * @return string
     */
    public function __toString();
}
