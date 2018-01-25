<?php
    namespace sednasoft\virmisco\singiere\error;

    /**
     * Thrown when trying to create a new aggregate with the ID of an existing one.
     */
    class AggregateExistsException extends AbstractRepositoryException
    {
    }
