<?php
    namespace sednasoft\virmisco\vpmh;

    use DateTimeInterface;
    use sednasoft\virmisco\oai\pmh\repository\data\Record;
    use Traversable;

    /**
     * Retrieves Record objects and related information.
     */
    interface IRecordProvider
    {
        /**
         * @param string $metadataPrefix
         * @param string|null $setSpec
         * @param DateTimeInterface|null $from
         * @param DateTimeInterface|null $until
         * @return int
         */
        public function countRecords(
            $metadataPrefix,
            $setSpec = null,
            DateTimeInterface $from = null,
            DateTimeInterface $until = null
        );

        /**
         * @param string $identifier
         * @param string $metadataPrefix
         * @param bool $returnHeaderOnly
         * @return Record|null
         */
        public function getRecordByIdentifier($identifier, $metadataPrefix, $returnHeaderOnly = false);

        /**
         * @param int $offset
         * @param int $length
         * @param string $metadataPrefix
         * @param bool $returnHeaderOnly
         * @param string|null $setSpec
         * @param DateTimeInterface|null $from
         * @param DateTimeInterface|null $until
         * @return Traversable Each member is an instance of Record.
         */
        public function iterateRecordsInRange(
            $offset,
            $length,
            $metadataPrefix,
            $returnHeaderOnly = false,
            $setSpec = null,
            DateTimeInterface $from = null,
            DateTimeInterface $until = null
        );
    }
