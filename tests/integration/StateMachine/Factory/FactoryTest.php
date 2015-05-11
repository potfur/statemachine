<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Factory;


use Fake\FakeLockHandler;
use Fake\FakePayloadHandler;
use Fake\FakeTimeoutHandler;
use StateMachine\Adapter\ArrayAdapter;
use StateMachine\Adapter\TimeoutConverter;
use StateMachine\Process;
use StateMachine\StateMachine;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TimeoutConverter
     */
    private $converter;

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

    /**
     * @var callable
     */
    private $definition;

    public function setUp()
    {
        $schema = [
            'name' => 'testSchema',
            'subjectClass' => '\stdClass',
            'initialState' => 'new',
            'states' => [
                'new' => [
                    'name' => 'new',
                    'events' => [
                        [
                            'name' => 'do',
                            'targetState' => 'pending',
                        ]
                    ],
                ],
                'pending' => [
                    'name' => 'pending',
                    'events' => [
                        [
                            'name' => Process::ON_TIME_OUT,
                            'targetState' => 'done',
                            'timeout' => new \DateInterval('PT1S'),
                        ]
                    ]
                ],
                'done' => [
                    'name' => 'done',
                    'events' => [],
                ]
            ]
        ];

        $this->converter = new TimeoutConverter();
        $this->adapter = new ArrayAdapter($schema, $this->converter);
        $this->payloadHandler = new FakePayloadHandler();
        $this->timeoutHandler = new FakeTimeoutHandler();
        $this->lockHandler = new FakeLockHandler();

        $this->definition = function () {
            return new StateMachine($this->adapter, $this->payloadHandler, $this->timeoutHandler, $this->lockHandler);
        };
    }

    public function testTriggerEvent()
    {
        $factory = new Factory();
        $factory->register('testSchema', $this->definition);

        $result = $factory->triggerEvent('testSchema', 'do', 1);

        $this->assertEquals(['new', 'pending'], $result);
    }

    public function testResolveTimeoutsForAllMachines()
    {
        $factory = new Factory();
        $factory->register('testSchema', $this->definition);
        $factory->triggerEvent('testSchema', 'do', 1);

        usleep(2000000); // 2s
        $result = $factory->resolveTimeouts();

        $this->assertEquals(['testSchema' => [1 => ['new', 'pending', 'done']]], $result);
    }
}
