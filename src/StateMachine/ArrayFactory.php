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

/**
 * Adapter for array schemas
 *
 * @package StateMachine
 */
final class ArrayFactory
{
    /**
     * Schema array
     *
     * @var array
     */
    private $schema;

    /**
     * Resolved process instance
     *
     * @var Process
     */
    private $process;

    /**
     * Construct
     *
     * @param array $schema
     */
    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Return schema (process) name
     *
     * @return string
     */
    public function getSchemaName(): string
    {
        return $this->getOffsetFromArray($this->schema, 'name');
    }

    /**
     * Return initial state name
     *
     * @return string
     */
    public function getInitialState(): string
    {
        return $this->getOffsetFromArray($this->schema, 'initialState');
    }

    /**
     * Build states for process
     *
     * @return array
     */
    private function buildStates(): array
    {
        $states = [];
        foreach ($this->getOffsetFromArray($this->schema, 'states', []) as $state) {
            $states[] = new State(
                $this->getOffsetFromArray($state, 'name'),
                $this->buildEvents($state),
                $this->getAdditionalFromArray($state, ['events', 'name'])
            );
        }

        return $states;
    }

    /**
     * Build state events
     *
     * @param array $state
     *
     * @return Event[]
     */
    private function buildEvents(array $state): array
    {
        $events = [];
        foreach ($this->getOffsetFromArray($state, 'events', []) as $event) {
            $events[] = new Event(
                $this->getOffsetFromArray($event, 'name'),
                $this->getOffsetFromArray($event, 'targetState'),
                $this->getOffsetFromArray($event, 'errorState'),
                $this->getOffsetFromArray($event, 'command'),
                $this->getAdditionalFromArray($event, ['name', 'command', 'targetState', 'errorState'])
            );
        }

        return $events;
    }

    /**
     * Return schema process
     *
     * @return Process
     */
    public function getProcess(): Process
    {
        if ($this->process === null) {
            $this->process = new Process(
                $this->getSchemaName(),
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

    /**
     * Return array elements not matching keys
     *
     * @param array $array
     * @param array $ignoredKeys
     *
     * @return array
     */
    private function getAdditionalFromArray(array $array, array $ignoredKeys): array
    {
        return array_intersect_key($array, array_diff_key($array, array_flip($ignoredKeys)));
    }
}
