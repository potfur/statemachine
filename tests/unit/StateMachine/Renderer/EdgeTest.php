<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Renderer;

class EdgeTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $expected = 'edge[label=" eventName",tooltip="comment",dir="forward",style="solid",color="#ffffff",fontcolor="#000000"] state_fromState -> state_toState;';

        $style = new Style('#000000', '#ffffff', 'solid');

        $dot = new Edge('fromState', 'toState', 'eventName', $style, 'comment');
        $this->assertEquals($expected, (string) $dot);
    }

    public function testToStringWithTimeoutInterval()
    {
        $expected = 'edge[label=" eventName\nP1DT10S",tooltip="comment",dir="forward",style="solid",color="#ffffff",fontcolor="#000000"] state_fromState -> state_toState;';

        $style = new Style('#000000', '#ffffff', 'solid');

        $dot = new Edge('fromState', 'toState', 'eventName', $style, 'comment', new \DateInterval('P1DT10S'));
        $this->assertEquals($expected, (string) $dot);
    }

    public function testToStringWithTimeoutDate()
    {
        $expected = 'edge[label=" eventName\n2015-05-11T10:10:10+00:00",tooltip="comment",dir="forward",style="solid",color="#ffffff",fontcolor="#000000"] state_fromState -> state_toState;';

        $style = new Style('#000000', '#ffffff', 'solid');

        $dot = new Edge('fromState', 'toState', 'eventName', $style, 'comment', new \DateTime('2015-05-11 10:10:10', new \DateTimeZone('UTC')));
        $this->assertEquals($expected, (string) $dot);
    }
}
