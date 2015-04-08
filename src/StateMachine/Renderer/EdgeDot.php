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
final class EdgeDot implements DotInterface
{
    private $fromState;
    private $toState;
    private $event;
    private $fillColor;
    private $textColor;

    /**
     * Build edge/path between two nodes in dot format
     *
     * @param string $fromState
     * @param string $toState
     * @param string $event
     * @param string $fillColor
     * @param string $textColor
     */
    public function __construct($fromState, $toState, $event, $fillColor, $textColor)
    {
        $this->fromState = $fromState;
        $this->toState = $toState;
        $this->event = $event;
        $this->fillColor = $fillColor;
        $this->textColor = $textColor;
    }

    /**
     * Return dot element string representation
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'edge[dir="forward",style="solid",color="%4$s",fontcolor="%5$s"] state_%1$s -> state_%2$s [label=" %3$s"];',
            $this->fromState,
            $this->toState,
            $this->event,
            $this->fillColor,
            $this->textColor
        );
    }
}
