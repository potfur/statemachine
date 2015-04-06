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

    public function setUp()
    {
        $this->adapter = $this->getMock('\StateMachine\AdapterInterface');

        $this->process = $this->getMock('\StateMachine\ProcessInterface');

        $this->payload = $this->getMock('\StateMachine\PayloadInterface');
        $this->payloadHandler = $this->getMock('\StateMachine\PayloadHandlerInterface');

        $this->timeoutHandler = $this->getMock('\StateMachine\TimeoutHandlerInterface');
    }

    public function testTriggerEventWithoutTimeoutBecauseThereIsNoTimeout()
    {
        $this->payload->expects($this->once())->method('hasChanged')->willReturn(true);

        $this->process->expects($this->once())->method('triggerEvent')->with('event', $this->payload);
        $this->process->expects($this->once())->method('hasTimeout')->willReturn(false);

        $this->adapter->expects($this->once())->method('getProcess')->willReturn($this->process);

        $this->payloadHandler->expects($this->once())->method('restore')->willReturn($this->payload);
        $this->payloadHandler->expects($this->once())->method('store')->with($this->payload);

        $this->timeoutHandler->expects($this->never())->method('store');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler);
        $machine->triggerEvent('event', 'identifier');
    }

    public function testTriggerEventWithoutTimeoutBecausePayloadDidNotChange()
    {
        $this->payload->expects($this->once())->method('hasChanged')->willReturn(false);

        $this->process->expects($this->once())->method('triggerEvent')->with('event', $this->payload);

        $this->adapter->expects($this->once())->method('getProcess')->willReturn($this->process);

        $this->payloadHandler->expects($this->once())->method('restore')->willReturn($this->payload);
        $this->payloadHandler->expects($this->once())->method('store')->with($this->payload);

        $this->timeoutHandler->expects($this->never())->method('store');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler);
        $machine->triggerEvent('event', 'identifier');
    }

    public function testTriggerEventWithTimeout()
    {
        $this->payload->expects($this->once())->method('hasChanged')->willReturn(true);

        $timeout = $this->getMockBuilder('\StateMachine\Timeout')->disableOriginalConstructor()->getMock();

        $this->process->expects($this->once())->method('triggerEvent')->with('event', $this->payload);
        $this->process->expects($this->once())->method('hasTimeout')->willReturn(true);
        $this->process->expects($this->once())->method('getTimeout')->willReturn($timeout);

        $this->adapter->expects($this->once())->method('getProcess')->willReturn($this->process);

        $this->payloadHandler->expects($this->once())->method('restore')->willReturn($this->payload);
        $this->payloadHandler->expects($this->once())->method('store')->with($this->payload);

        $this->timeoutHandler->expects($this->once())->method('store')->with($timeout);

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler);
        $machine->triggerEvent('event', 'identifier');
    }

    public function testResolveTimeoutsWithoutAny()
    {
        $this->timeoutHandler->expects($this->once())->method('getExpired')->willReturn([]);
        $this->timeoutHandler->expects($this->never())->method('remove');

        $this->payloadHandler->expects($this->never())->method('restore');

        $this->process->expects($this->never())->method('triggerEvent');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler);
        $machine->resolveTimeouts();
    }

    public function testResolveTimeoutsWithPayloadInInvalidState()
    {
        $timeout = $this->getMockBuilder('\StateMachine\Timeout')->disableOriginalConstructor()->getMock();
        $timeout->expects($this->once())->method('getState')->willReturn('timeout');

        $this->timeoutHandler->expects($this->once())->method('getExpired')->willReturn([$timeout]);
        $this->timeoutHandler->expects($this->once())->method('remove');

        $this->payload->expects($this->once())->method('getState')->willReturn('differentState');

        $this->payloadHandler->expects($this->once())->method('restore')->willReturn($this->payload);

        $this->process->expects($this->never())->method('triggerEvent');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler);
        $machine->resolveTimeouts();
    }

    public function testResolveTimeoutsWithPayloadInCorrectState()
    {
        $timeout = $this->getMockBuilder('\StateMachine\Timeout')->disableOriginalConstructor()->getMock();
        $timeout->expects($this->once())->method('getState')->willReturn('timeout');

        $this->timeoutHandler->expects($this->once())->method('getExpired')->willReturn([$timeout]);
        $this->timeoutHandler->expects($this->once())->method('remove');

        $this->payload->expects($this->once())->method('getState')->willReturn('timeout');

        $this->payloadHandler->expects($this->once())->method('restore')->willReturn($this->payload);

        $this->adapter->expects($this->once())->method('getProcess')->willReturn($this->process);

        $this->process->expects($this->once())->method('triggerEvent');

        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler);
        $machine->resolveTimeouts();
    }
}
