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

namespace StateMachine\Payload;


interface Stateful
{
    /**
     * Return payload state
     *
     * @return string
     */
    public function state(): string;

    /**
     * Change payload state
     *
     * @param string $name
     */
    public function changeState($name);
}
