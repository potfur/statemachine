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
final class PayloadEnvelope implements Payload
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
    }

    /**
     * @param mixed $subject
     *
     * @return PayloadEnvelope
     */
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
}
