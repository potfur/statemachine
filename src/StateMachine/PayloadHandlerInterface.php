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
 * Interface for handler that restores/stores subject
 *
 * @package StateMachine
 */
interface PayloadHandlerInterface
{
    /**
     * Restore subject by identifier and return it wrapped in payload
     *
     * @param mixed $identifier
     *
     * @return PayloadInterface
     */
    public function restore($identifier);

    /**
     * Store subject from payload
     *
     * @param PayloadInterface $payload
     */
    public function store(PayloadInterface $payload);
}
