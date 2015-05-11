<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine;

/**
 * Interface for adapter converter for different timeout formats to \DateTime or \DateInterval
 *
 * @package StateMachine
 */
interface TimeoutConverterInterface
{
    /**
     * Convert different timeout formats to \DateTime or \DateInterval
     * Handles integers, strings and callable.
     *
     * \DateInterval:
     *  - integers are treated as seconds,
     *  - strings starting with P are treated as ISO 8601 durations
     *
     * \DateTime:
     *  - other strings will be treated as date time format
     *
     * @see http://en.wikipedia.org/wiki/ISO_8601#Durations
     * @see http://php.net/manual/en/datetime.formats.php
     * @return \DateTime|\DateInterval
     */
    public function convert($value);
}
