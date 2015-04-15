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
 * Interface for schema reading adapters
 *
 * @package StateMachine
 */
interface AdapterInterface
{
    /**
     * Return schema (process) name
     *
     * @return string
     */
    public function getSchemaName();

    /**
     * Return fully qualified subjects class
     *
     * @return string
     */
    public function getSubjectClass();

    /**
     * Return initial state name
     *
     * @return string
     */
    public function getInitialState();

    /**
     * Return schema process
     *
     * @return Process
     */
    public function getProcess();
}
