<?php
    namespace sednasoft\virmisco\opamih;

    use DateTimeInterface;
    use DOMElement;
    use RuntimeException;
    use sednasoft\virmisco\oai\pmh\repository\Request as IRequest;
    use sednasoft\virmisco\oai\pmh\repository\response\Identify as IIdentify;

    /**
     * A successful response to an Identify request.
     */
    class IdentifyResponse extends AbstractResponse implements IIdentify
    {
        private $baseURL;
        private $deletedRecord;
        private $earliestDatestamp;
        private $initialAdmin;
        private $firstDescription;
        private $granularity;
        private $protocolVersion;
        private $repositoryName;

        /**
         * @param IRequest $request
         * @param string $dateGranularity One of the Response::HG_* constants.
         */
        public function __construct(IRequest $request, $dateGranularity)
        {
            parent::__construct($request, 'Identify', $dateGranularity);
            $container = $this->getContainer();
            $this->repositoryName = $container
                ->appendChild($this->createElement('repositoryName'))
                ->appendChild($this->createTextNode('(unspecified)'));
            $this->baseURL = $container
                ->appendChild($this->createElement('baseURL'))
                ->appendChild($this->createTextNode('http://example.com/unspecified/'));
            $this->protocolVersion = $container
                ->appendChild($this->createElement('protocolVersion'))
                ->appendChild($this->createTextNode('2.0'));
            $this->initialAdmin = $container
                ->appendChild($this->createElement('adminEmail'))
                ->appendChild($this->createTextNode('unspecified@example.com'));
            $this->earliestDatestamp = $container
                ->appendChild($this->createElement('earliestDatestamp'))
                ->appendChild($this->createTextNode('1970-01-01T00:00:00Z'));
            $this->deletedRecord = $container
                ->appendChild($this->createElement('deletedRecord'))
                ->appendChild($this->createTextNode(self::SL_TRANSIENT));
            $this->granularity = $container
                ->appendChild($this->createElement('granularity'))
                ->appendChild($this->createTextNode($dateGranularity));
        }

        /**
         * @param string $emailAddress The e-mail address of an administrator of the repository. At least one must be
         * specified.
         */
        public function addAdminEmail($emailAddress)
        {
            if ($this->initialAdmin) {
                $this->initialAdmin->parentNode->parentNode->removeChild($this->initialAdmin->parentNode);
                $this->initialAdmin = null;
            }
            $this->getContainer()
                ->insertBefore($this->createElement('adminEmail'), $this->earliestDatestamp->parentNode)
                ->appendChild($this->createTextNode($emailAddress));
        }

        /**
         * @param string $contentCoding A compression encoding supported by the repository (one of the CC_* constants).
         */
        public function addCompression($contentCoding)
        {
            $this->getContainer()
                ->insertBefore($this->createElement('compression'), $this->firstDescription)
                ->appendChild($this->createTextNode($contentCoding));
        }

        /**
         * @param DOMElement $rootElement An extensible mechanism for communities to describe their
         * repositories.
         */
        public function addDescription(DOMElement $rootElement)
        {
            $description = $this->getContainer()
                ->appendChild($this->createElement('description'))
                ->appendChild($rootElement);
            $this->firstDescription = $this->firstDescription ?: $description;
        }

        /**
         * @param string $url The base URL of the repository used for all requests. The base URL specifies the Internet
         * host and port, and optionally a path, of an HTTP server acting as a repository.
         */
        public function setBaseUrl($url)
        {
            $this->baseURL->nodeValue = $url;
        }

        /**
         * @param string $supportLevel The manner in which the repository supports the notion of deleted records as one
         * of the SL_* constants.
         */
        public function setDeletedRecord($supportLevel)
        {
            $this->deletedRecord->nodeValue = $supportLevel;
        }

        /**
         * @param DateTimeInterface $datestamp A UTCdatetime that is the guaranteed lower limit of all datestamps
         * recording changes, modifications, or deletions in the repository. A repository must not use datestamps lower
         * than the one specified here.
         */
        public function setEarliestDatestamp(DateTimeInterface $datestamp)
        {
            $this->earliestDatestamp->nodeValue = substr(
                $datestamp->format(self::ISO8601UTC),
                0,
                strlen($this->granularity->nodeValue)
            );
        }

        /**
         * @param string $harvestingGranularity The finest harvesting granularity supported by the repository as one of
         * the HG_* constants.
         * @throws RuntimeException
         */
        public function setGranularity($harvestingGranularity)
        {
            throw new RuntimeException(
                'Invocation of setGranularity() forbidden, the value cannot be changed and is provided/used globally in'
                . ' many places'
            );
        }

        /**
         * @param string $version The version of the OAI-PMH supported by the repository.
         */
        public function setProtocolVersion($version)
        {
            $this->protocolVersion->nodeValue = $version;
        }

        /**
         * @param string $name A human readable name for the repository.
         */
        public function setRepositoryName($name)
        {
            $this->repositoryName->nodeValue = $name;
        }
    }
