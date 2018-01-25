<?php
    namespace sednasoft\virmisco\opamih;

    use DateTimeInterface;
    use sednasoft\virmisco\oai\pmh\repository\request\feature\Classifiable as IClassifiable;
    use sednasoft\virmisco\oai\pmh\repository\request\feature\MetadataPrefixed as IMetadataPrefixed;
    use sednasoft\virmisco\oai\pmh\repository\request\feature\Temporal as ITemporal;

    /**
     * A request from a client, received over HTTP with a certain verb parameter that specifies one of six predefined
     * queries.
     */
    abstract class AbstractListItemsRequest extends AbstractResumableRequest implements IClassifiable, IMetadataPrefixed, ITemporal
    {
        /** @var DateTimeInterface|null */
        private $from;
        /** @var string|null */
        private $metadataPrefix;
        /** @var string|null */
        private $setSpec;
        /** @var DateTimeInterface|null */
        private $until;

        /**
         * @param string $requestUri
         * @param string $verb
         * @param string|null $resumptionToken
         * @param string|null $metadataPrefix
         * @param string|null $setSpec
         * @param DateTimeInterface $from
         * @param DateTimeInterface $until
         */
        public function __construct(
            $requestUri,
            $verb,
            $resumptionToken,
            $metadataPrefix,
            $setSpec = null,
            DateTimeInterface $from = null,
            DateTimeInterface $until = null
        ) {
            parent::__construct(
                $requestUri,
                $verb,
                array_merge(
                    $resumptionToken ? ['resumptionToken' => $resumptionToken] : [],
                    $metadataPrefix ? ['metadataPrefix' => $metadataPrefix] : [],
                    $setSpec ? ['set' => $setSpec] : [],
                    $from ? ['from' => $from] : [],
                    $until ? ['until' => $until] : []
                ),
                $resumptionToken
            );
            $this->from = $from;
            $this->metadataPrefix = $metadataPrefix;
            $this->setSpec = $setSpec;
            $this->until = $until;
        }

        /**
         * @return DateTimeInterface|null An optional argument with a UTCdatetime value, which specifies a lower bound
         * for datestamp-based selective harvesting.
         */
        public function getFrom()
        {
            return $this->from;
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

        /**
         * @return string[] An optional argument with a setSpec value, which specifies set criteria for selective
         * harvesting.
         */
        public function getSet()
        {
            return $this->setSpec;
        }

        /**
         * @return DateTimeInterface|null An optional argument with a UTCdatetime value, which specifies an upper bound
         * for datestamp-based selective harvesting.
         */
        public function getUntil()
        {
            return $this->until;
        }

        /**
         * @return bool Whether the optional from argument is present.
         */
        public function hasFrom()
        {
            return $this->getFrom() !== null;
        }

        /**
         * @return bool Whether the optional set argument is present.
         */
        public function hasSet()
        {
            return (bool)count($this->getSet());
        }

        /**
         * @return bool Whether the optional until argument is present.
         */
        public function hasUntil()
        {
            return $this->getUntil() !== null;
        }
    }
