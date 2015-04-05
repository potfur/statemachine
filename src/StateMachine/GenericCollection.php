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
use StateMachine\Exception\OutOfRangeException;

/**
 * Class Collection
 * Used for internal representations
 *
 * @package StateMachine
 */
final class GenericCollection implements \Countable
{
    private $collection = array();
    private $instanceOf;

    /**
     * Constructor
     * Creates collection that can be limited to certain instances
     *
     * @param array       $collection      collection elements
     * @param null|string $onlyInstancesOf fully qualified interface/class that limits collection elements
     */
    public function __construct(array $collection = [], $onlyInstancesOf = null)
    {
        $this->instanceOf = $onlyInstancesOf;
        foreach ($collection as $element) {
            $this->set($element);
        }
    }

    /**
     * Asserts if element if of expected instance
     *
     * @param mixed $element
     *
     * @throws InvalidArgumentException
     */
    private function assertInstanceOf($element)
    {
        $instanceOf = $this->instanceOf;
        if ($instanceOf !== null && !$element instanceof $instanceOf) {
            throw new InvalidArgumentException(sprintf('Element in collection must be instance of "%s", got "%s"', $instanceOf, $this->getType($element)));
        }
    }

    /**
     * Return value type
     *
     * @param mixed $value
     *
     * @return string
     */
    private function getType($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

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
            throw new OutOfRangeException(sprintf('Element for offset "%s" not found', $offset));
        }

        return $this->collection[$offset];
    }

    /**
     * Set value to collection
     *
     * @param mixed $element
     */
    public function set($element)
    {
        $this->assertInstanceOf($element);
        $this->collection[(string) $element] = $element;
    }

    /**
     * Check if there is element for offset
     *
     * @param string $offset
     *
     * @return bool
     */
    public function has($offset)
    {
        return array_key_exists($offset, $this->collection);
    }

    /**
     * Return all elements in collection
     *
     * @return array
     */
    public function all()
    {
        return $this->collection;
    }

    /**
     * Count elements of an object
     *
     * @return int
     */
    public function count()
    {
        return count($this->collection);
    }
}
