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

use StateMachine\Exception\InvalidArgumentException;

/**
 * Flag value object
 *
 * @package StateMachine
 */
class Flag
{
    /**
     * Flag name
     *
     * @var string
     */
    private $name;

    /**
     * Flag value
     *
     * @var mixed
     */
    private $value;

    /**
     * Constructor
     *
     * @param string $name  event name
     * @param mixed  $value flag value
     */
    public function __construct($name, $value)
    {
        $this->assertName($name);

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Assert if name is non empty string
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     */
    private function assertName($name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Invalid flag name, can not be empty string');
        }
    }

    /**
     * Return flag name;
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return flag value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return flag string representation - its name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
