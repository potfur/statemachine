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
        $expected = 'edge[dir="forward",style="solid",color="#ffffff",fontcolor="#000000"] state_fromState -> state_toState [label=" eventName"];';

        $dot = new Edge('fromState', 'toState', 'eventName', '#ffffff', '#000000', 'solid');
        $this->assertEquals($expected, (string) $dot);
    }
}
