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

use StateMachine\Collection\OutOfRangeException;
use StateMachine\Collection\States;
use StateMachine\State;

class StatesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $states;

    public function setUp()
    {
        $this->states = new State('foo');
    }

    public function testGet()
    {
        $collection = new States([$this->states]);
        $this->assertEquals($this->states, $collection->get('foo'));
    }

    public function testGetUndefinedOffset()
    {
        $this->expectException(OutOfRangeException::class);
        $this->expectExceptionMessage('Element for offset "undefined" not found');
        
        $collection = new States();
        $collection->get('undefined');
    }

    public function testAdd()
    {
        $collection = new States([]);
        $collection->add($this->states);
        $this->assertEquals('foo', $collection->get('foo'));
    }

    public function testAll()
    {
        $collection = new States([$this->states]);
        $this->assertEquals(['foo' => $this->states], $collection->all());
    }

    public function testCount()
    {
        $collection = new States([$this->states]);
        $this->assertEquals(1, $collection->count());
    }
}
