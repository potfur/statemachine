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

/**
 * Edge representing state machine event target and error path
 *
 * @package StateMachine
 */
final class Edge implements DotInterface
{
    private $fromState;
    private $toState;
    private $event;
    private $fillColor;
    private $textColor;
    private $edgeStyle;

    /**
     * Build edge/path between two nodes in dot format
     *
     * @param string $fromState
     * @param string $toState
     * @param string $event
     * @param string $fillColor
     * @param string $textColor
     * @param string $edgeStyle
     */
    public function __construct($fromState, $toState, $event, $fillColor, $textColor, $edgeStyle)
    {
        $this->fromState = $fromState;
        $this->toState = $toState;
        $this->event = $event;
        $this->fillColor = $fillColor;
        $this->textColor = $textColor;
        $this->edgeStyle = $edgeStyle;
    }

    /**
     * Return dot element string representation
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'edge[dir="forward",style="%6$s",color="%4$s",fontcolor="%5$s"] state_%1$s -> state_%2$s [label=" %3$s"];',
            $this->fromState,
            $this->toState,
            $this->event,
            $this->fillColor,
            $this->textColor,
            $this->edgeStyle
        );
    }
}
