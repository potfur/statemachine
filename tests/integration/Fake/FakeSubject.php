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


use StateMachine\Payload\FlagAwareInterface;
use StateMachine\Payload\StateAwareInterface;

class FakeSubject extends \stdClass implements StateAwareInterface, FlagAwareInterface
{
    private $identifier;
    private $state;
    private $flags = [];

    public function __construct($identifier, $state = null)
    {
        $this->identifier = $identifier;
        $this->state = $state;
    }

    public function setState($name)
    {
        $this->state = $name;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setFlags(array $flags)
    {
        $this->flags = array_merge($this->flags, $flags);
    }

    public function getFlags()
    {
        return $this->flags;
    }
}
