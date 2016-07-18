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
 * Exception thrown if an argument is not as expected
 *
 * @package StateMachine
 */
class InvalidArgumentException extends StateMachineException
{
    public static function emptyProcessName(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid process name, can not be empty string');
    }

    public static function emptyStateName(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid state name, can not be empty string');
    }

    public static function emptyEventName(): InvalidArgumentException
    {
        return new InvalidArgumentException('Invalid event name, can not be empty string');
    }
}
