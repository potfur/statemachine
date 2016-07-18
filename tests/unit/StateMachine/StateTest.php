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
use StateMachine\Collection\OutOfRangeException;
use StateMachine\Payload\PayloadEnvelope;
use StateMachine\State;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Event
     */
    private $event;

    public function setUp()
    {
        $this->event = new Event('eventName');
    }

    public function testNameIsNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid state name, can not be empty string');

        new State('', [], []);
    }

    public function testGetName()
    {
        $state = new State('stateName', [], []);
        $this->assertEquals('stateName', $state->name());
    }

    public function testGetEvents()
    {
        $state = new State('stateName', [$this->event]);
        $this->assertEquals(['eventName' => $this->event], $state->events());
    }

    /**
     * @dataProvider hasEventProvider
     */
    public function testHasEvent($event, $expected)
    {
        $state = new State('stateName', [$this->event]);
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
        $state = new State('stateName', [$this->event]);
        $this->assertEquals($this->event, $state->event('eventName'));
    }

    public function testGetUndefinedEvent()
    {
         $this->expectException(OutOfRangeException::class);
         $this->expectExceptionMessage('Element for offset "undefinedEvent" not found');

        $state = new State('stateName', [$this->event]);
        $state->event('undefinedEvent');
    }

    public function testAttributes()
    {
        $state = new State('stateName', [], [], []);
        $this->assertInstanceOf(Attributes::class, $state->attributes());
    }

    public function testTriggerEvent()
    {
        $command = function () { return true; };
        $event = new Event('eventName', 'targetState', 'errorState', $command);
        $state = new State('stateName', [$event]);
        $this->assertEquals('targetState', $state->triggerEvent('eventName', PayloadEnvelope::wrap('stuff')));
    }

    public function testString()
    {
        $this->assertEquals('stateName', (string) new State('stateName', [], []));
    }
}
