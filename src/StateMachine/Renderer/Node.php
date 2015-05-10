<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Renderer;

use StateMachine\Flag;

/**
 * State node
 *
 * @package StateMachine
 */
class Node implements DotInterface
{
    /**
     * State name
     *
     * @var string
     */
    private $state;

    /**
     * State flags
     *
     * @var array|\StateMachine\Flag[]
     */
    private $flags;

    /**
     * Description
     *
     * @var string
     */
    private $comment;

    /**
     * Color used for node background
     *
     * @var string
     */
    private $fillColor;

    /**
     * Label color
     *
     * @var string
     */
    private $textColor;

    /**
     * Shape
     *
     * @var string
     */
    private $shape;

    /**
     * Color for flag names
     *
     * @var string
     */
    private $flagColor;

    /**
     * Build edge/path between two nodes in dot format
     *
     * @param string $state     state name
     * @param Flag[] $flags     array with flags
     * @param string $comment   description
     * @param string $fillColor color shape background
     * @param string $textColor text color
     * @param string $shape     shape name
     * @param string $flagColor color for flag names
     */
    public function __construct($state, array $flags, $comment = null, $fillColor = '#ebebeb', $textColor = '#444444', $shape = 'ellipse', $flagColor = '#0066aa')
    {
        $this->state = $state;
        $this->flags = $flags;
        $this->comment = $comment;
        $this->fillColor = $fillColor;
        $this->textColor = $textColor;
        $this->shape = $shape;
        $this->flagColor = $flagColor;
    }

    /**
     * Return dot element string representation
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'node[label=<%1$s%2$s>,tooltip="%6$s",height="0.6",shape="%5$s",style="filled",color="transparent",fillcolor="%3$s",fontcolor="%4$s"]{ state_%1$s };',
            $this->state,
            $this->buildFlags($this->flags, $this->flagColor),
            $this->fillColor,
            $this->textColor,
            $this->shape,
            $this->comment
        );
    }

    /**
     * Build flag HTML string
     * Empty string is returned when there are no flags
     *
     * @param Flag[] $flags
     * @param string $color
     *
     * @return string
     */
    private function buildFlags(array $flags, $color)
    {
        if (empty($flags)) {
            return '';
        }

        $string = '';
        foreach ($flags as $flag) {
            $string .= sprintf('%s: %s<BR/>', $flag->getName(), $flag->getValue());
        }

        return sprintf('<BR/><I><FONT POINT-SIZE="10" COLOR="%s">%s</FONT></I>', $color, $string);
    }
}
