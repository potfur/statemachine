<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace unit\StateMachine;

use StateMachine\Event;
use StateMachine\Payload\PayloadEnvelope;
use StateMachine\Process;
use StateMachine\State;
use StateMachine\StateMachine;

class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    public function testTriggerEvent()
    {
        $payload = PayloadEnvelope::wrap('subject');
        $process = new Process(
            'processName',
            'initialState',
            [
                new State('initialState', [new Event('eventName', 'targetState')]),
                new State('targetState')
            ]
        );
        $stateMachine = new StateMachine($process);
        $stateMachine->triggerEvent('eventName', $payload);

        $this->assertEquals(['initialState', 'targetState'], $payload->history());
    }

    public function testTriggerEventEndedWithoutNextState()
    {
        $payload = PayloadEnvelope::wrap('subject');
        $process = new Process(
            'processName',
            'initialState',
            [
                new State('initialState', [new Event('eventName')]),
            ]
        );
        $stateMachine = new StateMachine($process);
        $stateMachine->triggerEvent('eventName', $payload);

        $this->assertEquals(['initialState'], $payload->history());
    }

    public function testTriggerEventWithOnStateWasSet()
    {
        $payload = PayloadEnvelope::wrap('subject');
        $process = new Process(
            'processName',
            'initialState',
            [
                new State('initialState', [new Event('eventName', 'transitionalState')]),
                new State('transitionalState', [new Event(StateMachine::ON_STATE_WAS_SET, 'finalState')]),
                new State('finalState')
            ]
        );
        $stateMachine = new StateMachine($process);
        $stateMachine->triggerEvent('eventName', $payload);

        $this->assertEquals(['initialState', 'transitionalState', 'finalState'], $payload->history());
    }

    public function testTriggerEventWithOnStateWasSetAndEndedWithoutNextState()
    {
        $payload = PayloadEnvelope::wrap('subject');
        $process = new Process(
            'processName',
            'initialState',
            [
                new State('initialState', [new Event('eventName', 'transitionalState')]),
                new State('transitionalState', [new Event(StateMachine::ON_STATE_WAS_SET)]),
            ]
        );
        $stateMachine = new StateMachine($process);
        $stateMachine->triggerEvent('eventName', $payload);

        $this->assertEquals(['initialState', 'transitionalState'], $payload->history());
    }
}
