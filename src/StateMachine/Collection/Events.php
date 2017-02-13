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


use StateMachine\Event;

final class Events extends Collection
{
    /**
     * Constructor
     *
     * @param Event[] $collection collection elements
     */
    public function __construct(array $collection = [])
    {
        foreach ($collection as $element) {
            $this->add($element);
        }
    }

    /**
     * Add event to collection
     *
     * @param Event $event
     */
    public function add(Event $event)
    {
        $this->collection[(string) $event] = $event;
    }
}
