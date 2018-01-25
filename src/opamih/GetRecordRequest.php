<?php
    namespace sednasoft\virmisco\opamih;

    use sednasoft\virmisco\oai\pmh\repository\request\GetRecord as IGetRecord;

    /**
     * This verb is used to retrieve an individual metadata record from a repository. Required arguments specify the
     * identifier of the item from which the record is requested and the format of the metadata that should be included
     * in the record. Depending on the level at which a repository tracks deletions, a header with a "deleted" value for
     * the status attribute may be returned, in case the metadata format specified by the metadataPrefix is no longer
     * available from the repository or from the specified item.
     */
    class GetRecordRequest extends AbstractRequest implements IGetRecord
    {
        /** @var string */
        private $identifier;
        /** @var string */
        private $metadataPrefix;

        /**
         * @param string $requestUri
         * @param string $identifier
         * @param string $metadataPrefix
         */
        public function __construct($requestUri, $identifier, $metadataPrefix)
        {
            parent::__construct($requestUri, 'GetRecord',
                ['identifier' => $identifier, 'metadataPrefix' => $metadataPrefix]);
            $this->identifier = $identifier;
            $this->metadataPrefix = $metadataPrefix;
        }

        /**
         * @return string A required argument that specifies the unique identifier of the item in the repository from
         * which the record must be disseminated.
         */
        public function getIdentifier()
        {
            return $this->identifier;
        }

        /**
         * @return string A required argument, which specifies that headers or records should be returned only if the
         * metadata format matching the supplied metadataPrefix is available or, depending on the repository's support
         * for deletions, has been deleted. The metadata formats supported by a repository and for a particular item can
         * be retrieved using the ListMetadataFormats request.
         */
        public function getMetadataPrefix()
        {
            return $this->metadataPrefix;
        }
    }
