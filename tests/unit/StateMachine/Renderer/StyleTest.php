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
    public function testText()
    {
        $style = new Style('#444444', '#99BB11', 'ellipse', '#0066aa');
        $this->assertEquals('#444444', $style->getText());
    }

    public function testColor()
    {
        $style = new Style('#444444', '#99BB11', 'ellipse', '#0066aa');
        $this->assertEquals('#99BB11', $style->getColor());
    }

    public function testAltColor()
    {
        $style = new Style('#444444', '#99BB11', 'ellipse', '#0066aa');
        $this->assertEquals('#0066aa', $style->getAltColor());
    }

    public function testStyle()
    {
        $style = new Style('#444444', '#99BB11', 'ellipse', '#0066aa');
        $this->assertEquals('ellipse', $style->getStyle());
    }
}
