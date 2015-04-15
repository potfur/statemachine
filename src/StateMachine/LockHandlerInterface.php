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
 * Interface for handler locking context for concurrent runs
 *
 * @package StateMachine
 */
interface LockHandlerInterface
{
    /**
     * Add lock for set identifier
     *
     * @param string $identifier
     */
    public function lock($identifier);

    /**
     * Release lock for identifier
     *
     * @param string $identifier
     */
    public function release($identifier);

    /**
     * Check if identifier is locked
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function isLocked($identifier);

    /**
     * Release locks older than now minus passed interval
     *
     * @param \DateInterval $interval
     */
    public function releaseTimedOut(\DateInterval $interval);
}
