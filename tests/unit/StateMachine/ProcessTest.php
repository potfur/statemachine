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

class ProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $state;

    /**
     * @var PayloadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $payload;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $subject;

    public function setUp()
    {
        $this->state = $this->getMockBuilder('\StateMachine\StateInterface')->disableOriginalConstructor()->getMock();
        $this->payload = $this->getMock('\StateMachine\PayloadInterface');
        $this->subject = $this->getMock('\stdClass');
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid process name, can not be empty string
     */
    public function testNameIsNull()
    {
        new Process('', '\stdClass', 'stateName', [$this->state]);
    }

    public function testName()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);

        $this->assertEquals('processName', $process->getName());
    }

    public function testSubjectClass()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);

        $this->assertEquals('\stdClass', $process->getSubjectClass());
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidStateException
     * @expectedExceptionMessage Initial state "undefinedState" does not exist in process "processName"
     */
    public function testInitialStateDoesNotExists()
    {
        new Process('processName', '\stdClass', 'undefinedState', []);
    }

    public function testInitialState()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('initialState');

        $process = new Process('processName', '\stdClass', 'initialState', [$this->state]);

        $this->assertEquals('initialState', $process->getInitialStateName());
    }

    public function testStates()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('initialState');

        $process = new Process('processName', '\stdClass', 'initialState', [$this->state]);

        $this->assertEquals(['initialState' => $this->state], $process->getStates());
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidSubjectException
     * @expectedExceptionMessage Unable to trigger with invalid payload in process "processName" - got "string", expected "\stdClass"
     */
    public function testTriggerEventWithInvalidPayload()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');

        $this->payload->expects($this->any())->method('getSubject')->willReturn('string');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);
        $process->triggerEvent('eventName', $this->payload);
    }

    public function testTriggerEventWithoutInitialState()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');
        $this->state->expects($this->any())->method('triggerEvent')->willReturn(null);

        $this->payload->expects($this->any())->method('getSubject')->willReturn($this->subject);
        $this->payload->expects($this->any())->method('getState')->willReturnOnConsecutiveCalls(null, 'stateName');
        $this->payload->expects($this->once())->method('setState')->with('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);
        $process->triggerEvent('eventName', $this->payload);
    }

    public function testTriggerEventWithTargetState()
    {
        $flag = $this->getMockBuilder('\StateMachine\Flag')->disableOriginalConstructor()->getMock();

        $state = clone $this->state;
        $state->expects($this->any())->method('__toString')->willReturn('targetState');
        $state->expects($this->any())->method('getName')->willReturn('targetState');
        $state->expects($this->any())->method('getFlags')->willReturn([$flag]);

        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');
        $this->state->expects($this->any())->method('triggerEvent')->willReturn('targetState');

        $this->payload->expects($this->any())->method('getSubject')->willReturn($this->subject);
        $this->payload->expects($this->any())->method('getState')->willReturn('stateName');
        $this->payload->expects($this->once())->method('setState')->with('targetState');
        $this->payload->expects($this->once())->method('setFlag')->with($flag);

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state, $state]);
        $process->triggerEvent('eventName', $this->payload);
    }

    public function testTriggerEventWithBlankState()
    {
        $state = clone $this->state;
        $state->expects($this->any())->method('__toString')->willReturn('targetState');

        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');
        $this->state->expects($this->any())->method('triggerEvent')->willReturn(null);

        $this->payload->expects($this->any())->method('getSubject')->willReturn($this->subject);
        $this->payload->expects($this->any())->method('getState')->willReturn('stateName');
        $this->payload->expects($this->never())->method('setState');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state, $state]);
        $process->triggerEvent('eventName', $this->payload);
    }

    public function testTriggerEventWithOnStateWasSet()
    {
        $finalState = clone $this->state;
        $finalState->expects($this->any())->method('__toString')->willReturn('finalState');
        $finalState->expects($this->any())->method('getName')->willReturn('finalState');
        $finalState->expects($this->any())->method('hasEvent')->with(Process::ON_STATE_WAS_SET)->willReturn(false);

        $transitionState = clone $this->state;
        $transitionState->expects($this->any())->method('__toString')->willReturn('transitionState');
        $transitionState->expects($this->any())->method('getName')->willReturn('transitionState');
        $transitionState->expects($this->any())->method('hasEvent')->with(Process::ON_STATE_WAS_SET)->willReturn(true);
        $transitionState->expects($this->any())->method('triggerEvent')->willReturn('finalState');

        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');
        $this->state->expects($this->any())->method('getName')->willReturn('stateName');
        $this->state->expects($this->any())->method('triggerEvent')->willReturn('transitionState');

        $this->payload->expects($this->any())->method('getState')->willReturn('stateName');
        $this->payload->expects($this->any())->method('getSubject')->willReturn($this->subject);
        $this->payload->expects($this->exactly(2))->method('setState')->withConsecutive(
            ['transitionState'],
            ['finalState']
        );

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state, $transitionState, $finalState]);
        $process->triggerEvent('eventName', $this->payload);
    }

    public function testTriggerEventWithOnStateWasSetAndEndedWithNull()
    {
        $finalState = clone $this->state;
        $finalState->expects($this->any())->method('__toString')->willReturn('finalState');
        $finalState->expects($this->any())->method('getName')->willReturn('finalState');
        $finalState->expects($this->any())->method('hasEvent')->with(Process::ON_STATE_WAS_SET)->willReturn(false);

        $transitionState = clone $this->state;
        $transitionState->expects($this->any())->method('__toString')->willReturn('transitionState');
        $transitionState->expects($this->any())->method('getName')->willReturn('transitionState');
        $transitionState->expects($this->any())->method('hasEvent')->with(Process::ON_STATE_WAS_SET)->willReturn(true);
        $transitionState->expects($this->any())->method('triggerEvent')->willReturn(null);

        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');
        $this->state->expects($this->any())->method('getName')->willReturn('stateName');
        $this->state->expects($this->any())->method('triggerEvent')->willReturn('transitionState');

        $this->payload->expects($this->any())->method('getState')->willReturn('stateName');
        $this->payload->expects($this->any())->method('getSubject')->willReturn($this->subject);
        $this->payload->expects($this->once())->method('setState')->with('transitionState');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state, $transitionState, $finalState]);
        $process->triggerEvent('eventName', $this->payload);
    }

    public function testHasTimeout()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');
        $this->state->expects($this->any())->method('getName')->willReturn('stateName');
        $this->state->expects($this->any())->method('hasEvent')->willReturn(true);

        $this->payload->expects($this->any())->method('getState')->willReturn('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);
        $this->assertTrue($process->hasTimeout($this->payload));
    }

    public function testDoesNotHaveTimeout()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');
        $this->state->expects($this->any())->method('getName')->willReturn('stateName');
        $this->state->expects($this->any())->method('hasEvent')->willReturn(false);

        $this->payload->expects($this->any())->method('getState')->willReturn('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);
        $this->assertFalse($process->hasTimeout($this->payload));
    }

    public function testGetTimeout()
    {
        $date = new \DateTime();

        $event = $this->getMockBuilder('\StateMachine\EventInterface')->disableOriginalConstructor()->getMock();
        $event->expects($this->any())->method('getName')->willReturn(Process::ON_TIME_OUT);
        $event->expects($this->any())->method('timeoutAt')->willReturn($date);

        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');
        $this->state->expects($this->any())->method('getName')->willReturn('stateName');
        $this->state->expects($this->any())->method('getEvent')->willReturn($event);

        $this->payload->expects($this->any())->method('getState')->willReturn('stateName');
        $this->payload->expects($this->any())->method('getIdentifier')->willReturn('identifier');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);
        $timeout = $process->getTimeout($this->payload, $date);

        $this->assertInstanceOf('\StateMachine\PayloadTimeout', $timeout);

        $this->assertEquals('stateName', $timeout->getState());
        $this->assertEquals(Process::ON_TIME_OUT, $timeout->getEvent());
        $this->assertEquals('identifier', $timeout->getIdentifier());
        $this->assertEquals($date, $timeout->getExecutionDate());
    }
}
