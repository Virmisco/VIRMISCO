<?php
    namespace sednasoft\virmisco\oai\pmh\repository\request\feature;

    /**
     * Enables selective harvesting for a request based on an optional hierarchical set identifier.
     */
    interface Classifiable
    {
        /**
         * @return string|null An optional argument with a setSpec value, which specifies set criteria for selective
         * harvesting.
         */
        public function getSet();

        /**
         * @return bool Whether the optional set argument is present.
         */
        public function hasSet();
    }
