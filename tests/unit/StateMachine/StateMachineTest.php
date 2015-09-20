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

class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    /**
     * @var ProcessInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $process;

    /**
     * @var PayloadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $payload;

    /**
     * @var PayloadHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $payloadHandler;

    /**
     * @var TimeoutHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $timeoutHandler;

    /**
     * @var LockHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $lockHandler;

    public function setUp()
    {
        $this->adapter = $this->getMock('\StateMachine\AdapterInterface');

        $this->process = $this->getMock('\StateMachine\ProcessInterface');

        $this->payload = $this->getMock('\StateMachine\PayloadInterface');
        $this->payloadHandler = $this->getMock('\StateMachine\PayloadHandlerInterface');

        $this->timeoutHandler = $this->getMock('\StateMachine\TimeoutHandlerInterface');
        $this->lockHandler = $this->getMock('\StateMachine\LockHandlerInterface');
    }

    public function testTriggerEventAndLockEntityAndRemoveAllRelatedTimeouts()
    {
        $this->payload->expects($this->any())->method('hasChanged')->willReturn(true);
        $this->payload->expects($this->any())->method('getIdentifier')->willReturn('identifier');
        $this->process->expects($this->any())->method('hasTimeout')->willReturn(false);
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);
        $this->payloadHandler->expects($this->any())->method('restore')->willReturn($this->payload);
        $this->timeoutHandler->expects($this->once())->method('remove')->with('identifier');

        $this->lockHandler->expects($this->once())->method('lock')->with('identifier');
        $this->lockHandler->expects($this->once())->method('release')->with('identifier');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->triggerEvent('event', 'identifier');
    }

    public function testTriggerEventWithoutTimeoutBecauseThereIsNoTimeout()
    {
        $this->payload->expects($this->any())->method('hasChanged')->willReturn(true);
        $this->payloadHandler->expects($this->any())->method('restore')->willReturn($this->payload);
        $this->process->expects($this->any())->method('hasTimeout')->willReturn(false);
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);

        $this->timeoutHandler->expects($this->never())->method('store');
        $this->process->expects($this->once())->method('triggerEvent')->with('event', $this->payload);

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->triggerEvent('event', 'identifier');
    }

    public function testTriggerEventWithoutTimeoutBecausePayloadDidNotChange()
    {
        $this->payload->expects($this->any())->method('hasChanged')->willReturn(false);
        $this->payloadHandler->expects($this->once())->method('restore')->willReturn($this->payload);
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);

        $this->timeoutHandler->expects($this->never())->method('store');
        $this->process->expects($this->once())->method('triggerEvent')->with('event', $this->payload);

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->triggerEvent('event', 'identifier');
    }

    public function testTriggerEventWithTimeout()
    {
        $timeout = $this->getMockBuilder('\StateMachine\PayloadTimeout')->disableOriginalConstructor()->getMock();

        $this->payload->expects($this->any())->method('hasChanged')->willReturn(true);
        $this->payloadHandler->expects($this->any())->method('restore')->willReturn($this->payload);
        $this->process->expects($this->any())->method('hasTimeout')->willReturn(true);
        $this->process->expects($this->any())->method('getTimeout')->willReturn($timeout);
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);

        $this->timeoutHandler->expects($this->once())->method('store')->with($timeout);
        $this->process->expects($this->once())->method('triggerEvent')->with('event', $this->payload);

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->triggerEvent('event', 'identifier');
    }

    public function testResolveTimeoutsAndLockWithPayloadInCorrectState()
    {
        $timeout = $this->getMockBuilder('\StateMachine\PayloadTimeout')->disableOriginalConstructor()->getMock();
        $timeout->expects($this->any())->method('getState')->willReturn('timeout');
        $timeout->expects($this->any())->method('getIdentifier')->willReturn('identifier');

        $this->timeoutHandler->expects($this->any())->method('getExpired')->willReturn([$timeout]);
        $this->payload->expects($this->any())->method('getState')->willReturn('timeout');
        $this->payloadHandler->expects($this->any())->method('restore')->willReturn($this->payload);
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);

        $this->lockHandler->expects($this->once())->method('lock')->with('identifier');
        $this->lockHandler->expects($this->once())->method('release')->with('identifier');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->resolveTimeouts();
    }

    public function testResolveTimeoutsAndLockWithPayloadInCorrectStateButLocked()
    {
        $timeout = $this->getMockBuilder('\StateMachine\PayloadTimeout')->disableOriginalConstructor()->getMock();
        $timeout->expects($this->any())->method('getState')->willReturn('timeout');
        $timeout->expects($this->any())->method('getIdentifier')->willReturn('identifier');

        $this->timeoutHandler->expects($this->any())->method('getExpired')->willReturn([$timeout]);
        $this->lockHandler->expects($this->any())->method('isLocked')->willReturn(true);
        $this->payload->expects($this->any())->method('getState')->willReturn('timeout');
        $this->payloadHandler->expects($this->any())->method('restore')->willReturn($this->payload);
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);

        $this->lockHandler->expects($this->never())->method('lock')->with('identifier');
        $this->lockHandler->expects($this->never())->method('release')->with('identifier');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->resolveTimeouts();
    }

    public function testResolveTimeoutsWithoutAny()
    {
        $this->timeoutHandler->expects($this->any())->method('getExpired')->willReturn([]);

        $this->timeoutHandler->expects($this->never())->method('remove');
        $this->payloadHandler->expects($this->never())->method('restore');
        $this->process->expects($this->never())->method('triggerEvent');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->resolveTimeouts();
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidStateException
     * @expectedExceptionMessage Payload is in different state, expected "timeout" but is "differentState"
     */
    public function testResolveTimeoutsWithPayloadInInvalidState()
    {
        $timeout = $this->getMockBuilder('\StateMachine\PayloadTimeout')->disableOriginalConstructor()->getMock();
        $timeout->expects($this->any())->method('getState')->willReturn('timeout');

        $this->timeoutHandler->expects($this->any())->method('getExpired')->willReturn([$timeout]);
        $this->payload->expects($this->any())->method('getState')->willReturn('differentState');
        $this->payloadHandler->expects($this->any())->method('restore')->willReturn($this->payload);
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->resolveTimeouts();
    }

    public function testResolveTimeoutsWithPayloadInCorrectState()
    {
        $timeout = $this->getMockBuilder('\StateMachine\PayloadTimeout')->disableOriginalConstructor()->getMock();
        $timeout->expects($this->any())->method('getState')->willReturn('timeout');

        $this->timeoutHandler->expects($this->any())->method('getExpired')->willReturn([$timeout]);
        $this->payload->expects($this->any())->method('getState')->willReturn('timeout');
        $this->payloadHandler->expects($this->any())->method('restore')->willReturn($this->payload);
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);

        $this->process->expects($this->once())->method('triggerEvent');
        $this->timeoutHandler->expects($this->once())->method('remove');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        $machine->resolveTimeouts();
    }
}
