<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace unit\StateMachine\Collection;

use StateMachine\Collection\Events;
use StateMachine\Event;
use StateMachine\Collection\OutOfRangeException;

class EventsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Event
     */
    private $event;

    public function setUp()
    {
        $this->event = new Event('foo');
    }

    public function testGet()
    {
        $collection = new Events([$this->event]);
        $this->assertEquals($this->event, $collection->get('foo'));
    }

    public function testGetUndefinedOffset()
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage('Element for offset "undefined" not found');

        $collection = new Events();
        $collection->get('undefined');
    }

    public function testAdd()
    {
        $collection = new Events([]);
        $collection->add($this->event);
        $this->assertEquals('foo', $collection->get('foo'));
    }

    public function testAll()
    {
        $collection = new Events([$this->event]);
        $this->assertEquals(['foo' => $this->event], $collection->all());
    }

    public function testCount()
    {
        $collection = new Events([$this->event]);
        $this->assertEquals(1, $collection->count());
    }
}
