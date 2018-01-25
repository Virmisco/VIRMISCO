<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class HigherTaxaForOrganismProvided extends AbstractEvent
    {
        /** @var string */
        private $higherTaxa;
        /** @var string */
        private $sequenceNumber;

        /**
         * SynonymsOfOrganismCleared constructor.
         * @param string $sequenceNumber
         * @param string $higherTaxa
         */
        public function __construct($sequenceNumber, $higherTaxa)
        {
            parent::__construct();
            $this->sequenceNumber = $sequenceNumber;
            $this->higherTaxa = $higherTaxa;
        }

        /**
         * @return string
         */
        public function getHigherTaxa()
        {
            return trim(preg_replace('<\\s+>', ' ', $this->higherTaxa));
        }

        /**
         * @return string
         */
        public function getSequenceNumber()
        {
            return $this->sequenceNumber;
        }
    }
