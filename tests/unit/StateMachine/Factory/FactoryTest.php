<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Factory;


use StateMachine\StateMachine;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StateMachine|\PHPUnit_Framework_MockObject_MockObject
     */
    private $machine;

    public function setUp()
    {
        $this->machine = $this->getMockBuilder('\StateMachine\StateMachine')->disableOriginalConstructor()->getMock();
    }

    public function testRegister()
    {
        $facade = new Factory();

        $this->assertFalse($facade->has('foo'));

        $facade->register('foo', function () { });

        $this->assertTrue($facade->has('foo'));
    }

    public function testGet()
    {
        $facade = new Factory();
        $facade->register('foo', function () { return $this->machine; });

        $this->assertInstanceOf('\StateMachine\StateMachine', $facade->get('foo'));
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage State machine with name "foo" was not defined
     */
    public function testGetUnregistered()
    {
        $facade = new Factory();
        $facade->get('foo');
    }

    public function testTriggerEvent()
    {
        $this->machine->expects($this->once())->method('triggerEvent')->with('bar', 1);
        $facade = new Factory();
        $facade->register('foo', function () { return $this->machine; });
        $facade->triggerEvent('foo', 'bar', 1);
    }

    public function testResolveTimeoutsForAllMachines()
    {
        $this->machine->expects($this->exactly(2))->method('resolveTimeouts')->with();

        $facade = new Factory();
        $facade->register('foo', function () { return $this->machine; });
        $facade->register('bar', function () { return $this->machine; });

        $facade->resolveTimeouts();
    }

    public function testResolveTimeoutsForSetMachines()
    {
        $this->machine->expects($this->once())->method('resolveTimeouts')->with();

        $facade = new Factory();
        $facade->register('foo', function () { return $this->machine; });
        $facade->register('bar', function () { return $this->machine; });

        $facade->resolveTimeouts(['foo']);
    }
}
