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

namespace StateMachine\Collection;

/**
 * Abstract generic Collection
 * Used for internal representations
 *
 * @package StateMachine
 */
abstract class Collection implements \Countable
{
    /**
     * Collection elements
     *
     * @var array
     */
    protected $collection = array();

    /**
     * Return value for given offset
     *
     * @param string $offset
     *
     * @return mixed
     * @throws OutOfRangeException
     */
    public function get($offset)
    {
        if (!$this->has($offset)) {
            throw OutOfRangeException::offsetNotFound($offset);
        }

        return $this->collection[$offset];
    }

    /**
     * Check if there is element for offset
     *
     * @param string $offset
     *
     * @return bool
     */
    public function has($offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    /**
     * Return all elements in collection
     *
     * @return array
     */
    public function all(): array
    {
        return $this->collection;
    }

    /**
     * Count elements of an object
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->collection);
    }
}
