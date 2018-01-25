<?php
    namespace sednasoft\virmisco\oai\pmh\repository\response;

    use sednasoft\virmisco\oai\pmh\repository\data\Header;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\oai\pmh\repository\response\feature\Incomplete;

    /**
     * A successful response to a ListIdentifiers request.
     */
    interface ListIdentifiers extends Response, Incomplete
    {
        /**
         * @param Header $header A header object identifying a record.
         */
        public function addHeader (Header $header);
    }
