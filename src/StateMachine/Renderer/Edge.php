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

use StateMachine\Timeout;

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
     * Style
     *
     * @var Style
     */
    private $style;

    /**
     * Timeout
     *
     * @var Timeout|null
     */
    private $timeout;

    /**
     * Build edge/path between two nodes in dot format
     *
     * @param string       $fromState source state
     * @param string       $toState   target state
     * @param string       $event     event name
     * @param Style        $style     edge style
     * @param string       $comment   description
     * @param Timeout|null $timeout   timeout interval or fixed date
     */
    public function __construct($fromState, $toState, $event, Style $style, $comment = null, Timeout $timeout = null)
    {
        $this->fromState = $fromState;
        $this->toState = $toState;
        $this->event = $event;
        $this->style = $style;

        $this->comment = $comment;
        $this->timeout = $timeout;
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
            $this->event . $this->buildTimeout(),
            $this->style->getColor(),
            $this->style->getText(),
            $this->style->getStyle(),
            $this->comment
        );
    }

    /**
     * Build timeout string representation
     *
     * @return string
     */
    private function buildTimeout()
    {
        if (!$this->timeout) {
            return '';
        }

        return '\n' . (string) $this->timeout;
    }
}
