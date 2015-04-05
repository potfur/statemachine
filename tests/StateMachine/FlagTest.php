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


class FlagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid flag name, can not be empty string
     */
    public function testNameIsNull()
    {
        new Flag('', 'flagValue');
    }

    public function testGetName()
    {
        $flag = new Flag('flagName', 'flagValue');
        $this->assertEquals('flagName', $flag->getName());
    }

    public function testGetValue()
    {
        $flag = new Flag('flagName', 'flagValue');
        $this->assertEquals('flagValue', $flag->getValue());
    }

    public function testString()
    {
        $this->assertEquals('flagName', new Flag('flagName', 'flagValue'));
    }
}
