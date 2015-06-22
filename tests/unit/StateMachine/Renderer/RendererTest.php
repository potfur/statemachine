<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Renderer;


function sys_get_temp_dir() { return './'; }

function tempnam($dir, $prefix = null) { return $dir . $prefix . 'tmp'; }

function exec($cmd, &$output, &$status) { echo $cmd; $output = []; $status = RendererTest::$status; }

class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    public static $status;

    /**
     * @var Document
     */
    private $document;

    public function setUp()
    {
        self::$status = 0;

        $this->document = new Document('Test doc');
    }

    public function tearDown()
    {
        foreach (['./foo.png', './foo.svg', './smrtmp'] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @expectedException \StateMachine\Exception\GraphvizException
     * @expectedExceptionMessage Unable to render graph from dot file
     */
    public function testInvalidExecPath()
    {
        self::$status = 1;

        $renderer = new Renderer('/usr/local/bin/dot');
        $renderer->png($this->document, './foo.png');
    }

    public function testPNG()
    {
        $renderer = new Renderer('/usr/local/bin/dot');
        $renderer->png($this->document, './foo.png');

        $this->expectOutputString('/usr/local/bin/dot -Tpng ./smrtmp -o./foo.png');
    }

    public function testSVG()
    {
        $renderer = new Renderer('/usr/local/bin/dot');
        $renderer->svg($this->document, './foo.svg');

        $this->expectOutputString('/usr/local/bin/dot -Tsvg ./smrtmp -o./foo.svg');
    }
}
