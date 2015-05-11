<?php

/*
* This file is part of the StateMachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Adapter;

use StateMachine\AdapterInterface;
use StateMachine\CommandCollection;
use StateMachine\Event;
use StateMachine\EventInterface;
use StateMachine\Flag;
use StateMachine\Process;
use StateMachine\State;
use StateMachine\TimeoutConverterInterface;

/**
 * Adapter for array schemas
 *
 * @package StateMachine
 */
class ArrayAdapter implements AdapterInterface
{
    /**
     * Schema array
     *
     * @var array
     */
    private $schema;

    /**
     * @var TimeoutConverterInterface
     */
    private $converter;

    /**
     * Resolved process instance
     *
     * @var Process
     */
    private $process;

    /**
     * Construct
     *
     * @param array                     $schema
     * @param TimeoutConverterInterface $converter
     */
    public function __construct(array $schema, TimeoutConverterInterface $converter)
    {
        $this->schema = $schema;
        $this->converter = $converter;
    }

    /**
     * Return schema (process) name
     *
     * @return string
     */
    public function getSchemaName()
    {
        return $this->getOffsetFromArray($this->schema, 'name');
    }

    /**
     * Return fully qualified subjects class
     *
     * @return string
     */
    public function getSubjectClass()
    {
        return $this->getOffsetFromArray($this->schema, 'subjectClass');
    }

    /**
     * Return initial state name
     *
     * @return string
     */
    public function getInitialState()
    {
        return $this->getOffsetFromArray($this->schema, 'initialState');
    }

    /**
     * Build states for process
     *
     * @return array
     */
    private function buildStates()
    {
        $states = [];
        foreach ($this->getOffsetFromArray($this->schema, 'states', []) as $state) {
            $states[] = new State(
                $this->getOffsetFromArray($state, 'name'),
                $this->buildEvents($state),
                $this->buildFlags($state),
                $this->getOffsetFromArray($state, 'comment')
            );
        }

        return $states;
    }

    /**
     * Build state flags
     *
     * @param array $state
     *
     * @return Flag[]
     */
    private function buildFlags($state)
    {
        $flags = [];
        foreach ($this->getOffsetFromArray($state, 'flags', []) as $name => $value) {
            $flags[] = new Flag($name, $value);
        }

        return $flags;
    }

    /**
     * Build state events
     *
     * @param array $state
     *
     * @return EventInterface[]
     */
    private function buildEvents($state)
    {
        $events = [];
        foreach ($this->getOffsetFromArray($state, 'events', []) as $event) {
            $events[] = new Event(
                $this->getOffsetFromArray($event, 'name'),
                $this->getOffsetFromArray($event, 'targetState'),
                $this->getOffsetFromArray($event, 'errorState'),
                $this->buildCommands($event),
                $this->buildTimeout($this->getOffsetFromArray($event, 'timeout')),
                $this->getOffsetFromArray($event, 'comment')
            );
        }

        return $events;
    }

    /**
     * Build timeout
     *
     * @param mixed $timeout
     *
     * @return \DateInterval|\DateTime|null
     */
    private function buildTimeout($timeout)
    {
        if ($timeout === null) {
            return null;
        }

        return $this->converter->convert($timeout);
    }

    /**
     * Build event command collection
     *
     * @param array $event
     *
     * @return CommandCollection
     */
    private function buildCommands($event)
    {
        $collection = new CommandCollection();
        foreach ($this->getOffsetFromArray($event, 'commands', []) as $command) {
            $collection->add($command);
        }

        $collection->resetStatus();

        return $collection;
    }

    /**
     * Return schema process
     *
     * @return Process
     */
    public function getProcess()
    {
        if ($this->process === null) {
            $this->process = new Process(
                $this->getSchemaName(),
                $this->getSubjectClass(),
                $this->getInitialState(),
                $this->buildStates()
            );
        }

        return $this->process;
    }

    /**
     * Returns array element matching key
     *
     * @param array  $array
     * @param string $offset
     * @param mixed  $default
     *
     * @return mixed
     */
    private function getOffsetFromArray(array $array, $offset, $default = null)
    {
        return array_key_exists($offset, $array) ? $array[$offset] : $default;
    }
}
