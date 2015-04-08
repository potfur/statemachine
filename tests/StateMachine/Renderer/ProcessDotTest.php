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

class ProcessDotTest extends \PHPUnit_Framework_TestCase
{
    public function testToStringWithNodes()
    {
        $path = $this->getMock('StateMachine\Renderer\DotInterface');
        $path->expects($this->any())->method('__toString')->willReturn('*EDGE*;');

        $state = $this->getMock('StateMachine\Renderer\DotInterface');
        $state->expects($this->any())->method('__toString')->willReturn('*STATE*;');

        $expected = 'digraph processName {dpi="75";pad="1";fontname="Courier";nodesep="1";rankdir="TD";ranksep="0.5";*STATE*;*EDGE*;}';

        $dot = new ProcessDot('processName', 75, 'Courier');
        $dot->addState($state);
        $dot->addEdge($path);
        $this->assertEquals($expected, (string) $dot);
    }

    public function testToStringWithoutNodes()
    {
        $path = $this->getMock('StateMachine\Renderer\DotInterface');
        $path->expects($this->any())->method('__toString')->willReturn('*EDGE*;');

        $state = $this->getMock('StateMachine\Renderer\DotInterface');
        $state->expects($this->any())->method('__toString')->willReturn('*STATE*;');

        $expected = 'digraph processName {dpi="75";pad="1";fontname="Courier";nodesep="1";rankdir="TD";ranksep="0.5";}';

        $dot = new ProcessDot('processName', 75, 'Courier');
        $this->assertEquals($expected, (string) $dot);
    }
}
