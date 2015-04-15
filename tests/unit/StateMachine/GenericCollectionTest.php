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

class FakeBaseClass
{
    public function __toString()
    {
        return 'fakeClass';
    }
}

class FakeExtendingClass extends FakeBaseClass
{
}


class GenericCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $collection = new GenericCollection(['foo']);
        $this->assertEquals('foo', $collection->get('foo'));
    }

    /**
     * @expectedException \StateMachine\Exception\OutOfRangeException
     * @expectedExceptionMessage Element for offset "undefined" not found
     */
    public function testGetUndefinedOffset()
    {
        $collection = new GenericCollection();
        $collection->get('undefined');
    }

    public function testSet()
    {
        $collection = new GenericCollection([]);
        $collection->set('foo');
        $this->assertEquals('foo', $collection->get('foo'));
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Element in collection must be instance of "\stdClass"
     */
    public function testSetInvalidInstance()
    {
        $collection = new GenericCollection([], '\stdClass');
        $collection->set('foo');
        $this->assertEquals('foo', $collection->get('foo'));
    }

    public function testSetProperInstance()
    {
        $element = $this->getMockBuilder('\StateMachine\FakeExtendingClass')->disableOriginalConstructor()->getMock();
        $element->expects($this->any())->method('__toString')->willReturn('key');

        $collection = new GenericCollection([], '\StateMachine\FakeBaseClass');
        $collection->set($element);
        $this->assertEquals($element, $collection->get('key'));
    }

    public function testAll()
    {
        $collection = new GenericCollection(['foo', 'bar']);
        $this->assertEquals(['foo' => 'foo', 'bar' => 'bar'], $collection->all());
    }

    public function testCount()
    {
        $element = $this->getMock('\StateMachine\Collection\CollectibleInterface');
        $element->expects($this->any())->method('getName')->willReturn('foo');

        $collection = new GenericCollection(['foo', 'bar']);
        $this->assertEquals(2, $collection->count());
    }
}
