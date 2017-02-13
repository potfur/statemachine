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
use StateMachine\Exception\InvalidArgumentException;
use StateMachine\Exception\InvalidStateException;
use StateMachine\PayloadEnvelope;
use StateMachine\Process;
use StateMachine\State;

class ProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $subject;

    public function setUp()
    {
        $this->state = new State('stateName');
        $this->subject = $this->createMock(\stdClass::class);
    }

    public function testNameIsNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid process name, can not be empty string');

        new Process('', 'stateName', []);
    }

    public function testName()
    {
        $process = new Process('processName', 'stateName', [$this->state]);

        $this->assertEquals('processName', $process->name());
    }

    public function testInitialStateDoesNotExists()
    {
        $this->expectException(InvalidStateException::class);
        $this->expectExceptionMessage('Initial state "undefinedState" does not exist in process "processName"');

        new Process('processName', 'undefinedState', [$this->state]);
    }

    public function testInitialState()
    {
        $process = new Process('processName', 'stateName', [$this->state]);

        $this->assertEquals('stateName', $process->initialState()->name());
    }

    public function testStates()
    {
        $process = new Process('processName', 'stateName', [$this->state]);

        $this->assertEquals(['stateName' => $this->state], $process->states());
    }

    public function testTriggerEvent()
    {
        $payload = PayloadEnvelope::wrap('stuff');
        $payload->changeState('initialState');

        $initialState = new State('initialState', [new Event('eventName', 'finalState')]);
        $finalState = new State('finalState');

        $process = new Process('processName', 'initialState', [$initialState, $finalState]);

        $this->assertEquals('finalState', $process->triggerEvent('eventName', $payload));
    }
}
