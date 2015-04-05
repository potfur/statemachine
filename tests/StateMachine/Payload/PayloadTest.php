<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Payload;


class PayloadTest extends \PHPUnit_Framework_TestCase
{
    public function testHasChangedBecauseOfStatusChange()
    {
        $payload = new Payload(new \stdClass());
        $this->assertFalse($payload->hasChanged());

        $payload->setState('foo');
        $this->assertTrue($payload->hasChanged());
    }

    public function testHasNotChangedBecauseOfFlagChange()
    {
        $payload = new Payload(new \stdClass());
        $this->assertFalse($payload->hasChanged());

        $flag = $this->getMockBuilder('\StateMachine\Flag')->disableOriginalConstructor()->getMock();

        $payload->setFlag($flag);
        $this->assertFalse($payload->hasChanged());
    }

    public function testDidNotReadStateOrFlagsFromScalarSubject()
    {
        new Payload([]);
    }

    public function testStateWasReadFromStateAwareSubject()
    {
        $subject = $this->getMock('\StateMachine\Payload\StateAwareInterface');
        $subject->expects($this->once())->method('getState');

        new Payload($subject);
    }

    public function testFlagsFromFlagAwareSubjectWereConvertedBackToVO()
    {
        $subject = $this->getMock('\StateMachine\Payload\FlagAwareInterface');
        $subject->expects($this->once())->method('getFlags')->willReturn(['flagName' => 'flagValue']);

        $payload = new Payload($subject);
        $flag = $payload->getFlag('flagName');

        $this->assertInstanceOf('\StateMachine\Flag', $flag);
        $this->assertEquals('flagName', $flag->getName());
        $this->assertEquals('flagValue', $flag->getValue());
    }

    public function testFlagsWereReadFromFlagAwareSubject()
    {
        $subject = $this->getMock('\StateMachine\Payload\FlagAwareInterface');
        $subject->expects($this->once())->method('getFlags');

        new Payload($subject);
    }

    public function testSetStateWithStateAwareSubject()
    {
        $subject = $this->getMock('\StateMachine\Payload\StateAwareInterface');
        $subject->expects($this->once())->method('setState')->with('stateName');

        $payload = new Payload($subject);
        $payload->setState('stateName');
    }

    public function testSetStateWithoutStateAwareSubject()
    {
        $subject = $this->getMock('\stdClass', ['setState']);
        $subject->expects($this->never())->method('setState');

        $payload = new Payload($subject);
        $payload->setState('stateName');
    }

    public function testGetState()
    {
        $subject = $this->getMock('\stdClass');

        $payload = new Payload($subject);
        $payload->setState('stateName');

        $this->assertEquals('stateName', $payload->getState());
    }

    public function testSetFlagWithFlagAwareSubject()
    {
        $flag = $this->getMockBuilder('\StateMachine\Flag')->disableOriginalConstructor()->getMock();
        $flag->expects($this->any())->method('getName')->willReturn('flagName');
        $flag->expects($this->any())->method('getValue')->willReturn('flagValue');

        $subject = $this->getMock('\StateMachine\Payload\FlagAwareInterface');
        $subject->expects($this->once())->method('setFlags')->with(['flagName' => 'flagValue']);

        $payload = new Payload($subject);
        $payload->setFlag($flag);
    }

    public function testSetFlagWithoutFlagAwareSubject()
    {
        $flag = $this->getMockBuilder('\StateMachine\Flag')->disableOriginalConstructor()->getMock();

        $subject = $this->getMock('\stdClass', ['setState']);
        $subject->expects($this->never())->method('setFlags');

        $payload = new Payload($subject);
        $payload->setFlag($flag);
    }

    public function testGetFlags()
    {
        $flag = $this->getMockBuilder('\StateMachine\Flag')->disableOriginalConstructor()->getMock();
        $flag->expects($this->any())->method('getName')->willReturn('flagName');

        $subject = $this->getMock('\stdClass');

        $payload = new Payload($subject);
        $payload->setFlag($flag);

        $this->assertEquals($flag, $payload->getFlag('flagName'));
    }

    public function testGetHistory()
    {
        $subject = $this->getMock('\stdClass');

        $payload = new Payload($subject);
        $payload->setState('firstState');
        $payload->setState('secondState');
        $payload->setState('thirdState');

        $this->assertEquals(['firstState', 'secondState', 'thirdState'], $payload->getHistory());
    }

    public function testSubject()
    {
        $subjectA = $this->getMock('\stdClass');
        $subjectB = $this->getMock('\stdClass');

        $payload = new Payload($subjectA);
        $this->assertSame($subjectA, $payload->getSubject());

        $payload->setSubject($subjectB);
        $this->assertSame($subjectB, $payload->getSubject());
    }
}
