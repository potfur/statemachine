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

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $event;

    /**
     * @var Flag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $flag;

    public function setUp()
    {
        $this->event = $this->getMockBuilder('\StateMachine\EventInterface')->disableOriginalConstructor()->getMock();
        $this->event->expects($this->any())->method('__toString')->willReturn('eventName');

        $this->flag = $this->getMockBuilder('\StateMachine\Flag')->disableOriginalConstructor()->getMock();
        $this->flag->expects($this->any())->method('__toString')->willReturn('flagName');
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Element in collection must be instance of "\StateMachine\EventInterface", got "string"
     */
    public function testProvidedInvalidEvent()
    {
        new State('stateName', ['yadayada'], []);
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Element in collection must be instance of "\StateMachine\Flag", got "string"
     */
    public function testProvidedInvalidFlag()
    {
        new State('stateName', [], ['yadayada']);
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid state name, can not be empty string
     */
    public function testNameIsNull()
    {
        new State('', [], []);
    }

    public function testGetName()
    {
        $state = new State('stateName', [], []);
        $this->assertEquals('stateName', $state->getName());
    }

    public function testGetEvents()
    {
        $state = new State('stateName', [$this->event], [$this->flag]);
        $this->assertEquals(['eventName' => $this->event], $state->getEvents());
    }

    /**
     * @dataProvider hasEventProvider
     */
    public function testHasEvent($event, $expected)
    {
        $state = new State('stateName', [$this->event], [$this->flag]);
        $this->assertEquals($expected, $state->hasEvent($event));
    }

    public function hasEventProvider()
    {
        return [
            ['eventName', true],
            ['foobar', false]
        ];
    }

    public function testGetEvent()
    {
        $state = new State('stateName', [$this->event], [$this->flag]);
        $this->assertEquals($this->event, $state->getEvent('eventName'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Element for offset "undefinedEvent" not found
     */
    public function testGetUndefinedEvent()
    {
        $state = new State('stateName', [$this->event], [$this->flag]);
        $state->getEvent('undefinedEvent');
    }

    public function testGetFlags()
    {
        $state = new State('stateName', [$this->event], [$this->flag]);
        $this->assertSame(['flagName' => $this->flag], $state->getFlags());
    }

    /**
     * @dataProvider hasFlagProvider
     */
    public function testHasFlag($event, $expected)
    {
        $state = new State('stateName', [$this->event], [$this->flag]);
        $this->assertEquals($expected, $state->hasFlag($event));
    }

    public function hasFlagProvider()
    {
        return [
            ['flagName', true],
            ['foobar', false]
        ];
    }

    public function testGetFlag()
    {
        $state = new State('stateName', [$this->event], [$this->flag]);
        $this->assertEquals($this->flag, $state->getFlag('flagName'));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Element for offset "undefinedFlag" not found
     */
    public function testGetUndefinedFlag()
    {
        $state = new State('stateName', [$this->event], [$this->flag]);
        $state->getFlag('undefinedFlag');
    }

    /**
     * @dataProvider triggerEventProvider
     */
    public function testTriggerEvent($result)
    {
        $this->event->expects($this->any())->method('trigger')->willReturn($result);

        $payload = $this->getMock('\StateMachine\PayloadInterface');

        $state = new State('stateName', [$this->event], [$this->flag]);
        $this->assertEquals($result, $state->triggerEvent('eventName', $payload));
    }

    public function triggerEventProvider()
    {
        return [
            ['target'],
            ['error'],
            [null]
        ];
    }

    public function testString()
    {
        $this->assertEquals('stateName', (string) new State('stateName', [], []));
    }
}
