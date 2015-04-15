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


use StateMachine\Payload\Payload;
use StateMachine\PayloadHandlerInterface;
use StateMachine\PayloadInterface;

class FakePayloadHandler implements PayloadHandlerInterface
{
    private $wasStored = false;
    private $payload;

    public function restore($identifier, $state = null)
    {
        $identifier = (string) $identifier;

        if ($this->payload === null) {
            $this->payload = new Payload($identifier, new FakeSubject($identifier, $state));
        }

        return $this->payload;
    }

    public function store(PayloadInterface $payload)
    {
        $this->wasStored = true;
    }

    public function wasStored()
    {
        return (bool) $this->wasStored;
    }
}
