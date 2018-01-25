<?php
    namespace sednasoft\virmisco\singiere\error;

    /**
     * Thrown when trying to load an aggregate of a certain class with an existing ID from another incompatible class.
     */
    class AggregateMismatchException extends AbstractRepositoryException
    {
    }
