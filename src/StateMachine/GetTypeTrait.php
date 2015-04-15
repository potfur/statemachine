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
 * Adds method for retrieving value type (internal or class)
 *
 * @package StateMachine
 */
trait GetTypeTrait
{
    /**
     * Return value type
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function getType($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }
}
