<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace unit\StateMachine;


use StateMachine\Timeout;

class TimeoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider formatProvider
     */
    public function testFormat($value, $expected)
    {
        $timeout = new Timeout($value);
        $this->assertEquals($expected, $timeout->getTimeout());
    }

    public function formatProvider()
    {
        return [
            [new \DateInterval('PT10S'), new \DateInterval('PT10S')],
            [new \DateTime('2015-05-11 10:10:10'), new \DateTime('2015-05-11 10:10:10')],
            [10, new \DateInterval('PT10S')],
            ['PT10S', new \DateInterval('PT10S')],
            ['midnight +2 days', new \DateTime('midnight +2 days')],
            ['2015-05-11 10:10:10', new \DateTime('2015-05-11 10:10:10')],
        ];
    }

    /**
     * @dataProvider stringProvider
     */
    public function testString($value, $expected)
    {
        $timeout = new Timeout($value);
        $this->assertEquals($expected, (string) $timeout);
    }

    public function stringProvider()
    {
        return [
            ['PT10S', 'PT10S'],
            ['P2D', 'P2D'],
            ['P2DT10S', 'P2DT10S'],
            ['2015-05-11 10:10:10', (new \DateTime('2015-05-11 10:10:10'))->format('c')],
        ];
    }

    /**
     * @dataProvider timeoutProvider
     */
    public function testTimeoutAt($value, $expected)
    {
        $now = new \DateTime('2015-03-31 10:10:10');

        $timeout = new Timeout($value);
        $this->assertEquals($expected, $timeout->timeoutAt($now));
    }

    public function timeoutProvider()
    {
        return [
            [new \DateTime('2015-04-01 10:10:10'), new \DateTime('2015-04-01 10:10:10')],
            [new \DateInterval('PT60S'), new \DateTime('2015-03-31 10:11:10')],
        ];
    }
}
