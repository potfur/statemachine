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
use StateMachine\Event;
use StateMachine\ProcessInterface;
use StateMachine\StateInterface;

function sys_get_temp_dir() { return './'; }

function tempnam($dir, $prefix = null) { return $dir . $prefix . 'tmp'; }

function shell_exec($cmd) { echo $cmd; }

class RendererTest extends \PHPUnit_Framework_TestCase
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

    public function tearDown()
    {
        foreach (['./foo.dot', './foo.png', './foo.svg', './smrtmp'] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function testColors()
    {
        $default = [
            'state' => ['text' => '#444444', 'color' => '#ebebeb', 'flag' => '#0066aa'],
            'target' => ['text' => '#999999', 'color' => '#99BB11'],
            'error' => ['text' => '#999999', 'color' => '#ee1155']
        ];

        $renderer = new Renderer($this->adapter, '/usr/local/bin/dot');
        $this->assertEquals($default, $renderer->getColors());

        $modified = [
            'state' => ['text' => '#444444', 'color' => '#ebebeb', 'flag' => '#0066aa'],
            'target' => ['text' => '#999999', 'color' => '#99BB11'],
            'error' => ['text' => '#999999', 'color' => '#ee1155']
        ];
        $renderer->setColors($modified);
        $this->assertEquals($modified, $renderer->getColors());
    }

    public function testDotFormat()
    {
        $this->state->expects($this->any())->method('getName')->willReturn('name');
        $this->state->expects($this->any())->method('getFlags')->willReturn([]);
        $this->state->expects($this->any())->method('getEvents')->willReturn([]);

        $this->process->expects($this->any())->method('getName')->willReturn('processName');

        $renderer = new Renderer($this->adapter, '/usr/local/bin/dot');
        $renderer->dot('./foo.dot');

        $this->assertFileExists('./foo.dot');

        // reversed, expected is actual
        $this->assertStringEqualsFile('./foo.dot', 'digraph processName {dpi="75";pad="1";fontname="Courier";nodesep="1";rankdir="TD";ranksep="0.5";node[label=<name>,height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="#ebebeb",fontcolor="#444444"]{ state_name };}');
    }

    public function testDotFormatWithEvent()
    {
        $event = $this->getMockBuilder('\StateMachine\Event')->disableOriginalConstructor()->getMock();
        $event->expects($this->any())->method('getName')->willReturn('eventName');
        $event->expects($this->any())->method('getTargetState')->willReturn('targetState');
        $event->expects($this->any())->method('getErrorState')->willReturn('errorState');

        $this->state->expects($this->any())->method('getName')->willReturn('name');
        $this->state->expects($this->any())->method('getFlags')->willReturn([]);
        $this->state->expects($this->any())->method('getEvents')->willReturn([$event]);

        $this->process->expects($this->any())->method('getName')->willReturn('processName');

        $renderer = new Renderer($this->adapter, '/usr/local/bin/dot');
        $renderer->dot('./foo.dot');

        $this->assertFileExists('./foo.dot');

        // reversed, expected is actual
        $this->assertStringEqualsFile('./foo.dot', 'digraph processName {dpi="75";pad="1";fontname="Courier";nodesep="1";rankdir="TD";ranksep="0.5";node[label=<name>,height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="#ebebeb",fontcolor="#444444"]{ state_name };edge[dir="forward",style="solid",color="#99BB11",fontcolor="#999999"] state_name -> state_targetState [label=" eventName"];edge[dir="forward",style="solid",color="#ee1155",fontcolor="#999999"] state_name -> state_errorState [label=" eventName"];}');
    }

    public function testPNG()
    {
        $this->state->expects($this->any())->method('getName')->willReturn('name');
        $this->state->expects($this->any())->method('getFlags')->willReturn([]);
        $this->state->expects($this->any())->method('getEvents')->willReturn([]);

        $this->process->expects($this->any())->method('getName')->willReturn('processName');

        $renderer = new Renderer($this->adapter, '/usr/local/bin/dot');
        $renderer->png('./foo.png');

        $this->expectOutputString('/usr/local/bin/dot -Tpng ./smrtmp -o./foo.png');
    }

    public function testSVG()
    {
        $this->state->expects($this->any())->method('getName')->willReturn('name');
        $this->state->expects($this->any())->method('getFlags')->willReturn([]);
        $this->state->expects($this->any())->method('getEvents')->willReturn([]);

        $this->process->expects($this->any())->method('getName')->willReturn('processName');

        $renderer = new Renderer($this->adapter, '/usr/local/bin/dot');
        $renderer->svg('./foo.svg');

        $this->expectOutputString('/usr/local/bin/dot -Tsvg ./smrtmp -o./foo.svg');
    }
}
