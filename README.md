# StateMachine

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/potfur/statemachine/badges/quality-score.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/?branch=dev)
[![Code Coverage](https://scrutinizer-ci.com/g/potfur/statemachine/badges/coverage.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/?branch=dev)
[![Build Status](https://scrutinizer-ci.com/g/potfur/statemachine/badges/build.png?b=dev)](https://scrutinizer-ci.com/g/potfur/statemachine/build-status/dev)
[![License](https://poser.pugx.org/potfur/statemachine/license.svg)](https://packagist.org/packages/potfur/statemachine)

StateMachine is an implementation of _non-deterministic finite automata_ (this can be also subsumed under _state pattern_ implementation).
In different words - machine will move _context_ from one state to another, depending on result of executed _commands_.

StateMachine can be used to describe order & payment processing, newsletter opt-in process, customer registration - anything not trivial.
In such case order becomes _context_, _states_ represent all stages of order processing and _commands_ will execute all those things like generating invoices, sending mails etc.

Documentation is available in [wiki](https://github.com/potfur/statemachine/wiki).

Pros:
 + improves decoupling (also decoupling from framework)
 + _state_ specific behaviour is described by simple _commands_ and not as monolithic classes,
 + _process_ and _states_ can be easily modified,
 + _commands_ can be shared between _states_, _processes_ and _contexts_

Cons:
 - scatters behaviour to other objects that are not necessary connected to _context_
 - can lead to code duplication
 - _context_ can end in undefined (eg. removed) _state_

## Next steps
 - factory for different schemas - **done**
 - comments for states & events - **done**
 - more timeout formats: date, interval string, seconds **done**
 - Renderer: onTimeOut must have its timeout displayed **done**
 - Renderer: onStateWasSet & onTimeOut should be distinguished in style - **done**
 
![](https://github.com/potfur/statemachine/wiki/schema.png)
