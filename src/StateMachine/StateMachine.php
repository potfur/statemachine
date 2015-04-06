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
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var PayloadHandlerInterface
     */
    private $payloadHandler;

    /**
     * @var TimeoutHandlerInterface
     */
    private $timeoutHandler;

    /**
     * Constructor
     *
     * @param AdapterInterface        $adapter
     * @param PayloadHandlerInterface $payloadHandler
     * @param TimeoutHandlerInterface $timeoutHandler
     */
    public function __construct(AdapterInterface $adapter, PayloadHandlerInterface $payloadHandler, TimeoutHandlerInterface $timeoutHandler)
    {
        $this->adapter = $adapter;
        $this->payloadHandler = $payloadHandler;
        $this->timeoutHandler = $timeoutHandler;
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
        $process = $this->adapter->getProcess();
        $payload = $this->payloadHandler->restore($identifier);

        return $this->resolveEvent($process, $payload, $event);
    }

    /**
     * Resolve timeouts
     * Retrieves all expired timeouts and triggers proper events
     * Return array of run histories
     *
     * @return array
     */
    public function resolveTimeouts()
    {
        $result = [];
        $process = $this->adapter->getProcess();
        $timeouts = $this->timeoutHandler->getExpired();

        foreach ($timeouts as $timeout) {
            $payload = $this->payloadHandler->restore($timeout->getIdentifier());

            if ($payload->getState() !== $timeout->getState()) {
                $this->timeoutHandler->remove($timeout);
                continue;
            }

            $result[$timeout->getIdentifier()] = $this->resolveEvent($process, $payload, $timeout->getEvent());
            $this->timeoutHandler->remove($timeout);
        }

        return $result;
    }

    /**
     * Execute event in set process for set payload
     * If final state has timeout event, store it for further execution
     * Return run history
     *
     * @param ProcessInterface $process
     * @param PayloadInterface $payload
     * @param string           $event
     *
     * @return array
     */
    private function resolveEvent(ProcessInterface $process, PayloadInterface $payload, $event)
    {
        $result = $process->triggerEvent($event, $payload);

        if ($payload->hasChanged() && $process->hasTimeout($payload)) {
            $this->timeoutHandler->store($process->getTimeout($payload, new \DateTime()));
        }

        $this->payloadHandler->store($payload);

        return $result;
    }
}
