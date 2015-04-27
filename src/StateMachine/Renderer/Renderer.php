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
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var string
     */
    private $executable;

    /**
     * @var Style
     */
    private $style;

    /**
     * @param AdapterInterface $adapter
     * @param string           $executable path to dot executable
     * @param Style           $style
     */
    public function __construct(AdapterInterface $adapter, $executable, Style $style)
    {
        $this->adapter = $adapter;
        $this->executable = $executable;
        $this->style = $style;
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
            $this->style->getDPI(),
            $this->style->getFont()
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
        $colors = $this->style->getStyle('state', $state->getName());

        $document->addState(
            new Node(
                $state->getName(),
                $state->getFlags(),
                $colors['color'],
                $colors['text'],
                $colors['style'],
                $colors['altColor']
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
        if ($event->getTargetState()) {
            $document->addEdge($this->createEdge($state, $event, $event->getTargetState(), $this->style->getStyle('target', $state->getName(), $event->getName())));
        }

        if ($event->getErrorState()) {
            $document->addEdge($this->createEdge($state, $event, $event->getErrorState(), $this->style->getStyle('error', $state->getName(), $event->getName())));
        }
    }

    /**
     * Create edge for dot notation
     *
     * @param StateInterface $state
     * @param EventInterface $event
     * @param string         $nextState
     * @param array          $style
     *
     * @return Edge
     */
    private function createEdge(StateInterface $state, EventInterface $event, $nextState, $style)
    {
        return new Edge(
            $state->getName(),
            $nextState,
            $event->getName(),
            $style['color'],
            $style['text'],
            $style['style']
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
