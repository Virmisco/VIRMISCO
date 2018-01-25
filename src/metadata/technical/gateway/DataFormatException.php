<?php
    namespace sednasoft\virmisco\metadata\technical\gateway;

    use Exception;
    use RuntimeException;

    /**
     * Exception thrown when an invalid, unsupported or unexpected value was detected that does not match the format
     * defined for the field.
     */
    class DataFormatException extends RuntimeException
    {
        /**
         * @param string $filePath The file containing the unexpected value.
         * @param string $fieldName The field holding the unexpected value.
         * @param mixed $fieldValue The value causing the problem.
         * @param string $message [optional]
         * @param int $code [optional] The Exception code.
         * @param Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
         */
        public function __construct(
            $filePath,
            $fieldName,
            $fieldValue,
            $message = null,
            $code = 0,
            Exception $previous = null
        ) {
            parent::__construct(
                sprintf(
                    'Unexpected value %s for field %s in file %s%s',
                    json_encode($fieldValue, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    $fieldName,
                    $filePath,
                    $message ? ': ' . $message : ''
                ),
                $code,
                $previous
            );
        }
    }
