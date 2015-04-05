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
     * @expectedException \StateMachine\Exception\InvalidStateException
     * @expectedExceptionMessage Initial state "undefinedState" does not exist in process "processName"
     */
    public function testInitialStateDoesNotExists()
    {
        new Process('processName', '\stdClass', 'undefinedState', []);
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

    public function testStates()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);

        $this->assertEquals(['stateName' => $this->state], $process->getStates());
    }

    public function testHasState()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);

        $this->assertFalse($process->hasState('undefinedState'));
        $this->assertTrue($process->hasState('stateName'));
    }

    public function testGetState()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);

        $this->assertSame($this->state, $process->getState('stateName'));
    }

    /**
     * @expectedException \StateMachine\Exception\OutOfRangeException
     * @expectedExceptionMessage Element for offset "undefinedState" not found
     */
    public function testGetUndefinedState()
    {
        $this->state->expects($this->any())->method('__toString')->willReturn('stateName');

        $process = new Process('processName', '\stdClass', 'stateName', [$this->state]);
        $process->getState('undefinedState');
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
}
