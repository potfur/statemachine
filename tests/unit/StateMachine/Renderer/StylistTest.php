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


class StylistTest extends \PHPUnit_Framework_TestCase
{
    public function testDPI()
    {
        $stylist = new Stylist(100);
        $this->assertEquals(100, $stylist->getDPI());
    }

    public function testFont()
    {
        $stylist = new Stylist(75, 'Arial');
        $this->assertEquals('Arial', $stylist->getFont());
    }

    /**
     * @expectedException \StateMachine\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid color provided, must be valid 6 character HTML color code.
     *
     * @dataProvider colorKeyProvider
     */
    public function testInvalidColorFormat($key)
    {
        $stylist = new Stylist();
        $stylist->setStyles(['foo.bar' => [$key => 'foobar']]);
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
        $stylist = new Stylist(75, 'Courier', []);
        $stylist->setStyles(['state.event' => ['text' => '#000000', 'color' => '#444444', 'style' => 'solid']]);

        $expected =[
            '*.onStateWasSet' => ['style' => 'bold'],
            '*.onTimeout' => ['style' => 'dashed'],
            'state.event' => ['text' => '#000000', 'color' => '#444444', 'style' => 'solid']
        ];

        $this->assertEquals($expected, $stylist->getStyles());
    }

    public function testGetStateEventStyle()
    {
        $colorSet = ['text' => '#000000', 'color' => '#444444', 'style' => 'solid', 'altColor' => '#999999'];

        $stylist = new Stylist(75, 'Courier', []);
        $stylist->setStyles(['stateName.eventName' => $colorSet]);

        $expected = new Style('#000000', '#444444', 'solid', '#999999');
        $this->assertEquals($expected, $stylist->getStyle('state', 'stateName', 'eventName'));
    }

    public function testGetWildcardStateStyle()
    {
        $colorSet = ['text' => '#000000', 'color' => '#444444', 'style' => 'solid', 'altColor' => '#999999'];

        $stylist = new Stylist(75, 'Courier', []);
        $stylist->setStyles(['stateName.*' => $colorSet]);

        $expected = new Style('#000000', '#444444', 'solid', '#999999');
        $this->assertEquals($expected, $stylist->getStyle('state', 'stateName', 'eventName'));
    }

    public function testGetWildcardForEventStyle()
    {
        $colorSet = ['text' => '#000000', 'color' => '#444444', 'style' => 'solid', 'altColor' => '#999999'];

        $stylist = new Stylist(75, 'Courier', []);
        $stylist->setStyles(['*.eventName' => $colorSet]);

        $expected = new Style('#000000', '#444444', 'solid', '#999999');
        $this->assertEquals($expected, $stylist->getStyle('state', 'stateName', 'eventName'));
    }

    public function testOverwriteStateDefault()
    {
        $colorSet = ['text' => '#000000', 'color' => '#444444', 'style' => 'solid', 'altColor' => '#999999'];

        $stylist = new Stylist(75, 'Courier', []);
        $stylist->setStyles(['state' => $colorSet]);

        $expected = new Style('#000000', '#444444', 'solid', '#999999');
        $this->assertEquals($expected, $stylist->getStyle('state', 'stateName', 'eventName'));
    }
}
