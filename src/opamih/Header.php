<?php
    namespace sednasoft\virmisco\opamih;

    use DateTime;
    use DateTimeZone;
    use DOMDocument;
    use DOMDocumentFragment;
    use DOMElement;
    use sednasoft\virmisco\oai\pmh\repository\data\Header as IHeader;

    /**
     * Represents identity and set membership of a repository item, but not its format-specific metadata itself.
     */
    class Header implements IHeader
    {
        /** @var DateTime */
        private $datestamp;
        /** @var string */
        private $identifier;
        /** @var string[] */
        private $setSpecs = [];
        /** @var bool */
        private $statusDeleted;

        /**
         * @param string $identifier
         * @param DateTime $datestamp
         * @param bool $statusDeleted
         */
        public function __construct($identifier, DateTime $datestamp, $statusDeleted = false)
        {
            $this->identifier = $identifier;
            $this->datestamp = $datestamp->setTimezone(new DateTimeZone('UTC'));
            $this->statusDeleted = $statusDeleted;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->identifier;
        }

        /**
         * @param string $setSpec
         */
        public function addSetSpec($setSpec)
        {
            $this->setSpecs[] = $setSpec;
        }

        /**
         * @param DOMDocument $ownerDocument
         * @return DOMDocumentFragment
         */
        public function toDomFragment(DOMDocument $ownerDocument)
        {
            $result = $ownerDocument->createDocumentFragment();
            /** @var DOMElement $header */
            $header = $result->appendChild($ownerDocument->createElement('header'));
            if ($this->statusDeleted) {
                $header->setAttribute('status', 'deleted');
            }
            $result->firstChild
                ->appendChild($ownerDocument->createElement('identifier'))
                ->appendChild($ownerDocument->createTextNode($this->identifier));
            $result->firstChild
                ->appendChild($ownerDocument->createElement('datestamp'))
                ->appendChild($ownerDocument->createTextNode($this->datestamp->format(AbstractResponse::ISO8601UTC)));
            /** @var string $setSpec */
            foreach ($this->setSpecs as $setSpec) {
                $result->firstChild
                    ->appendChild($ownerDocument->createElement('setSpec'))
                    ->appendChild($ownerDocument->createTextNode($setSpec));
            }

            return $result;
        }
    }