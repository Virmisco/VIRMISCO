<?php
    namespace sednasoft\virmisco\oai\pmh\repository\response;

    use sednasoft\virmisco\oai\pmh\repository\data\Record;
    use sednasoft\virmisco\oai\pmh\repository\Response;

    /**
     * A successful response to a GetRecord request.
     */
    interface GetRecord extends Response
    {
        /**
         * @param Record $record Metadata about a resource expressed in a single format.
         */
        public function setRecord (Record $record);
    }
