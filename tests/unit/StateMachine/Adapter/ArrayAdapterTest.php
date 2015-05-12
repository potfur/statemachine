<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Adapter;


use StateMachine\CommandCollection;
use StateMachine\Event;
use StateMachine\Flag;
use StateMachine\Process;
use StateMachine\State;
use StateMachine\Timeout;

class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSchemaName()
    {
        $schema = ['name' => 'schemaName'];

        $adapter = new ArrayAdapter($schema);
        $this->assertEquals('schemaName', $adapter->getSchemaName());
    }

    public function testSubjectClass()
    {
        $schema = ['subjectClass' => '\stdClass'];

        $adapter = new ArrayAdapter($schema);
        $this->assertEquals('\stdClass', $adapter->getSubjectClass());
    }

    public function testInitialState()
    {
        $schema = ['initialState' => 'new'];

        $adapter = new ArrayAdapter($schema);
        $this->assertEquals('new', $adapter->getInitialState());
    }

    public function testGetProcess()
    {
        $command = function () {
            // NOP
        };

        $schema = [
            'name' => 'testSchema',
            'subjectClass' => '\stdClass',
            'initialState' => 'testState',
            'states' => [
                [
                    'name' => 'testState',
                    'comment' => 'comment',
                    'flags' => [
                        'hasFlag' => true
                    ],
                    'events' => [
                        [
                            'name' => 'eventA',
                            'comment' => 'comment',
                            'targetState' => 'pending',
                            'errorState' => 'error',
                            'commands' => [
                                $command
                            ]
                        ],
                        [
                            'name' => 'eventB',
                            'comment' => 'comment',
                            'targetState' => 'pending',
                            'errorState' => 'error',
                            'timeout' => new \DateInterval('PT10S'),
                        ]
                    ],
                ]
            ]
        ];

        $expected = new Process(
            'testSchema',
            '\stdClass',
            'testState',
            [
                new State(
                    'testState',
                    [
                        new Event(
                            'eventA',
                            'pending',
                            'error',
                            new CommandCollection([$command]),
                            null,
                            'comment'
                        ),
                        new Event(
                            'eventB',
                            'pending',
                            'error',
                            new CommandCollection(),
                            new Timeout(new \DateInterval('PT10S')),
                            'comment'
                        )
                    ],
                    [
                        new Flag('hasFlag', true)
                    ],
                    'comment'
                )
            ]
        );

        $adapter = new ArrayAdapter($schema);
        $this->assertEquals($expected, $adapter->getProcess());
    }
}
