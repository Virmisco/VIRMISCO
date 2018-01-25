<?php
    namespace sednasoft\virmisco\oai\pmh\repository\request\feature;

    /**
     * Restricts a request to a specific repository item using a mandatory identifier.
     */
    interface Identified
    {
        /**
         * @return string A required argument that specifies the unique identifier of the item in the repository from
         * which the record must be disseminated.
         */
        public function getIdentifier();
    }
