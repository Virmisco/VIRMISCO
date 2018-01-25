<?php
    namespace sednasoft\virmisco\singiere\error;

    /**
     * Thrown when trying to append an event to the event store that creates a version conflict due to other events that
     * have been appended in the meantime.
     */
    class ConflictingVersionException extends AbstractEventStoreException
    {
    }
