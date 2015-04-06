# StateMachine

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/potfur/statemachine/badges/quality-score.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/?branch=dev)
[![Code Coverage](https://scrutinizer-ci.com/g/potfur/statemachine/badges/coverage.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/?branch=dev)
[![Build Status](https://scrutinizer-ci.com/g/potfur/statemachine/badges/build.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/build-status/dev)
[![License](https://poser.pugx.org/potfur/statemachine/license.svg)](https://packagist.org/packages/potfur/statemachine)

StateMachine is an implementation of _non-deterministic finite automata_ (this can be also subsumed under _state pattern_ implementation).
In different words - machine will move _context_ from one state to another, depending on result of executed _commands_.
StateMachine can be used to describe order & payment processing, newsletter opt-in process, customer registration - anything not trivial.
In such case order becomes _context_, _states_ represent all stages of order processing and _commands_ will execute all those things like generating invoices, sending mails etc.

Pros:
 + improves decoupling (also decoupling from framework)
 + _state_ specific behaviour is described by simple _commands_ and not as monolithic classes,
 + _process_ and _states_ can be easily modified,
 + _commands_ can be shared between _states_, _processes_ and _contexts_

Cons:
 - scatters behaviour to other objects that are not necessary connected to _context_
 - can lead to code duplication
 - _context_ can end in undefined (eg. removed) _state_

**Work in progress**

## Sample run

Sample run consists of two files - `schema.php` and `index.php`

```php
<?php
// schema.php

$commandResult = [
    true,
    false,
    true,
    true,
    true,
];

$command = function (\StateMachine\Payload\Payload $payload) use (&$commandResult) {

    $history = $payload->getHistory();
    $subject = $payload->getSubject();
    $subject->process[] = implode(array_slice($history, -2, 1)) . ' -> ' . implode(array_splice($history, -1, 1));
    $payload->setSubject($subject);

    return array_shift($commandResult);
};

return [
    'name' => 'testSchema',
    'subjectClass' => '\stdClass',
    'initialState' => 'new',
    'states' => [
        'new' => [
            'name' => 'new',
            'flags' => [
                'wasNew' => true
            ],
            'events' => [
                [
                    'name' => 'goPending',
                    'targetState' => 'pending',
                    'errorState' => 'error',
                    'commands' => [$command]
                ]
            ],
        ],
        'pending' => [
            'name' => 'pending',
            'flags' => [
                'wasPending' => true
            ],
            'events' => [
                [
                    'name' => \StateMachine\Process::ON_STATE_WAS_SET,
                    'targetState' => 'done',
                    'errorState' => 'error',
                    'commands' => [$command]
                ]
            ],
        ],
        'error' => [
            'name' => 'error',
            'flags' => [
                'hadError' => true
            ],
            'events' => [
                [
                    'name' => \StateMachine\Process::ON_STATE_WAS_SET,
                    'targetState' => 'pending',
                    'errorState' => 'error',
                    'commands' => [$command]
                ]
            ],
        ],
        'done' => [
            'name' => 'done',
            'flags' => [
                'isDone' => true
            ],
            'events' => [],
        ]
    ]
];
```

```php
<?php
// index.php

namespace {
    require __DIR__ . '/vendor/autoload.php';
}

namespace Fake {
    use StateMachine\Payload\Payload;
    use StateMachine\PayloadHandlerInterface;
    use StateMachine\PayloadInterface;

    class FakePayloadHandler implements PayloadHandlerInterface
    {
        public function restore($identifier)
        {
            return $payload = new Payload((object) ['identifier' => $identifier, 'process' => []]);
        }

        public function store(PayloadInterface $payload)
        {
            var_dump($payload);
        }
    }
}

namespace Test {
    use Fake\FakePayloadHandler;
    use StateMachine\Adapter\ArrayAdapter;
    use StateMachine\StateMachine;

    $adapter = new ArrayAdapter(require __DIR__ . '/schema.php');
    $handler = new FakePayloadHandler();

    $machine = new StateMachine($adapter, $handler);
    $machine->triggerEvent('goPending', 'FakeIdentifier');
}
```
