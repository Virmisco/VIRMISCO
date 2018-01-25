<?php
    namespace sednasoft\virmisco\oai\pmh\repository\response;

    use DateTimeInterface;
    use DOMElement;
    use sednasoft\virmisco\oai\pmh\repository\Response;

    /**
     * A successful response to an Identify request.
     */
    interface Identify extends Response
    {
        /** The encoding format produced by the common UNIX file compression program "compress". */
        const CC_COMPRESS = 'compress';
        /** The "zlib" format (RFC 1950) in combination with the "deflate" compression mechanism (RFC 1951). */
        const CC_DEFLATE = 'deflate';
        /** An encoding format produced by the file compression program "gzip" (GNU zip) as described in RFC 1952. */
        const CC_GZIP = 'gzip';
        /** The repository does not maintain information about deletions. */
        const SL_NO = 'no';
        /** The repository maintains information about deletions with no time limit. */
        const SL_PERSISTENT = 'persistent';
        /** The repository does not guarantee that a list of deletions is maintained persistently or consistently. */
        const SL_TRANSIENT = 'transient';

        /**
         * @param string $emailAddress The e-mail address of an administrator of the repository. At least one must be
         * specified.
         */
        public function addAdminEmail($emailAddress);

        /**
         * @param string $contentCoding A compression encoding supported by the repository (one of the CC_* constants).
         */
        public function addCompression($contentCoding);

        /**
         * @param DOMElement $rootElement An extensible mechanism for communities to describe their
         * repositories.
         */
        public function addDescription(DOMElement $rootElement);

        /**
         * @param string $url The base URL of the repository used for all requests. The base URL specifies the Internet
         * host and port, and optionally a path, of an HTTP server acting as a repository.
         */
        public function setBaseUrl($url);

        /**
         * @param string $supportLevel The manner in which the repository supports the notion of deleted records as one
         * of the SL_* constants.
         */
        public function setDeletedRecord($supportLevel);

        /**
         * @param DateTimeInterface $datestamp A UTCdatetime that is the guaranteed lower limit of all datestamps
         * recording changes, modifications, or deletions in the repository. A repository must not use datestamps lower
         * than the one specified here.
         */
        public function setEarliestDatestamp(DateTimeInterface $datestamp);

        /**
         * @param string $harvestingGranularity The finest harvesting granularity supported by the repository as one of
         * the HG_* constants.
         */
        public function setGranularity($harvestingGranularity);

        /**
         * @param string $version The version of the OAI-PMH supported by the repository.
         */
        public function setProtocolVersion($version);

        /**
         * @param string $name A human readable name for the repository.
         */
        public function setRepositoryName($name);
    }
