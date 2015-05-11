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
 * State machine process renderer
 *
 * @package StateMachine
 */
class Renderer
{
    /**
     * Process/schema adapter
     *
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Path to graphviz executable
     *
     * @var string
     */
    private $executable;

    /**
     * Style collection
     *
     * @var Stylist
     */
    private $stylist;

    /**
     * @param AdapterInterface $adapter    process/schema adapter
     * @param string           $executable path to dot executable
     * @param Stylist          $stylist    style collection
     */
    public function __construct(AdapterInterface $adapter, $executable, Stylist $stylist)
    {
        $this->adapter = $adapter;
        $this->executable = $executable;
        $this->stylist = $stylist;
    }

    /**
     * Render process as png
     * Return path to output file
     * If output file exists, will be overwritten
     *
     * @param string $outputFile
     *
     * @return string
     */
    public function png($outputFile)
    {
        $tempFile = $this->buildDotFile();

        shell_exec(sprintf('%s -Tpng %s -o%s', $this->executable, $tempFile, $outputFile));

        return $outputFile;
    }

    /**
     * Render process as svg
     * Return path to output file
     * If output file exists, will be overwritten
     *
     * @param string $outputFile
     *
     * @return string
     */
    public function svg($outputFile)
    {
        $tempFile = $this->buildDotFile();

        shell_exec(sprintf('%s -Tsvg %s -o%s', $this->executable, $tempFile, $outputFile));

        return $outputFile;
    }

    /**
     * Build process structure in dot format
     * Return path to output file
     * If output file exists, will be overwritten
     *
     * @param string $outputFile
     *
     * @return string
     */
    public function dot($outputFile)
    {
        $document = new Document(
            $this->adapter->getProcess()->getName(),
            $this->stylist->getDPI(),
            $this->stylist->getFont()
        );

        foreach ($this->adapter->getProcess()->getStates() as $state) {
            $this->addState($document, $state);

            foreach ($state->getEvents() as $event) {
                $this->addEvent($document, $state, $event);
            }
        }

        file_put_contents($outputFile, (string) $document);

        return $outputFile;
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

    /**
     * Create file with dot schema
     *
     * @return string
     */
    private function buildDotFile()
    {
        return $this->dot(tempnam(sys_get_temp_dir(), 'smr'));
    }
}
