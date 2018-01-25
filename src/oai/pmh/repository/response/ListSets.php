<?php
    namespace sednasoft\virmisco\oai\pmh\repository\response;

    use sednasoft\virmisco\oai\pmh\repository\data\Set;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\oai\pmh\repository\response\feature\Incomplete;

    /**
     * A successful response to a ListSets request.
     */
    interface ListSets extends Response, Incomplete
    {
        /**
         * @param Set $set
         */
        public function addSet (Set $set);
    }
