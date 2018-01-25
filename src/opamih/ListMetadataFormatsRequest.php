<?php
    namespace sednasoft\virmisco\opamih;

    use sednasoft\virmisco\oai\pmh\repository\request\ListMetadataFormats as IListMetadataFormats;

    /**
     * This verb is used to retrieve the metadata formats available from a repository. An optional argument restricts
     * the request to the formats available for a specific item.
     */
    class ListMetadataFormatsRequest extends AbstractRequest implements IListMetadataFormats
    {
        /** @var string|null */
        private $identifier;

        /**
         * @param string $requestUri
         * @param string|null $identifier
         */
        public function __construct($requestUri, $identifier = null)
        {
            parent::__construct($requestUri, 'ListMetadataFormats', $identifier ? ['identifier' => $identifier] : []);
            $this->identifier = $identifier;
        }

        /**
         * @return string|null An optional argument that specifies the unique identifier of the item for which available
         * metadata formats are being requested. If this argument is omitted, then the response includes all metadata
         * formats supported by this repository. Note that the fact that a metadata format is supported by a repository
         * does not mean that it can be disseminated from all items in the repository.
         */
        public function getIdentifier()
        {
            return $this->identifier;
        }

        /**
         * @return bool Whether the optional identifier argument is present.
         */
        public function hasIdentifier()
        {
            return $this->getIdentifier() !== null;
        }
    }
