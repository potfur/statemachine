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


final class Style
{
    private $text;

    private $color;

    private $altColor;

    private $style;

    /**
     * @param string $text
     * @param string $color
     * @param string $style
     * @param string $altColor
     */
    public function __construct($text, $color, $style, $altColor = null)
    {
        $this->text = $text;
        $this->color = $color;
        $this->style = $style;
        $this->altColor = $altColor;
    }

    /**
     * Return text color
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Return primary color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Return alternative color
     *
     * @return string
     */
    public function getAltColor()
    {
        return $this->altColor;
    }

    /**
     * Return style (shape/edge)
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }


}
