<?php
    namespace sednasoft\virmisco\oai\pmh\repository\request\feature;

    /**
     * Enables querying a specific repository item for a request using an optional identifier.
     */
    interface Identifiable
    {
        /**
         * @return string|null An optional argument that specifies the unique identifier of the item for which available
         * metadata formats are being requested. If this argument is omitted, then the response includes all metadata
         * formats supported by this repository. Note that the fact that a metadata format is supported by a repository
         * does not mean that it can be disseminated from all items in the repository.
         */
        public function getIdentifier();

        /**
         * @return bool Whether the optional identifier argument is present.
         */
        public function hasIdentifier();
    }
