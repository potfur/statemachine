<?php

declare(strict_types = 1);

/*
* This file is part of the statemachine package
*
* (c) Michal Wachowski <wachowski.michal@gmail.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace StateMachine\Collection;


use StateMachine\State;

final class States extends Collection
{
    /**
     * Constructor
     *
     * @param State[] $collection collection elements
     */
    public function __construct(array $collection = [])
    {
        foreach ($collection as $element) {
            $this->add($element);
        }
    }

    /**
     * Add state to collection
     *
     * @param State $state
     */
    public function add(State $state)
    {
        $this->collection[(string) $state] = $state;
    }
}
