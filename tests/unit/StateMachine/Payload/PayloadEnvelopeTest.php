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
}
