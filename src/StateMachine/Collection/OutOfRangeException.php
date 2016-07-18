<?php

declare(strict_types = 1);

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Collection;

use StateMachine\Exception\StateMachineException;

/**
 * Exception thrown when an illegal index was requested
 *
 * @package StateMachine
 */
class OutOfRangeException extends StateMachineException
{
    public static function offsetNotFound($offset): OutOfRangeException
    {
        return new OutOfRangeException(sprintf('Element for offset "%s" not found', $offset));
    }
}
