<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class SpecimenCarrierManipulated extends AbstractEvent
    {
        /** @var string */
        private $carrierNumber;
        /** @var string */
        private $labelTranscript;
        /** @var string */
        private $owner;
        /** @var string */
        private $preparationType;
        /** @var string */
        private $previousCollection;

        /**
         * SpecimenCarrierRecorded constructor.
         * @param string $carrierNumber
         * @param string $preparationType
         * @param string $owner
         * @param string $previousCollection
         * @param string $labelTranscript
         */
        public function __construct(
            $carrierNumber,
            $preparationType,
            $owner,
            $previousCollection,
            $labelTranscript
        ) {
            parent::__construct();
            $this->carrierNumber = $carrierNumber;
            $this->preparationType = $preparationType;
            $this->owner = $owner;
            $this->previousCollection = $previousCollection;
            $this->labelTranscript = $labelTranscript;
        }

        /**
         * @return string
         */
        public function getCarrierNumber()
        {
            return $this->carrierNumber;
        }

        /**
         * @return string
         */
        public function getLabelTranscript()
        {
            return $this->labelTranscript;
        }

        /**
         * @return string
         */
        public function getOwner()
        {
            return $this->owner;
        }

        /**
         * @return string
         */
        public function getPreparationType()
        {
            return $this->preparationType;
        }

        /**
         * @return string
         */
        public function getPreviousCollection()
        {
            return $this->previousCollection;
        }
    }
