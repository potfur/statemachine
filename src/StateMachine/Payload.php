<?php

declare(strict_types = 1);

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
 * Payload envelope, used to transport subject trough state machine process
 *
 * @package StateMachine
 */
interface Payload
{
    /**
     * Return true if state has changed (even if it was changed back to itself)
     *
     * @return bool
     */
    public function hasChanged(): bool;

    /**
     * Return current subject state
     *
     * @return null|string
     */
    public function state();

    /**
     * Set new state to subject
     *
     * @param string $name state name
     */
    public function changeState($name);

    /**
     * Return array with all states that were set in this run
     *
     * @return array
     */
    public function history(): array;

    /**
     * Return subject
     *
     * @return mixed
     */
    public function subject();
}
