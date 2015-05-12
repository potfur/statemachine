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

use StateMachine\Exception\InvalidArgumentException;

/**
 * TimeOut value object
 *
 * @package StateMachine
 */
class Timeout
{
    /**
     * Timeout value
     *
     * @var \DateTime|\DateInterval
     */
    private $timeout;

    /**
     * Constructor
     *
     * @param mixed  $timeout timeout value
     *
     * @throws InvalidArgumentException
     */
    public function __construct($timeout)
    {
        $this->timeout = $this->convert($timeout);
    }


    /**
     * Convert different timeout formats to \DateTime or \DateInterval
     * Handles integers, strings and callable.
     * \DateInterval:
     *  - integers are treated as seconds,
     *  - strings starting with P are treated as ISO 8601 durations
     * \DateTime:
     *  - other strings will be treated as date time format
     *
     * @see http://en.wikipedia.org/wiki/ISO_8601#Durations
     * @see http://php.net/manual/en/datetime.formats.php
     *
     * @param mixed $value
     *
     * @return \DateTime|\DateInterval
     */
    private function convert($value)
    {
        if ($value instanceof \DateTime || $value instanceof \DateInterval) {
            return $value;
        }

        if (filter_var($value, FILTER_VALIDATE_INT)) {
            return new \DateInterval(sprintf('PT%uS', $value));
        }

        if (strpos($value, 'P') === 0) {
            return new \DateInterval($value);
        }

        return new \DateTime($value);
    }

    /**
     * Return timeout value
     *
     * @return \DateInterval|\DateTime
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Return when event timeout
     *
     * @param \DateTime $now date will be used as reference for timeouts defined as intervals
     *
     * @return \DateTime
     */
    public function timeoutAt(\DateTime $now)
    {
        if ($this->timeout instanceof \DateInterval) {
            return $now->add($this->timeout);
        }

        return $this->timeout;
    }

    /**
     * Build timeout string representation
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->timeout instanceof \DateInterval) {
            return $this->intervalToString($this->timeout);
        }

        return $this->dateToString($this->timeout);
    }

    /**
     * Convert DateInterval to interval string
     *
     * @see http://en.wikipedia.org/wiki/ISO_8601#Durations
     *
     * @param \DateInterval $interval
     *
     * @return string
     */
    private function intervalToString(\DateInterval $interval)
    {
        $date = [
            'Y' => $interval->y,
            'M' => $interval->m,
            'D' => $interval->d
        ];

        $time = [
            'H' => $interval->h,
            'M' => $interval->i,
            'S' => $interval->s
        ];

        $str = 'P' . $this->implodeDuration($date);

        if (array_sum($time)) {
            $str .= 'T' . $this->implodeDuration($time);
        }

        return $str;
    }

    /**
     * Implodes duration array into string
     *
     * @param array $duration
     *
     * @return string
     */
    private function implodeDuration(array $duration)
    {
        $str = '';
        $duration = array_filter($duration);
        foreach ($duration as $key => $value) {
            $str .= $value . $key;
        }

        return $str;
    }

    /**
     * Convert DateTime to string
     *
     * @see http://en.wikipedia.org/wiki/ISO_8601#Dates
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    private function dateToString(\DateTime $dateTime)
    {
        return $dateTime->format('c');
    }
}
