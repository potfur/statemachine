<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine;

class CommandCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PayloadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $payload;

    /**
     * @var callable|\PHPUnit_Framework_MockObject_MockObject
     */
    private $command;

    /**
     * @var CommandCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collection;

    public function setUp()
    {
        $this->payload = $this->getMock('\StateMachine\PayloadInterface');
        $this->command = $this->getMock('\stdClass', ['__invoke']);
        $this->collection = new CommandCollection([$this->command]);
    }

    public function testExecute()
    {
        $this->command->expects($this->once())->method('__invoke')->with($this->payload);
        $this->collection->execute($this->payload);
    }

    public function testExecuteWithoutCommands()
    {
        $this->assertTrue((new CommandCollection([]))->execute($this->payload));
    }

    /**
     * @dataProvider executionStatusProvider
     */
    public function testExecutionStatus($result)
    {
        $this->command->expects($this->any())->method('__invoke')->willReturn($result);

        $this->assertEquals([null], $this->collection->getExecutionStatus());

        $this->collection->execute($this->payload);
        $this->assertEquals([$result], $this->collection->getExecutionStatus());
    }

    public function executionStatusProvider()
    {
        return [
            [true],
            [false],
        ];
    }

    public function testCount()
    {
        $this->assertEquals(1, $this->collection->count());
    }
}
