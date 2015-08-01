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
 * Attribute container interface
 *
 * @package StateMachine
 */
interface AttributeCollectionInterface
{
    /**
     * Check if attribute exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists($name);

    /**
     * Get attribute
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name, $default = null);
}
