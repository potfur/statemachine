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


class TimeoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid state or event name in timeout, can not be empty string
     */
    public function testStateNameIsNull()
    {
        new Timeout('', 'eventName', 'identifier', new \DateTime());
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid state or event name in timeout, can not be empty string
     */
    public function testEventNameIsNull()
    {
        new Timeout('stateName', '', 'identifier', new \DateTime());
    }

    public function testGetState()
    {
        $timeout = new Timeout('stateName', 'eventName', 'identifier', new \DateTime());
        $this->assertEquals('stateName', $timeout->getState());
    }

    public function testGetEvent()
    {
        $timeout = new Timeout('stateName', 'eventName', 'identifier', new \DateTime());
        $this->assertEquals('eventName', $timeout->getEvent());
    }

    public function testGetIdentifier()
    {
        $timeout = new Timeout('stateName', 'eventName', 'identifier', new \DateTime());
        $this->assertEquals('identifier', $timeout->getIdentifier());
    }

    public function testGetExecutionDate()
    {
        $date = new \DateTime();

        $timeout = new Timeout('stateName', 'eventName', 'identifier', $date);
        $this->assertEquals($date, $timeout->getExecutionDate());
    }
}
