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


use StateMachine\LockHandlerInterface;

class FakeLockHandler implements LockHandlerInterface
{
    private $locks = [];

    public function lock($identifier)
    {
        $this->locks[$identifier] = true;
    }

    public function release($identifier)
    {
        if (!isset($this->locks[$identifier])) {
            return;
        }

        $this->locks[$identifier] = false;
    }

    public function releaseTimedOut(\DateInterval $interval)
    {
        $this->locks = [];
    }

    public function isLocked($identifier)
    {
        return !empty($this->locks[$identifier]);
    }

    public function wasLocked($identifier)
    {
        return array_key_exists($identifier, $this->locks);
    }
}
