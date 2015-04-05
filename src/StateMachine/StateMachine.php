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

/**
 * State machine
 *
 * @package StateMachine
 */
class StateMachine
{
    private $adapter;
    private $payloadHandler;

    /**
     * Constructor
     *
     * @param AdapterInterface        $adapter
     * @param PayloadHandlerInterface $payloadHandler
     */
    public function __construct(AdapterInterface $adapter, PayloadHandlerInterface $payloadHandler)
    {
        $this->adapter = $adapter;
        $this->payloadHandler = $payloadHandler;
    }

    /**
     * Trigger event for subject identified by identifier
     * Restore subject from handler by its identifier, then triggers event and saves subject
     * Return run history
     *
     * @param string $event
     * @param mixed  $identifier
     *
     * @return array
     */
    public function triggerEvent($event, $identifier)
    {
        $payload = $this->payloadHandler->restore($identifier);
        $result = $this->adapter->getProcess()->triggerEvent($event, $payload);
        $this->payloadHandler->store($payload);

        return $result;
    }
}
