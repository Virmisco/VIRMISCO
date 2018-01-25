<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class OrganismOnSpecimenCarrierIdentified extends AbstractEvent
    {
        /** @var string */
        private $authorship;
        /** @var string */
        private $genus;
        /** @var string */
        private $identifier;
        /** @var string */
        private $infraspecificEpithet;
        /** @var bool */
        private $parenthesized;
        /** @var string */
        private $qualifier;
        /** @var string */
        private $sequenceNumber;
        /** @var string */
        private $specificEpithet;
        /** @var string */
        private $subgenus;
        /** @var int */
        private $year;

        /**
         * OrganismOnSpecimenCarrierIdentified constructor.
         * @param string $sequenceNumber
         * @param string $genus
         * @param string $subgenus
         * @param string $specificEpithet
         * @param string $infraspecificEpithet
         * @param string $authorship
         * @param int $year
         * @param bool $parenthesized
         * @param string $identifier
         * @param string $qualifier
         */
        public function __construct(
            $sequenceNumber,
            $genus,
            $subgenus,
            $specificEpithet,
            $infraspecificEpithet,
            $authorship,
            $year,
            $parenthesized,
            $identifier,
            $qualifier
        ) {
            parent::__construct();
            $this->sequenceNumber = $sequenceNumber;
            $this->genus = strval($genus);
            $this->subgenus = $subgenus;
            $this->specificEpithet = strval($specificEpithet);
            $this->infraspecificEpithet = $infraspecificEpithet;
            $this->authorship = $authorship;
            $this->year = $year;
            $this->parenthesized = $parenthesized;
            $this->identifier = $identifier;
            $this->qualifier = $qualifier;
        }

        /**
         * @return string
         */
        public function getAuthorship()
        {
            return $this->authorship;
        }

        /**
         * @return string
         */
        public function getGenus()
        {
            return $this->genus;
        }

        /**
         * @return string
         */
        public function getIdentifier()
        {
            return $this->identifier;
        }

        /**
         * @return string
         */
        public function getInfraspecificEpithet()
        {
            return $this->infraspecificEpithet;
        }

        /**
         * @return string
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
         * @return string
         */
        public function getSpecificEpithet()
        {
            return $this->specificEpithet;
        }

        /**
         * @return string
         */
        public function getSubgenus()
        {
            return $this->subgenus;
        }

        /**
         * @return int
         */
        public function getYear()
        {
            return $this->year;
        }

        /**
         * @return boolean
         */
        public function isParenthesized()
        {
            return $this->parenthesized;
        }
    }
