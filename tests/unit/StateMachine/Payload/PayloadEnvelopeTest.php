<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace unit\StateMachine\Payload;


use StateMachine\Payload\PayloadEnvelope;
use StateMachine\Payload\Stateful;

class PayloadEnvelopeTest extends \PHPUnit_Framework_TestCase
{
    public function testHasChangedBecauseOfStatusChange()
    {
        $envelope = PayloadEnvelope::wrap('stuff');
        $this->assertFalse($envelope->hasChanged());

        $envelope->changeState('foo');
        $this->assertTrue($envelope->hasChanged());
    }

    public function testState()
    {
        $envelope = PayloadEnvelope::wrap('stuff');
        $envelope->changeState('stateName');

        $this->assertEquals('stateName', $envelope->state());
    }

    public function testGetHistory()
    {
        $envelope = PayloadEnvelope::wrap('stuff');
        $envelope->changeState('firstState');
        $envelope->changeState('secondState');
        $envelope->changeState('thirdState');

        $this->assertEquals(['firstState', 'secondState', 'thirdState'], $envelope->history());
    }

    public function testSubject()
    {
        $envelope = PayloadEnvelope::wrap('stuff');
        $this->assertSame('stuff', $envelope->subject());
    }

    public function testConstructWithStateFromStatefulPayload()
    {
        $payload = $this->createMock(Stateful::class);
        $payload->expects($this->any())->method('state')->willReturn('currentState');

        $envelope = PayloadEnvelope::wrap($payload);
        $this->assertEquals('currentState', $envelope->state());
    }

    public function testSetStateWithStatefulPayload()
    {
        $payload = $this->createMock(Stateful::class);
        $payload->expects($this->once())->method('changeState')->with('newState');

        $envelope = PayloadEnvelope::wrap($payload);
        $envelope->changeState('newState');
    }
}
