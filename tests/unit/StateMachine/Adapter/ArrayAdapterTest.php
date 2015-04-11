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
        $command = function () { };

        $schema = [
            'name' => 'testSchema',
            'subjectClass' => '\stdClass',
            'initialState' => 'testState',
            'states' => [
                [
                    'name' => 'testState',
                    'flags' => [
                        'hasFlag' => true
                    ],
                    'events' => [
                        [
                            'name' => 'eventName',
                            'targetState' => 'pending',
                            'errorState' => 'error',
                            'commands' => [
                                $command
                            ]
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
                            'eventName',
                            'pending',
                            'error',
                            new CommandCollection([$command])
                        )
                    ],
                    [new Flag('hasFlag', true)]
                )
            ]
        );

        $adapter = new ArrayAdapter($schema);
        $this->assertEquals($expected, $adapter->getProcess());
    }
}
