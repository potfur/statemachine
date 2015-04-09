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
    private $state;
    private $flags;
    private $fillColor;
    private $textColor;
    private $flagColor;

    /**
     * Build edge/path between two nodes in dot format
     *
     * @param string $state
     * @param Flag[] $flags
     * @param string $fillColor
     * @param string $textColor
     * @param string $flagColor
     */
    public function __construct($state, array $flags, $fillColor, $textColor, $flagColor)
    {
        $this->state = $state;
        $this->flags = $flags;
        $this->fillColor = $fillColor;
        $this->textColor = $textColor;
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
            'node[label=<%1$s%2$s>,height="0.6",shape="ellipse",style="filled",color="transparent",fillcolor="%3$s",fontcolor="%4$s"]{ state_%1$s };',
            $this->state,
            $this->buildFlags($this->flags, $this->flagColor),
            $this->fillColor,
            $this->textColor
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
