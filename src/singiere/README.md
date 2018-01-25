# Singiere

A library that serves as the core for CQRS/ES implementations, named after the reversed German word *Ereignis* (English:
event). The recommended pronunciation is ```/sɪŋˈjɛɚ/``` (like *SING-YEAH(R)* with a French touch).

It comprises a small number of interfaces, abstract classes and a few simple implementations. Here is an overview over
its basic ideas, which are however not carved into stone and may be altered by implementations:

Aggregate roots have public methods following the ubiquitous language that do not perform any changes but create events,
apply them and yield them to the caller. In order to apply these events as well as those replayed an event stream the
aggregate root must provide protected methods in the form ```applyClassName```, where ```ClassName``` is the unqualified
name of the event they handle by accept it as their only and mandatory argument.

The default way of handling commands is a chain of responsibility comprising multiple dispatchers that collect the
events yielded from the aggregate root, appending them to an event store and then yielding them up to the caller that
generated the command and dispatched it to the chain.
