<?php
    namespace sednasoft\virmisco\opamih;

    use DateTime;
    use DateTimeZone;
    use DOMDocument;
    use DOMDocumentFragment;
    use DOMElement;
    use sednasoft\virmisco\oai\pmh\repository\data\ResumptionToken as IResumptionToken;

    /**
     * Represents a handle to resume a previously received partial list response.
     */
    class ResumptionToken implements IResumptionToken
    {
        /** @var int|null */
        private $completeListSize;
        /** @var int|null */
        private $cursor;
        /** @var DateTime|null */
        private $expirationDate;
        /** @var string */
        private $value;

        /**
         * @param string $value
         * @param DateTime|null $expirationDate
         * @param int|null $completeListSize
         * @param int|null $cursor
         */
        public function __construct($value, DateTime $expirationDate = null, $completeListSize = null, $cursor = null)
        {
            $this->value = $value;
            $this->expirationDate = $expirationDate ? $expirationDate->setTimezone(new DateTimeZone('UTC')) : null;
            $this->completeListSize = $completeListSize;
            $this->cursor = $cursor;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->value;
        }

        /**
         * @param DOMDocument $ownerDocument
         * @return DOMDocumentFragment
         */
        public function toDomFragment(DOMDocument $ownerDocument)
        {
            $result = $ownerDocument->createDocumentFragment();
            /** @var DOMElement $token */
            $token = $result->appendChild($ownerDocument->createElement('resumptionToken'));
            if ($this->expirationDate) {
                $token->setAttribute('expirationDate', $this->expirationDate->format(AbstractResponse::ISO8601UTC));
            }
            if ($this->completeListSize) {
                $token->setAttribute('completeListSize', $this->completeListSize);
            }
            if ($this->cursor !== null) {
                $token->setAttribute('cursor', $this->cursor);
            }
            $token->appendChild($ownerDocument->createTextNode($this->value));

            return $result;
        }
    }