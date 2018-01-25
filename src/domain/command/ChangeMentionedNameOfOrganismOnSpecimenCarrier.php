<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    class ChangeMentionedNameOfOrganismOnSpecimenCarrier extends AbstractCommand
    {
        /** @var null|string */
        private $authorship;
        /** @var string */
        private $genus;
        /** @var null|string */
        private $infraspecificEpithet;
        /** @var bool */
        private $parenthesized;
        /** @var string */
        private $sequenceNumber;
        /** @var string */
        private $specificEpithet;
        /** @var null|string */
        private $subgenus;
        /** @var int|null */
        private $year;

        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param Uuid $specimenCarrierId The unique identifier of the aggregate to receive this command.
         * @param string $sequenceNumber
         * @param string $genus
         * @param string|null $subgenus
         * @param string $specificEpithet
         * @param string|null $infraspecificEpithet
         * @param string|null $authorship
         * @param int|null $year
         * @param bool $parenthesized
         */
        public function __construct(
            Uuid $specimenCarrierId,
            $sequenceNumber,
            $genus,
            $subgenus,
            $specificEpithet,
            $infraspecificEpithet,
            $authorship,
            $year,
            $parenthesized
        ) {
            parent::__construct($specimenCarrierId);
            $this->sequenceNumber = (string)$sequenceNumber;
            $this->genus = $genus;
            $this->subgenus = $subgenus;
            $this->specificEpithet = $specificEpithet;
            $this->infraspecificEpithet = $infraspecificEpithet;
            $this->authorship = $authorship;
            $this->year = $year;
            $this->parenthesized = $parenthesized;
        }

        /**
         * @return null|string
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
         * @return null|string
         */
        public function getInfraspecificEpithet()
        {
            return $this->infraspecificEpithet;
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
         * @return null|string
         */
        public function getSubgenus()
        {
            return $this->subgenus;
        }

        /**
         * @return int|null
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
