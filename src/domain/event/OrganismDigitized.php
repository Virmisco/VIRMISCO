<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\Uuid;

    class OrganismDigitized extends AbstractEvent
    {
        /** @var Uuid */
        private $photomicrographId;
        /** @var string */
        private $sequenceNumber;

        /**
         * @param string $sequenceNumber
         * @param Uuid $photomicrographId
         */
        public function __construct($sequenceNumber, Uuid $photomicrographId)
        {
            parent::__construct();
            $this->sequenceNumber = $sequenceNumber;
            $this->photomicrographId = $photomicrographId;
        }

        /**
         * @return Uuid
         */
        public function getPhotomicrographId()
        {
            return $this->photomicrographId;
        }

        /**
         * @return string
         */
        public function getSequenceNumber()
        {
            return $this->sequenceNumber;
        }
    }
