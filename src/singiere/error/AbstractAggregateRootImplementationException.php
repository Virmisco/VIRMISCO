<?php
    namespace sednasoft\virmisco\singiere\error;

    use LogicException;

    /**
     * A base class for exceptions thrown by an aggregate root due to an incomplete or faulty implementation.
     */
    abstract class AbstractAggregateRootImplementationException extends LogicException
    {
    }
