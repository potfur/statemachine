<?php

declare(strict_types = 1);

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Payload;

/**
 * Payload envelope, used to transport subject trough state machine process
 *
 * @package StateMachine
 */
final class PayloadEnvelope
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
     * Current runs history
     *
     * @var array
     */
    private $history = [];

    /**
     * Actual context instance
     *
     * @var mixed
     */
    private $subject;

    private function __construct($subject)
    {
        $this->subject = $subject;

        if ($this->isSubjectStateful()) {
            $this->state = $this->subject->state();
        }
    }

    public static function wrap($subject): PayloadEnvelope
    {
        return new static($subject);
    }

    /**
     * Return true if state has changed (even if it was changed back to itself)
     *
     * @return bool
     */
    public function hasChanged(): bool
    {
        return $this->hasChanged;
    }

    /**
     * Return current subject state
     *
     * @return null|string
     */
    public function state()
    {
        return $this->state;
    }

    /**
     * Set new state to subject
     *
     * @param string $name state name
     */
    public function changeState($name)
    {
        $this->state = $name;
        $this->history[] = $name;
        $this->hasChanged = true;

        if ($this->isSubjectStateful()) {
            $this->subject->changeState($this->state);
        }
    }

    /**
     * Return array with all states that were set in this run
     *
     * @return array
     */
    public function history(): array
    {
        return $this->history;
    }

    /**
     * Return subject
     *
     * @return mixed
     */
    public function subject()
    {
        return $this->subject;
    }

    /**
     * Return true if subject implements Stateful interface
     *
     * @return bool
     */
    private function isSubjectStateful()
    {
        return $this->subject instanceof Stateful;
    }
}
