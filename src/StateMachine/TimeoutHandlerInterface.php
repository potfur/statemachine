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
 * Interface for handler that restores/stores timeouts
 *
 * @package StateMachine
 */
interface TimeoutHandlerInterface
{
    /**
     * Return list of timeouts that need to be run
     *
     * @return Timeout[]
     */
    public function getExpired();

    /**
     * Remove timeout
     *
     * @param Timeout $timeout
     */
    public function remove(Timeout $timeout);

    /**
     * Creates timeout
     *
     * @param Timeout $timeout
     */
    public function store(Timeout $timeout);
}
