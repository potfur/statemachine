<?php

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Factory;

use StateMachine\Exception\InvalidArgumentException;
use StateMachine\StateMachine;

/**
 * Factory class for easy handling of multiple state machines
 *
 * @package StateMachine
 */
class Factory
{
    /**
     * Definitions for building state machines
     *
     * @var callable[]
     */
    private $definitions = [];

    /**
     * Resolved state machine instances
     *
     * @var StateMachine[]
     */
    private $instances = [];

    /**
     * Register new state machine
     *
     * @param string   $name       state machine name - identifier
     * @param callable $definition callable that builds state machine instance
     */
    public function register($name, callable $definition)
    {
        $this->definitions[$name] = $definition;
    }

    /**
     * Check if state machine with given name was registered
     * Return true if definition exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->definitions);
    }

    /**
     * Get state machine with given name
     *
     * @param string $name
     *
     * @return StateMachine
     * @throws InvalidArgumentException
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException(sprintf('State machine with name "%s" was not defined', $name));
        }

        if (!array_key_exists($name, $this->instances)) {
            $this->instances[$name] = $this->definitions[$name]();
        }

        return $this->instances[$name];
    }

    /**
     * Trigger event from schema for context with given identifier
     * Return array with run history
     *
     * @param string $schema
     * @param string $event
     * @param mixed  $identifier
     *
     * @return array
     */
    public function triggerEvent($schema, $event, $identifier)
    {
        return $this->get($schema)->triggerEvent($event, $identifier);
    }

    /**
     * Resolve timeout events for schemas
     * If list of schemas is empty - all registered schemas are handled
     * Return associative array of results
     *
     * @param array $schemas
     *
     * @return array
     */
    public function resolveTimeouts($schemas = [])
    {
        $schemas = (array) $schemas;

        if (empty($schemas)) {
            $schemas = array_keys($this->definitions);
        }

        $result = [];
        foreach ($schemas as $name) {
            $result[$name] = $this->get($name)->resolveTimeouts();
        }

        return $result;
    }
}
