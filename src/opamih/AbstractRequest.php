<?php
    namespace sednasoft\virmisco\opamih;

    use sednasoft\virmisco\oai\pmh\repository\Request as IRequest;
    use Traversable;

    /**
     * A request from a client, received over HTTP with a certain verb parameter that specifies one of six predefined
     * queries.
     */
    abstract class AbstractRequest implements IRequest
    {
        /** @var array */
        private $parameters;
        /** @var */
        private $requestUri;
        /** @var string */
        private $verb;

        /**
         * @param string $requestUri
         * @param string $verb
         * @param array $parameters
         */
        public function __construct($requestUri, $verb, array $parameters)
        {
            $this->parameters = $parameters;
            $this->verb = $verb;
            $this->requestUri = $requestUri;
        }

        /**
         * @return Traversable A traversable yielding key-value-pairs of other parameters (apart from verb).
         */
        public function getParameters()
        {
            foreach ($this->parameters as $key => $value) {
                yield $key => $value;
            }
        }

        /**
         * @return string
         */
        public function getRequestUri()
        {
            return $this->requestUri;
        }

        /**
         * @return string
         */
        public function getVerb()
        {
            return $this->verb;
        }
    }
