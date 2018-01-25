<?php
    namespace sednasoft\virmisco\oai\pmh\repository\response;

    use sednasoft\virmisco\oai\pmh\repository\data\Record;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\oai\pmh\repository\response\feature\Incomplete;

    /**
     * A successful response to a ListRecords request.
     */
    interface ListRecords extends Response, Incomplete
    {
        /**
         * @param Record $record Metadata about a resource expressed in a single format.
         */
        public function addRecord (Record $record);
    }
