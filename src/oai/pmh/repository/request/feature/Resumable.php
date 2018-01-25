<?php
    namespace sednasoft\virmisco\oai\pmh\repository\request\feature;

    /**
     * Enables resuming a partial list response for a request by including an optional and exclusive resumption token.
     */
    interface Resumable
    {
        /**
         * @return string|null An exclusive argument with a value that is the flow control token returned by a previous
         * ListIdentifiers request that issued an incomplete list.
         */
        public function getResumptionToken();

        /**
         * @return bool Whether the optional but exclusive resumptionToken argument is present, in which case none of
         * the other arguments are allowed.
         */
        public function hasResumptionToken();
    }
