<?php

namespace StateMachine;

use StateMachine\Exception\InvalidArgumentException;
use StateMachine\Exception\InvalidStateException;
use StateMachine\Exception\InvalidSubjectException;

/**
 * State machine process
 *
 * @package StateMachine
 */
final class Process implements ProcessInterface
{
    use GetTypeTrait;

    const ON_STATE_WAS_SET = 'onStateWasSet';
    const ON_TIME_OUT = 'onTimeOut';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $subjectClass;

    /**
     * @var string
     */
    private $initialState;

    /**
     * @var GenericCollection|StateInterface[]
     */
    private $states = [];

    /**
     * @param string           $name
     * @param string           $subjectClass
     * @param string           $initialState
     * @param StateInterface[] $states
     *
     * @throws InvalidStateException
     */
    public function __construct($name, $subjectClass, $initialState, array $states)
    {
        $this->assertName($name);

        $this->name = $name;
        $this->subjectClass = $subjectClass;
        $this->initialState = $initialState;

        $this->states = new GenericCollection($states, '\StateMachine\StateInterface');

        if (!$this->hasState($this->initialState)) {
            throw new InvalidStateException(sprintf('Initial state "%s" does not exist in process "%s"', $this->initialState, $this->getName()));
        }
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
            throw new InvalidArgumentException('Invalid process name, can not be empty string');
        }
    }

    /**
     * Return process name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return entity class
     * Fully qualified subject class for which process is defined
     *
     * @return string
     */
    public function getSubjectClass()
    {
        return $this->subjectClass;
    }

    /**
     * Return initial state
     * All entities without state will have this one
     *
     * @return string
     */
    public function getInitialStateName()
    {
        return $this->initialState;
    }

    /**
     * Return all states
     *
     * @return StateInterface[]
     */
    public function getStates()
    {
        return $this->states->all();
    }

    /**
     * Return true if event exists in collection
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasState($name)
    {
        return $this->states->has($name);
    }

    /**
     * Return event with given name
     *
     * @param string $name
     *
     * @return StateInterface
     */
    public function getState($name)
    {
        return $this->states->get($name);
    }

    /**
     * Trigger event for payload
     * Return array with all transitional state names
     *
     * @param string           $event
     * @param PayloadInterface $payload
     *
     * @return array
     */
    public function triggerEvent($event, PayloadInterface $payload)
    {
        $this->assertPayloadSubject($payload);

        if ($payload->getState() === null) {
            $payload->setState($this->getInitialStateName());
        }

        $state = $this->getState($payload->getState());

        $result = $state->triggerEvent($event, $payload);
        if ($result === null) {
            return $payload->getHistory();
        }

        $state = $this->getState($result);

        $this->updatePayload($state, $payload);
        $this->handleOnStateWasSet($state, $payload);

        return $payload->getHistory();
    }

    /**
     * Assert if payload subject is instance of expected class
     *
     * @param PayloadInterface $payload
     *
     * @throws InvalidSubjectException
     */
    private function assertPayloadSubject(PayloadInterface $payload)
    {
        $subject = $payload->getSubject();
        $class = $this->subjectClass;

        if (!$subject instanceof $class) {
            throw new InvalidSubjectException(sprintf('Unable to trigger with invalid payload in process "%s" - got "%s", expected "%s"', $this->getName(), $this->getType($subject), $class));
        }
    }

    /**
     * Handles onStateWasSet event
     * Returns true if there was state change
     *
     * @param StateInterface   $state
     * @param PayloadInterface $payload
     *
     * @return null|StateInterface
     */
    private function handleOnStateWasSet(StateInterface $state, PayloadInterface $payload)
    {
        while ($state->hasEvent(self::ON_STATE_WAS_SET)) {
            $newState = $state->triggerEvent(self::ON_STATE_WAS_SET, $payload);
            if ($newState === null) {
                break;
            }

            $state = $this->getState($newState);
            $this->updatePayload($state, $payload);
        }
    }

    /**
     * Update payload with new state data
     *
     * @param StateInterface   $state
     * @param PayloadInterface $payload
     */
    private function updatePayload(StateInterface $state, PayloadInterface $payload)
    {
        $payload->setState($state->getName());
        foreach ((array) $state->getFlags() as $flag) {
            $payload->setFlag($flag);
        }
    }
}
