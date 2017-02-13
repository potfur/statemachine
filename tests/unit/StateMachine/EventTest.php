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

use StateMachine\Attributes;
use StateMachine\Event;
use StateMachine\Exception\InvalidArgumentException;
use StateMachine\PayloadEnvelope;

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
    public function testNameIsNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid event name, can not be empty string');

        new Event('');
    }

    public function testName()
    {
        $event = new Event('eventName');
        $this->assertEquals('eventName', $event->name());
    }

    public function testTargetState()
    {
        $event = new Event('eventName', 'targetState');
        $this->assertEquals('targetState', $event->targetState());
    }

    public function testErrorState()
    {
        $event = new Event('eventName', null, 'errorState');
        $this->assertEquals('errorState', $event->errorState());
    }

    public function testAttributes()
    {
        $event = new Event('eventName', null, 'errorState', null, []);
        $this->assertInstanceOf(Attributes::class, $event->attributes());
    }

    /**
     * @dataProvider triggerProvider
     */
    public function testTrigger($targetState, $errorState, $result, $expected)
    {
        $payload = PayloadEnvelope::wrap('stuff');

        $event = new Event('eventName', $targetState, $errorState, function() use ($result) { return $result; });

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
