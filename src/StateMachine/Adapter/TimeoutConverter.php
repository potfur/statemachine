<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Adapter;

use StateMachine\TimeoutConverterInterface;

/**
 * Adapter converter to convert different timeout formats to \DateTime or \DateInterval
 *
 * @package StateMachine
 */
final class TimeoutConverter implements TimeoutConverterInterface
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
    public function convert($value)
    {
        if ($value instanceof \DateTime || $value instanceof \DateInterval) {
            return $value;
        } elseif (filter_var($value, FILTER_VALIDATE_INT)) {
            return $this->convertInteger($value);
        } elseif (is_callable($value)) {
            return $this->convertCallable($value);
        } elseif (strpos($value, 'P') === 0) {
            return $this->convertDuration($value);
        } else {
            return $this->convertDate($value);
        }
    }

    /**
     * Convert integer to \DateInterval
     *
     * @param int $int
     *
     * @return \DateInterval
     */
    private function convertInteger($int)
    {
        return $this->convertDuration(sprintf('PT%uS', $int));
    }

    /**
     * Convert duration to \DateInterval
     *
     * @param int $duration
     *
     * @return \DateInterval
     */
    private function convertDuration($duration)
    {
        return new \DateInterval($duration);
    }

    /**
     * Convert string to \DateTime
     *
     * @param string $date
     *
     * @return \DateTime
     */
    private function convertDate($date)
    {
        return new \DateTime($date);
    }

    /**
     * Call callable and convert value
     *
     * @param callable $callable
     *
     * @return \DateInterval|\DateTime
     */
    private function convertCallable(callable $callable)
    {
        return $this->convert(call_user_func($callable));
    }
}