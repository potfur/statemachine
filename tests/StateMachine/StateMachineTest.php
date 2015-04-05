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

    public function setUp()
    {
        $this->adapter = $this->getMock('\StateMachine\AdapterInterface');
        $this->process = $this->getMock('\StateMachine\ProcessInterface');
        $this->payload = $this->getMock('\StateMachine\PayloadInterface');
        $this->payloadHandler = $this->getMock('\StateMachine\PayloadHandlerInterface');
    }

    public function testTriggerEvent()
    {
        $this->process->expects($this->once())->method('triggerEvent')->with('event', $this->payload);
        $this->adapter->expects($this->once())->method('getProcess')->willReturn($this->process);

        $this->payloadHandler->expects($this->once())->method('restore')->willReturn($this->payload);
        $this->payloadHandler->expects($this->once())->method('store')->with($this->payload);

        $machine = new StateMachine($this->adapter, $this->payloadHandler);
        $machine->triggerEvent('event', 'identifier');
    }
}
