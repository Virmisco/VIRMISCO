<?php
    namespace sednasoft\virmisco\opamih;

    use DateTimeInterface;
    use sednasoft\virmisco\oai\pmh\repository\request\ListRecords as IListRecords;

    /**
     * This verb is used to harvest records from a repository. Optional arguments permit selective harvesting of records
     * based on set membership and/or datestamp. Depending on the repository's support for deletions, a returned header
     * may have a status attribute of "deleted" if a record matching the arguments specified in the request has been
     * deleted. No metadata will be present for records with deleted status.
     */
    class ListRecordsRequest extends AbstractListItemsRequest implements IListRecords
    {
        /**
         * @param string $requestUri
         * @param string $resumptionToken
         * @param string|null $metadataPrefix
         * @param string|null $setSpec
         * @param DateTimeInterface $from
         * @param DateTimeInterface $until
         */
        public function __construct(
            $requestUri,
            $resumptionToken,
            $metadataPrefix,
            $setSpec = null,
            DateTimeInterface $from = null,
            DateTimeInterface $until = null
        ) {
            parent::__construct($requestUri, 'ListRecords', $resumptionToken, $metadataPrefix, $setSpec, $from, $until);
        }
    }
