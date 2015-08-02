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

use StateMachine\AdapterInterface;
use StateMachine\ProcessInterface;
use StateMachine\StateInterface;

class DocumentBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var StateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $state;

    /**
     * @var ProcessInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $process;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    public function setUp()
    {
        $this->state = $this->getMock('\StateMachine\StateInterface');

        $this->process = $this->getMock('\StateMachine\ProcessInterface');
        $this->process->expects($this->any())->method('getStates')->willReturn([$this->state]);

        $this->adapter = $this->getMock('\StateMachine\AdapterInterface');
        $this->adapter->expects($this->any())->method('getProcess')->willReturn($this->process);
    }

    public function testDotFormat()
    {
        $attributes = $this->getMock('\StateMachine\AttributeCollectionInterface');

        $this->state->expects($this->any())->method('getName')->willReturn('name');
        $this->state->expects($this->any())->method('getFlags')->willReturn([]);
        $this->state->expects($this->any())->method('getEvents')->willReturn([]);
        $this->state->expects($this->any())->method('getAttributes')->willReturn($attributes);

        $this->process->expects($this->any())->method('getName')->willReturn('processName');

        $renderer = new DocumentBuilder(new Stylist());
        $document = $renderer->build($this->adapter);

        $this->assertEquals(
            'digraph processName {dpi="75";pad="1";fontname="Courier";nodesep="1";rankdir="TD";ranksep="0.5";node[label=<name>,tooltip="",height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="#ebebeb",fontcolor="#444444"]{ state_name };}',
            (string) $document
        );
    }

    public function testDotFormatWithEvent()
    {
        $attributes = $this->getMock('\StateMachine\AttributeCollectionInterface');

        $event = $this->getMockBuilder('\StateMachine\EventInterface')->disableOriginalConstructor()->getMock();
        $event->expects($this->any())->method('getName')->willReturn('eventName');
        $event->expects($this->any())->method('getStates')->willReturn(['target' => 'targetState', 'error' => 'errorState']);
        $event->expects($this->any())->method('getAttributes')->willReturn($attributes);

        $this->state->expects($this->any())->method('getName')->willReturn('name');
        $this->state->expects($this->any())->method('getFlags')->willReturn([]);
        $this->state->expects($this->any())->method('getEvents')->willReturn([$event]);
        $this->state->expects($this->any())->method('getAttributes')->willReturn($attributes);

        $this->process->expects($this->any())->method('getName')->willReturn('processName');

        $renderer = new DocumentBuilder(new Stylist());
        $document = $renderer->build($this->adapter);

        $this->assertEquals(
            'digraph processName {dpi="75";pad="1";fontname="Courier";nodesep="1";rankdir="TD";ranksep="0.5";node[label=<name>,tooltip="",height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="#ebebeb",fontcolor="#444444"]{ state_name };edge[label=" eventName",tooltip="",dir="forward",style="solid",color="#99BB11",fontcolor="#999999"] state_name -> state_targetState;edge[label=" eventName",tooltip="",dir="forward",style="solid",color="#ee1155",fontcolor="#999999"] state_name -> state_errorState;}',
            (string) $document
        );
    }
}
