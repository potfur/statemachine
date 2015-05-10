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
    /**
     * Source state name
     *
     * @var string
     */
    private $fromState;

    /**
     * Target state name
     *
     * @var string
     */
    private $toState;

    /**
     * Event name
     *
     * @var string
     */
    private $event;

    /**
     * Description
     *
     * @var string
     */
    private $comment;

    /**
     * Edge/line color
     *
     * @var string
     */
    private $edgeColor;

    /**
     * Edge/line color
     *
     * @var string
     */
    private $edgeStyle;

    /**
     * Label color
     *
     * @var string
     */
    private $textColor;

    /**
     * Build edge/path between two nodes in dot format
     *
     * @param string $fromState source state
     * @param string $toState   target state
     * @param string $event     event name
     * @param string $comment   description
     * @param string $edgeColor color for edge/line
     * @param string $textColor label color
     * @param string $edgeStyle edge/line style
     */
    public function __construct($fromState, $toState, $event, $comment = null, $edgeColor, $edgeStyle, $textColor)
    {
        $this->fromState = $fromState;
        $this->toState = $toState;
        $this->event = $event;
        $this->comment = $comment;
        $this->edgeColor = $edgeColor;
        $this->edgeStyle = $edgeStyle;
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
            'edge[label=" %3$s",tooltip="%7$s",dir="forward",style="%6$s",color="%4$s",fontcolor="%5$s"] state_%1$s -> state_%2$s;',
            $this->fromState,
            $this->toState,
            $this->event,
            $this->edgeColor,
            $this->textColor,
            $this->edgeStyle,
            $this->comment
        );
    }
}
