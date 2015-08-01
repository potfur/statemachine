<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace unit\StateMachine;

use StateMachine\AttributeCollection;

class AttributesTest extends \PHPUnit_Framework_TestCase
{
    public function testExists()
    {
        $attributes = new AttributeCollection(['foo' => 'bar']);
        $this->assertTrue($attributes->exists('foo'));
    }

    public function testExistsWhenNull()
    {
        $attributes = new AttributeCollection(['foo' => null]);
        $this->assertTrue($attributes->exists('foo'));
    }

    public function testDoesNotExist()
    {
        $attributes = new AttributeCollection([]);
        $this->assertFalse($attributes->exists('foo'));
    }

    public function testGet()
    {
        $attributes = new AttributeCollection(['foo' => 'bar']);
        $this->assertEquals('bar' , $attributes->get('foo'));
    }

    public function testGetMissingKey()
    {
        $attributes = new AttributeCollection();
        $this->assertNull($attributes->get('foo'));
    }

    public function testGetMissingKeyWithDefault()
    {
        $attributes = new AttributeCollection();
        $this->assertEquals('bar' , $attributes->get('foo', 'bar'));
    }
}
