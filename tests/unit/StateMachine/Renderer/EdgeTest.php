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

        $dot = new Edge('fromState', 'toState', 'eventName', 'comment', '#ffffff', 'solid', '#000000');
        $this->assertEquals($expected, (string) $dot);
    }
}
