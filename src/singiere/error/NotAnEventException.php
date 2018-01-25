<?php
    namespace sednasoft\virmisco\singiere\error;

    use Exception;

    /**
     * Thrown when an event is expected but something else is provided. This can happen especially with generic
     * iterators representing an event stream.
     */
    class NotAnEventException extends Exception
    {
    }
