<?php
    namespace sednasoft\virmisco\opamih;

    use sednasoft\virmisco\oai\pmh\repository\response\Error as IError;
    use Traversable;

    /**
     * An error response to any request.
     */
    class ErrorResponse extends AbstractResponse implements IError
    {
        /**
         * @param string $requestUri
         * @param string|null $verb
         * @param Traversable|array $parameters
         */
        public function __construct($requestUri, $verb, $parameters = [])
        {
            $this->initialize($requestUri, $verb, $parameters, 'error');
        }

        /**
         * @param string $code One of the ERR_* constants.
         * @param string $message The error message meant to be understood by a human.
         */
        public function setError($code, $message)
        {
            $this->getContainer()->setAttribute('code', $code);
            $this->getContainer()->appendChild($this->createTextNode($message));
        }
    }
