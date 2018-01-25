<?php
    namespace sednasoft\virmisco\opamih;

    use DateTime;
    use DateTimeZone;
    use InvalidArgumentException;
    use sednasoft\virmisco\oai\pmh\repository\Request as IRequest;

    /**
     * Provides access to the request received from the client via the built-in SAPI.
     */
    class RequestGateway
    {
        const ERR_BAD_ARGUMENT = 0x3c713e;
        const ERR_BAD_VERB = 0x11a0d1;
        private $illegalError = 'The request includes illegal arguments';
        private $missingError = 'The %s argument is missing';
        private $requestUri;
        private $syntaxError = 'The value for the %s argument has an illegal syntax';
        private $verb = null;

        /**
         * @return string
         */
        public function getLastValidRequestUri()
        {
            return $this->requestUri;
        }

        /**
         * @return string|null
         */
        public function getLastValidVerb()
        {
            return $this->verb;
        }

        /**
         * @param array $get The original (or a manipulated copy of the) $_GET superglobal.
         * @param array $post The original (or a manipulated copy of the) $_POST superglobal.
         * @param array $server The original (or a manipulated copy of the) $_SERVER superglobal.
         * @return IRequest
         * @throws InvalidArgumentException
         */
        public function loadRequestFromSuperglobals($get, $post, $server)
        {
            $this->requestUri = sprintf(
                '%s://%s%s',
                $server['REQUEST_SCHEME'],
                $server['HTTP_HOST'],
                preg_replace('<\?.*$>', '', $server['REQUEST_URI'])
            );
            $arguments = strtolower($server['REQUEST_METHOD']) === 'post' ? $post : $get;
            if (!isset($arguments['verb'])) {
                throw new InvalidArgumentException(sprintf($this->missingError, 'verb'), self::ERR_BAD_VERB);
            }
            $this->verb = $arguments['verb'];
            unset($arguments['verb']);
            switch ($this->verb) {
                case 'GetRecord':
                    return $this->createGetRecordRequest($this->requestUri, $arguments);
                case 'Identify':
                    return $this->createIdentifyRequest($this->requestUri, $arguments);
                case 'ListIdentifiers':
                    return $this->createListIdentifiersRequest($this->requestUri, $arguments);
                case 'ListMetadataFormats':
                    return $this->createListMetadataFormatsRequest($this->requestUri, $arguments);
                case 'ListRecords':
                    return $this->createListRecordsRequest($this->requestUri, $arguments);
                case 'ListSets':
                    return $this->createListSetsRequest($this->requestUri, $arguments);
                default:
                    $this->verb = null;
                    throw new InvalidArgumentException(
                        'The value of the verb argument is not a legal OAI-PMH verb',
                        self::ERR_BAD_VERB
                    );
            }
        }

        /**
         * @param string $requestUri
         * @param array $arguments
         * @return GetRecordRequest
         * @throws InvalidArgumentException
         */
        private function createGetRecordRequest($requestUri, array $arguments)
        {
            $identifier = null;
            $metadataPrefix = null;
            foreach ($arguments as $key => $value) {
                switch ($key) {
                    case 'identifier':
                        $identifier = $value;
                        break;
                    case 'metadataPrefix':
                        $metadataPrefix = $value;
                        break;
                    default:
                        throw new InvalidArgumentException($this->illegalError, self::ERR_BAD_ARGUMENT);
                }
            }
            if ($identifier === null) {
                // TODO validate identifier against xs:anyUri
                throw new InvalidArgumentException(sprintf($this->missingError, 'identifier'), self::ERR_BAD_ARGUMENT);
            }
            if (!preg_match('<^[A-Za-z0-9\-_.!~*\'()]+$>', $metadataPrefix)) {
                throw new InvalidArgumentException(
                    sprintf(
                        strlen($metadataPrefix) ? $this->syntaxError : $this->missingError,
                        'metadataPrefix'
                    ),
                    self::ERR_BAD_ARGUMENT
                );
            }

            return new GetRecordRequest($requestUri, $identifier, $metadataPrefix);
        }

        /**
         * @param string $requestUri
         * @param array $arguments
         * @return IdentifyRequest
         * @throws InvalidArgumentException
         */
        private function createIdentifyRequest($requestUri, array $arguments)
        {
            foreach ($arguments as $key => $value) {
                throw new InvalidArgumentException($this->illegalError, self::ERR_BAD_ARGUMENT);
            }

            return new IdentifyRequest($requestUri);
        }

        /**
         * @param string $requestUri
         * @param array $arguments
         * @return ListIdentifiersRequest
         * @throws InvalidArgumentException
         */
        private function createListIdentifiersRequest($requestUri, array $arguments)
        {
            list($resumptionToken, $metadataPrefix, $setSpec, $from, $until)
                = $this->processListItemRequestArguments($arguments);

            return new ListIdentifiersRequest($requestUri, $resumptionToken, $metadataPrefix, $setSpec, $from, $until);
        }

        /**
         * @param string $requestUri
         * @param array $arguments
         * @return ListMetadataFormatsRequest
         * @throws InvalidArgumentException
         */
        private function createListMetadataFormatsRequest($requestUri, array $arguments)
        {
            $identifier = null;
            foreach ($arguments as $key => $value) {
                switch ($key) {
                    case 'identifier':
                        $identifier = $value;
                        break;
                    default:
                        throw new InvalidArgumentException($this->illegalError, self::ERR_BAD_ARGUMENT);
                }
            }

            // TODO validate identifier against xs:anyUri

            return new ListMetadataFormatsRequest($requestUri, $identifier);
        }

        /**
         * @param string $requestUri
         * @param array $arguments
         * @return ListRecordsRequest
         * @throws InvalidArgumentException
         */
        private function createListRecordsRequest($requestUri, array $arguments)
        {
            list($resumptionToken, $metadataPrefix, $setSpec, $from, $until)
                = $this->processListItemRequestArguments($arguments);

            return new ListRecordsRequest($requestUri, $resumptionToken, $metadataPrefix, $setSpec, $from, $until);
        }

        /**
         * @param string $requestUri
         * @param array $arguments
         * @return ListSetsRequest
         * @throws InvalidArgumentException
         */
        private function createListSetsRequest($requestUri, array $arguments)
        {
            $resumptionToken = null;
            foreach ($arguments as $key => $value) {
                switch ($key) {
                    case 'resumptionToken':
                        $resumptionToken = $value;
                        break;
                    default:
                        throw new InvalidArgumentException($this->illegalError, self::ERR_BAD_ARGUMENT);
                }
            }

            return new ListSetsRequest($requestUri, $resumptionToken);
        }

        /**
         * @param array $arguments
         * @return array
         * @throws InvalidArgumentException
         */
        private function processListItemRequestArguments(array $arguments)
        {
            $resumptionToken = null;
            $metadataPrefix = null;
            $setSpec = null;
            $from = null;
            $until = null;
            foreach ($arguments as $key => $value) {
                switch ($key) {
                    case 'resumptionToken':
                        $resumptionToken = $value;
                        break;
                    case 'metadataPrefix':
                        $metadataPrefix = $value;
                        break;
                    case 'set':
                        $setSpec = $value;
                        break;
                    case 'from':
                        $from = $value;
                        break;
                    case 'until':
                        $until = $value;
                        break;
                    default:
                        throw new InvalidArgumentException($this->illegalError, self::ERR_BAD_ARGUMENT);
                }
            }
            if ($resumptionToken !== null && count($arguments) > 1) {
                throw new InvalidArgumentException($this->illegalError, self::ERR_BAD_ARGUMENT);
            }
            if ($resumptionToken === null && $metadataPrefix === null) {
                throw new InvalidArgumentException(
                    sprintf($this->missingError, 'metadataPrefix'),
                    self::ERR_BAD_ARGUMENT
                );
            }
            if ($from !== null && !preg_match('<^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}:\d{2}Z)?$>', $from)) {
                throw new InvalidArgumentException(sprintf($this->syntaxError, 'from'), self::ERR_BAD_ARGUMENT);
            }
            if ($until !== null && !preg_match('<^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}:\d{2}Z)?$>', $until)) {
                throw new InvalidArgumentException(sprintf($this->syntaxError, 'until'), self::ERR_BAD_ARGUMENT);
            }
            if ($setSpec !== null && !preg_match('<^[0-9a-z\-_.!~*\'()]+(:[0-9a-z\-_.!~*\'()]+)*$>', $setSpec)) {
                throw new InvalidArgumentException(sprintf($this->syntaxError, 'set'), self::ERR_BAD_ARGUMENT);
            }
            if ($from && strlen($from) === 10) {
                $from .= 'T00:00:00Z';
            }
            if ($until && strlen($until) === 10) {
                $until .= 'T23:59:59Z';
            }
            $from = $from ? new DateTime($from, new DateTimeZone('UTC')) : null;
            $until = $until ? new DateTime($until, new DateTimeZone('UTC')) : null;
            if ($from && $until && $from->getTimestamp() > $until->getTimestamp()) {
                throw new InvalidArgumentException(
                    'The from argument must be less than or equal to the until argument.',
                    self::ERR_BAD_ARGUMENT
                );
            }

            return [$resumptionToken, $metadataPrefix, $setSpec, $from, $until];
        }
    }
