<?php
    namespace sednasoft\virmisco\singiere\error;

    /**
     * Thrown when an aggregate lacks a protected method to apply a certain event. The method must be named "apply"
     * followed by the unqualified class name of the event class.
     */
    class MissingApplyMethodForEventException extends AbstractAggregateRootImplementationException
    {
    }
