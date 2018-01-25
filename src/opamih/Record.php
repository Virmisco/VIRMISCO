<?php
    namespace sednasoft\virmisco\opamih;

    use DOMDocument;
    use DOMDocumentFragment;
    use DOMElement;
    use sednasoft\virmisco\oai\pmh\repository\data\Header as IHeader;
    use sednasoft\virmisco\oai\pmh\repository\data\Record as IRecord;

    /**
     * Represents a concrete representation of a repository item with a header (carrying identity) and metadata (in a
     * specific XML-based metadata format).
     */
    class Record implements IRecord
    {
        /** @var DOMElement[] */
        private $abouts = [];
        /** @var IHeader */
        private $header;
        /** @var DOMElement|null */
        private $metadata;

        /**
         * @param IHeader $header
         * @param DOMElement $metadata
         */
        public function __construct(IHeader $header, DOMElement $metadata = null)
        {
            $this->header = $header;
            $this->metadata = $metadata;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return strval($this->header);
        }

        /**
         * @param DOMElement $rootElement
         */
        public function addAbout(DOMElement $rootElement)
        {
            $this->abouts[] = $rootElement;
        }

        /**
         * @param DOMDocument $ownerDocument
         * @return DOMDocumentFragment
         */
        public function toDomFragment(DOMDocument $ownerDocument)
        {
            $result = $ownerDocument->createDocumentFragment();
            $result->appendChild($ownerDocument->createElement('element'));
            $result->firstChild->appendChild($this->header->toDomFragment($ownerDocument));
            if ($this->metadata) {
                $result->firstChild
                    ->appendChild($ownerDocument->createElement('metadata'))
                    ->appendChild($ownerDocument->importNode($this->metadata, true));
            }
            /** @var DOMElement $about */
            foreach ($this->abouts as $about) {
                $result->firstChild->appendChild($ownerDocument->createElement('about'))->appendChild($about);
            }

            return $result;
        }
    }