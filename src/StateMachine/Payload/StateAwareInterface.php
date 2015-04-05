<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Payload;

/**
 * All subjects aware of its own state, must implement this subject
 * Only subjects with this interface will have updated state.
 * State is represented as string
 *
 * @package StateMachine
 */
interface StateAwareInterface
{
    /**
     * Set new state to subject
     *
     * @param string $name state name
     */
    public function setState($name);

    /**
     * Return current subject state
     *
     * @return string
     */
    public function getState();
}
