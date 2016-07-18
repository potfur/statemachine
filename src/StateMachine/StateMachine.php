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

namespace StateMachine;

use StateMachine\Payload\PayloadEnvelope;

/**
 * State machine
 *
 * @package StateMachine
 */
class StateMachine
{
    const ON_STATE_WAS_SET = 'onStateWasSet';

    /**
     * Schema adapter
     *
     * @var ProcessInterface
     */
    private $process;

    /**
     * Constructor
     *
     * @param Process $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    /**
     * Trigger event for subject identified by identifier
     * Restore subject from handler by its identifier, then triggers event and saves subject
     * Return run history
     *
     * @param string          $event
     * @param PayloadEnvelope $payload
     */
    public function triggerEvent($event, PayloadEnvelope $payload)
    {
        if ($payload->state() === null) {
            $this->updatePayload($this->process->initialState(), $payload);
        }

        $state = $this->process->state($payload->state());

        $nextStateName = $state->triggerEvent($event, $payload);
        if ($nextStateName === null) {
            return;
        }

        $state = $this->process->state($nextStateName);

        $this->updatePayload($state, $payload);
        $this->handleOnStateWasSet($state, $payload);
    }

    /**
     * Handles onStateWasSet event
     * Returns true if there was state change
     *
     * @param State           $state
     * @param PayloadEnvelope $payload
     */
    private function handleOnStateWasSet(State $state, PayloadEnvelope $payload)
    {
        while ($state->hasEvent(self::ON_STATE_WAS_SET)) {
            $newState = $state->triggerEvent(self::ON_STATE_WAS_SET, $payload);
            if ($newState === null) {
                break;
            }

            $state = $this->process->state($newState);
            $this->updatePayload($state, $payload);
        }
    }

    /**
     * Update payload with new state data
     *
     * @param State           $state
     * @param PayloadEnvelope $payload
     */
    private function updatePayload(State $state, PayloadEnvelope $payload)
    {
        $payload->changeState($state->name());
    }
}
