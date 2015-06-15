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
     * Path to graphviz executable
     *
     * @var string
     */
    private $executable;

    /**
     * @param string $executable path to dot executable
     */
    public function __construct($executable)
    {
        $this->executable = $executable;
    }

    /**
     * Render process in dot document as png
     * Return path to output file
     * If output file exists, will be overwritten
     *
     * @param Document $document
     * @param string   $outputFile
     *
     * @return string
     */
    public function png(Document $document, $outputFile)
    {
        $tempFile = $this->buildDotFile($document);

        shell_exec(sprintf('%s -Tpng %s -o%s', $this->executable, $tempFile, $outputFile));

        return $outputFile;
    }

    /**
     * Render process as svg
     * Return path to output file
     * If output file exists, will be overwritten
     *
     * @param Document $document
     * @param string   $outputFile
     *
     * @return string
     */
    public function svg(Document $document, $outputFile)
    {
        $tempFile = $this->buildDotFile($document);

        shell_exec(sprintf('%s -Tsvg %s -o%s', $this->executable, $tempFile, $outputFile));

        return $outputFile;
    }

    /**
     * Create file with dot schema
     *
     * @param Document $document
     *
     * @return string
     */
    private function buildDotFile(Document $document)
    {
        $tmpname = tempnam(sys_get_temp_dir(), 'smr');
        file_put_contents($tmpname, (string) $document);

        return $tmpname;
    }
}
