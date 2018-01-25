<?php
    namespace sednasoft\virmisco\singiere\error;
    use InvalidArgumentException;

    /**
     * Thrown when the specified string does not follow the UUID syntax.
     */
    class MalformedUuidException extends InvalidArgumentException {
    }
