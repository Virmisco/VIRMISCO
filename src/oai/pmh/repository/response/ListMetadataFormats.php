<?php
    namespace sednasoft\virmisco\oai\pmh\repository\response;

    use sednasoft\virmisco\oai\pmh\repository\data\MetadataFormat;
    use sednasoft\virmisco\oai\pmh\repository\Response;

    /**
     * A successful response to a ListMetadataFormats request.
     */
    interface ListMetadataFormats extends Response
    {
        /**
         * @param MetadataFormat $metadataFormat
         */
        public function addMetadataFormat (MetadataFormat $metadataFormat);
    }
