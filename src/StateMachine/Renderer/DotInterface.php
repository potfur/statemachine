<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Renderer;

/**
 * Interface implemented by all dot elements
 *
 * @package StateMachine
 */
interface DotInterface
{
    /**
     * Return dot element string representation
     *
     * @return string
     */
    public function __toString();
}
