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

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testToStringWithFlag()
    {
        $expected = 'node[label=<stateName<BR/><I><FONT POINT-SIZE="10" COLOR="#0066aa">foo: bar<BR/></FONT></I>>,tooltip="comment",height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="#ffffff",fontcolor="#000000"]{ state_stateName };';

        $flag = $this->getMockBuilder('StateMachine\Flag')->disableOriginalConstructor()->getMock();
        $flag->expects($this->any())->method('getName')->willReturn('foo');
        $flag->expects($this->any())->method('getValue')->willReturn('bar');

        $style = new Style('#000000', '#ffffff', 'ellipse', '#0066aa');

        $dot = new Node('stateName', [$flag], $style, 'comment', '#ffffff', '#000000', 'ellipse', '#0066aa');
        $this->assertEquals($expected, (string) $dot);
    }

    public function testToStringWithoutFlag()
    {
        $expected = 'node[label=<stateName>,tooltip="comment",height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="#ffffff",fontcolor="#000000"]{ state_stateName };';

        $style = new Style('#000000', '#ffffff', 'ellipse', '#0066aa');

        $dot = new Node('stateName', [], $style, 'comment');
        $this->assertEquals($expected, (string) $dot);
    }
}
