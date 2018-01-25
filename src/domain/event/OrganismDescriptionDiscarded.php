<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class OrganismDescriptionDiscarded extends AbstractEvent
    {
        /** @var string */
        private $sequenceNumber;

        /**
         * SynonymsOfOrganismCleared constructor.
         * @param string $sequenceNumber
         */
        public function __construct($sequenceNumber)
        {
            parent::__construct();
            $this->sequenceNumber = $sequenceNumber;
        }

        /**
         * @return string
         */
        public function getSequenceNumber()
        {
            return $this->sequenceNumber;
        }
    }
