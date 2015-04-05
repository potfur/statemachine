# StateMachine

StateMachine is an implementation of **state pattern**.
This means that we have **context** for which we can define **process** described by **states** with specific behaviour.
**Context** also called **subject** can be anything, order & payment processing, newsletter opt-in process, customer registration - anything not trivial.

Pros:
 + improves decoupling (also decoupling from framework)
 + **state** specific behaviour is described by simple **commands** and not as monolithic classes,
 + **process** and **states** can be easily modified,
 + **commands** can be shared between **states**, **processes** and **contexts**

Cons:
 - scatters behaviour to other objects that are not necessary connected to **context**
 - can lead to code duplication
 - **context** can end in undefined (eg. removed) **state**

_Look into dev branch_
