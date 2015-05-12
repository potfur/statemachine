<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine;


use Fake\FakeLockHandler;
use Fake\FakePayloadHandler;
use Fake\FakeTimeoutHandler;
use StateMachine\Adapter\ArrayAdapter;

class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayAdapter
     */
    private $adapter;

    /**
     * @var FakePayloadHandler
     */
    private $payloadHandler;

    /**
     * @var FakeTimeoutHandler
     */
    private $timeoutHandler;

    /**
     * @var FakeLockHandler
     */
    private $lockHandler;


    private $commandResults = [
        true, // from new to pending
        false, // from pending to error (onStateWasSet)
        true, // from error to pending (onTimeOut)
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
            'subjectClass' => '\stdClass',
            'initialState' => 'new',
            'states' => [
                'new' => [
                    'name' => 'new',
                    'flags' => [
                        'wasNew' => true
                    ],
                    'events' => [
                        [
                            'name' => 'goPending',
                            'targetState' => 'pending',
                            'errorState' => 'error',
                            'commands' => [$command]
                        ]
                    ],
                ],
                'pending' => [
                    'name' => 'pending',
                    'flags' => [
                        'wasPending' => true
                    ],
                    'events' => [
                        [
                            'name' => Process::ON_STATE_WAS_SET,
                            'targetState' => 'done',
                            'errorState' => 'error',
                            'commands' => [$command]
                        ]
                    ],
                ],
                'error' => [
                    'name' => 'error',
                    'flags' => [
                        'hadError' => true
                    ],
                    'events' => [
                        [
                            'name' => Process::ON_TIME_OUT,
                            'targetState' => 'pending',
                            'errorState' => 'error',
                            'timeout' => new \DateInterval('PT1S'),
                            'commands' => [$command]
                        ]
                    ],
                ],
                'done' => [
                    'name' => 'done',
                    'flags' => [
                        'isDone' => true
                    ],
                    'events' => [],
                ]
            ]
        ];

        $this->adapter = new ArrayAdapter($schema);
        $this->payloadHandler = new FakePayloadHandler();
        $this->timeoutHandler = new FakeTimeoutHandler();
        $this->lockHandler = new FakeLockHandler();
    }

    public function tearDown()
    {

    }

    public function testTriggerEvent()
    {
        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);

        $this->assertFalse($this->payloadHandler->wasStored());
        $this->assertFalse($this->lockHandler->isLocked('FakeIdentifier'));
        $this->assertFalse($this->lockHandler->wasLocked('FakeIdentifier'));

        $history = $machine->triggerEvent('goPending', 'FakeIdentifier');

        $this->assertEquals(['new', 'pending', 'error'], $history);

        $this->assertTrue($this->payloadHandler->wasStored());
        $this->assertFalse($this->lockHandler->isLocked('FakeIdentifier'));
        $this->assertTrue($this->lockHandler->wasLocked('FakeIdentifier'));
    }

    public function testResolveTimeouts()
    {
        $machine = new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);

        $this->assertFalse($this->payloadHandler->wasStored());
        $this->assertFalse($this->lockHandler->isLocked('FakeIdentifier'));
        $this->assertFalse($this->lockHandler->wasLocked('FakeIdentifier'));

        $machine->triggerEvent('goPending', 'FakeIdentifier');

        usleep(800000); // 0.8s
        $history = $machine->resolveTimeouts(); // no expires, nothing to do
        $this->assertEquals([], $history);

        usleep(1500000); // 1.5s
        $history = $machine->resolveTimeouts(); // now expired
        $this->assertEquals(['FakeIdentifier' => ['new', 'pending', 'error', 'pending', 'done']], $history);

        $this->assertTrue($this->payloadHandler->wasStored());
        $this->assertFalse($this->lockHandler->isLocked('FakeIdentifier'));
        $this->assertTrue($this->lockHandler->wasLocked('FakeIdentifier'));
    }
}
