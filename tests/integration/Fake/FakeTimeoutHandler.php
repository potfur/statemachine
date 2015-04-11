<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Fake;


use StateMachine\Timeout;
use StateMachine\TimeoutHandlerInterface;

class FakeTimeoutHandler implements TimeoutHandlerInterface
{
    /** @var Timeout[] */
    private $timeouts = [];

    public function getExpired()
    {
        $now = time();
        $result = [];
        foreach ($this->timeouts as $timeout) {
            if ($timeout->getExecutionDate()->getTimestamp() < $now) {
                $result[] = $timeout;
            }
        }

        return $result;
    }

    public function remove(Timeout $timeout)
    {
        unset($this->timeouts[$timeout->getIdentifier()]);
    }

    public function store(Timeout $timeout)
    {
        $this->timeouts[$timeout->getIdentifier()] = $timeout;
    }
}
