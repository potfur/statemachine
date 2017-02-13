<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace unit\StateMachine\Adapter;


use StateMachine\ArrayFactory;
use StateMachine\Event;
use StateMachine\Process;
use StateMachine\State;

class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSchemaName()
    {
        $schema = ['name' => 'schemaName'];

        $adapter = new ArrayFactory($schema);
        $this->assertEquals('schemaName', $adapter->getSchemaName());
    }

    public function testInitialState()
    {
        $schema = ['initialState' => 'new'];

        $adapter = new ArrayFactory($schema);
        $this->assertEquals('new', $adapter->getInitialState());
    }

    public function testGetProcess()
    {
        $command = function () {
            // NOP
        };

        $schema = [
            'name' => 'testSchema',
            'initialState' => 'testState',
            'states' => [
                [
                    'name' => 'testState',
                    'events' => [
                        [
                            'name' => 'eventA',
                            'targetState' => 'pending',
                            'errorState' => 'error',
                            'command' => $command
                        ],
                        [
                            'name' => 'eventB',
                            'targetState' => 'pending',
                            'errorState' => 'error',
                        ]
                    ],
                ]
            ]
        ];

        $expected = new Process(
            'testSchema',
            'testState',
            [
                new State(
                    'testState',
                    [
                        new Event(
                            'eventA',
                            'pending',
                            'error',
                            $command
                        ),
                        new Event(
                            'eventB',
                            'pending',
                            'error'
                        )
                    ]
                )
            ]
        );

        $adapter = new ArrayFactory($schema);
        $this->assertEquals($expected, $adapter->getProcess());
    }
}
