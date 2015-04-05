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
 * Payload interface, used to wrap subject and gather information about process
 *
 * @package StateMachine
 */
interface PayloadInterface
{
    /**
     * Return true if state has changed (even if it was changed back to itself)
     *
     * @return bool
     */
    public function hasChanged();

    /**
     * Return current subject state
     *
     * @return string
     */
    public function getState();

    /**
     * Set new state to subject
     *
     * @param string $name state name
     */
    public function setState($name);

    /**
     * Return flag value or null if flag not set
     *
     * @param string $name requested flag
     *
     * @return mixed
     */
    public function getFlag($name);

    /**
     * Adds flag with value to payload
     *
     * @param Flag $flag
     */
    public function setFlag(Flag $flag);

    /**
     * Return array with all states that were set in this run
     *
     * @return array
     */
    public function getHistory();

    /**
     * Return subject instance
     *
     * @return mixed
     */
    public function getSubject();

    /**
     * Set subject instance
     *
     * @param mixed $subject
     */
    public function setSubject($subject);
}
