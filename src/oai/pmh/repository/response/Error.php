<?php
    namespace sednasoft\virmisco\oai\pmh\repository\response;

    use sednasoft\virmisco\oai\pmh\repository\Response;

    /**
     * An error response to any request.
     */
    interface Error extends Response
    {
        const ERR_BAD_ARGUMENT = 'badArgument';
        const ERR_BAD_RESUMPTION_TOKEN = 'badResumptionToken';
        const ERR_BAD_VERB = 'badVerb';
        const ERR_CANNOT_DISSEMINATE_FORMAT = 'cannotDisseminateFormat';
        const ERR_ID_DOES_NOT_EXIST = 'idDoesNotExist';
        const ERR_NO_METADATA_FORMATS = 'noMetadataFormats';
        const ERR_NO_RECORDS_MATCH = 'noRecordsMatch';
        const ERR_NO_SET_HIERARCHY = 'noSetHierarchy';

        /**
         * @param string $code One of the ERR_* constants.
         * @param string $message The error message meant to be understood by a human.
         */
        public function setError($code, $message);
    }
