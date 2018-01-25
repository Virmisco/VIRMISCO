<?php
    namespace sednasoft\virmisco\oai\pmh\repository\data;

    use DOMDocument;
    use DOMDocumentFragment;

    /**
     * Represents any data element that needs to be serializable to XML for being sent in a response.
     */
    interface DataElement
    {
        /**
         * @return string
         */
        public function __toString();

        /**
         * @param DOMDocument $ownerDocument
         * @return DOMDocumentFragment
         */
        public function toDomFragment(DOMDocument $ownerDocument);
    }
