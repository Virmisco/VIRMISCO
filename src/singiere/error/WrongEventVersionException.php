<?php
    namespace sednasoft\virmisco\singiere\error;

    /**
     * Thrown when attempting to apply an event whose version is not one more than the current version of the aggregate.
     * This usually indicates that an aggregate created multiple events using nextVersion() without applying them
     * immediately.
     */
    class WrongEventVersionException extends AbstractAggregateRootImplementationException
    {
    }
