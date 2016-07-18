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

namespace StateMachine\Exception;

/**
 * Exception thrown if requested or arguments is not State.
 *
 * @package StateMachine
 */
class InvalidStateException extends StateMachineException
{
    public static function missingInitialState($processName, $initialState): InvalidStateException
    {
        return new InvalidStateException(
            sprintf(
                'Initial state "%s" does not exist in process "%s"',
                $initialState,
                $processName
            )
        );
    }
}
