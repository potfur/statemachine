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

class CommandMock
{
    private $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function __invoke()
    {
        return $this->result;
    }
}

class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid event name, can not be empty string
     */
    public function testNameIsNull()
    {
        new Event('');
    }

    public function testName()
    {
        $event = new Event('eventName');
        $this->assertEquals('eventName', $event->getName());
    }

    public function testTargetState()
    {
        $event = new Event('eventName', 'targetState');
        $this->assertEquals('targetState', $event->getTargetState());
    }

    public function testErrorState()
    {
        $event = new Event('eventName', null, 'errorState');
        $this->assertEquals('errorState', $event->getErrorState());
    }

    public function testComment()
    {
        $event = new Event('eventName', null, 'errorState', null, null, 'Lorem ipsum');
        $this->assertEquals('Lorem ipsum', $event->getComment());
    }

    /**
     * @dataProvider timeoutProvider
     */
    public function testHasTimeout($timeout)
    {
        $event = new Event('eventName', null, null, null, $timeout);
        $this->assertEquals($timeout !== null, $event->hasTimeout());
    }

    /**
     * @dataProvider timeoutProvider
     */
    public function testGetTimeout($timeout, $expected)
    {
        $now = new \DateTime('2015-03-31 10:10:10');

        $event = new Event('eventName', null, null, null, $timeout);
        $this->assertEquals($expected, $event->getTimeout($now));
    }

    public function timeoutProvider()
    {
        return [
            [new \DateTime('2015-04-01 10:10:10'), new \DateTime('2015-04-01 10:10:10')],
            [new \DateInterval('PT60S'), new \DateTime('2015-03-31 10:11:10')],
            [null, new \DateTime('2015-03-31 10:10:10')],
        ];
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid timeout value, must be instance of \DateInterval for relative timeout, \DateTime for fixed date or null when without timeout
     */
    public function testInvalidTimeout()
    {
        new Event('eventName', null, null, null, 'foobar');
    }

    /**
     * @dataProvider triggerProvider
     */
    public function testTrigger($targetState, $errorState, $result, $expected)
    {
        $payload = $this->getMock('\StateMachine\PayloadInterface');


        $commands = new CommandCollection([new CommandMock($result)]);

        $event = new Event('eventName', $targetState, $errorState, $commands);

        $this->assertEquals($expected, $event->trigger($payload));
    }

    public function triggerProvider()
    {
        return [
            ['targetState', 'errorState', true, 'targetState'],
            ['targetState', 'errorState', false, 'errorState'],
            ['targetState', null, true, 'targetState'],
            [null, null, true, null],
            [null, 'errorState', false, 'errorState'],
            [null, null, false, null],
        ];
    }

    public function testString()
    {
        $this->assertEquals('eventName', new Event('eventName'));
    }
}
