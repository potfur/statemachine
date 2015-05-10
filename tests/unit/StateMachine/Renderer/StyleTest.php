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


class StyleTest extends \PHPUnit_Framework_TestCase
{
    public function testDPI()
    {
        $style = new Style(100);
        $this->assertEquals(100, $style->getDPI());
    }

    public function testFont()
    {
        $style = new Style(75, 'Arial');
        $this->assertEquals('Arial', $style->getFont());
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid color provided, must be valid 6 character HTML color code.
     *
     * @dataProvider colorKeyProvider
     */
    public function testInvalidColorFormat($key)
    {
        $style = new Style();
        $style->setStyles(['foo.bar' => [$key => 'foobar']]);
    }

    public function colorKeyProvider()
    {
        return [
            ['text'],
            ['color'],
            ['altColor']
        ];
    }

    public function testStyles()
    {
        $style = new Style(75, 'Courier', []);
        $style->setStyles(['state.event' => ['text' => '#000000', 'color' => '#444444', 'style' => 'solid']]);

        $expected =[
            '*.onStateWasSet' => ['style' => 'bold'],
            '*.onTimeout' => ['style' => 'dashed'],
            'state.event' => ['text' => '#000000', 'color' => '#444444', 'style' => 'solid']
        ];

        $this->assertEquals($expected, $style->getStyles());
    }

    public function testGetStateEventStyle()
    {
        $colorSet = ['text' => '#000000', 'color' => '#444444', 'style' => 'solid', 'altColor' => '#999999'];

        $style = new Style(75, 'Courier', []);
        $style->setStyles(['stateName.eventName' => $colorSet]);

        $this->assertEquals($colorSet, $style->getStyle('state', 'stateName', 'eventName'));
    }

    public function testGetWildcardStateStyle()
    {
        $colorSet = ['text' => '#000000', 'color' => '#444444', 'style' => 'solid', 'altColor' => '#999999'];

        $style = new Style(75, 'Courier', []);
        $style->setStyles(['stateName.*' => $colorSet]);

        $this->assertEquals($colorSet, $style->getStyle('state', 'stateName', 'eventName'));
    }

    public function testGetWildcardForEventStyle()
    {
        $colorSet = ['text' => '#000000', 'color' => '#444444', 'style' => 'solid', 'altColor' => '#999999'];

        $style = new Style(75, 'Courier', []);
        $style->setStyles(['*.eventName' => $colorSet]);

        $this->assertEquals($colorSet, $style->getStyle('state', 'stateName', 'eventName'));
    }

    public function testOverwriteStateDefault()
    {
        $colorSet = ['text' => '#000000', 'color' => '#444444', 'style' => 'solid', 'altColor' => '#999999'];

        $style = new Style(75, 'Courier', []);
        $style->setStyles(['state' => $colorSet]);

        $this->assertEquals($colorSet, $style->getStyle('state', 'stateName', 'eventName'));
    }
}
