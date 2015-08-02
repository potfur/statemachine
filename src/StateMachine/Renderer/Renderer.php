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

use StateMachine\Exception\GraphvizException;

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
        return $this->exec($document, $outputFile, 'png');
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
        return $this->exec($document, $outputFile, 'svg');
    }

    /**
     * Execute system call to Graphviz
     * Return path to output file
     *
     * @param Document $document
     * @param string   $outputFile
     * @param string   $format
     *
     * @return string
     * @throws GraphvizException
     */
    private function exec(Document $document, $outputFile, $format)
    {
        $tempFile = $this->buildDotFile($document);

        $output = [];
        $status = 0;

        exec(
            sprintf('%s -T%s %s -o%s', $this->executable, $format, $tempFile, $outputFile),
            $output,
            $status
        );

        if ($status !== 0) {
            throw new GraphvizException('Unable to render graph from dot file', $status);
        }

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
