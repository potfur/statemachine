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

use StateMachine\Flag;
use StateMachine\PayloadInterface;

/**
 * Payload wrapper, used to transport subject trough state machine process
 *
 * @package StateMachine
 */
final class Payload implements PayloadInterface
{
    /**
     * Flag if context changed
     *
     * @var bool
     */
    private $hasChanged = false;

    /**
     * Current contexts state
     *
     * @var string
     */
    private $state;

    /**
     * Contexts flags
     *
     * @var Flag[]
     */
    private $flags = [];

    /**
     * Current runs history
     *
     * @var array
     */
    private $history = [];

    /**
     * Context identifier
     *
     * @var mixed
     */
    private $identifier;

    /**
     * Actual context instance
     *
     * @var mixed|StateAwareInterface|FlagAwareInterface
     */
    private $subject;

    /**
     * Constructor
     * Creates payload around subject
     *
     * @param mixed $identifier
     * @param mixed $subject
     */
    public function __construct($identifier, $subject)
    {
        $this->identifier = $identifier;
        $this->subject = $subject;

        if (!is_object($subject)) {
            return;
        }

        if ($this->isStateAware()) {
            $this->state = $this->subject->getState();
        }

        if ($this->isFlagAware()) {
            $this->flags = $this->convertArrayToFlags((array) $this->subject->getFlags());
        }
    }

    /**
     * Convert array of key-value pairs into flag instances
     *
     * @param array $flags
     *
     * @return Flag[]
     */
    private function convertArrayToFlags(array $flags)
    {
        $result = [];
        foreach ($flags as $name => $value) {
            $result[$name] = new Flag($name, $value);
        }

        return $result;
    }

    /**
     * Return true if subject implements StateAwareInterface
     *
     * @return bool
     */
    private function isStateAware()
    {
        return is_object($this->subject) && $this->subject instanceof StateAwareInterface;
    }

    /**
     * Return true if subject implements FlagAwareInterface
     *
     * @return bool
     */
    private function isFlagAware()
    {
        return is_object($this->subject) && $this->subject instanceof FlagAwareInterface;
    }

    /**
     * Return subject identifier
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Return true if state has changed (even if it was changed back to itself)
     *
     * @return bool
     */
    public function hasChanged()
    {
        return $this->hasChanged;
    }

    /**
     * Return current subject state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set new state to subject
     *
     * @param string $name state name
     */
    public function setState($name)
    {
        $this->state = $name;
        $this->history[] = $name;
        $this->hasChanged = true;

        if ($this->isStateAware()) {
            $this->subject->setState($this->state);
        }
    }

    /**
     * Return flag value or null if flag not set
     *
     * @param string $name requested flag
     *
     * @return mixed
     */
    public function getFlag($name)
    {
        return array_key_exists($name, $this->flags) ? $this->flags[$name] : null;
    }

    /**
     * Adds flag with value to payload
     *
     * @param Flag $flag
     */
    public function setFlag(Flag $flag)
    {
        $this->flags[$flag->getName()] = $flag;

        if ($this->isFlagAware()) {
            $this->subject->setFlags($this->convertFlagsToArray($this->flags));
        }
    }

    /**
     * Convert array of Flag instances to key-value pairs
     *
     * @param Flag[] $flags
     *
     * @return array
     */
    private function convertFlagsToArray(array $flags)
    {
        $result = [];
        foreach ($flags as $flag) {
            $result[$flag->getName()] = $flag->getValue();
        }

        return $result;
    }

    /**
     * Return array with all states that were set in this run
     *
     * @return array
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Return subject
     *
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set subject
     *
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
}
