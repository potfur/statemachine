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

use StateMachine\AdapterInterface;
use StateMachine\EventInterface;
use StateMachine\StateInterface;

/**
 * Builds document from passed adapter
 *
 * @package StateMachine
 */
final class DocumentBuilder
{
    /**
     * Style collection
     *
     * @var Stylist
     */
    private $stylist;

    /**
     * @param Stylist $stylist style collection
     */
    public function __construct(Stylist $stylist)
    {
        $this->stylist = $stylist;
    }

    /**
     * Build dot document from process structure
     *
     * @param AdapterInterface $adapter process/schema adapter
     *
     * @return Document
     */
    public function build(AdapterInterface $adapter)
    {
        $document = new Document(
            $adapter->getProcess()->getName(),
            $this->stylist->getDPI(),
            $this->stylist->getFont()
        );

        foreach ($adapter->getProcess()->getStates() as $state) {
            $this->addState($document, $state);

            foreach ($state->getEvents() as $event) {
                $this->addEvent($document, $state, $event);
            }
        }

        return $document;
    }

    /**
     * Create state for dot notation
     *
     * @param Document       $document
     * @param StateInterface $state
     */
    private function addState(Document $document, StateInterface $state)
    {
        $document->addState(
            new Node(
                $state->getName(),
                $state->getFlags(),
                $this->stylist->getStyle('state', $state->getName()),
                $state->getComment()
            )
        );
    }

    /**
     * Create edge for dot notation
     *
     * @param Document       $document
     * @param StateInterface $state
     * @param EventInterface $event
     */
    private function addEvent(Document $document, StateInterface $state, EventInterface $event)
    {
        foreach (array_filter($event->getStates()) as $type => $target) {
            $style = $this->stylist->getStyle($type, $state->getName(), $event->getName());
            $document->addEdge($this->createEdge($state, $event, $target, $style));
        }
    }

    /**
     * Create edge for dot notation
     *
     * @param StateInterface $state
     * @param EventInterface $event
     * @param string         $nextState
     * @param Style          $style
     *
     * @return Edge
     */
    private function createEdge(StateInterface $state, EventInterface $event, $nextState, Style $style)
    {
        return new Edge(
            $state->getName(),
            $nextState,
            $event->getName(),
            $style,
            $event->getComment(),
            $event->getTimeout()
        );
    }
}
