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
    private $dpi;
    private $font;
    private $colors = [
        'state' => ['text' => '#444444', 'color' => '#ebebeb', 'flag' => '#0066aa'],
        'target' => ['text' => '#999999', 'color' => '#99BB11'],
        'error' => ['text' => '#999999', 'color' => '#ee1155']
    ];

    /**
     * @var AdapterInterface
     */
    private $adapter;

    private $executable;

    /**
     * @param AdapterInterface $adapter
     * @param string           $executable path to dot executable
     * @param int              $dpi        resolution
     * @param string           $font       font used for labels
     * @param array            $colors     colors used for states and edges
     */
    public function __construct(AdapterInterface $adapter, $executable, $dpi = 75, $font = 'Courier', $colors = [])
    {
        $this->adapter = $adapter;
        $this->executable = $executable;

        $this->dpi = (int) $dpi;
        $this->font = $font;

        $this->setColors($colors);
    }

    /**
     * Set colors used for states and edges
     * Passed array is merged with array just like that from getColors
     *
     * @param array $colors
     */
    public function setColors(array $colors)
    {
        foreach (array_keys($this->colors) as $node) {
            if (isset($colors[$node])) {
                $this->colors[$node] = array_merge($this->colors[$node], $colors[$node]);
            }
        }
    }

    /**
     * Return array with colors used for states and edges
     *
     * @return array
     */
    public function getColors()
    {
        return $this->colors;
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
            $this->dpi,
            $this->font
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
                $this->colors['state']['color'],
                $this->colors['state']['text'],
                $this->colors['state']['flag']
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
            $document->addEdge(new Edge($state->getName(), $event->getTargetState(), $event->getName(), $this->colors['target']['color'], $this->colors['target']['text']));
        }

        if ($event->getErrorState()) {
            $document->addEdge(new Edge($state->getName(), $event->getErrorState(), $event->getName(), $this->colors['error']['color'], $this->colors['error']['text']));
        }
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
