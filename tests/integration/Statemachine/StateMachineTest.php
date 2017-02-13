<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace integration\Statemachine;


use StateMachine\ArrayFactory;
use StateMachine\PayloadEnvelope;
use StateMachine\StateMachine;

class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayFactory
     */
    private $adapter;

    private $commandResults = [
        true, // from new to pending
        false, // from pending to error (onStateWasSet)
        true, // from error to pending (onStateWasSet)
        true, // from pending to done (onStateWasSet)
    ];

    public function setUp()
    {
        $command = function () {
            if (!count($this->commandResults)) {
                throw new \InvalidArgumentException('Out of results');
            }

            return array_shift($this->commandResults);
        };

        $schema = [
            'name' => 'testSchema',
            'initialState' => 'new',
            'states' => [
                [
                    'name' => 'new',
                    'events' => [
                        [
                            'name' => 'goPending',
                            'targetState' => 'pending',
                            'errorState' => 'error',
                            'command' => $command
                        ]
                    ],
                ],
                [
                    'name' => 'pending',
                    'events' => [
                        [
                            'name' => StateMachine::ON_STATE_WAS_SET,
                            'targetState' => 'done',
                            'errorState' => 'error',
                            'command' => $command
                        ]
                    ],
                ],
                [
                    'name' => 'error',
                    'events' => [
                        [
                            'name' => StateMachine::ON_STATE_WAS_SET,
                            'targetState' => 'pending',
                            'errorState' => 'error',
                            'command' => $command
                        ]
                    ],
                ],
                [
                    'name' => 'done',
                    'events' => [],
                ]
            ]
        ];

        $this->adapter = new ArrayFactory($schema);
    }

    public function testTriggerEvent()
    {
        $payload = PayloadEnvelope::wrap('payload');

        $machine = new StateMachine($this->adapter->getProcess());
        $machine->triggerEvent('goPending', $payload);

        $this->assertEquals(['new', 'pending', 'error', 'pending', 'done'], $payload->history());
    }
}
