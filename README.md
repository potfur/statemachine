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
- next step - timeout event

## Sample run

Sample run consists of two files - `schema.php` and `index.php`
Sample process looks like this
 * starts in initial state - `new`
 * from `new` trough `goPending` event to `pending`
 * because `pending` has `onStateWasSet` which is resolved automatically when reaching state go to `done` if commands were executed successfully or to `error` otherwise
 * in `error` it needs to be triggered manually with `returnPending` event
 * this time `pending` is executed successfully and again, because `onStateWasSet` no event triggering is needed, this time moves to `done`

```php
<?php
// index php

namespace {
    require __DIR__ . '/vendor/autoload.php';
}

namespace Fake {
    use StateMachine\Payload\FlagAwareInterface;
    use StateMachine\Payload\Payload;
    use StateMachine\Payload\StateAwareInterface;
    use StateMachine\PayloadHandlerInterface;
    use StateMachine\PayloadInterface;

    final class FakeSubject extends \stdClass implements StateAwareInterface, FlagAwareInterface
    {
        private $identifier;
        private $state;
        private $flags = [];

        public function __construct($identifier)
        {
            $this->identifier = $identifier;
        }

        public function setState($name)
        {
            $this->state = $name;
        }

        public function getState()
        {
            return $this->state;
        }

        public function setFlags(array $flags)
        {
            $this->flags = array_merge($this->flags, $flags);
        }

        public function getFlags()
        {
            return $this->flags;
        }
    }

    final class FakePayloadHandler implements PayloadHandlerInterface
    {
        private $payload = [];

        public function restore($identifier)
        {
            $identifier = (string) $identifier;

            if (!array_key_exists($identifier, $this->payload)) {
                $this->payload[$identifier] = new Payload(new FakeSubject($identifier));
            }

            return $this->payload[$identifier];
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
    $machine->triggerEvent('returnPending', 'FakeIdentifier');
}
```

```php

<?php
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
                    'name' => 'returnPending',
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
