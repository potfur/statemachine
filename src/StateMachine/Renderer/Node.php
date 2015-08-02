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
     * Style
     *
     * @var Style
     */
    private $style;

    /**
     * Build edge/path between two nodes in dot format
     *
     * @param string $state   state name
     * @param Flag[] $flags   array with flags
     * @param string $comment description
     * @param Style  $style   style
     */
    public function __construct($state, array $flags, Style $style, $comment = null)
    {
        $this->state = $state;
        $this->flags = $flags;
        $this->comment = $comment;
        $this->style = $style;
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
            $this->buildFlags($this->flags, $this->style->getAltColor()),
            $this->style->getColor(),
            $this->style->getText(),
            $this->style->getStyle(),
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
