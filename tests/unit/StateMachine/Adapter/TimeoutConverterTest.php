<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace unit\StateMachine\Adapter;


use StateMachine\Adapter\TimeoutConverter;

class TimeoutConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider timeoutProvider
     */
    public function testConvert($value, $expected)
    {
        $converter = new TimeoutConverter();
        $this->assertEquals($expected, $converter->convert($value));
    }

    public function timeoutProvider()
    {
        $fnc = function () {
            return 'PT10S';
        };

        return [
            [new \DateInterval('PT10S'), new \DateInterval('PT10S')],
            [new \DateTime('2015-05-11 10:10:10'), new \DateTime('2015-05-11 10:10:10')],
            [10, new \DateInterval('PT10S')],
            ['PT10S', new \DateInterval('PT10S')],
            ['midnight +2 days', new \DateTime('midnight +2 days')],
            ['2015-05-11 10:10:10', new \DateTime('2015-05-11 10:10:10')],
            [$fnc, new \DateInterval('PT10S')]
        ];
    }
}
