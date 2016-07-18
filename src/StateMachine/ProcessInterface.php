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

use StateMachine\Payload;

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
    public function name(): string;

    /**
     * Return initial state
     *
     * @return State
     */
    public function initialState(): State;

    /**
     * Return state by name
     *
     * @param $name
     *
     * @return State
     */
    public function state($name): State;

    /**
     * Return all states
     *
     * @return State[]
     */
    public function states(): array;

    /**
     * Trigger event for payload
     * Return next state name
     *
     * @param string  $event
     * @param Payload $payload
     *
     * @return string
     */
    public function triggerEvent($event, Payload $payload): string;
}
