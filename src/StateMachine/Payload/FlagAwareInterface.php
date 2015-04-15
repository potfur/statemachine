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
 * All subjects aware of flags must implement this interface.
 * Only subject with such, will be receiving flags
 * Be notified that multiple flags can be set in one state
 * Flags are represented as key-value pairs
 *
 * @package StateMachine
 */
interface FlagAwareInterface
{
    /**
     * Adds flag with value to subject
     *
     * @param array
     */
    public function setFlags(array $flags);

    /**
     * Return flag value or null if flag not set
     *
     * @return array
     */
    public function getFlags();
}
