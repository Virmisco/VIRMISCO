<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class NameOriginOfOrganismChanged extends AbstractEvent
    {
        /** @var null|string */
        private $identifier;
        /** @var null|string */
        private $qualifier;
        /** @var string */
        private $sequenceNumber;
        /** @var null|string */
        private $typeStatus;

        /**
         * NameOriginOfOrganismChanged constructor.
         * @param string $sequenceNumber
         * @param null|string $typeStatus
         * @param null|string $identifier
         * @param null|string $qualifier
         */
        public function __construct($sequenceNumber, $typeStatus, $identifier, $qualifier)
        {
            parent::__construct();
            $this->sequenceNumber = $sequenceNumber;
            $this->typeStatus = $typeStatus;
            $this->identifier = $identifier;
            $this->qualifier = $qualifier;
        }

        /**
         * @return null|string
         */
        public function getIdentifier()
        {
            return $this->identifier;
        }

        /**
         * @return null|string
         */
        public function getQualifier()
        {
            return $this->qualifier;
        }

        /**
         * @return string
         */
        public function getSequenceNumber()
        {
            return $this->sequenceNumber;
        }

        /**
         * @return null|string
         */
        public function getTypeStatus()
        {
            return $this->typeStatus;
        }
    }
