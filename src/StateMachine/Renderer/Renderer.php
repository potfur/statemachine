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
use StateMachine\Event;
use StateMachine\State;

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
        $process = new ProcessDot(
            $this->adapter->getProcess()->getName(),
            $this->dpi,
            $this->font
        );

        foreach ($this->adapter->getProcess()->getStates() as $state) {
            $process->addState($this->createState($state));

            foreach ($state->getEvents() as $event) {
                if ($event->getTargetState()) {
                    $process->addEdge($this->createEdge($state, $event, 'target'));
                }

                if ($event->getErrorState()) {
                    $process->addEdge($this->createEdge($state, $event, 'error'));
                }
            }
        }

        file_put_contents($outputFile, (string) $process);

        return $outputFile;
    }

    /**
     * Create state for dot notation
     *
     * @param State $state
     *
     * @return StateDot
     */
    private function createState(State $state)
    {
        return new StateDot(
            $state->getName(),
            $state->getFlags(),
            $this->colors['state']['color'],
            $this->colors['state']['text'],
            $this->colors['state']['flag']
        );
    }

    /**
     * Create edge for dot notation
     *
     * @param State  $state
     * @param Event  $event
     * @param string $type
     *
     * @return EdgeDot
     */
    private function createEdge(State $state, Event $event, $type)
    {
        return new EdgeDot(
            $state->getName(),
            $type === 'target' ? $event->getTargetState() : $event->getErrorState(),
            $event->getName(),
            $this->colors[$type]['color'],
            $this->colors[$type]['text']
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
