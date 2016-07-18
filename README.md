StateMachine
============

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/potfur/statemachine/badges/quality-score.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/?branch=dev)
[![Code Coverage](https://scrutinizer-ci.com/g/potfur/statemachine/badges/coverage.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/?branch=dev)
[![Build Status](https://scrutinizer-ci.com/g/potfur/statemachine/badges/build.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/build-status/dev)
[![License](https://poser.pugx.org/potfur/statemachine/license.svg)](https://packagist.org/packages/potfur/statemachine)
[![Latest Stable Version](https://poser.pugx.org/potfur/statemachine/v/stable.svg)](https://packagist.org/packages/potfur/statemachine) 

StateMachine is an implementation of _state pattern_, also can be treated as _non-deterministic finite automata_.
In different words - machine will react to events and move _conext_ from one state to another depending on result of executed _command_.

StateMachine can be used to describe order & payment processing, newsletter opt-in process, customer registration - any not trivial process.

## Processes


StateMachine follows process defined by _states_ taken by _context_ and _events_ that activate transitions between them.

Each of _events_ can define two transitions _successful_ and _erroneous_. Also, each _event_ can have a callable command that will be executed before transition. Value returned by it, will decide which transition will be chosen.

When _command_ returns `truthy` value - StateMachine will follow _successful_ transitions, any `falsy` value will be _erroneous_ transition.
If _event_ had no _command_ - StateMachine will follow _successfull_ transition.

Each _state_ can have special event `StateMachine::ON_STATE_WAS_SET` which will be triggered as soon as _context_ enters state.


## Schema

Processes are constructed from schemas.
StateMachine comes with `ArrayFactory` which creates `Process` instances from plain arrays.

```php
$commandResults = [
    true, // from new to pending
    false, // from pending to error (onStateWasSet)
    true, // from error to pending (onStateWasSet)
    true, // from pending to done (onStateWasSet)
];

$command = function () use (&$commandResults) {
    if (!count($commandResults)) {
        throw new \InvalidArgumentException('Out of results');
    }

    return array_shift($commandResults);
};

$schema = [
    'name' => 'testSchema',
    'initialState' => 'new',
    'states' => [
        [
            'name' => 'new',
            'events' => [
                [
                    'name' => 'goPending',
                    'targetState' => 'pending',
                    'errorState' => 'error',
                    'command' => $command,
                ]
            ],
        ],
        [
            'name' => 'pending',
            'events' => [
                [
                    'name' => StateMachine::ON_STATE_WAS_SET,
                    'targetState' => 'done',
                    'errorState' => 'error',
                    'command' => $command
                ]
            ],
        ],
        [
            'name' => 'error',
            'events' => [
                [
                    'name' => StateMachine::ON_STATE_WAS_SET,
                    'targetState' => 'pending',
                    'errorState' => 'error',
                    'command' => $command
                ]
            ],
        ],
        [
            'name' => 'done',
            'events' => [],
        ]
    ]
];

$process = (new ArrayFactory($schema))->getProcess();
```

## Payload and triggering events

To transit from one state to another an event needs to be trigered.
When it happens StateMachine will pass provided _context_ wrapped as a payload to _command_, execute it and follow resulting transitions.

```php
$payload = PayloadEnvelope::wrap('context');

$machine = new StateMachine($process);
$history = $machine->triggerEvent('goPending', $payload);
```

`PayloadEnvelope` is an implementation of `Payload` - simple wrapper that holds _context_. It can be anything, simple value, array, object etc.
`::triggerEvent` method returns history of transitions, ie. list of all states that _context_ passed.
