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

class StateDotTest extends \PHPUnit_Framework_TestCase
{
    public function testToStringWithFlag()
    {
        $flag = $this->getMockBuilder('StateMachine\Flag')->disableOriginalConstructor()->getMock();
        $flag->expects($this->any())->method('getName')->willReturn('foo');
        $flag->expects($this->any())->method('getValue')->willReturn('bar');

        $expected = 'node[label=<stateName<BR/><I><FONT POINT-SIZE="10" COLOR="#0066aa">foo: bar<BR/></FONT></I>>,height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="#ffffff",fontcolor="#000000"]{ state_stateName };';

        $dot = new StateDot('stateName', [$flag], '#ffffff', '#000000', '#0066aa');
        $this->assertEquals($expected, (string) $dot);
    }

    public function testToStringWithoutFlag()
    {
        $expected = 'node[label=<stateName>,height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="#ffffff",fontcolor="#000000"]{ state_stateName };';

        $dot = new StateDot('stateName', [], '#ffffff', '#000000', '#0066aa');
        $this->assertEquals($expected, (string) $dot);
    }
}
