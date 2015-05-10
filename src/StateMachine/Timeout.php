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
 * TimeOut value object
 *
 * @package StateMachine
 */
class Timeout
{
    /**
     * State name where timeout occurred
     *
     * @var string
     */
    private $state;

    /**
     * Event name, usually onTimeOut
     *
     * @var string
     */
    private $event;

    /**
     * Context identifier
     *
     * @var mixed
     */
    private $identifier;

    /**
     * Date when timeout should be executed
     *
     * @var \DateTime
     */
    private $execution;

    /**
     * Constructor
     *
     * @param string    $state      state name
     * @param string    $event      event name
     * @param mixed     $identifier context identifier
     * @param \DateTime $execution  execution date
     *
     * @throws InvalidArgumentException
     */
    public function __construct($state, $event, $identifier, \DateTime $execution)
    {
        $this->assertName($state);
        $this->assertName($event);

        $this->state = $state;
        $this->event = $event;
        $this->identifier = $identifier;
        $this->execution = $execution;
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
            throw new InvalidArgumentException('Invalid state or event name in timeout, can not be empty string');
        }
    }

    /**
     * Return state name
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Return event name
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Return identifier
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Return execution time
     *
     * @return \DateTime
     */
    public function getExecutionDate()
    {
        return $this->execution;
    }
}
